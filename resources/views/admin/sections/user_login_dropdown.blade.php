@if(auth()->guard('admin')->check())
@if($settings->twilio_enabled)
<li>
    <a href="javascript:void(0);" onclick="showHideDialer();" class="nav-link nav-link-lg message-toggle">
        <i class="fas fa-mobile-alt"></i>
    </a>
</li>
@endif
<li class="dropdown">
    <a href="#" data-toggle="dropdown" class="nav-link dropdown-toggle nav-link-lg nav-link-user">
        <img alt="image" src="{{ $user->image_url }}" class="rounded-circle mr-1">
        <div class="d-sm-none d-lg-inline-block">{{ $user->first_name }}</div>
    </a>
    <div class="dropdown-menu dropdown-menu-right">
        <a href="{{ route('admin.settings.profile.index') }}" class="dropdown-item has-icon">
            <i class="far fa-user"></i> @lang('module_settings.profileSettings')
        </a>
        @if($user->hasRole('admin'))
        <a href="{{ route('admin.settings.company.index') }}" class="dropdown-item has-icon">
            <i class="fas fa-cog"></i> @lang('menu.settings')
        </a>
        @endif
        <div class="dropdown-divider"></div>
        <a href="{{ route('admin.logout') }}" class="dropdown-item has-icon text-danger">
            <i class="fas fa-sign-out-alt"></i> @lang('app.logout')
        </a>
    </div>
</li>
@else
    <li class="dropdown dropdown-list-toggle">
        <a data-toggle="modal" href="#loginRegisterModal" class="btn btn-icon icon-left btn-outline-light"><i class="far fa-user"></i> Login</a>
        <a href="#" class="btn btn-outline-light">Register</a>
    </li>
@endif