<?php

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Models\Form;
use App\Models\FormField;

class FormTableSeeder extends Seeder
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

        DB::table('forms')->delete();
        DB::table('form_fields')->delete();

        DB::statement('ALTER TABLE forms AUTO_INCREMENT = 1');
        DB::statement('ALTER TABLE form_fields AUTO_INCREMENT = 1');

        $formNames = [
            0 => [
                'name' => 'Default Form',
                'fields' => [
                    'First Name', 'Last Name', 'Company', 'Email', 'Website', 'Phone No.', 'Notes'
                ]
            ],
            1 => [
                'name' => 'Software Development Form',
                'fields' => [
                    'First Name', 'Last Name', 'Company Name', 'Email', 'Contact No.', 'Software Name', 'Budget', 'Duration', 'Notes'
                ]
            ],
            2 => [
                'name' => 'Insurance Enquiry Form',
                'fields' => [
                    'Name', 'Mobile', 'Alternative Number', 'Salary', 'Gender', 'DOB', 'Married', 'Type of Insurance', 'Notes'
                ]
            ]
        ];

        foreach ($formNames as $formName)
        {
            $form = factory(Form::class)->create([
                'form_name' => $formName['name']
            ]);

            $orderNo = 1;
            foreach ($formName['fields'] as $formFieldName)
            {
                $formField = factory(FormField::class)->create([
                    'field_name' => $formFieldName,
                    'form_id' => $form->id,
                    'order' => $orderNo
                ]);

                $orderNo++;
            }
        }

        DB::commit();
    }
}
