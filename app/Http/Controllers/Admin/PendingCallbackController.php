<?php

namespace App\Http\Controllers\Admin;

use App\Classes\Common;
use App\Http\Requests\Admin\FollowUp\IndexRequest;
use App\Http\Requests\Admin\FollowUp\StoreRequest;
use App\Http\Requests\Admin\FollowUp\UpdateRequest;

use App\Classes\Reply;
use App\Models\Callback;
use App\Models\Campaign;
use App\Models\FormField;
use App\Models\Lead;
use App\Models\LeadData;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PendingCallbackController extends AdminBaseController
{
     /**
	 * UserController constructor.
	 */

    public function __construct()
    {
        parent::__construct();

        $this->pageTitle = trans('menu.pendingCallbacks');
        $this->pageIcon = 'fa fa-headphones';
        $this->appointmentMenuActive = 'active';
    }

    /**
     * @param IndexRequest $request
     * @return \Illuminate\Contracts\View\View
     */
    public function index(IndexRequest $request)
    {
        $this->pendingCallbackActive = 'active';

        // Get callback information of today or un actioned calls
        $this->getCallBackStats();

        return view('admin.callbacks.index', $this->data);
    }

     /**
	 * @return mixed
	 */
    public function getLists(Request $request)
    {
        $userActiveCampaigns = $this->user->activeCampaigns()->pluck('id')->toArray();

        $callbacks = Callback::select('leads.id as lead_id', 'leads.reference_number','campaigns.id as campaign_id', 'campaigns.name as campaign_name', 'callbacks.callback_time', 'users.first_name', 'users.last_name')
                             ->join('leads', 'leads.id', '=', 'callbacks.lead_id')
                             ->join('campaigns', 'campaigns.id', '=', 'leads.campaign_id')
                             ->join('users', 'users.id', '=', 'callbacks.attempted_by');

        if(!$this->user->hasRole('admin'))
        {
            $callbacks = $callbacks->where('callbacks.attempted_by', $this->user->id);
        }

        if($request->has('fetch_type') && $request->fetch_type == 'self')
        {
            $callbacks = $callbacks->where('callbacks.attempted_by', $this->user->id);
        }

        if($request->has('campaign_id') && $request->campaign_id != 'all')
        {
            $campaignId = $request->campaign_id;

            if(in_array($campaignId, $userActiveCampaigns))
            {
                $callbacks = $callbacks->where('campaigns.id', '=', $campaignId);
            } else {
                $callbacks = $callbacks->where('campaigns.id', '=', 'x');
            }
        } else {
            $callbacks = $callbacks->whereIn('campaigns.id', $userActiveCampaigns);
        }

        if ($request->has('start_date') && $request->has('end_date') && $request->start_date != '' && $request->end_date != '')
        {
            $startDate = $request->start_date;
            $endDate = $request->end_date;

            $callbacks = $callbacks->whereBetween(DB::raw('DATE(callbacks.callback_time)'), [$startDate, $endDate]);
        }

        return datatables()->eloquent($callbacks)
            ->addColumn('contact_person', function ($row) {
                $firstName = Common::getLeadDataByColumn($row->lead_id, $this->firstNameArray);
                $lastName = Common::getLeadDataByColumn($row->lead_id, $this->lastNameArray);
                $name = trim($firstName.' '. $lastName);

                if(trim($name) == '')
                {
                    $name = Common::getLeadDataByColumn($row->lead_id, $this->nameArray);
                }

                return $name;
            })
            ->addColumn('email', function ($row) {
                return Common::getLeadDataByColumn($row->lead_id, $this->emailArray);
            })
            ->addColumn('calling_agent', function ($row) {
                return trim($row->first_name . ' ' . $row->last_name);
            })
            ->addColumn('campaign_name', function ($row) {
                return '<a href="'.route('admin.campaigns.show', md5($row->campaign_id)).'">'.$row->campaign_name.'</a>';
            })
            ->addColumn('action', function ($row) {
                $text = '<div class="dropdown d-inline">
                              <button class="btn btn-info dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            '.trans('app.action').'
                        </button>';

                $text .= '<div class="dropdown-menu">
                            <a class="dropdown-item has-icon"href="'.route('admin.callmanager.lead', [md5($row->lead_id)]).'"><i class="fa fa-play"></i> '.trans('module_call_enquiry.goAndResumeCall').'</a>
                            <a class="dropdown-item has-icon"href="javascript:void(0);" onclick="viewLead(\''.md5($row->lead_id).'\')"><i class="fa fa-eye"></i> '.trans('module_call_enquiry.viewLead').'</a>
                            <a class="dropdown-item has-icon" href="javascript:void(0);" onclick="cancelCallback(\''.md5($row->lead_id).'\')"><i class="fa fa-ban"></i> '.trans('module_call_enquiry.cancelCallback').'</a>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item has-icon" href="javascript:void(0);" onclick="deleteLead(\''.md5($row->lead_id).'\')"><i class="fa fa-trash"></i> '.trans('module_call_enquiry.deleteLead').'</a>
                          </div>
                        </div>';

                return $text;
            })
            ->editColumn(
                'callback_time',
                function ($row) {
                    return $row->callback_time->format('d F, Y h:ia');
                }
            )
            ->rawColumns(['campaign_name', 'action'])
            ->make(true);
    }

    public function getFormFieldsByCampaign($campaignId)
    {
        $campaign = Campaign::find($campaignId);

        $this->formFields = FormField::where('form_id', $campaign->form_id)
                                ->oldest('order')
                                ->get();

        $html = view('admin.call-enquiry.campaign-form-field', $this->data)->render();

        $resultData = [
            'html' => $html
        ];

        return Reply::successWithData($resultData);
    }

    public function addEditByLead($leadId)
    {
        $this->icon = 'edit';
        $lead = Lead::whereRaw('md5(id) = ?', $leadId)->first();
        $this->pendingCallBack = $lead->callBack ? $lead->callBack : new Callback();
        $this->lead = $lead;
        // Check current logged in user is member of appointment campaign
        $userActiveCampaigns = $this->user->activeCampaigns()->pluck('id')->toArray();
        if(!in_array($lead->campaign_id, $userActiveCampaigns))
        {
            return response()->view($this->forbiddenErrorView);
        }

        $this->campaignTeamMembers = User::select('users.*')
                                         ->join('campaign_members', 'campaign_members.user_id', '=', 'users.id')
                                         ->where('campaign_members.campaign_id', $lead->campaign_id)
                                         ->get();

        return view('admin.callbacks.add-edit', $this->data);
    }

    public function store(StoreRequest $request)
    {
        \DB::beginTransaction();

        $callBack = new Callback();
        $callBack->lead_id = $request->lead_id;
        $callBack->callback_time = Carbon::createFromFormat('Y-m-d H:i', $request->callback_time);
        $callBack->attempted_by = $request->attempted_by;
        $callBack->save();

        \DB::commit();

        $data = [
            'html' => '<input type="hidden" id="delete_follow_up_id" name="delete_follow_up_id" value="' . $callBack->id . '"><a href="javascript:;" onclick="followUpChanged()">' . trans('app.view') . '</a>'
        ];

        return Reply::successWithData($data);
    }

    public function update(UpdateRequest $request)
    {
        \DB::beginTransaction();

        $callBack = Callback::where('lead_id', $request->lead_id)->first();
        $callBack->callback_time = Carbon::createFromFormat('Y-m-d H:i', $request->callback_time);
        $callBack->attempted_by = $request->attempted_by;
        $callBack->save();

        \DB::commit();

        $data = [
            'html' => '<input type="hidden" id="delete_follow_up_id" name="delete_follow_up_id" value="' . $callBack->id . '"><a href="javascript:;" onclick="followUpChanged()">' . trans('app.view') . '</a>'
        ];

        return Reply::successWithData($data);
    }
}
