<?php

use App\Http\Middleware\SecurityHeaders;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->trustProxies(at: '*', headers: Request::HEADER_X_FORWARDED_FOR
            | Request::HEADER_X_FORWARDED_HOST
            | Request::HEADER_X_FORWARDED_PORT
            | Request::HEADER_X_FORWARDED_PROTO);

        $middleware->append(SecurityHeaders::class);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*'),
        );

        $exceptions->render(function (NotFoundHttpException $e, Request $request) {
            // URL que não bate com nenhuma rota registrada ($request->route() === null):
            // redireciona direto para a home. Conteúdo que existe como rota mas está
            // despublicado/ausente (service/city/landing em draft, tool/submission inválidos)
            // cai na renderização padrão (errors/404.blade.php), que mostra a página 404 com
            // redirecionamento automático após alguns segundos.
            if (
                $request->route() === null
                && ! $request->is('admin/*')
                && ! $request->is('api/*')
                && ! $request->expectsJson()
            ) {
                return redirect()->route('home');
            }
        });
    })->create();
