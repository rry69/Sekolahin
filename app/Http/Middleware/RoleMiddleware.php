<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        if (!$request->user()) {
            abort(403, 'Unauthorized');
        }
        
        $userRole = $request->user()->role?->name;
        
        if (!$userRole || !in_array($userRole, $roles)) {
            abort(403, 'Unauthorized access');
        }
        
        return $next($request);
    }
}
