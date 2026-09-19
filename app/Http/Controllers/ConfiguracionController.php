<?php

namespace App\Http\Controllers;

use App\Models\Configuracion;
use Illuminate\Http\Request;

class ConfiguracionController extends Controller
{
    public function edit()
    {
        return view('modulos.configuracion', ['config' => Configuracion::actual()]);
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'empresa' => 'required|string|max:150',
            'ruc' => 'nullable|string|max:20',
            'direccion' => 'nullable|string|max:200',
            'telefono' => 'nullable|string|max:40',
            'email' => 'nullable|email|max:150',
            'moneda' => 'required|string|max:10',
            'igv' => 'required|numeric|min:0|max:100',
            'serie_boleta' => 'required|string|max:10',
            'serie_factura' => 'required|string|max:10',
        ], [
            'empresa.required' => 'El nombre de la empresa es obligatorio.',
            'igv.required' => 'El porcentaje de IGV es obligatorio.',
        ]);

        Configuracion::actual()->update($data);

        return redirect()->route('configuracion.index')->with('ok', 'Configuración guardada correctamente.');
    }
}
