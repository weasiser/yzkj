<?php

namespace App\Admin\Controllers;

use App\Models\Product;
use App\Models\VendingMachineAisle;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;

class VendingMachineAislesController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
//    protected $title = 'App\Models\VendingMachineAisle';

    protected $is_opened = [
        'on'  => ['value' => 1, 'text' => '已开启', 'color' => 'success'],
        'off' => ['value' => 0, 'text' => '已关闭', 'color' => 'danger'],
    ];

    protected $lead_rail = [
        'on'  => ['value' => 1, 'text' => '有', 'color' => 'success'],
        'off' => ['value' => 0, 'text' => '无', 'color' => 'danger'],
    ];

    public function index(Content $content)
    {
        return $content
            ->header('货道')
            ->description('列表')
            ->body($this->grid());
    }

    public function edit($id, Content $content)
    {
        return $content
            ->header('货道')
            ->description('编辑')
            ->body($this->form()->edit($id));
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new VendingMachineAisle);

        $grid->model()->with(['product', 'vendingMachine'])->orderBy('stock', 'asc');

        $grid->column('id', __('ID'))->sortable();
        $grid->column('vendingMachine.name', __('售卖机名称'));
        $grid->column('product.title', __('商品名称'));
        $grid->column('ordinal', __('货道号'));
        $grid->column('stock', __('库存'))->editable()->sortable();
        $grid->column('max_stock', __('最大库存'))->editable();
        $grid->column('preferential_price', __('优惠价'))->editable()->sortable();
        $grid->column('is_lead_rail', __('导轨'))->switch($this->lead_rail);
        $grid->column('is_opened', __('状态'))->switch($this->is_opened)->sortable();
//        $grid->column('created_at', __('Created at'));
//        $grid->column('updated_at', __('Updated at'));

        $grid->actions(function ($actions) {
            // 不在每一行后面展示查看按钮
            $actions->disableView();
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
            });
            $filter->column(1/2, function ($filter) {
                $filter->like('vendingMachine.name', '售卖机名称');
                $filter->like('product.title', '商品名称');
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
//        $show = new Show(VendingMachineAisle::findOrFail($id));
//
//        $show->field('id', __('Id'));
//        $show->field('ordinal', __('Ordinal'));
//        $show->field('stock', __('Stock'));
//        $show->field('max_stock', __('Max stock'));
//        $show->field('preferential_price', __('Preferential price'));
//        $show->field('is_lead_rail', __('Is lead rail'));
//        $show->field('is_opened', __('Is opened'));
//        $show->field('vending_machine_id', __('Vending machine id'));
//        $show->field('product_id', __('Product id'));
//        $show->field('created_at', __('Created at'));
//        $show->field('updated_at', __('Updated at'));
//
//        return $show;
//    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new VendingMachineAisle);

        $form->number('ordinal', __('货道号'));
        $form->number('stock', __('库存'));
        $form->number('max_stock', __('最大库存'));
        $form->currency('preferential_price', __('优惠价'))->rules('numeric')->placeholder('优惠价')->symbol('<i class="fa fa-rmb fa-fw"></i>')->default(0);
        $form->switch('is_lead_rail', __('导轨'))->states($this->lead_rail)->default(false);
        $form->switch('is_opened', __('状态'))->states($this->is_opened)->default(true);
        $form->select('product_id', '商品')->options(function ($id) {
            $product = Product::find($id);
            if ($product) {
                return [$product->id => $product->title];
            }
        })->ajax('/admin/api/products');

        $form->tools(function (Form\Tools $tools) {
            // 去掉`查看`按钮
            $tools->disableView();
            $tools->disableDelete();
        });

        $form->footer(function ($footer) {
            // 去掉`查看`checkbox
            $footer->disableViewCheck();
        });

        return $form;
    }
}
