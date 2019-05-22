<?php

namespace App\Admin\Controllers;

use App\Models\VendingMachine;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;
use Ichynul\RowTable\TableRow;

class VendingMachinesController extends Controller
{
    use HasResourceActions;

    protected $states = [
        'on'  => ['value' => 1, 'text' => '已开启', 'color' => 'success'],
        'off' => ['value' => 0, 'text' => '已关闭', 'color' => 'danger'],
    ];

    protected $lead_rail = [
        'on'  => ['value' => 1, 'text' => '有', 'color' => 'success'],
        'off' => ['value' => 0, 'text' => '无', 'color' => 'danger'],
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
            ->header('售货机')
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
            ->header('售货机')
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
            ->header('售货机')
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
        $grid = new Grid(new VendingMachine);

        $grid->id('ID')->sortable();
        $grid->name('名称')->editable();
        $grid->code('机器码')->editable();
        $grid->address('地址')->editable();
        $grid->iot_card_no('物联卡号')->editable();
        $grid->cabinet_id('机柜 ID')->editable();
        $grid->cabinet_type('机柜类型')->editable();
        $grid->is_opened('状态')->switch($this->states);

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
            });
            $filter->column(1/2, function ($filter) {
                $filter->like('name', '名称');
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

        $headers = ['名称', '机器码', '地址', '物联卡号', '机柜 ID', '机柜类型', '状态'];
        $tableRow = new TableRow();
        $tableRow->text('name', '名称')->rules('required')->placeholder('名称')->icon('fa-pencil');
        $tableRow->text('code', '机器码')->rules('required')->placeholder('机器码')->icon('fa-braille');
        $tableRow->text('address', '地址')->placeholder('地址')->icon('fa-map-marker');
        $tableRow->text('iot_card_no', '物联卡号')->placeholder('物联卡号')->icon('fa-microchip');
        $tableRow->number('cabinet_id', '机柜 ID')->rules('required')->default(1)->placeholder('机柜 ID');
        $tableRow->number('cabinet_type', '机柜类型')->rules('required')->default(1)->placeholder('机柜类型');
        $tableRow->switch('is_opened', '状态')->states($this->states)->default(false);
        $form->rowtable('售货机信息')->setHeaders($headers)->setRows([$tableRow]);

        $form->hasMany('aisles', '货道列表', function (Form\NestedForm $form) {
            $form->number('ordinal', '货道号')->rules('required|integer|min:1|max:54')->placeholder('货道号')->attribute(['min' => '1', 'max' => '54']);
            $form->number('stock', '库存')->rules('required|integer|min:0')->placeholder('库存');
            $form->number('max_stock', '最大库存')->rules('required|integer|min:3')->placeholder('最大库存');
            $form->currency('preferential_price', '优惠价')->rules('numeric')->placeholder('优惠价')->symbol('<i class="fa fa-rmb fa-fw"></i>');
            $form->switch('is_lead_rail', '导轨')->states($this->lead_rail)->default(false);
            $form->switch('is_opened', '状态')->states($this->states)->default(true);
        })->mode('table');

        return $form;
    }
}
