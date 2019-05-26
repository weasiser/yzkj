<?php

namespace App\Http\Requests\Api;

use Auth;

class LoginAuthorizationRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $userId = Auth::guard('api')->id();
        return [
            'user_info' => 'required|array',
        ];
    }
}
