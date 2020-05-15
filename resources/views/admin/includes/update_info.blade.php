@if($settings->app_update && $user->hasRole('admin'))
    @php($updateVersionInfo = \Froiden\Envato\Functions\EnvatoUpdate::updateVersionInfo())
    @if(isset($updateVersionInfo['lastVersion']))
        <div class="alert alert-info alert-has-icon">
            <div class="alert-icon"><i class="fas fa-gift"></i></div>
            <div class="alert-body">
                <div class="alert-title">
                    @lang('module_settings.newUpdate')
                    <label class="label label-success">{{ $updateVersionInfo['lastVersion'] }}</label>
                </div>
                <a href="{{ route('admin.settings.update-app.index') }}" class="btn btn-success btn-small">
                    @lang('module_settings.updateNow') <i class="fa fa-arrow-right"></i>
                </a>
            </div>
        </div>
    @endif
@endif