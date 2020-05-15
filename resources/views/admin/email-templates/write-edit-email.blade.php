<div class="modal-header modal-designed-header">
    <h5 class="modal-title">
        <i class="fa fa-{{ $icon }}"></i> @lang('module_email_template.sendMail')
    </h5>
</div>
{!!  Form::open(['url' => '','autocomplete'=>'off','id'=>'send-mail-form']) 	 !!}
<div class="modal-body">

    @if(isset($emailTemplate->name))
    <div class="form-group row">
        <label class="col-sm-3 col-form-label">@lang('module_email_template.templateName')</label>
        <div class="col-sm-9">
            {{ $emailTemplate->name ?? '' }}
        </div>
    </div>
    @endif

    <div class="form-group row">
        <label class="col-sm-3 col-form-label">@lang('module_email_template.senderEmail')</label>
        <div class="col-sm-9">
            <input type="text" class="form-control" id="sender_email" name="sender_email" value="{{ $senderEmail ?? '' }}">
        </div>
    </div>

    <div class="form-group row">
        <label class="col-sm-3 col-form-label">@lang('module_email_template.templateSubject')</label>
        <div class="col-sm-9">
            <input type="text" class="form-control" id="template_subject" name="template_subject" value="{{ $emailTemplate->subject ?? '' }}">
        </div>
    </div>

    <div class="form-group row">
        <label class="col-sm-3 col-form-label">@lang('module_email_template.template')</label>
        <div class="col-sm-9">
            <textarea  class="form-control" id="template_content" name="template_content">{{ $emailTemplate->content ?? '' }}</textarea>
        </div>
    </div>

    <div class="form-group row">
        <label class="col-sm-3 col-form-label">@lang('module_email_template.formFields')</label>
        <div class="col-sm-9">
            <div class="row">
                @foreach($formFields as $formField)
                    <div class="col-3 col-md-3 col-lg-3 mb-3">
                        <a href="javascript:void(0);" onclick="addTextToEditor('{{ $formField->field_name }}')">{{ $formField->field_name }}</a>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

</div>
<div class="modal-footer bg-whitesmoke">
    <button type="submit" class="btn btn-icon icon-left btn-success" onclick="sendMail();return false"><i class="fa fa-paper-plane"></i> @lang('module_email_template.sendMail')</button>
    <button class="btn default" data-dismiss="modal" aria-hidden="true">@lang('app.cancel')</button>
</div>
{{Form::close()}}

<script>

    $(function () {
        $("#template_content").summernote({
            dialogsInBody: true,
            minHeight: 250,
        });
    });

    function addTextToEditor(text) {
        $('#template_content').summernote('insertText', '##'+text+'##');
    }

</script>