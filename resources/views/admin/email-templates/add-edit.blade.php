@extends('admin.admin_layouts')

@section('styles')
    <link href="{{ asset('assets/modules/summernote/summernote-bs4.css') }}" rel="stylesheet">
@endsection

@section('breadcrumb')

    <div class="section-header">
        <div class="section-header-back">
            <a href="{{ route('admin.email-templates.index') }}" class="btn btn-icon"><i class="fas fa-arrow-left"></i></a>
        </div>
        <h1> {{ $pageTitle }}</h1>
        <div class="section-header-breadcrumb">
            <div class="breadcrumb-item active"><a href="{{ route('admin.dashboard.index') }}">@lang('menu.home')</a></div>
            <div class="breadcrumb-item active"><a href="{{ route('admin.email-templates.index') }}">@lang('menu.emailTemplates')</a></div>
            <div class="breadcrumb-item">@if($emailTemplate->id != '') @lang('app.edit') @else @lang('app.create') @endif</div>
        </div>
    </div>
@endsection

@section('content')

    <h2 class="section-title">@if($emailTemplate->id != '') @lang('app.edit') @else @lang('app.create') @endif</h2>
    <p class="section-lead">@lang('module_email_template.createEmailTemplateMessage')</p>

    {!!  Form::open(['url' => '','autocomplete'=>'off','id'=>'add-edit-form']) 	 !!}
    @if(isset($emailTemplate->id)) <input type="hidden" name="_method" value="PUT"> @endif
    <div class="row">
        <div class="col-12">
            <div class="card">
                {{--<div class="card-header">--}}
                    {{--<h4>@lang('module_email_template.createEmailTemplate')</h4>--}}
                {{--</div>--}}
                <div class="card-body">
                    <div class="form-group row mb-4">
                        <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">@lang('module_email_template.templateName')</label>
                        <div class="col-sm-12 col-md-5">
                            <input type="text" class="form-control" id="template_name" name="template_name" value="{{ isset($emailTemplate->name) ?  $emailTemplate->name : '' }}">
                        </div>
                        <div class="col-sm-12 col-md-3">
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" id="shareable" name="shareable" @if(isset($emailTemplate->shareable) && $emailTemplate->shareable == 1) checked @endif>
                                <label class="custom-control-label" for="shareable">@lang('module_email_template.shareTemplateWithTeam')</label>
                            </div>
                        </div>
                    </div>
                    <div class="form-group row mb-4">
                        <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">@lang('module_email_template.templateSubject')</label>
                        <div class="col-sm-12 col-md-5">
                            <input type="text" class="form-control" id="template_subject" name="template_subject" value="{{ isset($emailTemplate->subject) ?  $emailTemplate->subject : '' }}">
                        </div>
                    </div>
                    <div class="form-group row mb-4">
                        <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">@lang('module_email_template.template')</label>
                        <div class="col-sm-12 col-md-8">
                            <textarea  class="form-control" id="template_content" name="template_content">{{ isset($emailTemplate->content) ?  $emailTemplate->content : '' }}</textarea>
                        </div>
                    </div>
                    <div class="form-group row mb-4">
                        <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">@lang('module_email_template.formFields')</label>
                        <div class="col-sm-12 col-md-8">
                            <div class="row">
                                @foreach($formFields as $formField)
                                    <div class="col-3 col-md-3 col-lg-3 mb-3">
                                        <a href="javascript:void(0);" onclick="addTextToEditor('{{ $formField->field_name }}')">{{ $formField->field_name }}</a>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    <div class="form-group row mb-4">
                        <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3"></label>
                        <div class="col-sm-12 col-md-7">
                            <button id="saveFormButton" type="submit" class="btn btn-icon icon-left btn-success" onclick="addOrEdit({{ isset($emailTemplate->id) ? $emailTemplate->id : '' }});return false"><i class="fas fa-check"></i> @if($emailTemplate->id != '') @lang('app.update') @else @lang('app.save') @endif</button>
                            <a href="{{ route('admin.email-templates.index') }}" class="btn btn-secondary"> @lang('app.cancel')</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    {{Form::close()}}

@endsection

@section('modals')
    @include('admin.includes.add-edit-modal')
@endsection

@section('scripts')
    <script src="{{ asset('assets/modules/summernote/summernote-bs4.js') }}"></script>

    <script>

        $(function () {
            $("#template_content").summernote({
                dialogsInBody: true,
                minHeight: 250,
            });
        });

        function addOrEdit(id) {

            if(typeof id != 'undefined'){
                var url  ="{{route('admin.email-templates.update',':id')}}";
                url      = url.replace(':id',id);
            }


            if (typeof id == 'undefined'){
                url = "{{ route('admin.email-templates.store') }}";
            }

            $.easyAjax({
                type: 'POST',
                url: url,
                file: true,
                container: "#add-edit-form",
                messagePosition: "toastr"
            });
        }

        function addTextToEditor(text) {
            $('#template_content').summernote('insertText', '##'+text+'##');
        }

    </script>
@endsection