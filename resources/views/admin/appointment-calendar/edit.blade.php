<div class="modal-header">
    <h5 class="modal-title">
        <i class="fa fa-{{ $icon }}"></i> @lang('module_campaign.editAppointment')
    </h5>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close"> <span aria-hidden="true">×</span></button>
</div>
{!!  Form::open(['url' => '','autocomplete'=>'off','id'=>'appointment-edit-form']) 	 !!}
@if(isset($appointment->id)) <input type="hidden" name="_method" value="PUT"> @endif
<div class="modal-body">

    <div class="form-group row">
        <label class="col-sm-3 col-form-label">@lang('app.campaign')</label>
        <div class="col-sm-9">
            {{ $appointment->lead->campaign->name }}
        </div>
    </div>

    <div class="form-group row">
        <label class="col-sm-3 col-form-label">@lang('module_campaign.salesMember')</label>
        <div class="col-sm-9">
            <select id="sales_member_id" name="sales_member_id" class="form-control select2">
                <option value="">@lang('module_campaign.selectSalesMember')</option>
                @foreach($allSalesMembers as $allSalesMember)
                    <option value="{{ $allSalesMember->id }}" @if($allSalesMember->id == $appointment->sales_member_id) selected @endif>{{ trim($allSalesMember->first_name . ' ' . $allSalesMember->last_name) }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <div class="form-group row">
        <label class="col-sm-3 col-form-label">@lang('module_campaign.appointmentTime')</label>
        <div class="col-sm-9">
            <input type="text" id="appointment_time" name="appointment_time" class="form-control datetimepicker" value="{{ $appointment->appointment_time }}">
        </div>
    </div>

</div>
<div class="modal-footer bg-whitesmoke">
    <button id="saveFormButton" type="submit" class="btn btn-icon icon-left btn-success" onclick="editAppointment({{ $appointment->id }});return false"><i class="fas fa-check"></i> @if($appointment->id != '') @lang('app.update') @else @lang('app.save') @endif</button>
    <button id="cancelAppointmentButton" type="button" class="btn btn-icon icon-left btn-danger" onclick="deleteAppointment({{ $appointment->id }});return false"><i class="fas fa-trash"></i> @lang('module_campaign.deleteAppointment')</button>
    <a id="viewLeadButton" class="btn btn-icon icon-left btn-info" href="javascript:void(0);" onclick="viewLead('{{ md5($appointment->lead_id) }}')"><i class="fas fa-eye"></i> @lang('module_campaign.viewLead')</a>
    <button class="btn default" data-dismiss="modal" aria-hidden="true">@lang('app.cancel')</button>
</div>
{{Form::close()}}

<script>
    $('.datetimepicker').daterangepicker({
        locale: {format: 'YYYY-MM-DD HH:mm'},
        singleDatePicker: true,
        timePicker: true,
    });

    function viewLead (id) {
        var url = '{{ route('admin.callmanager.view-lead', ':id') }}';
        url      = url.replace(':id',id);
        $.ajaxModal('#addEditModal', url)
    }
</script>