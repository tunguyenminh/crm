<?php

namespace App\Http\Controllers\Admin;


use App\Classes\Common;

use App\Http\Requests\Admin\EmailTemplate\DeleteRequest;
use App\Http\Requests\Admin\EmailTemplate\IndexRequest;
use App\Http\Requests\Admin\EmailTemplate\SendMailRequest;
use App\Http\Requests\Admin\EmailTemplate\StoreRequest;
use App\Http\Requests\Admin\EmailTemplate\UpdateRequest;

use App\Classes\Reply;
use App\Models\Campaign;
use App\Models\EmailTemplate;
use App\Models\FormField;
use App\Models\Lead;
use App\Models\LeadData;
use App\Notifications\SendCampaignEmail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;

class EmailTemplateController extends AdminBaseController
{
     /**
	 * UserController constructor.
	 */

    public function __construct()
    {
        parent::__construct();

        $this->pageTitle = trans('menu.emailTemplates');
        $this->pageIcon = 'fa fa-envelope-open-text';
        $this->settingsMenuActive = 'active';
        $this->emailTemplateActive = 'active';
        $this->bootstrapModalRight = false;
    }

    public function index(IndexRequest $request)
    {
        return view('admin.email-templates.index', $this->data);
    }

    public function getLists()
    {

        $results = EmailTemplate::select('email_templates.id', 'email_templates.name', 'email_templates.subject', 'email_templates.created_by', 'email_templates.created_at')
                     ->with('creator');

        // Fetch data according to permission assigned
        if(!$this->user->ability('admin', 'email_template_view_all')) {
            $results = $results->where('email_templates.created_by', $this->user->id);
        }

        return datatables()->eloquent($results)
            ->addColumn('creator', function ($row) {
                return $row->creator ? Common::getUserWidget($row->creator) : '-';
            })
            ->editColumn(
                'created_at',
                function ($row) {
                    return $row->created_at->format('d F, Y');
                }
            )
            ->addColumn('action', function ($row) {
                $text = '<div class="buttons">';

                if($this->user->ability('admin', 'email_template_edit')) {
                    $text .= '<a href="' . route('admin.email-templates.edit', $row->id) . '" class="btn btn-info btn-icon icon-left"
                      data-toggle="tooltip" data-original-title="' . trans('app.edit') . '"><i class="fa fa-edit" aria-hidden="true"></i></a>';
                }

                if($this->user->ability('admin', 'email_template_delete')) {
                    $text .= '<button onclick="deleteModal(' . $row->id . ')" class="btn btn-danger btn-icon icon-left"
                      data-toggle="tooltip" data-original-title="' . trans('app.delete') . '"><i class="fa fa-trash" aria-hidden="true"></i></button>';
                }

                $text .= '</div>';

                return $text;
            })
            ->rawColumns(['creator', 'action'])
            ->make(true);
    }

    public function create()
    {
        $this->pageTitle = trans('module_email_template.createEmailTemplate');
        $this->icon = 'plus';

        $this->emailTemplate = new EmailTemplate();
        $this->formFields = FormField::select('field_name')->groupBy('field_name')->get();
        return view('admin.email-templates.add-edit', $this->data);
    }

    public function store(StoreRequest $request)
    {

        \DB::beginTransaction();

        $emailTemplate = new EmailTemplate();
        $emailTemplate->created_by = $this->user->id;
        $this->storeAndUpdate($emailTemplate, $request);

        \DB::commit();

        return Reply::redirect(route('admin.email-templates.index'), 'messages.createSuccess');

    }

    public function edit($id)
    {
        $this->pageTitle = trans('module_email_template.editEmailTemplate');
        $this->icon = 'edit';

        $emailTemplate = EmailTemplate::findOrFail($id);

        // Check if current email template created by logged in user
        // and not have permission to see all email templates
        if(!$this->user->ability('admin', 'email_template_view_all') && $emailTemplate->created_by != $this->user->id) {
            return response()->view($this->forbiddenErrorView);
        }

        $this->emailTemplate = $emailTemplate;
        $this->formFields = FormField::select('field_name')->groupBy('field_name')->get();

        // Call the same create view for edit
        return view('admin.email-templates.add-edit', $this->data);
    }

    public function update(UpdateRequest $request,$id)
    {

        \DB::beginTransaction();

        $emailTemplate         = EmailTemplate::findOrFail($id);

        // Check if current email template by created logged in user
        // and not have permission to see all email templates
        if(!$this->user->ability('admin', 'email_template_view_all') && $emailTemplate->created_by != $this->user->id) {
            return Reply::error('messages.notAllowed');
        }

        $this->storeAndUpdate($emailTemplate, $request);

        \DB::commit();

        return Reply::redirect(route('admin.email-templates.index'), 'messages.updateSuccess');

    }

    public function destroy(DeleteRequest $request, $id)
    {
        $emailTemplate = EmailTemplate::findOrFail($id);

        // Check if current email template by logged in user
        // and not have permission to see all email templates
        if(!$this->user->ability('admin', 'email_template_view_all') && $emailTemplate->created_by != $this->user->id) {
            return Reply::error('messages.notAllowed');
        }

        $emailTemplate->delete();

        return Reply::success('messages.deleteSuccess');
    }

    private function  storeAndUpdate($emailTemplate, $request)
    {
        $emailTemplate->name   = $request->template_name;
        $emailTemplate->subject   = $request->template_subject;
        $emailTemplate->content   = $request->template_content;
        $emailTemplate->shareable = $request->has('shareable') ? 1 : 0;
        $emailTemplate->save();
    }

    public function writeOrEditEmail($leadId, $id = null)
    {
        $this->icon = 'edit';
        $this->lead = Lead::whereRaw('md5(id) = ?', $leadId)->first();
        $campaign = $this->lead->campaign;

        if($id != '' && $id != null)
        {
            $this->emailTemplate =  EmailTemplate::find($id);
        } else {
            $this->emailTemplate = new EmailTemplate();
        }

        $this->formFields = FormField::where('form_id', $campaign->form_id)
                               ->orderBy('order')
                               ->get();

        $senderEmail = LeadData::where('lead_id', $this->lead->id)
                               ->where(function($query) {
                                   $query->whereRaw('LOWER(field_name) = ?', 'email')
                                         ->orWhereRaw('LOWER(field_name) = ?', 'email address');
                               })
                               ->first();

        $this->campaign =$campaign;
        $this->senderEmail = $senderEmail ? $senderEmail->field_value : '';
        return view('admin.email-templates.write-edit-email', $this->data);
    }

    public function sendMail(SendMailRequest $request, $leadId)
    {
        // TODO - Check Email Setting is verified or not

        $templateSubject = $request->template_subject;
        $templateContent = $request->template_content;
        $lead = Lead::whereRaw('md5(id) = ?', $leadId)->first();

        $leadDatas = LeadData::where('lead_id', $lead->id)
                                ->get();

        foreach ($leadDatas as $leadData)
        {
            $fieldNameString = '##'.$leadData->field_name.'##';
            $fieldValue = $leadData->field_value;

            $templateContent = str_replace($fieldNameString, $fieldValue, $templateContent);
        }

        Notification::route('mail', $request->sender_email)->notify(new SendCampaignEmail($templateSubject, $templateContent));

        return Reply::success('module_email_template.emailSentSuccessfully');
    }
}
