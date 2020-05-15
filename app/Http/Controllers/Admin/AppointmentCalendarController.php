<?php

namespace App\Http\Controllers\Admin;

use App\Classes\Reply;
use App\Http\Requests\Admin\Appointment\StoreRequest;
use App\Http\Requests\Admin\Appointment\UpdateRequest;
use App\Models\Appointment;
use App\Models\Campaign;
use App\Models\Lead;
use App\Models\SalesMember;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AppointmentCalendarController extends AdminBaseController
{
     /**
	 * UserController constructor.
	 */

    public function __construct()
    {
        parent::__construct();

        $this->pageTitle = trans('menu.appointmentCalendar');
        $this->pageIcon = 'fa fa-calendar-alt';
        $this->appointmentMenuActive = 'active';
        $this->bootstrapModalRight = false;
    }

    public function index(Request $request)
    {
        $this->appointmentCalendarActive = 'active';
        $this->allSalesMembers = SalesMember::all();

        return view('admin.appointment-calendar.index', $this->data);
    }

    public function getAppointments(Request $request)
    {
        $campaignId = $request->campaign_id;
        $salesmanId = $request->salesman_id;
        $startDate = $request->start;
        $endDate = $request->end;
        $userActiveCampaigns = $this->user->activeCampaigns()->pluck('id')->toArray();

        // current user is not member of  selected campaign
        if($campaignId != '' && !in_array($campaignId, $userActiveCampaigns))
        {
            return [];
        }

        $appointments = Appointment::select('sales_members.first_name', 'sales_members.last_name', 'appointments.appointment_time', 'appointments.sales_member_id', 'appointments.id', 'appointments.lead_id')
                                    ->join('leads', 'leads.id', '=', 'appointments.lead_id')
                                    ->join('sales_members', 'sales_members.id', '=', 'appointments.sales_member_id')
                                    ->whereBetween(DB::raw('DATE(appointments.appointment_time)'), [$startDate, $endDate]);

        // Only Admin can see all booked appointments
        if(!$this->user->hasRole('admin'))
        {
            $appointments = $appointments->where('created_by', $this->user->id);
        }

        if($salesmanId != '')
        {
            $appointments = $appointments->where('appointments.sales_member_id', $salesmanId);
        }

        if($campaignId == '')
        {
            $appointments = $appointments->whereIn('leads.campaign_id', $userActiveCampaigns);
        } else {
            $appointments = $appointments->where('leads.campaign_id', $campaignId);
        }

        $appointments = $appointments->orderBy('appointments.appointment_time', 'asc')->get();

        $appointmentData = [];

        foreach ($appointments as $appointment)
        {
            $appointmentData[] = [
                'title' => $appointment->first_name . ' ' . $appointment->last_name,
                'start' => $appointment->appointment_time,
                'backgroundColor'=> "#007bff",
                'borderColor'=> "#007bff",
                'textColor'=> '#fff',
                'lead_id' => $appointment->lead_id,
                'appointment_id' => $appointment->id
            ];
        }

        return $appointmentData;
    }

    public function edit($id)
    {
        $this->icon = 'edit';
        $this->appointment = Appointment::findOrFail($id);
        $this->lead = $this->appointment->lead;

        // Check current logged in user is member of appointment campaign
        $userActiveCampaigns = $this->user->activeCampaigns()->pluck('id')->toArray();
        if(!in_array($this->appointment->lead->campaign_id, $userActiveCampaigns))
        {
            return response()->view($this->forbiddenErrorView);
        }

        $this->allSalesMembers = SalesMember::all();

        return view('admin.appointment-calendar.edit', $this->data);
    }

    public function update(UpdateRequest $request,$id)
    {

        $appointment         = Appointment::findOrFail($id);

        // Check current logged in user is member of appointment campaign
        $userActiveCampaigns = $this->user->activeCampaigns()->pluck('id')->toArray();
        if(!in_array($appointment->lead->campaign_id, $userActiveCampaigns))
        {
            $errorMessage = Reply::error('messages.notAllowed');

            return response()->json($errorMessage);
        }

        \DB::beginTransaction();

        $appointment->appointment_time = Carbon::createFromFormat('Y-m-d H:i', $request->appointment_time);
        $appointment->sales_member_id = $request->sales_member_id;
        $appointment->save();

        \DB::commit();

        $data = [
            'html' => '<input type="hidden" id="delete_appointment_id" name="delete_appointment_id" value="'.$appointment->id.'"><a href="javascript:;" onclick="appointmentChanged()">'.trans('app.view').'</a>'
        ];

        return Reply::successWithData($data);

    }

    public function destroy($id)
    {
        $appointment         = Appointment::findOrFail($id);

        // Check current logged in user is member of appointment campaign
        $userActiveCampaigns = $this->user->activeCampaigns()->pluck('id')->toArray();
        if(!in_array($appointment->lead->campaign_id, $userActiveCampaigns))
        {
            $errorMessage = Reply::error('messages.notAllowed');

            return response()->json($errorMessage);
        }

        $appointment->delete();

        return Reply::success('messages.deleteSuccess');
    }

    public function addEditByLead($leadId)
    {
        $this->icon = 'edit';
        $lead = Lead::whereRaw('md5(id) = ?', $leadId)->first();
        $this->appointment = $lead->appointment ? $lead->appointment : new Appointment();
        $this->lead = $lead;
        // Check current logged in user is member of appointment campaign
        $userActiveCampaigns = $this->user->activeCampaigns()->pluck('id')->toArray();
        if(!in_array($lead->campaign_id, $userActiveCampaigns))
        {
            return response()->view($this->forbiddenErrorView);
        }

        $this->allSalesMembers = SalesMember::all();

        return view('admin.appointment-calendar.add-edit', $this->data);
    }

    public function store(StoreRequest $request)
    {
        \DB::beginTransaction();

        $appointment         = new Appointment();
        $appointment->lead_id = $request->lead_id;
        $appointment->appointment_time = Carbon::createFromFormat('Y-m-d H:i', $request->appointment_time);
        $appointment->sales_member_id = $request->sales_member_id;
        $appointment->created_by = $this->user->id;
        $appointment->save();

        $lead = Lead::find($request->lead_id);
        $lead->appointment_booked = true;
        $lead->save();

        \DB::commit();

        $data = [
            'html' => '<input type="hidden" id="delete_appointment_id" name="delete_appointment_id" value="'.$appointment->id.'"><a href="javascript:;" onclick="appointmentChanged()">'.trans('app.view').'</a>'
        ];

        return Reply::successWithData($data);

    }
}
