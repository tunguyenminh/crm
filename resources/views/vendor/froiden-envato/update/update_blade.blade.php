@php($updateVersionInfo = \Froiden\Envato\Functions\EnvatoUpdate::updateVersionInfo())
@if(isset($updateVersionInfo['lastVersion']))
    <div class="alert alert-danger alert-has-icon">
        <div class="alert-icon"><i class="fas fa-exclamation-triangle"></i></div>
        <div class="alert-body">
            <p> @lang('messages.updateAlert')</p>
            <p>@lang('messages.updateBackupNotice')</p>
        </div>
    </div>


    <div class="alert alert-info col-md-12">
        <div class="row">
            <div class="col-md-9">
                <i class="fa fa-gift"></i> @lang('module_settings.newUpdate') <label
                        class="label label-success">{{ $updateVersionInfo['lastVersion'] }}</label>
                <br><br>
                <h6 class="text-white font-bold"><label class="badge badge-danger">ALERT</label> You will get logged
                    out after update. Login again to use the application.</h6>
                <span>@lang('module_settings.updateAlternate')</span>
            </div>
            <div class="col-md-3 text-center">
            <a id="update-app" href="javascript:;"
               class="btn btn-success btn-small">@lang('module_settings.updateNow') <i
                        class="fa fa-download"></i></a>
        </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <p>{!! $updateVersionInfo['updateInfo'] !!}</p>
            </div>
        </div>
    </div>
    <div id="update-area" class="m-t-20 m-b-20 col-md-12 white-box hide">
        Loading...
    </div>
@else
    <div class="alert alert-success col-md-12">
        <div class="col-md-12">You have latest version of this app.</div>
    </div>
@endif
