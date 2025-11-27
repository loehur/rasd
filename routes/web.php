<?php

/** @var \Laravel\Lumen\Routing\Router $router */

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$router->get('/', function () use ($router) {
    return $router->app->version();
});

// API Routes
$router->group(['prefix' => 'api'], function () use ($router) {
    $router->post('login', 'AuthController@login');

    // Staff routes
    $router->get('staff', 'StaffController@index');
    $router->post('staff/import', 'StaffController@import');
    $router->get('staff/template', 'StaffController@downloadTemplate');

    // Account routes
    $router->put('account/name', 'AccountController@updateName');
    $router->put('account/password', 'AccountController@updatePassword');
});
