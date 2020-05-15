@extends('admin.admin_layouts')

@section('styles')
    <link href="{{ asset('assets/modules/select2/dist/css/select2.min.css') }}" rel="stylesheet">
@endsection

@section('breadcrumb')

    <div class="section-header">
        <h1><i class="{{ $pageIcon }}"></i> {{ $pageTitle }}</h1>
        <div class="section-header-breadcrumb">
            <div class="breadcrumb-item active"><a href="{{ route('admin.dashboard.index') }}">@lang('menu.home')</a></div>
            <div class="breadcrumb-item">{{ $pageTitle }}</div>
        </div>
    </div>
@endsection

@section('content')

    {!!  Form::open(['url' => '','autocomplete'=>'off','id'=>'add-edit-form']) 	 !!}
    <input type="hidden" id="lead_form_fields" name="lead_form_fields" value="" />
    <div class="row" id="step_2_div">
        <div class="col-12">
            <div class="card">
                <div class="card-header card-header-description">
                    <h4>@lang('module_campaign.importLeadsToCampaign')</h4>
                    <p>@lang('module_campaign.step2Description')</p>
                </div>
                <div class="card-body">
                    <div class="form-group row mb-4">
                        <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">@lang('module_campaign.selectCampaign')</label>
                        <div class="col-sm-12 col-md-8">
                            <select class="form-control select2" id="campaign_id" name="campaign_id">
                                <option value="">@lang('module_campaign.selectCampaign')...</option>
                                @foreach($user->activeCampaigns() as $activeCampaign)
                                    <option value="{{ $activeCampaign->id }}">{{ $activeCampaign->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
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
                </div>
                <div class="card-footer">
                    <div class="form-group row mb-4">
                        <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3"></label>
                        <div class="col-sm-12 col-md-7">
                            <button type="button" class="btn btn-icon icon-left btn-info" onclick="importLead();return false"><i class="fas fa-upload"></i> @lang('module_campaign.processData')</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div id="step_3_div">

    </div>
    {{Form::close()}}

@endsection

@section('modals')
    @include('admin.includes.add-edit-modal')
@endsection

@section('scripts')
<script src="{{ asset('assets/modules/select2/dist/js/select2.full.min.js') }}"></script>
<script src="{{ asset('assets/modules/jquery-ui/jquery-ui.min.js') }}"></script>
<script>

    function importTypeClicked()
    {
        var importType = $('input[name=import_type]:checked').val();

        if(importType == 'file')
        {
            $('#importTextDiv').hide();
            $('#importFileDiv').show();
        } else if(importType == 'text')
        {
            $('#importFileDiv').hide();
            $('#importTextDiv').show();
        }
    }

    function importLead() {
        var url = "{{ route('admin.campaigns.import-lead-data') }}";

        $.easyAjax({
            type: 'POST',
            url: url,
            file: true,
            container: "#add-edit-form",
            messagePosition: "toastr",
            success: function (response) {
                if(response.status == 'success')
                {
                    $('#step_2_div').hide();
                    $('#step_3_div').html(response.data.html);
                }
            }
        });
    }
    
    function submitLeadData() {
        var leadFormFields =  JSON.stringify(jsMatchedColumnArray);
        $('#lead_form_fields').val(leadFormFields);

        var url = "{{ route('admin.campaigns.save-lead-data') }}";

        $.easyAjax({
            type: 'POST',
            url: url,
            file: true,
            container: "#add-edit-form",
            messagePosition: "toastr",
            success: function (response) {
                if(response.status == 'success')
                {
                    $('#step_2_div').hide();
                    $('#step_3_div').html(response.data.html);
                }
            }
        });
    }
</script>
@endsection