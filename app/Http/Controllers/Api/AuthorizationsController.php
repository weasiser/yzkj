<?php

namespace App\Http\Controllers\Api;

use Alipay\AlipayRequestFactory;
use Alipay\AopClient;
use Alipay\Key\AlipayKeyPair;
use App\Http\Requests\Api\AuthorizationRequest;
use App\Http\Requests\Api\LoginRequest;
use App\Models\User;
use App\Transformers\UserTransformer;
use Auth;
use Illuminate\Http\Request;

class AuthorizationsController extends Controller
{
    public function weappStore(AuthorizationRequest $request)
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

        return $this->userStore($user, $attributes);
    }

    public function aliappStore(AuthorizationRequest $request)
    {
        $code = $request->code;

        $data = $this->getAliAccessToken($code);

        $user = User::where('alipay_user_id', $data['user_id'])->first();

        $attributes['alipay_access_token'] = $data['access_token'];
        $attributes['alipay_user_id'] = $data['user_id'];

        return $this->userStore($user, $attributes);
    }

    public function aliappReplaceToken(AuthorizationRequest $request, UserTransformer $userTransformer)
    {
        $user = $this->user();
        if ($code = $request->code) {
            $data = $this->getAliAccessToken($code);
            $attributes['alipay_access_token'] = $data['access_token'];
        }
        if ($userInfo = $request->userInfo) {
            $attributes['nick_name'] = isset($userInfo['nickName']) ? $userInfo['nickName'] : '未设置';
            $attributes['avatar'] = isset($userInfo['avatar']) ? $userInfo['avatar'] : 'https://s2.ax1x.com/2019/05/29/Vnuqk4.png';
        }
//        $data = $this->getAliAccessToken($code);
//        $user = User::where('alipay_user_id', $data['user_id'])->first();
//        $attributes['alipay_access_token'] = $data['access_token'];
//        $attributes['nick_name'] = isset($userInfo['nickName']) ? $userInfo['nickName'] : '未设置';
//        $attributes['avatar'] = isset($userInfo['avatar']) ? $userInfo['avatar'] : 'https://s2.ax1x.com/2019/05/29/Vnuqk4.png';
        $user->update($attributes);
//        return $this->response->array([
//            'replace_access_token' => 'success'
//        ]);
        return $this->response->item($user, $userTransformer);
    }

    public function update()
    {
        $token = Auth::guard('api')->refresh();
        return $this->respondWithToken($token);
    }

    public function login(LoginRequest $request)
    {
        $credentials['phone'] = $request->phone;
        $credentials['password'] = $request->password;

        // 验证用户名和密码是否正确
        if (!auth('api')->once($credentials)) {
            return $this->response->errorUnauthorized('手机号码或密码错误');
        }

        // 获取对应的用户
        $user = auth('api')->getUser();

        // 为对应用户创建 JWT
        $token = auth('api')->login($user);

        return $this->respondWithToken($token)->setStatusCode(201);
    }

//    public function destroy()
//    {
//        Auth::guard('api')->logout();
//        return $this->response->noContent();
//    }

    protected function respondWithToken($token)
    {
        return $this->response->array([
            'access_token' => $token,
            'token_type' => 'Bearer',
            'expires_in' => Auth::guard('api')->factory()->getTTL() * 60
        ]);
    }

    protected function userStore($user, $attributes)
    {
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

        return $this->respondWithToken($token);
    }

    protected function getAliAccessToken($code/*, $getUserInfo = false*/)
    {
        $keyPair = AlipayKeyPair::create(
            config('services.alipay.app_private_key'),
            config('services.alipay.public_key')
        );
        $aop = new AopClient(config('services.alipay.mini_program_appid'), $keyPair);
        $request = AlipayRequestFactory::create('alipay.system.oauth.token', [
            'grant_type' => 'authorization_code',
            'code' => $code
        ]);
//        $response = new \Alipay\Request\AlipaySystemOauthTokenRequest();
//        $response->setCode($code);
        $data = $aop->execute($request)->getData();
        // 如果结果错误，说明 code 已过期或不正确，返回 401 错误
        if (isset($data['code'])) {
            return $this->response->errorUnauthorized($data['sub_msg']);
        }
//        if ($getUserInfo) {
//            $request = AlipayRequestFactory::create('alipay.user.info.share', [
//                'auth_token' => $data['access_token']
//            ]);
//            $data['user_info'] = $aop->execute($request)->getData();
//            dd($data);
//            if ($data['user_info']['code'] !== '10000') {
//                return $this->response->errorUnauthorized($data['user_info']['msg']);
//            }
//        }

        return $data;
    }
}
