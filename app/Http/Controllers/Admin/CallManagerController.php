<?php

namespace App\Http\Controllers\Admin;


use App\Classes\Common;

use App\Http\Requests\Admin\SalesMember\DeleteRequest;
use App\Http\Requests\Admin\SalesMember\IndexRequest;
use App\Http\Requests\Admin\SalesMember\StoreRequest;
use App\Http\Requests\Admin\SalesMember\UpdateRequest;

use App\Classes\Reply;
use App\Models\Appointment;
use App\Models\Callback;
use App\Models\CallLog;
use App\Models\Campaign;
use App\Models\EmailTemplate;
use App\Models\FormField;
use App\Models\Lead;
use App\Models\LeadData;
use App\Models\SalesMember;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;

class CallManagerController extends AdminBaseController
{
     /**
	 * UserController constructor.
	 */

    public function __construct()
    {
        parent::__construct();

        $this->pageTitle = trans('menu.callManager');
        $this->pageIcon = 'fa fa-th-large';
        $this->leadManagementMenuActive = 'active';
        $this->callManagerActive = 'active';
    }

    public function index(Request $request)
    {
        $type = $request->has('type') ? $request->type : 'active';

        if($type == 'active')
        {
            $this->userCampaigns = Campaign::select('campaigns.id', 'campaigns.name', 'campaigns.started_on', 'campaigns.total_leads', 'campaigns.remaining_leads')
                ->join('campaign_members', 'campaign_members.campaign_id', '=', 'campaigns.id')
                ->where('campaign_members.user_id', '=', $this->user->id);

            if($type == 'completed')
            {
                $this->userCampaigns = $this->userCampaigns->where('campaigns.status', '=', 'completed');
            } else {
                $this->userCampaigns = $this->userCampaigns->where(function($query) {
                    return $query->where('campaigns.status', '=', 'started')
                        ->orWhereNull('campaigns.status');
                });
            }

            $this->userCampaigns = $this->userCampaigns->with('staffMembers:user_id,campaign_id', 'staffMembers.user')
                ->get();
        }

        $this->type = $type;
        return view('admin.callmanager.index', $this->data);
    }

    public function getLists(Request $request)
    {

        $users = Campaign::select('campaigns.id', 'campaigns.name', 'campaigns.started_on', 'campaigns.completed_on', 'campaigns.total_leads', 'campaigns.remaining_leads')
            ->join('campaign_members', 'campaign_members.campaign_id', '=', 'campaigns.id')
            ->where('campaign_members.user_id', '=', $this->user->id)
            ->where('campaigns.status', '=', 'completed');

        $results = datatables()->eloquent($users);

        $rawColumns = ['name', 'action', 'started_on', 'total_leads'];

        $results = $results
            ->editColumn(
                'name',
                function ($row) {

                    return '<a href="'.route('admin.campaigns.show', md5($row->id)).'">'.$row->name.'</a>';
                }
            )
            ->editColumn(
                'started_on',
                function ($row) {

                    return $row->started_on != NULL ? $row->started_on->timezone($this->user->timezone)->format($this->user->date_format) : '-';
                }
            )
            ->editColumn(
                'completed_on',
                function ($row) {

                    return $row->completed_on != NULL ? $row->completed_on->timezone($this->user->timezone)->format($this->user->date_format) : '-';
                }
            )

            ->addColumn('action', function ($row) {

                $text = '';

                return $text;
            })
            ->rawColumns($rawColumns)
            ->make(true);

        return $results;
    }

