<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'role' => \Spatie\Permission\Middleware\RoleMiddleware::class,
            'permission' => \Spatie\Permission\Middleware\PermissionMiddleware::class,
            'role_or_permission' => \Spatie\Permission\Middleware\RoleOrPermissionMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // Reporting: Registra o erro no AuditLog via Fila (Queue)
        $exceptions->report(function (Throwable $e) {
            if (app()->bound('request')) {
                $request = app('request');
                
                \App\Jobs\LogAuditEventJob::dispatch([
                    'event_type' => 'SYSTEM_ERROR',
                    'payload' => [
                        'exception_class' => get_class($e),
                        'message' => $e->getMessage(),
                        'file' => $e->getFile(),
                        'line' => $e->getLine(),
                        'method' => $request->method(),
                        'url' => $request->fullUrl(),
                    ],
                    'url' => $request->fullUrl(),
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                    'user_id' => auth()->id(),
                ]);
            }
        });

        $exceptions->render(function (Throwable $e, $request) {
            if (!config('app.debug') && !$request->is('api/*')) {
                return response()->view('errors.custom', [], 500);
            }
        });
    })->create();
