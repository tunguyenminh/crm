<div class="card">
    <div class="card-header">
        <h4><i class="fa fa-cog"></i> @lang('menu.settings')</h4>
    </div>
    <div class="card-body">
        <ul class="nav nav-pills flex-column">
            <li class="nav-item">
                <a href="{{ route('admin.settings.company.index') }}" class="nav-link {{ isset($companySettingsActive) ? $companySettingsActive : '' }}"><i class="fa fa-cogs"></i> @lang('module_settings.companySettings')</a>
            </li>
            <li class="nav-item">
                <a href="{{ route('admin.settings.profile.index') }}" class="nav-link {{ isset($profileSettingsActive) ? $profileSettingsActive : '' }}"><i class="fa fa-user"></i> @lang('module_settings.profileSettings')</a>
            </li>
            <li class="nav-item">
                <a href="{{ route('admin.settings.calls.index') }}" class="nav-link {{ isset($callSettingsActive) ? $callSettingsActive : '' }}"><i class="fa fa-phone-volume"></i> @lang('module_settings.callSettings')</a>
            </li>
            <li class="nav-item">
                <a href="{{ route('admin.settings.email.index') }}" class="nav-link {{ isset($emailSettingsActive) ? $emailSettingsActive : '' }}"><i class="fa fa-envelope"></i> @lang('module_settings.emailSettings')</a>
            </li>
            <li class="nav-item">
                <a href="{{ route('admin.settings.form-field-name.index') }}" class="nav-link {{ isset($formFieldNameSettingsActive) ? $formFieldNameSettingsActive : '' }}"><i class="fa fa-address-card"></i> @lang('module_settings.formFieldsName')</a>
            </li>
            <li class="nav-item">
                <a href="{{ route('admin.settings.roles.index') }}" class="nav-link {{ isset($roleSettingsActive) ? $roleSettingsActive : '' }}"><i class="fa fa-user-cog"></i> @lang('module_settings.roleAndPermissionSettings')</a>
            </li>
            <li class="nav-item">
                <a href="{{ route('admin.settings.translations') }}" class="nav-link {{ isset($translationManagerActive) ? $translationManagerActive : '' }}"><i class="fa fa-language"></i> @lang('module_settings.translationManager')</a>
            </li>
            <li class="nav-item">
                <a href="{{ route('admin.settings.update-app.index') }}" class="nav-link {{ isset($updateAppSettingsActive) ? $updateAppSettingsActive : '' }}"><i class="fa fa-gift"></i> @lang('module_settings.updateApp')</a>
            </li>
        </ul>
    </div>
</div>