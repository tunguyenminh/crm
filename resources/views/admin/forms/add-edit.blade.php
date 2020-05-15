@extends('admin.admin_layouts')

@section('styles')
    <link href="{{ asset('assets/modules/bootstrap-fileinput/bootstrap-fileinput.css') }}" rel="stylesheet">
@endsection

@section('breadcrumb')

    <div class="section-header">
        <div class="section-header-back">
            <a href="{{ route('admin.forms.index') }}" class="btn btn-icon"><i class="fas fa-arrow-left"></i></a>
        </div>
        <h1> {{ $pageTitle }}</h1>
        <div class="section-header-breadcrumb">
            <div class="breadcrumb-item active"><a href="{{ route('admin.dashboard.index') }}">@lang('menu.home')</a></div>
            <div class="breadcrumb-item active"><a href="{{ route('admin.forms.index') }}">@lang('menu.forms')</a></div>
            <div class="breadcrumb-item">@if($form->id != '') @lang('app.edit') @else @lang('app.create') @endif</div>
        </div>
    </div>
@endsection

@section('content')

    <input type="hidden" id="form_id" value="{{ $form->id ?? '' }}">

    <h2 class="section-title">@lang('app.create')</h2>
    <p class="section-lead">@lang('module_form.createEditFormMessage')</p>

    <div id="add-edit-form">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <label class="col-form-label text-md-right col-12 col-md-2 col-lg-2">@lang('module_form.formName')</label>
                        <div @if(!$user->ability('admin', 'form_edit')) class="col-sm-12 col-md-8" @else class="col-sm-12 col-md-4" @endif>
                            <input type="text" class="form-control" id="form_name" name="form_name" value="{{ isset($form->form_name) ?  $form->form_name : '' }}">
                        </div>

                        @if($user->ability('admin', 'form_edit'))
                            <div class="col-sm-12 col-md-1 form-inline">
                                <strong class="orText">@lang('app.or')</strong>
                            </div>
                            <div class="col-sm-12 col-md-4">
                                <select class="form-control" id="selected_form_id" name="selected_form_id" onchange="formSelected()">
                                    <option value="">@lang('module_form.selectFormForEdit')</option>
                                    @foreach($allFormLists as $allFormList)
                                        <option value="{{ $allFormList->id }}" @if(isset($form->id) && $form->id == $allFormList->id) selected @endif>{{ $allFormList->form_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="alert alert-info">
                <p><i class="fas fa-th"></i> @lang('module_form.orderFieldMessage')</p>
                <p><i class="fas fa-trash"></i> @lang('module_form.removeFieldMessage')</p>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-6">
            <div class="card">
                <div class="card-header">
                    <h4>@lang('module_form.defaultFields')</h4>
                </div>
                <div class="card-body bg-whitesmoke card-height">
                    <div class="row" id="defaultFormFieldsDiv">
                        @include('admin.forms.default-fields')
                    </div>
                </div>
                <div class="card-footer">
                    <p class="text-warning"><strong>@lang('app.notes') : </strong> @lang('module_form.addDefaultFieldMessage')</p>
                </div>
            </div>
        </div>
        <div class="col-6">
            <div class="card">
                <div class="card-header">
                    <h4>@lang('module_form.formFields')</h4>

                    <div class="card-header-action">
                        {!!  Form::open(['url' => '','autocomplete'=>'off','id'=>'upload-csv-form']) 	 !!}
                        <div class="fileinput fileinput-new" data-provides="fileinput">
                            <div>
                              <span class="btn btn-info btn-file">
                                  <span class="fileinput-new">
                                  @lang('module_form.importFromCSV') </span>
                                  <span class="fileinput-exists">
                                  @lang('module_form.importFromCSV') </span>
                                  <input type="file" name="import_from_csv" id="import_from_csv" onchange="csvFileAdded()">
                              </span>
                            </div>
                            </div>
                        {{Form::close()}}
                    </div>
                </div>
                <div class="card-body bg-whitesmoke card-height">
                    <div class="row">
                        <div class="col-md-8">
                            <ul id="sortable-section" class="list-unstyled list-unstyled-border list-unstyled-noborder">
                                @include('admin.forms.form-fields')
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <button class="btn btn-primary" onclick="addNewFieldForm()"><i class="fa fa-plus"></i> @lang('module_form.addNewField')</button>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-footer text-center">
                    <button class="btn btn-success" onclick="addOrEdit()"><i class="fa fa-check"></i> @lang('module_form.saveForm')</button>
                </div>
            </div>
        </div>
    </div>

    </div>
@endsection

@section('modals')
    @include('admin.includes.add-edit-modal')
@endsection

@section('scripts')
    <script src="{{ asset('assets/modules/jquery-ui/jquery-ui.min.js') }}"></script>
    <script src="{{ asset('assets/modules/bootstrap-fileinput/bootstrap-fileinput.js') }}"></script>

    <script>
        initSectionSorting();

        function initSectionSorting() {
            $('#sortable-section').sortable({
                handle: '.sort-handler',
                opacity: .8,
                tolerance: 'pointer'
            });
        }

        function addOrEdit() {

            var id = $('#form_id').val();
            var token = "{{ csrf_token() }}";
            var formName = $('#form_name').val();
            var fields = [];

            if(typeof id != 'undefined' && id != ""){
                var url  ="{{route('admin.forms.update',':id')}}";
                url      = url.replace(':id',id);
                var method = 'PUT';
            } else {
                var url = "{{ route('admin.forms.store') }}";
                var method = 'POST';
            }

            $('#sortable-section .media-body').each( function(e) {
                fields.push( $(this).attr('data-value'));
            });

            $.easyAjax({
                type: 'POST',
                url: url,
                container: "#add-edit-form",
                data: {'_token': token, form_name: formName, fields: fields, '_method': method},
                messagePosition: "toastr"
            });
        }

        function formFieldSelected(selectedFormField)
        {
            var value = $(selectedFormField).val();
            var id = $(selectedFormField).attr('id');
            var fieldValue = $(selectedFormField).val();
            var classAttr = $(selectedFormField).attr('data-field-value');

            if($(selectedFormField).is(':checked'))
            {
                var html = '<li class="media sortable_'+classAttr+'"><div class="sort-handler mr-3"><i class="fas fa-th"></i></div>'+
                    '<div class="media-body" data-value="'+fieldValue+'">'+
                    '<div class="media-right">'+
                    '<a href="javascript:void(0);" onclick="removeThisField(this)" class="btn btn-icon btn-sm btn-danger"><i class="fas fa-trash"></i></a>'+
                    '</div>'+
                    '<div class="media-title mb-1">'+fieldValue+'</div>'+
                    '</div>'+
                    '</li>';

                $('#sortable-section').append(html);

            }else{
                $('.sortable_'+classAttr).remove();
            }

            $('#sortable-section').sortable('destroy');
            initSectionSorting();
        }

        function removeThisField(field)
        {
            $(field).closest('li').remove();
            $('#sortable-section').sortable('destroy');
            initSectionSorting();
        }

        @if($user->ability('admin', 'form_edit'))
        function formSelected() {
            var formValue = $('#selected_form_id').val();
            var token = "{{ csrf_token() }}";
            var url = '{{ route('admin.forms.select-form-data') }}';

            if(formValue == '')
            {
                $('#form_name').val('');
                $('#sortable-section').html('');
                $('#sortable-section').sortable('destroy');

                // uncheck all default fields
                $('.selectField').prop('checked', false);

                initSectionSorting();
            } else {
                $.easyAjax({
                    type: 'POST',
                    url: url,
                    container: "#add-edit-form",
                    data: {'_token': token, selected_form_id: formValue},
                    messagePosition: "toastr",
                    success: function (response) {
                        if (response.status == "success") {
                            $('#defaultFormFieldsDiv').html(response.html1);
                            $('#sortable-section').html(response.html);
                            $('#form_name').val(response.form.form_name);
                            $('#sortable-section').sortable('destroy');
                            initSectionSorting();
                        }
                    }
                });
            }
        }
        @endif

        function addNewFieldForm() {
            $.ajaxModal('#addEditModal', '{{ route('admin.forms.add-new-field') }}');
        }

        function addNewField() {
            var newFieldValue = $('#new_field_name').val();
            $.showErrors({});

            if(newFieldValue == '')
            {
                var errors = {
                    new_field_name : ['{{ __('module_form.fieldNameRequired')}}']
                };

                $.showErrors(errors);
            } else {
                var fieldAlreadyExists = false;
                $('#sortable-section .media-body').each( function(e) {
                    if($(this).attr('data-value') == newFieldValue){
                        fieldAlreadyExists = true;
                    }
                });

                if(fieldAlreadyExists)
                {
                    var errors = {
                        new_field_name : ['{{ __('module_form.fieldAlreadyExists')}}']
                    };

                    $.showErrors(errors);
                } else {
                    var classAttr = newFieldValue;

                    $('.selectField').each( function(e) {
                        if($(this).val() == newFieldValue){
                            classAttr = $(this).attr('data-field-value');
                            $(this).prop('checked', true);
                        }
                    });

                    var html = '<li class="media sortable_'+classAttr+'"><div class="sort-handler mr-3"><i class="fas fa-th"></i></div>'+
                        '<div class="media-body" data-value="'+newFieldValue+'">'+
                        '<div class="media-right">'+
                        '<a href="javascript:void(0);" onclick="removeThisField(this)" class="btn btn-icon btn-sm btn-danger"><i class="fas fa-trash"></i></a>'+
                        '</div>'+
                        '<div class="media-title mb-1">'+newFieldValue+'</div>'+
                        '</div>'+
                        '</li>';

                    $('#sortable-section').append(html);
                    $('#sortable-section').sortable('destroy');
                    initSectionSorting();

                    $('#addEditModal').modal('hide');
                }
            }
        }

        function csvFileAdded() {
            var url = "{{ route('admin.forms.upload-fields-from-csv') }}";

            $.easyAjax({
                type: 'POST',
                url: url,
                file: true,
                container: "#upload-csv-form",
                messagePosition: "toastr",
                success: function(response) {
                    if (response.status == "success") {
                        $('#defaultFormFieldsDiv').html(response.html1);
                        $('#sortable-section').html(response.html);
                        $('#sortable-section').sortable('destroy');
                        initSectionSorting();
                    }
                }
            });
        }
    </script>
@endsection