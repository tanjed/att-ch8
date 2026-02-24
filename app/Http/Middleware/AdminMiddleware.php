<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check() || (!in_array(auth()->user()->role, ['super_admin', 'Manager']))) {
            abort(403, 'Unauthorized Access - Super Admin or Manager Only');
        }

        return $next($request);
    }
}
