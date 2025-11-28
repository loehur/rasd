<?php

namespace App\Http\Controllers;

use Laravel\Lumen\Routing\Controller as BaseController;

class Controller extends BaseController
{
    protected function logAction($request, $action, array $payload = [])
    {
        try {
            $role = method_exists($request, 'header') ? ($request->header('X-Role') ?? null) : null;
            $ip = method_exists($request, 'ip') ? $request->ip() : null;
            $ua = method_exists($request, 'header') ? ($request->header('User-Agent') ?? null) : null;
            $line = json_encode([
                'ts' => date('c'),
                'action' => $action,
                'role' => $role,
                'ip' => $ip,
                'ua' => $ua,
                'payload' => $payload,
            ], JSON_UNESCAPED_SLASHES);

            $logDir = base_path('storage/logs');
            if (!file_exists($logDir)) {
                @mkdir($logDir, 0755, true);
            }
            @file_put_contents($logDir . DIRECTORY_SEPARATOR . 'admin-actions.log', $line . PHP_EOL, FILE_APPEND | LOCK_EX);
        } catch (\Throwable $e) {
            // swallow any logging errors to keep it lightweight
        }
    }
}
