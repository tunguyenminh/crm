<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no" name="viewport">
    <title>Login &mdash; {{ $settings->name }}</title>

    <!-- General CSS Files -->
    <link href="{{ asset('assets/modules/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/modules/fontawesome/css/all.min.css') }}" rel="stylesheet">

    <!-- CSS Libraries -->
    <link href="{{ asset('assets/modules/bootstrap-social/bootstrap-social.css') }}" rel="stylesheet">

    <!-- Template CSS -->
    <link href="{{ asset('assets/css/style.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/components.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/custom.css') }}" rel="stylesheet">
</head>

<body>
<div id="app">
    <section class="section">
        <div class="container mt-5">
            <div class="row">
                <div class="col-12 col-sm-8 offset-sm-2 col-md-6 offset-md-3 col-lg-6 offset-lg-3 col-xl-4 offset-xl-4">
                    <div class="login-brand" style="margin-bottom: 25px;">
                        <img src="{{ $settings->logo_url }}" style="max-height: 45px;" alt="{{ $settings->name }}">
                    </div>

                    <div class="card card-primary">
                        <div class="card-header"><h4>@lang('app.login')</h4></div>

                        <div class="card-body">
                            {!! Form::open(['url' => '', 'method' => 'post','id'=>'loginform']) !!}
                                <div class="form-group">
                                    <label for="email">@lang('app.email')</label>
                                    <input id="email" type="email" class="form-control" name="email" tabindex="1" required autofocus>
                                </div>

                                <div class="form-group">
                                    <div class="d-block">
                                        <label for="password" class="control-label">@lang('app.password')</label>
                                    </div>
                                    <input id="password" type="password" class="form-control" name="password" tabindex="2" required>
                                </div>

                                <div class="form-group">
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" name="remember" class="custom-control-input" tabindex="3" id="remember-me">
                                        <label class="custom-control-label" for="remember-me">@lang('app.remember_me')</label>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <button onclick="login();return false;" type="submit" class="btn btn-primary btn-lg btn-block" tabindex="4">
                                        Login
                                    </button>
                                </div>
                            {!! Form::close() !!}
                        </div>
                    </div>

                    <div class="simple-footer">
                        Copyright &copy; {{ $settings->short_name }} {{ $year }}
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<!-- JS Scripts -->
@include('common.sections.scripts')


<script>

    function login() {
        var url = "{{ route('admin.login_check') }}";

        $.easyAjax({
            url: url,
            type: "POST",
            data: $("#loginform").serialize(),
            container: "#loginform",
            messagePosition: "inline"
        });
    }

</script>

</body>
</html>