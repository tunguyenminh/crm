<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\MainBaseController;
use App\Models\Callback;
use App\Models\CampaignMember;
use App\Models\Lead;
use Carbon\Carbon;

class AdminBaseController extends MainBaseController
{

    public function __construct()
    {
        parent::__construct();

        // SEO
        $this->pageTitle = '';
        $this->isFront = false;

        // Pending Callback for current loggedIn user
        $this->middleware(function ($request, $next) {
            $user = auth()->guard('admin')->user();
            $todayDate = Carbon::now($user->timezone)->format('Y-m-d');
            $this->userPendingCallbacks = Callback::where('attempted_by', $user->id)
                ->whereRaw('DATE(callback_time) <= ?', $todayDate)
                ->count();

            return $next($request);
        });
    }

    public function getCallEnquiryStats($campaignId = 'all', $fetchType = 'all')
    {
        if($campaignId == 'all')
        {
            $userActiveCampaigns = $this->user->activeCampaigns('active', $fetchType);
            $userActiveCampaignArray = $userActiveCampaigns->pluck('id')->toArray();

            $this->yourCampaigns = $userActiveCampaigns->count();
            $this->totalLeads = Lead::whereIn('campaign_id', $userActiveCampaignArray)->count();
            $this->yourLeads = Lead::whereIn('campaign_id', $userActiveCampaignArray)
                ->where('last_actioned_by', $this->user->id)
                ->count();
            $this->totalTimes = Lead::whereIn('campaign_id', $userActiveCampaignArray)
                ->where('last_actioned_by', $this->user->id)
                ->sum('time_taken');
        } else {
            $this->totalLeads = Lead::where('campaign_id', $campaignId)->count();
            $this->yourLeads = Lead::where('campaign_id', $campaignId)
                ->where('last_actioned_by', $this->user->id)
                ->count();
            $this->totalTimes = Lead::where('campaign_id', $campaignId)
                ->where('last_actioned_by', $this->user->id)
                ->sum('time_taken');
            $this->campaignMemberCount = CampaignMember::where('campaign_id', $campaignId)->count();
        }

    }

    public function getCallBackStats()
    {
        $this->todayDate = Carbon::now($this->user->timezone)->format('Y-m-d');
        $this->todayDatePicker = Carbon::now($this->user->timezone)->subDay(1)->format('Y-m-d');

        $this->todayPendingCallbacks = Callback::where('attempted_by', $this->user->id)
            ->whereRaw('DATE(callback_time) = ?', $this->todayDate)
            ->count();

        $this->unactionedPendingCallbacks = Callback::where('attempted_by', $this->user->id)
            ->whereRaw('DATE(callback_time) < ?', $this->todayDate)
            ->count();

        $lastUnActionedCallback = Callback::where('attempted_by', $this->user->id)
            ->whereRaw('DATE(callback_time) < ?', $this->todayDate)
            ->orderBy('callback_time', 'asc')
            ->first();

        if($lastUnActionedCallback)
        {
            $this->lastCallBackDate = $lastUnActionedCallback->callback_time->format('Y-m-d');
        }
    }

}
