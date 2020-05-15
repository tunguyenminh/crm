<ul class="@if($siteLayout == 'sidebar') sidebar-menu @elseif($siteLayout == 'top') navbar-nav @endif">

    {{--Dashobard--}}
    @if($siteLayout == 'sidebar')
        <li class="menu-header">@lang('menu.dashboard')</li>
    @endif
    <li class="@if($siteLayout == 'top') nav-item @endif {{ isset($dashboardActive) ? $dashboardActive : '' }}">
        <a class="nav-link" href="{{ route('admin.dashboard.index') }}"><i class="fas fa-home"></i> <span>@lang('menu.dashboard')</span></a>
    </li>

    {{--Leads--}}
    @if($siteLayout == 'sidebar')
        <li class="menu-header">@lang('menu.leadManagement')</li>
        <li class="{{ isset($callManagerActive) ? $callManagerActive : '' }}">
            <a class="nav-link" href="{{ route('admin.callmanager.index') }}"><i class="fas fa-th-large"></i> <span>@lang('menu.callManager')</span></a>
        </li>
        @if($user->ability('admin', 'campaign_view,campaign_view_all'))
        <li class="{{ isset($campaignActive) ? $campaignActive : '' }}">
            <a class="nav-link" href="{{ route('admin.campaigns.index') }}"><i class="fas fa-business-time"></i> <span>@lang('menu.campaigns')</span></a>
        </li>
        @endif
        <li class="{{ isset($callEnquiryActive) ? $callEnquiryActive : '' }}">
            <a class="nav-link" href="{{ route('admin.call-enquiry.index') }}"><i class="fas fa-phone-volume"></i> <span>@lang('menu.callEnquiry')</span></a>
        </li>
        <li class="{{ isset($callHistoryActive) ? $callHistoryActive : '' }}">
            <a class="nav-link" href="{{ route('admin.call-history.index') }}"><i class="fas fa-stopwatch"></i> <span>@lang('menu.callHistory')</span></a>
        </li>
        @if($user->ability('admin', 'import_lead'))
        <li class="{{ isset($importLeadActive) ? $importLeadActive : '' }}">
            <a class="nav-link" href="{{ route('admin.campaigns.import-leads') }}"><i class="fas fa-upload"></i> <span>@lang('menu.importLeads')</span></a>
        </li>
        @endif
        @if($user->ability('admin', 'export_lead'))
        <li class="{{ isset($exportLeadActive) ? $exportLeadActive : '' }}">
            <a class="nav-link" href="{{ route('admin.campaigns.export-leads') }}"><i class="fas fa-anchor"></i> <span>@lang('menu.exportLeads')</span></a>
        </li>
        @endif
    @else
        <li class="nav-item dropdown {{ isset($leadManagementMenuActive) ? $leadManagementMenuActive : '' }}">
            <a href="#" class="nav-link has-dropdown" data-toggle="dropdown"><i class="fas fa-mail-bulk"></i> <span>@lang('menu.leadManagement')</span></a>
            <ul class="dropdown-menu">
                <li class="@if($siteLayout == 'top') nav-item @endif {{ isset($callManagerActive) ? $callManagerActive : '' }}">
                    <a class="nav-link" href="{{ route('admin.callmanager.index') }}"><i class="fas fa-th-large"></i> <span>@lang('menu.callManager')</span></a>
                </li>
                @if($user->ability('admin', 'campaign_view,campaign_view_all'))
                <li class="@if($siteLayout == 'top') nav-item @endif {{ isset($campaignActive) ? $campaignActive : '' }}">
                    <a class="nav-link" href="{{ route('admin.campaigns.index') }}"><i class="fas fa-business-time"></i> <span>@lang('menu.campaigns')</span></a>
                </li>
                @endif
                <li class="@if($siteLayout == 'top') nav-item @endif {{ isset($callEnquiryActive) ? $callEnquiryActive : '' }}">
                    <a class="nav-link" href="{{ route('admin.call-enquiry.index') }}"><i class="fas fa-phone-volume"></i> <span>@lang('menu.callEnquiry')</span></a>
                </li>
                <li class="@if($siteLayout == 'top') nav-item @endif {{ isset($callHistoryActive) ? $callHistoryActive : '' }}">
                    <a class="nav-link" href="{{ route('admin.call-history.index') }}"><i class="fas fa-stopwatch"></i> <span>@lang('menu.callHistory')</span></a>
                </li>
                @if($user->ability('admin', 'import_lead'))
                <li class="@if($siteLayout == 'top') nav-item @endif {{ isset($importLeadActive) ? $importLeadActive : '' }}">
                    <a class="nav-link" href="{{ route('admin.campaigns.import-leads') }}"><i class="fas fa-upload"></i> <span>@lang('menu.importLeads')</span></a>
                </li>
                @endif
                @if($user->ability('admin', 'export_lead'))
                <li class="@if($siteLayout == 'top') nav-item @endif {{ isset($exportLeadActive) ? $exportLeadActive : '' }}">
                    <a class="nav-link" href="{{ route('admin.campaigns.export-leads') }}"><i class="fas fa-anchor"></i> <span>@lang('menu.exportLeads')</span></a>
                </li>
                @endif
            </ul>
        </li>
    @endif

    {{--Appointments--}}
    @if($siteLayout == 'sidebar')
        <li class="menu-header">@lang('menu.appointments')</li>
        <li class="{{ isset($pendingCallbackActive) ? $pendingCallbackActive : '' }}">
            <a class="nav-link" href="{{ route('admin.pending-callback.index') }}"><i class="fas fa-headphones"></i> <span>@lang('menu.pendingCallbacks')</span> @if($userPendingCallbacks>0)<i class="badge badge-danger sidebar-badge">{{ $userPendingCallbacks }}</i>@endif</a>
        </li>
        <li class="{{ isset($appointmentCalendarActive) ? $appointmentCalendarActive : '' }}">
            <a class="nav-link" href="{{ route('admin.appointments.index') }}"><i class="fas fa-calendar-alt"></i> <span>@lang('menu.appointmentCalendar')</span></a>
        </li>
    @else
        <li class="nav-item dropdown {{ isset($appointmentMenuActive) ? $appointmentMenuActive : '' }}">
            <a href="#" class="nav-link has-dropdown" data-toggle="dropdown"><i class="fas fa-calendar-alt"></i> <span>@lang('menu.appointments')</span></a>
            <ul class="dropdown-menu">
                <li class="@if($siteLayout == 'top') nav-item @endif {{ isset($pendingCallbackActive) ? $pendingCallbackActive : '' }}">
                    <a class="nav-link" href="{{ route('admin.pending-callback.index') }}"><i class="fas fa-headphones"></i> <span>@lang('menu.pendingCallbacks')</span></a>
                </li>
                <li class="@if($siteLayout == 'top') nav-item @endif {{ isset($appointmentCalendarActive) ? $appointmentCalendarActive : '' }}">
                    <a class="nav-link" href="{{ route('admin.appointments.index') }}"><i class="fas fa-calendar-alt"></i> <span>@lang('menu.appointmentCalendar')</span></a>
                </li>
            </ul>
        </li>
    @endif

    {{--Users--}}
    @if($siteLayout == 'sidebar')
        <li class="menu-header">@lang('menu.userManagement')</li>
        <li class="{{ isset($staffMemberActive) ? $staffMemberActive : '' }}">
            <a class="nav-link" href="{{ route('admin.users.index') }}"><i class="fas fa-user-secret"></i> <span>@lang('menu.staffMembers')</span></a>
        </li>
        <li class="{{ isset($salesMemberActive) ? $salesMemberActive : '' }}">
            <a class="nav-link" href="{{ route('admin.sales-users.index') }}"><i class="fas fa-user-tie"></i> <span>@lang('menu.salesMembers')</span></a>
        </li>
    @else
        <li class="nav-item dropdown {{ isset($userManagementMenuActive) ? $userManagementMenuActive : '' }}">
            <a href="#" class="nav-link has-dropdown" data-toggle="dropdown"><i class="fas fa-user"></i> <span>@lang('menu.userManagement')</span></a>
            <ul class="dropdown-menu">
                <li class="@if($siteLayout == 'top') nav-item @endif {{ isset($staffMemberActive) ? $staffMemberActive : '' }}">
                    <a class="nav-link" href="{{ route('admin.users.index') }}"><i class="fas fa-user-secret"></i> <span>@lang('menu.staffMembers')</span></a>
                </li>
                <li class="@if($siteLayout == 'top') nav-item @endif {{ isset($salesMemberActive) ? $salesMemberActive : '' }}">
                    <a class="nav-link" href="{{ route('admin.sales-users.index') }}"><i class="fas fa-user-tie"></i> <span>@lang('menu.salesMembers')</span></a>
                </li>
            </ul>
        </li>
    @endif


    {{--Settings--}}
    @if($siteLayout == 'sidebar')
        <li class="menu-header">@lang('menu.settings')</li>

        @if($user->ability('admin', 'email_template_view,email_template_view_all'))
        <li class="{{ isset($emailTemplateActive) ? $emailTemplateActive : '' }}">
            <a class="nav-link" href="{{ route('admin.email-templates.index') }}"><i class="fas fa-envelope-open-text"></i> <span>@lang('menu.emailTemplates')</span></a>
        </li>
        @endif

        @if($user->ability('admin', 'form_view,form_view_all'))
        <li class="{{ isset($formActive) ? $formActive : '' }}">
            <a class="nav-link" href="{{ route('admin.forms.index') }}"><i class="fas fa-address-card"></i> <span>@lang('menu.formBuilder')</span></a>
        </li>
        @endif

        @if($user->hasRole('admin'))
            <li class="{{ isset($settingMenuActive) ? $settingMenuActive : '' }}">
                <a class="nav-link" href="{{ route('admin.settings.company.index') }}"><i class="fas fa-cog"></i> <span>@lang('menu.settings')</span></a>
            </li>
        @else
            <li class="{{ isset($settingMenuActive) ? $settingMenuActive : '' }}">
                <a class="nav-link" href="{{ route('admin.settings.profile.index') }}"><i class="fas fa-user-cog"></i> <span>@lang('module_settings.profileSettings')</span></a>
            </li>
        @endif
    @else
        <li class="nav-item dropdown {{ isset($settingsMenuActive) ? $settingsMenuActive : '' }}">
            <a href="#" class="nav-link has-dropdown" data-toggle="dropdown"><i class="fas fa-cog"></i> <span>@lang('menu.settings')</span></a>
            <ul class="dropdown-menu">
                @if($user->ability('admin', 'email_template_view,email_template_view_all'))
                <li class="@if($siteLayout == 'top') nav-item @endif {{ isset($emailTemplateActive) ? $emailTemplateActive : '' }}">
                    <a class="nav-link" href="{{ route('admin.email-templates.index') }}"><i class="fas fa-envelope-open-text"></i> <span>@lang('menu.emailTemplates')</span></a>
                </li>
                @endif

                @if($user->ability('admin', 'form_view,form_view_all'))
                <li class="@if($siteLayout == 'top') nav-item @endif {{ isset($formActive) ? $formActive : '' }}">
                    <a class="nav-link" href="{{ route('admin.forms.index') }}"><i class="fas fa-clipboard"></i> <span>@lang('menu.formBuilder')</span></a>
                </li>
                @endif

                @if($user->hasRole('admin'))
                    <li class="@if($siteLayout == 'top') nav-item @endif {{ isset($settingMenuActive) ? $settingMenuActive : '' }}">
                        <a class="nav-link" href="{{ route('admin.settings.company.index') }}"><i class="fas fa-cog"></i> <span>@lang('menu.settings')</span></a>
                    </li>
                @else
                    <li class="@if($siteLayout == 'top') nav-item @endif {{ isset($settingMenuActive) ? $settingMenuActive : '' }}">
                        <a class="nav-link" href="{{ route('admin.settings.profile.index') }}"><i class="fas fa-user-cog"></i> <span>@lang('module_settings.profileSettings')</span></a>
                    </li>
                @endif
            </ul>
        </li>
    @endif
</ul>