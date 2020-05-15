<?php

namespace App\Http\Controllers\Admin;


use App\Classes\Common;

use App\Http\Requests\Admin\Campaign\DeleteRequest;
use App\Http\Requests\Admin\Campaign\ImportLeadRequest;
use App\Http\Requests\Admin\Campaign\IndexRequest;
use App\Http\Requests\Admin\Campaign\StoreRequest;
use App\Http\Requests\Admin\Campaign\UpdateRequest;

use App\Classes\Reply;
use App\Models\Campaign;
use App\Models\CampaignMember;
use App\Models\Form;
use App\Models\FormField;
use App\Models\Lead;
use App\Models\LeadData;
use App\Models\SalesMember;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Str;

class CampaignController extends AdminBaseController
{
     /**
	 * UserController constructor.
	 */

    public function __construct()
    {
        parent::__construct();

        $this->pageTitle = trans('menu.campaigns');
        $this->pageIcon = 'fa fa-business-time';
        $this->leadManagementMenuActive = 'active';
    }

    /**
     * @param IndexRequest $request
     * @return \Illuminate\Contracts\View\View
     */
    public function index(IndexRequest $request)
    {
        $this->campaignActive = 'active';
        $this->campaignType = $request->has('type') && $request->type == 'completed' ? 'completed' : 'active';
        return view('admin.campaigns.index', $this->data);
    }

