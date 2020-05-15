<div class="modal-header">
    <h5 class="modal-title">
        <i class="fa fa-{{ $icon }}"></i> @if(isset($userDetails->id)) @lang('module_user.editUser') @else @lang('module_user.createUser') @endif
    </h5>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close"> <span aria-hidden="true">×</span></button>
</div>
{!!  Form::open(['url' => '','autocomplete'=>'off','id'=>'user-add-edit-form']) 	 !!}
@if(isset($userDetails->id)) <input type="hidden" name="_method" value="PUT"> @endif
<div class="modal-body">

    <div class="form-group row">
        <label class="col-sm-3 col-form-label">@lang('module_user.firstName')</label>
        <div class="col-sm-9">
            <input type="text" name="first_name" class="form-control" value="{{ isset($userDetails->first_name) ?  $userDetails->first_name : '' }}">
        </div>
    </div>

    <div class="form-group row">
        <label class="col-sm-3 col-form-label">@lang('module_user.lastName')</label>
        <div class="col-sm-9">
            <input type="text" name="last_name" class="form-control" value="{{ isset($userDetails->last_name) ?  $userDetails->last_name : '' }}">
        </div>
    </div>

    <div class="form-group row">
        <label class="col-sm-3 col-form-label">@lang('module_user.email')</label>
        <div class="col-sm-9">
            <input type="text" name="email" class="form-control" value="{{ isset($userDetails->email) ? $userDetails->email : '' }}">
        </div>
    </div>

    <div class="form-group row">
        <label for="exampleInputPassword1" class="col-sm-3 col-form-label">@lang('module_user.profileImage')</label>
        <div class="col-sm-9">
            <div class="fileinput fileinput-new" data-provides="fileinput">
                <div class="fileinput-new thumbnail" style="width: 200px; height: 150px;">
                    <img src="{{ $userDetails->image_url }}" alt=""/>
                </div>
                <div class="fileinput-preview fileinput-exists thumbnail" style="max-width: 200px; max-height: 150px;">
                </div>
                <div>
                                          <span class="btn btn-info btn-file">
                                          <span class="fileinput-new">
                                          @lang('app.selectImage') </span>
                                          <span class="fileinput-exists">
                                          @lang('app.change') </span>
                                          <input type="file" name="image" id="image">
                                          </span>
                    <a href="#" class="btn btn-danger fileinput-exists" data-dismiss="fileinput">
                        Remove </a>
                </div>
            </div>
        </div>
    </div>

    <div class="form-group row">
        <label class="col-sm-3 col-form-label">@lang('module_user.contactNumber')</label>
        <div class="col-sm-9">
            <input type="text" name="contact_number" class="form-control" value="{{ isset($userDetails->contact_number) ? $userDetails->contact_number : '' }}">
        </div>
    </div>

    <div class="form-group row">
        <label class="col-sm-3 col-form-label">@lang('module_user.skypeId')</label>
        <div class="col-sm-9">
            <input type="text" name="skype_id" class="form-control" value="{{ isset($userDetails->skype_id) ? $userDetails->skype_id : '' }}">
        </div>
    </div>

</div>
<div class="modal-footer bg-whitesmoke">
    <button id="saveFormButton" type="submit" class="btn btn-icon icon-left btn-success" onclick="addOrEditUser({{ isset($userDetails->id) ? $userDetails->id : '' }});return false"><i class="fas fa-check"></i> @if($userDetails->id != '') @lang('app.update') @else @lang('app.save') @endif</button>
    <button class="btn default" data-dismiss="modal" aria-hidden="true">@lang('app.cancel')</button>
</div>
{{Form::close()}}