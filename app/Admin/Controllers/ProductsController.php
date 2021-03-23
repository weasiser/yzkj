<?php

namespace App\Admin\Controllers;

use App\Models\Product;
use App\Http\Controllers\Controller;
use App\Models\Warehouse;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;
use Illuminate\Http\Request;

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

        $grid->id('ID')->sortable();
        $grid->column('title', '名称')->editable();
        $grid->column('image', '缩略图')->display(function ($image) {
            return config('filesystems.disks.oss.cdnDomain') ? config('filesystems.disks.oss.cdnDomain') . '/' . $image . '-adminProductList' : '/' . $image;
        })->image('', 50, 50);
        $grid->buying_price('进货价')->editable();
        $grid->selling_price('销售价')->editable();
        $grid->column('market_price', '市场价')->editable();
        $grid->column('promotion_price', '促销优惠')->editable()->sortable();
        $grid->quality_guarantee_period('保质期（月）')->editable()->sortable();
//        $grid->column('warehouse_stock', '仓库库存')->sortable();
        $grid->column('vending_machine_stock', '售卖机库存')->sortable();
//        $grid->total_stock('总库存')->sortable();
//        $grid->column('total_registered_stock', '登记库存')->sortable();
        $grid->sold_count('销量（件）')->sortable();
        $grid->sold_value('销售额')->sortable();
        $grid->sold_profit('利润')->sortable();
