<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\CajaController;
use App\Http\Controllers\CitaController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\CompraController;
use App\Http\Controllers\ConfiguracionController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ExportController;
use App\Http\Controllers\FacturacionController;
use App\Http\Controllers\FacturacionElectronicaController;
use App\Http\Controllers\GuiaRemisionController;
use App\Http\Controllers\ImportController;
use App\Http\Controllers\LandingController;
use App\Http\Controllers\OrdenController;
use App\Http\Controllers\PerfilController;
use App\Http\Controllers\PersonalController;
use App\Http\Controllers\ProveedorController;
use App\Http\Controllers\ReporteController;
use App\Http\Controllers\RepuestoController;
use App\Http\Controllers\ServicioController;
use App\Http\Controllers\VehiculoController;
use Illuminate\Support\Facades\Route;

// ===== Público =====
Route::get('/', [LandingController::class, 'index'])->name('landing');
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.attempt');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// ===== Panel Super Admin =====
Route::middleware(['auth', 'superadmin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [\App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('dashboard');
    Route::resource('planes', \App\Http\Controllers\Admin\PlanController::class)
        ->parameters(['planes' => 'plan'])->except('show');
    Route::resource('talleres', \App\Http\Controllers\Admin\TallerController::class)
        ->parameters(['talleres' => 'taller']);
    Route::get('/pagos', [\App\Http\Controllers\Admin\PagoController::class, 'index'])->name('pagos.index');
    Route::post('/pagos', [\App\Http\Controllers\Admin\PagoController::class, 'store'])->name('pagos.store');
    Route::delete('/pagos/{pago}', [\App\Http\Controllers\Admin\PagoController::class, 'destroy'])->name('pagos.destroy');
});

