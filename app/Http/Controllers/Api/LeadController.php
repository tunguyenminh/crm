<?php

namespace App\Http\Controllers\Api;

use App\Classes\Reply;
use App\Http\Controllers\Controller as Controller;
use App\Models\Lead;
use App\Models\LeadData;
use Illuminate\Http\Request;

class LeadController extends Controller
{
    public function saveLeadData(Request $request)
    {
        $lead = Lead::whereRaw('id', $request->id)->first();
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

        return Reply::success('messages.updateSuccess');
    }
}
