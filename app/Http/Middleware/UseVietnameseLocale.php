<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/** Serves the customer panel (login, registration, tax tool) in Vietnamese regardless of the app default. */
class UseVietnameseLocale
{
    public function handle(Request $request, Closure $next): Response
    {
        app()->setLocale('vi');

        return $next($request);
    }
}
