<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class PersonalController extends Controller
{
    public function index(Request $request)
    {
        $q = trim($request->get('q', ''));

        $personal = User::withCount('ordenes')
            ->when($q, fn ($query) => $query->where('name', 'like', "%{$q}%")
                ->orWhere('email', 'like', "%{$q}%")
                ->orWhere('rol', 'like', "%{$q}%"))
            ->latest()->paginate(15)->withQueryString();

        return view('modulos.personal', compact('personal', 'q'));
    }

    public function create()
    {
        return view('modulos.personal_form', ['user' => new User(['rol' => 'mecanico', 'activo' => true])]);
    }

    public function store(Request $request)
    {
        $data = $this->validar($request);
        $data['password'] = Hash::make($request->password);
        $data['activo'] = $request->boolean('activo');

        User::create($data);

        return redirect()->route('personal.index')->with('ok', 'Usuario creado correctamente.');
    }

    public function edit(User $user)
    {
        return view('modulos.personal_form', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $data = $this->validar($request, $user->id);
        $data['activo'] = $request->boolean('activo');

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return redirect()->route('personal.index')->with('ok', 'Usuario actualizado correctamente.');
    }

    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return redirect()->route('personal.index')->with('ok', 'No puedes eliminar tu propio usuario.');
        }
        $user->delete();

        return redirect()->route('personal.index')->with('ok', 'Usuario eliminado.');
    }

    private function validar(Request $request, ?int $id = null): array
    {
        return $request->validate([
            'name' => 'required|string|max:150',
            'email' => ['required', 'email', 'max:150', Rule::unique('users', 'email')->ignore($id)],
            'rol' => 'required|in:admin,gerente,mecanico,empleado',
            'telefono' => 'nullable|string|max:30',
            'password' => $id ? 'nullable|string|min:6' : 'required|string|min:6',
        ], [
            'name.required' => 'El nombre es obligatorio.',
            'email.required' => 'El correo es obligatorio.',
            'email.unique' => 'Ese correo ya está registrado.',
            'password.required' => 'La contraseña es obligatoria.',
            'password.min' => 'La contraseña debe tener al menos 6 caracteres.',
        ]);
    }
}
