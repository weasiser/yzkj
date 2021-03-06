<?php

namespace App\Http\Requests\Admin;

use App\Http\Requests\Request;

class RefundRequest extends Request
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
//    public function authorize()
//    {
//        return false;
//    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'refundAmount'  => 'required|integer|min:1|max:23',
            'moreOptionsForRefund' => 'array'
        ];
    }
}
