<?php

namespace App\Admin\Controllers;

use App\Handlers\ImageUploadHandler;
use App\Models\Article;
use App\Models\ArticleCategory;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;
use Illuminate\Http\Request;

class ArticlesController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
//    protected $title = 'App\Models\Article';

    public function index(Content $content)
    {
        return $content
            ->header('文章')
            ->description('列表')
            ->body($this->grid());
    }

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
            ->header('文章')
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
            ->header('文章')
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
        $grid = new Grid(new Article);
        $grid->model()->with('articleCategory');

        $grid->column('id', __('ID'))->sortable();
        $grid->column('title', __('标题'));
        $grid->column('author', __('作者'));
        $grid->column('articleCategory.name', __('分类'));
//        $grid->column('body', __('Body'));
        $grid->column('comment_count', __('评论数'))->sortable();
        $grid->column('visit_count', __('阅读数'))->sortable();
        $grid->column('created_at', __('创建时间'))->sortable();
        $grid->column('updated_at', __('更新时间'))->sortable();

        $grid->actions(function ($actions) {
            // 不在每一行后面展示查看按钮
            $actions->disableView();
            // 不在每一行后面展示删除按钮
            $actions->disableDelete();
        });

        $grid->filter(function ($filter) {
            $filter->column(1 / 2, function ($filter) {
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
//        $show = new Show(Article::findOrFail($id));
//
//        $show->field('id', __('Id'));
//        $show->field('title', __('Title'));
//        $show->field('author', __('Author'));
//        $show->field('article_category_id', __('Article category id'));
//        $show->field('body', __('Body'));
//        $show->field('comment_count', __('Comment count'));
//        $show->field('visit_count', __('Visit count'));
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
        $form = new Form(new Article);

        $form->text('title', __('标题'))->required();
        $form->text('author', __('作者'));
        $form->select('article_category_id', '分类')->options(ArticleCategory::all()->pluck('name', 'id'))->required();
//        $form->number('article_category_id', __('Article category id'));
//        $form->textarea('body', __('Body'));
        $form->ckeditor('body', '正文');
//        $form->number('comment_count', __('Comment count'));
//        $form->number('visit_count', __('Visit count'));

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

    public function imageUpload(Request $request, ImageUploadHandler $imageUploadHandler)
    {
        // 初始化返回数据，默认是失败的
        $data = [
            'error' => [
                'message' => '上传失败!'
            ]
        ];
        // 判断是否有上传文件，并赋值给 $file
        if ($file = $request->upload) {
            // 保存图片到本地
            $result = $imageUploadHandler->save($file, 'articles', 'admin', 416);
            // 图片保存成功的话
            if ($result) {
                $data = [
                    'url' => $result['path']
                ];
            }
        }
        return $data;
    }
}