// ===== Área privada =====
Route::middleware(['auth', 'permiso'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // ===== Perfil =====
    Route::get('/perfil', [PerfilController::class, 'edit'])->name('perfil.index');
    Route::put('/perfil', [PerfilController::class, 'update'])->name('perfil.update');

    Route::get('/ordenes', [OrdenController::class, 'index'])->name('ordenes.index');
    Route::get('/ordenes/crear', [OrdenController::class, 'create'])->name('ordenes.create');
    Route::post('/ordenes', [OrdenController::class, 'store'])->name('ordenes.store');
    Route::get('/ordenes/{orden}', [OrdenController::class, 'show'])->name('ordenes.show');
    Route::get('/ordenes/{orden}/editar', [OrdenController::class, 'edit'])->name('ordenes.edit');
    Route::put('/ordenes/{orden}', [OrdenController::class, 'update'])->name('ordenes.update');
    Route::delete('/ordenes/{orden}', [OrdenController::class, 'destroy'])->name('ordenes.destroy');
    Route::patch('/ordenes/{orden}/estado', [OrdenController::class, 'cambiarEstado'])->name('ordenes.estado');
    Route::get('/ordenes/{orden}/imprimir', [OrdenController::class, 'imprimir'])->name('ordenes.imprimir');
    Route::get('/citas/calendario', [CitaController::class, 'calendario'])->name('citas.calendario');
    Route::resource('citas', CitaController::class)->except('show');
    Route::resource('clientes', ClienteController::class);
    Route::resource('vehiculos', VehiculoController::class);
    Route::resource('servicios', ServicioController::class)->except('show');
    Route::resource('repuestos', RepuestoController::class)->except('show');
    Route::resource('proveedores', ProveedorController::class)->except('show')->parameters(['proveedores' => 'proveedor']);
    Route::resource('personal', PersonalController::class)->except('show')->parameters(['personal' => 'user']);

    // ===== Compras =====
    Route::get('/compras', [CompraController::class, 'index'])->name('compras.index');
    Route::get('/compras/crear', [CompraController::class, 'create'])->name('compras.create');
    Route::post('/compras', [CompraController::class, 'store'])->name('compras.store');
    Route::get('/compras/{compra}', [CompraController::class, 'show'])->name('compras.show');
    Route::delete('/compras/{compra}', [CompraController::class, 'destroy'])->name('compras.destroy');

    // ===== Facturación Electrónica (SUNAT) =====
    Route::get('/facturacion/electronica', [FacturacionElectronicaController::class, 'edit'])->name('facturacion.electronica.index');
    Route::put('/facturacion/electronica', [FacturacionElectronicaController::class, 'update'])->name('facturacion.electronica.update');
    Route::post('/facturacion/electronica/probar', [FacturacionElectronicaController::class, 'probar'])->name('facturacion.electronica.probar');
    Route::get('/facturacion/electronica/monitor', [FacturacionElectronicaController::class, 'monitor'])->name('facturacion.electronica.monitor');
    Route::post('/facturacion/electronica/reintentar', [FacturacionElectronicaController::class, 'reintentar'])->name('facturacion.electronica.reintentar');

    // ===== Facturación =====
    Route::get('/facturacion', [FacturacionController::class, 'index'])->name('facturacion.index');
    Route::get('/facturacion/crear', [FacturacionController::class, 'create'])->name('facturacion.create');
    Route::post('/facturacion', [FacturacionController::class, 'store'])->name('facturacion.store');
    Route::get('/facturacion/{comprobante}/imprimir', [FacturacionController::class, 'imprimir'])->name('facturacion.imprimir');
    Route::patch('/facturacion/{comprobante}/anular', [FacturacionController::class, 'anular'])->name('facturacion.anular');
    Route::post('/facturacion/{comprobante}/reenviar', [FacturacionController::class, 'reenviar'])->name('facturacion.reenviar');
    Route::post('/facturacion/{comprobante}/consultar-baja', [FacturacionController::class, 'consultarBaja'])->name('facturacion.consultar-baja');
    Route::get('/facturacion/{comprobante}/nota', [FacturacionController::class, 'notaCreate'])->name('facturacion.nota.create');
    Route::post('/facturacion/{comprobante}/nota', [FacturacionController::class, 'notaStore'])->name('facturacion.nota.store');

    // ===== Guías de Remisión Electrónica (GRE) =====
    Route::get('/guias', [GuiaRemisionController::class, 'index'])->name('guias.index');
    Route::get('/guias/crear', [GuiaRemisionController::class, 'create'])->name('guias.create');
    Route::post('/guias', [GuiaRemisionController::class, 'store'])->name('guias.store');
    Route::get('/guias/{guia}/imprimir', [GuiaRemisionController::class, 'imprimir'])->name('guias.imprimir');
    Route::post('/guias/{guia}/consultar', [GuiaRemisionController::class, 'consultar'])->name('guias.consultar');

    // ===== Caja =====
    Route::get('/caja', [CajaController::class, 'index'])->name('caja.index');
    Route::post('/caja', [CajaController::class, 'store'])->name('caja.store');
    Route::delete('/caja/{movimiento}', [CajaController::class, 'destroy'])->name('caja.destroy');

    // ===== Reportes y configuración =====
    Route::get('/reportes', [ReporteController::class, 'index'])->name('reportes.index');

    // ===== Importaciones =====
    Route::get('/importar', [ImportController::class, 'index'])->name('import.index');
    Route::get('/importar/plantilla/{tipo}', [ImportController::class, 'plantilla'])->name('import.plantilla');
    Route::post('/importar', [ImportController::class, 'store'])->name('import.store');

    // ===== Exportaciones =====
    Route::get('/export/ordenes', [ExportController::class, 'ordenes'])->name('export.ordenes');
    Route::get('/export/ventas', [ExportController::class, 'ventas'])->name('export.ventas');
    Route::get('/export/inventario', [ExportController::class, 'inventario'])->name('export.inventario');
    Route::get('/export/reportes', [ExportController::class, 'reportesPdf'])->name('export.reportes');

    Route::get('/configuracion', [ConfiguracionController::class, 'edit'])->name('configuracion.index');
    Route::put('/configuracion', [ConfiguracionController::class, 'update'])->name('configuracion.update');
});
