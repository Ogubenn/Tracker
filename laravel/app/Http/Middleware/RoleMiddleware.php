<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Dinamik rol kontrolü yapar.
     *
     * Kullanım: Route::middleware(['role:admin,personel'])
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  ...$roles
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        if (!auth()->check()) {
            return redirect()->route('login')->with('error', 'Lütfen giriş yapın.');
        }

        $userRole = auth()->user()->rol;

        if (!in_array($userRole, $roles)) {
            abort(403, 'Bu işlem için yetkiniz yok.');
        }

        return $next($request);
    }
}
