<div class="modal-header">
    <h5 class="modal-title">
        <i class="fa fa-{{ $icon }}"></i> @lang('module_form.addNewField')
    </h5>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close"> <span aria-hidden="true">×</span></button>
</div>
{!!  Form::open(['url' => '','autocomplete'=>'off','id'=>'form-field-add-form']) 	 !!}
<div class="modal-body">

    <div class="form-group row">
        <label class="col-sm-3 col-form-label">@lang('module_form.fieldName')</label>
        <div class="col-sm-9">
            <input type="text" id="new_field_name" name="new_field_name" class="form-control" value="">
        </div>
    </div>

</div>
<div class="modal-footer bg-whitesmoke">
    <button id="saveFormButton" type="submit" class="btn btn-icon icon-left btn-success" onclick="addNewField();return false"><i class="fas fa-check"></i> @lang('app.add')</button>
    <button class="btn default" data-dismiss="modal" aria-hidden="true">@lang('app.cancel')</button>
</div>
{{Form::close()}}