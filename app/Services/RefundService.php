<?php

namespace App\Services;

use App\Events\OrderPaidOrRefunded;
use App\Http\Requests\Admin\RefundRequest;
use App\Models\Order;

class RefundService
{
    public function miniappRefund(Order $order, $refundAmount)
    {
//        $refundAmount = $request->input('refundAmount');
        if ($refundAmount > $order->amount) {
            throw new \Exception('部分退款数量超过最大值');
        } elseif ($refundAmount < $order->amount) {
            $refund_amount = big_number($order->sold_price)->multiply($refundAmount)->getValue();
        } else {
            $refund_amount = $order->total_amount;
        }
        // 判断该订单的支付方式
        switch ($order->payment_method) {
            case 'wxpay':
                // 生成退款订单号
                $refundNo = Order::getAvailableRefundNo();
                $result = app('wxpay')->refund([
                    'type' => 'miniapp',
                    'out_trade_no' => $order->no, // 之前的订单流水号
                    'total_fee' => $order->total_amount * 100, //原订单金额，单位分
                    'refund_fee' => $refund_amount * 100, // 要退款的订单金额，单位分
                    'out_refund_no' => $refundNo, // 退款订单号
                    // 微信支付的退款结果并不是实时返回的，而是通过退款回调来通知，因此这里需要配上退款回调接口地址
                    'notify_url' => route('paymentNotifications.miniapp.wxpay.refundNotify'), // 由于是开发环境，需要配成 requestbin 地址
//                    'refund_desc' => '卡货'
                ]);
                if ($result->return_code === 'SUCCESS' && $result->result_code === 'SUCCESS') {
                    // 将订单状态改成退款中
                    $order->update([
                        'refund_no' => $refundNo,
                        'refund_status' => Order::REFUND_STATUS_PROCESSING,
                        'refund_amount' => $refund_amount,
                        'refund_number' => $refundAmount,
                    ]);
                } else {
                    $order->update([
                        'refund_no' => $refundNo,
                        'refund_status' => Order::REFUND_STATUS_FAILED,
                        'extra' => [
                            'return_msg' => $result->return_msg,
                            'err_code' => $result->err_code,
                            'err_code_des' => $result->err_code_des,
                        ],
                    ]);
                }
                break;
            case 'alipay':
                // 用我们刚刚写的方法来生成一个退款订单号
                $refundNo = Order::getAvailableRefundNo();
                // 调用支付宝支付实例的 refund 方法
                $ret = app('alipay')->refund([
                    'out_trade_no' => $order->no, // 之前的订单流水号
                    'refund_amount' => $refund_amount, // 退款金额，单位元
                    'out_request_no' => $refundNo, // 退款订单号
                ]);
                // 根据支付宝的文档，如果返回值里有 sub_code 字段说明退款失败
                if ($ret->sub_code) {
                    // 将退款失败的保存存入 extra 字段
//                    $extra = $order->extra;
//                    $extra['refund_failed_code'] = $ret->sub_code;
//                    $extra['refund_failed_msg'] = $ret->sub_msg;
                    // 将订单的退款状态标记为退款失败
                    $order->update([
                        'refund_no' => $refundNo,
                        'refund_status' => Order::REFUND_STATUS_FAILED,
                        'extra' => [
                            'err_code' => $ret->sub_code,
                            'err_code_des' => $ret->sub_msg,
                        ],
                    ]);
                } else {
                    // 将订单的退款状态标记为退款成功并保存退款订单号
                    $order->update([
                        'refund_no' => $refundNo,
                        'refund_status' => Order::REFUND_STATUS_SUCCESS,
                        'refund_amount' => $refund_amount,
                        'refund_number' => $refundAmount,
                    ]);

                    $this->afterRefunded($order);
                }
                break;
            default:
                // 原则上不可能出现，这个只是为了代码健壮性
                throw new \Exception('未知订单支付方式：'.$order->payment_method);
                break;
        }
    }

    protected function afterRefunded(Order $order)
    {
        event(new OrderPaidOrRefunded($order));
    }
}
