<?php

namespace App\Http\Controllers;

use App\Models\Plan;

class LandingController extends Controller
{
    public function index()
    {
        if (auth()->check()) {
            return auth()->user()->rol === 'superadmin'
                ? redirect()->route('admin.dashboard')
                : redirect()->route('dashboard');
        }

        // Tolerante si aún no se ejecutaron las migraciones del SaaS
        $planes = rescue(fn () => Plan::where('activo', true)->orderBy('orden')->orderBy('precio_mensual')->get(), collect(), false);

        return view('landing', compact('planes'));
    }
}
