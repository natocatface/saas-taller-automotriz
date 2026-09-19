-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Versión del servidor:         8.4.3 - MySQL Community Server - GPL
-- SO del servidor:              Win64
-- HeidiSQL Versión:             12.8.0.6908
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


-- Volcando estructura de base de datos para saas_taller_automotriz
CREATE DATABASE IF NOT EXISTS `saas_taller_automotriz` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci */ /*!80016 DEFAULT ENCRYPTION='N' */;
USE `saas_taller_automotriz`;

-- Volcando estructura para tabla saas_taller_automotriz.citas
CREATE TABLE IF NOT EXISTS `citas` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `cliente_id` bigint unsigned DEFAULT NULL,
  `vehiculo_id` bigint unsigned DEFAULT NULL,
  `mecanico_id` bigint unsigned DEFAULT NULL,
  `titulo` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `fecha_hora` datetime NOT NULL,
  `duracion_min` int NOT NULL DEFAULT '60',
  `motivo` text COLLATE utf8mb4_unicode_ci,
  `estado` enum('pendiente','confirmada','atendida','cancelada') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pendiente',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `citas_cliente_id_foreign` (`cliente_id`),
  KEY `citas_vehiculo_id_foreign` (`vehiculo_id`),
  KEY `citas_mecanico_id_foreign` (`mecanico_id`),
  CONSTRAINT `citas_cliente_id_foreign` FOREIGN KEY (`cliente_id`) REFERENCES `clientes` (`id`) ON DELETE SET NULL,
  CONSTRAINT `citas_mecanico_id_foreign` FOREIGN KEY (`mecanico_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `citas_vehiculo_id_foreign` FOREIGN KEY (`vehiculo_id`) REFERENCES `vehiculos` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla saas_taller_automotriz.citas: ~18 rows (aproximadamente)
DELETE FROM `citas`;
INSERT INTO `citas` (`id`, `cliente_id`, `vehiculo_id`, `mecanico_id`, `titulo`, `fecha_hora`, `duracion_min`, `motivo`, `estado`, `created_at`, `updated_at`) VALUES
	(1, 2, 2, 5, 'Mantenimiento preventivo', '2026-07-07 15:00:43', 60, 'Cliente solicita revisión general.', 'confirmada', '2026-07-04 02:52:43', '2026-07-04 02:52:43'),
	(2, 2, 2, 3, 'Mantenimiento preventivo', '2026-07-05 12:00:43', 60, 'Cliente solicita revisión general.', 'confirmada', '2026-07-04 02:52:43', '2026-07-04 02:52:43'),
	(3, 3, 3, 4, 'Mantenimiento preventivo', '2026-07-04 13:00:43', 60, 'Cliente solicita revisión general.', 'confirmada', '2026-07-04 02:52:43', '2026-07-04 02:52:43'),
	(4, 3, 3, 4, 'Mantenimiento preventivo', '2026-07-12 10:00:43', 60, 'Cliente solicita revisión general.', 'pendiente', '2026-07-04 02:52:43', '2026-07-04 02:52:43'),
	(5, 5, 5, 2, 'Mantenimiento preventivo', '2026-07-12 15:00:43', 60, 'Cliente solicita revisión general.', 'confirmada', '2026-07-04 02:52:43', '2026-07-04 02:52:43'),
	(6, 4, 4, 5, 'Mantenimiento preventivo', '2026-07-06 17:00:43', 60, 'Cliente solicita revisión general.', 'pendiente', '2026-07-04 02:52:43', '2026-07-04 02:52:43'),
	(7, 3, 3, 2, 'Mantenimiento preventivo', '2026-07-06 11:00:43', 60, 'Cliente solicita revisión general.', 'pendiente', '2026-07-04 02:52:43', '2026-07-04 02:52:43'),
	(8, 5, 5, 5, 'Mantenimiento preventivo', '2026-07-07 11:00:43', 60, 'Cliente solicita revisión general.', 'pendiente', '2026-07-04 02:52:43', '2026-07-04 02:52:43'),
	(9, 35, 31, 4, 'Cita explicabo', '2026-07-12 16:56:06', 60, NULL, 'pendiente', '2026-02-07 03:05:54', '2026-07-10 13:34:00'),
	(10, 28, 30, 1, 'Cita ut', '2026-04-12 04:43:47', 60, NULL, 'atendida', '2026-03-18 17:35:32', '2026-07-10 13:34:00'),
	(11, 34, 36, 2, 'Cita culpa', '2026-01-18 11:25:47', 60, NULL, 'atendida', '2026-06-18 12:46:52', '2026-07-10 13:34:00'),
	(12, 31, 33, 3, 'Cita cupiditate', '2026-02-01 02:36:51', 60, NULL, 'confirmada', '2026-07-02 09:02:40', '2026-07-10 13:34:00'),
	(13, 34, 31, 3, 'Cita error', '2026-02-14 20:30:36', 60, NULL, 'atendida', '2026-04-08 00:52:44', '2026-07-10 13:34:00'),
	(14, 35, 28, 4, 'Cita quia', '2026-05-25 08:35:11', 60, NULL, 'atendida', '2026-03-06 00:06:16', '2026-07-10 13:34:00'),
	(15, 28, 28, 3, 'Cita doloribus', '2026-03-05 07:28:48', 60, NULL, 'pendiente', '2026-05-19 19:05:17', '2026-07-10 13:34:00'),
	(16, 32, 33, 2, 'Cita et', '2026-04-15 11:13:50', 60, NULL, 'atendida', '2026-04-11 09:36:23', '2026-07-10 13:34:00'),
	(17, 33, 28, 6, 'Cita dicta', '2026-06-26 08:21:59', 60, NULL, 'atendida', '2026-05-26 03:03:50', '2026-07-10 13:34:00'),
	(18, 36, 36, 2, 'Cita velit', '2026-05-12 11:03:20', 60, NULL, 'pendiente', '2026-03-09 08:24:19', '2026-07-10 13:34:00');

-- Volcando estructura para tabla saas_taller_automotriz.clientes
CREATE TABLE IF NOT EXISTS `clientes` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tipo_documento` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'DNI',
  `documento` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nombre` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `apellidos` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `razon_social` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `telefono` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `direccion` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ciudad` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tipo` enum('persona','empresa') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'persona',
  `observaciones` text COLLATE utf8mb4_unicode_ci,
  `activo` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=37 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla saas_taller_automotriz.clientes: ~36 rows (aproximadamente)
DELETE FROM `clientes`;
INSERT INTO `clientes` (`id`, `tipo_documento`, `documento`, `nombre`, `apellidos`, `razon_social`, `telefono`, `email`, `direccion`, `ciudad`, `tipo`, `observaciones`, `activo`, `created_at`, `updated_at`) VALUES
	(1, 'DNI', '45678912', 'Juan', 'Pérez Gómez', NULL, '987654321', 'juan.perez@gmail.com', NULL, 'Lima', 'persona', NULL, 1, '2026-07-04 02:52:43', '2026-07-04 02:52:43'),
	(2, 'DNI', '41236547', 'María', 'López Vargas', NULL, '912345678', 'maria.lopez@gmail.com', NULL, 'Lima', 'persona', NULL, 1, '2026-07-04 02:52:43', '2026-07-04 02:52:43'),
	(3, 'DNI', '40012345', 'Pedro', 'Sánchez Ruiz', NULL, '998877665', 'pedro.sanchez@gmail.com', NULL, 'Lima', 'persona', NULL, 1, '2026-07-04 02:52:43', '2026-07-04 02:52:43'),
	(4, 'DNI', '44556677', 'Ana', 'Flores Díaz', NULL, '955443322', 'ana.flores@gmail.com', NULL, 'Lima', 'persona', NULL, 1, '2026-07-04 02:52:43', '2026-07-04 02:52:43'),
	(5, 'DNI', '43219876', 'Roberto', 'Castro León', NULL, '966554433', 'roberto.castro@gmail.com', NULL, 'Lima', 'persona', NULL, 1, '2026-07-04 02:52:43', '2026-07-04 02:52:43'),
	(6, 'RUC', '20601234567', 'Transportes El Rápido', NULL, 'Transportes El Rápido S.A.C.', '014441122', 'flota@elrapido.com', NULL, 'Lima', 'empresa', NULL, 1, '2026-07-04 02:52:43', '2026-07-04 02:52:43'),
	(7, 'DNI', '07419916', 'Keira', 'Altenwerth', NULL, '984589749', 'dickens.monty@example.org', NULL, 'Lima', 'persona', NULL, 1, '2026-05-24 20:03:37', '2026-07-10 13:32:22'),
	(8, 'DNI', '75037725', 'Enos', 'Quigley', NULL, '946504776', 'dell70@example.net', NULL, 'Lima', 'persona', NULL, 1, '2026-05-11 20:09:13', '2026-07-10 13:32:22'),
	(9, 'DNI', '86525327', 'Wiley', 'Mosciski', NULL, '938641425', 'dwill@example.org', NULL, 'Lima', 'persona', NULL, 1, '2026-02-04 14:48:40', '2026-07-10 13:32:22'),
	(10, 'DNI', '72531407', 'Kelley', 'West', NULL, '947932366', 'sonny00@example.com', NULL, 'Lima', 'persona', NULL, 1, '2026-05-03 18:40:09', '2026-07-10 13:32:22'),
	(11, 'DNI', '73961140', 'Charity', 'Feest', NULL, '914780559', 'robbie.von@example.net', NULL, 'Lima', 'persona', NULL, 1, '2026-06-20 02:10:07', '2026-07-10 13:32:22'),
	(12, 'DNI', '44380682', 'Colt', 'Ortiz', NULL, '930000321', 'magnus48@example.net', NULL, 'Lima', 'persona', NULL, 1, '2026-04-08 11:47:19', '2026-07-10 13:32:22'),
	(13, 'DNI', '70144380', 'Royal', 'Will', NULL, '967611929', 'schamberger.nelson@example.org', NULL, 'Lima', 'persona', NULL, 1, '2026-01-14 23:03:57', '2026-07-10 13:32:22'),
	(14, 'DNI', '70159562', 'Deven', 'Volkman', NULL, '911018882', 'shanna12@example.net', NULL, 'Lima', 'persona', NULL, 1, '2026-02-13 01:46:46', '2026-07-10 13:32:22'),
	(15, 'DNI', '54461381', 'Dorthy', 'Farrell', NULL, '975062226', 'schiller.alia@example.org', NULL, 'Lima', 'persona', NULL, 1, '2026-04-15 05:35:13', '2026-07-10 13:32:22'),
	(16, 'DNI', '49902268', 'Clementine', 'Deckow', NULL, '932758438', 'elvera.ward@example.net', NULL, 'Lima', 'persona', NULL, 1, '2026-02-01 13:39:50', '2026-07-10 13:32:22'),
	(17, 'DNI', '02563865', 'Era', 'Treutel', NULL, '981284591', 'theodore69@example.org', NULL, 'Lima', 'persona', NULL, 1, '2026-03-22 05:28:50', '2026-07-10 13:33:36'),
	(18, 'DNI', '27099994', 'Ansel', 'Schultz', NULL, '990100824', 'kozey.gerry@example.net', NULL, 'Lima', 'persona', NULL, 1, '2026-01-31 14:47:24', '2026-07-10 13:33:36'),
	(19, 'DNI', '16543119', 'Emerald', 'Walter', NULL, '977890343', 'eldora.kohler@example.org', NULL, 'Lima', 'persona', NULL, 1, '2026-03-15 00:58:41', '2026-07-10 13:33:36'),
	(20, 'DNI', '51099587', 'Angelina', 'D\'Amore', NULL, '943894064', 'pdamore@example.net', NULL, 'Lima', 'persona', NULL, 1, '2026-06-07 05:54:46', '2026-07-10 13:33:36'),
	(21, 'DNI', '67053796', 'Anika', 'Bergnaum', NULL, '943132605', 'cade11@example.net', NULL, 'Lima', 'persona', NULL, 1, '2026-07-02 06:54:21', '2026-07-10 13:33:36'),
	(22, 'DNI', '09918036', 'Catherine', 'Heller', NULL, '908023971', 'ynolan@example.org', NULL, 'Lima', 'persona', NULL, 1, '2026-05-19 12:02:21', '2026-07-10 13:33:36'),
	(23, 'DNI', '48647178', 'Turner', 'Lowe', NULL, '953052225', 'pietro98@example.net', NULL, 'Lima', 'persona', NULL, 1, '2026-03-21 04:32:00', '2026-07-10 13:33:36'),
	(24, 'DNI', '49960957', 'Jacques', 'Sawayn', NULL, '917146304', 'jackeline.wilkinson@example.com', NULL, 'Lima', 'persona', NULL, 1, '2026-02-01 19:38:33', '2026-07-10 13:33:36'),
	(25, 'DNI', '05909288', 'Ethan', 'White', NULL, '936283988', 'eleanora26@example.com', NULL, 'Lima', 'persona', NULL, 1, '2026-01-15 01:06:37', '2026-07-10 13:33:36'),
	(26, 'DNI', '36009143', 'Tyler', 'Kulas', NULL, '974034421', 'zechariah.bode@example.net', NULL, 'Lima', 'persona', NULL, 1, '2026-03-30 08:17:49', '2026-07-10 13:33:36'),
	(27, 'DNI', '69080800', 'Max', 'McKenzie', NULL, '910815388', 'sylvester.gleason@example.org', NULL, 'Lima', 'persona', NULL, 1, '2026-05-08 20:15:56', '2026-07-10 13:33:59'),
	(28, 'DNI', '21584312', 'Adolphus', 'Mayer', NULL, '992216113', 'swift.jorge@example.com', NULL, 'Lima', 'persona', NULL, 1, '2026-05-20 09:56:42', '2026-07-10 13:33:59'),
	(29, 'DNI', '39559558', 'Laverna', 'Runte', NULL, '984993594', 'frogahn@example.com', NULL, 'Lima', 'persona', NULL, 1, '2026-04-05 23:53:36', '2026-07-10 13:33:59'),
	(30, 'DNI', '11791318', 'Kenneth', 'Jenkins', NULL, '963824934', 'ankunding.mekhi@example.org', NULL, 'Lima', 'persona', NULL, 1, '2026-06-30 09:41:54', '2026-07-10 13:34:00'),
	(31, 'DNI', '19622811', 'Graciela', 'Jaskolski', NULL, '929983435', 'mokuneva@example.net', NULL, 'Lima', 'persona', NULL, 1, '2026-05-30 13:35:25', '2026-07-10 13:34:00'),
	(32, 'DNI', '26575621', 'Zane', 'Swaniawski', NULL, '909524414', 'zola44@example.org', NULL, 'Lima', 'persona', NULL, 1, '2026-03-12 08:22:41', '2026-07-10 13:34:00'),
	(33, 'DNI', '93244275', 'Yoshiko', 'Treutel', NULL, '994780710', 'garrick14@example.net', NULL, 'Lima', 'persona', NULL, 1, '2026-07-09 12:56:53', '2026-07-10 13:34:00'),
	(34, 'DNI', '96017208', 'Norval', 'Hartmann', NULL, '939695835', 'nmitchell@example.com', NULL, 'Lima', 'persona', NULL, 1, '2026-05-26 09:11:39', '2026-07-10 13:34:00'),
	(35, 'DNI', '16902183', 'Terrence', 'Dibbert', NULL, '973230776', 'easton.ward@example.com', NULL, 'Lima', 'persona', NULL, 1, '2026-02-16 15:25:53', '2026-07-10 13:34:00'),
	(36, 'DNI', '80841184', 'Rowan', 'Kerluke', NULL, '953153060', 'stracke.dorcas@example.com', NULL, 'Lima', 'persona', NULL, 1, '2026-04-27 23:32:34', '2026-07-10 13:34:00');

-- Volcando estructura para tabla saas_taller_automotriz.compras
CREATE TABLE IF NOT EXISTS `compras` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `numero` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `proveedor_id` bigint unsigned NOT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `fecha` date NOT NULL,
  `tipo_documento` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'factura',
  `documento_ref` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `subtotal` decimal(12,2) NOT NULL DEFAULT '0.00',
  `impuesto` decimal(12,2) NOT NULL DEFAULT '0.00',
  `total` decimal(12,2) NOT NULL DEFAULT '0.00',
  `estado` enum('registrada','anulada') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'registrada',
  `observaciones` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `compras_numero_unique` (`numero`),
  KEY `compras_proveedor_id_foreign` (`proveedor_id`),
  KEY `compras_user_id_foreign` (`user_id`),
  CONSTRAINT `compras_proveedor_id_foreign` FOREIGN KEY (`proveedor_id`) REFERENCES `proveedores` (`id`) ON DELETE CASCADE,
  CONSTRAINT `compras_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla saas_taller_automotriz.compras: ~22 rows (aproximadamente)
DELETE FROM `compras`;
INSERT INTO `compras` (`id`, `numero`, `proveedor_id`, `user_id`, `fecha`, `tipo_documento`, `documento_ref`, `subtotal`, `impuesto`, `total`, `estado`, `observaciones`, `created_at`, `updated_at`) VALUES
	(1, 'CMP-0001', 2, 1, '2026-02-15', 'factura', 'F129-6875', 3128.00, 563.04, 3691.04, 'registrada', NULL, '2026-07-04 10:29:18', '2026-07-04 10:29:18'),
	(2, 'CMP-0002', 3, 1, '2026-04-29', 'factura', 'F111-9564', 4885.00, 879.30, 5764.30, 'registrada', NULL, '2026-07-04 10:29:18', '2026-07-04 10:29:18'),
	(3, 'CMP-0003', 1, 1, '2026-05-13', 'factura', 'F485-8104', 823.00, 148.14, 971.14, 'registrada', NULL, '2026-07-04 10:29:18', '2026-07-04 10:29:19'),
	(4, 'CMP-0004', 2, 1, '2026-01-23', 'factura', 'F271-6224', 5435.00, 978.30, 6413.30, 'registrada', NULL, '2026-07-04 10:29:19', '2026-07-04 10:29:19'),
	(5, 'CMP-0005', 2, 1, '2026-04-29', 'factura', 'F278-3288', 2219.00, 399.42, 2618.42, 'registrada', NULL, '2026-07-04 10:29:19', '2026-07-04 10:29:19'),
	(6, 'CMP-0006', 2, 1, '2026-06-17', 'factura', 'F567-4765', 2650.00, 477.00, 3127.00, 'registrada', NULL, '2026-07-04 10:29:19', '2026-07-04 10:29:19'),
	(7, 'CMP-0007', 2, 1, '2026-02-25', 'factura', 'F923-2656', 2787.00, 501.66, 3288.66, 'registrada', NULL, '2026-07-04 10:29:19', '2026-07-04 10:29:19'),
	(8, 'CMP-0008', 1, 1, '2026-03-31', 'factura', 'F192-7476', 4740.00, 853.20, 5593.20, 'registrada', NULL, '2026-07-04 10:29:19', '2026-07-04 10:29:19'),
	(9, 'CMP-0009', 3, 1, '2026-04-23', 'factura', 'F132-6657', 4170.00, 750.60, 4920.60, 'registrada', NULL, '2026-07-04 10:29:19', '2026-07-04 10:29:19'),
	(10, 'CMP-0010', 1, 1, '2026-06-03', 'factura', 'F914-5285', 1365.00, 245.70, 1610.70, 'registrada', NULL, '2026-07-04 10:29:19', '2026-07-04 10:29:19'),
	(11, 'CMP-0011', 2, 1, '2026-03-12', 'factura', 'F989-3839', 1949.00, 350.82, 2299.82, 'registrada', NULL, '2026-07-04 10:29:19', '2026-07-04 10:29:19'),
	(12, 'CMP-0012', 3, 1, '2026-05-13', 'factura', 'F145-3757', 780.00, 140.40, 920.40, 'registrada', NULL, '2026-07-04 10:29:19', '2026-07-04 10:29:19'),
	(13, 'C-DUMMY-25094', 14, 1, '2026-04-10', 'factura', NULL, 0.00, 0.00, 1981.35, 'registrada', NULL, '2026-04-11 01:26:05', '2026-04-11 01:26:05'),
	(14, 'C-DUMMY-12451', 21, 1, '2026-03-15', 'factura', NULL, 0.00, 0.00, 1683.01, 'registrada', NULL, '2026-03-15 09:05:01', '2026-03-15 09:05:01'),
	(15, 'C-DUMMY-43956', 20, 1, '2026-04-22', 'factura', NULL, 0.00, 0.00, 1517.76, 'registrada', NULL, '2026-04-22 09:35:42', '2026-04-22 09:35:42'),
	(16, 'C-DUMMY-30044', 16, 1, '2026-04-23', 'factura', NULL, 0.00, 0.00, 1390.08, 'registrada', NULL, '2026-04-23 06:14:40', '2026-04-23 06:14:40'),
	(17, 'C-DUMMY-25655', 21, 1, '2026-06-08', 'factura', NULL, 0.00, 0.00, 1343.58, 'registrada', NULL, '2026-06-09 02:08:46', '2026-06-09 02:08:46'),
	(18, 'C-DUMMY-17794', 22, 1, '2026-06-19', 'factura', NULL, 0.00, 0.00, 1119.75, 'registrada', NULL, '2026-06-19 14:57:45', '2026-06-19 14:57:45'),
	(19, 'C-DUMMY-93152', 22, 1, '2026-01-30', 'factura', NULL, 0.00, 0.00, 1739.36, 'registrada', NULL, '2026-01-30 17:51:28', '2026-01-30 17:51:28'),
	(20, 'C-DUMMY-73644', 23, 1, '2026-02-20', 'factura', NULL, 0.00, 0.00, 1618.81, 'registrada', NULL, '2026-02-20 06:03:19', '2026-02-20 06:03:19'),
	(21, 'C-DUMMY-56574', 22, 1, '2026-06-13', 'factura', NULL, 0.00, 0.00, 975.93, 'registrada', NULL, '2026-06-13 18:25:37', '2026-06-13 18:25:37'),
	(22, 'C-DUMMY-92393', 15, 1, '2026-02-19', 'factura', NULL, 0.00, 0.00, 1750.39, 'registrada', NULL, '2026-02-20 01:45:16', '2026-02-20 01:45:16');

-- Volcando estructura para tabla saas_taller_automotriz.compra_detalle
CREATE TABLE IF NOT EXISTS `compra_detalle` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `compra_id` bigint unsigned NOT NULL,
  `repuesto_id` bigint unsigned DEFAULT NULL,
  `descripcion` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `cantidad` int NOT NULL DEFAULT '1',
  `precio` decimal(12,2) NOT NULL DEFAULT '0.00',
  `subtotal` decimal(12,2) NOT NULL DEFAULT '0.00',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `compra_detalle_compra_id_foreign` (`compra_id`),
  KEY `compra_detalle_repuesto_id_foreign` (`repuesto_id`),
  CONSTRAINT `compra_detalle_compra_id_foreign` FOREIGN KEY (`compra_id`) REFERENCES `compras` (`id`) ON DELETE CASCADE,
  CONSTRAINT `compra_detalle_repuesto_id_foreign` FOREIGN KEY (`repuesto_id`) REFERENCES `repuestos` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=37 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla saas_taller_automotriz.compra_detalle: ~36 rows (aproximadamente)
DELETE FROM `compra_detalle`;
INSERT INTO `compra_detalle` (`id`, `compra_id`, `repuesto_id`, `descripcion`, `cantidad`, `precio`, `subtotal`, `created_at`, `updated_at`) VALUES
	(1, 1, 4, 'Batería 12V 70Ah', 13, 180.00, 2340.00, '2026-07-04 10:29:18', '2026-07-04 10:29:18'),
	(2, 1, 5, 'Bujías (juego x4)', 17, 40.00, 680.00, '2026-07-04 10:29:18', '2026-07-04 10:29:18'),
	(3, 1, 6, 'Filtro de aire', 6, 18.00, 108.00, '2026-07-04 10:29:18', '2026-07-04 10:29:18'),
	(4, 2, 4, 'Batería 12V 70Ah', 17, 180.00, 3060.00, '2026-07-04 10:29:18', '2026-07-04 10:29:18'),
	(5, 2, 5, 'Bujías (juego x4)', 18, 40.00, 720.00, '2026-07-04 10:29:18', '2026-07-04 10:29:18'),
	(6, 2, 7, 'Correa de distribución', 13, 85.00, 1105.00, '2026-07-04 10:29:18', '2026-07-04 10:29:18'),
	(7, 3, 1, 'Filtro de aceite', 5, 15.00, 75.00, '2026-07-04 10:29:18', '2026-07-04 10:29:18'),
	(8, 3, 2, 'Aceite 15W-40 (1L)', 9, 22.00, 198.00, '2026-07-04 10:29:18', '2026-07-04 10:29:18'),
	(9, 3, 3, 'Pastillas de freno delanteras', 10, 55.00, 550.00, '2026-07-04 10:29:18', '2026-07-04 10:29:18'),
	(10, 4, 4, 'Batería 12V 70Ah', 20, 180.00, 3600.00, '2026-07-04 10:29:19', '2026-07-04 10:29:19'),
	(11, 4, 5, 'Bujías (juego x4)', 14, 40.00, 560.00, '2026-07-04 10:29:19', '2026-07-04 10:29:19'),
	(12, 4, 7, 'Correa de distribución', 15, 85.00, 1275.00, '2026-07-04 10:29:19', '2026-07-04 10:29:19'),
	(13, 5, 5, 'Bujías (juego x4)', 20, 40.00, 800.00, '2026-07-04 10:29:19', '2026-07-04 10:29:19'),
	(14, 5, 6, 'Filtro de aire', 8, 18.00, 144.00, '2026-07-04 10:29:19', '2026-07-04 10:29:19'),
	(15, 5, 7, 'Correa de distribución', 15, 85.00, 1275.00, '2026-07-04 10:29:19', '2026-07-04 10:29:19'),
	(16, 6, 3, 'Pastillas de freno delanteras', 10, 55.00, 550.00, '2026-07-04 10:29:19', '2026-07-04 10:29:19'),
	(17, 6, 5, 'Bujías (juego x4)', 10, 40.00, 400.00, '2026-07-04 10:29:19', '2026-07-04 10:29:19'),
	(18, 6, 7, 'Correa de distribución', 20, 85.00, 1700.00, '2026-07-04 10:29:19', '2026-07-04 10:29:19'),
	(19, 7, 3, 'Pastillas de freno delanteras', 15, 55.00, 825.00, '2026-07-04 10:29:19', '2026-07-04 10:29:19'),
	(20, 7, 4, 'Batería 12V 70Ah', 10, 180.00, 1800.00, '2026-07-04 10:29:19', '2026-07-04 10:29:19'),
	(21, 7, 6, 'Filtro de aire', 9, 18.00, 162.00, '2026-07-04 10:29:19', '2026-07-04 10:29:19'),
	(22, 8, 3, 'Pastillas de freno delanteras', 20, 55.00, 1100.00, '2026-07-04 10:29:19', '2026-07-04 10:29:19'),
	(23, 8, 4, 'Batería 12V 70Ah', 18, 180.00, 3240.00, '2026-07-04 10:29:19', '2026-07-04 10:29:19'),
	(24, 8, 5, 'Bujías (juego x4)', 10, 40.00, 400.00, '2026-07-04 10:29:19', '2026-07-04 10:29:19'),
	(25, 9, 1, 'Filtro de aceite', 13, 15.00, 195.00, '2026-07-04 10:29:19', '2026-07-04 10:29:19'),
	(26, 9, 4, 'Batería 12V 70Ah', 15, 180.00, 2700.00, '2026-07-04 10:29:19', '2026-07-04 10:29:19'),
	(27, 9, 7, 'Correa de distribución', 15, 85.00, 1275.00, '2026-07-04 10:29:19', '2026-07-04 10:29:19'),
	(28, 10, 1, 'Filtro de aceite', 13, 15.00, 195.00, '2026-07-04 10:29:19', '2026-07-04 10:29:19'),
	(29, 10, 5, 'Bujías (juego x4)', 8, 40.00, 320.00, '2026-07-04 10:29:19', '2026-07-04 10:29:19'),
	(30, 10, 7, 'Correa de distribución', 10, 85.00, 850.00, '2026-07-04 10:29:19', '2026-07-04 10:29:19'),
	(31, 11, 2, 'Aceite 15W-40 (1L)', 7, 22.00, 154.00, '2026-07-04 10:29:19', '2026-07-04 10:29:19'),
	(32, 11, 3, 'Pastillas de freno delanteras', 13, 55.00, 715.00, '2026-07-04 10:29:19', '2026-07-04 10:29:19'),
	(33, 11, 4, 'Batería 12V 70Ah', 6, 180.00, 1080.00, '2026-07-04 10:29:19', '2026-07-04 10:29:19'),
	(34, 12, 1, 'Filtro de aceite', 18, 15.00, 270.00, '2026-07-04 10:29:19', '2026-07-04 10:29:19'),
	(35, 12, 2, 'Aceite 15W-40 (1L)', 15, 22.00, 330.00, '2026-07-04 10:29:19', '2026-07-04 10:29:19'),
	(36, 12, 6, 'Filtro de aire', 10, 18.00, 180.00, '2026-07-04 10:29:19', '2026-07-04 10:29:19');

-- Volcando estructura para tabla saas_taller_automotriz.comprobantes
CREATE TABLE IF NOT EXISTS `comprobantes` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tipo` enum('boleta','factura') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'boleta',
  `serie` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'B001',
  `numero` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `orden_id` bigint unsigned DEFAULT NULL,
  `cliente_id` bigint unsigned DEFAULT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `fecha` date NOT NULL,
  `subtotal` decimal(12,2) NOT NULL DEFAULT '0.00',
  `igv` decimal(12,2) NOT NULL DEFAULT '0.00',
  `total` decimal(12,2) NOT NULL DEFAULT '0.00',
  `metodo_pago` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `estado` enum('emitido','anulado') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'emitido',
  `estado_sunat` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pendiente',
  `hash_cpe` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sunat_ticket` varchar(60) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `xml_path` varchar(250) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cdr_path` varchar(250) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sunat_observaciones` text COLLATE utf8mb4_unicode_ci,
  `enviado_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `comprobantes_orden_id_foreign` (`orden_id`),
  KEY `comprobantes_cliente_id_foreign` (`cliente_id`),
  KEY `comprobantes_user_id_foreign` (`user_id`),
  CONSTRAINT `comprobantes_cliente_id_foreign` FOREIGN KEY (`cliente_id`) REFERENCES `clientes` (`id`) ON DELETE SET NULL,
  CONSTRAINT `comprobantes_orden_id_foreign` FOREIGN KEY (`orden_id`) REFERENCES `ordenes` (`id`) ON DELETE SET NULL,
  CONSTRAINT `comprobantes_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla saas_taller_automotriz.comprobantes: ~13 rows (aproximadamente)
DELETE FROM `comprobantes`;
INSERT INTO `comprobantes` (`id`, `tipo`, `serie`, `numero`, `orden_id`, `cliente_id`, `user_id`, `fecha`, `subtotal`, `igv`, `total`, `metodo_pago`, `estado`, `estado_sunat`, `hash_cpe`, `sunat_ticket`, `xml_path`, `cdr_path`, `sunat_observaciones`, `enviado_at`, `created_at`, `updated_at`) VALUES
	(1, 'boleta', 'B001', '1', 1, 1, 1, '2026-07-05', 900.00, 162.00, 1062.00, 'efectivo', 'emitido', 'pendiente', NULL, NULL, NULL, NULL, NULL, NULL, '2026-07-04 10:29:18', '2026-07-04 10:29:18'),
	(2, 'factura', 'F001', '1', 2, 6, 1, '2026-06-29', 540.00, 97.20, 637.20, 'efectivo', 'emitido', 'pendiente', NULL, NULL, NULL, NULL, NULL, NULL, '2026-07-04 10:29:18', '2026-07-04 10:29:18'),
	(3, 'boleta', 'B001', '2', 3, 5, 1, '2026-06-30', 600.00, 108.00, 708.00, 'yape', 'emitido', 'pendiente', NULL, NULL, NULL, NULL, NULL, NULL, '2026-07-04 10:29:18', '2026-07-04 10:29:18'),
	(4, 'factura', 'F001', '2', 5, 4, 1, '2026-06-20', 586.00, 105.48, 691.48, 'tarjeta', 'emitido', 'pendiente', NULL, NULL, NULL, NULL, NULL, NULL, '2026-07-04 10:29:18', '2026-07-04 10:29:18'),
	(5, 'boleta', 'B001', '3', 7, 4, 1, '2026-06-21', 435.00, 78.30, 513.30, 'efectivo', 'emitido', 'pendiente', NULL, NULL, NULL, NULL, NULL, NULL, '2026-07-04 10:29:18', '2026-07-04 10:29:18'),
	(6, 'factura', 'F001', '3', 9, 3, 1, '2026-06-09', 112.00, 20.16, 132.16, 'yape', 'emitido', 'pendiente', NULL, NULL, NULL, NULL, NULL, NULL, '2026-07-04 10:29:18', '2026-07-04 10:29:18'),
	(7, 'factura', 'F001', '4', 10, 5, 1, '2026-06-16', 250.00, 45.00, 295.00, 'yape', 'emitido', 'pendiente', NULL, NULL, NULL, NULL, NULL, NULL, '2026-07-04 10:29:18', '2026-07-04 10:29:18'),
	(8, 'factura', 'F001', '5', 11, 3, 1, '2026-06-29', 804.00, 144.72, 948.72, 'yape', 'emitido', 'pendiente', NULL, NULL, NULL, NULL, NULL, NULL, '2026-07-04 10:29:18', '2026-07-04 10:29:18'),
	(9, 'boleta', 'B001', '4', 13, 1, 1, '2026-06-22', 370.00, 66.60, 436.60, 'transferencia', 'emitido', 'pendiente', NULL, NULL, NULL, NULL, NULL, NULL, '2026-07-04 10:29:18', '2026-07-04 10:29:18'),
	(10, 'factura', 'F001', '6', 14, 1, 1, '2026-06-09', 180.00, 32.40, 212.40, 'efectivo', 'emitido', 'pendiente', NULL, NULL, NULL, NULL, NULL, NULL, '2026-07-04 10:29:18', '2026-07-04 10:29:18'),
	(11, 'factura', 'F001', '7', 15, 6, 1, '2026-06-12', 455.00, 81.90, 536.90, 'tarjeta', 'emitido', 'pendiente', NULL, NULL, NULL, NULL, NULL, NULL, '2026-07-04 10:29:18', '2026-07-04 10:29:18'),
	(12, 'boleta', 'B001', '5', 16, 4, 1, '2026-06-25', 380.00, 68.40, 448.40, 'yape', 'emitido', 'pendiente', NULL, NULL, NULL, NULL, NULL, NULL, '2026-07-04 10:29:18', '2026-07-04 10:29:18'),
	(13, 'boleta', 'B001', '6', 17, 4, 1, '2026-06-25', 280.00, 50.40, 330.40, 'efectivo', 'emitido', 'pendiente', NULL, NULL, NULL, NULL, NULL, NULL, '2026-07-04 10:29:18', '2026-07-04 10:29:18');

-- Volcando estructura para tabla saas_taller_automotriz.configuraciones
CREATE TABLE IF NOT EXISTS `configuraciones` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `empresa` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'AutoTaller Pro',
  `ruc` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `direccion` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `telefono` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `moneda` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'S/',
  `igv` decimal(5,2) NOT NULL DEFAULT '18.00',
  `serie_boleta` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'B001',
  `serie_factura` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'F001',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla saas_taller_automotriz.configuraciones: ~0 rows (aproximadamente)
DELETE FROM `configuraciones`;
INSERT INTO `configuraciones` (`id`, `empresa`, `ruc`, `direccion`, `telefono`, `email`, `moneda`, `igv`, `serie_boleta`, `serie_factura`, `created_at`, `updated_at`) VALUES
	(1, 'AutoTaller Pro', '20601234567', 'Av. Principal 123, Lima', '01 456 7890', 'contacto@autotallerpro.com', 'S/', 18.00, 'B001', 'F001', '2026-07-04 03:55:54', '2026-07-04 03:55:54');

-- Volcando estructura para tabla saas_taller_automotriz.facturacion_electronica
CREATE TABLE IF NOT EXISTS `facturacion_electronica` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `habilitado` tinyint(1) NOT NULL DEFAULT '0',
  `emitir_automatico` tinyint(1) NOT NULL DEFAULT '1',
  `driver` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'none',
  `modo` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'beta',
  `ruc` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `razon_social` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nombre_comercial` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `direccion_fiscal` varchar(250) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ubigeo` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `departamento` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `provincia` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `distrito` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `usuario_sol` varchar(60) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `clave_sol` text COLLATE utf8mb4_unicode_ci,
  `certificado_path` varchar(250) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `certificado_password` text COLLATE utf8mb4_unicode_ci,
  `client_id` varchar(120) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `client_secret` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla saas_taller_automotriz.facturacion_electronica: ~0 rows (aproximadamente)
DELETE FROM `facturacion_electronica`;
INSERT INTO `facturacion_electronica` (`id`, `habilitado`, `emitir_automatico`, `driver`, `modo`, `ruc`, `razon_social`, `nombre_comercial`, `direccion_fiscal`, `ubigeo`, `departamento`, `provincia`, `distrito`, `usuario_sol`, `clave_sol`, `certificado_path`, `certificado_password`, `client_id`, `client_secret`, `created_at`, `updated_at`) VALUES
	(1, 0, 1, 'none', 'beta', '20000000001', 'EMPRESA DEMO S.A.C.', 'AutoTaller Pro', 'Av. Principal 123', '150101', 'LIMA', 'LIMA', 'LIMA', 'MODDATOS', NULL, 'C:\\SAAS\\saas_taller-automotriz\\storage\\facturacion/pe/certificate.pem', NULL, NULL, NULL, '2026-08-07 02:10:19', '2026-08-07 02:10:19');

-- Volcando estructura para tabla saas_taller_automotriz.migrations
CREATE TABLE IF NOT EXISTS `migrations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla saas_taller_automotriz.migrations: ~15 rows (aproximadamente)
DELETE FROM `migrations`;
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
	(1, '0001_01_01_000000_create_users_table', 1),
	(2, '2026_01_01_000001_create_clientes_table', 1),
	(3, '2026_01_01_000002_create_vehiculos_table', 1),
	(4, '2026_01_01_000003_create_proveedores_table', 1),
	(5, '2026_01_01_000004_create_servicios_table', 1),
	(6, '2026_01_01_000005_create_repuestos_table', 1),
	(7, '2026_01_01_000006_create_ordenes_table', 1),
	(8, '2026_01_01_000007_create_orden_servicio_table', 1),
	(9, '2026_01_01_000008_create_orden_repuesto_table', 1),
	(10, '2026_01_01_000009_create_citas_table', 1),
	(11, '2026_01_02_000001_create_compras_table', 2),
	(12, '2026_01_02_000002_create_comprobantes_table', 2),
	(13, '2026_01_02_000003_create_movimientos_caja_table', 2),
	(14, '2026_01_02_000004_create_configuraciones_table', 2),
	(15, '2026_01_03_000001_create_planes_table', 3),
	(16, '2026_01_03_000002_create_talleres_table', 3),
	(17, '2026_01_03_000003_create_pagos_suscripcion_table', 3),
	(18, '2026_08_06_000001_create_facturacion_electronica_table', 4),
	(19, '2026_08_06_000002_add_sunat_fields_to_comprobantes_table', 4);

-- Volcando estructura para tabla saas_taller_automotriz.movimientos_caja
CREATE TABLE IF NOT EXISTS `movimientos_caja` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tipo` enum('ingreso','egreso') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'ingreso',
  `concepto` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `monto` decimal(12,2) NOT NULL DEFAULT '0.00',
  `fecha` date NOT NULL,
  `metodo_pago` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'efectivo',
  `orden_id` bigint unsigned DEFAULT NULL,
  `comprobante_id` bigint unsigned DEFAULT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `movimientos_caja_orden_id_foreign` (`orden_id`),
  KEY `movimientos_caja_comprobante_id_foreign` (`comprobante_id`),
  KEY `movimientos_caja_user_id_foreign` (`user_id`),
  CONSTRAINT `movimientos_caja_comprobante_id_foreign` FOREIGN KEY (`comprobante_id`) REFERENCES `comprobantes` (`id`) ON DELETE SET NULL,
  CONSTRAINT `movimientos_caja_orden_id_foreign` FOREIGN KEY (`orden_id`) REFERENCES `ordenes` (`id`) ON DELETE SET NULL,
  CONSTRAINT `movimientos_caja_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=31 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla saas_taller_automotriz.movimientos_caja: ~30 rows (aproximadamente)
DELETE FROM `movimientos_caja`;
INSERT INTO `movimientos_caja` (`id`, `tipo`, `concepto`, `monto`, `fecha`, `metodo_pago`, `orden_id`, `comprobante_id`, `user_id`, `created_at`, `updated_at`) VALUES
	(1, 'ingreso', 'Venta B001-000001 · Orden OT-0001', 1062.00, '2026-07-05', 'efectivo', 1, 1, 1, '2026-07-04 10:29:18', '2026-07-04 10:29:18'),
	(2, 'ingreso', 'Venta F001-000001 · Orden OT-0002', 637.20, '2026-06-29', 'efectivo', 2, 2, 1, '2026-07-04 10:29:18', '2026-07-04 10:29:18'),
	(3, 'ingreso', 'Venta B001-000002 · Orden OT-0003', 708.00, '2026-06-30', 'yape', 3, 3, 1, '2026-07-04 10:29:18', '2026-07-04 10:29:18'),
	(4, 'ingreso', 'Venta F001-000002 · Orden OT-0005', 691.48, '2026-06-20', 'tarjeta', 5, 4, 1, '2026-07-04 10:29:18', '2026-07-04 10:29:18'),
	(5, 'ingreso', 'Venta B001-000003 · Orden OT-0007', 513.30, '2026-06-21', 'efectivo', 7, 5, 1, '2026-07-04 10:29:18', '2026-07-04 10:29:18'),
	(6, 'ingreso', 'Venta F001-000003 · Orden OT-0009', 132.16, '2026-06-09', 'yape', 9, 6, 1, '2026-07-04 10:29:18', '2026-07-04 10:29:18'),
	(7, 'ingreso', 'Venta F001-000004 · Orden OT-0010', 295.00, '2026-06-16', 'yape', 10, 7, 1, '2026-07-04 10:29:18', '2026-07-04 10:29:18'),
	(8, 'ingreso', 'Venta F001-000005 · Orden OT-0011', 948.72, '2026-06-29', 'yape', 11, 8, 1, '2026-07-04 10:29:18', '2026-07-04 10:29:18'),
	(9, 'ingreso', 'Venta B001-000004 · Orden OT-0013', 436.60, '2026-06-22', 'transferencia', 13, 9, 1, '2026-07-04 10:29:18', '2026-07-04 10:29:18'),
	(10, 'ingreso', 'Venta F001-000006 · Orden OT-0014', 212.40, '2026-06-09', 'efectivo', 14, 10, 1, '2026-07-04 10:29:18', '2026-07-04 10:29:18'),
	(11, 'ingreso', 'Venta F001-000007 · Orden OT-0015', 536.90, '2026-06-12', 'tarjeta', 15, 11, 1, '2026-07-04 10:29:18', '2026-07-04 10:29:18'),
	(12, 'ingreso', 'Venta B001-000005 · Orden OT-0016', 448.40, '2026-06-25', 'yape', 16, 12, 1, '2026-07-04 10:29:18', '2026-07-04 10:29:18'),
	(13, 'ingreso', 'Venta B001-000006 · Orden OT-0017', 330.40, '2026-06-25', 'efectivo', 17, 13, 1, '2026-07-04 10:29:18', '2026-07-04 10:29:18'),
	(14, 'egreso', 'Compra CMP-0001 · AutoPartes del Perú', 3691.04, '2026-02-15', 'transferencia', NULL, NULL, 1, '2026-07-04 10:29:18', '2026-07-04 10:29:18'),
	(15, 'egreso', 'Compra CMP-0002 · Lubricantes Premium', 5764.30, '2026-04-29', 'transferencia', NULL, NULL, 1, '2026-07-04 10:29:18', '2026-07-04 10:29:18'),
	(16, 'egreso', 'Compra CMP-0003 · Repuestos San Martín', 971.14, '2026-05-13', 'transferencia', NULL, NULL, 1, '2026-07-04 10:29:19', '2026-07-04 10:29:19'),
	(17, 'egreso', 'Compra CMP-0004 · AutoPartes del Perú', 6413.30, '2026-01-23', 'transferencia', NULL, NULL, 1, '2026-07-04 10:29:19', '2026-07-04 10:29:19'),
	(18, 'egreso', 'Compra CMP-0005 · AutoPartes del Perú', 2618.42, '2026-04-29', 'transferencia', NULL, NULL, 1, '2026-07-04 10:29:19', '2026-07-04 10:29:19'),
	(19, 'egreso', 'Compra CMP-0006 · AutoPartes del Perú', 3127.00, '2026-06-17', 'transferencia', NULL, NULL, 1, '2026-07-04 10:29:19', '2026-07-04 10:29:19'),
	(20, 'egreso', 'Compra CMP-0007 · AutoPartes del Perú', 3288.66, '2026-02-25', 'transferencia', NULL, NULL, 1, '2026-07-04 10:29:19', '2026-07-04 10:29:19'),
	(21, 'egreso', 'Compra CMP-0008 · Repuestos San Martín', 5593.20, '2026-03-31', 'transferencia', NULL, NULL, 1, '2026-07-04 10:29:19', '2026-07-04 10:29:19'),
	(22, 'egreso', 'Compra CMP-0009 · Lubricantes Premium', 4920.60, '2026-04-23', 'transferencia', NULL, NULL, 1, '2026-07-04 10:29:19', '2026-07-04 10:29:19'),
	(23, 'egreso', 'Compra CMP-0010 · Repuestos San Martín', 1610.70, '2026-06-03', 'transferencia', NULL, NULL, 1, '2026-07-04 10:29:19', '2026-07-04 10:29:19'),
	(24, 'egreso', 'Compra CMP-0011 · AutoPartes del Perú', 2299.82, '2026-03-12', 'transferencia', NULL, NULL, 1, '2026-07-04 10:29:19', '2026-07-04 10:29:19'),
	(25, 'egreso', 'Compra CMP-0012 · Lubricantes Premium', 920.40, '2026-05-13', 'transferencia', NULL, NULL, 1, '2026-07-04 10:29:19', '2026-07-04 10:29:19'),
	(26, 'egreso', 'Pago de alquiler del local', 1500.00, '2026-05-27', 'efectivo', NULL, NULL, 1, '2026-07-04 10:29:19', '2026-07-04 10:29:19'),
	(27, 'egreso', 'Servicios (luz, agua, internet)', 480.00, '2026-06-05', 'efectivo', NULL, NULL, 1, '2026-07-04 10:29:19', '2026-07-04 10:29:19'),
	(28, 'egreso', 'Compra de insumos de limpieza', 120.00, '2026-06-27', 'efectivo', NULL, NULL, 1, '2026-07-04 10:29:19', '2026-07-04 10:29:19'),
	(29, 'egreso', 'Pago de planilla', 4200.00, '2026-06-21', 'efectivo', NULL, NULL, 1, '2026-07-04 10:29:19', '2026-07-04 10:29:19'),
	(30, 'egreso', 'Mantenimiento de herramientas', 260.00, '2026-06-22', 'efectivo', NULL, NULL, 1, '2026-07-04 10:29:19', '2026-07-04 10:29:19');

-- Volcando estructura para tabla saas_taller_automotriz.ordenes
CREATE TABLE IF NOT EXISTS `ordenes` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `numero` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `cliente_id` bigint unsigned NOT NULL,
  `vehiculo_id` bigint unsigned NOT NULL,
  `mecanico_id` bigint unsigned DEFAULT NULL,
  `fecha_ingreso` date NOT NULL,
  `fecha_entrega` date DEFAULT NULL,
  `kilometraje` int DEFAULT NULL,
  `diagnostico` text COLLATE utf8mb4_unicode_ci,
  `observaciones` text COLLATE utf8mb4_unicode_ci,
  `estado` enum('recepcion','diagnostico','en_proceso','esperando_repuestos','terminado','entregado','anulado') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'recepcion',
  `prioridad` enum('baja','media','alta') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'media',
  `subtotal` decimal(10,2) NOT NULL DEFAULT '0.00',
  `descuento` decimal(10,2) NOT NULL DEFAULT '0.00',
  `impuesto` decimal(10,2) NOT NULL DEFAULT '0.00',
  `total` decimal(10,2) NOT NULL DEFAULT '0.00',
  `estado_pago` enum('pendiente','parcial','pagado') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pendiente',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `ordenes_numero_unique` (`numero`),
  KEY `ordenes_cliente_id_foreign` (`cliente_id`),
  KEY `ordenes_vehiculo_id_foreign` (`vehiculo_id`),
  KEY `ordenes_mecanico_id_foreign` (`mecanico_id`),
  CONSTRAINT `ordenes_cliente_id_foreign` FOREIGN KEY (`cliente_id`) REFERENCES `clientes` (`id`) ON DELETE CASCADE,
  CONSTRAINT `ordenes_mecanico_id_foreign` FOREIGN KEY (`mecanico_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `ordenes_vehiculo_id_foreign` FOREIGN KEY (`vehiculo_id`) REFERENCES `vehiculos` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=29 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla saas_taller_automotriz.ordenes: ~28 rows (aproximadamente)
DELETE FROM `ordenes`;
INSERT INTO `ordenes` (`id`, `numero`, `cliente_id`, `vehiculo_id`, `mecanico_id`, `fecha_ingreso`, `fecha_entrega`, `kilometraje`, `diagnostico`, `observaciones`, `estado`, `prioridad`, `subtotal`, `descuento`, `impuesto`, `total`, `estado_pago`, `created_at`, `updated_at`) VALUES
	(1, 'OT-0001', 1, 1, 2, '2026-07-02', '2026-07-05', 59128, 'Revisión general y mantenimiento preventivo.', NULL, 'terminado', 'alta', 900.00, 0.00, 162.00, 1062.00, 'pagado', '2026-07-04 02:52:43', '2026-07-04 10:29:18'),
	(2, 'OT-0002', 6, 6, 2, '2026-06-29', NULL, 106070, 'Revisión general y mantenimiento preventivo.', NULL, 'esperando_repuestos', 'baja', 540.00, 0.00, 97.20, 637.20, 'pagado', '2026-07-04 02:52:43', '2026-07-04 10:29:18'),
	(3, 'OT-0003', 5, 5, 4, '2026-06-30', NULL, 115235, 'Revisión general y mantenimiento preventivo.', NULL, 'esperando_repuestos', 'alta', 600.00, 0.00, 108.00, 708.00, 'pagado', '2026-07-04 02:52:43', '2026-07-04 10:29:18'),
	(4, 'OT-0004', 4, 4, 5, '2026-06-15', '2026-06-16', 53878, 'Revisión general y mantenimiento preventivo.', NULL, 'entregado', 'baja', 420.00, 0.00, 75.60, 495.60, 'pagado', '2026-07-04 02:52:43', '2026-07-04 02:52:43'),
	(5, 'OT-0005', 4, 4, 5, '2026-06-18', '2026-06-21', 81879, 'Revisión general y mantenimiento preventivo.', NULL, 'terminado', 'alta', 586.00, 0.00, 105.48, 691.48, 'pagado', '2026-07-04 02:52:43', '2026-07-04 10:29:18'),
	(6, 'OT-0006', 1, 1, 4, '2026-06-17', NULL, 37268, 'Revisión general y mantenimiento preventivo.', NULL, 'recepcion', 'media', 668.00, 0.00, 120.24, 788.24, 'pendiente', '2026-07-04 02:52:43', '2026-07-04 02:52:43'),
	(7, 'OT-0007', 4, 4, 2, '2026-06-18', NULL, 107530, 'Revisión general y mantenimiento preventivo.', NULL, 'en_proceso', 'media', 435.00, 0.00, 78.30, 513.30, 'pagado', '2026-07-04 02:52:43', '2026-07-04 10:29:18'),
	(8, 'OT-0008', 1, 1, 2, '2026-06-10', '2026-06-14', 62087, 'Revisión general y mantenimiento preventivo.', NULL, 'terminado', 'media', 816.00, 0.00, 146.88, 962.88, 'pendiente', '2026-07-04 02:52:43', '2026-07-04 02:52:43'),
	(9, 'OT-0009', 3, 3, 3, '2026-06-09', NULL, 119896, 'Revisión general y mantenimiento preventivo.', NULL, 'recepcion', 'media', 112.00, 0.00, 20.16, 132.16, 'pagado', '2026-07-04 02:52:43', '2026-07-04 10:29:18'),
	(10, 'OT-0010', 5, 5, 3, '2026-06-16', '2026-06-19', 91603, 'Revisión general y mantenimiento preventivo.', NULL, 'entregado', 'alta', 250.00, 0.00, 45.00, 295.00, 'pagado', '2026-07-04 02:52:43', '2026-07-04 02:52:43'),
	(11, 'OT-0011', 3, 3, 3, '2026-06-29', NULL, 146340, 'Revisión general y mantenimiento preventivo.', NULL, 'esperando_repuestos', 'media', 804.00, 0.00, 144.72, 948.72, 'pagado', '2026-07-04 02:52:43', '2026-07-04 10:29:18'),
	(12, 'OT-0012', 6, 6, 2, '2026-06-23', NULL, 41895, 'Revisión general y mantenimiento preventivo.', NULL, 'esperando_repuestos', 'baja', 258.00, 0.00, 46.44, 304.44, 'pendiente', '2026-07-04 02:52:43', '2026-07-04 02:52:43'),
	(13, 'OT-0013', 1, 1, 2, '2026-06-21', NULL, 117367, 'Revisión general y mantenimiento preventivo.', NULL, 'recepcion', 'media', 370.00, 0.00, 66.60, 436.60, 'pagado', '2026-07-04 02:52:43', '2026-07-04 10:29:18'),
	(14, 'OT-0014', 1, 1, 5, '2026-06-08', NULL, 109214, 'Revisión general y mantenimiento preventivo.', NULL, 'en_proceso', 'baja', 180.00, 0.00, 32.40, 212.40, 'pagado', '2026-07-04 02:52:43', '2026-07-04 10:29:18'),
	(15, 'OT-0015', 6, 6, 2, '2026-06-10', NULL, 35405, 'Revisión general y mantenimiento preventivo.', NULL, 'esperando_repuestos', 'media', 455.00, 0.00, 81.90, 536.90, 'pagado', '2026-07-04 02:52:43', '2026-07-04 10:29:18'),
	(16, 'OT-0016', 4, 4, 2, '2026-06-25', NULL, 102991, 'Revisión general y mantenimiento preventivo.', NULL, 'en_proceso', 'media', 380.00, 0.00, 68.40, 448.40, 'pagado', '2026-07-04 02:52:43', '2026-07-04 10:29:18'),
	(17, 'OT-0017', 4, 4, 5, '2026-06-23', NULL, 134534, 'Revisión general y mantenimiento preventivo.', NULL, 'en_proceso', 'alta', 280.00, 0.00, 50.40, 330.40, 'pagado', '2026-07-04 02:52:43', '2026-07-04 10:29:18'),
	(18, 'OT-0018', 3, 3, 3, '2026-06-08', NULL, 113368, 'Revisión general y mantenimiento preventivo.', NULL, 'en_proceso', 'baja', 280.00, 0.00, 50.40, 330.40, 'pendiente', '2026-07-04 02:52:43', '2026-07-04 02:52:43'),
	(19, 'OT-DUMMY-18241', 28, 31, 1, '2026-06-07', NULL, NULL, NULL, NULL, 'en_proceso', 'media', 0.00, 0.00, 0.00, 693.23, 'pendiente', '2026-06-07 05:11:22', '2026-06-07 05:11:22'),
	(20, 'OT-DUMMY-22084', 35, 30, 5, '2026-03-19', NULL, NULL, NULL, NULL, 'en_proceso', 'media', 0.00, 0.00, 0.00, 468.82, 'pendiente', '2026-03-19 18:03:23', '2026-03-19 18:03:23'),
	(21, 'OT-DUMMY-59540', 30, 35, 4, '2026-02-17', NULL, NULL, NULL, NULL, 'en_proceso', 'media', 0.00, 0.00, 0.00, 938.29, 'pendiente', '2026-02-18 03:29:36', '2026-02-18 03:29:36'),
	(22, 'OT-DUMMY-78643', 28, 34, 6, '2026-01-27', NULL, NULL, NULL, NULL, 'esperando_repuestos', 'media', 0.00, 0.00, 0.00, 773.50, 'pendiente', '2026-01-28 02:00:51', '2026-01-28 02:00:51'),
	(23, 'OT-DUMMY-12946', 33, 33, 4, '2026-06-06', NULL, NULL, NULL, NULL, 'esperando_repuestos', 'media', 0.00, 0.00, 0.00, 1418.38, 'pendiente', '2026-06-06 16:07:50', '2026-06-06 16:07:50'),
	(24, 'OT-DUMMY-75933', 36, 32, 1, '2026-05-01', NULL, NULL, NULL, NULL, 'terminado', 'media', 0.00, 0.00, 0.00, 1054.15, 'pagado', '2026-05-02 00:36:20', '2026-05-02 00:36:20'),
	(25, 'OT-DUMMY-86039', 36, 33, 1, '2026-04-29', NULL, NULL, NULL, NULL, 'esperando_repuestos', 'media', 0.00, 0.00, 0.00, 313.21, 'pendiente', '2026-04-29 17:26:51', '2026-04-29 17:26:51'),
	(26, 'OT-DUMMY-57906', 27, 27, 5, '2026-05-26', NULL, NULL, NULL, NULL, 'esperando_repuestos', 'media', 0.00, 0.00, 0.00, 232.49, 'pendiente', '2026-05-26 16:09:44', '2026-05-26 16:09:44'),
	(27, 'OT-DUMMY-50120', 29, 29, 1, '2026-03-25', '2026-03-25', NULL, NULL, NULL, 'entregado', 'media', 0.00, 0.00, 0.00, 142.87, 'pagado', '2026-03-25 13:29:15', '2026-03-25 13:29:15'),
	(28, 'OT-DUMMY-24678', 28, 28, 1, '2026-04-18', NULL, NULL, NULL, NULL, 'esperando_repuestos', 'media', 0.00, 0.00, 0.00, 877.82, 'pendiente', '2026-04-18 18:25:06', '2026-04-18 18:25:06');

-- Volcando estructura para tabla saas_taller_automotriz.orden_repuesto
CREATE TABLE IF NOT EXISTS `orden_repuesto` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `orden_id` bigint unsigned NOT NULL,
  `repuesto_id` bigint unsigned DEFAULT NULL,
  `descripcion` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `cantidad` int NOT NULL DEFAULT '1',
  `precio` decimal(10,2) NOT NULL DEFAULT '0.00',
  `subtotal` decimal(10,2) NOT NULL DEFAULT '0.00',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `orden_repuesto_orden_id_foreign` (`orden_id`),
  KEY `orden_repuesto_repuesto_id_foreign` (`repuesto_id`),
  CONSTRAINT `orden_repuesto_orden_id_foreign` FOREIGN KEY (`orden_id`) REFERENCES `ordenes` (`id`) ON DELETE CASCADE,
  CONSTRAINT `orden_repuesto_repuesto_id_foreign` FOREIGN KEY (`repuesto_id`) REFERENCES `repuestos` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla saas_taller_automotriz.orden_repuesto: ~18 rows (aproximadamente)
DELETE FROM `orden_repuesto`;
INSERT INTO `orden_repuesto` (`id`, `orden_id`, `repuesto_id`, `descripcion`, `cantidad`, `precio`, `subtotal`, `created_at`, `updated_at`) VALUES
	(1, 1, 3, 'Pastillas de freno delanteras', 2, 95.00, 190.00, '2026-07-04 02:52:43', '2026-07-04 02:52:43'),
	(2, 1, 7, 'Correa de distribución', 2, 140.00, 280.00, '2026-07-04 02:52:43', '2026-07-04 02:52:43'),
	(3, 3, 5, 'Bujías (juego x4)', 2, 75.00, 150.00, '2026-07-04 02:52:43', '2026-07-04 02:52:43'),
	(4, 4, 1, 'Filtro de aceite', 1, 28.00, 28.00, '2026-07-04 02:52:43', '2026-07-04 02:52:43'),
	(5, 4, 6, 'Filtro de aire', 1, 32.00, 32.00, '2026-07-04 02:52:43', '2026-07-04 02:52:43'),
	(6, 5, 2, 'Aceite 15W-40 (1L)', 2, 38.00, 76.00, '2026-07-04 02:52:43', '2026-07-04 02:52:43'),
	(7, 6, 1, 'Filtro de aceite', 1, 28.00, 28.00, '2026-07-04 02:52:43', '2026-07-04 02:52:43'),
	(8, 6, 3, 'Pastillas de freno delanteras', 2, 95.00, 190.00, '2026-07-04 02:52:43', '2026-07-04 02:52:43'),
	(9, 7, 5, 'Bujías (juego x4)', 1, 75.00, 75.00, '2026-07-04 02:52:43', '2026-07-04 02:52:43'),
	(10, 8, 2, 'Aceite 15W-40 (1L)', 2, 38.00, 76.00, '2026-07-04 02:52:43', '2026-07-04 02:52:43'),
	(11, 8, 5, 'Bujías (juego x4)', 2, 75.00, 150.00, '2026-07-04 02:52:43', '2026-07-04 02:52:43'),
	(12, 9, 6, 'Filtro de aire', 1, 32.00, 32.00, '2026-07-04 02:52:43', '2026-07-04 02:52:43'),
	(13, 10, 3, 'Pastillas de freno delanteras', 2, 95.00, 190.00, '2026-07-04 02:52:43', '2026-07-04 02:52:43'),
	(14, 11, 6, 'Filtro de aire', 2, 32.00, 64.00, '2026-07-04 02:52:43', '2026-07-04 02:52:43'),
	(15, 11, 7, 'Correa de distribución', 1, 140.00, 140.00, '2026-07-04 02:52:43', '2026-07-04 02:52:43'),
	(16, 12, 1, 'Filtro de aceite', 1, 28.00, 28.00, '2026-07-04 02:52:43', '2026-07-04 02:52:43'),
	(17, 15, 3, 'Pastillas de freno delanteras', 1, 95.00, 95.00, '2026-07-04 02:52:43', '2026-07-04 02:52:43'),
	(18, 15, 7, 'Correa de distribución', 2, 140.00, 280.00, '2026-07-04 02:52:43', '2026-07-04 02:52:43');

-- Volcando estructura para tabla saas_taller_automotriz.orden_servicio
CREATE TABLE IF NOT EXISTS `orden_servicio` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `orden_id` bigint unsigned NOT NULL,
  `servicio_id` bigint unsigned DEFAULT NULL,
  `descripcion` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `cantidad` int NOT NULL DEFAULT '1',
  `precio` decimal(10,2) NOT NULL DEFAULT '0.00',
  `subtotal` decimal(10,2) NOT NULL DEFAULT '0.00',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `orden_servicio_orden_id_foreign` (`orden_id`),
  KEY `orden_servicio_servicio_id_foreign` (`servicio_id`),
  CONSTRAINT `orden_servicio_orden_id_foreign` FOREIGN KEY (`orden_id`) REFERENCES `ordenes` (`id`) ON DELETE CASCADE,
  CONSTRAINT `orden_servicio_servicio_id_foreign` FOREIGN KEY (`servicio_id`) REFERENCES `servicios` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=45 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla saas_taller_automotriz.orden_servicio: ~44 rows (aproximadamente)
DELETE FROM `orden_servicio`;
INSERT INTO `orden_servicio` (`id`, `orden_id`, `servicio_id`, `descripcion`, `cantidad`, `precio`, `subtotal`, `created_at`, `updated_at`) VALUES
	(1, 1, 3, 'Cambio de pastillas de freno', 1, 150.00, 150.00, '2026-07-04 02:52:43', '2026-07-04 02:52:43'),
	(2, 1, 4, 'Afinamiento de motor', 1, 280.00, 280.00, '2026-07-04 02:52:43', '2026-07-04 02:52:43'),
	(3, 2, 2, 'Alineamiento y balanceo', 1, 90.00, 90.00, '2026-07-04 02:52:43', '2026-07-04 02:52:43'),
	(4, 2, 8, 'Cambio de correa de distribución', 1, 450.00, 450.00, '2026-07-04 02:52:43', '2026-07-04 02:52:43'),
	(5, 3, 8, 'Cambio de correa de distribución', 1, 450.00, 450.00, '2026-07-04 02:52:43', '2026-07-04 02:52:43'),
	(6, 4, 4, 'Afinamiento de motor', 1, 280.00, 280.00, '2026-07-04 02:52:43', '2026-07-04 02:52:43'),
	(7, 4, 5, 'Diagnóstico por escáner', 1, 80.00, 80.00, '2026-07-04 02:52:43', '2026-07-04 02:52:43'),
	(8, 5, 3, 'Cambio de pastillas de freno', 1, 150.00, 150.00, '2026-07-04 02:52:43', '2026-07-04 02:52:43'),
	(9, 5, 4, 'Afinamiento de motor', 1, 280.00, 280.00, '2026-07-04 02:52:43', '2026-07-04 02:52:43'),
	(10, 5, 5, 'Diagnóstico por escáner', 1, 80.00, 80.00, '2026-07-04 02:52:43', '2026-07-04 02:52:43'),
	(11, 6, 8, 'Cambio de correa de distribución', 1, 450.00, 450.00, '2026-07-04 02:52:43', '2026-07-04 02:52:43'),
	(12, 7, 1, 'Cambio de aceite y filtro', 1, 120.00, 120.00, '2026-07-04 02:52:43', '2026-07-04 02:52:43'),
	(13, 7, 2, 'Alineamiento y balanceo', 1, 90.00, 90.00, '2026-07-04 02:52:43', '2026-07-04 02:52:43'),
	(14, 7, 3, 'Cambio de pastillas de freno', 1, 150.00, 150.00, '2026-07-04 02:52:43', '2026-07-04 02:52:43'),
	(15, 8, 5, 'Diagnóstico por escáner', 1, 80.00, 80.00, '2026-07-04 02:52:43', '2026-07-04 02:52:43'),
	(16, 8, 6, 'Cambio de batería', 1, 60.00, 60.00, '2026-07-04 02:52:43', '2026-07-04 02:52:43'),
	(17, 8, 8, 'Cambio de correa de distribución', 1, 450.00, 450.00, '2026-07-04 02:52:43', '2026-07-04 02:52:43'),
	(18, 9, 5, 'Diagnóstico por escáner', 1, 80.00, 80.00, '2026-07-04 02:52:43', '2026-07-04 02:52:43'),
	(19, 10, 6, 'Cambio de batería', 1, 60.00, 60.00, '2026-07-04 02:52:43', '2026-07-04 02:52:43'),
	(20, 11, 2, 'Alineamiento y balanceo', 1, 90.00, 90.00, '2026-07-04 02:52:43', '2026-07-04 02:52:43'),
	(21, 11, 6, 'Cambio de batería', 1, 60.00, 60.00, '2026-07-04 02:52:43', '2026-07-04 02:52:43'),
	(22, 11, 8, 'Cambio de correa de distribución', 1, 450.00, 450.00, '2026-07-04 02:52:43', '2026-07-04 02:52:43'),
	(23, 12, 2, 'Alineamiento y balanceo', 1, 90.00, 90.00, '2026-07-04 02:52:43', '2026-07-04 02:52:43'),
	(24, 12, 5, 'Diagnóstico por escáner', 1, 80.00, 80.00, '2026-07-04 02:52:43', '2026-07-04 02:52:43'),
	(25, 12, 6, 'Cambio de batería', 1, 60.00, 60.00, '2026-07-04 02:52:43', '2026-07-04 02:52:43'),
	(26, 13, 2, 'Alineamiento y balanceo', 1, 90.00, 90.00, '2026-07-04 02:52:43', '2026-07-04 02:52:43'),
	(27, 13, 4, 'Afinamiento de motor', 1, 280.00, 280.00, '2026-07-04 02:52:43', '2026-07-04 02:52:43'),
	(28, 14, 1, 'Cambio de aceite y filtro', 1, 120.00, 120.00, '2026-07-04 02:52:43', '2026-07-04 02:52:43'),
	(29, 14, 6, 'Cambio de batería', 1, 60.00, 60.00, '2026-07-04 02:52:43', '2026-07-04 02:52:43'),
	(30, 15, 5, 'Diagnóstico por escáner', 1, 80.00, 80.00, '2026-07-04 02:52:43', '2026-07-04 02:52:43'),
	(31, 16, 4, 'Afinamiento de motor', 1, 280.00, 280.00, '2026-07-04 02:52:43', '2026-07-04 02:52:43'),
	(32, 16, 7, 'Revisión de suspensión', 1, 100.00, 100.00, '2026-07-04 02:52:43', '2026-07-04 02:52:43'),
	(33, 17, 4, 'Afinamiento de motor', 1, 280.00, 280.00, '2026-07-04 02:52:43', '2026-07-04 02:52:43'),
	(34, 18, 4, 'Afinamiento de motor', 1, 280.00, 280.00, '2026-07-04 02:52:43', '2026-07-04 02:52:43'),
	(35, 19, 28, 'Servicio minus', 1, 693.23, 693.23, '2026-06-07 05:11:22', '2026-06-07 05:11:22'),
	(36, 20, 25, 'Servicio iste', 1, 468.82, 468.82, '2026-03-19 18:03:23', '2026-03-19 18:03:23'),
	(37, 21, 20, 'Servicio sapiente', 1, 938.29, 938.29, '2026-02-18 03:29:36', '2026-02-18 03:29:36'),
	(38, 22, 23, 'Servicio id', 1, 773.50, 773.50, '2026-01-28 02:00:51', '2026-01-28 02:00:51'),
	(39, 23, 21, 'Servicio delectus', 1, 1418.38, 1418.38, '2026-06-06 16:07:50', '2026-06-06 16:07:50'),
	(40, 24, 23, 'Servicio inventore', 1, 1054.15, 1054.15, '2026-05-02 00:36:20', '2026-05-02 00:36:20'),
	(41, 25, 23, 'Servicio voluptas', 1, 313.21, 313.21, '2026-04-29 17:26:51', '2026-04-29 17:26:51'),
	(42, 26, 24, 'Servicio et', 1, 232.49, 232.49, '2026-05-26 16:09:44', '2026-05-26 16:09:44'),
	(43, 27, 25, 'Servicio quod', 1, 142.87, 142.87, '2026-03-25 13:29:15', '2026-03-25 13:29:15'),
	(44, 28, 23, 'Servicio non', 1, 877.82, 877.82, '2026-04-18 18:25:06', '2026-04-18 18:25:06');

-- Volcando estructura para tabla saas_taller_automotriz.pagos_suscripcion
CREATE TABLE IF NOT EXISTS `pagos_suscripcion` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `taller_id` bigint unsigned NOT NULL,
  `plan_id` bigint unsigned DEFAULT NULL,
  `monto` decimal(10,2) NOT NULL DEFAULT '0.00',
  `fecha_pago` date NOT NULL,
  `periodo` enum('mensual','anual') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'mensual',
  `metodo` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'transferencia',
  `referencia` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cubre_hasta` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `pagos_suscripcion_taller_id_foreign` (`taller_id`),
  KEY `pagos_suscripcion_plan_id_foreign` (`plan_id`),
  CONSTRAINT `pagos_suscripcion_plan_id_foreign` FOREIGN KEY (`plan_id`) REFERENCES `planes` (`id`) ON DELETE SET NULL,
  CONSTRAINT `pagos_suscripcion_taller_id_foreign` FOREIGN KEY (`taller_id`) REFERENCES `talleres` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla saas_taller_automotriz.pagos_suscripcion: ~20 rows (aproximadamente)
DELETE FROM `pagos_suscripcion`;
INSERT INTO `pagos_suscripcion` (`id`, `taller_id`, `plan_id`, `monto`, `fecha_pago`, `periodo`, `metodo`, `referencia`, `cubre_hasta`, `created_at`, `updated_at`) VALUES
	(1, 1, 1, 990.00, '2026-06-04', 'anual', 'yape', 'PAG-64262', '2026-07-04', '2026-07-04 10:56:36', '2026-07-04 10:56:36'),
	(2, 1, 1, 990.00, '2026-05-04', 'anual', 'tarjeta', 'PAG-39725', '2026-06-04', '2026-07-04 10:56:36', '2026-07-04 10:56:36'),
	(3, 1, 1, 990.00, '2026-04-04', 'anual', 'tarjeta', 'PAG-22298', '2026-05-04', '2026-07-04 10:56:36', '2026-07-04 10:56:36'),
	(4, 1, 1, 990.00, '2026-03-04', 'anual', 'tarjeta', 'PAG-68287', '2026-04-04', '2026-07-04 10:56:36', '2026-07-04 10:56:36'),
	(5, 2, 2, 199.00, '2026-06-04', 'mensual', 'transferencia', 'PAG-10781', '2026-07-04', '2026-07-04 10:56:36', '2026-07-04 10:56:36'),
	(6, 2, 2, 199.00, '2026-05-04', 'mensual', 'transferencia', 'PAG-61238', '2026-06-04', '2026-07-04 10:56:36', '2026-07-04 10:56:36'),
	(7, 4, 1, 99.00, '2026-06-04', 'mensual', 'transferencia', 'PAG-38609', '2026-07-04', '2026-07-04 10:56:36', '2026-07-04 10:56:36'),
	(8, 4, 1, 99.00, '2026-05-04', 'mensual', 'transferencia', 'PAG-97984', '2026-06-04', '2026-07-04 10:56:36', '2026-07-04 10:56:36'),
	(9, 4, 1, 99.00, '2026-04-04', 'mensual', 'transferencia', 'PAG-98205', '2026-05-04', '2026-07-04 10:56:36', '2026-07-04 10:56:36'),
	(10, 5, 2, 199.00, '2026-06-04', 'mensual', 'tarjeta', 'PAG-93298', '2026-07-04', '2026-07-04 10:56:36', '2026-07-04 10:56:36'),
	(11, 5, 2, 199.00, '2026-05-04', 'mensual', 'yape', 'PAG-12096', '2026-06-04', '2026-07-04 10:56:36', '2026-07-04 10:56:36'),
	(12, 6, 3, 3990.00, '2026-06-04', 'anual', 'tarjeta', 'PAG-89771', '2026-07-04', '2026-07-04 10:56:36', '2026-07-04 10:56:36'),
	(13, 8, 2, 199.00, '2026-06-04', 'mensual', 'transferencia', 'PAG-31205', '2026-07-04', '2026-07-04 10:56:36', '2026-07-04 10:56:36'),
	(14, 9, 3, 399.00, '2026-06-04', 'mensual', 'yape', 'PAG-98744', '2026-07-04', '2026-07-04 10:56:36', '2026-07-04 10:56:36'),
	(15, 9, 3, 399.00, '2026-05-04', 'mensual', 'yape', 'PAG-64456', '2026-06-04', '2026-07-04 10:56:36', '2026-07-04 10:56:36'),
	(16, 9, 3, 399.00, '2026-04-04', 'mensual', 'transferencia', 'PAG-20325', '2026-05-04', '2026-07-04 10:56:36', '2026-07-04 10:56:36'),
	(17, 9, 3, 399.00, '2026-03-04', 'mensual', 'yape', 'PAG-21309', '2026-04-04', '2026-07-04 10:56:36', '2026-07-04 10:56:36'),
	(18, 10, 1, 990.00, '2026-06-04', 'anual', 'tarjeta', 'PAG-39065', '2026-07-04', '2026-07-04 10:56:36', '2026-07-04 10:56:36'),
	(19, 10, 1, 990.00, '2026-05-04', 'anual', 'tarjeta', 'PAG-67217', '2026-06-04', '2026-07-04 10:56:36', '2026-07-04 10:56:36'),
	(20, 10, 1, 990.00, '2026-04-04', 'anual', 'transferencia', 'PAG-93606', '2026-05-04', '2026-07-04 10:56:36', '2026-07-04 10:56:36');

-- Volcando estructura para tabla saas_taller_automotriz.password_reset_tokens
CREATE TABLE IF NOT EXISTS `password_reset_tokens` (
  `email` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla saas_taller_automotriz.password_reset_tokens: ~0 rows (aproximadamente)
DELETE FROM `password_reset_tokens`;

-- Volcando estructura para tabla saas_taller_automotriz.planes
CREATE TABLE IF NOT EXISTS `planes` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `nombre` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `precio_mensual` decimal(10,2) NOT NULL DEFAULT '0.00',
  `precio_anual` decimal(10,2) NOT NULL DEFAULT '0.00',
  `limite_usuarios` int NOT NULL DEFAULT '0',
  `limite_ordenes` int NOT NULL DEFAULT '0',
  `descripcion` text COLLATE utf8mb4_unicode_ci,
  `caracteristicas` text COLLATE utf8mb4_unicode_ci,
  `destacado` tinyint(1) NOT NULL DEFAULT '0',
  `activo` tinyint(1) NOT NULL DEFAULT '1',
  `orden` int NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla saas_taller_automotriz.planes: ~2 rows (aproximadamente)
DELETE FROM `planes`;
INSERT INTO `planes` (`id`, `nombre`, `slug`, `precio_mensual`, `precio_anual`, `limite_usuarios`, `limite_ordenes`, `descripcion`, `caracteristicas`, `destacado`, `activo`, `orden`, `created_at`, `updated_at`) VALUES
	(1, 'Básico', 'basico', 99.00, 990.00, 3, 100, 'Ideal para talleres que están empezando.', 'Hasta 3 usuarios\n100 órdenes por mes\nGestión de clientes y vehículos\nInventario básico\nSoporte por correo', 0, 1, 1, '2026-07-04 10:56:36', '2026-07-04 10:56:36'),
	(2, 'Profesional', 'profesional', 199.00, 1990.00, 10, 500, 'El favorito de los talleres en crecimiento.', 'Hasta 10 usuarios\n500 órdenes por mes\nFacturación y caja\nReportes avanzados\nImportar / exportar datos\nSoporte prioritario', 1, 1, 2, '2026-07-04 10:56:36', '2026-07-04 10:56:36'),
	(3, 'Empresarial', 'empresarial', 399.00, 3990.00, 0, 0, 'Para cadenas y talleres de alto volumen.', 'Usuarios ilimitados\nÓrdenes ilimitadas\nMulti-sucursal\nRoles y permisos avanzados\nAPI e integraciones\nSoporte 24/7 dedicado', 0, 1, 3, '2026-07-04 10:56:36', '2026-07-04 10:56:36');

-- Volcando estructura para tabla saas_taller_automotriz.proveedores
CREATE TABLE IF NOT EXISTS `proveedores` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `ruc` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nombre` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `contacto` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `telefono` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `direccion` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=24 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla saas_taller_automotriz.proveedores: ~23 rows (aproximadamente)
DELETE FROM `proveedores`;
INSERT INTO `proveedores` (`id`, `ruc`, `nombre`, `contacto`, `telefono`, `email`, `direccion`, `activo`, `created_at`, `updated_at`) VALUES
	(1, '20123456781', 'Repuestos San Martín', NULL, '014567890', NULL, NULL, 1, '2026-07-04 02:52:43', '2026-07-04 02:52:43'),
	(2, '20456789012', 'AutoPartes del Perú', NULL, '013334455', NULL, NULL, 1, '2026-07-04 02:52:43', '2026-07-04 02:52:43'),
	(3, '20567890123', 'Lubricantes Premium', NULL, '015556677', NULL, NULL, 1, '2026-07-04 02:52:43', '2026-07-04 02:52:43'),
	(4, '20055341271', 'Kassulke PLC', 'Meghan Bogisich', '012641161', 'shayna72@example.net', '46943 Wintheiser Street\nWest Guy, AR 92604-2437', 1, '2026-05-27 06:26:23', '2026-07-10 13:33:36'),
	(5, '20909992451', 'Hagenes-Daniel', 'Sister Greenfelder', '012908802', 'hjohns@example.com', '5091 Connelly Light Apt. 737\nWest Martina, SD 28746', 1, '2026-01-25 12:52:11', '2026-07-10 13:33:36'),
	(6, '20618645481', 'Fay-Brown', 'Micaela Jakubowski', '014103564', 'grace.goldner@example.org', '3221 Bernhard Island Apt. 871\nWillview, KY 58880-1793', 1, '2026-06-28 05:29:56', '2026-07-10 13:33:36'),
	(7, '20395852481', 'Kemmer, Huels and Wisozk', 'Antonia Jacobs', '012347080', 'ndonnelly@example.net', '47771 Stokes Meadow Apt. 513\nPort Lorenzo, SC 36484-9154', 1, '2026-02-19 13:08:18', '2026-07-10 13:33:36'),
	(8, '20481326011', 'Mueller PLC', 'Orland Boehm', '018243244', 'kertzmann.alexandrine@example.org', '52687 Ernest Mount\nNorth Letitia, WA 64791-5399', 1, '2026-05-01 01:08:52', '2026-07-10 13:33:36'),
	(9, '20049672191', 'King Group', 'Theresa Ritchie', '014129383', 'adella12@example.net', '5355 Adah Bypass Apt. 542\nLake Randalbury, HI 24074', 1, '2026-02-07 23:53:40', '2026-07-10 13:33:36'),
	(10, '20958849501', 'Kunde-Pagac', 'Dr. Ruby Price', '016185337', 'elissa.ritchie@example.com', '65373 Demarcus Groves Suite 673\nZiemannside, MS 87774-9785', 1, '2026-02-10 02:33:39', '2026-07-10 13:33:36'),
	(11, '20122010581', 'Hammes Ltd', 'Fatima Volkman', '012745526', 'morissette.citlalli@example.com', '4263 Price Ports Apt. 930\nSouth Meganeborough, OK 10340-1740', 1, '2026-07-01 12:40:14', '2026-07-10 13:33:36'),
	(12, '20779798771', 'Bergnaum, Daniel and Bergstrom', 'Beatrice Schuster II', '012406803', 'huels.bernice@example.com', '2101 Ullrich Meadow\nNew Anastaciohaven, CT 51753-7509', 1, '2026-02-25 13:58:50', '2026-07-10 13:33:36'),
	(13, '20922477481', 'Gutkowski, Rempel and Abernathy', 'Prof. Bernadette Torp', '012392701', 'lupe.reilly@example.com', '6800 Olson Spurs\nEast Karine, NY 93389', 1, '2026-03-30 13:12:41', '2026-07-10 13:33:36'),
	(14, '20603140031', 'Nicolas-Heidenreich', 'Elinor Boehm V', '014794333', 'santina.goldner@example.net', '7888 Noemie Isle\nLake Hector, MS 55260-8409', 1, '2026-01-12 17:08:03', '2026-07-10 13:34:00'),
	(15, '20291495971', 'Kulas and Sons', 'Lacey Lakin', '017858208', 'shanelle49@example.net', '769 Huels Burg\nKulasfurt, AL 59162', 1, '2026-06-11 06:49:32', '2026-07-10 13:34:00'),
	(16, '20068861391', 'Franecki, Schowalter and O\'Kon', 'Kira Bogan', '018361986', 'mueller.karli@example.net', '274 Ondricka Orchard Apt. 917\nReichelside, MI 51821-4697', 1, '2026-06-12 00:46:24', '2026-07-10 13:34:00'),
	(17, '20044990491', 'Mayert-Goodwin', 'Dr. Marge Metz IV', '015527728', 'hackett.lisa@example.org', '6519 Summer Corner\nWest Florencioburgh, VT 08972-8400', 1, '2026-04-13 15:56:09', '2026-07-10 13:34:00'),
	(18, '20392546941', 'Pfannerstill-Connelly', 'Prof. Arthur Prosacco', '018792199', 'torp.raphaelle@example.net', '649 Francisco Trafficway\nPort Alexandria, CT 62936', 1, '2026-02-22 19:07:46', '2026-07-10 13:34:00'),
	(19, '20466319761', 'Halvorson, Dibbert and Lueilwitz', 'Mr. Nat DuBuque', '014782468', 'kendrick18@example.org', '2809 Skiles Green Suite 258\nPort Ebbaborough, CO 46949', 1, '2026-06-04 11:12:35', '2026-07-10 13:34:00'),
	(20, '20804715261', 'Zemlak, Klocko and O\'Hara', 'Dr. Sebastian Gutmann', '016842178', 'hansen.jabari@example.org', '65612 Harvey Burg Suite 759\nKreigerport, DC 96745', 1, '2026-02-16 02:19:40', '2026-07-10 13:34:00'),
	(21, '20693108561', 'Leuschke-Becker', 'Gardner Bins', '010388223', 'koby40@example.org', '319 Makayla Mountain\nEast Nelleburgh, NC 29596-4590', 1, '2026-05-11 01:26:20', '2026-07-10 13:34:00'),
	(22, '20003329371', 'Beahan, Hettinger and Herzog', 'Dr. Jarrod Ward Jr.', '011789762', 'aufderhar.tracy@example.com', '18323 Strosin Springs\nNinaside, CA 29860', 1, '2026-05-13 21:18:39', '2026-07-10 13:34:00'),
	(23, '20983378531', 'Koch Inc', 'Ara Gorczany', '014146682', 'dwill@example.com', '7903 Beier Turnpike Suite 564\nCasperville, OK 12080', 1, '2026-05-28 18:28:17', '2026-07-10 13:34:00');

-- Volcando estructura para tabla saas_taller_automotriz.repuestos
CREATE TABLE IF NOT EXISTS `repuestos` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `proveedor_id` bigint unsigned DEFAULT NULL,
  `codigo` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nombre` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `categoria` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `unidad` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'UND',
  `precio_compra` decimal(10,2) NOT NULL DEFAULT '0.00',
  `precio_venta` decimal(10,2) NOT NULL DEFAULT '0.00',
  `stock` int NOT NULL DEFAULT '0',
  `stock_minimo` int NOT NULL DEFAULT '0',
  `ubicacion` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `repuestos_proveedor_id_foreign` (`proveedor_id`),
  CONSTRAINT `repuestos_proveedor_id_foreign` FOREIGN KEY (`proveedor_id`) REFERENCES `proveedores` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=28 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla saas_taller_automotriz.repuestos: ~27 rows (aproximadamente)
DELETE FROM `repuestos`;
INSERT INTO `repuestos` (`id`, `proveedor_id`, `codigo`, `nombre`, `categoria`, `unidad`, `precio_compra`, `precio_venta`, `stock`, `stock_minimo`, `ubicacion`, `activo`, `created_at`, `updated_at`) VALUES
	(1, 1, 'REP-001', 'Filtro de aceite', 'Filtros', 'UND', 15.00, 28.00, 94, 10, NULL, 1, '2026-07-04 02:52:43', '2026-07-04 10:29:19'),
	(2, 3, 'REP-002', 'Aceite 15W-40 (1L)', 'Lubricantes', 'UND', 22.00, 38.00, 91, 15, NULL, 1, '2026-07-04 02:52:43', '2026-07-04 10:29:19'),
	(3, 3, 'REP-003', 'Pastillas de freno delanteras', 'Frenos', 'UND', 55.00, 95.00, 76, 12, NULL, 1, '2026-07-04 02:52:43', '2026-07-04 10:29:19'),
	(4, 1, 'REP-004', 'Batería 12V 70Ah', 'Eléctrico', 'UND', 180.00, 260.00, 111, 5, NULL, 1, '2026-07-04 02:52:43', '2026-07-04 10:29:19'),
	(5, 1, 'REP-005', 'Bujías (juego x4)', 'Motor', 'UND', 40.00, 75.00, 127, 8, NULL, 1, '2026-07-04 02:52:43', '2026-07-04 10:29:19'),
	(6, 1, 'REP-006', 'Filtro de aire', 'Filtros', 'UND', 18.00, 32.00, 37, 10, NULL, 1, '2026-07-04 02:52:43', '2026-07-04 10:29:19'),
	(7, 2, 'REP-007', 'Correa de distribución', 'Motor', 'UND', 85.00, 140.00, 103, 5, NULL, 1, '2026-07-04 02:52:43', '2026-07-04 10:29:19'),
	(8, 4, 'REP-8631', 'Repuesto cumque', 'Filtros', 'UND', 99.70, 72.81, 31, 5, NULL, 1, '2026-02-04 12:17:58', '2026-07-10 13:33:36'),
	(9, 11, 'REP-8621', 'Repuesto minus', 'Filtros', 'UND', 13.92, 133.30, 16, 5, NULL, 1, '2026-05-17 19:17:54', '2026-07-10 13:33:36'),
	(10, 6, 'REP-7018', 'Repuesto harum', 'Filtros', 'UND', 20.64, 111.85, 25, 5, NULL, 1, '2026-03-20 19:55:20', '2026-07-10 13:33:36'),
	(11, 8, 'REP-8154', 'Repuesto iure', 'Filtros', 'UND', 38.08, 95.75, 25, 5, NULL, 1, '2026-02-23 05:31:44', '2026-07-10 13:33:36'),
	(12, 4, 'REP-6765', 'Repuesto ab', 'Filtros', 'UND', 85.32, 103.70, 21, 5, NULL, 1, '2026-03-02 18:33:34', '2026-07-10 13:33:36'),
	(13, 8, 'REP-9423', 'Repuesto quae', 'Filtros', 'UND', 84.66, 190.04, 19, 5, NULL, 1, '2026-05-28 13:43:33', '2026-07-10 13:33:36'),
	(14, 10, 'REP-3419', 'Repuesto voluptas', 'Filtros', 'UND', 41.32, 110.52, 48, 5, NULL, 1, '2026-01-12 17:25:09', '2026-07-10 13:33:36'),
	(15, 6, 'REP-5531', 'Repuesto tenetur', 'Filtros', 'UND', 42.56, 95.30, 12, 5, NULL, 1, '2026-02-28 00:12:15', '2026-07-10 13:33:36'),
	(16, 10, 'REP-2332', 'Repuesto placeat', 'Filtros', 'UND', 60.28, 150.38, 35, 5, NULL, 1, '2026-03-14 16:19:50', '2026-07-10 13:33:36'),
	(17, 6, 'REP-8371', 'Repuesto ab', 'Filtros', 'UND', 20.35, 59.98, 40, 5, NULL, 1, '2026-03-15 18:15:30', '2026-07-10 13:33:36'),
	(18, 20, 'REP-1515', 'Repuesto expedita', 'Filtros', 'UND', 17.12, 85.45, 22, 5, NULL, 1, '2026-05-18 12:09:29', '2026-07-10 13:34:00'),
	(19, 15, 'REP-2009', 'Repuesto vel', 'Filtros', 'UND', 96.05, 190.80, 23, 5, NULL, 1, '2026-03-31 08:31:16', '2026-07-10 13:34:00'),
	(20, 20, 'REP-2115', 'Repuesto et', 'Filtros', 'UND', 71.46, 75.09, 8, 5, NULL, 1, '2026-02-14 03:14:22', '2026-07-10 13:34:00'),
	(21, 22, 'REP-6126', 'Repuesto a', 'Filtros', 'UND', 65.94, 127.24, 35, 5, NULL, 1, '2026-03-08 10:29:45', '2026-07-10 13:34:00'),
	(22, 18, 'REP-5979', 'Repuesto enim', 'Filtros', 'UND', 73.01, 59.58, 30, 5, NULL, 1, '2026-01-10 17:31:11', '2026-07-10 13:34:00'),
	(23, 16, 'REP-4878', 'Repuesto quisquam', 'Filtros', 'UND', 72.12, 34.98, 50, 5, NULL, 1, '2026-05-06 05:59:25', '2026-07-10 13:34:00'),
	(24, 22, 'REP-7239', 'Repuesto sit', 'Filtros', 'UND', 41.75, 74.25, 6, 5, NULL, 1, '2026-05-04 20:35:45', '2026-07-10 13:34:00'),
	(25, 16, 'REP-2399', 'Repuesto ratione', 'Filtros', 'UND', 52.72, 129.94, 10, 5, NULL, 1, '2026-04-20 04:24:38', '2026-07-10 13:34:00'),
	(26, 17, 'REP-2027', 'Repuesto consectetur', 'Filtros', 'UND', 77.90, 104.97, 8, 5, NULL, 1, '2026-03-12 09:46:19', '2026-07-10 13:34:00'),
	(27, 14, 'REP-5997', 'Repuesto iusto', 'Filtros', 'UND', 58.56, 191.46, 33, 5, NULL, 1, '2026-05-17 10:17:07', '2026-07-10 13:34:00');

-- Volcando estructura para tabla saas_taller_automotriz.servicios
CREATE TABLE IF NOT EXISTS `servicios` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `codigo` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nombre` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `categoria` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `descripcion` text COLLATE utf8mb4_unicode_ci,
  `precio` decimal(10,2) NOT NULL DEFAULT '0.00',
  `duracion_min` int DEFAULT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=29 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla saas_taller_automotriz.servicios: ~28 rows (aproximadamente)
DELETE FROM `servicios`;
INSERT INTO `servicios` (`id`, `codigo`, `nombre`, `categoria`, `descripcion`, `precio`, `duracion_min`, `activo`, `created_at`, `updated_at`) VALUES
	(1, 'SRV-001', 'Cambio de aceite y filtro', 'Mantenimiento', NULL, 120.00, 40, 1, '2026-07-04 02:52:43', '2026-07-04 02:52:43'),
	(2, 'SRV-002', 'Alineamiento y balanceo', 'Suspensión', NULL, 90.00, 60, 1, '2026-07-04 02:52:43', '2026-07-04 02:52:43'),
	(3, 'SRV-003', 'Cambio de pastillas de freno', 'Frenos', NULL, 150.00, 90, 1, '2026-07-04 02:52:43', '2026-07-04 02:52:43'),
	(4, 'SRV-004', 'Afinamiento de motor', 'Motor', NULL, 280.00, 120, 1, '2026-07-04 02:52:43', '2026-07-04 02:52:43'),
	(5, 'SRV-005', 'Diagnóstico por escáner', 'Electrónica', NULL, 80.00, 45, 1, '2026-07-04 02:52:43', '2026-07-04 02:52:43'),
	(6, 'SRV-006', 'Cambio de batería', 'Electricidad', NULL, 60.00, 30, 1, '2026-07-04 02:52:43', '2026-07-04 02:52:43'),
	(7, 'SRV-007', 'Revisión de suspensión', 'Suspensión', NULL, 100.00, 60, 1, '2026-07-04 02:52:43', '2026-07-04 02:52:43'),
	(8, 'SRV-008', 'Cambio de correa de distribución', 'Motor', NULL, 450.00, 180, 1, '2026-07-04 02:52:43', '2026-07-04 02:52:43'),
	(9, 'SRV-3484', 'Servicio similique', 'Planchado', NULL, 56.45, 90, 1, '2026-01-30 09:01:46', '2026-07-10 13:33:36'),
	(10, 'SRV-8058', 'Servicio illum', 'Planchado', NULL, 204.54, 90, 1, '2026-06-23 01:02:22', '2026-07-10 13:33:36'),
	(11, 'SRV-3988', 'Servicio non', 'Planchado', NULL, 127.17, 60, 1, '2026-07-09 20:41:18', '2026-07-10 13:33:36'),
	(12, 'SRV-1278', 'Servicio non', 'Mecánica', NULL, 334.02, 60, 1, '2026-05-08 08:03:10', '2026-07-10 13:33:36'),
	(13, 'SRV-9615', 'Servicio consequatur', 'Mecánica', NULL, 321.74, 30, 1, '2026-01-20 05:24:04', '2026-07-10 13:33:36'),
	(14, 'SRV-7014', 'Servicio necessitatibus', 'Mecánica', NULL, 250.14, 120, 1, '2026-02-21 16:18:09', '2026-07-10 13:33:36'),
	(15, 'SRV-3108', 'Servicio aut', 'Electricidad', NULL, 222.89, 60, 1, '2026-06-10 22:36:08', '2026-07-10 13:33:36'),
	(16, 'SRV-5920', 'Servicio rerum', 'Electricidad', NULL, 77.14, 30, 1, '2026-06-11 13:13:39', '2026-07-10 13:33:36'),
	(17, 'SRV-9989', 'Servicio asperiores', 'Electricidad', NULL, 115.64, 120, 1, '2026-06-27 22:35:42', '2026-07-10 13:33:36'),
	(18, 'SRV-8286', 'Servicio sint', 'Electricidad', NULL, 483.84, 60, 1, '2026-05-25 14:09:10', '2026-07-10 13:33:36'),
	(19, 'SRV-0959', 'Servicio cumque', 'Planchado', NULL, 198.67, 30, 1, '2026-04-11 04:22:10', '2026-07-10 13:34:00'),
	(20, 'SRV-8276', 'Servicio consectetur', 'Planchado', NULL, 440.81, 30, 1, '2026-05-02 13:29:38', '2026-07-10 13:34:00'),
	(21, 'SRV-4435', 'Servicio nihil', 'Mecánica', NULL, 417.38, 120, 1, '2026-04-04 04:22:32', '2026-07-10 13:34:00'),
	(22, 'SRV-4551', 'Servicio qui', 'Electricidad', NULL, 222.30, 60, 1, '2026-04-12 03:10:22', '2026-07-10 13:34:00'),
	(23, 'SRV-0130', 'Servicio assumenda', 'Electricidad', NULL, 237.88, 120, 1, '2026-05-20 16:33:59', '2026-07-10 13:34:00'),
	(24, 'SRV-3137', 'Servicio perferendis', 'Planchado', NULL, 378.32, 120, 1, '2026-04-11 01:11:24', '2026-07-10 13:34:00'),
	(25, 'SRV-9693', 'Servicio aut', 'Electricidad', NULL, 295.25, 60, 1, '2026-05-20 19:56:22', '2026-07-10 13:34:00'),
	(26, 'SRV-1813', 'Servicio vel', 'Electricidad', NULL, 479.15, 90, 1, '2026-06-30 15:50:01', '2026-07-10 13:34:00'),
	(27, 'SRV-6817', 'Servicio et', 'Mecánica', NULL, 274.91, 120, 1, '2026-07-05 14:16:54', '2026-07-10 13:34:00'),
	(28, 'SRV-6602', 'Servicio sint', 'Mecánica', NULL, 431.29, 60, 1, '2026-06-19 02:56:17', '2026-07-10 13:34:00');

-- Volcando estructura para tabla saas_taller_automotriz.sessions
CREATE TABLE IF NOT EXISTS `sessions` (
  `id` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_activity` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla saas_taller_automotriz.sessions: ~0 rows (aproximadamente)
DELETE FROM `sessions`;

-- Volcando estructura para tabla saas_taller_automotriz.talleres
CREATE TABLE IF NOT EXISTS `talleres` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `nombre` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `ruc` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `contacto_nombre` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `telefono` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ciudad` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `plan_id` bigint unsigned DEFAULT NULL,
  `estado` enum('prueba','activo','suspendido','cancelado') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'prueba',
  `periodo` enum('mensual','anual') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'mensual',
  `fecha_inicio` date DEFAULT NULL,
  `fecha_vencimiento` date DEFAULT NULL,
  `notas` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `talleres_plan_id_foreign` (`plan_id`),
  CONSTRAINT `talleres_plan_id_foreign` FOREIGN KEY (`plan_id`) REFERENCES `planes` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla saas_taller_automotriz.talleres: ~10 rows (aproximadamente)
DELETE FROM `talleres`;
INSERT INTO `talleres` (`id`, `nombre`, `ruc`, `contacto_nombre`, `email`, `telefono`, `ciudad`, `plan_id`, `estado`, `periodo`, `fecha_inicio`, `fecha_vencimiento`, `notas`, `created_at`, `updated_at`) VALUES
	(1, 'Taller MotorMax', '20383934879', 'Contacto 1', 'tallermotormax@correo.com', '995428010', 'Trujillo', 1, 'activo', 'anual', '2025-09-07', '2026-08-13', NULL, '2026-07-04 10:56:36', '2026-07-04 10:56:36'),
	(2, 'Servicentro El Rápido', '20933428976', 'Contacto 2', 'servicentroelrápido@correo.com', '940752367', 'Arequipa', 2, 'activo', 'mensual', '2026-06-14', '2026-07-14', NULL, '2026-07-04 10:56:36', '2026-07-04 10:56:36'),
	(3, 'AutoFix Premium', '20623296566', 'Contacto 3', 'autofixpremium@correo.com', '972862314', 'Trujillo', 3, 'prueba', 'mensual', '2026-06-29', '2026-07-13', NULL, '2026-07-04 10:56:36', '2026-07-04 10:56:36'),
	(4, 'Mecánica González', '20604346982', 'Contacto 4', 'mecánicagonzález@correo.com', '972465457', 'Cusco', 1, 'activo', 'mensual', '2026-05-05', '2026-07-07', NULL, '2026-07-04 10:56:36', '2026-07-04 10:56:36'),
	(5, 'Taller Los Andes', '20706784742', 'Contacto 5', 'tallerlosandes@correo.com', '995142367', 'Arequipa', 2, 'suspendido', 'mensual', '2026-05-15', '2026-06-24', NULL, '2026-07-04 10:56:36', '2026-07-04 10:56:36'),
	(6, 'CarService Pro', '20829411045', 'Contacto 6', 'carservicepro@correo.com', '953295645', 'Trujillo', 3, 'activo', 'anual', '2026-03-06', '2027-03-01', NULL, '2026-07-04 10:56:36', '2026-07-04 10:56:36'),
	(7, 'Lubricentro Central', '20882075750', 'Contacto 7', 'lubricentrocentral@correo.com', '955463081', 'Arequipa', 1, 'prueba', 'mensual', '2026-07-02', '2026-07-16', NULL, '2026-07-04 10:56:36', '2026-07-04 10:56:36'),
	(8, 'Taller Hermanos Ruiz', '20559416571', 'Contacto 8', 'tallerhermanosruiz@correo.com', '969191578', 'Cusco', 2, 'cancelado', 'mensual', '2025-12-16', '2026-04-15', NULL, '2026-07-04 10:56:36', '2026-07-04 10:56:36'),
	(9, 'AutoElite', '20950708062', 'Contacto 9', 'autoelite@correo.com', '939506528', 'Lima', 3, 'activo', 'mensual', '2026-06-19', '2026-07-19', NULL, '2026-07-04 10:56:36', '2026-07-04 10:56:36'),
	(10, 'Multiservicios Díaz', '20339840259', 'Contacto 10', 'multiserviciosdíaz@correo.com', '984663522', 'Arequipa', 1, 'activo', 'anual', '2025-05-30', '2027-05-30', NULL, '2026-07-04 10:56:36', '2026-07-04 10:56:36');

-- Volcando estructura para tabla saas_taller_automotriz.users
CREATE TABLE IF NOT EXISTS `users` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `rol` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'empleado',
  `telefono` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `avatar` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT '1',
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla saas_taller_automotriz.users: ~5 rows (aproximadamente)
DELETE FROM `users`;
INSERT INTO `users` (`id`, `name`, `email`, `email_verified_at`, `password`, `rol`, `telefono`, `avatar`, `activo`, `remember_token`, `created_at`, `updated_at`) VALUES
	(1, 'Administrador', 'admin@taller.com', NULL, '$2y$12$7JgEd72GyMV3NtztTKNVG.0SpHD1T4Kgppi4hjoDTj2iw.5ODpWWe', 'admin', '999888777', NULL, 1, NULL, '2026-07-04 02:52:42', '2026-07-04 02:52:42'),
	(2, 'Carlos Ramírez', 'carlos@taller.com', NULL, '$2y$12$55IvIXSxMPhUBF19pdyB/eh9YFlchJzzvEVrg9uR5syDr0CDJ.mJa', 'mecanico', NULL, NULL, 1, NULL, '2026-07-04 02:52:42', '2026-07-04 02:52:42'),
	(3, 'Luis Torres', 'luis@taller.com', NULL, '$2y$12$DxS17rEbONMIme6np/RocO4Kwdp0LwHLT1Fc7wmafdG95QlLCxd8G', 'mecanico', NULL, NULL, 1, NULL, '2026-07-04 02:52:43', '2026-07-04 02:52:43'),
	(4, 'Miguel Ángel Díaz', 'miguel@taller.com', NULL, '$2y$12$A1TwVc72zw1FgHTOXdl48uD6xdpNCRT2gQ2iwgbS2enYvyVlHFAvS', 'mecanico', NULL, NULL, 1, NULL, '2026-07-04 02:52:43', '2026-07-04 02:52:43'),
	(5, 'Gerente General', 'gerente@taller.com', NULL, '$2y$12$2VrP.q1uKQwlpHxF49QBQun/7BGLCc15Qpavez6rxZiUFYA8Tcuoe', 'gerente', NULL, NULL, 1, NULL, '2026-07-04 02:52:43', '2026-07-04 02:52:43'),
	(6, 'Super Administrador', 'superadmin@autotaller.com', NULL, '$2y$12$bquvk04KyP.EEfMaix/GUu/FqcfRaPaeFgVuOKcPP0pR4fd7SEjsq', 'superadmin', '900000000', NULL, 1, NULL, '2026-07-04 10:56:36', '2026-07-04 10:56:36');

-- Volcando estructura para tabla saas_taller_automotriz.vehiculos
CREATE TABLE IF NOT EXISTS `vehiculos` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `cliente_id` bigint unsigned NOT NULL,
  `placa` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `marca` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `modelo` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `anio` year DEFAULT NULL,
  `color` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `vin` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `motor` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `combustible` enum('gasolina','diesel','glp','gnv','electrico','hibrido') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'gasolina',
  `transmision` enum('manual','automatica') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `kilometraje` int DEFAULT NULL,
  `observaciones` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `vehiculos_cliente_id_foreign` (`cliente_id`),
  KEY `vehiculos_placa_index` (`placa`),
  CONSTRAINT `vehiculos_cliente_id_foreign` FOREIGN KEY (`cliente_id`) REFERENCES `clientes` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=37 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla saas_taller_automotriz.vehiculos: ~36 rows (aproximadamente)
DELETE FROM `vehiculos`;
INSERT INTO `vehiculos` (`id`, `cliente_id`, `placa`, `marca`, `modelo`, `anio`, `color`, `vin`, `motor`, `combustible`, `transmision`, `kilometraje`, `observaciones`, `created_at`, `updated_at`) VALUES
	(1, 1, 'ABC-123', 'Toyota', 'Corolla', '2019', 'Blanco', NULL, NULL, 'gasolina', 'automatica', 51972, NULL, '2026-07-04 02:52:43', '2026-07-04 02:52:43'),
	(2, 2, 'XYZ-789', 'Hyundai', 'Accent', '2017', 'Negro', NULL, NULL, 'gasolina', 'automatica', 73422, NULL, '2026-07-04 02:52:43', '2026-07-04 02:52:43'),
	(3, 3, 'DEF-456', 'Kia', 'Rio', '2021', 'Gris', NULL, NULL, 'gasolina', 'manual', 26690, NULL, '2026-07-04 02:52:43', '2026-07-04 02:52:43'),
	(4, 4, 'GHI-321', 'Nissan', 'Sentra', '2013', 'Rojo', NULL, NULL, 'gasolina', 'automatica', 102295, NULL, '2026-07-04 02:52:43', '2026-07-04 02:52:43'),
	(5, 5, 'JKL-654', 'Volkswagen', 'Gol', '2023', 'Azul', NULL, NULL, 'gasolina', 'manual', 38581, NULL, '2026-07-04 02:52:43', '2026-07-04 02:52:43'),
	(6, 6, 'F1B-200', 'Toyota', 'Hiace', '2020', 'Blanco', NULL, NULL, 'diesel', 'manual', 98000, NULL, '2026-07-04 02:52:43', '2026-07-04 02:52:43'),
	(7, 7, 'HEA-067', 'Kia', 'eos', '2010', 'teal', NULL, NULL, 'gasolina', NULL, NULL, NULL, '2026-04-22 17:12:01', '2026-07-10 13:32:22'),
	(8, 8, 'XUM-959', 'Toyota', 'laboriosam', '2007', 'purple', NULL, NULL, 'gasolina', NULL, NULL, NULL, '2026-05-01 11:24:52', '2026-07-10 13:32:22'),
	(9, 9, 'TXS-130', 'Kia', 'aut', '2021', 'green', NULL, NULL, 'gasolina', NULL, NULL, NULL, '2026-04-25 15:49:39', '2026-07-10 13:32:22'),
	(10, 10, 'VGQ-016', 'Kia', 'qui', '2005', 'navy', NULL, NULL, 'gasolina', NULL, NULL, NULL, '2026-04-17 17:58:06', '2026-07-10 13:32:22'),
	(11, 11, 'UDD-617', 'Toyota', 'et', '2016', 'green', NULL, NULL, 'gasolina', NULL, NULL, NULL, '2026-03-09 00:00:15', '2026-07-10 13:32:22'),
	(12, 12, 'HTG-585', 'Nissan', 'rerum', '2007', 'white', NULL, NULL, 'gasolina', NULL, NULL, NULL, '2026-06-13 20:08:32', '2026-07-10 13:32:22'),
	(13, 13, 'KNG-356', 'Nissan', 'id', '2013', 'navy', NULL, NULL, 'gasolina', NULL, NULL, NULL, '2026-02-24 17:38:25', '2026-07-10 13:32:22'),
	(14, 14, 'UDD-047', 'Nissan', 'cum', '2022', 'fuchsia', NULL, NULL, 'gasolina', NULL, NULL, NULL, '2026-01-16 04:36:11', '2026-07-10 13:32:22'),
	(15, 15, 'NVN-674', 'Honda', 'qui', '2015', 'olive', NULL, NULL, 'gasolina', NULL, NULL, NULL, '2026-02-15 04:40:05', '2026-07-10 13:32:22'),
	(16, 16, 'CHQ-345', 'Nissan', 'possimus', '2009', 'aqua', NULL, NULL, 'gasolina', NULL, NULL, NULL, '2026-02-14 08:02:59', '2026-07-10 13:32:22'),
	(17, 17, 'HRU-747', 'Honda', 'fuga', '2020', 'teal', NULL, NULL, 'gasolina', NULL, NULL, NULL, '2026-03-19 03:26:54', '2026-07-10 13:33:36'),
	(18, 18, 'VEU-800', 'Toyota', 'id', '2019', 'green', NULL, NULL, 'gasolina', NULL, NULL, NULL, '2026-01-16 22:13:51', '2026-07-10 13:33:36'),
	(19, 19, 'BJC-616', 'Toyota', 'perferendis', '2003', 'blue', NULL, NULL, 'gasolina', NULL, NULL, NULL, '2026-04-24 21:19:54', '2026-07-10 13:33:36'),
	(20, 20, 'TBY-291', 'Nissan', 'ipsum', '2011', 'fuchsia', NULL, NULL, 'gasolina', NULL, NULL, NULL, '2026-05-22 00:53:34', '2026-07-10 13:33:36'),
	(21, 21, 'OWH-924', 'Nissan', 'architecto', '2004', 'purple', NULL, NULL, 'gasolina', NULL, NULL, NULL, '2026-05-23 22:58:06', '2026-07-10 13:33:36'),
	(22, 22, 'KPF-997', 'Toyota', 'quo', '2002', 'purple', NULL, NULL, 'gasolina', NULL, NULL, NULL, '2026-03-24 14:34:51', '2026-07-10 13:33:36'),
	(23, 23, 'GLE-018', 'Honda', 'voluptatum', '2004', 'maroon', NULL, NULL, 'gasolina', NULL, NULL, NULL, '2026-03-31 13:50:50', '2026-07-10 13:33:36'),
	(24, 24, 'YKU-698', 'Hyundai', 'velit', '2001', 'teal', NULL, NULL, 'gasolina', NULL, NULL, NULL, '2026-03-20 11:55:53', '2026-07-10 13:33:36'),
	(25, 25, 'LCV-926', 'Hyundai', 'maxime', '2018', 'silver', NULL, NULL, 'gasolina', NULL, NULL, NULL, '2026-04-21 14:21:15', '2026-07-10 13:33:36'),
	(26, 26, 'KTV-265', 'Toyota', 'voluptatem', '2001', 'lime', NULL, NULL, 'gasolina', NULL, NULL, NULL, '2026-06-27 03:57:10', '2026-07-10 13:33:36'),
	(27, 27, 'SAK-096', 'Hyundai', 'eum', '2000', 'olive', NULL, NULL, 'gasolina', NULL, NULL, NULL, '2026-05-06 13:48:38', '2026-07-10 13:34:00'),
	(28, 28, 'LNO-668', 'Nissan', 'quibusdam', '2004', 'navy', NULL, NULL, 'gasolina', NULL, NULL, NULL, '2026-06-15 06:08:53', '2026-07-10 13:34:00'),
	(29, 29, 'GRM-837', 'Hyundai', 'voluptatem', '2024', 'blue', NULL, NULL, 'gasolina', NULL, NULL, NULL, '2026-07-05 01:49:23', '2026-07-10 13:34:00'),
	(30, 30, 'UUX-758', 'Kia', 'et', '2012', 'purple', NULL, NULL, 'gasolina', NULL, NULL, NULL, '2026-05-26 04:33:07', '2026-07-10 13:34:00'),
	(31, 31, 'AVQ-838', 'Toyota', 'pariatur', '2002', 'purple', NULL, NULL, 'gasolina', NULL, NULL, NULL, '2026-02-19 02:02:45', '2026-07-10 13:34:00'),
	(32, 32, 'UWX-830', 'Hyundai', 'inventore', '2016', 'navy', NULL, NULL, 'gasolina', NULL, NULL, NULL, '2026-05-28 05:30:03', '2026-07-10 13:34:00'),
	(33, 33, 'CUL-825', 'Kia', 'accusantium', '2021', 'olive', NULL, NULL, 'gasolina', NULL, NULL, NULL, '2026-01-10 17:05:30', '2026-07-10 13:34:00'),
	(34, 34, 'PAW-344', 'Nissan', 'eos', '2007', 'black', NULL, NULL, 'gasolina', NULL, NULL, NULL, '2026-06-18 05:39:37', '2026-07-10 13:34:00'),
	(35, 35, 'KAD-776', 'Honda', 'qui', '2007', 'black', NULL, NULL, 'gasolina', NULL, NULL, NULL, '2026-04-22 22:25:33', '2026-07-10 13:34:00'),
	(36, 36, 'HIO-619', 'Hyundai', 'omnis', '2015', 'white', NULL, NULL, 'gasolina', NULL, NULL, NULL, '2026-04-28 01:43:02', '2026-07-10 13:34:00');

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
