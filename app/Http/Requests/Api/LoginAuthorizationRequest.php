<?php

namespace App\Http\Requests\Api;

class LoginAuthorizationRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'user_info' => 'array',
        ];
    }
}
