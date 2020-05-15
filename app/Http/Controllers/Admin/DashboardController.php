<?php

namespace App\Http\Controllers\Admin;

use App\Models\Appointment;
use App\Models\Callback;
use App\Models\CampaignMember;
use App\Models\ExamSyllabus;
use App\Models\Lead;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends AdminBaseController
{

     /**
	 * UserDashboardController constructor.
	 */
    public function __construct()
    {
        parent::__construct();
        $this->pageTitle = trans('menu.dashboard');
        $this->pageIcon = 'fa fa-home';
        $this->dashboardActive = 'active';
    }

    /**
     * @param IndexRequest $request
     * @return \Illuminate\Contracts\View\View
     */
    public function index()
    {

        $this->pendingCallbacks = Callback::select('leads.id as lead_id', 'leads.reference_number', 'campaigns.id as campaign_id', 'campaigns.name as campaign_name', 'callbacks.callback_time')
            ->join('leads', 'leads.id', '=', 'callbacks.lead_id')
            ->join('campaigns', 'campaigns.id', '=', 'leads.campaign_id')
            ->where('callbacks.attempted_by', $this->user->id)
            ->where('campaigns.status', '!=', 'completed')
            ->latest('callbacks.created_at')
            ->take(5)
            ->get();

        $this->bookedAppointments = Appointment::select('sales_members.first_name', 'sales_members.last_name', 'campaigns.id as campaign_id', 'campaigns.name as campaign_name', 'appointments.appointment_time', 'appointments.sales_member_id', 'appointments.id', 'appointments.lead_id')
            ->join('leads', 'leads.id', '=', 'appointments.lead_id')
            ->join('campaigns', 'campaigns.id', '=', 'leads.campaign_id')
            ->join('sales_members', 'sales_members.id', '=', 'appointments.sales_member_id')
            ->where('appointments.created_by', $this->user->id)
            ->where('campaigns.status', '!=', 'completed')
            ->latest('appointments.created_at')
            ->take(5)
            ->get();

        // Call Enquiry Stats
        $this->getCallEnquiryStats('all', 'none');

        $userActiveCampaigns = CampaignMember::select('campaigns.name', 'campaigns.id')
            ->join('campaigns', 'campaigns.id', '=', 'campaign_id')
            ->where('campaign_members.user_id', $this->user->id)
            ->where(function($query) {
                return $query->where('campaigns.status', 'started')
                    ->orWhereNull('campaigns.status');
            })
            ->get();

        $userActiveCampaignArray = $userActiveCampaigns->pluck('id')->toArray();

        $this->yourCampaigns = $userActiveCampaigns->count();
        $this->yourLeads = Lead::whereIn('campaign_id', $userActiveCampaignArray)
            ->where('last_actioned_by', $this->user->id)
            ->count();
        $this->totalTimes = Lead::whereIn('campaign_id', $userActiveCampaignArray)
            ->where('last_actioned_by', $this->user->id)
            ->sum('time_taken');

        $dateArray = [];
        $leadCountArray = [];
        $userLeads = Lead::selectRaw("DATE(updated_at) as date")
                         ->where('last_actioned_by', $this->user->id)
                         ->whereIn('campaign_id', $userActiveCampaignArray)
                         ->orderBy(DB::raw("DATE(updated_at)"), 'asc')
                         ->groupBy(DB::raw("DATE(updated_at)"))->take(7)->get();

        foreach ($userLeads as $userLead)
        {
            $dateArray[] = $userLead->date;

            $leadCount = Lead::where(DB::raw('DATE(updated_at)'), '=', $userLead->date)
                            ->where('last_actioned_by', $this->user->id)
                            ->whereIn('campaign_id', $userActiveCampaignArray)
                             ->count();

            $leadCountArray[] = $leadCount;

        }



        $userActiveCampaignsDataLists = [];
        foreach ($userActiveCampaigns as $userActiveCampaign)
        {
            $totalLeadAttended = Lead::where('last_actioned_by', $this->user->id)
                                     ->where('campaign_id', $userActiveCampaign->id)
                                    ->count();

            $interestedLeads = Lead::where('interested', 'interested')
                                    ->where('last_actioned_by', $this->user->id)
                                    ->where('campaign_id', $userActiveCampaign->id)
                                    ->count();

            $userActiveCampaignsDataLists[] = [
                'id' => $userActiveCampaign->id,
                'name' => $userActiveCampaign->name,
                'totalLeads' => $totalLeadAttended,
                'interested'  => $interestedLeads
            ];
        }

        $this->dateArray = $dateArray;
        $this->leadCountArray = $leadCountArray;
        $this->userActiveCampaignsDataLists = $userActiveCampaignsDataLists;
        return view('admin.dashboard', $this->data);
    }

}
