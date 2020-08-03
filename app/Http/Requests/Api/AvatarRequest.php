<?php

namespace App\Http\Requests\Api;

class AvatarRequest extends FormRequest
{
    public function rules()
    {
        return [
            'avatar' => 'mimes:jpeg,jpg,png,gif|dimensions:min_width=100,min_height=100',
        ];
    }

    public function attributes()
    {
        return [
            'avatar' => '头像',
        ];
    }

    public function messages()
    {
        return [
            'avatar.dimensions' => '头像的清晰度不够，宽和高需要 100px 以上',
        ];
    }
}
