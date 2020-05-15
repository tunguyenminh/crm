@extends('vendor.installer.layouts.master')

@section('title', trans('installer_messages.final.title'))
@section('container')
    <p class="paragraph" style="text-align: center;">{{ session('message')['message'] }}</p>
    <div class="buttons">
        <h4>Login Details:</h4>
        <p style="margin-bottom: 5px;">Email: admin@example.com</p>
        <p>Password: 123456</p>
        <a href="{{ url('/') }}" class="button">{{ trans('installer_messages.final.exit') }}</a>
    </div>
@stop
