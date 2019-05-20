<?php

namespace App\Admin\Controllers;

use App\Models\Product;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;
use Ichynul\RowTable\TableRow;

class ProductsController extends Controller
{
    use HasResourceActions;

    /**
     * Index interface.
     *
     * @param Content $content
     * @return Content
     */
    public function index(Content $content)
    {
        return $content
            ->header('商品')
            ->description('列表')
            ->body($this->grid());
    }

    /**
     * Show interface.
     *
     * @param mixed $id
     * @param Content $content
     * @return Content
     */
//    public function show($id, Content $content)
//    {
//        return $content
//            ->header('商品')
//            ->description('展示')
//            ->body($this->detail($id));
//    }

    /**
     * Edit interface.
     *
     * @param mixed $id
     * @param Content $content
     * @return Content
     */
    public function edit($id, Content $content)
    {
        return $content
            ->header('商品')
            ->description('编辑')
            ->body($this->form()->edit($id));
    }

    /**
     * Create interface.
     *
     * @param Content $content
     * @return Content
     */
    public function create(Content $content)
    {
        return $content
            ->header('商品')
            ->description('创建')
            ->body($this->form());
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Product);

        $grid->id('ID');
        $grid->title('名称')->editable();
        $grid->image('缩略图')->image('', 50, 50);
        $grid->buying_price('进货价')->editable();
        $grid->selling_price('销售价')->editable();
        $grid->quality_guarantee_period('保质期（月）')->editable();
        $grid->total_stock('总库存');
        $grid->sold_count('销量（件）');
        $grid->sold_value('销售额');
        $grid->sold_profit('利润');
        $grid->created_at('创建时间');
//        $grid->updated_at('Updated at');

        $grid->actions(function ($actions) {
            // 不在每一行后面展示查看按钮
            $actions->disableView();
        });

        $grid->tools(function ($tools) {
            // 禁用批量删除按钮
            $tools->batch(function ($batch) {
                $batch->disableDelete();
            });
        });

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
//        $show = new Show(Product::findOrFail($id));
//
//        $show->id('Id');
//        $show->title('Title');
//        $show->image('Image');
//        $show->buying_price('Buying price');
//        $show->selling_price('Selling price');
//        $show->quality_guarantee_period('Quality guarantee period');
//        $show->total_stock('Total stock');
//        $show->sold_count('Sold count');
//        $show->created_at('Created at');
//        $show->updated_at('Updated at');
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
        $form = new Form(new Product);



        $headers = ['名称', '进货价', '销售价', '保质期（月）'];
        $tableRow = new TableRow();
        $tableRow->text('title', '名称')->rules('required')->placeholder('名称');
        $tableRow->decimal('buying_price', '进货价')->rules('required|numeric|min:0.01')->placeholder('进货价');
        $tableRow->decimal('selling_price', '销售价')->rules('required|numeric|min:0.01')->placeholder('销售价');;
        $tableRow->number('quality_guarantee_period', '保质期（月）')->rules('required|integer|min:1')->placeholder('保质期（月）');
        $form->rowtable('商品信息')->setHeaders($headers)->setRows([$tableRow]);
//        $form->text('title', '商品名称')->rules('required');
//        $form->decimal('buying_price', '进货价')->rules('required|numeric|min:0.01');
//        $form->decimal('selling_price', '销售价')->rules('required|numeric|min:0.01');
//        $form->number('quality_guarantee_period', '保质期（月）')->rules('required|integer|min:1');
        $form->image('image', '图片')->rules('required|image', [
            'required' => '请上传图片'
        ]);

        $form->hasMany('pes', '日期库存列表', function (Form\NestedForm $form) {
            $form->date('production_date', '生产日期')->rules('required')->placeholder('生产日期');
            $form->date('expiration_date', '有效日期')->rules('required')->placeholder('有效日期');
            $form->number('stock', '库存')->rules('required|integer|min:0')->placeholder('库存');
        });

        return $form;
    }
}
