<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

final class Idempoten
{
    public function handle(Request $request, Closure $next): Response
    {
        $kunci = trim((string) $request->header('Idempotency-Key', ''));

        if ($kunci === '') {
            return response()->json([
                'kesalahan' => 'idempotency_key_hilang',
                'pesan' => 'Header Idempotency-Key wajib disertakan.',
            ], 400);
        }

        $kunciCache = 'idem:'.sha1($request->path().'|'.$kunci);
        $tersimpan = Cache::get($kunciCache);

        if (is_array($tersimpan)) {
            return response(
                $tersimpan['konten'],
                $tersimpan['status'],
                $tersimpan['header'],
            )->header('Idempotent-Replayed', 'true');
        }

        $response = $next($request);

        if ($response->getStatusCode() < 300) {
            Cache::put($kunciCache, [
                'konten' => (string) $response->getContent(),
                'status' => $response->getStatusCode(),
                'header' => $response->headers->all(),
            ], now()->addHours(24));
        }

        return $response;
    }
}
