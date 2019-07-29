<?php

namespace App\Admin\Controllers;

use App\Models\Order;
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
//        $grid->column('product.image', __('商品图片'))->image(config('filesystems.disks.oss.cdnDomain'), 40, 40);
        $grid->column('vendingMachine.name', __('售卖机名称'));
        $grid->column('vendingMachineAisle.ordinal', __('货道号'));
        $grid->column('amount', __('数量'));
        $grid->column('sold_price', __('单价'));
        $grid->column('total_amount', __('总价'));
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
        });
//        $grid->column('refund_no', __('Refund no'));
//        $grid->column('is_closed', __('Is closed'));
//        $grid->column('deliver_status', __('出货状态'));
        $grid->column('deliver_status', __('出货状态'))->display(function($value) {
            return Order::$deliverStatusMap[$value];
        });
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

        return $grid;
    }

    /**
     * Make a show builder.
     *
     * @param mixed $id
     * @return Show
     */
    protected function detail($id)
    {
        $show = new Show(Order::findOrFail($id));

//        $content->body(Admin::show(Post::findOrFail($id)));

//        $show->fields(['id', 'no']);
        $show->field('id', __('Id'));
        $show->field('no', __('No'));
        $show->field('user_id', __('User id'));
        $show->field('product_id', __('Product id'));
        $show->field('vending_machine_id', __('Vending machine id'));
        $show->field('vending_machine_aisle_id', __('Vending machine aisle id'));
        $show->field('amount', __('Amount'));
        $show->field('sold_price', __('Sold price'));
        $show->field('total_amount', __('Total amount'));
        $show->field('paid_at', __('Paid at'));
        $show->field('payment_method', __('Payment method'))->badge();
        $show->field('payment_no', __('Payment no'));
        $show->field('refund_note', __('Refund note'));
        $show->field('refund_picture', __('Refund picture'));
        $show->field('refund_refuse_note', __('Refund refuse note'));
        $show->field('refund_status', __('Refund status'));
        $show->field('refund_no', __('Refund no'));
        $show->field('is_closed', __('Is closed'));
        $show->field('deliver_status', __('Deliver status'));
        $show->field('deliver_data', __('Deliver data'));
        $show->field('extra', __('Extra'));
        $show->field('created_at', __('Created at'));
        $show->field('updated_at', __('Updated at'));

        $show->panel()
//            ->style('danger')
            ->title('订单号：'.$show->getModel()->no);

        $show->panel()
            ->tools(function ($tools) {
                $tools->disableEdit();
                $tools->disableDelete();
            });;

        return $show;
    }

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
}
