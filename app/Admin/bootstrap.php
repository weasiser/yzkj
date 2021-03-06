<?php

/**
 * Laravel-admin - admin builder based on Laravel.
 * @author z-song <https://github.com/z-song>
 *
 * Bootstraper for Admin.
 *
 * Here you can remove builtin form field:
 * Encore\Admin\Form::forget(['map', 'editor']);
 *
 * Or extend custom form field:
 * Encore\Admin\Form::extend('php', PHPEditor::class);
 *
 * Or require js and css assets:
 * Admin::css('/packages/prettydocs/css/styles.css');
 * Admin::js('/packages/prettydocs/js/main.js');
 *
 */

Encore\Admin\Form::forget(['map', 'editor']);

//Admin::disablePjax();

//Admin::js('https://lib.baomitu.com/font-awesome/5.8.2/js/all.min.js');
//Admin::js('https://lib.baomitu.com/font-awesome/5.8.2/js/v4-shims.min.js');

Admin::script('$("ul.nav.navbar-nav:first").css("display", "none")');
\Encore\Admin\Form::extend('ckeditor', \ghost\CKEditor\Editor::class);

//Encore\Admin\Form::extend('image', \App\Admin\Extensions\Form\CdnImage::class); // 重新注册新的 Image 组件
