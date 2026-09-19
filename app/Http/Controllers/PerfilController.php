<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class PerfilController extends Controller
{
    public function edit()
    {
        return view('modulos.perfil', ['user' => auth()->user()]);
    }

    public function update(Request $request)
    {
        $user = $request->user();

        $data = $request->validate([
            'name' => 'required|string|max:150',
            'email' => ['required', 'email', 'max:150', Rule::unique('users', 'email')->ignore($user->id)],
            'telefono' => 'nullable|string|max:30',
            'password' => 'nullable|string|min:6|confirmed',
        ], [
            'name.required' => 'El nombre es obligatorio.',
            'email.unique' => 'Ese correo ya está en uso.',
            'password.min' => 'La contraseña debe tener al menos 6 caracteres.',
            'password.confirmed' => 'La confirmación de contraseña no coincide.',
        ]);

        $user->name = $data['name'];
        $user->email = $data['email'];
        $user->telefono = $data['telefono'] ?? null;
        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }
        $user->save();

        return redirect()->route('perfil.index')->with('ok', 'Perfil actualizado correctamente.');
    }
}
