<div class="navbar-bg"></div>
<nav class="navbar navbar-expand-lg main-navbar">
    @if($siteLayout == 'top')
        <a href="{{ route('admin.dashboard.index') }}" class="navbar-brand sidebar-gone-hide">{{ $settings->short_name }}</a>
        <a href="#" class="nav-link sidebar-gone-show" data-toggle="sidebar"><i class="fas fa-bars"></i></a>
    @endif
    <form class="form-inline @if($siteLayout == 'top' || $rtl) ml-auto @else mr-auto @endif">
        <ul class="navbar-nav @if($siteLayout == 'sidebar' || $rtl) mr-3 @endif">
            @if($siteLayout == 'sidebar')
            <li><a href="#" data-toggle="sidebar" class="nav-link nav-link-lg"><i class="fas fa-bars"></i></a></li>
            @endif
        </ul>
    </form>
    <ul class="navbar-nav navbar-right">
        @include('admin.sections.user_login_dropdown')
    </ul>
</nav>