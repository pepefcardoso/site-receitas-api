<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Support\Facades\RateLimiter;
use App\Http\Middleware\SecureHeaders;
use Illuminate\Http\Request;
use Sentry\Laravel\Integration;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
        then: function () {
            RateLimiter::for(
                'api',
                fn(Request $request) =>
                Limit::perMinute(60)->by($request->user()?->id ?: $request->ip())
            );

            RateLimiter::for(
                'auth',
                fn(Request $request) =>
                Limit::perMinute(5)->by($request->ip())
            );
        }
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'throttle' => \Illuminate\Routing\Middleware\ThrottleRequests::class,
        ]);

        $middleware->api(append: [
            SecureHeaders::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        Integration::handles($exceptions);
    })
    ->create();
