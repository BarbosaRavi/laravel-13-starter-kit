<?php

namespace App\Http\Middlewares;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class SetBroadcastGuard
{
    public function handle(Request $request, Closure $next): Response
    {
        Auth::shouldUse('api');
        return $next($request);
    }
}