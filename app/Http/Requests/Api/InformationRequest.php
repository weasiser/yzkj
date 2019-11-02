<?php

namespace App\Http\Requests\Api;

class InformationRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'contact' => 'string',
            'contact_info' => 'string',
            'location' => 'string',
            'summary' => 'string',
        ];
    }
}
