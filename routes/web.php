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

// --- 1. DEBUG ROUTES ---
$router->get('test-path', function () {
    return response()->json([
        'path' => request()->path(),
        'url' => request()->url(),
        'method' => request()->method()
    ]);
});

$router->get('check-assets', function () {
    $path = base_path('public/assets');
    $files = is_dir($path) ? scandir($path) : [];
    return response()->json([
        'path' => $path,
        'exists' => is_dir($path),
        'files' => $files,
        'public_path' => base_path('public'),
    ]);
});

// --- 2. PAGE ROUTES (SERVE HTML) ---
// Main Entry Point
$router->get('/', function () use ($router) {
    return file_get_contents(base_path('public/pages/public/team-leader-login.html'));
});

// Admin Pages
$router->get('/admin', function () {
    return file_get_contents(base_path('public/pages/admin/login.html'));
});
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

// Team Leader Pages
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

// --- 3. API ROUTES (WITH DYNAMIC PREFIX) ---

// Detect if we are running in a subfolder and adjust prefix accordingly
$apiPrefix = 'api';
$requestPath = request()->path();
// Regex to capture optional prefix folder before 'api'
if (preg_match('#^(.*/)?api(/|$)#', $requestPath, $matches)) {
    $prefixPath = $matches[1] ?? '';
    $prefixPath = rtrim($prefixPath, '/');
    if (!empty($prefixPath)) {
        $apiPrefix = $prefixPath . '/api';
    } else {
        $apiPrefix = 'api';
    }
}
$apiPrefix = ltrim($apiPrefix, '/'); // Ensure clean prefix

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


// --- 4. CATCH-ALL ROUTE (ASSETS FALLBACK & DEBUGGING) ---
// This must be the LAST route definition.
// It accepts ALL methods (GET, POST, etc) to catch everything.

$router->group(['prefix' => ''], function ($router) {
    $router->addRoute(['GET', 'POST', 'PUT', 'DELETE', 'OPTIONS'], '/{any:.*}', function ($any) {
        
        // A. Asset Handling Logic
        // Checks if URL contains 'assets/' or 'dist/'
        if (preg_match('#(assets|dist)/(.*)$#', $any, $matches)) {
            $folder = $matches[1];
            $file = $matches[2];
            
            // Security check
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
            // If asset not found, don't return 404 yet, let it fall through or return specific asset 404
             return response("Asset file not found at: $path", 404);
        }

        // B. Debug Logic for API/Login failures
        // If we reached here with an API request, it means the API route didn't match.
        if (strpos($any, 'login') !== false || strpos($any, 'api') !== false) {
             return response()->json([
                'STATUS' => 'FALLBACK_HIT_MEANS_ROUTE_MISMATCH',
                'method' => request()->method(),
                'path_seen_by_lumen' => $any,
                'request_path' => request()->path(),
                'full_url' => request()->fullUrl(),
                'calculated_prefix_test' => preg_match('#^(.*/)?api(/|$)#', request()->path(), $m) ? ($m[1] . 'api') : 'NOMATCH',
            ], 404);
        }

        // C. Generic 404
        return response('Route not defined: ' . $any, 404);
    });
});
