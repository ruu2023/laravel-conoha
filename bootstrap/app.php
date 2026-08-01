<?php

use App\Http\Middleware\HandleInertiaRequests;
use App\Http\Middleware\ResolveAppSubdomain;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // config() isn't bootstrapped yet when this callback runs, so read
        // the Cloudflare IP ranges straight off disk instead.
        $cloudflare = require __DIR__.'/../config/cloudflare.php';

        $middleware->trustProxies(
            at: array_merge($cloudflare['ipv4'], $cloudflare['ipv6']),
        );

        $middleware->append(ResolveAppSubdomain::class);
        $middleware->web(append: [HandleInertiaRequests::class]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*'),
        );
    })->create();
