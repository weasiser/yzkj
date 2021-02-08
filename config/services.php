<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Stripe, Mailgun, SparkPost and others. This file provides a sane
    | default location for this type of information, allowing packages
    | to have a conventional place to find your various credentials.
    |
    */

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'sparkpost' => [
        'secret' => env('SPARKPOST_SECRET'),
    ],

    'stripe' => [
        'model' => App\Models\User::class,
        'key' => env('STRIPE_KEY'),
        'secret' => env('STRIPE_SECRET'),
        'webhook' => [
            'secret' => env('STRIPE_WEBHOOK_SECRET'),
            'tolerance' => env('STRIPE_WEBHOOK_TOLERANCE', 300),
        ],
    ],

    'alipay' => [
        'app_private_key' => resource_path('alipay/app_private_key.pem'),//resource_path('keys/app_private_key.pem'),
        'public_key' => resource_path('alipay/alipay_public_key.pem'),//resource_path('keys/alipay_public_key.pem'),
        'mini_program_appid' => env('ALIPAY_MINI_PROGRAM_APPID'),
    ],

    'huiyijie_vending_machine' => [
        'get_access_token_api' => env('HUIYIJIE_GET_ACCESS_TOKEN_API'),
        'deliver_product_api' => env('HUIYIJIE_DELIVER_PRODUCT_API'),
        'query_machine_info' => env('HUIYIJIE_QUERY_MACHINE_INFO_API'),
        'user_name' => env('HUIYIJIE_USER_NAME'),
        'password' => env('HUIYIJIE_PASSWORD')
    ],

    'yiputeng_vending_machine' => [
        'get_api_token' => env('YIPUTENG_GET_API_TOKEN'),
        'query_machine_list' => env('YIPUTENG_QUERY_MACHINE_LIST'),
        'query_goods_list' => env('YIPUTENG_QUERY_GOODS_LIST'),
        'query_shelf_list' => env('YIPUTENG_QUERY_SHELF_LIST'),
        'pay_delivery' => env('YIPUTENG_PAY_DELIVERY'),
        'pay_multi_delivery' => env('YIPUTENG_PAY_MULTI_DELIVERY'),
        'app_key' => env('YIPUTENG_APP_KEY'),
        'app_secret' => env('YIPUTENG_APP_SECRET')
    ]

];
