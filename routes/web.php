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

// Asset handlers removed - moved to catch-all at the end

// Debug Path Route - Access this to see what Lumen sees
$router->get('debug-path', function () {
    return response()->json([
        'path_path' => request()->path(),
        'url' => request()->url(),
        'full_url' => request()->fullUrl(),
        'method' => request()->method(),
        'base_path' => base_path(),
    ]);
});

// Main Entry Point
$router->get('/', function () use ($router) {
    return file_get_contents(base_path('public/pages/public/team-leader-login.html'));
});

// Admin Dashboard
$router->get('/admin/dashboard', function () {
    return file_get_contents(base_path('public/pages/admin/dashboard.html'));
});

// ... (Other admin routes are defined below in the original file, I should not delete them if I can avoid it) ... 
// But since I am appending, I will assume user routes are fine if I just append catch-all AT THE END.

// Wait, the previous replace removed the '/' route. I need to put it back.
// AND I need to make sure I didn't delete the rest of the file.

// Let's rewrite the catch-all section correctly.


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

$router->get('/admin/team-leader-resign', function () {
    return file_get_contents(base_path('public/pages/admin/team-leader-resign.html'));
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

$router->get('/admin/phone-numbers', function () {
    return file_get_contents(base_path('public/pages/admin/phone-numbers.html'));
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

$router->get('/team-leader/phone-numbers', function () {
    return file_get_contents(base_path('public/pages/team-leader/phone-numbers.html'));
});

// API Routes
// Detect if we are running in a subfolder and adjust prefix accordingly
$apiPrefix = 'api';
$requestPath = request()->path();
// If the path starts with 'jobs/sd_pro/api' (or any other subfolder variant), use that as prefix
if (preg_match('#^(.*)/api(/|$)#', $requestPath, $matches)) {
    $apiPrefix = $matches[1] . '/api';
}

$router->group(['prefix' => $apiPrefix], function () use ($router) {
    $router->post('login', 'AuthController@login');

    // Staff routes
    $router->get('staff', 'StaffController@index');
    $router->post('staff/import', 'StaffController@import');
    $router->get('staff/template', 'StaffController@downloadTemplate');
    $router->post('staff/{staffId}/reset-password', 'StaffController@resetPassword');
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
    $router->post('team-leaders/resign', 'TeamLeaderController@resign');
    $router->get('team-leaders/resignations/recent', 'TeamLeaderController@getRecentResignations');
    $router->post('team-leaders/resignations/revert', 'TeamLeaderController@revertResignation');
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
    $router->get('resignations/template', 'ResignationController@downloadTemplate');
    $router->get('resignations/months', 'ResignationController@months');
    $router->post('resignations/reactivate', 'ResignationController@reactivate');
    $router->post('resignations/import', 'ResignationController@import');
    $router->get('resignations/{id}', 'ResignationController@show');

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
    
    // Phone Number Management routes
    $router->get('phone-numbers', 'PhoneNumberController@index');
    $router->post('phone-numbers', 'PhoneNumberController@store');
    $router->put('phone-numbers/{id}', 'PhoneNumberController@update');
    $router->delete('phone-numbers/{id}', 'PhoneNumberController@destroy');
});

// Fallback Catch-All Route for Assets & Debugging
// matches GET requests to anything not matched above
$router->get('/{any:.*}', function ($any) {
    // Debug Path Route
    if (strpos($any, 'check-assets') !== false || strpos($any, 'debug-path') !== false) {
        return response()->json([
            'message' => 'Catch-All Route Hit!',
            'path_seen_by_lumen' => $any,
            'full_url' => request()->fullUrl(),
            'base_path' => base_path(),
            'public_path' => base_path('public'),
            'asset_dir_exists' => is_dir(base_path('public/assets')),
            'sample_file_check' => file_exists(base_path('public/assets/staff.png')) ? 'FOUND' : 'NOT FOUND',
        ]);
    }
    
    // Check if this looks like an asset request (contains 'assets/' or 'dist/')
    // Usage of preg_match allows matching "jobs/sd_pro/public/assets/foo.js" 
    // and extracting "assets/foo.js" regardless of prefix.
    if (preg_match('#(assets|dist)/(.*)$#', $any, $matches)) {
        $folder = $matches[1]; // assets or dist
        $file = $matches[2];   // filename.js
        
        // Prevent directory traversal
        if (strpos($file, '..') !== false) {
           return response('Forbidden', 403);
        }

        $path = base_path("public/$folder/$file");
        
        if (file_exists($path)) {
            $ext = pathinfo($path, PATHINFO_EXTENSION);
            $types = [
                'js' => 'application/javascript',
                'css' => 'text/css',
                'png' => 'image/png',
                'jpg' => 'image/jpeg',
                'svg' => 'image/svg+xml',
                'woff' => 'font/woff', 
                'woff2' => 'font/woff2'
            ];
            $contentType = $types[$ext] ?? 'text/plain';
            return response(file_get_contents($path))
                ->header('Content-Type', $contentType);
        }
        
        // If file not found, return 404 but with debug info for now
        // return response("Asset file not found at: $path", 404);
    }

    return response('Route not defined: ' . $any, 404);
});
