<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

//Route::middleware('auth:api')->get('/user', function (Request $request) {
//    return $request->user();
//});

$api = app('Dingo\Api\Routing\Router');

$api->version('v1', [
    'namespace' => 'App\Http\Controllers\Api',
    'middleware' => ['bindings']
], function($api) {
    $api->group([
        'middleware' => 'api.throttle',
        'limit' => config('api.rate_limits.sign.limit'),
        'expires' => config('api.rate_limits.sign.expires'),
    ], function($api) {
        // 登录
        $api->post('weapp/authorizations', 'AuthorizationsController@weappStore')
            ->name('api.weapp.authorizations.store');
        $api->post('aliapp/authorizations', 'AuthorizationsController@aliappStore')
            ->name('api.aliapp.authorizations.store');
        // 刷新token
        $api->put('authorizations/current', 'AuthorizationsController@update')
            ->name('api.authorizations.update');
        $api->post('authorizations/current', 'AuthorizationsController@update')
            ->name('api.authorizations.update');
        // 更换ali_token
        $api->post('authorizations/ali_token', 'AuthorizationsController@aliappReplaceToken')
            ->name('api.authorizations.aliappReplaceToken');
        // 删除token
//        $api->delete('authorizations/current', 'AuthorizationsController@destroy')
//            ->name('api.authorizations.destroy');
        $api->get('vending_machines/{vending_machine}', 'VendingMachinesController@show')
            ->name('api.vending_machines.show');
    });

    // 需要 token 验证的接口
    $api->group([
        'middleware' => 'api.auth',
        'limit' => config('api.rate_limits.access.expires'),
        'expires' => config('api.rate_limits.access.expires'),
    ], function($api) {
        // 当前登录用户信息
        $api->patch('user', 'UsersController@me')
            ->name('api.user.patch');
        $api->put('user', 'UsersController@me')
            ->name('api.user.put');
        $api->post('user', 'UsersController@me')
            ->name('api.user.post');
        // 创建订单
        $api->post('orders', 'OrdersController@store')
            ->name('api.orders.store');
    });
});
