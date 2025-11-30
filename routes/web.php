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
    return file_get_contents(base_path('public/pages/public/team-leader-login.html'));
});

// Admin frontend routes - serve appropriate HTML based on path
$router->get('/admin/dashboard', function () {
    return file_get_contents(base_path('public/pages/admin/dashboard.html'));
});

$router->get('/admin/staff-list', function () {
    return file_get_contents(base_path('public/pages/admin/staff-list.html'));
});

$router->get('/admin/inactive-staff', function () {
    return file_get_contents(base_path('public/pages/admin/inactive-staff.html'));
});

$router->get('/admin/import-staff', function () {
    return file_get_contents(base_path('public/pages/admin/import-staff.html'));
});

$router->get('/admin/import-team-leader', function () {
    return file_get_contents(base_path('public/pages/admin/import-team-leader.html'));
});

$router->get('/admin/account', function () {
    return file_get_contents(base_path('public/pages/admin/account.html'));
});

$router->get('/admin/team-leader-list', function () {
    return file_get_contents(base_path('public/pages/admin/team-leader-list.html'));
});

$router->get('/admin/team-leader/edit/{employeeId}', function () {
    return file_get_contents(base_path('public/pages/admin/edit-team-leader.html'));
});

$router->get('/admin/change-password', function () {
    return file_get_contents(base_path('public/pages/admin/change-password.html'));
});
$router->get('/admin/attendance', function () {
    return file_get_contents(base_path('public/pages/admin/attendance.html'));
});

$router->get('/admin/users', function () {
    return file_get_contents(base_path('public/pages/admin/users.html'));
});

$router->get('/admin/staff-changes', function () {
    return file_get_contents(base_path('public/pages/admin/staff-changes.html'));
});

$router->get('/admin/system', function () {
    return file_get_contents(base_path('public/pages/admin/system.html'));
});

$router->get('/admin', function () {
    return file_get_contents(base_path('public/pages/admin/login.html'));
});

// Team Leader public routes
$router->get('/team-leader/login', function () {
    return file_get_contents(base_path('public/pages/public/team-leader-login.html'));
});

$router->get('/team-leader/dashboard', function () {
    return file_get_contents(base_path('public/pages/team-leader/dashboard.html'));
});

$router->get('/team-leader/attendance', function () {
    return file_get_contents(base_path('public/pages/team-leader/attendance.html'));
});

$router->get('/team-leader/account', function () {
    return file_get_contents(base_path('public/pages/team-leader/account.html'));
});

$router->get('/team-leader/change-password', function () {
    return file_get_contents(base_path('public/pages/team-leader/change-password.html'));
});

$router->get('/team-leader/staff-list', function () {
    return file_get_contents(base_path('public/pages/team-leader/staff-list.html'));
});

$router->get('/team-leader/inactive-staff', function () {
    return file_get_contents(base_path('public/pages/team-leader/inactive-staff.html'));
});

$router->get('/team-leader/resignation', function () {
    return file_get_contents(base_path('public/pages/team-leader/resignation.html'));
});

// API Routes
$router->group(['prefix' => 'api'], function () use ($router) {
    $router->post('login', 'AuthController@login');

    // Staff routes
    $router->get('staff', 'StaffController@index');
    $router->post('staff/import', 'StaffController@import');
    $router->get('staff/template', 'StaffController@downloadTemplate');
    $router->delete('staff/reset-all', 'StaffController@resetAll');
    $router->delete('staff/{staffId}', 'StaffController@destroy');

    // Team Leader routes
    $router->post('team-leader/login', 'TeamLeaderController@login');
    $router->get('team-leader/staff', 'TeamLeaderController@getStaff');
    $router->put('team-leader/account/name', 'TeamLeaderController@updateName');
    $router->put('team-leader/account/password', 'TeamLeaderController@updatePassword');
    $router->get('team-leaders', 'TeamLeaderController@index');
    $router->post('team-leaders/import', 'TeamLeaderController@import');
    $router->get('team-leaders/template', 'TeamLeaderController@downloadTemplate');
    $router->get('team-leaders/{employeeId}', 'TeamLeaderController@show');
    $router->put('team-leaders/{employeeId}', 'TeamLeaderController@update');
    $router->post('team-leaders/{employeeId}/reset-password', 'TeamLeaderController@resetPassword');

    // Account routes
    $router->put('account/name', 'AccountController@updateName');
    $router->put('account/password', 'AccountController@updatePassword');

    // Attendance routes
    $router->get('attendances', 'AttendanceController@index');
    $router->get('attendances/staff/team-leader', 'AttendanceController@getStaffByTeamLeader');
    $router->get('attendances/staff/{staffId}', 'AttendanceController@getStaffDetail');
    $router->post('attendances', 'AttendanceController@store');
    $router->get('attendances/{id}', 'AttendanceController@show');
    $router->put('attendances/{id}', 'AttendanceController@update');
    $router->delete('attendances/{id}', 'AttendanceController@destroy');
    // Users management routes
    $router->get('users', 'UserController@index');
    $router->post('users', 'UserController@store');
    $router->post('users/{id}/reset-password', 'UserController@resetPassword');
    $router->delete('users/{id}', 'UserController@destroy');

    // Resignation routes
    $router->get('resignations', 'ResignationController@index');
    $router->post('resignations', 'ResignationController@store');
    $router->get('resignations/{id}', 'ResignationController@show');
    $router->post('resignations/reactivate', 'ResignationController@reactivate');
    $router->post('resignations/import', 'ResignationController@import');
    $router->get('resignations/template', 'ResignationController@downloadTemplate');
    $router->get('resignations/months', 'ResignationController@months');

    // Staff Change Management routes
    $router->get('staff-changes/all-staff', 'StaffChangeController@getAllStaff');
    $router->get('staff-changes/team-leaders', 'StaffChangeController@getTeamLeaders');
    $router->post('staff-changes/transfer-division', 'StaffChangeController@transferDivision');
    $router->post('staff-changes/promote-tl', 'StaffChangeController@promoteToTL');
    $router->post('staff-changes/change-rank', 'StaffChangeController@changeRank');
    $router->post('staff-changes/warning-letter', 'StaffChangeController@updateWarningLetter');
    $router->get('staff-changes/logs', 'StaffChangeController@getStaffLogs');
    $router->get('staff-changes/logs/{staffId}', 'StaffChangeController@getStaffLogs');
    $router->get('staff-changes/detail/{staffId}', 'StaffChangeController@getStaffDetail');
});
