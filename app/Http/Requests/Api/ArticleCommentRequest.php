<?php

namespace App\Http\Requests\Api;

class ArticleCommentRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'content' => 'required|min:1',
        ];
    }
}
