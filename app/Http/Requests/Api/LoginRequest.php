<?php

namespace App\Http\Requests\Api;

class LoginRequest extends FormRequest
{
    public function rules()
    {
        return [
            'phone' => [
                'required',
                'regex:' . config('app.phone_regex')
            ],
            'password' => 'required|string|min:6',
        ];
    }

    public function attributes()
    {
        return [
            'phone' => '手机号码',
            'password' => '密码',
        ];
    }
}
