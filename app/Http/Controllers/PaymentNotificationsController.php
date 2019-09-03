<?php

namespace App\Http\Controllers;

use App\Events\OrderPaidOrRefunded;
use App\Handlers\VendingMachineDeliverAndQuery;
use App\Models\Order;
use App\Models\VendingMachine;
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

        $this->afterPaidOrRefunded($order);

        $this->isDeliveringChange($order->vendingMachine);

        $this->deliverProduct($order);

        return app('wxpay')->success();
    }

    public function miniappPayByAlipayNotify()
    {
        // 校验输入参数
        $data  = app('alipay')->verify();
        // 如果订单状态不是成功或者结束，则不走后续的逻辑
        // 所有交易状态：https://docs.open.alipay.com/59/103672
        if(!in_array($data->trade_status, ['TRADE_SUCCESS', 'TRADE_FINISHED'])) {
            return app('alipay')->success();
        }
        // $data->out_trade_no 拿到订单流水号，并在数据库中查询
        $order = Order::where('no', $data->out_trade_no)->first();
        // 正常来说不太可能出现支付了一笔不存在的订单，这个判断只是加强系统健壮性。
        if (!$order) {
            return 'fail';
        }
        // 如果这笔订单的状态已经是已支付
        if ($order->paid_at) {
            // 返回数据给支付宝
            return app('alipay')->success();
        }

        $order->update([
            'paid_at'        => Carbon::now(), // 支付时间
            'payment_method' => 'alipay', // 支付方式
            'payment_no'     => $data->trade_no, // 支付宝订单号
        ]);

        $this->afterPaidOrRefunded($order);

        $this->isDeliveringChange($order->vendingMachine);

        $this->deliverProduct($order);

        return app('alipay')->success();
    }

    public function miniappWxpayRefundNotify(Request $request)
    {
        // 给微信的失败响应
        $failXml = '<xml><return_code><![CDATA[FAIL]]></return_code><return_msg><![CDATA[FAIL]]></return_msg></xml>';
        $data = app('wxpay')->verify(null, true);

        // 没有找到对应的订单，原则上不可能发生，保证代码健壮性
        if(!$order = Order::where('no', $data['out_trade_no'])->first()) {
            return $failXml;
        }

        if ($data['refund_status'] === 'SUCCESS') {
            // 退款成功，将订单退款状态改成退款成功
            $order->update([
                'refund_status' => Order::REFUND_STATUS_SUCCESS,
            ]);
        } else {
            // 退款失败，将具体状态存入 extra 字段，并表退款状态改成失败
            $extra = $order->extra;
            $extra['refund_failed_code'] = $data['refund_status'];
            $order->update([
                'refund_status' => Order::REFUND_STATUS_FAILED,
                'extra' => $extra
            ]);
        }

        $this->afterPaidOrRefunded($order);

        return app('wxpay')->success();
    }

    protected function deliverProduct(Order $order)
    {
        $order->update(['deliver_status' => Order::DELIVER_STATUS_DELIVERING]);
        $vendingMachine = $order->vendingMachine;
        $ordinal = $order->vendingMachineAisle->ordinal;
        $orderNo = $order->no . '01';
        app(VendingMachineDeliverAndQuery::class)->deliverProduct($vendingMachine->code, $orderNo, $ordinal, $vendingMachine->cabinet_id, $vendingMachine->cabinet_type);
    }

    protected function isDeliveringChange(VendingMachine $vendingMachine)
    {
        $vendingMachine->is_delivering = true;
        $vendingMachine->update();
        dispatch(function () use ($vendingMachine) {
            if ($vendingMachine->is_delivering) {
                $vendingMachine->is_delivering = false;
                $vendingMachine->update();
            }
        })->delay(now()->addSeconds(60));
    }

    protected function afterPaidOrRefunded(Order $order)
    {
        event(new OrderPaidOrRefunded($order));
    }
}
