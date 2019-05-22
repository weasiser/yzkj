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
        $grid->name('名称');
        $grid->code('机器码');
        $grid->address('地址');
        $grid->iot_card_no('物联卡号');
        $states = [
            'on'  => ['value' => 1, 'text' => '已开启', 'color' => 'success'],
            'off' => ['value' => 2, 'text' => '已关闭', 'color' => 'danger'],
        ];
        $grid->is_opened('状态')->switch($states);
//        $grid->is_opened('状态')->display(function ($is_opened) {
//            $status_note = $is_opened ? '已开启' : '已关闭';
//            $status_class = $is_opened ? 'success' : 'danger';
//            return "<span class='label label-$status_class'>$status_note</span>";
//        });
//        $grid->created_at('Created at');
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

        $headers = ['名称', '机器码', '地址', '物联卡号', '状态'];
        $tableRow = new TableRow();
        $tableRow->text('name', '名称')->rules('required')->placeholder('名称');
        $tableRow->text('code', '机器码')->rules('required')->placeholder('机器码')->icon('fa-calendar');
        $tableRow->text('address', '地址')->placeholder('地址');
        $tableRow->text('iot_card_no', '物联卡号')->placeholder('物联卡号');
        $states = [
            'on'  => ['value' => 1, 'text' => '开启', 'color' => 'success'],
            'off' => ['value' => 0, 'text' => '关闭', 'color' => 'danger'],
        ];
        $tableRow->switch('is_opened', '状态')->states($states);
        $form->rowtable('售货机信息')->setHeaders($headers)->setRows([$tableRow]);
//        $form->text('name', 'Name');
//        $form->text('code', 'Code');
//        $form->text('address', 'Address');
//        $form->text('iot_card_no', 'Iot card no');
//        $form->switch('is_opened', 'Is opened');

        return $form;
    }
}
