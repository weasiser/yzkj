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
        // 删除token
        $api->delete('authorizations/current', 'AuthorizationsController@destroy')
            ->name('api.authorizations.destroy');

        // 短信验证码
        $api->post('verificationCodes', 'VerificationCodesController@store')
            ->name('api.verificationCodes.store');
        // 用户注册
        $api->post('users', 'UsersController@store')
            ->name('api.users.store');
        // 用户登录
        $api->post('login', 'AuthorizationsController@login')
            ->name('api.authorizations.login');
    });

    $api->group([
        'middleware' => 'api.throttle',
        'limit' => config('api.rate_limits.access.limit'),
        'expires' => config('api.rate_limits.access.expires'),
    ], function($api) {
        // 游客可以访问的接口
        // 售卖机信息
        $api->get('vendingMachines/{vendingMachine}', 'VendingMachinesController@show')
            ->name('api.vendingMachines.show');
        // 查询是否在线
        $api->get('vendingMachine/query', 'VMDeliverAndQueryController@queryMachineInfo')
            ->name('api.VMDeliverAndQuery.queryMachineInfo');
        $api->get('vendingMachineAisles/query', 'VMDeliverAndQueryController@queryCommodityInfo')
            ->name('api.VMDeliverAndQuery.queryCommodityInfo');
        $api->get('articleCategories', 'ArticleCategoriesController@index')
            ->name('api.articleCategories.index');
        $api->get('articles', 'ArticlesController@index')
            ->name('api.articles.index');
        $api->get('articles/{article}', 'ArticlesController@show')
            ->name('api.articles.show');
        $api->get('articles/{article}/articleComments', 'ArticlesController@articleCommentsIndex')
            ->name('api.articles.comment.index');
        $api->get('vendingMachines/yiputeng/queryMachineList', 'VMDeliverAndQueryController@queryMachineList')
            ->name('api.vendingMachines.yiputeng.queryMachineList');
        $api->get('vendingMachines/yiputeng/queryShelfList', 'VMDeliverAndQueryController@queryShelfList')
            ->name('api.vendingMachines.yiputeng.queryShelfList');
        $api->get('getProductSaleStatisticsMonthly', 'ProductsController@getProductSaleStatisticsMonthly')
            ->name('api.products.getProductSaleStatisticsMonthly');

        // 需要 token 验证的接口
        $api->group(['middleware' => 'api.auth'], function($api) {
            // 更换ali_token
            $api->post('authorizations/aliToken', 'AuthorizationsController@aliappReplaceToken')
                ->name('api.authorizations.aliappReplaceToken');
            // 当前登录用户信息
            $api->get('user', 'UsersController@userInfo')
                ->name('api.user.info');
            $api->patch('user', 'UsersController@me')
                ->name('api.user.patch');
            $api->put('user', 'UsersController@me')
                ->name('api.user.put');
            $api->post('user', 'UsersController@me')
                ->name('api.user.post');
            $api->get('myWarehouses', 'UsersController@myWarehouses')
                ->name('api.user.myWarehouses');
            // 创建订单
            $api->post('orders', 'OrdersController@store')
                ->name('api.orders.store');
            // 显示订单
            $api->get('orders/{order}', 'OrdersController@show')
                ->name('api.orders.show');
            // 删除订单
            $api->post('orders/{order}/destroy', 'OrdersController@destroy')
                ->name('api.orders.destroy');
            // 订单申请退款
            $api->post('orders/{order}/applyRefund', 'OrdersController@applyRefund')
                ->name('api.orders.applyRefund');

            // 创建退款订单
            $api->post('refundOrderFeedback', 'RefundOrderFeedbackController@store')
                ->name('api.refundOrderFeedback.store');
            // 显示退款订单
            $api->get('refundOrderFeedback/{refundOrderFeedback}', 'RefundOrderFeedbackController@show')
                ->name('api.refundOrderFeedback.show');
            // 更新退款订单
            $api->post('refundOrderFeedback/{refundOrderFeedback}/update', 'RefundOrderFeedbackController@update')
                ->name('api.refundOrderFeedback.update');
            // 处理退款订单
            $api->post('refundOrderFeedback/{refundOrderFeedback}/handle', 'RefundOrderFeedbackController@handle')
                ->name('api.refundOrderFeedback.handle');
            // 取消退款订单
            $api->post('refundOrderFeedback/{refundOrderFeedback}/destroy', 'RefundOrderFeedbackController@destroy')
                ->name('api.refundOrderFeedback.destroy');
            // 删除图片
            $api->post('refundOrderFeedback/{refundOrderFeedback}/deleteImage', 'RefundOrderFeedbackController@deleteImage')
                ->name('api.refundOrderFeedback.deleteImage');
            // 退款订单上传图片
            $api->post('refundOrderFeedback/uploadPicture', 'RefundOrderFeedbackController@uploadPicture')
                ->name('api.refundOrderFeedback.uploadPicture');
            // 退款订单列表
            $api->get('refundOrders', 'OrdersController@refundOrders')
                ->name('api.orders.refundOrders');
            // 退款订单数量
            $api->get('refundOrdersCount', 'OrdersController@refundOrdersCount')
                ->name('api.orders.refundOrdersCount');
            // 订阅消息
            $api->post('subscribeMessages', 'SubscribeMessagesController@store')
                ->name('api.subscribeMessages.store');
            $api->get('subscribeMessage', 'SubscribeMessagesController@show')
                ->name('api.subscribeMessages.show');
            $api->post('cancelSubscribeMessage', 'SubscribeMessagesController@destroy')
                ->name('api.subscribeMessages.destroy');

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
            // 小程序支付宝支付
            $api->get('payments/{order}/miniapp/alipay', 'PaymentsController@miniappPayByAlipay')
                ->name('api.payments.miniappPayByAlipay');
            // 小程序退款
            $api->post('payments/{order}/miniapp/refund', 'PaymentsController@miniappRefund')
                ->name('api.payments.refund.miniappRefund');
            // 出货
            $api->post('vendingMachine/deliver', 'VMDeliverAndQueryController@deliverProduct')
                ->name('api.VMDeliverAndQuery.deliverProduct');
            $api->post('vendingMachines/yiputeng/payMultiDelivery', 'VMDeliverAndQueryController@payMultiDelivery')
                ->name('api.vendingMachines.yiputeng.payMultiDelivery');
            $api->post('vendingMachines/{vendingMachine}/deliverProductTest', 'VMDeliverAndQueryController@deliverProductTest')
                ->name('api.VMDeliverAndQuery.deliverProductTest');
            // 仓库列表
            $api->get('warehouses', 'WarehousesController@index')
                ->name('api.warehouses.index');
            // 售卖机列表
            $api->get('vendingMachines', 'VendingMachinesController@index')
                ->name('api.vendingMachines.index');
            // 出货状态查询
            $api->get('queryDeliverStatus', 'VMDeliverAndQueryController@queryDeliverStatus')
                ->name('api.queryDeliverStatus');
            $api->get('queryYiputengDeliverStatus', 'VMDeliverAndQueryController@queryYiputengDeliverStatus')
                ->name('api.queryYiputengDeliverStatus');
            // 售卖机第三方接口状态查询
            $api->get('queryVendingMachineApiStatus', 'VMDeliverAndQueryController@queryVendingMachineApiStatus')
                ->name('api.queryVendingMachineApiStatus');
            // 订单正在出货
//            $api->post('orders/{order}/delivering', 'OrdersController@delivering')
//                ->name('api.orders.delivering');
            // 订单出货成功
//            $api->post('orders/{order}/delivered', 'OrdersController@delivered')
//                ->name('api.orders.delivered');
            // 我的订单列表
            $api->get('userOrders', 'OrdersController@userOrders')
                ->name('api.orders.userOrders');
            // 是否正在出货
            $api->get('vendingMachines/{vendingMachine}/isDelivering', 'VendingMachinesController@isDelivering')
                ->name('api.vendingMachines.isDelivering');
            // 改变是否正在出货
//            $api->get('vendingMachines/{vendingMachine}/isDeliveringChange', 'VendingMachinesController@isDeliveringChange')
//                ->name('api.vendingMachines.isDeliveringChange');
            // 账单列表
            $api->get('orders', 'OrdersController@index')
                ->name('api.orders.index');
            // 商品销量统计
            $api->get('getProductSaleStatistics', 'OrdersController@getProductSaleStatistics')
                ->name('api.orders.getProductSaleStatistics');
            // 月内每天统计
            $api->get('getDailyStatistics', 'OrdersController@getDailyStatistics')
                ->name('api.orders.getDailyStatistics');
            // 可补充
            $api->get('getAvailableProductStock', 'ProductsController@getAvailableProductStock')
                ->name('api.products.getAvailableProductStock');
            // 信息
            $api->post('information', 'InformationController@store')
                ->name('api.information.store');
            $api->get('information/{information}', 'InformationController@show')
                ->name('api.information.show');
            $api->get('information', 'InformationController@index')
                ->name('api.information.index');
            $api->put('information/{information}', 'InformationController@update')
                ->name('api.information.update');
            $api->delete('information/{information}', 'InformationController@destroy')
                ->name('api.information.destroy');

            // 发布评论
            $api->post('articles/{article}/articleComments', 'ArticleCommentsController@store')
                ->name('api.articles.comments.store');
            // 删除评论
            $api->delete('articles/{article}/articleComments/{articleComment}', 'ArticleCommentsController@destroy')
                ->name('api.articles.comments.destroy');

            $api->patch('users/info', 'UsersController@update')
                ->name('api.users.info.update');
            $api->post('users/avatar', 'UsersController@replaceAvatar')
                ->name('api.users.avatar.replace');
        });
    });

//    $api->group([
//        'middleware' => ['api.throttle', 'cors']
//    ], function($api) {
//        $api->get('vendingMachines/yiputeng/getApiToken', 'VMDeliverAndQueryController@getApiToken')
//            ->name('api.vendingMachines.yiputeng.getApiToken');
//        $api->get('vendingMachines/yiputeng/queryMachineList', 'VMDeliverAndQueryController@queryMachineList')
//            ->name('api.vendingMachines.yiputeng.queryMachineList');
//        $api->get('vendingMachines/yiputeng/queryShelfList', 'VMDeliverAndQueryController@queryShelfList')
//            ->name('api.vendingMachines.yiputeng.queryShelfList');
//        $api->post('vendingMachines/yiputeng/payDelivery', 'VMDeliverAndQueryController@payDelivery')
//            ->name('api.vendingMachines.yiputeng.payDelivery');
//        $api->post('vendingMachines/yiputeng/payMultiDelivery', 'VMDeliverAndQueryController@payMultiDelivery')
//            ->name('api.vendingMachines.yiputeng.payMultiDelivery');
//    });
});
