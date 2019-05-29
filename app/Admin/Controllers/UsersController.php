<?php

namespace App\Admin\Controllers;

use App\Models\User;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;

class UsersController extends Controller
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
            ->header('用户')
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
//    public function edit($id, Content $content)
//    {
//        return $content
//            ->header('Edit')
//            ->description('description')
//            ->body($this->form()->edit($id));
//    }

    /**
     * Create interface.
     *
     * @param Content $content
     * @return Content
     */
//    public function create(Content $content)
//    {
//        return $content
//            ->header('Create')
//            ->description('description')
//            ->body($this->form());
//    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new User);

        $grid->id('ID');
//        $grid->name('用户名');
//        $grid->phone('手机号');
//        $grid->email('邮箱');
        $grid->nick_name('昵称');
        $grid->avatar('头像')->image('', 50, 50);
//        $grid->weapp_openid('小程序')->display(function ($value) {
//            return $value ? '<i class="fa fa-lg fa-weixin" style="color: #4caf50"></i>' : '';
//        });
        $grid->column('小程序')->display(function () {
            return $this->weapp_openid ? '<img src="https://s2.ax1x.com/2019/05/29/Vnnzdg.png" class="img img-sm">' : ($this->alipay_user_id ? '<img src="https://s2.ax1x.com/2019/05/29/VnuSoQ.png" class="img img-sm">' : '');
        });
//        $grid->email_verified_at('已验证邮箱')->display(function ($value) {
//            return $value ? '是' : '否';
//        });
//        $grid->password('Password');
//        $grid->remember_token('Remember token');
        $grid->created_at('注册时间');
//        $grid->updated_at('Updated at');

        // 不在页面显示 `新建` 按钮，因为我们不需要在后台新建用户
        $grid->disableCreateButton();

        $grid->actions(function ($actions) {
            // 不在每一行后面展示查看按钮
            $actions->disableView();
            // 不在每一行后面展示删除按钮
            $actions->disableDelete();
            // 不在每一行后面展示编辑按钮
            $actions->disableEdit();
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
                $filter->like('nick_name', '昵称');
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
//        $show = new Show(User::findOrFail($id));
//
//        $show->id('Id');
//        $show->name('Name');
//        $show->email('Email');
//        $show->email_verified_at('Email verified at');
//        $show->password('Password');
//        $show->remember_token('Remember token');
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
//    protected function form()
//    {
//        $form = new Form(new User);
//
//        $form->text('name', 'Name');
//        $form->email('email', 'Email');
//        $form->datetime('email_verified_at', 'Email verified at')->default(date('Y-m-d H:i:s'));
//        $form->password('password', 'Password');
//        $form->text('remember_token', 'Remember token');
//
//        return $form;
//    }
}
