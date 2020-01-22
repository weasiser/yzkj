<?php

namespace App\Transformers;

use App\Models\User;
use League\Fractal\TransformerAbstract;

class UserTransformer extends TransformerAbstract
{
    public function transform(User $user)
    {
        return [
            'id'       => $user->id,
            'nickName' => $user->nick_name,
            'avatar'   => $user->avatar,
            'is_mobile_admin' => $user->is_mobile_admin,
            'is_warehouse_manager' => $user->warehouses->count() ? true : false,
//            'created_at' => $user->created_at->toDateTimeString(),
//            'updated_at' => $user->updated_at->toDateTimeString(),
        ];
    }
}
