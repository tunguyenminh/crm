@extends('admin.admin_layouts')

@section('styles')
    <link href="{{ asset('assets/modules/jquery-selectric/selectric.css') }}" rel="stylesheet">
@endsection

@section('breadcrumb')

    <div class="section-header">
        <h1><i class="{{ $pageIcon }}"></i> {{ $pageTitle }}</h1>
        <div class="section-header-breadcrumb">
            <div class="breadcrumb-item active"><a href="{{ route('admin.dashboard.index') }}">@lang('menu.home')</a></div>
            <div class="breadcrumb-item">{{ $pageTitle }}</div>
        </div>
    </div>
@endsection

@section('content')

    <div class="row">
        <div class="col-md-3">
            @include('admin.includes.setting_sidebar')
        </div>
        <div class="col-md-9">

            <ul class="nav nav-tabs custom-tab" id="myTab" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" id="twilio-settings-tab" data-toggle="tab" href="#twilio-settings-app" role="tab" aria-controls="twilio-settings-app" aria-selected="true"><i class="fa fa-cog"></i> @lang('module_settings.twilioSettings')</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="twilio-numbers-tab" data-toggle="tab" href="#twilio-numbers-app" role="tab" aria-controls="twilio-numbers-app" aria-selected="true"><i class="fa fa-mobile-alt"></i> @lang('module_settings.twilioNumbers')</a>
                </li>
            </ul>
            <div class="tab-content" id="myTabContent">
                <div class="tab-pane fade show active" id="twilio-settings-app" role="tabpanel" aria-labelledby="twilio-settings-tab">
                <div class="alert alert-info alert-has-icon">
                    <div class="alert-icon"><i class="far fa-lightbulb"></i></div>
                    <div class="alert-body">
                        <div class="alert-title">@lang('module_settings.outboundCallRequestUrl')</div>
                        {{ route('front.twilio.support-call') }}
                    </div>
                </div>

                    {!! Form::open(['url' => '','class'=> ' ajax-form', 'id'=>'add-edit-calls-form']) !!}
                    <div class="row">
                        <div class="col-md-12">
                            <div class="card" id="settings-card">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <div class="control-label">@lang('module_settings.enableTwilio')</div>
                                                <label class="custom-switch mt-2">
                                                    <input type="checkbox" name="twilio_enabled" class="custom-switch-input" value="1" @if($editSetting->twilio_enabled) checked @endif onchange="showHideTwilioFields(this)">
                                                    <span class="custom-switch-indicator"></span>
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row show_hide_twilio_fields" @if(!$editSetting->twilio_enabled) style="display: none;" @endif>
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label class="control-label">@lang('module_settings.twilioAccountSid')</label>
                                            <input type="text" name="twilio_account_sid" class="form-control" value="{{ $editSetting->twilio_account_sid }}">
                                            <small id="twilioAccountSidHelpBlock" class="form-text text-muted">
                                                @lang('module_settings.clickHereToFindIt') : <a href="https://www.twilio.com/console" target="_blank">https://www.twilio.com/console</a>
                                            </small>
                                        </div>
                                    </div>
                                </div>
                                    <div class="row show_hide_twilio_fields" @if(!$editSetting->twilio_enabled) style="display: none;" @endif>
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label class="control-label">@lang('module_settings.twilioAuthToken')</label>
                                                <input type="text" name="twilio_auth_token" class="form-control" value="{{ $editSetting->twilio_auth_token }}">
                                                <small id="twilioAuthTokenHelpBlock" class="form-text text-muted">
                                                    @lang('module_settings.clickHereToFindIt') : <a href="https://www.twilio.com/console" target="_blank">https://www.twilio.com/console</a>
                                                </small>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row show_hide_twilio_fields" @if(!$editSetting->twilio_enabled) style="display: none;" @endif>
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label class="control-label">@lang('module_settings.twilioApplicationSid')</label>
                                                <input type="text" name="twilio_application_sid" class="form-control" value="{{ $editSetting->twilio_application_sid }}">
                                                <small id="twilioApplicationSidHelpBlock" class="form-text text-muted">
                                                    @lang('module_settings.clickHereToFindIt') : <a href="https://www.twilio.com/console/voice/twiml/apps" target="_blank">https://www.twilio.com/console/voice/twiml/apps</a>
                                                </small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-footer bg-whitesmoke text-md-left">
                                    <button class="btn btn-primary" onclick="updateSetting();return false">@lang('app.save')</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    {{Form::close()}}
                </div>
                <div class="tab-pane fade" id="twilio-numbers-app" role="tabpanel" aria-labelledby="twilio-numbers-tab">
                    <div class="alert alert-info alert-has-icon">
                        <div class="alert-icon"><i class="far fa-lightbulb"></i></div>
                        <div class="alert-body">
                            <div class="alert-title">@lang('module_settings.inboundCallWebhook')</div>
                                {{ route('front.twilio.inbound-webhook', [md5($twilioNumber->id)]) }}
                                <br>
                                @lang('module_settings.inboundCallWebhookMessage')
                        </div>
                    </div>

                    {!! Form::open(['url' => '','class'=> ' ajax-form', 'id'=>'add-edit-number-form']) !!}
                    <div class="row">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label class="control-label">@lang('module_settings.phoneNumber')</label>
                                                <input type="text" name="number" class="form-control" value="{{ $twilioNumber->number }}">
                                                <small id="twilioPhoneNumberHelpBlock" class="form-text text-muted">
                                                    @lang('module_settings.clickHereToFindIt') : <a href="https://www.twilio.com/console/voice/numbers" target="_blank">https://www.twilio.com/console/voice/numbers</a>
                                                </small>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label>@lang('module_settings.outboundCallRecordingOption')</label>
                                                <select class="form-control selectric" name="outbound_recording">
                                                    @foreach($recordingOptions as $recordingOptionKey => $recordingOptionValue)
                                                        <option value="{{ $recordingOptionKey }}" @if($twilioNumber->outbound_recording == $recordingOptionKey) selected @endif>{{ $recordingOptionValue }}</option>
                                                    @endforeach
                                                </select>
                                                <small id="twilioRecordingHelpBlock" class="form-text text-muted">
                                                    @lang('module_settings.clickHereToFindIt') : <a href="https://www.twilio.com/docs/voice/twiml/dial#record" target="_blank">https://www.twilio.com/docs/voice/twiml/dial#record</a>
                                                </small>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label>@lang('module_settings.inboundCallRecordingOption')</label>
                                                <select class="form-control selectric" name="inbound_recording">
                                                    @foreach($recordingOptions as $recordingOptionKey => $recordingOptionValue)
                                                        <option value="{{ $recordingOptionKey }}" @if($twilioNumber->inbound_recording == $recordingOptionKey) selected @endif>{{ $recordingOptionValue }}</option>
                                                    @endforeach
                                                </select>
                                                <small id="twilioRecordingHelpBlock" class="form-text text-muted">
                                                    @lang('module_settings.clickHereToFindIt') : <a href="https://www.twilio.com/docs/voice/twiml/dial#record" target="_blank">https://www.twilio.com/docs/voice/twiml/dial#record</a>
                                                </small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-footer bg-whitesmoke text-md-left">
                                    <button class="btn btn-primary" onclick="updateTwilioNumber();return false">@lang('app.save')</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    {{Form::close()}}
                </div>
            </div>
        </div>
    </div>

@endsection

@section('scripts')
<script src="{{ asset('assets/modules/jquery-selectric/jquery.selectric.min.js') }}"></script>
<script>
    function showHideTwilioFields(enableCheckbox) {
        if($(enableCheckbox).is(':checked'))
        {
            $('.show_hide_twilio_fields').fadeIn('slow');
        } else {
            $('.show_hide_twilio_fields').fadeOut('slow');
        }
    }

    function updateSetting () {
        $.easyAjax({
            url: '{{route('admin.settings.calls.store')}}',
            container: "#add-edit-calls-form",
            type: 'POST',
            file: true,
            messagePosition: "toastr",
            redirect: true
        })
    }

    function updateTwilioNumber () {
        $.easyAjax({
            url: '{{route('admin.settings.calls.save-twilio-number')}}',
            container: "#add-edit-number-form",
            type: 'POST',
            file: true,
            messagePosition: "toastr"
        })
    }
</script>
@endsection