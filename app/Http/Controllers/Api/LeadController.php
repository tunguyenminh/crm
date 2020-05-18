<?php

namespace App\Http\Controllers;

use App\Classes\Common;
use App\Classes\Reply;
use App\Http\Controllers\Controller as Controller;
use App\Models\Campaign;
use App\Models\FormField;
use App\Models\FormFieldName;
use App\Models\Lead;
use App\Models\LeadData;
use App\Models\Setting;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Froiden\Envato\Traits\AppBoot;

class LeadController extends Controller
{
    public function saveLeadData(Request $request)
    {
        $campaign = Campaign::find($request->campaign_id);
        $this->saveImportedLeads($request, $campaign);
        return 'success';
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
}
