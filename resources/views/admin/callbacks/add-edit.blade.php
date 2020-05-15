<div class="modal-header">
    <h5 class="modal-title">
        <i class="fa fa-{{ $icon }}"></i> @if(isset($pendingCallBack->id)) @lang('module_call_enquiry.editFollowUp') @else @lang('module_call_enquiry.createFollowUp') @endif
    </h5>
</div>
{!!  Form::open(['url' => '','autocomplete'=>'off','id'=>'follow-up-edit-form']) 	 !!}
@if(isset($pendingCallBack->id)) <input type="hidden" name="_method" value="PUT"> @endif
<input type="hidden" name="lead_id" value="{{ $lead->id }}" />
<div class="modal-body">

    <div class="form-group row">
        <label class="col-sm-3 col-form-label">@lang('app.campaign')</label>
        <div class="col-sm-9">
            {{ $lead->campaign->name ?? ''}}
        </div>
    </div>

    <div class="form-group row">
        <label class="col-sm-3 col-form-label">@lang('module_call_enquiry.callingAgent')</label>
        <div class="col-sm-9">
            <select name="attempted_by" class="form-control select2">
                <option value="">@lang('module_call_enquiry.selectAgent')</option>
                @foreach($campaignTeamMembers as $campaignTeamMember)
                    <option value="{{ $campaignTeamMember->id }}" @if($pendingCallBack && $campaignTeamMember->id == $pendingCallBack->attempted_by) selected @endif>{{ trim($campaignTeamMember->first_name . ' ' . $campaignTeamMember->last_name) }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <div class="form-group row">
        <label class="col-sm-3 col-form-label">@lang('module_call_enquiry.callbackTime')</label>
        <div class="col-sm-9">
            <input type="text" name="callback_time" class="form-control datetimepicker" value="{{ $pendingCallBack->callback_time ?? '' }}">
        </div>
    </div>

</div>
<div class="modal-footer bg-whitesmoke">
    <button type="submit" class="btn btn-icon icon-left btn-success" onclick="addEditFollowUp({{ $pendingCallBack->id ?? '' }});return false"><i class="fas fa-check"></i> @if(isset($pendingCallBack->id) && $pendingCallBack->id != '') @lang('app.update') @else @lang('app.save') @endif</button>
    @if(isset($pendingCallBack->id))
    <button type="button" class="btn btn-icon icon-left btn-danger" onclick="deleteFollowUp({{ $pendingCallBack->id ?? '' }});return false"><i class="fa fa-ban"></i> @lang('module_call_enquiry.cancelCallback')</button>
    @endif
    <button type="button" class="btn default" onclick="cancelFollowUpModal({{ isset($pendingCallBack->id) ? 1 : 0 }})">@lang('app.cancel')</button>
</div>
{{Form::close()}}

<script>
    $('.datetimepicker').daterangepicker({
        locale: {format: 'YYYY-MM-DD HH:mm'},
        singleDatePicker: true,
        timePicker: true,
    });
</script>