    public function takeAction($campaignId)
    {
        $campaign = Campaign::whereRaw('md5(id) = ?', $campaignId)->first();

        // TODO - Check if user is member of this campaign or not


        // Next unactioned lead
        $lead = $this->nextUnActionedLead($campaign->id);

        if($lead == null)
        {
            $fieldValueString = '';

            // Creating new lead
            $lead = new Lead();
            $lead->first_actioned_by = $this->user->id;
            $lead->last_actioned_by = $this->user->id;
            $lead->status = 'actioned';
            $lead->campaign_id = $campaign->id;
            if($campaign->auto_reference)
            {
                $lead->reference_number = $campaign->auto_reference_prefix.'_'.Carbon::now()->timestamp;
            }
            $lead->hash = md5($fieldValueString.$campaign->id);
            $lead->save();

            // Saving Lead Data
            $formFields = FormField::where('form_id', $campaign->form_id)->orderBy('order')->get();

            foreach($formFields as $formField)
            {
                $leadData = new LeadData();
                $leadData->lead_id = $lead->id;
                $leadData->form_field_id = $formField->id;
                $leadData->field_name = $formField->field_name;
                $leadData->field_value = '';
                $leadData->save();
            }
        }

        $totalLeads = Lead::where('campaign_id', $campaign->id)->count();

        $actionedLeads = Lead::where('campaign_id', $campaign->id)
            ->where('status', 'actioned')->count();

        $remainingLeads = $totalLeads - $actionedLeads;

        $campaign->total_leads = $totalLeads;
        $campaign->remaining_leads = $remainingLeads;
        if($campaign->started_on == null)
        {
            $campaign->started_on = Carbon::now();
            $campaign->status = 'started';
        }
        $campaign->save();

        $newLeadUrl = route('admin.callmanager.lead', md5($lead->id));
        return Reply::redirect($newLeadUrl);
    }

    public function stopCampaign($id)
    {
        // Check if user is valid memeber of this campaign
        $campaign = Campaign::find($id);
        $campaign->status = 'completed';
        $campaign->completed_on = Carbon::now();
        $campaign->save();

        // Deleting Pending CallBacks
        Callback::join('leads', 'leads.id', '=', 'callbacks.lead_id')
            ->where('leads.campaign_id', $campaign->id)
            ->delete();

        // Deleting Appointments
        Appointment::join('leads', 'leads.id', '=', 'appointments.lead_id')
            ->where('leads.campaign_id', $campaign->id)
            ->delete();

        // Deleting unactioned leads
        Lead::where('status', 'unactioned')
            ->where('campaign_id', $campaign->id)
            ->delete();


        $totalLeads = Lead::where('campaign_id', $campaign->id)->count();
        $campaign->total_leads = $totalLeads;
        $campaign->remaining_leads = 0;
        $campaign->save();

        return Reply::redirect( route('admin.callmanager.index'), 'module_campaign.stopCampaignSuccess');
    }

    public function getUsersList()
    {

        $users = SalesMember::select('id', 'image', 'first_name', 'last_name', 'email', 'created_at');

        return datatables()->eloquent($users)
            ->editColumn('first_name', function ($row) {
                return Common::getUserWidget($row);
            })
            ->editColumn(
                'email',
                function ($row) {
                    $data = $row->email. ' ';

                    if($row->email_verified == 'yes') {
                        $data .= '<i class="fa fa-check-circle" style="color: green;"></i>';
                    }

                    return $data;
                }
            )
            ->editColumn(
                'created_at',
                function ($row) {
                    return $row->created_at->format('d F, Y');
                }
            )
            ->addColumn('action', function ($row) {
                $text = '<div class="buttons">';


                    $text .= '<a href="javascript:void(0);" onclick="editModal('.$row->id.')" class="btn btn-info btn-icon icon-left"
                      data-toggle="tooltip" data-original-title="'.trans('app.edit').'"><i class="fa fa-edit" aria-hidden="true"></i></a>';

                    $text .= '<button onclick="deleteModal('.$row->id.')" class="btn btn-danger btn-icon icon-left"
                      data-toggle="tooltip" data-original-title="'.trans('app.delete').'"><i class="fa fa-trash" aria-hidden="true"></i></button>';

                $text .= '</div>';

                return $text;
            })
            ->rawColumns(['first_name', 'action', 'email'])
            ->make(true);
    }

