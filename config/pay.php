<?php

return [
    'alipay' => [
        'app_id' => '2016082000295641',
        'notify_url' => 'http://yansongda.cn/notify.php',
        'return_url' => 'http://yansongda.cn/return.php',
        'ali_public_key' => '',
        // 加密方式： **RSA2**
        'private_key' => '',
        'log' => [ // optional
            'file' => './logs/alipay.log',
            'level' => 'info', // 建议生产环境等级调整为 info，开发环境为 debug
            'type' => 'single', // optional, 可选 daily.
            'max_file' => 30, // optional, 当 type 为 daily 时有效，默认 30 天
        ],
        'http' => [ // optional
            'timeout' => 5.0,
            'connect_timeout' => 5.0,
            // 更多配置项请参考 [Guzzle](https://guzzle-cn.readthedocs.io/zh_CN/latest/request-options.html)
        ],
        'mode' => 'dev', // optional,设置此参数，将进入沙箱模式
    ],

    'wxpay' => [
//        'appid' => 'wxb3fxxxxxxxxxxx', // APP APPID
//        'app_id' => 'wxb3fxxxxxxxxxxx', // 公众号 APPID
        'miniapp_id' => env('WXPAY_MINI_APP_ID'), // 小程序 APPID
        'mch_id' => env('WXPAY_MCH_ID'),
        'key' => env('WXPAY_KEY'),
//        'notify_url' => '',
        'cert_client' => resource_path('wxpay/apiclient_cert.pem'),//'./cert/apiclient_cert.pem', // optional，退款等情况时用到
        'cert_key' => resource_path('wxpay/apiclient_key.pem'),//'./cert/apiclient_key.pem',// optional，退款等情况时用到
        'log' => [ // optional
            'file' => storage_path('logs/wechat_pay.log'),
//            'level' => 'debug', // 建议生产环境等级调整为 info，开发环境为 debug
//            'type' => 'single', // optional, 可选 daily.
//            'max_file' => 30, // optional, 当 type 为 daily 时有效，默认 30 天
        ],
//        'http' => [ // optional
//            'timeout' => 5.0,
//            'connect_timeout' => 5.0,
//            // 更多配置项请参考 [Guzzle](https://guzzle-cn.readthedocs.io/zh_CN/latest/request-options.html)
//        ],
//        'mode' => 'dev', // optional, dev/hk;当为 `hk` 时，为香港 gateway。
    ],
];