     /**
	 * @return mixed
	 */
    public function getLists(Request $request)
    {

        if($this->user->ability('admin', 'campaign_view_all'))
        {
            $users = Campaign::select('campaigns.id', 'campaigns.name', 'campaigns.started_on', 'campaigns.completed_on', 'campaigns.total_leads', 'campaigns.remaining_leads')
                    ->with('staffMembers:user_id,campaign_id', 'staffMembers.user');

        } else {
            $users = Campaign::select('campaigns.id', 'campaigns.name', 'campaigns.started_on', 'campaigns.completed_on', 'campaigns.total_leads', 'campaigns.remaining_leads')
                ->where('campaigns.created_by', '=', $this->user->id)
                ->with('staffMembers:user_id,campaign_id', 'staffMembers.user');
        }

        if($request->campaign_type == 'completed')
        {
            $users = $users->where('campaigns.status', '=', 'completed');
            $rawColumns = ['name', 'action', 'started_on', 'total_leads'];

            $results = datatables()->eloquent($users);

        } else {
            $users = $users->where(function ($query) {
                $query->where('campaigns.status', '!=', 'completed')
                      ->orWhereNull('campaigns.status');

            });
            $rawColumns = ['name', 'progress', 'members', 'action', 'started_on', 'total_leads'];

            $results = datatables()->eloquent($users)
                                    ->addColumn(
                                        'members',
                                        function ($row) {

                                            if($row->staffMembers->count() > 0)
                                            {
                                                $string = '';
                                                $randomColorArray = ['bg-success', 'bg-success', 'bg-warning', 'bg-info'];

                                                foreach ($row->staffMembers as $staffMember)
                                                {
                                                    if($staffMember->user->image == null)
                                                    {
                                                        $shortName = ucfirst($staffMember->user->first_name[0]).ucfirst($staffMember->user->last_name[0]);
                                                        $string .= '<figure class="avatar mr-2 mb-2 avatar-sm '.$randomColorArray[array_rand($randomColorArray)].' text-white" data-initial="'.$shortName.'" data-toggle="tooltip" title="'.$staffMember->user->name.'"></figure>';
                                                    } else {
                                                        $string .= '<img alt="image" src="'.$staffMember->user->image_url.'" class="rounded-circle" width="35" data-toggle="tooltip" title="'.$staffMember->user->name.'">';
                                                    }
                                                }
                                            } else {
                                                $string = '-';
                                            }


                                            return $string;
                                        }
                                    )
                                    ->addColumn(
                                        'progress',
                                        function ($row) {
                                            $addLeadString = '';

                                            if($this->user->ability('admin', 'campaign_view_all') || ($this->campaignDetails->created_by == $this->user->id && $this->user->can('campaign_view'))) {
                                                $addLeadString = '<a href="javascript:void(0);" onclick="addLeadModal(\''.md5($row->id).'\')"  class="btn btn-icon icon-left"><i class="fa fa-plus"></i> '. trans('module_lead.addNewLead'). '</a>';
                                            }

                                            if($row->remaining_leads === 0)
                                            {
                                                return  $addLeadString != '' ? $addLeadString : trans('module_campaign.flyCampaign');
                                            } else if($row->total_leads != null && $row->remaining_leads != null)
                                            {
                                                $percentage = intval((($row->total_leads - $row->remaining_leads)/$row->total_leads)*100);

                                                return '<div class="progress" data-height="6" data-toggle="tooltip" title="'.$percentage.'%">
                                                        <div class="progress-bar bg-success" data-width="'.$percentage.'%"></div>
                                                      </div>
                                                      <br>
                                                      '.trans('module_campaign.remainingLeads').': <strong>'.($row->total_leads - $row->remaining_leads).'/'.$row->total_leads.'</strong>
                                                      ';
                                            }else {
                                                return $addLeadString;
                                            }
                                        }
                                    );


                $rawColumns[] = 'last_active_member';

                $results = $results->addColumn(
                        'last_active_member',
                        function ($row) {

                            $lastActiveMember = $row->leads()
                                                     ->with('lastActioner')
                                                     ->orderBy('leads.updated_at', 'desc')
                                                     ->first();

                            return $lastActiveMember && $lastActiveMember->lastActioner ? $lastActiveMember->lastActioner->first_name . ' ' . $lastActiveMember->lastActioner->last_name : '-';
                        }
                    );
        }



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
                $text = '<div class="buttons">';

                    if($this->user->ability('admin', 'campaign_edit'))
                    {
                        $text .= '<a href="javascript:void(0);" onclick="editCampaignModal(\''.md5($row->id).'\')" class="btn btn-info btn-icon icon-left"
                      data-toggle="tooltip" data-original-title="'.trans('app.edit').'"><i class="fa fa-edit" aria-hidden="true"></i></a>';
                    }

                    if($row->remaining_leads == null && $row->total_leads == null && $this->user->ability('admin', 'campaign_delete'))
                    {
                        $text .= '<button onclick="deleteCampaignModal('.$row->id.')" class="btn btn-danger btn-icon icon-left"
                      data-toggle="tooltip" data-original-title="'.trans('app.delete').'"><i class="fa fa-trash" aria-hidden="true"></i></button>';
                    }

                $text .= '</div>';

                return $text;
            })
            ->rawColumns($rawColumns)
            ->make(true);

        return $results;
    }

    public function show($id)
    {
        $this->callManagerActive = 'active';
        $this->icon = 'edit';
        $this->campaignDetails = Campaign::whereRaw('md5(id) = ?', $id)->with('staffMembers:user_id,campaign_id', 'staffMembers.user:id,first_name,last_name,image')->first();
        $this->pageTitle = $this->campaignDetails->name;
        $userActiveCampaigns = $this->user->activeCampaigns('all')->pluck('id')->toArray();
        // Check if current campaign is created by logged in user
        // and not have permission to see all email templates
        if(!($this->user->ability('admin', 'campaign_view_all') || ($this->campaignDetails->created_by == $this->user->id && $this->user->can('campaign_view'))) && !in_array($this->campaignDetails->id, $userActiveCampaigns)) {
            return response()->view($this->forbiddenErrorView);
        }

        $this->formFields = FormField::where('form_id', $this->campaignDetails->form_id)
            ->oldest('order')
            ->get();

        foreach ($this->campaignDetails->staffMembers as $campaignStaffMember)
        {
            $totalLeadByUsers = Lead::where('campaign_id', $this->campaignDetails->id)
                                    ->where('last_actioned_by', $campaignStaffMember->user_id)
                                    ->count();

            $interestedLeadByUsers = Lead::where('campaign_id', $this->campaignDetails->id)
                                        ->where('last_actioned_by', $campaignStaffMember->user_id)
                                        ->where('interested', 'interested')
                                        ->count();

            $notInterestedLeadByUsers = Lead::where('campaign_id', $this->campaignDetails->id)
                ->where('last_actioned_by', $campaignStaffMember->user_id)
                ->where('interested', 'not_interested')
                ->count();

            $unreachableLeadByUsers = Lead::where('campaign_id', $this->campaignDetails->id)
                ->where('last_actioned_by', $campaignStaffMember->user_id)
                ->where('interested', 'unreachable')
                ->count();

            $campaignStaffMember->total_leads = $totalLeadByUsers;
            $campaignStaffMember->interested_leads = $interestedLeadByUsers;
            $campaignStaffMember->not_interested_leads = $notInterestedLeadByUsers;
            $campaignStaffMember->unreachable_leads = $unreachableLeadByUsers;
        }


        $topPerformer =  $this->campaignDetails->staffMembers->sortBy('total_leads', SORT_REGULAR, true)->first();
        $this->isTopper = $topPerformer && isset($topPerformer->user_id) && $topPerformer->user_id === $this->user->id ? true : false;

        $this->campaignMembers = $this->campaignDetails->staffMembers->pluck('user_id')->toArray();
        $this->teamMembers = User::all();

        $this->campaignTeamMembers = User::select('id', 'first_name', 'last_name')
                                        ->get();

        $this->yourLeadCount = Lead::where('campaign_id', $this->campaignDetails->id)->where('last_actioned_by', $this->user->id)->count();
        // Call the same create view for edit
        return view('admin.campaigns.details.show', $this->data);
    }
    public function create()
    {
        $this->campaignActive = 'active';
        $this->icon = 'plus';

        if($this->user->ability('admin', 'form_view_all')) {

            $this->formLists = Form::all();
        } else {
            $this->formLists = Form::where('forms.created_by', $this->user->id)->get();
        }

        $this->campaign = new Campaign();
        $this->campaignMembers = User::all();
        return view('admin.campaigns.create', $this->data);
    }

    public function store(StoreRequest $request)
    {

        if($request->step == 1)
        {
            return Reply::success('messages.completeNextStep', [
                'show_next' => true,
                'step' => $request->step + 1
            ]);
        }
        else if($request->step == 2 && $request->import_type != 'without_import')
        {
            $formFields = FormField::where('form_id', $request->form)->orderBy('order', 'asc')->get();
            $html = $this->getImportHtml($request, $formFields, false);

            return Reply::success('messages.completeNextStep', [
                'show_next' => true,
                'step' => $request->step + 1,
                'html' => $html
            ]);
        }
        else {
            \DB::beginTransaction();

            $campaign = new Campaign();
            $campaign->name = $request->campaign_name;

            if($request->has('auto_reference'))
            {
                $campaign->auto_reference = 1;
                $campaign->auto_reference_prefix = $request->auto_reference_prefix;
            } else {
                $campaign->auto_reference = 0;
            }

            $campaign->created_by = $this->user->id;
            $campaign->form_id = $request->form;
            $campaign->save();

            // Inserting campaign members
            $campaignMembers = [];
            foreach ($request->campaign_members as $campaignMember)
            {
                $campaignMembers[] =  [
                    'user_id' => $campaignMember,
                    'campaign_id' => $campaign->id
                ];
            }
            CampaignMember::insert($campaignMembers);

            if($request->import_type != 'without_import')
            {
                $this->saveImportedLeads($request, $campaign);
            }

            \DB::commit();
            return Reply::redirect(route('admin.campaigns.index'), 'messages.createSuccess');
        }


    }

    public function edit($id)
    {
        $this->campaignActive = 'active';
        $this->icon = 'edit';
        $this->campaignDetails = Campaign::whereRaw('md5(id) = ?', $id)->first();

        // Check if current campaign is created by logged in user
        // and not have permission to see all email templates
        if(!$this->user->ability('admin', 'campaign_view_all') && $this->campaignDetails->created_by != $this->user->id) {
            return response()->view($this->forbiddenErrorView);
        }

        $this->campaignMembers = $this->campaignDetails->staffMembers->pluck('user_id')->toArray();
        $this->teamMembers = User::all();

        // Call the same create view for edit
        return view('admin.campaigns.edit', $this->data);
    }

    public function update(UpdateRequest $request,$id)
    {

        \DB::beginTransaction();

        $campaign         = Campaign::find($id);

        // Check if current campaign is created by logged in user
        // and not have permission to see all email templates
        if(!$this->user->ability('admin', 'campaign_view_all') && $campaign->created_by != $this->user->id) {
            return Reply::error('messages.notAllowed');
        }

        $campaign->name = $request->campaign_name;
        $campaign->auto_reference = $request->has('auto_reference') ? 1 : 0;
        $campaign->auto_reference_prefix = $request->auto_reference_prefix;
        $campaign->save();

        // Deleting Previous Campaign Members
        CampaignMember::where('campaign_id', $campaign->id)->delete();

        // Inserting campaign members
        $campaignMembers = [];
        foreach ($request->campaign_members as $campaignMember)
        {
            $campaignMembers[] =  [
                'user_id' => $campaignMember,
                'campaign_id' => $campaign->id
            ];
        }
        CampaignMember::insert($campaignMembers);

        \DB::commit();
        return Reply::success('messages.updateSuccess');

    }

    public function destroy(DeleteRequest $request, $id)
    {
        $campaign = Campaign::find($id);

        if($campaign->remaining_leads != null && $campaign->total_leads != null)
        {
            return Reply::error('messages.notAllowed');
        }

        // Check if current campaign is created by logged in user
        // and not have permission to see all email templates
        if(!$this->user->ability('admin', 'campaign_view_all') && $campaign->created_by != $this->user->id) {
            return Reply::error('messages.notAllowed');
        }

        $campaign->delete();

        return Reply::success('messages.deleteSuccess');
    }

    public function exportLeadData(Request $request)
    {
        $this->pageTitle = trans('menu.exportLeads');
        $this->exportLeadActive = 'active';
        $this->pageIcon = 'fa fa-anchor';

        $this->campaignType = $request->has('type') && $request->type == 'completed' ? 'completed' : 'active';
        return view('admin.campaigns.export-leads', $this->data);
    }

    public function getExportLeadLists(Request $request)
    {

        if($this->user->ability('admin', 'campaign_view_all'))
        {
            $results = Campaign::select('campaigns.id', 'campaigns.name', 'campaigns.started_on', 'campaigns.completed_on', 'campaigns.total_leads', 'campaigns.remaining_leads');

        } else {
            $results = Campaign::select('campaigns.id', 'campaigns.name', 'campaigns.started_on', 'campaigns.total_leads', 'campaigns.remaining_leads')
                ->join('campaign_members', 'campaign_members.campaign_id', '=', 'campaigns.id')
                ->where('campaign_members.user_id', '=', $this->user->id)
                ->with('staffMembers:user_id,campaign_id', 'staffMembers.user');
        }

        if($request->campaign_type == 'completed')
        {
            $results = $results->where('campaigns.status', '=', 'completed');
        } else if($request->campaign_type == 'active') {
            $results = $results->where('campaigns.status', '=', 'started');
        }
        else {
            $results = $results->where(function ($query) {
                $query->where('campaigns.status', '!=', 'completed')
                    ->orWhereNull('campaigns.status');
            });
        }

        return datatables()->eloquent($results)
            ->editColumn(
                'started_on',
                function ($row) {

                    return $row->started_on != NULL ? $row->started_on->format($this->user->date_format) : '-';
                }
            )
            ->editColumn(
                'total_leads',
                function ($row) {

                    return $row->total_leads != NULL ? $row->total_leads : '-';
                }
            )
            ->editColumn(
                'remaining_leads',
                function ($row) {

                    return $row->remaining_leads != NULL ? $row->remaining_leads : '-';
                }
            )
            ->addColumn('export', function ($row) {
                $text = '<div class="buttons">';


                $text .= '<a href="javascript:void(0);" onclick="downloadExportLeadData('.$row->id.')" class="btn btn-info btn-icon icon-left"
                      data-toggle="tooltip" data-original-title="'.trans('app.export').'"><i class="fa fa-download" aria-hidden="true"></i> '.trans('app.export').'</a>';

                $text .= '</div>';



                return $text;
            })
            ->rawColumns(['export'])
            ->make(true);
    }

    public function downloadExportLead(Request $request)
    {
        $campaignId = $request->campaign_id;
        $userActiveCampaigns = $this->user->activeCampaigns('all')->pluck('id')->toArray();

        // If user doesn't have the permission to download the exported lead
        if(!in_array($campaignId, $userActiveCampaigns))
        {
            return response()->view($this->forbiddenErrorView);
        }

        $fields = [
            trans('app.sn'),
            trans('module_lead.referenceNumber'),
            trans('app.createdAt'),
            trans('app.updatedAt'),
            trans('module_lead.status'),
            trans('module_lead.interested'),
            trans('module_lead.appointment_booked'),
        ];

        // TODO - set these options from dropdown in index form
        $appointmentBooked = false;


        $leads = Lead::where('campaign_id', '=', $campaignId);
        $fieldsHeaderColumns = [
            'leads.id',
            'leads.reference_number',
            'leads.created_at',
            'leads.updated_at',
            'leads.status',
            'leads.interested',
            'leads.appointment_booked',
        ];

        if($appointmentBooked)
        {
            $fieldsHeaderColumns[] = 'sales_members.first_name';
            $fieldsHeaderColumns[] = 'sales_members.last_name';
            $fieldsHeaderColumns[] = 'appointments.appointment_time';

            $fields[] = trans('module_lead.salesMemberName');
            $fields[] = trans('module_lead.appointmentTime');

            $leads = $leads->join('appointments', 'appointments.lead_id', '=', 'leads.id')
                            ->join('sales_members', 'sales_members.id', '=', 'appointments.sales_member_id')
                            ->where('leads.appointment_booked', '=', 1);
        }

        $leads = $leads->get($fieldsHeaderColumns);

        $campaign = Campaign::find($campaignId);
        $fileName =  Str::slug($campaign->name, '_') . '_'.(Carbon::now())->format("Y_m_d_H_i_s").".csv";

        $formFields = LeadData::select('lead_data.field_name')
                              ->join('leads', 'leads.id', '=', 'lead_data.lead_id')
                              ->join('campaigns', 'campaigns.id', '=', 'leads.campaign_id')
                              ->where('leads.campaign_id', '=', $campaignId)
                              ->groupBy('lead_data.field_name')
                              ->get();


        foreach ($formFields as $formField)
        {
            $fields[] = $formField->field_name;
        }


        $headers = [
            'Cache-Control'       => 'must-revalidate, post-check=0, pre-check=0',
            'Content-Encoding'       => 'UTF-8',
            'Content-type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename='.$fileName.'.csv',
            'Expires'             => '0',
            'Pragma'              => 'public'
        ];

        $callback = function() use ( $fields, $leads, $formFields)
        {
            $counter = 1;
            $file = fopen('php://output', 'w');
            fputcsv($file, $fields, ",", "\"");


            foreach ($leads as $lead)
            {
                $fieldData = [];

                $fieldData[] = $counter;
                $fieldData[] = $lead->reference_number;
                $fieldData[] = $lead->created_at;
                $fieldData[] = $lead->updated_at;
                $fieldData[] = $lead->status;
                $fieldData[] = $lead->interested;
                $fieldData[] = $lead->appointment_booked ? 'Yes' : 'No';

                if($lead->appointment_booked)
                {
                    $fieldData[] = trim($lead->first_name . ' ' . $lead->last_name);
                    $fieldData[] = $lead->appointment_time;
                }

                $leadData = LeadData::where('lead_id', $lead->id)->pluck('field_value', 'field_name');

                foreach ($formFields as $formField)
                {
                    $fieldData[] = isset($leadData[$formField->field_name]) ?  $leadData[$formField->field_name] : '';
                }

                fputcsv($file, $fieldData, ",", "\"");
                $counter++;
            }

            fclose($file);
        };

        return Response::stream($callback, 200, $headers);

    }

    public function importLeads()
    {
        $this->pageTitle = trans('menu.importLeads');
        $this->importLeadActive = 'active';
        $this->pageIcon = 'fa fa-upload';

        return view('admin.campaigns.import-leads', $this->data);
    }

    public function importLeadData(ImportLeadRequest $request)
    {
        $campaignId = $request->campaign_id;
        $userActiveCampaigns = $this->user->activeCampaigns()->pluck('id')->toArray();

        // If user doesn't have the permission to import lead
        if(!in_array($campaignId, $userActiveCampaigns))
        {
            $errorMessage = Reply::error('messages.notAllowed');
            return response()->json($errorMessage);
        }

        $campaign = Campaign::find($campaignId);
        $formFields = FormField::where('form_id', $campaign->form_id)->orderBy('order', 'asc')->get();

        $html = $this->getImportHtml($request, $formFields);

        $resultData = [
            'html' => $html
        ];

        return Reply::successWithData($resultData);
    }

    private function getImportHtml($request, $formFields, $fullForm = true)
    {
        $importType = $request->import_type;

        $headerFields = [];
        $headerFieldsData = [];
        $campaignFormFields = [];


        if($importType == 'text')
        {
            $text = trim($request->import_text);
            $textArray = explode("\n", $text);
            $textArray = array_filter($textArray, 'trim'); // remove any extra \r characters left behind
            $row = 0;

            foreach ($textArray as $line) {
                $lineArrayResults = str_getcsv($line);

                if($row == 0)
                {
                    foreach ($lineArrayResults as $lineArrayResultKey => $lineArrayResult)
                    {
                        $headerFields[$lineArrayResultKey] = $lineArrayResult;
                    }
                } else {
                    foreach ($lineArrayResults as $lineArrayResultKey => $lineArrayResult)
                    {
                        $headerFieldsData[$lineArrayResultKey][] = $lineArrayResult;
                    }
                }

                if($row == 5)
                    break;

                $row++;
            }
        }else if($importType == 'file')
        {
            $file = fopen($request->import_file, 'r');
            $row = 0;

            while(($lineArrayResults = fgetcsv($file)) !== FALSE)
            {

                if($row == 0)
                {
                    foreach ($lineArrayResults as $lineArrayResultKey => $lineArrayResult)
                    {
                        $headerFields[$lineArrayResultKey] = $lineArrayResult;
                    }
                } else
                {
                    foreach ($lineArrayResults as $lineArrayResultKey => $lineArrayResult)
                    {
                        $headerFieldsData[$lineArrayResultKey][] = $lineArrayResult;
                    }

                }

                if($row == 5)
                    break;

                $row++;
            }

            // TODO stop after 5 rows

            fclose($file);

        }

        foreach($formFields as $formField)
        {
            $campaignFormFields[] = [
                'id'    => $formField->id,
                'name'  => $formField->field_name
            ];
            $campaignFormDetailsByID[$formField->id] = $formField->field_name;
        }

        $csvMatchedColumns = array_fill(0, count($headerFields), false);
        $csvMatchedColumnsDetail = array_fill(0, count($headerFields), -1);
        $formMatchedColumns = [];
        $matchCount = 0;

        foreach ($headerFields as $fieldKey => $fieldValue)
        {
            $fieldValueModified = strtolower(str_replace(array(' ', '_'), '', trim($fieldValue)));

            foreach ($formFields as $formField)
            {
                $formFieldModified = strtolower(str_replace(array(' ', '_'), '', trim($formField->field_name)));

                if($fieldValueModified == $formFieldModified)
                {
                    $csvMatchedColumns[$fieldKey] = true;
                    $csvMatchedColumnsDetail[$fieldKey] = $formField->id;
                    $formMatchedColumns[$formField->id] = 1;
                    $matchCount++;
                    break;
                }
            }
        }

        $this->headerFields = $headerFields;
        $this->headerFieldsData = $headerFieldsData;
        $this->csvMatchedColumns = $csvMatchedColumns;
        $this->unMatchCsvColumnCount   = count($formFields) - $matchCount;
        $this->csvMatchedColumnsDetail = $csvMatchedColumnsDetail;
        $this->campaignFormFields = $campaignFormFields;
        $this->campaignFormDetailsByID = $campaignFormDetailsByID;
        $this->formMatchedColumns = $formMatchedColumns;

        if($fullForm)
        {
            $view = 'admin.campaigns.import-leads-data';
        } else {
            $view = 'admin.campaigns.import-leads-data-content';
        }

        $html = view($view, $this->data)->render();

        return $html;
    }

    public function getDataFieldsWithDataUpload()
    {
        $results = [];

        $results['headerFields'] = '';


        return $results;
    }

    public function saveLeadData(Request $request)
    {
        $campaign = Campaign::find($request->campaign_id);

        $this->saveImportedLeads($request, $campaign);

        return Reply::redirect( route('admin.campaigns.index'), 'messages.importedSuccessfully');
    }

    private function saveImportedLeads($request, $campaign)
    {
        $submittedFormFields = json_decode($request->lead_form_fields);
        $importType = $request->import_type;
        $formFields = FormField::where('form_id', $campaign->form_id)->orderBy('order', 'asc')->pluck('field_name', 'id');
        $leadFormFields = [];

        foreach ($submittedFormFields as $submittedFormFieldsKey => $submittedFormFieldValue)
        {
            if($submittedFormFieldValue > 0)
            {
                $leadFormFields[] = $formFields[$submittedFormFieldValue];
            } else {
                $leadFormFields[] = $submittedFormFieldValue;
            }
        }

        if($importType == 'text')
        {
            $text = trim($request->import_text);
            $textArray = explode("\n", $text);
            $textArray = array_filter($textArray, 'trim'); // remove any extra \r characters left behind
            $row = 0;

            foreach ($textArray as $line) {
                $lineArrayResults = str_getcsv($line);

                if($row > 0)
                {
                    $this->checkAndSaveLead($lineArrayResults, $campaign, $submittedFormFields, $formFields);
                }

                $row++;
            }
        }else if($importType == 'file')
        {
            $file = fopen($request->import_file, 'r');
            $row = 0;

            while(($lineArrayResults = fgetcsv($file)) !== FALSE)
            {
                // Skip first row
                if($row > 0)
                {
                    $this->checkAndSaveLead($lineArrayResults, $campaign, $submittedFormFields, $formFields);
                }

                $row++;
            }

            fclose($file);

        }

        // Saving Total Lead For Campaign
        $totalCampaignLeads = Lead::where('campaign_id', $campaign->id)->count();
        $campaign->total_leads =$totalCampaignLeads;

        // Saving remaining lead for campaign
        $actionedLeads = Lead::where('campaign_id', $campaign->id)
            ->where('status', 'actioned')->count();
        $remainingLeads = $totalCampaignLeads - $actionedLeads;
        $campaign->total_leads = $totalCampaignLeads;
        $campaign->remaining_leads = $remainingLeads;
        $campaign->save();
    }

    public function checkAndSaveLead($lineArrayResults, $campaign, $submittedFormFields, $formFields)
    {
        // Creating array from csv/txt data
        $newLeadDataArray = [];
        foreach ($formFields as $formFieldKey => $formFieldValue)
        {
            $newLeadDataArray[$formFieldKey] = [
                'field_name' => $formFieldValue,
                'field_value' => ''
            ];
        }

        foreach ($lineArrayResults as $lineArrayResultKey => $lineArrayResult)
        {
            $currentFormFieldId = $submittedFormFields[$lineArrayResultKey];
            if($currentFormFieldId > 0)
            {
                $newLeadDataArray[$currentFormFieldId] = [
                    'field_name' => $formFields[$currentFormFieldId],
                    'field_value' => $lineArrayResult
                ];
            }
        }

        // Generating field string
        $fieldValueString = '';
        foreach ($formFields as $formFieldKey => $formFieldValue)
        {
            if($newLeadDataArray[$formFieldKey]['field_value'] != '')
            {
                $fieldValueString .= strtolower($newLeadDataArray[$formFieldKey]['field_value']);
            }
        }

        // Checking created hash exists in database
        $fieldValueStringHash = md5($fieldValueString.$campaign->id);
        $hashCheck = Lead::where('hash', $fieldValueStringHash)->count();
        if($hashCheck == 0)
        {
            // Creating Lead
            $lead = new Lead();
            $lead->reference_number = $campaign->auto_reference == 1 ? $campaign->auto_reference_prefix . '_' . Carbon::now()->timestamp : null;
            $lead->campaign_id = $campaign->id;
            $lead->hash = $fieldValueStringHash;
            $lead->save();

            // Saving Lead Data
            foreach ($formFields as $formFieldKey => $formFieldValue)
            {
//                if($newLeadDataArray[$formFieldKey]['field_value'] != '')
//                {
                    $leadData = new LeadData();
                    $leadData->lead_id = $lead->id;
                    $leadData->form_field_id = $formFieldKey;
                    $leadData->field_name = $formFieldValue;
                    $leadData->field_value = $newLeadDataArray[$formFieldKey]['field_value'];
                    $leadData->save();
//                }
            }

        }
    }

    public function createLead($campaignId)
    {
        $this->campaignActive = 'active';
        $this->icon = 'plus';

        $this->campaignDetails = Campaign::whereRaw('md5(id) = ?', $campaignId)->first();
        $this->pageTitle = trans('module_lead.addNewLead');

        // Only admin and user having to permission to view all campaign can create a new lead
        if(!($this->user->ability('admin', 'campaign_view_all') || ($this->campaignDetails->created_by == $this->user->id && $this->user->can('campaign_view')))) {
            return Reply::error('messages.notAllowed');
        }

        $this->formFields = FormField::where('form_id', $this->campaignDetails->form_id)
            ->oldest('order')
            ->get();

        return view('admin.campaigns.details.create', $this->data);
    }

    public function storeLead(Request $request, $campaignId)
    {
        $campaign = Campaign::whereRaw('md5(id) = ?', $campaignId)->first();
        $formFields = FormField::where('form_id', $campaign->form_id)->orderBy('order', 'asc')->pluck('field_name', 'id');

        // Only admin and user having to permission to view all campaign can create a new lead
        if(!($this->user->ability('admin', 'campaign_view_all') || ($this->campaignDetails->created_by == $this->user->id && $this->user->can('campaign_view')))) {
            return Reply::error('messages.notAllowed');
        }

        // Creating array from csv/txt data
        $newLeadDataArray = [];
        foreach ($formFields as $formFieldKey => $formFieldValue)
        {
            $newLeadDataArray[$formFieldKey] = [
                'field_name' => $formFieldValue,
                'field_value' => ''
            ];
        }

        foreach ($request->fields as $lineArrayResultKey => $lineArrayResult)
        {
            $newLeadDataArray[$lineArrayResultKey] = [
                'field_name' => $formFields[$lineArrayResultKey],
                'field_value' => $lineArrayResult
            ];
        }

        // Generating field string
        $fieldValueString = '';
        foreach ($formFields as $formFieldKey => $formFieldValue)
        {
            if($newLeadDataArray[$formFieldKey]['field_value'] != '')
            {
                $fieldValueString .= strtolower($newLeadDataArray[$formFieldKey]['field_value']);
            }
        }

        // Checking created hash exists in database
        $fieldValueStringHash = md5($fieldValueString.$campaign->id);
        $hashCheck = Lead::where('hash', $fieldValueStringHash)->count();
        if($hashCheck == 0)
        {
            \DB::beginTransaction();

            // Creating Lead
            $lead = new Lead();
            $lead->reference_number = $campaign->auto_reference == 1 ? $campaign->auto_reference_prefix . '_' . Carbon::now()->timestamp : null;
            $lead->campaign_id = $campaign->id;
            $lead->hash = $fieldValueStringHash;
            $lead->save();

            // Saving Lead Data
            foreach ($formFields as $formFieldKey => $formFieldValue)
            {
                $leadData = new LeadData();
                $leadData->lead_id = $lead->id;
                $leadData->form_field_id = $formFieldKey;
                $leadData->field_name = $formFieldValue;
                $leadData->field_value = $newLeadDataArray[$formFieldKey]['field_value'] ?? '';
                $leadData->save();
            }

            // Saving Total Lead For Campaign
            $totalCampaignLeads = Lead::where('campaign_id', $campaign->id)->count();
            $campaign->total_leads =$totalCampaignLeads;

            // Saving remaining lead for campaign
            $actionedLeads = Lead::where('campaign_id', $campaign->id)
                ->where('status', 'actioned')->count();
            $remainingLeads = $totalCampaignLeads - $actionedLeads;
            $campaign->total_leads = $totalCampaignLeads;
            $campaign->remaining_leads = $remainingLeads;
            $campaign->save();

            \DB::commit();

            return Reply::success('messages.createSuccess');
        } else {
            return Reply::error('module_lead.duplicateLeadWithData');
        }
    }
}
