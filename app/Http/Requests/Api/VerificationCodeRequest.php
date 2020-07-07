<?php

namespace App\Http\Requests\Api;

class VerificationCodeRequest extends FormRequest
{
    public function rules()
    {
        return [
            'phone' => [
                'required',
                'regex:/^(?:\+?86)?1(?:3\d{3}|5[^4\D]\d{2}|8\d{3}|7(?:[235-8]\d{2}|4(?:0\d|1[0-2]|9\d))|9[0-35-9]\d{2}|66\d{2})\d{6}$/',
                'unique:users'
            ]
        ];
    }
}
