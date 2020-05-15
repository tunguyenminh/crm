<div class="modal-header">
    <h5 class="modal-title">
        <i class="fa fa-{{ $icon }}"></i> @lang('module_campaign.editCampaign')
    </h5>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close"> <span aria-hidden="true">×</span></button>
</div>
{!!  Form::open(['url' => '','autocomplete'=>'off','id'=>'campaigns-edit-form']) 	 !!}
@if(isset($campaignDetails->id)) <input type="hidden" name="_method" value="PUT"> @endif
<div class="modal-body">

    <div class="form-group row">
        <label class="col-sm-4 col-form-label">@lang('module_campaign.name')</label>
        <div class="col-sm-8">
            <input type="text" id="campaign_name" name="campaign_name" class="form-control" value="{{ $campaignDetails->name }}">
        </div>
    </div>

    <div class="form-group row">
        <label class="col-sm-4 col-form-label">@lang('module_campaign.campaignMembers')</label>
        <div class="col-sm-8">
            <div class="row">
                @foreach($teamMembers as $teamMember)
                    <div class="col-12 col-md-12 col-lg-12 mb-3">
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="user_{{ $teamMember->id }}" name="campaign_members[]" value="{{ $teamMember->id }}" @if(in_array($teamMember->id, $campaignMembers)) checked @endif>
                            <label class="custom-control-label" for="user_{{ $teamMember->id }}">{{ $teamMember->name }}</label>
                        </div>
                    </div>
                @endforeach
            </div>
            <input type="hidden" class="form-control" id="campaign_member" name="campaign_member" value="">
        </div>
    </div>

    <div class="form-group row">
        <label class="col-sm-4 col-form-label">@lang('module_campaign.autoReferenceEnable')</label>
        <div class="col-sm-8">
            <label class="custom-switch mt-2">
                <input type="checkbox" id="auto_reference" name="auto_reference" onclick="autoReferenceClicked()" class="custom-switch-input" value="1" @if($campaignDetails->auto_reference) checked @endif>
                <span class="custom-switch-indicator"></span>
            </label>
        </div>
    </div>
    <div class="form-group row">
        <label class="col-sm-4 col-form-label">@lang('module_campaign.autoReferencePrefix')</label>
        <div class="col-sm-8">
            <input type="text" class="form-control" id="auto_reference_prefix" name="auto_reference_prefix" value="{{ $campaignDetails->auto_reference_prefix ?? '' }}" @if(!$campaignDetails->auto_reference) disabled @endif>
        </div>
    </div>

</div>
<div class="modal-footer bg-whitesmoke">
    <button id="saveFormButton" type="submit" class="btn btn-icon icon-left btn-success" onclick="editCampaign({{ $campaignDetails->id }});return false"><i class="fas fa-check"></i> @lang('app.update')</button>
    <button class="btn default" data-dismiss="modal" aria-hidden="true">@lang('app.cancel')</button>
</div>
{{Form::close()}}

<script>
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
</script>