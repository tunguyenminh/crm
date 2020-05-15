@if($unactionedPendingCallbacks > 0)
    <div class="row">
        <div class="col-12">
            <div class="alert alert-danger alert-has-icon">
                <div class="alert-icon"><i class="fa fa-ban"></i></div>
                <div class="alert-body">
                    <div class="alert-title">@lang('app.actionRequired')</div>
                    {{ $unactionedPendingCallbacks }} @lang('module_call_enquiry.unactionedCallBackMessage')
                </div>
                <div class="mt-4">
                    <a href="javascript:void(0);" onclick="selectDateRange('{{ $lastCallBackDate }}', '{{ $todayDatePicker }}')" class="btn btn-outline-white btn-lg btn-icon icon-left"><i class="fa fa-check"></i> @lang('app.clickHereToView')</a>
                </div>
            </div>
        </div>
    </div>
@endif

@if($todayPendingCallbacks > 0)
    <div class="row">
        <div class="col-12">
            <div class="alert alert-warning alert-has-icon">
                <div class="alert-icon"><i class="far fa-lightbulb"></i></div>
                <div class="alert-body">
                    <div class="alert-title">@lang('app.warning')</div>
                    {{ $todayPendingCallbacks }} @lang('module_call_enquiry.todayPendingCallBackMessage')
                </div>
                <div class="mt-4">
                    <a href="javascript:void(0);" onclick="selectDateRange('{{ $todayDate }}', '{{ $todayDate }}')" class="btn btn-outline-white btn-lg btn-icon icon-left"><i class="fa fa-check"></i> @lang('app.clickHereToView')</a>
                </div>
            </div>
        </div>
    </div>
@endif