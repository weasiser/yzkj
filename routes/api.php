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
        $api->post('authorizations/aliToken', 'AuthorizationsController@aliappReplaceToken')
            ->name('api.authorizations.aliappReplaceToken');
        // 删除token
//        $api->delete('authorizations/current', 'AuthorizationsController@destroy')
//            ->name('api.authorizations.destroy');
        $api->get('vendingMachines/{vendingMachine}', 'VendingMachinesController@show')
            ->name('api.vendingMachines.show');
        // 查询是否在线
        $api->get('vendingMachine/query', 'VMDeliverAndQueryController@queryMachineInfo')
            ->name('api.VMDeliverAndQuery.queryMachineInfo');
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
        // 创建订单
        $api->get('orders/{order}', 'OrdersController@show')
            ->name('api.orders.show');
        // 删除订单
        $api->post('orders/{order}/destroy', 'OrdersController@destroy')
            ->name('api.orders.destroy');
        // 订单申请退款
        $api->post('orders/{order}/applyRefund', 'OrdersController@applyRefund')
            ->name('api.orders.applyRefund');
        // 更新货道
        $api->put('vendingMachineAisles/{vendingMachineAisle}', 'VendingMachineAislesController@update')
            ->name('api.vendingMachineAisles.update');
        // 更新售卖机
        $api->put('vendingMachines/{vendingMachine}', 'VendingMachinesController@update')
            ->name('api.vendingMachines.update');
        // 商品列表
        $api->get('products', 'ProductsController@index')
            ->name('api.products.index');
        // 小程序微信支付
        $api->get('payments/{order}/miniapp/wxpay', 'PaymentsController@miniappPayByWxpay')
            ->name('api.payments.miniappPayByWxpay');
        // 小程序退款
        $api->post('payments/{order}/miniapp/refund', 'PaymentsController@miniappRefund')
            ->name('api.payments.refund.miniappRefund');
        // 出货
        $api->post('vendingMachine/deliver', 'VMDeliverAndQueryController@deliverProduct')
            ->name('api.VMDeliverAndQuery.deliverProduct');

        $api->get('vendingMachines', 'VendingMachinesController@index')
            ->name('api.vendingMachines.index');

        $api->get('queryDeliverStatus', 'VMDeliverAndQueryController@queryDeliverStatus')
            ->name('api.queryDeliverStatus');

        // 订单正在出货
        $api->post('orders/{order}/delivering', 'OrdersController@delivering')
            ->name('api.orders.delivering');
        // 订单出货成功
        $api->post('orders/{order}/delivered', 'OrdersController@delivered')
            ->name('api.orders.delivered');

        // 我的订单列表
        $api->get('userOrders', 'OrdersController@userOrders')
            ->name('api.orders.userOrders');

        // 是否正在出货
        $api->get('vendingMachines/{vendingMachine}/isDelivering', 'VendingMachinesController@isDelivering')
            ->name('api.vendingMachines.isDelivering');

        // 改变是否正在出货
        $api->get('vendingMachines/{vendingMachine}/isDeliveringChange', 'VendingMachinesController@isDeliveringChange')
            ->name('api.vendingMachines.isDeliveringChange');
    });
});
