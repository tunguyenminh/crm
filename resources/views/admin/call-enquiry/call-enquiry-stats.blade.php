@if(isset($yourCampaigns))
<div class="col-lg-3 col-md-6 col-sm-6 col-12">
    <div class="card card-statistic-1">
        <div class="card-icon bg-primary">
            <i class="fas fa-business-time"></i>
        </div>
        <div class="card-wrap">
            <div class="card-header">
                <h4>@lang('module_call_enquiry.yourCampaigns')</h4>
            </div>
            <div class="card-body">
                {{ $yourCampaigns }}
            </div>
        </div>
    </div>
</div>
@endif

@if(isset($campaignMemberCount))
    <div class="col-lg-3 col-md-6 col-sm-6 col-12">
        <div class="card card-statistic-1">
            <div class="card-icon bg-primary">
                <i class="fas fa-users"></i>
            </div>
            <div class="card-wrap">
                <div class="card-header">
                    <h4>@lang('module_campaign.campaignMembers')</h4>
                </div>
                <div class="card-body">
                    {{ $campaignMemberCount }}
                </div>
            </div>
        </div>
    </div>
@endif

<div class="col-lg-3 col-md-6 col-sm-6 col-12">
    <div class="card card-statistic-1">
        <div class="card-icon bg-danger">
            <i class="far fa-newspaper"></i>
        </div>
        <div class="card-wrap">
            <div class="card-header">
                <h4>@lang('module_call_enquiry.totalLeads')</h4>
            </div>
            <div class="card-body">
                {{ $totalLeads }}
            </div>
        </div>
    </div>
</div>
<div class="col-lg-3 col-md-6 col-sm-6 col-12">
    <div class="card card-statistic-1">
        <div class="card-icon bg-warning">
            <i class="fas fa-mobile-alt"></i>
        </div>
        <div class="card-wrap">
            <div class="card-header">
                <h4>@lang('module_call_enquiry.callMade')</h4>
            </div>
            <div class="card-body">
                {{ $yourLeads }}
            </div>
        </div>
    </div>
</div>
<div class="col-lg-3 col-md-6 col-sm-6 col-12">
    <div class="card card-statistic-1">
        <div class="card-icon bg-success">
            <i class="fas fa-stopwatch"></i>
        </div>
        <div class="card-wrap">
            <div class="card-header">
                <h4>@lang('module_call_enquiry.totalDuration')</h4>
            </div>
            <div class="card-body">
                <p style="font-size: 12px; font-weight: 700;">{{ $totalTimes > 0 ? \App\Classes\Common::secondsToStr($totalTimes) : '-' }}</p>
            </div>
        </div>
    </div>
</div>