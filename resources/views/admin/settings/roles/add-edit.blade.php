<div class="modal-header">
    <h5 class="modal-title">
        <i class="fa fa-{{ $icon }}"></i> @if($roleDetails->id != NULL) @lang('module_settings.editRole') @else @lang('module_settings.createRole') @endif
    </h5>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close"> <span aria-hidden="true">×</span></button>
</div>
{!!  Form::open(['url' => '','autocomplete'=>'off','id'=>'role-add-edit-form']) 	 !!}
@if(isset($roleDetails->id)) <input type="hidden" name="_method" value="PUT"> @endif
<div class="modal-body">

    <h5 class="box-title text-primary"><b>@lang('module_settings.basicDetails')</b></h5>
    <hr>

    <div class="form-group row">
        <label class="col-sm-3 col-form-label">@lang('module_settings.displayName')</label>
        <div class="col-sm-9">
            <input type="text" name="display_name" id="display_name" class="form-control" value="{{ isset($roleDetails->display_name) ? $roleDetails->display_name : '' }}">
        </div>
    </div>

    <div class="form-group row">
        <label class="col-sm-3 col-form-label">@lang('module_settings.roleName')</label>
        <div class="col-sm-9">
            <input type="text" name="name" id="name" class="form-control" value="{{ isset($roleDetails->name) ?$roleDetails->name : '' }}">
        </div>
    </div>

    <div class="form-group row">
        <label class="col-sm-3 col-form-label">@lang('app.description')</label>
        <div class="col-sm-9">
            <textarea class="form-control" name="description" id="description" rows="4">{{ isset($roleDetails->description) ? $roleDetails->description : '' }}</textarea>
        </div>
    </div>

    <h5 class="box-title text-info">@lang('module_settings.permissionSetup')</h5>
    <hr>
    @include('admin.settings.roles.include_add_edit')
    <span class="help-block"></span>

</div>
<div class="modal-footer bg-whitesmoke">
    <button id="saveFormButton" type="button" class="btn btn-icon icon-left btn-success" onclick="addOrEditRole({{ isset($roleDetails->id) ? $roleDetails->id : '' }});return false"><i class="fas fa-check"></i> @if($roleDetails->id != '') @lang('app.update') @else @lang('app.save') @endif</button>
    <button class="btn default" data-dismiss="modal" aria-hidden="true">@lang('app.cancel')</button>
</div>
{{Form::close()}}