    public function startLead($id)
    {
        $this->bootstrapModalRight = false;
        $this->bootstrapModalSize = 'lg';
        $this->showFooter = false;

        // TODO - check this lead comes in campaign for which current logged in user is member or not
        $lead = Lead::with(['callLogs' => function ($query) {
            $query->orderBy('id', 'desc');
        }])->whereRaw('md5(id) = ?', $id)->first();
        $campaign = $lead->campaign;
        $this->emailTemplates = EmailTemplate::where('created_by', $this->user->id)
                                             ->orWhere('shareable', 1)
                                             ->get();

        $this->allCampaigns = Campaign::select('campaigns.*')
                                    ->join('campaign_members', 'campaign_members.campaign_id', '=', 'campaigns.id')
                                    ->where('campaigns.status', 'started')
                                    ->where('campaign_members.user_id', '=', $this->user->id)
                                    ->get();
        $this->lead = $lead;
        $this->campaign = $campaign;

        $formFields = FormField::where('form_id', $campaign->form_id)->orderBy('order')->get();

        $leadDatas = $lead->leadData;
        $leadWithDataAll = [];
        $leadWithData = [];
        $leadWithData1 = [];

        $this->nextLeadCount = Lead::where('id', '>', $lead->id)
            ->where ('campaign_id', $campaign->id)
            ->count();
        $this->previousLeadCount = Lead::where('id', '<', $lead->id)
            ->where ('campaign_id', $campaign->id)
            ->count();

        foreach ($leadDatas as $leadData)
        {
            $leadWithDataAll[$leadData->field_name] = [
                'lead_data_id' => $leadData->id,
                'field_id' => $leadData->form_field_id,
                'field_name'  => $leadData->field_name,
                'field_value'  => $leadData->field_value,

            ];
        }

        foreach ($formFields as $formField)
        {
            if( strtolower($formField->field_name) == 'address' ||
                strtolower($formField->field_name) == 'notes' ||
                strtolower($formField->field_name) == 'postal code' ||
                strtolower($formField->field_name) == 'website'
            ) {
                $leadWithData1[] = isset($leadWithDataAll[$formField->field_name]) ? $leadWithDataAll[$formField->field_name] : '';
            } else if(isset($leadWithDataAll[$formField->field_name])) {
                $leadWithData[] = $leadWithDataAll[$formField->field_name];
            } else {
                // Inserting Lead Data
                $newLeadData = new LeadData();
                $newLeadData->lead_id= $lead->id;
                $newLeadData->form_field_id= $formField->id;
                $newLeadData->field_name= $formField->field_name;
                $newLeadData->field_value= '';
                $newLeadData->save();

                $leadWithData[] = [
                    'lead_data_id' => $newLeadData->id,
                    'field_id' => $formField->id,
                    'field_name'  => $formField->field_name,
                    'field_value'  => ''
                ];
            }
        }

        $this->appointment = $lead->appointment;

        // Save lead appointment booked value
        $lead->appointment_booked = $this->appointment ? 1 : 0;
        $lead->save();

        // Follow Up Call
        $this->followUpCall = Callback::where('lead_id', $lead->id)
                                ->orderBy('callback_time', 'asc')
                                ->first();

        $this->leadNumber = Lead::whereNotNull('first_actioned_by')
                                ->where ('campaign_id', $lead->campaign_id)
                                ->count();
        $this->leadWithData = $leadWithData;
        $this->leadWithData1 = $leadWithData1;

        // Lead Call Log
        $callLog = new CallLog();
        $callLog->lead_id = $lead->id;
        $callLog->attempted_by = $this->user->id;
        $callLog->started_on = $lead->time_taken ?? 0;
        $callLog->time_taken = 0;
        $callLog->save();

        $this->callLog = $callLog;
        return view('admin.callmanager.startlead', $this->data);
    }

    public function takeLeadAction(Request $request, $leadId, $action)
    {
        $currentLead = Lead::find($leadId);

        if($action == 'back')
        {
            $lead = Lead::where('id', '<', $leadId)
                           ->where('last_actioned_by', $this->user->id)
                           ->where ('campaign_id', $currentLead->campaign_id)
                           ->orderBy('id', 'desc')
                            ->first();

            // Saving lead data
            $this->saveLeadData($request, md5($currentLead->id));

            return Reply::redirect( route('admin.callmanager.lead', [md5($lead->id)]), 'module_campaign.leadDataSaved');
        } else if($action == 'next')
        {
            // Checking if next lead exists for current user
            $lead = Lead::where('id', '>', $leadId)
                ->where('last_actioned_by', $this->user->id)
                ->where ('campaign_id', $currentLead->campaign_id)
                ->orderBy('id', 'asc')
                ->first();

            if(!$lead)
            {
                $lead = Lead::where('id', '>', $leadId)
                    ->where ('campaign_id', $currentLead->campaign_id)
                    ->orderBy('id', 'asc')
                    ->first();
            }

            // Saving lead data
            $this->saveLeadData($request, md5($currentLead->id));

            // If no further lead exists in campaign
            if(!$lead)
            {
                return Reply::redirect( route('admin.callmanager.index'), 'module_campaign.noLeadExists');
            }

            return Reply::redirect( route('admin.callmanager.lead', [md5($lead->id)]), 'module_campaign.leadDataSaved');
        }
    }

