<div class="box box-info">
  <div class="box-header with-border">
    <h3 class="box-title">订单号：{{ $order->no }}</h3>
    <div class="box-tools">
      <div class="btn-group float-right">
        <a href="{{ route('admin.orders.index') }}" class="btn btn-sm btn-default"><i class="fa fa-list"></i> 列表</a>
      </div>
    </div>
  </div>
  <div class="box-body">
    <table class="table table-bordered">
      <tbody>
      <tr>
        <td>用户昵称：</td>
        <td class="text-bold">{{ $order->user->nick_name }}</td>
        <td>支付时间：</td>
        <td class="text-bold">{{ $order->paid_at }}</td>
      </tr>
      <tr>
        <td>支付方式：</td>
        <td class="text-bold">{{ \App\Models\Order::$paymentMethodMap[$order->payment_method] }}</td>
        <td>交易号：</td>
        <td class="text-bold">{{ $order->payment_no }}</td>
      </tr>
      <tr>
        <td>商品名称：</td>
        <td class="text-bold">{{ $order->product->title }}</td>
        <td>售卖机名称：</td>
        <td class="text-bold">{{ $order->vendingMachine->name }}</td>
        <td>货道号：</td>
        <td class="text-bold">{{ $order->vendingMachineAisle->ordinal }}</td>
      </tr>
      <tr>
        <td>创建时间：</td>
        <td class="text-bold">{{ $order->created_at }}</td>
        <td>更新时间：</td>
        <td class="text-bold">{{ $order->updated_at }}</td>
      </tr>
      <tr>
{{--        <td>订单金额：</td>--}}
{{--        <td>￥ {{ $order->total_amount }}</td>--}}
        <td>出货状态：</td>
        <td class="text-bold">{{ \App\Models\Order::$deliverStatusMap[$order->deliver_status] }}</td>
        <td>退款状态：</td>
        <td class="text-bold">{{ \App\Models\Order::$refundStatusMap[$order->refund_status] }}</td>
      </tr>
      <tr>
        <td>单价：</td>
        <td class="text-bold">￥ {{ $order->sold_price }}</td>
        <td>数量：</td>
        <td class="text-bold">{{ $order->amount }}</td>
        <td>总价：</td>
        <td class="text-bold">￥ {{ $order->total_amount }}</td>
      </tr>
      </tbody>
    </table>
  </div>
</div>
