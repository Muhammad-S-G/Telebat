<?php

use App\Http\Middleware\EnsureJsonContent;
use App\Http\Middleware\IsVerified;
use App\Http\Middleware\SetLocale;
use App\Http\Middleware\SwaggerNoCSRF;
use App\Scheduled\NullifyUserConfirmedAt;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Spatie\Permission\Middleware\PermissionMiddleware;
use Spatie\Permission\Middleware\RoleMiddleware;
use Spatie\Permission\Middleware\RoleOrPermissionMiddleware;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->append(SetLocale::class);

        $middleware->alias([
            'is_verified' => IsVerified::class,
            'is_json' => EnsureJsonContent::class,
            'role' => RoleMiddleware::class,
            'permission' => PermissionMiddleware::class,
            'role_or_permission' => RoleOrPermissionMiddleware::class,
            'swagger.no_csrf' => SwaggerNoCSRF::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->render(function (NotFoundHttpException $e, Request $request) {
            if ($e->getPrevious() instanceof ModelNotFoundException) {
                return error('The requested record was not found.', 404);
            }
        });
    })
    ->withSchedule(function (Schedule $schedule) {
        $schedule->call(new NullifyUserConfirmedAt())->daily();
    })
    ->create();
