@extends('admin.admin_layouts')

@section('styles')
    <link href="{{ asset('assets/modules/dropzonejs/dropzone.css') }}" rel="stylesheet">
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
            @php($envatoUpdateCompanySetting = \Froiden\Envato\Functions\EnvatoUpdate::companySetting())

            @if(!is_null($envatoUpdateCompanySetting->supported_until))
                <div id="support-div">
                    @if(\Carbon\Carbon::parse($envatoUpdateCompanySetting->supported_until)->isPast())
                        <div class="col-md-12 alert alert-danger ">
                            <div class="col-md-6">
                                Your support has been expired on <b><span
                                            id="support-date">{{\Carbon\Carbon::parse($envatoUpdateCompanySetting->supported_until)->format('d M, Y')}}</span></b>
                            </div>
                            <div class="col-md-6 text-right">
                                <a href="{{ config('froiden_envato.envato_product_url') }}" target="_blank"
                                   class="btn btn-inverse btn-small">Renew support <i class="fa fa-shopping-cart"></i></a>
                                <a href="javascript:;" onclick="getPurchaseData();" class="btn btn-inverse btn-small">Refresh
                                    <i
                                            class="fa fa-refresh"></i></a>
                            </div>
                        </div>
                    @else
                        <div class="alert alert-primary alert-has-icon">
                            <div class="alert-icon"><i class="far fa-life-ring"></i></div>
                            <div class="alert-body">
                                <div class="alert-title">@lang('module_settings.support')</div>
                                @lang('module_settings.supportWillExpireOn') <b><span
                                            id="support-date">{{\Carbon\Carbon::parse($envatoUpdateCompanySetting->supported_until)->format('d M, Y')}}</span></b>
                            </div>
                        </div>
                    @endif
                </div>
            @endif

            <ul class="nav nav-tabs custom-tab" id="myTab" role="tablist">
                <li class="nav-item">
                    <a class="nav-link @if($activeUpdateTab == 'appDetails' || !$settings->app_update) active @endif" id="system-details-tab" data-toggle="tab" href="#system-details-app" role="tab" aria-controls="system-details-app" aria-selected="true"><i class="fa fa-address-book"></i> @lang('module_settings.systemDetails')</a>
                </li>
                @if($settings->app_update)
                    <li class="nav-item">
                        <a class="nav-link @if($activeUpdateTab == 'oneClickUpdate') active @endif" id="one-click-update-app-tab" data-toggle="tab" href="#one-click-update-app" role="tab" aria-controls="one-click-update-app" aria-selected="false"><i class="fa fa-chart-pie"></i> @lang('module_settings.oneClickUpdate')</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="manual-update-app-tab" data-toggle="tab" href="#manual-update-app" role="tab" aria-controls="manual-update-app" aria-selected="false"><i class="fa fa-upload"></i> @lang('module_settings.manualUpdate')</a>
                    </li>
                @endif
            </ul>
            <div class="tab-content" id="myTabContent">
                <div class="tab-pane fade @if($activeUpdateTab == 'appDetails' || !$settings->app_update) show active @endif" id="system-details-app" role="tabpanel" aria-labelledby="system-details-tab">
                    @include('vendor.froiden-envato.update.version_info')
                </div>
                @if($settings->app_update)
                <div class="tab-pane fade @if($activeUpdateTab == 'oneClickUpdate') show active @endif" id="one-click-update-app" role="tabpanel" aria-labelledby="one-click-update-app-tab">
                    @include('vendor.froiden-envato.update.update_blade')
                </div>
                <div class="tab-pane fade" id="manual-update-app" role="tabpanel" aria-labelledby="manual-update-app-tab">
                    <div class="col-md-12">
                        <div class="alert alert-danger alert-has-icon">
                            <div class="alert-icon"><i class="fas fa-exclamation-triangle"></i></div>
                            <div class="alert-body">
                                <p>@lang('messages.updateBackupNotice')</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-12">
                                        <h6 class="text-info">@lang('app.step1')</h6>
                                        <a href="{{ $downloadLink }}" class="btn btn-outline-success btn-small">
                                            <i class="fa fa-download"></i> @lang('module_settings.downloadUpdateFile')
                                        </a>
                                    </div>
                                    <div class="col-md-12 mt-5">
                                        <h6 class="text-info">@lang('app.step2')</h6>
                                        <form action="{{ route('admin.settings.update-app.store') }}" class="dropzone" id="file-upload-dropzone">
                                            {{ csrf_field() }}

                                            <div class="fallback">
                                                <input name="file" type="file" multiple />
                                            </div>
                                        </form>
                                    </div>

                                    <div class="col-md-12" id="install-process">

                                    </div>

                                    <div class="col-md-12 mt-5">
                                        <h6 class="text-info">@lang('app.step3')</h6>
                                        <ul class="list-group" id="files-list">
                                            @include('admin.settings.update-app.manual_files')
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>

