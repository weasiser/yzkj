<?php

namespace App\Http\Requests\Api;

use Illuminate\Validation\Rule;

class UserRequest extends FormRequest
{
    public function rules()
    {
        switch ($this->method()) {
            case 'POST':
                return [
                    'password' => 'required|string|min:6',
                    'verification_key' => 'required|string',
                    'verification_code' => 'required|string',
                ];
                break;
            case 'PATCH':
                $userId = \Auth::guard('api')->id();

                return [
                    'nick_name' => [
                        'between:3,16',
                        'regex:/^[a-zA-Z0-9\u4e00-\u9fa5]+$/',
                        Rule::unique('users')->ignore($userId),
                    ],
                ];
                break;
        }
    }

    public function attributes()
    {
        return [
            'verification_key' => '短信验证码 key',
            'verification_code' => '短信验证码',
            'nick_name' => '昵称'
        ];
    }

    public function messages()
    {
        return [
            'nick_name.regex' => '昵称只支持中英文和数字',
        ];
    }
}
