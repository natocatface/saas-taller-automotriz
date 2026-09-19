# 🚗 AutoTaller Pro — Sistema SaaS de Taller Automotriz

Sistema de gestión para talleres automotrices desarrollado en **Laravel 11 + MySQL**.
Incluye login, dashboard con indicadores, y menú lateral con todos los módulos del negocio.

---

## ✅ Requisitos (ya los tienes con Laragon)

- PHP **8.2 o superior**
- Composer
- MySQL
- Laragon (recomendado)

---

## 🚀 Instalación paso a paso (Laragon)

Abre la **Terminal de Laragon** (botón *Terminal*) dentro de la carpeta del proyecto
y ejecuta en orden:

```bash
# 1) Instalar dependencias de Laravel (descarga el framework)
composer install

# 2) Generar la llave de la aplicación
php artisan key:generate

# 3) Crear la base de datos "saas_taller_automotriz"
#    Opción A (terminal):
mysql -u root -e "CREATE DATABASE IF NOT EXISTS saas_taller_automotriz CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
#    Opción B: abre HeidiSQL (Menú Laragon > MySQL) y ejecuta database/create_database.sql

# 4) Crear las tablas y cargar datos de demostración
php artisan migrate --seed

# 5) Levantar el servidor
php artisan serve
```

Luego abre en el navegador: **http://localhost:8000**

> 💡 Alternativa con Laragon (dominio bonito): copia la carpeta a `C:\laragon\www\`,
> reinicia Laragon y entra a `http://saas_taller-automotriz.test`.
> Recuerda apuntar el *Document Root* a la subcarpeta `public/`.

---

## 🔑 Acceso de demostración

| Rol           | Correo               | Contraseña |
|---------------|----------------------|------------|
| Administrador | `admin@taller.com`   | `password` |
| Gerente       | `gerente@taller.com` | `password` |
| Mecánico      | `carlos@taller.com`  | `password` |

---

## ⚙️ Configuración de MySQL

Ya viene lista en el archivo `.env`:

```
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=saas_taller_automotriz
DB_USERNAME=root
DB_PASSWORD=
```

Si tu MySQL tiene contraseña, edítala en `DB_PASSWORD`.

---

## 🧩 Módulos incluidos

**Operaciones:** Órdenes de servicio · Citas/Agenda · Servicios
**Clientes:** Clientes · Vehículos
**Inventario:** Repuestos · Proveedores · Compras
**Finanzas:** Facturación · Caja y pagos
**Administración:** Personal · Reportes · Configuración

En esta primera entrega están **100% funcionales**: Login, Dashboard, Órdenes,
Citas, Servicios, Clientes, Vehículos, Repuestos, Proveedores y Personal
(con datos reales de la base de datos).
Compras, Facturación, Caja, Reportes y Configuración quedan listos en el menú
como módulos a desarrollar en las siguientes fases (CRUD interno).

---

## 🛠️ Comandos útiles

```bash
php artisan migrate:fresh --seed   # Reinicia la BD con datos demo
php artisan optimize:clear         # Limpia cachés si algo no refresca
```

---

## 📁 Estructura principal

```
app/Http/Controllers   → Controladores (Dashboard, Orden, Cliente, ...)
app/Models             → Modelos Eloquent
database/migrations    → Estructura de la base de datos
database/seeders       → Datos de demostración
resources/views        → Vistas Blade (layout, login, dashboard, módulos)
public/css/app.css     → Diseño visual del sistema
routes/web.php         → Rutas de la aplicación
```
