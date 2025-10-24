<?php

use App\Exceptions\CustomHandler;
use Illuminate\Http\Request;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Http\Middleware\V1\AuthenticateMiddleware;
use App\Http\Middleware\V1\CheckRoleMiddleware;


return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        api: __DIR__ . '/../routes/api.php',
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->api(append: [
            \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class,
        ]);

        // Register your route middleware (for use with Route::middleware())
        //Using alias() to register route middleware you can apply selectively
        $middleware->alias([
            'auth.token' =>AuthenticateMiddleware::class,
            'role' => CheckRoleMiddleware::class,
        ]);
        //
    })
    ->withExceptions(function (Exceptions $exceptions) {

        $exceptions->render(function (Throwable $e, Request $request) {
            $handler = new CustomHandler(app());
            return $handler->render($request, $e);
        });
        
    })->create();
