<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

//Route::get('/', function () {
//    return view('welcome');
//});

//Auth::routes(['register' => false]);

Route::get('/', 'HomeController@index')->name('home');

Route::post('paymentNotifications/miniapp/wxpay/notify', 'PaymentNotificationsController@miniappPayByWxpayNotify')->name('paymentNotifications.miniapp.wxpay.notify');

Route::post('paymentNotifications/miniapp/alipay/notify', 'PaymentNotificationsController@miniappPayByAlipayNotify')->name('paymentNotifications.miniapp.alipay.notify');

Route::post('deliverProductNotifications/notify', 'DeliverProductNotificationsController@deliverProductNotify')->name('deliverProductNotifications.deliverProductNotify');

Route::post('paymentNotifications/miniapp/wxpay/refundNotify', 'PaymentNotificationsController@miniappWxpayRefundNotify')->name('paymentNotifications.miniapp.wxpay.refundNotify');

Route::post('yiputeng/deliverProductNotifications/notify', 'DeliverProductNotificationsController@yiputengDeliverProductNotify')->name('deliverProductNotifications.yiputengDeliverProductNotify');
