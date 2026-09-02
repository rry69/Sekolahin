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
        $middleware->alias([
            'role' => \App\Http\Middleware\RoleMiddleware::class,
        ]);
        $middleware->validateCsrfTokens(except: [
            'webhooks/xendit',
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->render(function (Throwable $e, $request) {
            if ($request->expectsJson()) return null;
            $code = $e instanceof \Symfony\Component\HttpKernel\Exception\HttpException ? $e->getStatusCode() : 500;
            if ($code < 400) $code = 500;
            // Hanya tangani HTTP error yang punya view; biarkan debug detail jika perlu
            if (in_array($code, [400,401,403,404,419,422,429,500,503]) || $code >= 400) {
                if (view()->exists('errors.error')) {
                    return response()->view('errors.error', ['exception' => $e, 'code' => $code], $code);
                }
            }
            return null;
        });
    })->create();
