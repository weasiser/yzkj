<?php

namespace App\Admin\Controllers;

use App\Handlers\VendingMachineDeliverAndQuery;
use App\Models\Product;
use App\Models\VendingMachine;
use App\Http\Controllers\Controller;
use App\Models\Warehouse;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;

class VendingMachinesController extends Controller
{
    use HasResourceActions;

    protected $is_opened = [
        'on'  => ['value' => 1, 'text' => '已开启', 'color' => 'success'],
        'off' => ['value' => 0, 'text' => '已关闭', 'color' => 'danger'],
    ];

    protected $lead_rail = [
        'on'  => ['value' => 1, 'text' => '有', 'color' => 'success'],
        'off' => ['value' => 0, 'text' => '无', 'color' => 'danger'],
    ];

    protected $is_delivering = [
        'on'  => ['value' => 1, 'text' => '是', 'color' => 'success'],
        'off' => ['value' => 0, 'text' => '否', 'color' => 'danger'],
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
            ->header('售卖机')
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
//            ->header('Detail')
//            ->description('description')
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
            ->header('售卖机')
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
            ->header('售卖机')
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
        $vmInfo = app(VendingMachineDeliverAndQuery::class)->queryMachineInfo();
//        dd($vmInfo);
        $vmNetStat = [];
        foreach ($vmInfo['data'] as $value) {
            $vmNetStat[$value['machineUuid']] = $value['netStat'];
        }

        $grid = new Grid(new VendingMachine);

        $grid->model()->with(['warehouse']);

        $grid->id('ID')->sortable();
        $grid->name('名称')->editable();
        $grid->code('机器码')->editable();
        $grid->address('地址')->editable();
        $grid->column('warehouse.name', '仓库')->label('primary');
        $grid->iot_card_no('物联卡号')->editable();
        $grid->column('aisle_type', '货道类型')->editable();
        $grid->cabinet_id('机柜 ID')->editable();
        $grid->cabinet_type('机柜类型')->editable();
        $grid->sold_count('销量（件）')->sortable();
        $grid->sold_value('销售额')->sortable();
        $grid->sold_profit('利润')->sortable();
        $grid->is_delivering('正在出货')->switch($this->is_delivering);
        $grid->is_opened('使用状态')->switch($this->is_opened);
        $grid->column('在离线')->display(function () use ($vmNetStat) {
            return $vmNetStat[$this->code] === 1 ? '<span class="label label-success">在线</span>' : '<span class="label label-danger">离线</span>';
        });

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
            });
            $filter->column(1/2, function ($filter) {
                $filter->like('name', '名称');
                $filter->in('warehouse_id', '仓库')->multipleSelect(Warehouse::all()->pluck('name', 'id'));
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
//        $show = new Show(VendingMachine::findOrFail($id));
//
//        $show->id('Id');
//        $show->name('Name');
//        $show->code('Code');
//        $show->address('Address');
//        $show->iot_card_no('Iot card no');
//        $show->is_opened('Is opened');
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
        $form = new Form(new VendingMachine);

//        $headers = ['名称', '机器码', '地址', '物联卡号', '机柜 ID', '机柜类型', '状态'];
//        $tableRow = new TableRow();
//        $tableRow->text('name', '名称')->rules('required')->placeholder('名称')->icon('fa-pencil');
//        $tableRow->text('code', '机器码')->rules('required')->placeholder('机器码')->icon('fa-braille');
//        $tableRow->text('address', '地址')->placeholder('地址')->icon('fa-map-marker');
//        $tableRow->text('iot_card_no', '物联卡号')->placeholder('物联卡号')->icon('fa-microchip');
//        $tableRow->number('cabinet_id', '机柜 ID')->rules('required')->default(1)->placeholder('机柜 ID')->attribute(['style' => 'width: 50px']);
//        $tableRow->number('cabinet_type', '机柜类型')->rules('required')->default(1)->placeholder('机柜类型')->attribute(['style' => 'width: 50px']);
//        $tableRow->switch('is_opened', '状态')->states($this->is_opened)->default(false);
//        $form->rowtable('售卖机信息')->setHeaders($headers)->setRows([$tableRow]);

        $form->tab('售卖机信息', function ($form) {

            $form->text('name', '名称')->required()->placeholder('名称');
            $form->select('warehouse_id', '仓库')->options(Warehouse::all()->pluck('name', 'id'))->required();
            $form->text('code', '机器码')->required()->placeholder('机器码')->icon('fa-braille');
            $form->text('address', '地址')->placeholder('地址')->icon('fa-map-marker');
            $form->text('iot_card_no', '物联卡号')->placeholder('物联卡号')->icon('fa-microchip');
            $form->number('aisle_type', '货道类型')->required()->default(1)->placeholder('货道类型')->help('0表示一行9条货道，1表示一行10条货道');;
            $form->number('cabinet_id', '机柜 ID')->required()->default(1)->placeholder('机柜 ID');
            $form->number('cabinet_type', '机柜类型')->required()->default(1)->placeholder('机柜类型');
            $form->switch('is_delivering', '正在出货')->states($this->is_delivering)->default(false);
            $form->switch('is_opened', '使用状态')->states($this->is_opened)->default(false);

        })->tab('货道信息', function ($form) {

            $products = Product::all();

            $form->hasMany('vendingmachineaisles', '货道列表：', function (Form\NestedForm $form) use ($products) {
                $form->number('ordinal', '货道号')->required()->rules('integer|min:1|max:60')->placeholder('货道号')->attribute(['min' => '1', 'max' => '60', 'style' => 'width: 50px']);
                $form->number('stock', '库存')->required()->rules('integer|min:0')->placeholder('库存')->default(5)->attribute(['style' => 'width: 50px']);
                $form->number('max_stock', '最大库存')->required()->rules('integer|min:3')->placeholder('最大库存')->default(5)->attribute(['style' => 'width: 50px']);
                $form->select('product_id', '商品')->options(function ($id) use ($products) {
                    $product = $products->find($id);
                    if ($product) {
                        return [$product->id => $product->title];
                    }
                })->ajax('/admin/api/products');
                $form->currency('preferential_price', '优惠价')->rules('numeric')->placeholder('优惠价')->symbol('<i class="fa fa-rmb fa-fw"></i>')->default(0)->attribute(['style' => 'width: 60px']);
                $form->switch('is_lead_rail', '导轨')->states($this->lead_rail)->default(false);
                $form->switch('is_opened', '状态')->states($this->is_opened)->default(true);
            })->mode('table');

        });

        $form->setWidth(9, 1);

//        $form->rowtable('售卖机信息：', function ($table) {
//            $table->row(function ($row) {
//                $row->text('name', '名称')->required()->placeholder('名称');
//                $row->text('code', '机器码')->required()->placeholder('机器码')->icon('fa-braille');
//                $row->text('address', '地址')->placeholder('地址')->icon('fa-map-marker');
//                $row->text('iot_card_no', '物联卡号')->placeholder('物联卡号')->icon('fa-microchip');
//                $row->number('cabinet_id', '机柜 ID')->required()->default(1)->placeholder('机柜 ID')->attribute(['style' => 'width: 50px']);
//                $row->number('cabinet_type', '机柜类型')->required()->default(1)->placeholder('机柜类型')->attribute(['style' => 'width: 50px']);
//                $row->switch('is_opened', '状态')->states($this->is_opened)->default(false);
//            });
//            //$table->useDiv(false);
//            $table->setHeaders(['名称', '机器码', '地址', '物联卡号', '机柜 ID', '机柜类型', '状态']);
//            //$table->useDiv(false);
//            //$table->headersTh(true);//使用table时 头部使用<th></th>，默认使用<td></td>样式有些差别
//            //$table->getTableWidget()//extends Encore\Admin\Widgets\Table
//            //->offsetSet("style", "width:1000px;");
//        });
//
//        $products = Product::all();
//
//        $form->hasMany('aisles', '货道列表：', function (Form\NestedForm $form) use ($products) {
//            $form->number('ordinal', '货道号')->required()->rules('integer|min:1|max:54')->placeholder('货道号')->attribute(['min' => '1', 'max' => '54', 'style' => 'width: 50px']);
//            $form->number('stock', '库存')->required()->rules('integer|min:0')->placeholder('库存')->default(5)->attribute(['style' => 'width: 50px']);
//            $form->number('max_stock', '最大库存')->required()->rules('integer|min:3')->placeholder('最大库存')->default(5)->attribute(['style' => 'width: 50px']);
//            $form->select('product_id', '商品')->options(function ($id) use ($products) {
//                $product = $products->find($id);
//                if ($product) {
//                    return [$product->id => $product->title];
//                }
//            })->ajax('/admin/api/products');
//            $form->currency('preferential_price', '优惠价')->rules('numeric')->placeholder('优惠价')->symbol('<i class="fa fa-rmb fa-fw"></i>')->default(0)->attribute(['style' => 'width: 60px']);
//            $form->switch('is_lead_rail', '导轨')->states($this->lead_rail)->default(false);
//            $form->switch('is_opened', '状态')->states($this->is_opened)->default(true);
//        })->mode('table');

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
}
