<?php

namespace App\Admin\Controllers;

use App\Models\Product;
use App\Models\ProductPes;
use App\Models\Warehouse;
use Encore\Admin\Admin;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;

class ProductPesController extends AdminController
{
    protected $is_sold_out_checked = [
        'on' => ['value' => 1, 'text' => '已检查', 'color' => 'success'],
        'off' => ['value' => 0, 'text' => '未检查', 'color' => 'danger'],
    ];

    /**
     * Title for current resource.
     *
     * @var string
     */
//    protected $title = 'App\Models\ProductPes';

    public function index(Content $content)
    {
        return $content
            ->header('日期库存')
            ->description('列表')
            ->body($this->grid());
    }

    public function edit($id, Content $content)
    {
        return $content
            ->header('日期库存')
            ->description('编辑')
            ->body($this->form()->edit($id));
    }

    public function create(Content $content)
    {
        return $content
            ->header('日期库存')
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
        $grid = new Grid(new ProductPes);

        $grid->model()->with(['product', 'warehouse']);

        $grid->column('id', __('ID'))->sortable();
        $grid->column('product.title', __('商品名称'));
        $grid->column('warehouse.name', '仓库')->label('primary');
        $grid->column('production_date', __('生产日期'))->sortable();
        $grid->column('expiration_date', __('有效日期'))->sortable();
        $grid->column('stock', __('库存'))->editable()->sortable();
        $grid->column('registered_stock', __('登记库存'))->editable()->sortable();
        $grid->column('is_sold_out_checked', __('售完过期检查'))->switch($this->is_sold_out_checked);
        $grid->column('created_at', __('创建时间'));
        $grid->column('updated_at', __('更新时间'));

        $grid->actions(function ($actions) {
            // 不在每一行后面展示查看按钮
            $actions->disableView();
        });

        $grid->filter(function ($filter) {
            $filter->column(1 / 2, function ($filter) {
                $filter->in('warehouse_id', '仓库')->multipleSelect(Warehouse::all()->pluck('name', 'id'));
            });
            $filter->column(1 / 2, function ($filter) {
                $filter->like('product.title', '商品名称');
                $filter->in('is_sold_out_checked', '售完过期检查')->checkbox([
                    false => '未检查',
                    true => '已检查',
                ]);
            });
//            $filter->expand();
        });

        $grid->quickCreate(function (Grid\Tools\QuickCreate $create) {
            $create->select('product_id', '选择商品')->options(function ($id) {
                $product = Product::find($id);
                if ($product) {
                    return [$product->id => $product->title . '<span class="quality_guarantee_period_note">保质期：<span id="quality_guarantee_period">' . $product->quality_guarantee_period . '</span>个月</span>'];
                }
            })->ajax('/admin/api/products?qgp=1')->required();
            $create->select('warehouse_id', '仓库')->options(Warehouse::all()->pluck('name', 'id'))->required();
            $create->text('production_date', '生产日期')->icon('fa-calendar')->required()->placeholder('生产日期')->attribute(['type' => 'date', 'style' => 'width: 150px', 'min' => '2000-01-01', 'max' => '2099-12-31']);
            $create->text('expiration_date', '有效日期')->icon('fa-calendar')->required()->readonly()->placeholder('有效日期')->attribute(['type' => 'date', 'style' => 'width: 150px', 'min' => '2000-01-01', 'max' => '2099-12-31']);
            $create->integer('stock', '库存')->required()->rules('integer|min:0')->placeholder('库存')->attribute(['style' => 'width: 50px']);
            $create->integer('registered_stock', '登记库存')->readonly()->required()->rules('integer|min:1')->placeholder('登记库存')->attribute(['style' => 'width: 70px']);
        });

        $script = <<<EOT
  $(function() {
    $('.create-form').on('change', '#production_date', function () {
      let quality_guarantee_period = parseInt($('#quality_guarantee_period').text())
      let beforeDate = $(this)
      let complete = function(n){
        return (n>9) ? n : '0' + n;
      }
      let this_production_date = new Date(beforeDate.val())
      this_production_date.setMonth(this_production_date.getMonth() + quality_guarantee_period)
      let this_year = this_production_date.getFullYear()
      let this_month = complete(this_production_date.getMonth() + 1)
      let this_day = complete(this_production_date.getDate())
      let this_expiration_date = (this_year+'-'+this_month+'-'+this_day)
      beforeDate.parents('.create-form').find('#expiration_date').val(this_expiration_date)
    })
    $('.create-form').on('input', '#stock', function () {
      $('#registered_stock').val($('#stock').val())
    })
    $('span.select2-selection.select2-selection--single').first().css('cssText','width: 400px !important')
  })
EOT;

        Admin::script($script);

        Admin::style('.quality_guarantee_period_note {margin-left: 10px;}');

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
//        $show = new Show(ProductPes::findOrFail($id));
//
//        $show->field('id', __('Id'));
//        $show->field('production_date', __('Production date'));
//        $show->field('expiration_date', __('Expiration date'));
//        $show->field('stock', __('Stock'));
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
        $form = new Form(new ProductPes);

        $form->select('product_id', '商品')->options(function ($id) {
            $product = Product::find($id);
            if ($product) {
                return [$product->id => $product->title . '<span class="quality_guarantee_period_note">保质期：<span id="quality_guarantee_period">' . $product->quality_guarantee_period . '</span>个月</span>'];
            }
        })->ajax('/admin/api/products?qgp=1')->required();
        $form->select('warehouse_id', '仓库')->options(Warehouse::all()->pluck('name', 'id'))->required();
        $form->text('production_date', '生产日期')->icon('fa-calendar')->required()->placeholder('生产日期')->attribute(['type' => 'date', 'style' => 'width: 150px', 'min' => '2000-01-01', 'max' => '2099-12-31']);
        $form->text('expiration_date', '有效日期')->icon('fa-calendar')->required()->placeholder('有效日期')->readonly()->attribute(['type' => 'date', 'style' => 'width: 150px']);
        $form->number('stock', '库存')->required()->rules('integer')->placeholder('库存');
        $form->number('registered_stock', '登记库存')->required()->rules('integer|min:1')->placeholder('登记库存');
        $form->switch('is_sold_out_checked', '售完过期检查')->states($this->is_sold_out_checked)->default(false);

        $form->tools(function (Form\Tools $tools) {
            // 去掉`查看`按钮
            $tools->disableView();
        });

        $form->saved(function (Form $form) {
            $product = $form->model()->product;
            $this->offsetStock($product);
            $this->updateTotalStockAndMinExpirationDate($product);
        });

        $form->deleted(function (Form $form) {
            $product = $form->model()->product;
            $this->offsetStock($product);
            $this->updateTotalStockAndMinExpirationDate($product);
        });

        $form->html(view('admin.utils.product_pes_edit'));

        $form->footer(function ($footer) {
            // 去掉`查看`checkbox
            $footer->disableViewCheck();
        });

        return $form;
    }

