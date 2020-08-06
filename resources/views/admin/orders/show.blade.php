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
    <table class="table table-bordered table-hover">
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
        <td class="text-bold">
          {{ \App\Models\Order::$refundStatusMap[$order->refund_status] }}
          <!-- 如果订单退款状态是已申请，则展示处理按钮 -->
          @if($order->refund_status === \App\Models\Order::REFUND_STATUS_APPLIED || $order->refund_status === \App\Models\Order::REFUND_STATUS_PENDING)
            <button class="btn btn-sm btn-success col-sm-offset-1" id="btn-refund-agree">退款</button>
{{--            <button class="btn btn-sm btn-danger" id="btn-refund-disagree">不同意</button>--}}
          @endif
        </td>
        @if($order->refund_status === \App\Models\Order::REFUND_STATUS_SUCCESS)
          <td>退款数量：</td>
          <td class="text-bold">{{ $order->refund_number }}</td>
          <td>退款金额：</td>
          <td class="text-bold">￥ {{ $order->refund_amount }}</td>
        @endif
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

<script>
  $(document).ready(function() {
    let amountStr = '{{ $order->amount }}'
    let amount = parseInt(amountStr)
    let amountOptions = {}
    for (let i = 1; i <= amount; i++) {
      if (i === amount) {
        amountOptions[i] = i + '（全部退款）'
      } else {
        amountOptions[i] = i
      }
    }
    // 同意 按钮的点击事件
    $('#btn-refund-agree').click(function() {
      Swal.fire({
        title: '请确认是否要将款项退还给用户？',
        html: '<label class="checkbox-inline"><input type="checkbox" name="returnToStock" value="returnToStock" />补回库存</label><label class="checkbox-inline"><input type="checkbox" name="disableAisle" value="disableAisle" />禁用货道</label>',
        type: 'warning',
        input: 'select',
        inputOptions: amountOptions,
        // inputPlaceholder: '退款原因，会在下发给用户的退款消息中体现退款原因，可不填',
        // inputValue: amountStr,
        inputPlaceholder: '选择部分退款数量，最大即为全部退款',
        showCancelButton: true,
        confirmButtonText: "确认",
        cancelButtonText: "取消",
        showLoaderOnConfirm: true,
        inputValidator: (value) => {
          if (!value) {
            return '请选择部分退款数量'
          }
        },
        preConfirm: function() {
          return $.ajax({
            url: '{{ route('admin.orders.miniappRefund', [$order->id]) }}',
            type: 'POST',
            data: JSON.stringify({
              refundAmount: parseInt($('.swal2-select').val()),
              moreOptionsForRefund: [
                $('[name="returnToStock"]:checked').val(),
                $('[name="disableAisle"]:checked').val()
              ],
              _token: LA.token,
            }),
            contentType: 'application/json',
          })
        },
        allowOutsideClick: false
      }).then(function (ret) {
        // 如果用户点击了『取消』按钮，则不做任何操作
        if (ret.dismiss === 'cancel') {
          return
        }
        Swal.fire({
          title: '操作成功',
          type: 'success'
        }).then(function() {
          // 用户点击 swal 上的按钮时刷新页面
          // location.reload();
          $.admin.reload()
        })
      })
    })

  })
</script>
