<?php

use App\Exceptions\KesalahanPos;
use App\Http\Middleware\Idempoten;
use App\Http\Middleware\CatatRequest;
use App\Http\Middleware\HitungKueri;
use App\Http\Middleware\JamOperasional;
use App\Http\Middleware\KunciApiKasir;
use App\Http\Middleware\PeranKasir;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

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
            HitungKueri::class,
        ]);

        // Middleware yang dipanggil menggunakan alias
        $middleware->alias([
            'kasir' => KunciApiKasir::class,
            'idempoten' => Idempoten::class,
            'peran' => PeranKasir::class,
            'jam.buka' => JamOperasional::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // Permintaan API tetap mendapatkan response JSON saat error
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request): bool =>
                $request->is('api/*') || $request->expectsJson(),
        );

        // Kesalahan domain POS
        $exceptions->render(
            function (KesalahanPos $e, Request $request) {
                if (! $request->is('api/*')) {
                    return null;
                }

                return response()->json(
                    [
                        'kesalahan' => $e->kodeKesalahan(),
                        'pesan' => $e->getMessage(),
                    ] + $e->konteks(),
                    $e->statusHttp(),
                );
            }
        );

        // Data tidak ditemukan
        $exceptions->render(
            fn (ModelNotFoundException $e, Request $request) =>
                $request->is('api/*')
                    ? response()->json([
                        'kesalahan' => 'data_tidak_ditemukan',
                        'pesan' => 'Data '
                            . class_basename($e->getModel())
                            . ' tidak ditemukan.',
                    ], 404)
                    : null
        );

        // Endpoint tidak ditemukan
        $exceptions->render(
            fn (NotFoundHttpException $e, Request $request) =>
                $request->is('api/*')
                    ? response()->json([
                        'kesalahan' => 'endpoint_tidak_ditemukan',
                        'pesan' => 'Endpoint tidak ditemukan.',
                    ], 404)
                    : null
        );

        // Tidak memiliki izin
        $exceptions->render(
            fn (AuthorizationException $e, Request $request) =>
                $request->is('api/*')
                    ? response()->json([
                        'kesalahan' => 'tidak_berwenang',
                        'pesan' => 'Anda tidak berwenang melakukan tindakan ini.',
                    ], 403)
                    : null
        );
    })
    ->create();