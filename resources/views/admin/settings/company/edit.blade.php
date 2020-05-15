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
                        <h4>@lang('module_settings.editCompanySettings')</h4>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="control-label">@lang('app.company') @lang('app.name')</label>
                                        <input type="text" name="name" class="form-control" value="{{ $editSetting->name }}">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="control-label">@lang('app.company') @lang('app.shortName')</label>
                                        <input type="text" name="short_name" class="form-control" value="{{ $editSetting->short_name }}">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="control-label">@lang('app.company') @lang('app.email')</label>
                                        <input type="text" name="email" class="form-control" value="{{ $editSetting->email }}">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="control-label">@lang('app.company') @lang('app.phone')</label>
                                        <input type="text" name="phone" class="form-control" value="{{ $editSetting->phone }}">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label class="control-label">@lang('app.address')</label>
                                        <textarea name="address" class="form-control" rows="8">{{ $editSetting->address }}</textarea>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="control-label">@lang('app.locale')</label>
                                        <select name="lang" id="lang" class="form-control">
                                            @foreach($allLangs as $allLang)
                                                <option value="{{ $allLang }}" @if($editSetting->locale == $allLang) selected @endif>{{ $allLang }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="control-label">@lang('app.layout')</label>
                                        <select name="app_layout" id="app_layout" class="form-control">
                                            <option value="sidebar" @if($editSetting->app_layout == 'sidebar') selected @endif>Sidebar</option>
                                            <option value="top" @if($editSetting->app_layout == 'top') selected @endif>Top</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="exampleInputPassword1">@lang('module_settings.companyLogo')</label>
                                        <div class="col-md-12">
                                            <div class="fileinput fileinput-new" data-provides="fileinput">
                                                <div class="fileinput-new thumbnail" style="width: 200px; height: 150px;">
                                                    <img src="@if($editSetting->logo != NULL) {{ asset($companyLogoPath.$editSetting->logo) }} @else {{ asset('assets/no-image.png') }} @endif" alt=""/>
                                                </div>
                                                <div class="fileinput-preview fileinput-exists thumbnail" style="max-width: 200px; max-height: 150px;">
                                                </div>
                                                <div>
                                          <span class="btn btn-info btn-file">
                                          <span class="fileinput-new">
                                          @lang('app.selectImage') </span>
                                          <span class="fileinput-exists">
                                          @lang('app.change') </span>
                                          <input type="file" name="logo" id="logo">
                                          </span>
                                                    <a href="#" class="btn btn-danger fileinput-exists" data-dismiss="fileinput">
                                                        Remove </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <div class="control-label">@lang('module_settings.updateApp')</div>
                                        <label class="custom-switch mt-2">
                                            <input type="checkbox" name="app_update" class="custom-switch-input" value="1" @if($editSetting->app_update) checked @endif>
                                            <span class="custom-switch-indicator"></span>
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <div class="control-label">@lang('module_settings.appDebug')</div>
                                        <label class="custom-switch mt-2">
                                            <input type="checkbox" name="app_debug" class="custom-switch-input" value="1" @if($editSetting->app_debug) checked @endif>
                                            <span class="custom-switch-indicator"></span>
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <div class="control-label">@lang('module_settings.rtlMode')</div>
                                        <label class="custom-switch mt-2">
                                            <input type="checkbox" name="rtl" class="custom-switch-input" value="1" @if($editSetting->rtl) checked @endif>
                                            <span class="custom-switch-indicator"></span>
                                        </label>
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
    </div>



@endsection

@section('modals')
    @include('admin.includes.add-edit-modal')
@endsection

@section('scripts')
    <script src="{{ asset('assets/modules/bootstrap-fileinput/bootstrap-fileinput.js') }}"></script>
    <script src="{{ asset('assets/modules/select2/dist/js/select2.full.min.js') }}"></script>

    <script>
        function updateSetting () {
            $.easyAjax({
                url: '{{route('admin.settings.company.store')}}',
                container: '#add-edit-company-form',
                type: "POST",
                redirect: true,
                file: true
            })
        }
    </script>
@endsection