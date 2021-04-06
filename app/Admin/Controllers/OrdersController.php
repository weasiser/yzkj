<?php

namespace App\Admin\Controllers;

use App\Http\Requests\Admin\RefundRequest;
use App\Models\Order;
use App\Services\RefundService;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;

class OrdersController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
//    protected $title = 'App\Models\Order';

    public function index(Content $content)
    {
        return $content
            ->header('订单')
            ->description('列表')
            ->body($this->grid());
    }

    public function show($id, Content $content)
    {
        return $content
            ->header('订单')
            ->description('详情')
//            ->body($this->detail($id));
            ->body(view('admin.orders.show', ['order' => Order::findOrFail($id)]));
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Order);

        $grid->model()->with(['user','product', 'vendingMachine', 'vendingMachineAisle'])->orderBy('paid_at', 'desc');

        $grid->column('id', __('ID'));
        $grid->column('no', __('订单号'));
        $grid->column('user.nick_name', __('用户昵称'));
        $grid->column('product.title', __('商品名称'));
        $grid->column('product.image', __('缩略图'))->display(function ($image) {
            return config('filesystems.disks.oss.cdnDomain') ? config('filesystems.disks.oss.cdnDomain') . '/' . $image . '-adminProductList' : '/' . $image;
        })->image('', 50, 50);
//        $grid->column('product.image', __('商品图片'))->image(config('filesystems.disks.oss.cdnDomain'), 40, 40);
        $grid->column('vendingMachine.name', __('售卖机名称'));
        $grid->column('vendingMachineAisle.ordinal', __('货道号'));
        $grid->column('amount', __('数量'))->totalRow();
        $grid->column('purchase_price', __('进货价'));
        $grid->column('sold_price', __('单价'));
        $grid->column('total_amount', __('总金额'))->totalRow();
        $grid->column('refund_number', __('退款数量'))->display(function($value) {
            return $value ?: '';
        })->totalRow();
        $grid->column('refund_amount', __('退款金额'))->display(function($value) {
            return $value == 0 ? '' : $value;
        })->totalRow();
        $grid->column('paid_at', __('支付时间'));
//        $grid->column('payment_method', __('支付方式'));
        $grid->column('payment_method', __('支付方式'))->using([
            'alipay' => '<img src="https://s2.ax1x.com/2019/05/29/VnuSoQ.png" class="img img-sm">',
            'wxpay' => '<img src="https://s2.ax1x.com/2019/05/29/Vnnzdg.png" class="img img-sm">'
        ]);
//        $grid->column('payment_no', __('交易号'));
//        $grid->column('refund_note', __('Refund note'));
//        $grid->column('refund_picture', __('Refund picture'));
//        $grid->column('refund_refuse_note', __('Refund refuse note'));
//        $grid->column('refund_status', __('退款状态'));
        $grid->column('refund_status', __('退款状态'))->display(function($value) {
            return Order::$refundStatusMap[$value];
        })->filter(Order::$refundStatusMap);
//        $grid->column('refund_no', __('Refund no'));
//        $grid->column('is_closed', __('Is closed'));
//        $grid->column('deliver_status', __('出货状态'));
        $grid->column('deliver_status', __('出货状态'))->display(function($value) {
            return Order::$deliverStatusMap[$value];
        })->filter(Order::$deliverStatusMap);
//        $grid->column('deliver_data', __('Deliver data'));
//        $grid->column('extra', __('Extra'));
//        $grid->column('created_at', __('创建时间'));
//        $grid->column('updated_at', __('更新时间'));

        $grid->actions(function ($actions) {
            // 不在每一行后面展示查看按钮
//            $actions->disableView();
            $actions->disableEdit();
            $actions->disableDelete();
        });

        $grid->disableCreateButton();

        $grid->tools(function ($tools) {
            // 禁用批量删除按钮
            $tools->batch(function ($batch) {
                $batch->disableDelete();
            });
        });

        $grid->filter(function($filter){
            $filter->column(1/2, function ($filter) {
                $filter->like('no', '订单号');
                $filter->like('user.name', '用户昵称');
            });
            $filter->column(1/2, function ($filter) {
                $filter->like('product.title', '商品名称');
                $filter->like('vendingMachine.name', '售卖机名称');
            });
//            $filter->expand();
        });

        $grid->paginate(10);

        return $grid;
    }

    /**
     * Make a show builder.
     *
     * @param mixed $id
     * @return Show
     */
