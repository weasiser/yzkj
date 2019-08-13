<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Admin\RefundRequest;
use App\Models\Order;
use App\Services\RefundService;
use Illuminate\Http\Request;

class PaymentsController extends Controller
{
    public function miniappPayByWxpay(Order $order)
    {
        $this->authorize('own', $order);
        $user = $this->user();
        // 校验订单状态
        if ($order->paid_at || $order->closed) {
            throw new \Exception('订单状态不正确');
        }
        return app('wxpay')->miniapp([
            'out_trade_no' => $order->no,  // 商户订单流水号，与支付宝 out_trade_no 一样
            'total_fee' => $order->total_amount * 100, // 与支付宝不同，微信支付的金额单位是分。
            'body' => '匀贞-售卖机速购', // 订单描述
            'openid' => $user->weapp_openid
        ]);
    }

    public function miniappRefund(Order $order, RefundRequest $request, RefundService $refundService)
    {
        $user = $this->user();

        if ($user->is_mobile_admin) {
            $refundAmount = $request->input('refundAmount');
            $refundService->miniappRefund($order, $refundAmount);
            return $this->response->array([
                'refund_status' => $order->refund_status
            ]);
        } else {
            return $this->response->array([
                'refund_status' => 'Unauthorized'
            ]);
        }
    }
}
