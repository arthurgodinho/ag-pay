<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'admin' => \App\Http\Middleware\AdminMiddleware::class,
            'api.token' => \App\Http\Middleware\ApiTokenMiddleware::class,
            'approved' => \App\Http\Middleware\CheckApproved::class,
            'session.timeout' => \App\Http\Middleware\CheckSessionTimeout::class,
            'locale' => \App\Http\Middleware\SetLocale::class,
        ]);
        
        $middleware->validateCsrfTokens(except: [
            'api/webhooks/*',
        ]);

        $middleware->trustProxies(at: '*');

        // Aplica o middleware de locale globalmente
        $middleware->web(append: [
            \App\Http\Middleware\SetLocale::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // Permite mostrar erros úteis mesmo em produção para problemas de configuração
        $exceptions->render(function (\Throwable $e, $request) {
            // Para erros de banco de dados, sempre mostra mensagens úteis
            if ($e instanceof \PDOException || $e instanceof \Illuminate\Database\QueryException) {
                return app(\App\Exceptions\Handler::class)->render($request, $e);
            }
            return null;
        });
    })->create();
