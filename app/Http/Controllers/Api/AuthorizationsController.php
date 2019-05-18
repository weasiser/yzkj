<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Api\AuthorizationRequest;
use App\Http\Requests\Api\WeappAuthorizationRequest;
use App\Models\User;
use App\Transformers\UserTransformer;
use Auth;
use Illuminate\Http\Request;

class AuthorizationsController extends Controller
{
//    public function store(AuthorizationRequest $authorizationRequest, UserTransformer $userTransformer)
//    {
//        $user = User::first();
//        $token = Auth::guard('api')->fromUser($user);
//        return $this->response->item($user, $userTransformer)
//            ->setMeta([
//                'access_token' => Auth::guard('api')->fromUser($user),
//                'token_type' => 'Bearer',
//                'expires_in' => Auth::guard('api')->factory()->getTTL() * 60
//            ])
//            ->setStatusCode(201);
//    }

    public function weappStore(WeappAuthorizationRequest $request)
    {
        $code = $request->code;

        // 根据 code 获取微信 openid 和 session_key
        $miniProgram = \EasyWeChat::miniProgram();
        $data = $miniProgram->auth->session($code);

        // 如果结果错误，说明 code 已过期或不正确，返回 401 错误
        if (isset($data['errcode'])) {
            return $this->response->errorUnauthorized('code 不正确');
        }

        // 找到 openid 对应的用户
        $user = User::where('weapp_openid', $data['openid'])->first();

        $attributes['weixin_session_key'] = $data['session_key'];
        $attributes['weapp_openid'] = $data['openid'];

        // 未找到对应用户则直接创建用户
        if (!$user) {
            // 创建用户
            $user = User::create($attributes);
        } else {
            // 更新用户数据
            $user->update($attributes);
        }

        // 为对应用户创建 JWT
        $token = Auth::guard('api')->fromUser($user);

        return $this->respondWithToken($token)->setStatusCode(201);
    }

    public function update()
    {
        $token = Auth::guard('api')->refresh();
        return $this->respondWithToken($token);
    }

    public function destroy()
    {
        Auth::guard('api')->logout();
        return $this->response->noContent();
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