@endsection

@section('scripts')
    <script src="{{ asset('assets/modules/dropzonejs/min/dropzone.min.js') }}"></script>
    @include('vendor.froiden-envato.update.update_script')

    <script>

        Dropzone.options.fileUploadDropzone = {
            paramName: "file", // The name that will be used to transfer the file
            dictDefaultMessage: "@lang('module_settings.dropzoneUploadText')",
            accept: function (file, done) {
                done();
            },
            init: function () {
                this.on("success", function (file, response) {
                    $('#files-list').html(response.html);
                })
            }
        };

        var dropzone = new Dropzone("#file-upload-dropzone");

        function deleteUploadedFile(fileNumber, filePath) {
            swal({
                title: "{{ trans('app.areYouSure') }}",
                text: "{{ trans('app.areYouSureText') }}",
                dangerMode: true,
                icon: 'warning',
                buttons: {
                    cancel: "{{ trans('app.noCancelIt') }}",
                    confirm: {
                        text: "{{ trans('app.yesDeleteIt') }}",
                        value: true,
                        visible: true,
                        className: "danger",
                    }
                },
            }).then(function(isConfirm) {
                if (isConfirm) {
                    $.easyAjax({
                        type: 'POST',
                        container: '#manual-update-app',
                        url: '{!! route("admin.settings.update-app.delete-file") !!}',
                        data: {"_token": "{{ csrf_token() }}", filePath: filePath},
                        messagePosition: "toastr",
                        success: function (response) {
                            $('#file-'+fileNumber).remove();
                        }
                    });
                }
            });
        }

        function installUploadedFile(fileNumber, filePath) {
            swal({
                title: "{{ trans('app.install') }}",
                text: "{{ trans('app.areYouSure') }}",
                dangerMode: false,
                icon: 'warning',
                buttons: {
                    confirm: {
                        text: "{{ trans('app.install') }}",
                        value: true,
                        visible: true,
                        className: "warning",
                    },
                    cancel: "{{ trans('app.noCancelIt') }}",
                },
            }).then(function(isConfirm) {
                if (isConfirm) {
                    $('#install-process').html('<div class="alert alert-info alert-has-icon mt-5"><div class="alert-icon"><i class="far fa-lightbulb"></i></div><div class="alert-body"><div class="alert-title">@lang('module_settings.installing')</div>@lang('module_settings.installationInProcess') </div>');

                    $.easyAjax({
                        type: 'POST',
                        container: '#manual-update-app',
                        url: '{!! route("admin.settings.update-app.install-by-file") !!}',
                        data: {"_token": "{{ csrf_token() }}", filePath: filePath},
                        messagePosition: "toastr",
                        success: function (response) {
                            if(response.status == 'success')
                            {
                                $('#install-process').html('<div class="alert alert-success mt-5"><i class="fa fa-check"></i> @lang('module_settings.installationSuccess')</div>');
                                window.location.reload();
                            }
                        }
                    });
                }
            });
        }

    </script>
@endsection