-- ============================================================
--  Creación de la base de datos del Sistema de Taller Automotriz
--  Ejecutar este script UNA sola vez antes de correr las migraciones.
--  En Laragon puedes abrir HeidiSQL (Menú > MySQL > HeidiSQL) y ejecutarlo,
--  o usar la terminal:  mysql -u root < database/create_database.sql
-- ============================================================

CREATE DATABASE IF NOT EXISTS `saas_taller_automotriz`
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

-- Las tablas se crean automáticamente con:  php artisan migrate --seed
