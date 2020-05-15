@extends('admin.admin_layouts')

@section('styles')
    <link href="{{ asset('assets/modules/bootstrap-fileinput/bootstrap-fileinput.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/modules/select2/dist/css/select2.min.css') }}" rel="stylesheet">
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

    @include('admin.includes.update_info')

    <div class="row">
        <div class="col-md-3">
            @include('admin.includes.setting_sidebar')
        </div>
        <div class="col-md-9">
            {!! Form::open(['url' => '','class'=> ' ajax-form', 'id'=>'add-edit-company-form']) !!}
            <div class="row">
                <div class="col-md-12">
                    <div class="card" id="settings-card">
                        <div class="card-header">
                            <h4>@lang('module_settings.editEmailSettings')</h4>
                        </div>
                        <div class="card-body">
                            <div id="alert">
                                @if($editSetting->verified)
                                    <div class="alert alert-success mail_driver_smtp" @if($editSetting->mail_driver == 'mail') style="display: none;" @endif>
                                        @lang('messages.smtpSuccess')
                                    </div>
                                @else
                                    <div class="alert alert-danger mail_driver_smtp" @if($editSetting->mail_driver == 'mail') style="display: none;" @endif>
                                        @lang('messages.smtpError')
                                    </div>
                                @endif
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label class="d-block">@lang('module_settings.mailDriver')</label>
                                        <div class="custom-control custom-radio custom-control-inline">
                                            <input type="radio" id="mail_driver_checkbox" name="mail_driver" class="custom-control-input" onclick="mailDriverChanged('mail')" value="mail" @if($editSetting->mail_driver == 'mail') checked @endif>
                                            <label class="custom-control-label" for="mail_driver_checkbox">@lang('module_settings.mail')</label>
                                        </div>
                                        <div class="custom-control custom-radio custom-control-inline">
                                            <input type="radio" id="mail_driver_smtp_checkbox" name="mail_driver" class="custom-control-input" onclick="mailDriverChanged('smtp')" value="smtp" @if($editSetting->mail_driver == 'smtp') checked @endif>
                                            <label class="custom-control-label" for="mail_driver_smtp_checkbox">@lang('module_settings.smtp')</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row mail_driver_smtp" @if($editSetting->mail_driver == 'mail') style="display: none;" @endif>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label class="control-label">@lang('module_settings.mailHost')</label>
                                        <input type="text" name="mail_host" class="form-control" value="{{ $editSetting->mail_host }}">
                                    </div>
                                </div>
                            </div>
                            <div class="row mail_driver_smtp" @if($editSetting->mail_driver == 'mail') style="display: none;" @endif>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label class="control-label">@lang('module_settings.mailPort')</label>
                                        <input type="text" name="mail_port" class="form-control" value="{{ $editSetting->mail_port }}">
                                    </div>
                                </div>
                            </div>
                            <div class="row mail_driver_smtp" @if($editSetting->mail_driver == 'mail') style="display: none;" @endif>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label class="control-label">@lang('module_settings.mailUserName')</label>
                                        <input type="text" name="mail_username" class="form-control" value="{{ $editSetting->mail_username }}">
                                    </div>
                                </div>
                            </div>
                            <div class="row mail_driver_smtp" @if($editSetting->mail_driver == 'mail') style="display: none;" @endif>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label class="control-label">@lang('module_settings.mailPassword')</label>
                                        <input type="password" name="mail_password" class="form-control" value="{{ $editSetting->mail_password }}">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label class="control-label">@lang('module_settings.mailFromName')</label>
                                        <input type="text" name="mail_from_name" class="form-control" value="{{ $editSetting->mail_from_name }}">
                                    </div>
                                </div>
                            </div>
                            <div class="row mail_driver_mail">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label class="control-label">@lang('module_settings.mailFromEmail')</label>
                                        <input type="text" name="mail_from_email" class="form-control" value="{{ $editSetting->mail_from_email }}">
                                    </div>
                                </div>
                            </div>
                            <div class="row mail_driver_smtp" @if($editSetting->mail_driver == 'mail') style="display: none;" @endif>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label class="control-label">@lang('module_settings.mailEncryption')</label>
                                        <select id="mail_encryption" name="mail_encryption" class="form-control">
                                            <option value="tls" @if($editSetting->mail_encryption == 'tls') selected @endif>TLS</option>
                                            <option value="ssl" @if($editSetting->mail_encryption == 'ssl') selected @endif>SSL</option>
                                            <option value="null" @if($editSetting->mail_encryption == null) selected @endif>@lang('app.none')</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer bg-whitesmoke text-md-left">
                            <button class="btn btn-primary" onclick="updateSetting();return false"><i class="fas fa-check"></i> @lang('app.save')</button>
                            <button class="btn btn-info" type="button" onclick="sendTestMailModal();return false"><i class="fa fa-paper-plane"></i> @lang('module_settings.send') @lang('module_settings.testMail')</button>
                        </div>
                    </div>
                </div>
            </div>
            {{Form::close()}}
        </div>
    </div>
@endsection

@section('modals')
    @include('admin.includes.add-edit-modal')
@endsection

@section('scripts')
    <script src="{{ asset('assets/modules/bootstrap-fileinput/bootstrap-fileinput.js') }}"></script>
    <script src="{{ asset('assets/modules/select2/dist/js/select2.full.min.js') }}"></script>

    <script>
        function mailDriverChanged(mailDriver) {
            if(mailDriver == 'mail')
            {
                $('.mail_driver_smtp').hide();
            } else {
                $('.mail_driver_smtp').show();
            }
        }

        function updateSetting () {
            $.easyAjax({
                url: '{{route('admin.settings.email.store')}}',
                container: '#add-edit-company-form',
                type: "POST",
                messagePosition: "inline",
                data: $('#add-edit-company-form').serialize(),
                success: function (response) {
                    if (response.status == 'error') {
                        $('#alert').prepend('<div class="alert alert-danger">{{__('messages.smtpError')}}</div>')
                    } else {
                        $('#alert').show();
                    }
                }
            })
        }

        function sendTestMailModal() {
            $.ajaxModal('#addEditModal','{{ route('admin.settings.get-send-mail-modal') }}');
        }

        function sendTestMail() {
            $.easyAjax({
                url: '{{route('admin.settings.send-test-email')}}',
                container: '#test-mail-form',
                type: "POST",
                messagePosition: "inline",
                data: $('#test-mail-form').serialize(),

            })
        }
    </script>
@endsection