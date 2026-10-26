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
    ->withMiddleware(function (Middleware $middleware): void {
        // تبدیل لینک‌های مطلقِ ساخته‌شده با Host پروکسی به لینک نسبی (پیش‌نمایش پلتفرم)
        $middleware->append(\App\Http\Middleware\MakeUrlsRelative::class);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
