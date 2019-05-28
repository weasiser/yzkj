<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Api\LoginAuthorizationRequest;
use App\Transformers\UserTransformer;
use Illuminate\Http\Request;

class UsersController extends Controller
{
    public function me(LoginAuthorizationRequest $request, UserTransformer $userTransformer)
    {
        $user = $this->user();

        $attributes['nick_name'] = $request->user_info['nickName'];
        $attributes['avatar'] = $request->user_info['avatarUrl'];

        $user->update($attributes);
        return $this->response->item($user, $userTransformer);
    }
}
