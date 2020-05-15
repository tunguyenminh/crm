<?php

namespace App\Http\Controllers\Admin;


use App\Classes\Common;

use App\Http\Requests\Admin\Campaign\DeleteRequest;
use App\Http\Requests\Admin\Campaign\ImportLeadRequest;
use App\Http\Requests\Admin\Campaign\IndexRequest;
use App\Http\Requests\Admin\Campaign\StoreRequest;
use App\Http\Requests\Admin\Campaign\UpdateRequest;

use App\Classes\Reply;
use App\Models\Campaign;
use App\Models\CampaignMember;
use App\Models\Form;
use App\Models\FormField;
use App\Models\Lead;
use App\Models\LeadData;
use App\Models\SalesMember;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Str;

class CallEnquiryController extends AdminBaseController
{
     /**
	 * UserController constructor.
	 */

    public function __construct()
    {
        parent::__construct();

        $this->pageTitle = trans('menu.callEnquiry');
        $this->pageIcon = 'fa fa-phone-volume';
        $this->leadManagementMenuActive = 'active';
    }

    /**
     * @param IndexRequest $request
     * @return \Illuminate\Contracts\View\View
     */
    public function index(IndexRequest $request)
    {
        $this->callEnquiryActive = 'active';

        // Call Enquiry Stats
        $this->getCallEnquiryStats();

        return view('admin.call-enquiry.index', $this->data);
    }

     /**
	 * @return mixed
	 */
    public function getLists(Request $request)
    {
        $campaignId = $request->campaign_id;
        $formFieldId = $request->form_field_id;
        $formFieldValue = $request->form_field_value;

        $callLeads = LeadData::select('leads.id as lead_id', 'leads.reference_number', 'campaigns.id as campaign_id', 'campaigns.status as campaign_status', 'campaigns.name as campaign_name', DB::raw('CONCAT(users.first_name, " ", COALESCE(users.last_name, "")) as calling_agent'))
                             ->join('leads', 'leads.id', '=', 'lead_data.lead_id')
                             ->join('campaigns', 'campaigns.id', '=', 'leads.campaign_id');

        if($request->from_page == 'campaign_detail')
        {
            $userActiveCampaigns = $this->user->activeCampaigns('all')->pluck('id')->toArray();

            $callLeads = $callLeads->leftJoin('users', 'users.id', '=', 'leads.last_actioned_by');
        } else {
            $userActiveCampaigns = $this->user->activeCampaigns()->pluck('id')->toArray();
            $callLeads = $callLeads->join('users', 'users.id', '=', 'leads.last_actioned_by');
        }

        $callLeads = $callLeads->where('lead_data.field_value', 'LIKE', '%'.$formFieldValue.'%');

        if($request->has('campaign_id') && $campaignId != '' && $campaignId != 'all')
        {
            $campaign = Campaign::find($campaignId);

            // Also include campaign which is created by itself
            if(!in_array($campaignId, $userActiveCampaigns) && $campaign->created_by == $this->user->id && $request->from_page == 'campaign_detail')
            {
                $userActiveCampaigns[] = $campaignId;
            }

            if(in_array($campaignId, $userActiveCampaigns))
            {
                $callLeads = $callLeads->where('campaigns.id', '=', $campaignId);
            } else {
                $callLeads = $callLeads->where('campaigns.id', '=', 'x');
            }
        } else {
            $callLeads = $callLeads->whereIn('campaigns.id', $userActiveCampaigns);
        }

        // If user is not admin or not have permission campaign_view_all
        // and ( If page is call enquiry or campaign is not created by user)
        if(!$this->user->ability('admin', 'campaign_view_all') &&
            (isset($campaign) && $campaign->created_by != $this->user->id || $request->from_page != 'campaign_detail'))
        {
            $callLeads = $callLeads->where('users.id', $this->user->id);
        }

        if($formFieldId != '')
        {
            $callLeads = $callLeads->where('lead_data.form_field_id', '=', $formFieldId);
        } else {
            $callLeads = $callLeads->groupBy('lead_data.lead_id');
        }


        return datatables()->eloquent($callLeads)
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
            ->editColumn('campaign_name', function ($row) {
                return '<a href="'.route('admin.campaigns.show', md5($row->campaign_id)).'">'.$row->campaign_name.'</a>';
            })
            ->addColumn('action', function ($row) {
                $text = '<div class="dropdown d-inline">
                              <button class="btn btn-info dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            '.trans('app.action').'
                        </button>';

                $text .= '<div class="dropdown-menu">
                            <a class="dropdown-item has-icon"href="javascript:void(0);" onclick="viewLead(\''.md5($row->lead_id).'\')"><i class="fa fa-eye"></i> '.trans('module_call_enquiry.viewLead').'</a>';


                if($row->campaign_status != 'completed')
                {
                    $text .= '<a class="dropdown-item has-icon"href="'.route('admin.callmanager.lead', [md5($row->lead_id)]).'"><i class="fa fa-play"></i> '.trans('module_call_enquiry.goAndResumeCall').'</a>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item has-icon" href="javascript:void(0);" onclick="deleteLead(\''.md5($row->lead_id).'\')"><i class="fa fa-trash"></i> '.trans('module_call_enquiry.deleteLead').'</a>';
                }

                          $text .= '</div>
                        </div>';

                return $text;
            })
            ->rawColumns(['campaign_name', 'action'])
            ->make(true);
    }

    public function getFormFieldsByCampaign($campaignId)
    {

        if($campaignId != 'all')
        {
            $campaign = Campaign::find($campaignId);

            $this->formFields = FormField::where('form_id', $campaign->form_id)
                ->oldest('order')
                ->get();


        } else {
            $this->formFields = [];
        }

        // Call Enquiry Stats
        $this->getCallEnquiryStats($campaignId);

        $html = view('admin.call-enquiry.campaign-form-field', $this->data)->render();
        $htmlStats = view('admin.call-enquiry.call-enquiry-stats', $this->data)->render();

        $resultData = [
            'html' => $html,
            'stats' => $htmlStats
        ];

        return Reply::successWithData($resultData);
    }

}
