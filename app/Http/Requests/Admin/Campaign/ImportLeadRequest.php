<?php

namespace App\Http\Requests\Admin\Campaign;

use App\Http\Requests\AdminCoreRequest;

class ImportLeadRequest extends AdminCoreRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */

    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {


            $rules = [
                'campaign_id'    => 'required'
            ];

            if ($this->import_type == 'text')
            {
                $rules['import_text'] = 'required';
            }

            if ($this->import_type == 'file')
            {
                $rules['import_file'] = 'required';
            }
        return $rules;

    }

    public function messages()
    {
        return [
            'campaign_id.required' => 'Please select a campaign'
        ];
    }
}