    public function saveLeadData(Request $request, $id)
    {
        $lead = Lead::whereRaw('md5(id) = ?', $id)->first();
        $campaign = $lead->campaign;

        // Saving Lead related data
        $lead->interested = $request->interested == '' ? null : $request->interested;
        $lead->email_template_id = ($request->send_email == '' || $request->send_email == 'new') ? null : $request->send_email;
        $lead->appointment_booked = $request->book_appointment;
        $lead->time_taken = $request->time_taken;
        if(!$campaign->auto_reference)
        {
            $lead->reference_number = $request->reference_number;
        }

        $lead->first_actioned_by = $lead->first_actioned_by === null ? $this->user->id : $lead->first_actioned_by;
        $lead->last_actioned_by = $lead->last_actioned_by === null ? $this->user->id : $lead->last_actioned_by;
        $lead->status = 'actioned';

        // TODO - Update timer
        $lead->save();


        $totalLeads = Lead::where('campaign_id', $campaign->id)->count();
        $actionedLeads = Lead::where('campaign_id', $campaign->id)
            ->where('status', 'actioned')->count();
        $remainingLeads = $totalLeads - $actionedLeads;
        $campaign->total_leads = $totalLeads;
        $campaign->remaining_leads = $remainingLeads;
        $campaign->save();

        // Saving Lead Data
        $fields = $request->fields;
        foreach ($fields as $key => $field)
        {
            $leadData = LeadData::find($key);
            $leadData->field_value = $field ?? '';
            $leadData->save();
        }

        // Save Call Log Time
        $callLog = CallLog::whereRaw('md5(id) = ?', $request->call_log_id)
                          ->where('attempted_by', $this->user->id)
                          ->first();

        if($callLog)
        {
            $callLog->time_taken = $request->time_taken - $callLog->started_on;
            $callLog->save();
        }

        return Reply::success('messages.updateSuccess');
    }

    // Delete lead and redirect to unactioned/next lead
    public function skipAndDelete(Request $request, $id)
    {
        $lead = Lead::whereRaw('md5(id) = ?', $id)->first();
        $leadId = $lead->id;
        $userActiveCampaigns = $this->user->activeCampaigns()->pluck('id')->toArray();

        if(!$this->user->hasRole('admin') && !in_array($lead->campaign_id, $userActiveCampaigns))
        {
            $errorMessage = Reply::error('messages.notAllowed');

            return response()->json($errorMessage);
        }

        $campaign = $this->deleteCampaignLead($lead);

        // This is for request coming from call enquiry page
        if($request->has('delete') && $request->delete == 'yes')
        {
            // Call Enquiry Stats
            $this->getCallEnquiryStats($request->campaign_id);

            $htmlStats = view('admin.call-enquiry.call-enquiry-stats', $this->data)->render();

            $resultData = [
                'stats' => $htmlStats
            ];

            return Reply::successWithData( $resultData);
        }

        // This is for request coming from call enquiry page
        if($request->has('delete_callback') && $request->delete_callback == 'yes')
        {

            // Get callback information of today or un actioned calls
            $this->getCallBackStats();

            $htmlStats = view('admin.callbacks.call-back-stats', $this->data)->render();

            $resultData = [
                'stats' => $htmlStats
            ];

            return Reply::successWithData( $resultData);
        }

        $nextLead = Lead::where('id', '>', $leadId)
            ->where('last_actioned_by', $this->user->id)
            ->where ('campaign_id', $campaign->id)
            ->orderBy('id', 'asc')
            ->first();

        if($nextLead)
        {
            $redirectUrl = route('admin.callmanager.lead', [md5($nextLead->id)]);
        } else {

            // Next unactioned lead
            $nextUnActionedLead = $this->nextUnActionedLead($campaign->id);

            if($nextUnActionedLead)
            {
                $redirectUrl = route('admin.callmanager.lead', [md5($nextUnActionedLead->id)]);
            } else {
                $redirectUrl = route('admin.callmanager.index');
            }
        }

        return Reply::redirect($redirectUrl, 'messages.deleteSuccess');
    }