//        $grid->min_expiration_date('最小有效日期')->sortable();
//            ->expand(function ($model) {
//                $productPes = $model->productPes()->get()->map(function ($productPes) {
//                    return $productPes->only(['production_date', 'expiration_date', 'stock', 'created_at']);
//                });
//                return new Table(['生产日期', '有效日期', '库存', '创建时间'], $productPes->toArray());
//            });
        $grid->on_sale('上下架')->switch($this->on_sale);

        $grid->actions(function ($actions) {
            // 不在每一行后面展示查看按钮
            $actions->disableView();
            // 不在每一行后面展示删除按钮
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
                $filter->in('on_sale', '上下架')->checkbox([
                    true => '上架',
                    false => '下架',
                ]);
            });
            $filter->column(1/2, function ($filter) {
                $filter->like('title', '名称');
//                $filter->equal('productPes.warehouse_id', '仓库')->select(Warehouse::all()->pluck('name', 'id'));
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

        $form->model()->where('is_sold_out_checked', false);

//        $headers = ['名称', '进货价', '销售价', '保质期（月）'];
//        $tableRow = new TableRow();
//        $tableRow->text('title', '名称')->required()->placeholder('名称');
//        $tableRow->currency('buying_price', '进货价')->required()->rules('numeric|min:0.01')->placeholder('进货价')->symbol('<i class="fa fa-rmb fa-fw"></i>');
//        $tableRow->currency('selling_price', '销售价')->required()->rules('numeric|min:0.01')->placeholder('销售价')->symbol('<i class="fa fa-rmb fa-fw"></i>');
//        $tableRow->number('quality_guarantee_period', '保质期（月）')->required()->rules('integer|min:1')->placeholder('保质期（月）')->default(0);
//        $form->rowtable('商品信息：')->setHeaders($headers)->setRows([$tableRow]);

        $form->tab('商品信息', function ($form) {

            $form->text('title', '名称')->required()->placeholder('名称');
            $form->currency('buying_price', '进货价')->required()->rules('numeric|min:0.01')->placeholder('进货价')->symbol('<i class="fa fa-rmb fa-fw"></i>');
            $form->currency('selling_price', '销售价')->required()->rules('numeric|min:0.01')->placeholder('销售价')->symbol('<i class="fa fa-rmb fa-fw"></i>');
            $form->currency('market_price', '市场价')->rules('numeric|min:0.00')->placeholder('市场价')->symbol('<i class="fa fa-rmb fa-fw"></i>')->default('0.00');
            $form->currency('promotion_price', '促销优惠')->rules('numeric')->placeholder('促销优惠')->symbol('<i class="fa fa-rmb fa-fw"></i>')->default('0.00');
            $form->number('quality_guarantee_period', '保质期（月）')->required()->rules('integer|min:1')->placeholder('保质期（月）')->default(0);
            $form->switch('on_sale', '上下架')->states($this->on_sale)->default(true);
            $form->image('image', '图片')->attribute('accept', 'image/gif, image/jpeg, image/png')->required();

        })->tab('日期库存', function ($form) {

            $form->hasMany('productpeswithoutsoldoutchecked', '日期库存列表：', function (Form\NestedForm $form) {
                $form->select('warehouse_id', '仓库')->options(Warehouse::all()->pluck('name', 'id'));
                $form->text('production_date', '生产日期')->icon('fa-calendar')->required()->placeholder('生产日期')->attribute(['type' => 'date', 'style' => 'width: 150px', 'min' => '2000-01-01', 'max' => '2099-12-31']);
                $form->text('expiration_date', '有效日期')->icon('fa-calendar')->required()->placeholder('有效日期')->readonly()->attribute(['type' => 'date', 'style' => 'width: 150px']);
                $form->number('stock', '库存')->required()->rules('integer|min:0')->placeholder('库存')->attribute(['style' => 'width: 50px']);
                $form->number('registered_stock', '登记库存')->required()->rules('integer|min:1')->placeholder('登记库存')->attribute(['style' => 'width: 50px']);
                $form->datetime('created_at', '创建时间')->disable()->placeholder('无需输入，自动生成');
            })->mode('table');

            $form->html(view('admin.utils.product_edit'));

        });

//        $form->setWidth(8, 2);

//        $form->rowtable('商品信息：', function ($table) {
//            $table->row(function ($row) {
//                $row->text('title', '名称')->required()->placeholder('名称');
//                $row->currency('buying_price', '进货价')->required()->rules('numeric|min:0.01')->placeholder('进货价')->symbol('<i class="fa fa-rmb fa-fw"></i>');
//                $row->currency('selling_price', '销售价')->required()->rules('numeric|min:0.01')->placeholder('销售价')->symbol('<i class="fa fa-rmb fa-fw"></i>');
//                $row->number('quality_guarantee_period', '保质期（月）')->required()->rules('integer|min:1')->placeholder('保质期（月）')->default(0);
//                $row->switch('on_sale', '上下架')->states($this->on_sale)->default(true);
//            });
//            //$table->useDiv(false);
//            $table->setHeaders(['名称', '进货价', '销售价', '保质期（月）', '上下架']);
//            //$table->useDiv(false);
//            //$table->headersTh(true);//使用table时 头部使用<th></th>，默认使用<td></td>样式有些差别
//            //$table->getTableWidget()//extends Encore\Admin\Widgets\Table
//            //->offsetSet("style", "width:1000px;");
//        });
//
//        $form->image('image', '图片：')->rules('image')->required()->value();
//
//        $form->hasMany('pes', '日期库存列表：', function (Form\NestedForm $form) {
//            $form->text('production_date', '生产日期')->icon('fa-calendar')->required()->placeholder('生产日期')->attribute(['type' => 'date', 'style' => 'width: 150px', 'min' => '2000-01-01', 'max' => '2099-12-31']);
//            $form->text('expiration_date', '有效日期')->icon('fa-calendar')->required()->placeholder('有效日期')->readonly()->attribute(['type' => 'date', 'style' => 'width: 150px']);
//            $form->number('stock', '库存')->required()->rules('integer|min:0')->placeholder('库存');
//            $form->datetime('created_at', '创建时间')->disable()->placeholder('无需输入，自动生成');
//        })->mode('table');

//        $form->html(view('admin.utils.product_edit'));

//        $form->saving(function (Form $form) {
//            if ($form->input('productpes')) {
//                $form->model()->min_expiration_date = collect($form->input('productpes'))->where(Form::REMOVE_FLAG_NAME, 0)->min('expiration_date') ?: NULL;
//                $form->model()->total_stock = collect($form->input('productpes'))->where(Form::REMOVE_FLAG_NAME, 0)->sum('stock') ?: 0;
//            }
//        });

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
            ->where('title', 'like', '%'.$search.'%')
            ->where('on_sale', '=', true)
            ->paginate(10);

        // 把查询出来的结果重新组装成 Laravel-Admin 需要的格式
        $result->setCollection($result->getCollection()->map(function (Product $product) use ($qgp) {
            if ($qgp) {
                return ['id' => $product->id, 'text' => $product->title . '<span class="quality_guarantee_period_note">保质期：<span id="quality_guarantee_period">' . $product->quality_guarantee_period . '</span>个月</span>'];
            }
            return ['id' => $product->id, 'text' => $product->title];
        }));

        return $result;
    }

    public function apiAll(Request $request)
    {
        $qgp = boolval($request->input('qgp', false));
        $result = Product::where('on_sale', '=', true)->get()->map(function (Product $product) use ($qgp) {
            if ($qgp) {
                return ['id' => $product->id, 'text' => $product->title . '<span class="quality_guarantee_period_note">保质期：<span id="quality_guarantee_period">' . $product->quality_guarantee_period . '</span>个月</span>'];
            }
            return ['id' => $product->id, 'text' => $product->title];
        });

        // 把查询出来的结果重新组装成 Laravel-Admin 需要的格式
//        $result->setCollection($result->getCollection()->map(function (Product $product) use ($qgp) {
//            if ($qgp) {
//                return ['id' => $product->id, 'text' => $product->title . '<span class="quality_guarantee_period_note">保质期：<span id="quality_guarantee_period">' . $product->quality_guarantee_period . '</span>个月</span>'];
//            }
//            return ['id' => $product->id, 'text' => $product->title];
//        }));

        return $result;
    }
}
