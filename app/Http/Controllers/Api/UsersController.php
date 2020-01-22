<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Api\LoginAuthorizationRequest;
use App\Transformers\UserTransformer;
use GuzzleHttp\Client;
use Illuminate\Http\Request;

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
}
