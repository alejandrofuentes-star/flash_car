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
        $middleware->validateCsrfTokens(except: [
            'webhook/stripe',
        ]);
        $middleware->alias([
            'role' => \App\Http\Middleware\CheckRole::class,
        ]);
        $middleware->web(append: [
            \App\Http\Middleware\SetLocale::class,
            \App\Http\Middleware\CheckMaintenance::class,
            \App\Http\Middleware\TrackVisitor::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->render(function (\Illuminate\Session\TokenMismatchException $e, $request) {
            if ($request->wantsJson()) {
                return response()->json([
                    'message' => 'La sesión expiró. Por favor actualiza la página e intenta de nuevo.',
                ], 419);
            }

            // Formulario que llegó vacío (sin token): el servidor lo descartó por rebasar post_max_size
            if ($request->isMethod('post') && empty($request->all())) {
                return back()->with('error', 'El servidor rechazó el envío porque pesa más de lo permitido (post_max_size = '
                    . ini_get('post_max_size') . '). Por favor avisa al administrador.');
            }

            return back()
                ->withInput($request->except('password', 'password_confirmation'))
                ->with('error', 'Tu sesión expiró por inactividad. Por favor intenta de nuevo.');
        });
    })->create();