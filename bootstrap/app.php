<?php


use Mockery\VerificationExpectation;
use Illuminate\Foundation\Application;
use Illuminate\Auth\Middleware\Authenticate;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Auth\Middleware\EnsureEmailIsVerified;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
         $middleware->alias([
        'auth' => Authenticate::class,
        'verified' => EnsureEmailIsVerified::class,
    
    ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
            $exceptions->render(function (AuthorizationException $e, $request) {
            return response()->json([
                'status' => 403,
                'message' => 'Anda tidak memiliki izin untuk melakukan aksi ini.'
            ], 403);
        });

        $exceptions->render(function (AccessDeniedHttpException $e, $request) {
            return response()->json([
                'status' => 403,
                'message' => 'Akses ditolak: Anda tidak memiliki izin.'
            ], 403);
        });

       $exceptions->render(function (HttpException $e, $request) {
    if ($e->getStatusCode() === 403) {
        return response()->json([
            'status' => 403,
            'message' => 'Anda belum verifikasi email.'
        ], 403);
    }

    if ($e->getStatusCode() === 422) {
        return response()->json([
            'status' => 422,
            'message' => $e->getMessage()
        ], 422);
    }

    return response()->json([
        'status' => $e->getStatusCode(),
        'message' => $e->getMessage() ?: 'Terjadi kesalahan'
    ], $e->getStatusCode());
});
    })->create();
