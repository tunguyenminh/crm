@php($updateVersionInfo = \Froiden\Envato\Functions\EnvatoUpdate::updateVersionInfo())
@php($envatoUpdateCompanySetting = \Froiden\Envato\Functions\EnvatoUpdate::companySetting())
<div class="table table-responsive mt-2">

    <table class="table table-bordered">
        <thead>
        <th>@lang('module_settings.systemDetails')</th>
        </thead>
        <tbody>
        <tr>
            <td>@lang('module_settings.appVersion') <span
                        class="pull-right">{{ $updateVersionInfo['appVersion'] }}</span></td>
        </tr>
        <tr>
            <td>@lang('module_settings.laravelVersion') <span
                        class="pull-right">{{ $updateVersionInfo['laravelVersion'] }}</span></td>
        </tr>
        <td>@lang('module_settings.phpVersion')
            @if (version_compare(PHP_VERSION, '7.1.0') > 0)
                <span class="pull-right">{{ phpversion() }} <i class="fa fa fa-check-circle text-success"></i></span>
            @else
                <span class="pull-right">{{ phpversion() }} <i  data-toggle="tooltip" data-original-title="@lang('messages.phpUpdateRequired')" class="fa fa fa-warning text-danger"></i></span>
            @endif
        </td>
        @if(!is_null($envatoUpdateCompanySetting->purchase_code))
            <tr>
                <td>@lang('module_settings.envatoPurchaseCode') <span
                            class="pull-right">{{$envatoUpdateCompanySetting->purchase_code}}</span>
                </td>
            </tr>
        @endif
        </tbody>
    </table>
</div>
