<?php

namespace App\Admin\Controllers;

use App\Models\User;
use App\Models\Warehouse;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;
use Illuminate\Http\Request;

class WarehousesController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
//    protected $title = 'App\Models\Warehouse';

    public function index(Content $content)
    {
        return $content
            ->header('仓库')
            ->description('列表')
            ->body($this->grid());
    }

    public function edit($id, Content $content)
    {
        return $content
            ->header('仓库')
            ->description('编辑')
            ->body($this->form()->edit($id));
    }

    public function show($id, Content $content)
    {
        return $content
            ->header('仓库')
            ->description('详情')
            ->body($this->detail($id));
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Warehouse);

        $grid->column('id', __('ID'))->sortable();
        $grid->column('name', __('名称'))->editable();
        $grid->column('address', __('地址'))->editable();
        $grid->column('managers', __('管理员'))->display(function ($managers) {
            $managers = array_map(function ($manager) {
                return "<span class='label label-primary'>{$manager['nick_name']}</span>";
            }, $managers);

            return join('&nbsp;&nbsp;', $managers);
        });
        $grid->column('vendingmachines', __('售卖机'))->display(function ($vendingMachines) {
            $vendingMachines = array_map(function ($vendingMachine) {
                return "<span class='label label-primary'>{$vendingMachine['name']}</span>";
            }, $vendingMachines);

            return join('&nbsp;&nbsp;', $vendingMachines);
        });
//        $grid->column('created_at', __('Created at'));
//        $grid->column('updated_at', __('Updated at'));

        $grid->actions(function ($actions) {
            // 不在每一行后面展示查看按钮
//            $actions->disableView();
            $actions->disableDelete();
        });

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
                $filter->like('name', '名称');
                $filter->like('address', '地址');
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
    protected function detail($id)
    {
        $show = new Show(Warehouse::findOrFail($id));

        $warehouse = $show->getModel();
//        $show->panel()->title('ID：' . $warehouse->id . '，' . '名称：' . $warehouse->name . '，' . '地址：' . $warehouse->address);

        $show->field('id', __('ID'));
        $show->field('name', __('名称'));
        $show->field('address', __('地址'));
//        $show->field('created_at', __('创建时间'));
//        $show->field('updated_at', __('更新时间'));

        $show->productStock('商品库存', function ($productStock) {

//            $productPes->resource('productPes');

//            $productStock->column('id', __('ID'));
            $productStock->column('title', '商品名称');
            $productStock->column('warehouse_stock', __('仓库库存'))->sortable();
            $productStock->column('vending_machine_stock', __('售卖机库存'))->sortable();
            $productStock->column('total_stock', __('总库存'))->sortable();

            $productStock->disableActions();
            $productStock->disableCreateButton();

            $productStock->filter(function($filter){
                // 去掉默认的id过滤器
                $filter->disableIdFilter();

                // 在这里添加字段过滤器
                $filter->like('title', '商品名称');
            });
        });

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new Warehouse);

        $form->text('name', __('名称'))->required();
        $form->text('address', __('地址'));
        $form->multipleSelect('managers', '管理员')->options(function ($ids) {
            if ($ids) {
                return User::find($ids)->pluck('nick_name', 'id');
            }
        })->ajax('/admin/api/users');

//        $form->hasMany('vendingmachines', '售卖机列表：', function (Form\NestedForm $form) {
//        });

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