    // Skip lead and redirect to unactioned/next lead
    public function comeBack(Request $request, $id)
    {
        $lead = Lead::whereRaw('md5(id) = ?', $id)->first();
        $leadId = $lead->id;
        $userActiveCampaigns = $this->user->activeCampaigns()->pluck('id')->toArray();

        if(!$this->user->hasRole('admin') && !in_array($lead->campaign_id, $userActiveCampaigns))
        {
            $errorMessage = Reply::error('messages.notAllowed');

            return response()->json($errorMessage);
        }

        $nextLead = Lead::where('id', '>', $leadId)
            ->where('last_actioned_by', $this->user->id)
            ->where ('campaign_id', $lead->campaign_id)
            ->orderBy('id', 'asc')
            ->first();

        if($nextLead)
        {
            $redirectUrl = route('admin.callmanager.lead', [md5($nextLead->id)]);
        } else {

            // Next unactioned lead
            $nextUnActionedLead = $this->nextUnActionedLead($lead->campaign_id);

            if($nextUnActionedLead)
            {
                $redirectUrl = route('admin.callmanager.lead', [md5($nextUnActionedLead->id)]);
            } else {
                $redirectUrl = route('admin.callmanager.index');
            }
        }

        return Reply::redirect($redirectUrl, 'messages.deleteSuccess');
    }

    private function nextUnActionedLead($campaignId)
    {
        // Taking latest unactioned lead
        $lead = Lead::where('campaign_id', $campaignId)
            ->where('status', 'unactioned')
            ->oldest()
            ->first();

        if($lead)
        {
            $lead->first_actioned_by = $this->user->id;
            $lead->last_actioned_by = $this->user->id;
            $lead->status = 'actioned';
            $lead->save();

            return $lead;
        } else {
            return null;
        }
    }

    public function cancelCallback(Request $request, $id)
    {
        $lead = Lead::whereRaw('md5(id) = ?', $id)->first();
        $userActiveCampaigns = $this->user->activeCampaigns()->pluck('id')->toArray();

        if(!$this->user->hasRole('admin') && !in_array($lead->campaign_id, $userActiveCampaigns))
        {
            $errorMessage = Reply::error('messages.notAllowed');

            return response()->json($errorMessage);
        }

        // Delete callback with lead & user
        $callBack = Callback::where('lead_id', $lead->id);
        if(!$this->user->hasRole('admin'))
        {
            $callBack = $callBack->where('attempted_by', $this->user->id);
        }
        $callBack->delete();

        if($request->has('cancel_callback') && $request->cancel_callback == 'yes')
        {

            // Get callback information of today or un actioned calls
            $this->getCallBackStats();

            $htmlStats = view('admin.callbacks.call-back-stats', $this->data)->render();

            $resultData = [
                'stats' => $htmlStats
            ];

            return Reply::successWithData( $resultData);
        }

        return Reply::success( 'module_call_enquiry.cancelSuccessful');
    }

    public function saveLeadTime(Request $request, $id)
    {
        $lead = Lead::whereRaw('md5(id) = ?', $id)->first();
        $lead->time_taken = $request->time_taken;
        $lead->save();

        return Reply::success( '');
    }

    public function viewLead(Request $request, $id)
    {
        $this->icon = 'eye';

        $this->lead = Lead::whereRaw('md5(id) = ?', $id)->with('campaign', 'leadData', 'lastActioner')->first();
        $this->leadCampaign = $this->lead->campaign;

        return view('admin.callmanager.view-lead', $this->data);
    }

    private function deleteCampaignLead($lead)
    {
        $campaign = Campaign::find($lead->campaign_id);
        $lead->delete();

        $totalLeads = Lead::where('campaign_id', $campaign->id)->count();
        $actionedLeads = Lead::where('campaign_id', $campaign->id)
            ->where('status', 'actioned')->count();
        $remainingLeads = $totalLeads - $actionedLeads;

        $campaign->total_leads = $totalLeads;
        $campaign->remaining_leads = $remainingLeads;
        $campaign->save();

        return $campaign;
    }
}
