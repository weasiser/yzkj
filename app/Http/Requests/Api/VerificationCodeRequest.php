<?php

namespace App\Http\Requests\Api;

class VerificationCodeRequest extends FormRequest
{
    public function rules()
    {
        return [
            'phone' => [
                'required',
                'regex:' . config('app.phone_regex'),
                'unique:users'
            ]
        ];
    }

    public function attributes()
    {
        return [
            'phone' => '手机号码',
        ];
    }
}
