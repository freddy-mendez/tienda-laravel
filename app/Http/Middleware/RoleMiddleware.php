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
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        if (! $request->user() || ! $request->user()->role) {
            return response()->json(['message' => 'No tiene permisos'], 403);
        } else if ($request->user()->role->nombre === 'admin') {
            return $next($request);
        } else if ($request->user()->role->nombre === $role) {
            return $next($request);
        }
        return response()->json(['message' => 'No tiene permisos'], 403);
    }
}
