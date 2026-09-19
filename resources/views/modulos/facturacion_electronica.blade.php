@extends('layouts.app')
@section('title', 'Facturación Electrónica')

@section('content')
<div class="page-head">
    <div>
        <h1>Facturación Electrónica</h1>
        <div class="crumb"><a href="{{ route('dashboard') }}">Inicio</a> · Facturación · Electrónica</div>
    </div>
</div>

@include('partials.form_errors')

{{-- ===================== BANNER ===================== --}}
<div class="fe-banner">
    <div class="fe-banner-main">
        <div class="fe-banner-icon"><i class="fa-solid fa-file-invoice"></i></div>
        <div>
            <h2>Facturación Electrónica <span class="fe-flag">🇵🇪 Perú</span></h2>
            <p>Emisión de comprobantes electrónicos ante <b>SUNAT</b> · UBL 2.1 · Boletas, facturas y notas de crédito</p>
        </div>
    </div>
    <div class="fe-banner-side">
        <div class="fe-sunat-badge">SUNAT</div>
        <div class="fe-sunat-sub">Comprobantes de Pago Electrónicos</div>
    </div>
</div>

<div class="fe-pills">
    <span class="fe-pill {{ $config->habilitado ? 'is-on' : 'is-off' }}">
        <i class="fa-solid fa-circle"></i> {{ $config->habilitado ? 'Habilitada' : 'Deshabilitada' }}
    </span>
    <span class="fe-pill is-neutral">Driver: {{ $config->driver === 'none' ? 'null' : $config->driver }}</span>
    <span class="fe-pill is-neutral">Modo: {{ $config->modo }}</span>
    <span class="fe-pill {{ $certificadoExiste ? 'is-on' : 'is-off' }}">
        <i class="fa-solid {{ $certificadoExiste ? 'fa-circle-check' : 'fa-xmark' }}"></i>
        {{ $certificadoExiste ? 'Certificado encontrado' : 'Certificado no encontrado' }}
    </span>
    @unless ($greenterInstalado)
        <span class="fe-pill is-off"><i class="fa-solid fa-box"></i> Greenter no instalado</span>
    @endunless
    <div style="margin-left:auto; display:flex; gap:10px;">
        <a href="{{ route('facturacion.electronica.monitor') }}" class="btn btn-light"><i class="fa-solid fa-gauge-high"></i> Estado SUNAT</a>
        <form method="POST" action="{{ route('facturacion.electronica.probar') }}">
            @csrf
            <button type="submit" class="btn btn-primary"><i class="fa-solid fa-bolt"></i> Probar conexión con SUNAT</button>
        </form>
    </div>
</div>

