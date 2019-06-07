<?php

namespace App\Http\Requests\Api;

use App\Models\VendingMachineAisle;

class OrderRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'vending_machine_aisle_id' => [
                'required',
                'integer',
            ],
            'amount' => [
                'required',
                'integer',
                'min:1',
                function ($attribute, $value, $fail) {
                    if (!$aisles = VendingMachineAisle::find($this->input('vending_machine_aisle_id'))) {
                        return $fail('该货道不存在');
                    }
                    if (!$aisles->is_opened) {
                        return $fail('该货道已关闭');
                    }
                    if ($aisles->stock === 0) {
                        return $fail('该货道商品已售罄');
                    }

                    if ($aisles->stock < $this->input('amount')) {
                        return $fail('该货道商品库存不足');
                    }
                },
            ]
        ];
    }
}
