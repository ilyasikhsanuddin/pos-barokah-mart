<?php

use App\Http\Middleware\CatatRequest;
use App\Http\Middleware\JamOperasional;
use App\Http\Middleware\KunciApiKasir;
use App\Http\Middleware\PeranKasir;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Middleware global untuk semua route API
        $middleware->api(append: [
            CatatRequest::class,
        ]);

        // Middleware yang dipanggil menggunakan alias
        $middleware->alias([
            'kasir' => KunciApiKasir::class,
            'peran' => PeranKasir::class,
            'jam.buka' => JamOperasional::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*') || $request->expectsJson(),
        );
    })
    ->create();
