<!-- General CSS Files -->
<link href="{{ asset('assets/modules/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
<link href="{{ asset('assets/modules/fontawesome/css/all.min.css') }}" rel="stylesheet">

<!-- CSS Libraries -->
<link href="{{ asset('assets/modules/bootstrap-social/bootstrap-social.css') }}" rel="stylesheet">
<link href="{{ asset('assets/modules/izitoast/css/iziToast.min.css') }}" rel="stylesheet">

@yield('styles')

<!-- Template CSS -->
<link href="{{ asset('assets/css/style.css') }}" rel="stylesheet">
<link href="{{ asset('assets/css/components.css') }}" rel="stylesheet">
<link href="{{ asset('assets/js/ajax-helper/admin/helper.css') }}" rel="stylesheet">
<link href="{{ asset('assets/css/custom.css') }}" rel="stylesheet">

@if($bootstrapModalRight)
    <link href="{{ asset('assets/css/bootstrap-model-right.css') }}" rel="stylesheet">
@endif

@if($rtl)
<link href="{{ asset('assets/css/rtl.css') }}" rel="stylesheet">
<link href="{{ asset('assets/css/rtl-custom.css') }}" rel="stylesheet">
@endif