    public function apiStock(Warehouse $warehouse)
    {
        $result = $warehouse->productPes;

        return $result;
    }

    protected function offsetStock(Product $product)
    {
        $productPes = $product->productPesWithoutSoldOutChecked;
        $negative_stock = $productPes->where('stock', '<', 0)->first();
        if ($negative_stock) {
            $positive_stock = $productPes->where('stock', '>', 0)->first();
            if ($positive_stock && abs($negative_stock->stock) <= $positive_stock->stock) {
                $positive_stock->stock = $negative_stock->stock + $positive_stock->stock;
                $negative_stock->stock = 0;
                $positive_stock->save();
                $negative_stock->save();
            } elseif (abs($negative_stock->stock) > $positive_stock->stock) {
                do {
                    $negative_stock->stock = $negative_stock->stock + $positive_stock->stock;
                    $positive_stock->stock = 0;
                    $positive_stock->save();
                    $negative_stock->save();
                    $positive_stock = $productPes->where('stock', '>', 0)->first();
                } while ($positive_stock && abs($negative_stock->stock) > $positive_stock->stock);
                if ($positive_stock) {
                    $positive_stock->stock = $negative_stock->stock + $positive_stock->stock;
                    $negative_stock->stock = 0;
                    $positive_stock->save();
                    $negative_stock->save();
                }
            }
        }
    }

    protected function updateTotalStockAndMinExpirationDate(Product $product)
    {
        $productPes = $product->productPesWithoutSoldOutChecked;
        $product->min_expiration_date = $productPes->min('expiration_date');
        $total_stock = $productPes->sum('stock');
        $total_registered_stock = $productPes->sum('registered_stock');
        $product->total_stock = $total_stock;
        $product->total_registered_stock = $total_registered_stock;
        $product->warehouse_stock = $total_stock - $product->vending_machine_stock;
        $product->save();
    }
}
