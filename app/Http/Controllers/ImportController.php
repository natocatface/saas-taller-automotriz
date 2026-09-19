<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\Repuesto;
use Illuminate\Http\Request;

class ImportController extends Controller
{
    private array $plantillas = [
        'clientes' => ['nombre', 'apellidos', 'tipo_documento', 'documento', 'telefono', 'email', 'ciudad'],
        'repuestos' => ['codigo', 'nombre', 'categoria', 'unidad', 'precio_compra', 'precio_venta', 'stock', 'stock_minimo'],
    ];

    public function index(Request $request)
    {
        $tipo = in_array($request->get('tipo'), ['clientes', 'repuestos']) ? $request->get('tipo') : 'clientes';

        return view('modulos.importar', ['tipo' => $tipo, 'columnas' => $this->plantillas[$tipo]]);
    }

    public function plantilla(string $tipo)
    {
        abort_unless(isset($this->plantillas[$tipo]), 404);

        $cabeceras = $this->plantillas[$tipo];
        $ejemplo = $tipo === 'clientes'
            ? ['Juan', 'Pérez', 'DNI', '45678912', '987654321', 'juan@correo.com', 'Lima']
            : ['REP-100', 'Filtro de aceite', 'Filtros', 'UND', '15.00', '28.00', '20', '5'];

        $contenido = implode(',', $cabeceras)."\n".implode(',', $ejemplo)."\n";

        return response("\xEF\xBB\xBF".$contenido, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="plantilla_'.$tipo.'.csv"',
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'tipo' => 'required|in:clientes,repuestos',
            'archivo' => 'required|file|mimes:csv,txt|max:4096',
        ], [
            'archivo.required' => 'Selecciona un archivo CSV.',
            'archivo.mimes' => 'El archivo debe ser CSV (puedes guardarlo así desde Excel).',
        ]);

        $tipo = $request->tipo;
        $ruta = $request->file('archivo')->getRealPath();

        $importados = 0;
        $omitidos = 0;

        if (($handle = fopen($ruta, 'r')) !== false) {
            $primera = fgets($handle);
            $delim = substr_count($primera, ';') > substr_count($primera, ',') ? ';' : ',';
            rewind($handle);

            $fila = 0;
            while (($cols = fgetcsv($handle, 0, $delim)) !== false) {
                $fila++;
                if ($fila === 1) {
                    continue; // saltar cabecera
                }
                if (count(array_filter($cols, fn ($v) => trim((string) $v) !== '')) === 0) {
                    continue;
                }

                if ($tipo === 'clientes') {
                    $nombre = trim($cols[0] ?? '');
                    if ($nombre === '') {
                        $omitidos++;
                        continue;
                    }
                    Cliente::create([
                        'nombre' => $nombre,
                        'apellidos' => trim($cols[1] ?? ''),
                        'tipo_documento' => trim($cols[2] ?? 'DNI') ?: 'DNI',
                        'documento' => trim($cols[3] ?? ''),
                        'telefono' => trim($cols[4] ?? ''),
                        'email' => trim($cols[5] ?? '') ?: null,
                        'ciudad' => trim($cols[6] ?? ''),
                        'tipo' => 'persona',
                        'activo' => true,
                    ]);
                    $importados++;
                } else {
                    $nombre = trim($cols[1] ?? '');
                    if ($nombre === '') {
                        $omitidos++;
                        continue;
                    }
                    Repuesto::create([
                        'codigo' => trim($cols[0] ?? ''),
                        'nombre' => $nombre,
                        'categoria' => trim($cols[2] ?? ''),
                        'unidad' => trim($cols[3] ?? 'UND') ?: 'UND',
                        'precio_compra' => (float) str_replace(',', '.', $cols[4] ?? 0),
                        'precio_venta' => (float) str_replace(',', '.', $cols[5] ?? 0),
                        'stock' => (int) ($cols[6] ?? 0),
                        'stock_minimo' => (int) ($cols[7] ?? 0),
                        'activo' => true,
                    ]);
                    $importados++;
                }
            }
            fclose($handle);
        }

        $msg = "Importación completada: {$importados} registros agregados";
        $msg .= $omitidos > 0 ? ", {$omitidos} omitidos (sin nombre)." : '.';

        return redirect()->route($tipo.'.index')->with('ok', $msg);
    }
}
