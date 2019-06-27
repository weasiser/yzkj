<?php

namespace App\Admin\Controllers;

use App\Models\Product;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;
use Encore\Admin\Widgets\Table;
//use Ichynul\RowTable\TableRow;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductsController extends Controller
{
    use HasResourceActions;

    protected $on_sale = [
        'on'  => ['value' => 1, 'text' => '已上架', 'color' => 'success'],
        'off' => ['value' => 0, 'text' => '已下架', 'color' => 'danger'],
    ];

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
//        $grid->model()->with('pes');

        $grid->id('ID')->sortable();
        $grid->column('title', '名称')->editable();
        $grid->image('缩略图')->image('', 50, 50);
        $grid->buying_price('进货价')->editable();
        $grid->selling_price('销售价')->editable();
        $grid->quality_guarantee_period('保质期（月）')->editable();
        $grid->total_stock('总库存')->sortable();
        $grid->sold_count('销量（件）')->sortable();
        $grid->sold_value('销售额')->sortable();
        $grid->sold_profit('利润')->sortable();
        $grid->min_expiration_date('最小有效日期')->sortable()
            ->expand(function ($model) {
                $pes = $model->pes()->get()->map(function ($pes) {
                    return $pes->only(['production_date', 'expiration_date', 'stock', 'created_at']);
                });
                return new Table(['生产日期', '有效日期', '库存', '创建时间'], $pes->toArray());
            });
        $grid->on_sale('上下架')->switch($this->on_sale);

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

        $grid->filter(function($filter){
            $filter->column(1/2, function ($filter) {
                $filter->in('on_sale', '上下架')->checkbox([
                    true => '上架',
                    false => '下架',
                ]);
            });
            $filter->column(1/2, function ($filter) {
                $filter->like('title', '名称');
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

//        $headers = ['名称', '进货价', '销售价', '保质期（月）'];
//        $tableRow = new TableRow();
//        $tableRow->text('title', '名称')->required()->placeholder('名称');
//        $tableRow->currency('buying_price', '进货价')->required()->rules('numeric|min:0.01')->placeholder('进货价')->symbol('<i class="fa fa-rmb fa-fw"></i>');
//        $tableRow->currency('selling_price', '销售价')->required()->rules('numeric|min:0.01')->placeholder('销售价')->symbol('<i class="fa fa-rmb fa-fw"></i>');
//        $tableRow->number('quality_guarantee_period', '保质期（月）')->required()->rules('integer|min:1')->placeholder('保质期（月）')->default(0);
//        $form->rowtable('商品信息：')->setHeaders($headers)->setRows([$tableRow]);

        $form->rowtable('商品信息：', function ($table) {
            $table->row(function ($row) {
                $row->text('title', '名称')->required()->placeholder('名称');
                $row->currency('buying_price', '进货价')->required()->rules('numeric|min:0.01')->placeholder('进货价')->symbol('<i class="fa fa-rmb fa-fw"></i>');
                $row->currency('selling_price', '销售价')->required()->rules('numeric|min:0.01')->placeholder('销售价')->symbol('<i class="fa fa-rmb fa-fw"></i>');
                $row->number('quality_guarantee_period', '保质期（月）')->required()->rules('integer|min:1')->placeholder('保质期（月）')->default(0);
                $row->switch('on_sale', '上下架')->states($this->on_sale)->default(true);
            });
            //$table->useDiv(false);
            $table->setHeaders(['名称', '进货价', '销售价', '保质期（月）', '上下架']);
            //$table->useDiv(false);
            //$table->headersTh(true);//使用table时 头部使用<th></th>，默认使用<td></td>样式有些差别
            //$table->getTableWidget()//extends Encore\Admin\Widgets\Table
            //->offsetSet("style", "width:1000px;");
        });

        $form->image('image', '图片：')->rules('image')->required();

        $form->hasMany('pes', '日期库存列表：', function (Form\NestedForm $form) {
            $form->text('production_date', '生产日期')->icon('fa-calendar')->required()->placeholder('生产日期')->attribute(['type' => 'date', 'style' => 'width: 150px', 'min' => '2000-01-01', 'max' => '2099-12-31']);
            $form->text('expiration_date', '有效日期')->icon('fa-calendar')->required()->placeholder('有效日期')->readonly()->attribute(['type' => 'date', 'style' => 'width: 150px']);
            $form->number('stock', '库存')->required()->rules('integer|min:0')->placeholder('库存');
            $form->datetime('created_at', '创建时间')->disable()->placeholder('无需输入，自动生成');
        })->mode('table');

        $form->html(view('admin.utils.product_edit'));

        $form->saving(function (Form $form) {
            if ($form->input('pes')) {
                $form->model()->min_expiration_date = collect($form->input('pes'))->where(Form::REMOVE_FLAG_NAME, 0)->min('expiration_date') ?: NULL;
                $form->model()->total_stock = collect($form->input('pes'))->where(Form::REMOVE_FLAG_NAME, 0)->sum('stock') ?: 0;
            }
        });

        $form->tools(function (Form\Tools $tools) {
            // 去掉`查看`按钮
            $tools->disableView();
        });

        $form->footer(function ($footer) {
            // 去掉`查看`checkbox
            $footer->disableViewCheck();
        });

        return $form;
    }

    // 定义商品下拉框搜索接口
    public function apiIndex(Request $request)
    {
        // 用户输入的值通过 q 参数获取
        $search = $request->input('q');
        $qgp = boolval($request->input('qgp', false));
        $result = Product::query()
//            ->where('on_sale', true)
            ->where('title', 'like', '%'.$search.'%')
            ->where('on_sale', '=', true)
            ->paginate();

        // 把查询出来的结果重新组装成 Laravel-Admin 需要的格式
        $result->setCollection($result->getCollection()->map(function (Product $product) use ($qgp) {
            if ($qgp) {
                return ['id' => $product->id, 'text' => $product->title . '<span class="quality_guarantee_period_note">保质期：<span id="quality_guarantee_period">' . $product->quality_guarantee_period . '</span>个月</span>'];
            }
            return ['id' => $product->id, 'text' => $product->title];
        }));

        return $result;
    }
}
