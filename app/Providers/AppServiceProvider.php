<?php

namespace App\Providers;

use App\Models\Article;
use App\Models\ArticleComment;
use App\Models\ProductPes;
use App\Models\VendingMachineAisle;
use App\Models\YipuengDeliverProductNotification;
use App\Observers\ArticleCommentObserver;
use App\Observers\ArticleObserver;
use App\Observers\ProductPesObserver;
use App\Observers\VendingMachineAisleObserver;
use App\Observers\YipuengDeliverProductNotificationObserver;
use Illuminate\Support\ServiceProvider;
use Monolog\Logger;
use Yansongda\Pay\Pay;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        // 往服务容器中注入一个名为 alipay 的单例对象
        $this->app->singleton('alipay', function () {
            $config = config('pay.alipay');
            $config['notify_url'] = route('paymentNotifications.miniapp.alipay.notify');
            // 判断当前项目运行环境是否为线上环境
            if (app()->environment() !== 'production') {
                $config['mode']         = 'dev';
                $config['log']['level'] = Logger::DEBUG;
            } else {
                $config['log']['level'] = Logger::INFO;
            }
            // 调用 Yansongda\Pay 来创建一个支付宝支付对象
            return Pay::alipay($config);
        });

        $this->app->singleton('wxpay', function () {
            $config = config('pay.wxpay');
            $config['notify_url'] = route('paymentNotifications.miniapp.wxpay.notify');
            if (app()->environment() !== 'production') {
                $config['log']['level'] = Logger::DEBUG;
            } else {
                $config['log']['level'] = Logger::INFO;
            }
            // 调用 Yansongda\Pay 来创建一个微信支付对象
            return Pay::wechat($config);
        });
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        app('Dingo\Api\Transformer\Factory')->setAdapter(function ($app) {
            $fractal = new \League\Fractal\Manager;
            $fractal->setSerializer(new \League\Fractal\Serializer\ArraySerializer);
            return new \Dingo\Api\Transformer\Adapter\Fractal($fractal);
        });

        ProductPes::observe(ProductPesObserver::class);
        VendingMachineAisle::observe(VendingMachineAisleObserver::class);
        Article::observe(ArticleObserver::class);
        ArticleComment::observe(ArticleCommentObserver::class);
        YipuengDeliverProductNotification::observe(YipuengDeliverProductNotificationObserver::class);
    }
}