//    protected function detail($id)
//    {
//        $show = new Show(Order::findOrFail($id));
//
////        $content->body(Admin::show(Post::findOrFail($id)));
//
////        $show->fields(['id', 'no']);
//        $show->field('id', __('Id'));
//        $show->field('no', __('No'));
//        $show->field('user_id', __('User id'));
//        $show->field('product_id', __('Product id'));
//        $show->field('vending_machine_id', __('Vending machine id'));
//        $show->field('vending_machine_aisle_id', __('Vending machine aisle id'));
//        $show->field('amount', __('Amount'));
//        $show->field('sold_price', __('Sold price'));
//        $show->field('total_amount', __('Total amount'));
//        $show->field('paid_at', __('Paid at'));
//        $show->field('payment_method', __('Payment method'))->badge();
//        $show->field('payment_no', __('Payment no'));
//        $show->field('refund_note', __('Refund note'));
//        $show->field('refund_picture', __('Refund picture'));
//        $show->field('refund_refuse_note', __('Refund refuse note'));
//        $show->field('refund_status', __('Refund status'));
//        $show->field('refund_no', __('Refund no'));
//        $show->field('is_closed', __('Is closed'));
//        $show->field('deliver_status', __('Deliver status'));
//        $show->field('deliver_data', __('Deliver data'));
//        $show->field('extra', __('Extra'));
//        $show->field('created_at', __('Created at'));
//        $show->field('updated_at', __('Updated at'));
//
//        $show->panel()
////            ->style('danger')
//            ->title('订单号：'.$show->getModel()->no);
//
//        $show->panel()
//            ->tools(function ($tools) {
//                $tools->disableEdit();
//                $tools->disableDelete();
//            });;
//
//        return $show;
//    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
//    protected function form()
//    {
//        $form = new Form(new Order);
//
//        $form->text('no', __('No'));
//        $form->number('user_id', __('User id'));
//        $form->number('product_id', __('Product id'));
//        $form->number('vending_machine_id', __('Vending machine id'));
//        $form->number('vending_machine_aisle_id', __('Vending machine aisle id'));
//        $form->switch('amount', __('Amount'));
//        $form->decimal('sold_price', __('Sold price'));
//        $form->decimal('total_amount', __('Total amount'));
//        $form->datetime('paid_at', __('Paid at'))->default(date('Y-m-d H:i:s'));
//        $form->text('payment_method', __('Payment method'));
//        $form->text('payment_no', __('Payment no'));
//        $form->text('refund_note', __('Refund note'));
//        $form->text('refund_picture', __('Refund picture'));
//        $form->text('refund_refuse_note', __('Refund refuse note'));
//        $form->text('refund_status', __('Refund status'))->default('pending');
//        $form->text('refund_no', __('Refund no'));
//        $form->switch('is_closed', __('Is closed'));
//        $form->text('deliver_status', __('Deliver status'))->default('pending');
//        $form->text('deliver_data', __('Deliver data'));
//        $form->text('extra', __('Extra'));
//
//        return $form;
//    }

    public function miniappRefund(Order $order, RefundRequest $request, RefundService $refundService)
    {
        $refundAmount = $request->input('refundAmount');

        $extra = $order->extra;

        if (in_array('returnToStock', $request->input('moreOptionsForRefund'))) {
            $extra['return_to_stock'] = true;
        }
        if (in_array('disableAisle', $request->input('moreOptionsForRefund'))) {
            $extra['disable_aisle'] = true;
        }

        $order->update(['extra' => $extra]);

        $refundService->miniappRefund($order, $refundAmount);


//        if ($refundAmount > $order->amount) {
//            throw new \Exception('部分退款数量超过最大值');
//        } elseif ($refundAmount < $order->amount) {
//            $refund_amount = big_number($order->sold_price)->multiply($refundAmount);
//        } else {
//            $refund_amount = $order->total_amount;
//        }
//        // 判断该订单的支付方式
//        switch ($order->payment_method) {
//            case 'wxpay':
//                // 生成退款订单号
//                $refundNo = Order::getAvailableRefundNo();
//                app('wxpay')->refund([
//                    'type' => 'miniapp',
//                    'out_trade_no' => $order->no, // 之前的订单流水号
//                    'total_fee' => $order->total_amount * 100, //原订单金额，单位分
//                    'refund_fee' => $refund_amount * 100, // 要退款的订单金额，单位分
//                    'out_refund_no' => $refundNo, // 退款订单号
//                    // 微信支付的退款结果并不是实时返回的，而是通过退款回调来通知，因此这里需要配上退款回调接口地址
//                    'notify_url' => route('paymentNotifications.miniapp.wxpay.refundNotify'), // 由于是开发环境，需要配成 requestbin 地址
////                    'refund_desc' => '卡货'
//                ]);
//                // 将订单状态改成退款中
//                $order->update([
//                    'refund_no' => $refundNo,
//                    'refund_status' => Order::REFUND_STATUS_PROCESSING,
//                ]);
//                break;
//            case 'alipay':
//                // 用我们刚刚写的方法来生成一个退款订单号
//                $refundNo = Order::getAvailableRefundNo();
//                // 调用支付宝支付实例的 refund 方法
//                $ret = app('alipay')->refund([
//                    'out_trade_no' => $order->no, // 之前的订单流水号
//                    'refund_amount' => $refund_amount, // 退款金额，单位元
//                    'out_request_no' => $refundNo, // 退款订单号
//                ]);
//                // 根据支付宝的文档，如果返回值里有 sub_code 字段说明退款失败
//                if ($ret->sub_code) {
//                    // 将退款失败的保存存入 extra 字段
//                    $extra = $order->extra;
//                    $extra['refund_failed_code'] = $ret->sub_code;
//                    // 将订单的退款状态标记为退款失败
//                    $order->update([
//                        'refund_no' => $refundNo,
//                        'refund_status' => Order::REFUND_STATUS_FAILED,
//                        'extra' => $extra,
//                    ]);
//                } else {
//                    // 将订单的退款状态标记为退款成功并保存退款订单号
//                    $order->update([
//                        'refund_no' => $refundNo,
//                        'refund_status' => Order::REFUND_STATUS_SUCCESS,
//                    ]);
//                }
//                break;
//            default:
//                // 原则上不可能出现，这个只是为了代码健壮性
//                throw new \Exception('未知订单支付方式：'.$order->payment_method);
//                break;
//        }
    }
}
