<?php

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Models\Campaign;
use App\Models\Lead;

class CampaignTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        DB::beginTransaction();

        DB::table('campaigns')->delete();
        DB::table('leads')->delete();
        DB::table('lead_data')->delete();

        DB::statement('ALTER TABLE campaigns AUTO_INCREMENT = 1');
        DB::statement('ALTER TABLE leads AUTO_INCREMENT = 1');
        DB::statement('ALTER TABLE lead_data AUTO_INCREMENT = 1');

        factory(Campaign::class, 7)
            ->create()->each(function ($campaign) {
                $assignedMembers = $this->assignMembers($campaign);

                if($campaign->status == 'completed' || $campaign->status == 'started')
                {
                    $completedLeads = $campaign->total_leads - $campaign->remaining_leads;

                    if($completedLeads > 0)
                    {
                        $faker = Faker\Factory::create();

                        factory(Lead::class, $completedLeads)->create([
                            'campaign_id' => $campaign->id,
                            'status' => 'actioned'
                        ])->each(function ($lead) use($campaign, $faker, $assignedMembers) {

                            // Call actioned by
                            if($lead->status == 'actioned')
                            {
                                $leadActionUser = \App\Models\CampaignMember::where('campaign_id', $campaign->id)
                                    ->inRandomOrder()
                                    ->first();
                                $lead->first_actioned_by = $leadActionUser->user_id;
                                $lead->last_actioned_by = $leadActionUser->user_id;
                                $lead->created_at = $faker->dateTimeBetween($campaign->started_on, 'now');
                                $lead->updated_at = $faker->dateTimeBetween($lead->created_at, 'now');

                                $callLog = new \App\Models\CallLog();
                                $callLog->lead_id = $lead->id;
                                $callLog->attempted_by = $leadActionUser->user_id;
                                $callLog->started_on = 0;
                                $callLog->time_taken = $lead->time_taken;
                                $callLog->created_at = $faker->dateTimeBetween($campaign->started_on, 'now');
                                $callLog->updated_at = $faker->dateTimeBetween($lead->created_at, 'now');
                                $callLog->save();
                            }

                            $lead->reference_number = $campaign->auto_reference == 1 ? $campaign->auto_reference_prefix .'_'.rand(10000, 9999999) : 'MEP_'.rand(10000, 9999999);
                            $lead->save();

                            if($campaign->status == 'started')
                            {
                                $pendingCallBackRandomNumber = rand(1, 4);
                                if($pendingCallBackRandomNumber == 1)
                                {
                                    $callBack = new \App\Models\Callback();
                                    $callBack->lead_id = $lead->id;
                                    $callBack->callback_time = $faker->dateTimeBetween($lead->updated_at, '+5 days');
                                    $callBack->attempted_by = $faker->randomElement($assignedMembers);
                                    $callBack->save();
                                }
                            }

                            // Saving Form Data
                            $formFields = \App\Models\FormField::where('form_id', $campaign->form_id)->orderBy('order', 'asc')->get();
                            foreach ($formFields as $formField)
                            {
                                $newLeadData = new \App\Models\LeadData();
                                $newLeadData->lead_id = $lead->id;
                                $newLeadData->form_field_id = $formField->id;
                                $newLeadData->field_name = $formField->field_name;
                                $newLeadData->field_value = $this->getFieldValue($formField->field_name, $faker);
                                $newLeadData->save();
                            }

                            // Sales Members
                            if($lead->appointment_booked == 1)
                            {
                                $randomSalesMember = \App\Models\SalesMember::inRandomOrder()->first();
                                $createdBy = \App\Models\CampaignMember::where('campaign_id', $lead->campaign_id)->inRandomOrder()->first();

                                $appointment = new \App\Models\Appointment();
                                $appointment->appointment_time =$faker->dateTimeBetween('+2 days', '+10 days');
                                $appointment->lead_id =$lead->id;
                                $appointment->sales_member_id =$randomSalesMember->id;
                                $appointment->created_by =$createdBy->user_id;
                                $appointment->save();
                            }
                        });
                    }

                    if($campaign->remaining_leads > 0)
                    {
                        $faker = Faker\Factory::create();

                        factory(Lead::class, $campaign->remaining_leads)->create([
                            'campaign_id' => $campaign->id,
                            'status' => 'unactioned'
                        ])->each(function ($lead) use($campaign, $faker) {
//                            $lead->reference_number = $campaign->auto_reference == 0 ? $campaign->auto_reference_prefix .'_'.rand(10000, 9999999) : 'MEP_'.rand(10000, 9999999);
                            $lead->save();

                            // Saving Form Data
                            $formFields = \App\Models\FormField::where('form_id', $campaign->form_id)->orderBy('order', 'asc')->get();
                            foreach ($formFields as $formField)
                            {
                                $newLeadData = new \App\Models\LeadData();
                                $newLeadData->lead_id = $lead->id;
                                $newLeadData->form_field_id = $formField->id;
                                $newLeadData->field_name = $formField->field_name;
                                $newLeadData->field_value = $this->getFieldValue($formField->field_name, $faker);
                                $newLeadData->save();
                            }
                        });
                    }

                }

            });

        DB::commit();
    }

    public function assignMembers($campaign)
    {
        $randomMembers = \App\Models\User::select('users.id')
                                    ->leftJoin('role_user', 'role_user.user_id', '=', 'users.id')
                                    ->leftJoin('roles', 'roles.id', '=', 'role_user.role_id')
                                    ->where(function($query) {
                                        $query->where('roles.name', 'member')
                                              ->orWhere('roles.name', 'manager')
                                              ->orWhere('roles.name', 'admin');
                                    })
                                    ->inRandomOrder()->take(3)->get();

        $mainUsers = [];

        foreach ($randomMembers as $randomMember)
        {
            $campaignMember = new \App\Models\CampaignMember();
            $campaignMember->user_id = $randomMember->id;
            $campaignMember->campaign_id = $campaign->id;
            $campaignMember->save();

            $mainUsers[] = $randomMember->id;
        }

        $randomOtherMembers = \App\Models\User::select('users.id')
            ->whereNotIn('id', $mainUsers)
            ->inRandomOrder()->take(rand(1, 2))->get();

        foreach ($randomOtherMembers as $randomMember)
        {
            $campaignMember = new \App\Models\CampaignMember();
            $campaignMember->user_id = $randomMember->id;
            $campaignMember->campaign_id = $campaign->id;
            $campaignMember->save();

            $mainUsers[] = $randomMember->id;
        }

        return $mainUsers;
    }

    public function getFieldValue($fieldName, $faker)
    {
        $value = '';
        $currencySymbol = [
            '€',
            '£',
            '$',
            '¥',
            '₹',
        ];
        $currency = $faker->randomElement($currencySymbol);

        if($fieldName == 'First Name')
        {
            $value = $faker->firstName();
        } else if($fieldName == 'Last Name')
        {
            $value = $faker->lastName;
        } else if($fieldName == 'Name')
        {
            $value = $faker->firstName() . ' ' . $faker->lastName;
        }  else if($fieldName == 'Company' || $fieldName == 'Company Name')
        {
            $value = $faker->company;
        } else if($fieldName == 'Email')
        {
            $value = $faker->safeEmail;
        } else if($fieldName == 'Website')
        {
            $value = $faker->domainName;
        } else if($fieldName == 'Phone No.' || $fieldName == 'Contact No.' || $fieldName == 'Mobile')
        {
            $value = $faker->e164PhoneNumber;
        } else if($fieldName == 'Alternative Number')
        {
            $value = $faker->tollFreePhoneNumber;
        } else if($fieldName == 'Notes')
        {
            $value = $faker->realText($faker->numberBetween(50,80));
        } else if($fieldName == 'Software Name')
        {
            $softwareNames = [
                'ERP Software',
                'NetSuite',
                'CRM Software',
                'Lead Management Software',
                'School Management Software',
                'Online Photo Edit Software',
            ];

            $value = $faker->randomElement($softwareNames);
        } else if($fieldName == 'Budget')
        {


            $value = $currency. ''.$faker->numberBetween(1000, 5000);
        } else if($fieldName == 'Duration')
        {
            $value = $faker->numberBetween(1, 10) . ' Months';
        } else if($fieldName == 'Salary')
        {
            $value = $value = $currency. ''.$faker->numberBetween(10000, 50000);
        } else if($fieldName == 'Gender')
        {
            $gender = [
                'Male',
                'Female'
            ];

            $value = $faker->randomElement($gender);
        } else if($fieldName == 'DOB')
        {
            $value = $faker->dateTimeBetween('-50 years', '-20 years');
        } else if($fieldName == 'Married')
        {
            $married = [
                'Yes',
                'No'
            ];

            $value = $faker->randomElement($married);
        } else if($fieldName == 'Type of Insurance')
        {
            $insurance = [
                'Property Insurance',
                'Life Insurance',
                'Home Loan Insurance',
                'Car Insurance',
                'Bike Insurance',
                'Term Plan Insurance'
            ];

            $value = $faker->randomElement($insurance);
        }

        return $value;
    }
}
