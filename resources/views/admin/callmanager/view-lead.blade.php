<div class="modal-header">
    <h5 class="modal-title">
        <i class="fa fa-{{ $icon }}"></i> @lang('module_call_enquiry.viewLead')
    </h5>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close"> <span aria-hidden="true">×</span></button>
</div>
<div class="modal-body">
        <h5 class="box-title text-primary"><b>@lang('module_settings.basicDetails')</b></h5>
        <hr>

        <div class="row">
            <div class="col-sm-4">@lang('app.campaign')</div>
            <div class="col-sm-8">
                {{ $leadCampaign->name }}
            </div>
        </div>

        <div class="row mt-3">
            <div class="col-sm-4">@lang('module_lead.lastActionBy')</div>
            <div class="col-sm-8">
                {{ $lead->lastActioner ? $lead->lastActioner->name : '-' }}
            </div>
        </div>

        <div class="row mt-3">
            <div class="col-sm-4">@lang('module_lead.referenceNumber')</div>
            <div class="col-sm-8">
                {{ $lead->reference_number }}
            </div>
        </div>

        <div class="row mt-3">
            <div class="col-sm-4">@lang('module_lead.book_appointment')</div>
            <div class="col-sm-8">
                @if($lead->appointment_booked == 0)
                    @lang('app.no')
                @elseif($lead->appointment_booked == 1)
                    @lang('app.yes')
                @endif
            </div>
        </div>

        <div class="row mt-3">
            <div class="col-sm-4">@lang('module_lead.callDuration')</div>
            <div class="col-sm-8">
                {{ \App\Classes\Common::secondsToStrFull($lead->time_taken) }}
            </div>
        </div>

        <div class="row mt-3">
            <div class="col-sm-4">@lang('module_lead.interested')</div>
            <div class="col-sm-8">
                @if($lead->interested == 'interested')
                    @lang('module_lead.interested')
                @elseif($lead->interested == 'not_interested')
                    @lang('module_lead.notInterested')
                @elseif($lead->interested == 'unreachable')
                    @lang('module_lead.unreachable')
                @else
                    -
                @endif
            </div>
        </div>

        <h5 class="box-title text-info mt-5"><b>@lang('module_lead.leadDataInfo')</b></h5>
        <hr>

        @foreach($lead->leadData as $leadData)
        <div class="row mt-3">
            <div class="col-sm-4">{{ $leadData->field_name }}</div>
            <div class="col-sm-8">
                {{ $leadData->field_value }}
            </div>
        </div>
        @endforeach

        <h5 class="box-title text-warning mt-5"><b>@lang('menu.callHistory')</b></h5>
        <hr>

        <table class="table table-sm">
            <thead class="table-head">
            <tr>
                <th scope="col">@lang('module_call_enquiry.callingAgent')</th>
                <th scope="col">@lang('module_call_enquiry.duration')</th>
                <th scope="col">@lang('module_call_enquiry.calledOn')</th>
            </tr>
            </thead>
            <tbody>
            @foreach($lead->callLogs()->with('user:id,first_name,last_name')->latest()->get() as $callHistory)
                <tr>
                    <td>{{ $callHistory->user->name }}</td>
                    <td>{{ $callHistory->time_taken > 0 ? strtolower(\App\Classes\Common::secondsToStr($callHistory->time_taken)) : '-' }}</td>
                    <td>{{ $callHistory->created_at->format('d F, Y') }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
</div>
<div class="modal-footer bg-whitesmoke">
    <button class="btn default" data-dismiss="modal" aria-hidden="true">@lang('app.cancel')</button>
</div>