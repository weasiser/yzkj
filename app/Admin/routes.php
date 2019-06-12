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

    $router->get('products', 'ProductsController@index');
    $router->get('products/create', 'ProductsController@create');
    $router->post('products', 'ProductsController@store');
    $router->get('products/{id}/edit', 'ProductsController@edit');
    $router->put('products/{id}', 'ProductsController@update');
    $router->delete('products/{id}', 'ProductsController@destroy');
    $router->get('api/products', 'ProductsController@apiIndex');
    $router->get('api/product/{id}/pes', 'ProductsController@apiHasManyPes');

    $router->get('vending_machines', 'VendingMachinesController@index');
    $router->get('vending_machines/create', 'VendingMachinesController@create');
    $router->post('vending_machines', 'VendingMachinesController@store');
    $router->get('vending_machines/{id}/edit', 'VendingMachinesController@edit');
    $router->put('vending_machines/{id}', 'VendingMachinesController@update');
    $router->delete('vending_machines/{id}', 'VendingMachinesController@destroy');

    $router->resource('productpes', ProductPesController::class);

});
