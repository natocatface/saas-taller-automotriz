<?php

namespace App\Http\Middleware;

use App\Support\Permisos;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VerificarPermiso
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        if (! $user) {
            return redirect()->route('login');
        }

        $nombre = $request->route()?->getName() ?? '';
        $modulo = explode('.', $nombre)[0];

        if ($modulo === '' || Permisos::puede($modulo, $user)) {
            return $next($request);
        }

        abort(403, 'No tienes permiso para acceder a este módulo.');
    }
}
