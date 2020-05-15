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
        @if($user->hasRole('admin'))
        <div class="col-md-3">
            @include('admin.includes.setting_sidebar')
        </div>
        @endif
        <div @if($user->hasRole('admin')) class="col-md-9" @else class="col-md-12" @endif>
            {!! Form::open(['url' => '','class'=> ' ajax-form', 'id'=>'add-edit-profile-form']) !!}
            <div class="row">
                <div class="col-md-12">
                    <div class="card" id="settings-card">
                        <div class="card-header">
                        <h4>@lang('module_settings.editProfileSettings')</h4>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="control-label">@lang('app.your') @lang('app.firstName')</label>
                                        <input type="text" name="first_name" class="form-control" value="{{ $user->first_name }}">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="control-label">@lang('app.your') @lang('app.lastName')</label>
                                        <input type="text" name="last_name" class="form-control" value="{{ $user->last_name }}">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="control-label">@lang('app.your') @lang('app.email')</label>
                                        <input type="text" name="email" class="form-control" value="{{ $user->email }}" disabled>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="control-label">@lang('app.your') @lang('app.skypeId')</label>
                                        <input type="text" name="skype_id" class="form-control" value="{{ $user->skype_id }}">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="control-label">@lang('app.your') @lang('app.phone')</label>
                                        <input type="text" name="contact_number" class="form-control" value="{{ $user->contact_number }}">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="control-label">@lang('app.password')</label>
                                        <input type="password" name="password" class="form-control" autocomplete="false" value="">
                                        <small id="passwordHelpBlock" class="form-text text-muted">
                                            @lang('module_settings.passwordHelpMessage')
                                        </small>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label class="control-label">@lang('app.address')</label>
                                        <textarea name="address" class="form-control" rows="8">{{ $user->address }}</textarea>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="exampleInputPassword1">@lang('module_settings.profileImage')</label>
                                        <div class="col-md-12">
                                            <div class="fileinput fileinput-new" data-provides="fileinput">
                                                <div class="fileinput-new thumbnail" style="width: 200px; height: 150px;">
                                                    <img src="@if($user->image != NULL) {{ asset($userImagePath.$user->image) }} @else {{ asset('assets/no-image.png') }} @endif" alt=""/>
                                                </div>
                                                <div class="fileinput-preview fileinput-exists thumbnail" style="max-width: 200px; max-height: 150px;">
                                                </div>
                                                <div>
                                          <span class="btn btn-info btn-file">
                                          <span class="fileinput-new">
                                          @lang('app.selectImage') </span>
                                          <span class="fileinput-exists">
                                          @lang('app.change') </span>
                                          <input type="file" name="image" id="image">
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
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="control-label">@lang('app.country')</label>
                                        <input type="text" name="country" class="form-control" value="{{ $user->country }}">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="control-label">@lang('app.zipCode')</label>
                                        <input type="text" name="zip_code" class="form-control" value="{{ $user->zip_code }}">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label class="control-label">@lang('module_settings.defaultTimezone')</label>
                                        <select name="timezone" id="timezone" class="form-control select2">
                                            @foreach($timezones as $timezone)
                                                <option value="{{ $timezone }}" @if($user->timezone == $timezone) selected @endif>{{ $timezone }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="control-label">@lang('module_settings.dateFormat')</label>
                                        <select name="date_format" id="date_format" class="form-control select2">
                                            <option value="d-m-Y" @if($user->date_format == 'd-m-Y') selected @endif >d-m-Y ({{ $dateObject->format('d-m-Y') }}) </option>
                                            <option value="m-d-Y" @if($user->date_format == 'm-d-Y') selected @endif >m-d-Y ({{ $dateObject->format('m-d-Y') }}) </option>
                                            <option value="Y-m-d" @if($user->date_format == 'Y-m-d') selected @endif >Y-m-d ({{ $dateObject->format('Y-m-d') }}) </option>
                                            <option value="d.m.Y" @if($user->date_format == 'd.m.Y') selected @endif >d.m.Y ({{ $dateObject->format('d.m.Y') }}) </option>
                                            <option value="m.d.Y" @if($user->date_format == 'm.d.Y') selected @endif >m.d.Y ({{ $dateObject->format('m.d.Y') }}) </option>
                                            <option value="Y.m.d" @if($user->date_format == 'Y.m.d') selected @endif >Y.m.d ({{ $dateObject->format('Y.m.d') }}) </option>
                                            <option value="d/m/Y" @if($user->date_format == 'd/m/Y') selected @endif >d/m/Y ({{ $dateObject->format('d/m/Y') }}) </option>
                                            <option value="m/d/Y" @if($user->date_format == 'm/d/Y') selected @endif >m/d/Y ({{ $dateObject->format('m/d/Y') }}) </option>
                                            <option value="Y/m/d" @if($user->date_format == 'Y/m/d') selected @endif >Y/m/d ({{ $dateObject->format('Y/m/d') }}) </option>
                                            <option value="d-M-Y" @if($user->date_format == 'd-M-Y') selected @endif >d-M-Y ({{ $dateObject->format('d-M-Y') }}) </option>
                                            <option value="d/M/Y" @if($user->date_format == 'd/M/Y') selected @endif >d/M/Y ({{ $dateObject->format('d/M/Y') }}) </option>
                                            <option value="d.M.Y" @if($user->date_format == 'd.M.Y') selected @endif >d.M.Y ({{ $dateObject->format('d.M.Y') }}) </option>
                                            <option value="d-M-Y" @if($user->date_format == 'd-M-Y') selected @endif >d-M-Y ({{ $dateObject->format('d-M-Y') }}) </option>
                                            <option value="d M Y" @if($user->date_format == 'd M Y') selected @endif >d M Y ({{ $dateObject->format('d M Y') }}) </option>
                                            <option value="d F, Y" @if($user->date_format == 'd F, Y') selected @endif >d F, Y ({{ $dateObject->format('d F, Y') }}) </option>
                                            <option value="D/M/Y" @if($user->date_format == 'D/M/Y') selected @endif >D/M/Y ({{ $dateObject->format('D/M/Y') }}) </option>
                                            <option value="D.M.Y" @if($user->date_format == 'D.M.Y') selected @endif >D.M.Y ({{ $dateObject->format('D.M.Y') }}) </option>
                                            <option value="D-M-Y" @if($user->date_format == 'D-M-Y') selected @endif >D-M-Y ({{ $dateObject->format('D-M-Y') }}) </option>
                                            <option value="D M Y" @if($user->date_format == 'D M Y') selected @endif >D M Y ({{ $dateObject->format('D M Y') }}) </option>
                                            <option value="d D M Y" @if($user->date_format == 'd D M Y') selected @endif >d D M Y ({{ $dateObject->format('d D M Y') }}) </option>
                                            <option value="D d M Y" @if($user->date_format == 'D d M Y') selected @endif >D d M Y ({{ $dateObject->format('D d M Y') }}) </option>
                                            <option value="dS M Y" @if($user->date_format == 'dS M Y') selected @endif >dS M Y ({{ $dateObject->format('dS M Y') }}) </option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="control-label">@lang('module_settings.timeFormat')</label>
                                        <select name="time_format" id="time_format" class="form-control select2">
                                            <option value="h:i A" @if($user->time_format == 'H:i A') selected @endif >12 Hour  (6:20 PM) </option>
                                            <option value="h:i a" @if($user->time_format == 'H:i a') selected @endif >12 Hour  (6:20 pm) </option>
                                            <option value="H:i" @if($user->time_format == 'H:i') selected @endif >24 Hour  (18:20) </option>
                                        </select>
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
                url: '{{route('admin.settings.profile.store')}}',
                container: '#add-edit-profile-form',
                type: "POST",
                redirect: true,
                file: true
            })
        }
    </script>
@endsection