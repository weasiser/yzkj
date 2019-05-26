<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Api\LoginAuthorizationRequest;
use App\Transformers\UserTransformer;
use Illuminate\Http\Request;
use Auth;

class UsersController extends Controller
{
    public function me(LoginAuthorizationRequest $request, UserTransformer $userTransformer)
    {
        $user = $this->user();

        $attributes['nick_name'] = $request->user_info['nickName'];
        $attributes['avatar'] = $request->user_info['avatarUrl'];
        if ($request->user_info['gender'] === 0) {
            $attributes['gender'] = '未知';
        } elseif ($request->user_info['gender'] === 1) {
            $attributes['gender'] = '男';
        } else {
            $attributes['gender'] = '女';
        }
        $attributes['user_info'] = json_encode($request->user_info);

        $user->update($attributes);
        return $this->response->item($user, $userTransformer);
    }
}