<form method="POST" action="{{ route('facturacion.electronica.update') }}" enctype="multipart/form-data">
    @csrf @method('PUT')

    {{-- ===================== ESTADO Y MODO ===================== --}}
    <div class="panel" style="margin-bottom:20px;">
        <div class="panel-head">
            <div class="fe-sec-title">
                <span class="fe-sec-icon bg-teal"><i class="fa-solid fa-bolt"></i></span>
                <div><h3>Estado y modo</h3><div class="sub">Activación, forma de emisión y entorno de SUNAT</div></div>
            </div>
        </div>
        <div class="panel-body">
            <label class="fe-check">
                <input type="checkbox" name="habilitado" value="1" {{ $config->habilitado ? 'checked' : '' }}>
                <span>
                    <b>Habilitar facturación electrónica</b>
                    <small>Si está desactivada, las ventas no generan comprobante ante SUNAT.</small>
                </span>
            </label>

            <label class="fe-check">
                <input type="checkbox" name="emitir_automatico" value="1" {{ $config->emitir_automatico ? 'checked' : '' }}>
                <span>
                    <b>Emitir automáticamente al cerrar la venta</b>
                    <small>Cada boleta o factura se envía apenas se registra el comprobante.</small>
                </span>
            </label>

            <div class="form-grid" style="margin-top:16px;">
                <div>
                    <label class="form-label">Driver de emisión</label>
                    <select name="driver" class="form-control">
                        <option value="none" {{ $config->driver === 'none' ? 'selected' : '' }}>Ninguno (no emite, deja pendiente)</option>
                        <option value="greenter" {{ $config->driver === 'greenter' ? 'selected' : '' }}>Greenter (firma local UBL 2.1)</option>
                    </select>
                </div>
                <div>
                    <label class="form-label">Entorno SUNAT</label>
                    <select name="modo" class="form-control">
                        <option value="beta" {{ $config->modo === 'beta' ? 'selected' : '' }}>Beta (homologación / pruebas)</option>
                        <option value="produccion" {{ $config->modo === 'produccion' ? 'selected' : '' }}>Producción</option>
                    </select>
                </div>
            </div>
        </div>
    </div>

    {{-- ===================== DATOS DEL EMISOR ===================== --}}
    <div class="panel" style="margin-bottom:20px;">
        <div class="panel-head">
            <div class="fe-sec-title">
                <span class="fe-sec-icon bg-blue"><i class="fa-solid fa-building"></i></span>
                <div><h3>Datos del emisor</h3><div class="sub">Aparecen en el comprobante electrónico</div></div>
            </div>
        </div>
        <div class="panel-body">
            <div class="form-grid">
                <div>
                    <label class="form-label">RUC *</label>
                    <input type="text" name="ruc" class="form-control" value="{{ old('ruc', $config->ruc) }}" maxlength="11" required>
                </div>
                <div>
                    <label class="form-label">Razón social *</label>
                    <input type="text" name="razon_social" class="form-control" value="{{ old('razon_social', $config->razon_social) }}" required>
                </div>
                <div>
                    <label class="form-label">Nombre comercial</label>
                    <input type="text" name="nombre_comercial" class="form-control" value="{{ old('nombre_comercial', $config->nombre_comercial) }}">
                </div>
                <div>
                    <label class="form-label">Dirección fiscal</label>
                    <input type="text" name="direccion_fiscal" class="form-control" value="{{ old('direccion_fiscal', $config->direccion_fiscal) }}">
                </div>
                <div>
                    <label class="form-label">Ubigeo</label>
                    <input type="text" name="ubigeo" class="form-control" value="{{ old('ubigeo', $config->ubigeo) }}" maxlength="6">
                </div>
                <div>
                    <label class="form-label">Departamento</label>
                    <input type="text" name="departamento" class="form-control" value="{{ old('departamento', $config->departamento) }}">
                </div>
                <div>
                    <label class="form-label">Provincia</label>
                    <input type="text" name="provincia" class="form-control" value="{{ old('provincia', $config->provincia) }}">
                </div>
                <div>
                    <label class="form-label">Distrito</label>
                    <input type="text" name="distrito" class="form-control" value="{{ old('distrito', $config->distrito) }}">
                </div>
            </div>
        </div>
    </div>

    {{-- ===================== CREDENCIALES SUNAT ===================== --}}
    <div class="panel" style="margin-bottom:20px;">
        <div class="panel-head">
            <div class="fe-sec-title">
                <span class="fe-sec-icon bg-amber"><i class="fa-solid fa-key"></i></span>
                <div><h3>Credenciales SUNAT</h3><div class="sub">Clave SOL y certificado digital</div></div>
            </div>
        </div>
        <div class="panel-body">
            <div class="fe-note">
                <i class="fa-solid fa-circle-info"></i>
                En <b>beta</b> puedes usar RUC <b>20000000001</b> con usuario y clave <b>MODDATOS</b>.
            </div>

            <div class="form-grid">
                <div>
                    <label class="form-label">Usuario Clave SOL</label>
                    <input type="text" name="usuario_sol" class="form-control" value="{{ old('usuario_sol', $config->usuario_sol) }}">
                </div>
                <div>
                    <label class="form-label">Clave SOL</label>
                    <input type="password" name="clave_sol" class="form-control" placeholder="{{ $config->clave_sol ? '•••••••• (sin cambios)' : '' }}" autocomplete="new-password">
                </div>
                <div style="grid-column:1/-1;">
                    <label class="form-label">Ruta del certificado (.pem)</label>
                    <input type="text" name="certificado_path" class="form-control" value="{{ old('certificado_path', $config->certificado_path) }}">
                    @if ($certificadoExiste)
                        <small class="fe-hint ok"><i class="fa-solid fa-circle-check"></i> Certificado encontrado en la ruta indicada.</small>
                    @else
                        <small class="fe-hint bad"><i class="fa-solid fa-triangle-exclamation"></i> No se encontró el certificado en la ruta indicada.</small>
                    @endif
                </div>
                <div>
                    <label class="form-label">Contraseña del certificado</label>
                    <input type="password" name="certificado_password" class="form-control" placeholder="{{ $config->certificado_password ? '•••••••• (sin cambios)' : '' }}" autocomplete="new-password">
                </div>
                <div>
                    <label class="form-label">Subir certificado (.pem / .pfx)</label>
                    <input type="file" name="certificado_file" class="form-control" accept=".pem,.pfx,.p12" style="padding:8px 12px;">
                </div>
            </div>

            <div class="fe-note" style="margin-top:18px;">
                <i class="fa-solid fa-truck-fast"></i>
                Credenciales del <b>API REST</b> (solo para <b>Guías de Remisión Electrónica</b>). Se obtienen en SUNAT SOL → Empresas → Guías.
            </div>
            <div class="form-grid">
                <div>
                    <label class="form-label">Client ID (API GRE)</label>
                    <input type="text" name="client_id" class="form-control" value="{{ old('client_id', $config->client_id) }}" autocomplete="off">
                </div>
                <div>
                    <label class="form-label">Client Secret (API GRE)</label>
                    <input type="password" name="client_secret" class="form-control" placeholder="{{ $config->client_secret ? '•••••••• (sin cambios)' : '' }}" autocomplete="new-password">
                </div>
            </div>
        </div>
    </div>

    <div style="display:flex; gap:12px; justify-content:space-between; align-items:center; margin-bottom:30px;">
        <a href="{{ route('facturacion.index') }}" class="btn btn-light"><i class="fa-solid fa-arrow-left"></i> Volver</a>
        <button type="submit" class="btn btn-primary"><i class="fa-solid fa-floppy-disk"></i> Guardar configuración</button>
    </div>
