<?php

use Illuminate\Routing\Router;

Admin::registerAuthRoutes();

Route::group([
    'prefix'        => config('admin.route.prefix'),
    'namespace'     => config('admin.route.namespace'),
    'middleware'    => config('admin.route.middleware'),
], function (Router $router) {

    $router->get('/', 'HomeController@index')->name('admin.home');
    $router->get('users', 'UsersController@index');
    $router->put('users/{id}', 'UsersController@update');
    $router->get('api/users', 'UsersController@apiIndex');

    $router->get('products', 'ProductsController@index');
    $router->get('products/create', 'ProductsController@create');
    $router->post('products', 'ProductsController@store');
    $router->get('products/{id}/edit', 'ProductsController@edit');
    $router->put('products/{id}', 'ProductsController@update');
    $router->delete('products/{id}', 'ProductsController@destroy');
    $router->get('api/products', 'ProductsController@apiIndex');
    $router->get('api/products/all', 'ProductsController@apiAll');

    $router->get('vendingMachines', 'VendingMachinesController@index');
    $router->get('vendingMachines/create', 'VendingMachinesController@create');
    $router->post('vendingMachines', 'VendingMachinesController@store');
    $router->get('vendingMachines/{id}/edit', 'VendingMachinesController@edit');
    $router->put('vendingMachines/{id}', 'VendingMachinesController@update');
    $router->delete('vendingMachines/{id}', 'VendingMachinesController@destroy');

    $router->resource('productPes', ProductPesController::class);
    $router->get('api/{id}/stock', 'ProductPesController@apiStock');

    $router->resource('vendingMachineAisles', VendingMachineAislesController::class);

    $router->resource('warehouses', WarehousesController::class);

    $router->get('orders', 'OrdersController@index')->name('admin.orders.index');
    $router->get('orders/{order}', 'OrdersController@show')->name('admin.orders.show');
    $router->post('orders/{order}/miniapp/refund', 'OrdersController@miniappRefund')->name('admin.orders.miniappRefund');
//    $router->resource('orders', OrdersController::class);
});
