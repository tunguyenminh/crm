<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no" name="viewport">
    <title>{{ $pageTitle . ' - ' . $settings->short_name }}</title>

    @include('common.sections.styles')
</head>

<body @if($siteLayout == 'top') class="layout-3" @endif>
<div id="app">
    <div class="main-wrapper @if($siteLayout == 'top') container @else main-wrapper-1 @endif ">


        @include('admin.sections.navbar_admin')

        @include('admin.sections.left_sidebar')

        <!-- Main Content -->
        <div class="main-content">
            <section class="section">

                @yield('breadcrumb')

                <div class="section-body">
                    @yield('content')

                    @if($settings->twilio_enabled)
                        @include('admin.includes.twilio-dialer')
                    @endif
                </div>
            </section>
        </div>

        @yield('modals')

        @if($showFooter)
            @include('common.sections.footer')
        @endif
    </div>
</div>

<!-- JS Scripts -->
@include('common.sections.scripts')

@if($settings->twilio_enabled)
<script>
    function showHideDialer() {
        $('#liveCallWidget').toggle('slow');
    }
</script>
@endif

</body>
</html>