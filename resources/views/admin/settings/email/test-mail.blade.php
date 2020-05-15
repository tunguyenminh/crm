<div class="modal-header modal-designed-header">
    <h5 class="modal-title">
        <i class="fa fa-{{ $icon }}"></i> @lang('module_settings.testMail')
    </h5>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close"> <span aria-hidden="true">×</span></button>
</div>
{!!  Form::open(['url' => '','autocomplete'=>'off','id'=>'test-mail-form']) 	 !!}
<div class="modal-body">

    <div class="row">
        <div class="col-md-12">
            <div class="form-group">
                <label class="control-label">@lang('module_settings.testEmailAddress')</label>
                <input type="text" name="test_email" class="form-control" value="{{ $user->email }}">
            </div>
        </div>
    </div>

</div>
<div class="modal-footer bg-whitesmoke">
    <button type="submit" class="btn btn-icon icon-left btn-success" onclick="sendTestMail();return false"><i class="fas fa-check"></i> @lang('module_settings.send')</button>
    <button class="btn default" data-dismiss="modal" aria-hidden="true">@lang('app.cancel')</button>
</div>
{{Form::close()}}