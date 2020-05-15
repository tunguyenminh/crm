<div class="modal-header">
    <h5 class="modal-title">
        <i class="fa fa-{{ $icon }}"></i> @lang('module_lead.addNewLead')
    </h5>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close"> <span aria-hidden="true">×</span></button>
</div>
{!!  Form::open(['url' => '','autocomplete'=>'off','id'=>'lead-add-edit-form']) 	 !!}
<div class="modal-body">

    @foreach($formFields as $formField)
        <div class="form-group mb-10">
            <label>{{ $formField->field_name }}</label>
            @if(strtolower($formField->field_name) == 'notes' || strtolower($formField->field_name) == 'note')
                <textarea class="form-control" name="fields[{{$formField->id}}]"></textarea>
            @else
                <input type="text" name="fields[{{$formField->id}}]" class="form-control">
            @endif
        </div>
    @endforeach

</div>
<div class="modal-footer bg-whitesmoke">
    <button id="saveFormButton" type="submit" class="btn btn-icon icon-left btn-success" onclick="addNewLead('{{ md5($campaignDetails->id)  }}');return false"><i class="fas fa-check"></i> @lang('app.save')</button>
    <button class="btn default" data-dismiss="modal" aria-hidden="true">@lang('app.cancel')</button>
</div>
{{Form::close()}}