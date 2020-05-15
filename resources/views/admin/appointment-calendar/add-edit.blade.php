<div class="modal-header">
    <h5 class="modal-title">
        <i class="fa fa-{{ $icon }}"></i> @if(isset($appointment->id)) @lang('module_campaign.editAppointment') @else @lang('module_campaign.createAppointment') @endif
    </h5>
</div>
{!!  Form::open(['url' => '','autocomplete'=>'off','id'=>'appointment-edit-form']) 	 !!}
@if(isset($appointment->id)) <input type="hidden" name="_method" value="PUT"> @endif
<input type="hidden" name="lead_id" value="{{ $lead->id }}" />
<div class="modal-body">

    <div class="form-group row">
        <label class="col-sm-3 col-form-label">@lang('app.campaign')</label>
        <div class="col-sm-9">
            {{ $lead->campaign->name ?? ''}}
        </div>
    </div>

    <div class="form-group row">
        <label class="col-sm-3 col-form-label">@lang('module_campaign.salesMember')</label>
        <div class="col-sm-9">
            <select id="sales_member_id" name="sales_member_id" class="form-control select2">
                <option value="">@lang('module_campaign.selectSalesMember')</option>
                @foreach($allSalesMembers as $allSalesMember)
                    <option value="{{ $allSalesMember->id }}" @if($appointment && $allSalesMember->id == $appointment->sales_member_id) selected @endif>{{ trim($allSalesMember->first_name . ' ' . $allSalesMember->last_name) }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <div class="form-group row">
        <label class="col-sm-3 col-form-label">@lang('module_campaign.appointmentTime')</label>
        <div class="col-sm-9">
            <input type="text" id="appointment_time" name="appointment_time" class="form-control datetimepicker" value="{{ $appointment->appointment_time ?? '' }}">
        </div>
    </div>

</div>
<div class="modal-footer bg-whitesmoke">
    <button id="saveFormButton" type="submit" class="btn btn-icon icon-left btn-success" onclick="addEditAppointment({{ $appointment->id ?? '' }});return false"><i class="fas fa-check"></i> @if(isset($appointment->id) && $appointment->id != '') @lang('app.update') @else @lang('app.save') @endif</button>
    @if(isset($appointment->id))
    <button id="cancelAppointmentButton" type="button" class="btn btn-icon icon-left btn-danger" onclick="deleteAppointment({{ $appointment->id ?? '' }});return false"><i class="fas fa-trash"></i> @lang('module_campaign.deleteAppointment')</button>
    @endif
    <button type="button" class="btn default" onclick="cancelAppointmentModal({{ isset($appointment->id) ? 1 : 0 }})">@lang('app.cancel')</button>
</div>
{{Form::close()}}

<script>
    $('.datetimepicker').daterangepicker({
        locale: {format: 'YYYY-MM-DD HH:mm'},
        singleDatePicker: true,
        timePicker: true,
    });
</script>