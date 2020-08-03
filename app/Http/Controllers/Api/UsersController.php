<?php

namespace App\Http\Controllers\Api;

use App\Handlers\ImageUploadHandler;
use App\Http\Requests\Api\AvatarRequest;
use App\Http\Requests\Api\LoginAuthorizationRequest;
use App\Http\Requests\Api\UserRequest;
use App\Models\User;
use App\Transformers\UserTransformer;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Auth;

class UsersController extends Controller
{
    public function me(LoginAuthorizationRequest $request, UserTransformer $userTransformer)
    {
        $user = $this->user();

        $attributes['nick_name'] = $request->user_info['nickName'];
        $http = new Client();
        $response = $http->get($request->user_info['avatarUrl'], ['http_errors' => false])->getStatusCode();
        if ($response === 404) {
            $attributes['avatar'] = 'https://s2.ax1x.com/2019/05/29/Vnuqk4.png';
        } else {
            $attributes['avatar'] = $request->user_info['avatarUrl'];
        }

        $user->update($attributes);
        return $this->response->item($user, $userTransformer);
    }

    public function userInfo(UserTransformer $userTransformer)
    {
        $user = $this->user();
        return $this->response->item($user, $userTransformer);
    }

    public function myWarehouses()
    {
        $user = $this->user();
        $warehouses = $user->warehouses;
        $myWarehouses = [];
        foreach ($warehouses as $warehouse) {
            $myWarehouses[] = $warehouse->id;
        }
        return $this->response->array([
            'is_mobile_admin' => $user->is_mobile_admin,
            'myWarehouses' => $myWarehouses,
        ]);
    }

    public function store(UserRequest $request)
    {
        $verifyData = Cache::store('redis')->get($request->verification_key);

        if (!$verifyData) {
            return $this->response->error('验证码已失效', 422);
        }

        if (!hash_equals($verifyData['code'], $request->verification_code)) {
            // 返回401
            return $this->response->errorUnauthorized('验证码错误');
        }

        $user = User::create([
            'phone' => $verifyData['phone'],
            'password' => bcrypt($request->password),
        ]);

        // 清除验证码缓存
        Cache::store('redis')->forget($request->verification_key);

        $token = Auth::guard('api')->fromUser($user);

        return $this->respondWithToken($token)->setStatusCode(201);
    }

    public function update(UserRequest $request, UserTransformer $userTransformer)
    {
        $user = $this->user();

        $attributes = $request->only(['nick_name']);

        $user->update($attributes);

        return $this->response->item($user, $userTransformer);
    }

    public function replaceAvatar(AvatarRequest $request, ImageUploadHandler $imageUploadHandler, UserTransformer $userTransformer)
    {
        $user = $this->user();

        if ($file = $request->avatar) {
            $result = $imageUploadHandler->save($file, 'avatar', $user->id, '', false, $user->avatar);
            $user->avatar = $result['path'];
            $user->save();
        }

        return $this->response->item($user, $userTransformer);
    }

    protected function respondWithToken($token)
    {
        return $this->response->array([
            'access_token' => $token,
            'token_type' => 'Bearer',
            'expires_in' => Auth::guard('api')->factory()->getTTL() * 60
        ]);
    }
}
