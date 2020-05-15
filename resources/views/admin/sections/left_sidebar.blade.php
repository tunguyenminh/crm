@if($siteLayout == 'sidebar')
    <div class="main-sidebar sidebar-style-2">
    <aside id="sidebar-wrapper">
        <div class="sidebar-brand">
            <img src="{{ $settings->logo_url }}" style="max-height: 45px" alt="{{ config('app.name') }}">
{{--            <a href="{{ route('admin.dashboard.index') }}">{{ $settings->short_name }}</a>--}}
        </div>
        <div class="sidebar-brand sidebar-brand-sm">
            <a href="{{ route('admin.dashboard.index') }}">{{ str_limit($settings->short_name, 2) }}</a>
        </div>

        @include('admin.sections.left_sidebar_menu')

       </aside>
</div>
@elseif($siteLayout == 'top')
    <nav class="navbar navbar-secondary navbar-expand-lg">
        <div class="container">
            @include('admin.sections.left_sidebar_menu')
        </div>
    </nav>
@endif