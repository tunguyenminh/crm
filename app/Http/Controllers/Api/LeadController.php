<?php

namespace App\Http\Controllers\Api;

use App\Classes\Reply;
use App\Http\Controllers\Controller as Controller;
use App\Models\Campaign;
use App\Models\FormField;
use App\Models\Lead;
use App\Models\LeadData;
use Carbon\Carbon;
use Illuminate\Http\Request;

class LeadController extends Controller
{
    public function storeLead(Request $request)
    {
        $campaign = Campaign::whereRaw('id', $request->id)->first();
        $formFields = FormField::where('form_id', $campaign->form_id)->orderBy('order', 'asc')->pluck('field_name', 'id');

        // Creating array from csv/txt data
        $newLeadDataArray = [];
        foreach ($formFields as $formFieldKey => $formFieldValue)
        {
            $newLeadDataArray[$formFieldKey] = [
                'field_name' => $formFieldValue,
                'field_value' => ''
            ];
        }

        foreach ($request->field as $lineArrayResultKey => $lineArrayResult)
        {
            if ($lineArrayResultKey == 0) continue;
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
