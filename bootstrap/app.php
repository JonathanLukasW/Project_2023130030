<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        
        // Ini adalah array "Alias" (shortcut)
        $middleware->alias([
            'verified' => \App\Http\Middleware\EnsureEmailIsVerified::class,
            
            // 'guest' and 'auth' aliases are automatically registered 
            // by Laravel 11/12, so we can remove them from here.
            
            // --- PERBAIKANNYA DI SINI ---
            // Tambahkan 3 baris ini untuk mendaftarkan alias Spatie
            'role' => \Spatie\Permission\Middleware\RoleMiddleware::class,
            'permission' => \Spatie\Permission\Middleware\PermissionMiddleware::class,
            'role_or_permission' => \Spatie\Permission\Middleware\RoleOrPermissionMiddleware::class,
        ]);

        // (Mungkin ada konfigurasi middleware lain di sini...)

    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();