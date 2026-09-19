<?php

namespace App\Http\Controllers;

class PaginaController extends Controller
{
    public function compras()
    {
        return $this->render('Compras', 'fa-cart-shopping',
            'Registra órdenes de compra a proveedores, recepción de mercadería y actualización automática de stock.');
    }

    public function facturacion()
    {
        return $this->render('Facturación y Ventas', 'fa-file-invoice-dollar',
            'Emite comprobantes (boleta/factura), lleva el control de ventas y exporta tus reportes de facturación.');
    }

    public function caja()
    {
        return $this->render('Caja y Pagos', 'fa-cash-register',
            'Controla ingresos y egresos, cierres de caja diarios y registro de pagos de las órdenes de servicio.');
    }

    public function reportes()
    {
        return $this->render('Reportes', 'fa-chart-line',
            'Reportes de ingresos, servicios más solicitados, productividad de mecánicos y rotación de inventario.');
    }

    public function configuracion()
    {
        return $this->render('Configuración', 'fa-gear',
            'Datos de la empresa, usuarios y roles, impuestos, series de comprobantes y parámetros del sistema.');
    }

    private function render(string $titulo, string $icono, string $descripcion)
    {
        return view('modulos.placeholder', compact('titulo', 'icono', 'descripcion'));
    }
}
