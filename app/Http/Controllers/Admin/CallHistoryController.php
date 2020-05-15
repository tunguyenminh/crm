<?php

namespace App\Http\Controllers\Admin;


use App\Classes\Common;

use App\Http\Requests\Admin\Campaign\DeleteRequest;
use App\Http\Requests\Admin\Campaign\ImportLeadRequest;
use App\Http\Requests\Admin\Campaign\IndexRequest;
use App\Http\Requests\Admin\Campaign\StoreRequest;
use App\Http\Requests\Admin\Campaign\UpdateRequest;

use App\Classes\Reply;
use App\Models\CallLog;
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

class CallHistoryController extends AdminBaseController
{
     /**
	 * UserController constructor.
	 */

    public function __construct()
    {
        parent::__construct();

        $this->pageTitle = trans('menu.callHistory');
        $this->pageIcon = 'fa fa-stopwatch';
        $this->leadManagementMenuActive = 'active';
    }

    /**
     * @param IndexRequest $request
     * @return \Illuminate\Contracts\View\View
     */
    public function index(IndexRequest $request)
    {
        $this->callHistoryActive = 'active';
        $this->campaignTeamMembers = User::select('id', 'first_name', 'last_name')
                                        ->get();

        return view('admin.call-history.index', $this->data);
    }

     /**
	 * @return mixed
	 */
    public function getLists(Request $request)
    {
        $campaignId = $request->campaign_id;

        $callLogs = CallLog::select('leads.id as lead_id', 'leads.reference_number', 'campaigns.id as campaign_id', 'call_logs.time_taken', 'campaigns.status as campaign_status', 'campaigns.name as campaign_name', DB::raw('CONCAT(users.first_name, " ", COALESCE(users.last_name, "")) as calling_agent'), 'call_logs.created_at')
                             ->join('leads', 'leads.id', '=', 'call_logs.lead_id')
                             ->join('campaigns', 'campaigns.id', '=', 'leads.campaign_id');

        if($request->from_page == 'campaign_detail')
        {
            $userActiveCampaigns = $this->user->activeCampaigns('all')->pluck('id')->toArray();

            $callLogs = $callLogs->leftJoin('users', 'users.id', '=', 'call_logs.attempted_by');
        } else {
            $userActiveCampaigns = $this->user->activeCampaigns()->pluck('id')->toArray();
            $callLogs = $callLogs->join('users', 'users.id', '=', 'call_logs.attempted_by');
        }

        if($request->has('campaign_id') && $campaignId != '' && $campaignId != 'all')
        {
            if($request->from_page == 'campaign_detail')
            {
                $campaign = Campaign::find($campaignId);
            } else {
                $campaign = Campaign::whereRaw('md5(id) = ?', $campaignId)->first();
                $campaignId = $campaign->id;
            }

            // Also include campaign which is created by itself
            if(!in_array($campaignId, $userActiveCampaigns) && $campaign->created_by == $this->user->id && $request->from_page == 'campaign_detail')
            {
                $userActiveCampaigns[] = $campaignId;
            }

            if(in_array($campaignId, $userActiveCampaigns))
            {
                $callLogs = $callLogs->where('campaigns.id', '=', $campaignId);
            } else {
                $callLogs = $callLogs->where('campaigns.id', '=', 'x');
            }
        } else {
            $callLogs = $callLogs->whereIn('campaigns.id', $userActiveCampaigns);
        }

        // If user is not admin or not have permission campaign_view_all
        // and ( If page is call enquiry or campaign is not created by user)
        if(!$this->user->ability('admin', 'campaign_view_all') &&
            (isset($campaign) && $campaign->created_by != $this->user->id || $request->from_page != 'campaign_detail'))
        {
            $callLogs = $callLogs->where('users.id', $this->user->id);
        } else if($request->has('team_member_id') && $request->team_member_id != '')
        {
            $callLogs = $callLogs->where('users.id', $request->team_member_id);
        }


        return datatables()->eloquent($callLogs)
            ->addColumn('contact_person', function ($row) {
                $firstName = Common::getLeadDataByColumn($row->lead_id, $this->firstNameArray);
                $lastName = Common::getLeadDataByColumn($row->lead_id, $this->lastNameArray);
                $name = trim($firstName.' '. $lastName);

                if(trim($name) == '')
                {
                    $name = Common::getLeadDataByColumn($row->lead_id, $this->nameArray);
                }

                $email = Common::getLeadDataByColumn($row->lead_id, $this->emailArray);
                $phone = Common::getLeadDataByColumn($row->lead_id, $this->phoneArray);

                $string = "<p>";
                $string .= "<i class='fa fa-user'></i> {$name}</br>";
                $string .= "<i class='fa fa-envelope'></i> {$email}</br>";
                $string .= "<i class='fa fa-phone-volume'></i> {$phone}</br>";
                $string .= "</p>";
                return $string;
            })
            ->editColumn('time_taken', function ($row) {
                $formatTimeTaken = strtolower(Common::secondsToStr($row->time_taken));
                $calledOn = $row->created_at->format('d F, Y h:i:s A');

                $string = "<p>";
                $string .= "<i class='fa fa-stopwatch'></i> {$formatTimeTaken}</br>";
                $string .= "<i class='fa fa-calendar'></i> {$calledOn}</br>";
                $string .= "</p>";
                return $string;
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
                    $text .= '<a class="dropdown-item has-icon"href="'.route('admin.callmanager.lead', [md5($row->lead_id)]).'"><i class="fa fa-play"></i> '.trans('module_call_enquiry.goAndResumeCall').'</a>';
                }

                          $text .= '</div>
                        </div>';

                return $text;
            })
            ->rawColumns(['campaign_name', 'contact_person', 'time_taken', 'action'])
            ->make(true);
    }

    public function getCampaignTeamMember($campaignId)
    {
        if($campaignId != 'all')
        {
            $this->campaignTeamMembers = CampaignMember::with('user:id,first_name,last_name')
                                                      ->where('campaign_id', $campaignId)
                                                      ->get();
        } else {
            $this->campaignTeamMembers = User::select('id', 'first_name', 'last_name')
                                             ->get();
        }

        $html = view('admin.call-history.campaign-team-members', $this->data)->render();

        $resultData = [
            'html' => $html
        ];

        return Reply::successWithData($resultData);
    }
}