</form>

<style>
    .fe-banner {
        display:flex; justify-content:space-between; align-items:flex-start; gap:20px;
        background: linear-gradient(135deg, var(--primary) 0%, #14b8a6 55%, #22d3ee 100%);
        color:#fff; border-radius:16px; padding:22px 26px; margin-bottom:16px;
        box-shadow:0 12px 30px rgba(13,148,136,.28);
    }
    .fe-banner-main { display:flex; gap:16px; align-items:flex-start; }
    .fe-banner-icon {
        width:56px; height:56px; border-radius:14px; background:rgba(255,255,255,.18);
        display:grid; place-items:center; font-size:26px; flex-shrink:0;
    }
    .fe-banner h2 { font-size:20px; font-weight:800; margin:0 0 4px; }
    .fe-banner p { font-size:13px; opacity:.92; margin:0; max-width:560px; }
    .fe-flag { font-size:13px; font-weight:600; background:rgba(255,255,255,.2); padding:2px 10px; border-radius:20px; margin-left:6px; vertical-align:middle; }
    .fe-banner-side { text-align:right; flex-shrink:0; }
    .fe-sunat-badge { font-weight:800; letter-spacing:1px; background:#fff; color:var(--primary-dark); padding:5px 14px; border-radius:8px; display:inline-block; }
    .fe-sunat-sub { font-size:11px; opacity:.9; margin-top:6px; }

    .fe-pills { display:flex; flex-wrap:wrap; align-items:center; gap:10px; margin-bottom:22px; }
    .fe-pill { display:inline-flex; align-items:center; gap:7px; font-size:12.5px; font-weight:600; padding:7px 14px; border-radius:20px; border:1px solid var(--border); background:#fff; }
    .fe-pill i { font-size:9px; }
    .fe-pill.is-on { color:#15803d; border-color:#bbf7d0; background:#f0fdf4; }
    .fe-pill.is-off { color:#b91c1c; border-color:#fecaca; background:#fef2f2; }
    .fe-pill.is-neutral { color:var(--text-muted); background:#f6f8fa; }

    .fe-sec-title { display:flex; align-items:center; gap:12px; }
    .fe-sec-icon { width:38px; height:38px; border-radius:10px; display:grid; place-items:center; color:#fff; font-size:15px; }

    .fe-check { display:flex; align-items:flex-start; gap:12px; padding:14px 16px; border:1px solid var(--border); border-radius:12px; margin-bottom:12px; cursor:pointer; transition:border-color .15s, background .15s; }
    .fe-check:hover { border-color:var(--primary); background:#f6fefd; }
    .fe-check input { width:18px; height:18px; margin-top:2px; accent-color:var(--primary); cursor:pointer; }
    .fe-check span { display:flex; flex-direction:column; gap:2px; }
    .fe-check b { font-size:13.5px; color:var(--text); }
    .fe-check small { font-size:12px; color:var(--text-muted); }

    .fe-note { display:flex; align-items:center; gap:10px; background:#eff6ff; color:#1e40af; border:1px solid #bfdbfe; border-radius:10px; padding:11px 14px; font-size:12.5px; margin-bottom:16px; }
    .fe-hint { display:inline-flex; align-items:center; gap:6px; font-size:12px; margin-top:6px; }
    .fe-hint.ok { color:#15803d; }
    .fe-hint.bad { color:#b45309; }

    @media (max-width:640px){ .fe-banner{ flex-direction:column; } .fe-banner-side{ text-align:left; } }
</style>
@endsection
