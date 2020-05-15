<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                @include('admin.campaigns.import-leads-data-content')
            </div>
            <div class="card-footer">
                <div class="form-group row mb-4">
                    <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3"></label>
                    <div class="col-sm-12 col-md-7">
                        <button type="button" class="btn btn-icon icon-left btn-info" onclick="submitLeadData();return false"><i class="fas fa-upload"></i> @lang('module_campaign.importLeadsToCampaign')</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
