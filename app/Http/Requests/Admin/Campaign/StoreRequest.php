<?php

namespace App\Http\Requests\Admin\Campaign;

use App\Http\Requests\AdminCoreRequest;

class StoreRequest extends AdminCoreRequest
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

        if($this->step == 1)
        {
            $rules = [
                'campaign_name'    => 'required',
                'form'    => 'required',
            ];

            if(!$this->has('campaign_members'))
            {
                $rules['campaign_member'] = 'required';
            }

            if($this->has('auto_reference'))
            {
                $rules['auto_reference_prefix'] = 'required';
            }
        } else if($this->step == 2)
        {
            $rules = [
                'import_type'    => 'required'
            ];

            if ($this->import_type == 'text')
            {
                $rules['import_text'] = 'required';
            }

            if ($this->import_type == 'file')
            {
                $rules['import_file'] = 'required';
            }
        } else {
            $rules = [];
        }



        return $rules;

    }

    public function messages()
    {
        return [
            'campaign_member.required' => 'Please select at least one member to campaign'
        ];
    }
}
