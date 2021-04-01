<?php

namespace App\Http\Requests\Api;

class RefundOrderFeedbackRequest extends FormRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'content' => 'string|required|min:2|max:100'
        ];
    }
}
