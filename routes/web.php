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

// Admin frontend routes - serve appropriate HTML based on path
$router->get('/admin/dashboard', function () {
    return file_get_contents(base_path('public/pages/admin/dashboard.html'));
});

$router->get('/admin/staff-list', function () {
    return file_get_contents(base_path('public/pages/admin/staff-list.html'));
});

$router->get('/admin/import-staff', function () {
    return file_get_contents(base_path('public/pages/admin/import-staff.html'));
});

$router->get('/admin/account', function () {
    return file_get_contents(base_path('public/pages/admin/account.html'));
});

$router->get('/admin/change-password', function () {
    return file_get_contents(base_path('public/pages/admin/change-password.html'));
});

$router->get('/admin', function () {
    return file_get_contents(base_path('public/pages/admin/login.html'));
});

// API Routes
$router->group(['prefix' => 'api'], function () use ($router) {
    $router->post('login', 'AuthController@login');

    // Staff routes
    $router->get('staff', 'StaffController@index');
    $router->post('staff/import', 'StaffController@import');
    $router->get('staff/template', 'StaffController@downloadTemplate');

    // Team Leader routes
    $router->get('team-leaders', 'TeamLeaderController@index');
    $router->post('team-leaders/import', 'TeamLeaderController@import');
    $router->get('team-leaders/template', 'TeamLeaderController@downloadTemplate');

    // Account routes
    $router->put('account/name', 'AccountController@updateName');
    $router->put('account/password', 'AccountController@updatePassword');
});
