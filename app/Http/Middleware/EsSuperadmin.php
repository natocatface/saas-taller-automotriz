<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EsSuperadmin
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        if (! $user) {
            return redirect()->route('login');
        }
        if ($user->rol !== 'superadmin') {
            abort(403, 'Área exclusiva del Super Administrador.');
        }

        return $next($request);
    }
}
