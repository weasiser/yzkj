<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Http\Request;

class PaymentNotificationsController extends Controller
{
    public function miniappPayByWxpayNotify()
    {
        // 校验回调参数是否正确
        $data  = app('wxpay')->verify();
        // 找到对应的订单
        $order = Order::where('no', $data->out_trade_no)->first();
        // 订单不存在则告知微信支付
        if (!$order) {
            return 'fail';
        }
        // 订单已支付
        if ($order->paid_at) {
            // 告知微信支付此订单已处理
            return app('wxpay')->success();
        }

        // 将订单标记为已支付
        $order->update([
            'paid_at'        => Carbon::now(),
            'payment_method' => 'wxpay',
            'payment_no'     => $data->transaction_id,
        ]);

        return app('wxpay')->success();
    }
}
