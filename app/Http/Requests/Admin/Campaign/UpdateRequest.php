<?php

namespace App\Http\Requests\Admin\Campaign;

use App\Http\Requests\AdminCoreRequest;

class UpdateRequest extends AdminCoreRequest
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
            'campaign_name'    => 'required'
        ];

        if(!$this->has('campaign_members'))
        {
            $rules['campaign_member'] = 'required';
        }

        if($this->has('auto_reference'))
        {
            $rules['auto_reference_prefix'] = 'required';
        }

        return $rules;

    }

}
