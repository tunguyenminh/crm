@extends('admin.admin_layouts')

@section('styles')
    <link href="{{ asset('assets/modules/summernote/summernote-bs4.css') }}" rel="stylesheet">
@endsection

@section('breadcrumb')

    <div class="section-header">
        <div class="section-header-back">
            <a href="{{ route('admin.campaigns.index') }}" class="btn btn-icon"><i class="fas fa-arrow-left"></i></a>
        </div>
        <h1> {{ $pageTitle }}</h1>
        <div class="section-header-breadcrumb">
            <div class="breadcrumb-item active"><a href="{{ route('admin.dashboard.index') }}">@lang('menu.home')</a></div>
            <div class="breadcrumb-item active"><a href="{{ route('admin.campaigns.index') }}">@lang('menu.campaigns')</a></div>
            <div class="breadcrumb-item">@if($campaign->id != '') @lang('app.edit') @else @lang('app.create') @endif</div>
        </div>
    </div>
@endsection

@section('content')

    {!!  Form::open(['url' => '','autocomplete'=>'off','id'=>'add-edit-form']) 	 !!}
    @if(isset($campaign->id)) <input type="hidden" name="_method" value="PUT"> @endif
    <input type="hidden" id="lead_form_fields" name="lead_form_fields" value="" />
    <input type="hidden" name="step" id="step" value="1">
    <div class="row continer_step_div container_step_1">
        <div class="col-12">
            <div class="card">
                <div class="card-header card-header-description">
                    <h4>@lang('module_campaign.step1Name')</h4>
                    <p>@lang('module_campaign.step1Description')</p>
                </div>
                <div class="card-body">
                    <div class="form-group row mb-4">
                        <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">@lang('module_campaign.name')</label>
                        <div class="col-sm-12 col-md-8">
                            <input type="text" class="form-control" id="campaign_name" name="campaign_name" value="{{ isset($campaign->name) ?  $campaign->name : '' }}">
                        </div>
                    </div>
                    <div class="form-group row mb-4">
                        <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">@lang('module_campaign.campaignMembers')</label>
                        <div class="col-sm-12 col-md-8">
                            <div class="row">
                                @foreach($campaignMembers as $campaignMember)
                                    <div class="col-3 col-md-3 col-lg-3 mb-3">
                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox" class="custom-control-input" id="user_{{ $campaignMember->id }}" name="campaign_members[]" value="{{ $campaignMember->id }}">
                                            <label class="custom-control-label" for="user_{{ $campaignMember->id }}">{{ $campaignMember->name }}</label>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            <input type="hidden" class="form-control" id="campaign_member" name="campaign_member" value="">
                        </div>
                    </div>
                    <div class="form-group row mb-4">
                        <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">@lang('module_campaign.formLayout')</label>
                        <div class="col-sm-12 col-md-8">
                            <select class="form-control" name="form" id="form">
                                @foreach($formLists as $formList)
                                    <option value="{{ $formList->id }}">{{ $formList->form_name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-group row mb-4">
                        <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">@lang('module_campaign.autoReferenceEnable')</label>
                        <div class="col-sm-12 col-md-8">
                            <label class="custom-switch mt-2">
                                <input type="checkbox" id="auto_reference" name="auto_reference" onclick="autoReferenceClicked()" class="custom-switch-input" value="1">
                                <span class="custom-switch-indicator"></span>
                            </label>
                        </div>
                    </div>
                    <div class="form-group row mb-4">
                        <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">@lang('module_campaign.autoReferencePrefix')</label>
                        <div class="col-sm-12 col-md-8">
                            <input type="text" class="form-control" id="auto_reference_prefix" name="auto_reference_prefix" value="" disabled>
                        </div>
                    </div>

                </div>
                <div class="card-footer">
                    <div class="form-group row mb-4">
                        <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3"></label>
                        <div class="col-sm-12 col-md-7">
                            <a href="{{ route('admin.campaigns.index') }}" class="btn btn-secondary"> @lang('app.cancel')</a>
                            <button type="button" class="btn btn-icon icon-right btn-primary" onclick="nextStep(2);return false">@lang('app.next') <i class="fas fa-arrow-right"></i></button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row continer_step_div container_step_2" style="display:none;">
        <div class="col-12">
            <div class="card">
                <div class="card-header card-header-description">
                    <h4>@lang('module_campaign.step2Name')</h4>
                    <p>@lang('module_campaign.step2Description')</p>
                </div>
                <div class="card-body">
                    <div class="form-group row mb-4">
                        <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">@lang('module_campaign.selectImportType')</label>
                        <div class="col-sm-12 col-md-5">
                            <div class="selectgroup w-100">
                                <label class="selectgroup-item">
                                    <input type="radio" name="import_type" onclick="importTypeClicked()" value="text" class="selectgroup-input" checked>
                                    <span class="selectgroup-button">@lang('module_campaign.byText')</span>
                                </label>
                                <label class="selectgroup-item">
                                    <input type="radio" name="import_type" onclick="importTypeClicked()" value="file" class="selectgroup-input">
                                    <span class="selectgroup-button">@lang('module_campaign.byCSVFile')</span>
                                </label>
                                <label class="selectgroup-item">
                                    <input type="radio" name="import_type" onclick="importTypeClicked()" value="without_import" class="selectgroup-input">
                                    <span class="selectgroup-button">@lang('module_campaign.withoutImport')</span>
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="form-group row mb-4" id="importTextDiv">
                        <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">@lang('module_campaign.copyPasteFromExcelTxt')</label>
                        <div class="col-sm-12 col-md-8">
                            <textarea class="form-control" id="import_text" name="import_text" style="height: auto;"></textarea>
                        </div>
                    </div>
                    <div class="form-group row mb-4" id="importFileDiv" style="display: none;">
                        <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">@lang('module_campaign.copyPasteFromExcelTxt')</label>
                        <div class="col-sm-12 col-md-8">
                            <input class="form-control" type="file" id="import_file" name="import_file">
                        </div>
                    </div>
                    <div class="form-group row mb-4" id="withoutImportDiv" style="display: none;">
                        <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3"></label>
                        <div class="col-sm-12 col-md-8">
                            <small class="form-text text-info">
                               @lang('module_campaign.withoutImportMessage')
                            </small>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <div class="form-group row mb-4">
                        <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3"></label>
                        <div class="col-sm-12 col-md-7">
                            <button type="button" class="btn btn-icon icon-left btn-primary" onclick="goToStep(1);return false"><i class="fas fa-arrow-left"></i> @lang('app.previous')</button>
                            <button type="button" class="btn btn-icon icon-right btn-primary" onclick="nextStep(2);return false">@lang('app.next') <i class="fas fa-arrow-right"></i></button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row continer_step_div container_step_3" style="display:none;">
        <div class="col-12">
            <div class="card">
                <div class="card-header card-header-description">
                    <h4>@lang('module_campaign.step3Name')</h4>
                    <p>@lang('module_campaign.importLeadsToCampaign')</p>
                </div>
                <div class="card-body" id="container_step_3_content">

                </div>
                <div class="card-footer">
                    <div class="form-group row mb-4">
                        <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3"></label>
                        <div class="col-sm-12 col-md-7">
                            <button type="button" class="btn btn-icon icon-left btn-primary" onclick="goToStep(2);return false"><i class="fas fa-arrow-left"></i> @lang('app.previous')</button>
                            <button type="button" class="btn btn-icon icon-right btn-primary" onclick="nextStep(3);return false">@lang('app.next') <i class="fas fa-arrow-right"></i></button>
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

        function nextStep(step)
        {
            if(step == 3)
            {
                var leadFormFields =  JSON.stringify(jsMatchedColumnArray);
                $('#lead_form_fields').val(leadFormFields);
            }

            $.easyAjax({
                type: 'POST',
                url: "{{route('admin.campaigns.store')}}",
                file: true,
                container: "#add-edit-form",
                messagePosition: "toastr",
                success: function (response) {

                    if (response.status == "success" && response.show_next) {
                        $('.continer_step_div').hide();
                        $('.container_step_'+ response.step).show();
                        $("html, body").animate({ scrollTop: 0 }, "slow");

                        if(response.step == 3)
                        {
                            $('#container_step_3_content').html(response.html);
                        }

                        $('#step').val(response.step);
                    } else if(response.status == "success" && !response.show_next)
                    {

                    }
                }
            });
        }

        function goToStep(step)
        {
            clearErrors();
            $('.continer_step_div').hide();
            $('.container_step_'+ step).show();
            $("html, body").animate({ scrollTop: 0 }, "slow");

            $('#step').val(step);
        }

        function clearErrors()
        {
            $('#add-edit-form').find(".has-error").each(function () {
                $(this).find(".invalid-feedback").text("");
                $(this).find('.is-invalid').removeClass("is-invalid");
                $(this).removeClass("has-error");
            });

            $('#add-edit-form').find("#alert").html("");
        }


        function autoReferenceClicked()
        {
            $("#auto_reference_prefix").val('');

            if($('#auto_reference').is(":checked"))
            {
                $("#auto_reference_prefix").prop('disabled', false);
            } else {
                $("#auto_reference_prefix").prop('disabled', true);
            }
        }

        function importTypeClicked()
        {
            var importType = $('input[name=import_type]:checked').val();

            if(importType == 'file')
            {
                $('#importTextDiv').hide();
                $('#withoutImportDiv').hide();
                $('#importFileDiv').show();
            } else if(importType == 'text')
            {
                $('#importFileDiv').hide();
                $('#withoutImportDiv').hide();
                $('#importTextDiv').show();
            }else {
                $('#importFileDiv').hide();
                $('#importTextDiv').hide();
                $('#withoutImportDiv').show();
            }
        }
    </script>
@endsection