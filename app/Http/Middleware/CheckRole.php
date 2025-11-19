<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  ...$roles
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $user = auth()->user();
        
        // Pimpinan (superadmin) can access everything
        if ($user->role === 'superadmin') {
            return $next($request);
        }

        // Admin can access admin and employee routes
        if ($user->role === 'admin' && (in_array('admin', $roles) || in_array('employee', $roles))) {
            return $next($request);
        }

        // Employee can only access employee routes
        if ($user->role === 'employee' && in_array('employee', $roles)) {
            return $next($request);
        }

        // If no role matches, deny access
        abort(403, 'Unauthorized access.');
    }
}
