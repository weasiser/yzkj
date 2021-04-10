<?php

namespace App\Transformers;

use App\Models\User;
use Illuminate\Support\Facades\Storage;
use League\Fractal\TransformerAbstract;

class UserTransformer extends TransformerAbstract
{
    public function transform(User $user)
    {
        return [
            'id'       => $user->id,
            'nickName' => $user->nick_name,
            'avatar'   => $user->avatar === null || filter_var($user->avatar, FILTER_VALIDATE_URL) ? $user->avatar : (config('filesystems.disks.oss.cdnDomain') ? config('filesystems.disks.oss.cdnDomain') . '/' . $user->avatar . '-avatar' : Storage::disk(config('admin.upload.disk'))->url($user->avatar)),
            'is_mobile_admin' => $user->is_mobile_admin,
            'is_warehouse_manager' => $user->warehouses->count() ? true : false,
//            'created_at' => $user->created_at->toDateTimeString(),
//            'updated_at' => $user->updated_at->toDateTimeString(),
        ];
    }
}
