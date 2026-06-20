-- =====================================================
-- SCHEMA FINAL — GreenHouse Management System
-- AKS Revisi 2.0 (gabungan schema + migrasi, sudah sinkron dgn code)
-- Import file INI SAJA. Tidak perlu db_greenhouse.sql / migration_revisi2.sql lagi.
-- =====================================================

CREATE DATABASE IF NOT EXISTS `db_greenhouse` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `db_greenhouse`;

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";
SET NAMES utf8mb4;

-- Drop dgn urutan aman (child dulu)
SET FOREIGN_KEY_CHECKS = 0;
DROP TABLE IF EXISTS `order_status_history`;
DROP TABLE IF EXISTS `order_items`;
DROP TABLE IF EXISTS `orders`;
DROP TABLE IF EXISTS `couriers`;
DROP TABLE IF EXISTS `harvest_logs`;
DROP TABLE IF EXISTS `stock_inventories`;
DROP TABLE IF EXISTS `reports`;
DROP TABLE IF EXISTS `products`;
DROP TABLE IF EXISTS `users`;
SET FOREIGN_KEY_CHECKS = 1;

-- =====================================================
-- USERS
-- =====================================================
CREATE TABLE `users` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `full_name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `profile_picture` varchar(255) DEFAULT NULL,
  `two_factor_enabled` tinyint(1) NOT NULL DEFAULT 0,
  `password_hash` varchar(255) NOT NULL,
  `role` enum('customer','manager','owner','admin') NOT NULL DEFAULT 'customer',
  `status` enum('active','offline','suspended') NOT NULL DEFAULT 'active',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Password default semua akun di bawah: "admin123" (ganti setelah login!)
INSERT INTO `users` (`id`, `full_name`, `email`, `phone`, `password_hash`, `role`, `status`, `created_at`, `updated_at`) VALUES
(1, 'Bapak Agung', 'agung@manager.com', '081234567890', '$2y$12$4Vc1/xR9KDk4iHO0Fm3AeuH7M.s2fGPp80x2Wz06sIW9uKjYYAybq', 'manager', 'active', '2026-04-21 08:00:00', '2026-04-21 08:00:00'),
(2, 'Siti Nurbaya', 'siti.nurbaya@greenhouse.com', '081298765432', '$2y$12$4Vc1/xR9KDk4iHO0Fm3AeuH7M.s2fGPp80x2Wz06sIW9uKjYYAybq', 'manager', 'active', '2026-04-21 08:00:00', '2026-04-21 08:00:00'),
(3, 'Taufik', 'taufik@admin.com', '081311112222', '$2y$12$4Vc1/xR9KDk4iHO0Fm3AeuH7M.s2fGPp80x2Wz06sIW9uKjYYAybq', 'owner', 'active', '2026-04-21 08:00:00', '2026-04-21 08:00:00'),
(5, 'Pelanggan Demo', 'customer@greenhouse.com', '081300000000', '$2y$12$4Vc1/xR9KDk4iHO0Fm3AeuH7M.s2fGPp80x2Wz06sIW9uKjYYAybq', 'customer', 'active', '2026-04-21 08:00:00', '2026-04-21 08:00:00');

-- =====================================================
-- PRODUCTS
-- =====================================================
CREATE TABLE `products` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(150) NOT NULL,
  `category` varchar(100) DEFAULT NULL,
  `sku` varchar(50) DEFAULT NULL,
  `image_path` varchar(255) DEFAULT NULL,
  `price_per_kg` decimal(10,2) NOT NULL DEFAULT 0.00,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `sku` (`sku`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `products` (`id`, `name`, `category`, `sku`, `image_path`, `price_per_kg`, `created_at`, `updated_at`) VALUES
(1, 'Bayam Organik Super', 'Sayur Daun', 'BYM-ORG-101', 'bayam.jpg', 15000.00, '2026-04-21 08:00:00', '2026-04-21 08:00:00'),
(2, 'Kangkung Hidroponik', 'Sayur Daun', 'KKG-HDR-202', 'kangkung.jpg', 12000.00, '2026-04-21 08:00:00', '2026-04-21 08:00:00'),
(3, 'Selada Keriting Premium', 'Sayur Daun', 'SLD-KRT-303', 'selada.jpg', 25000.00, '2026-04-21 08:00:00', '2026-04-21 08:00:00');

-- =====================================================
-- STOCK INVENTORIES (dgn estimated_harvest)
-- =====================================================
CREATE TABLE `stock_inventories` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `product_id` int(11) UNSIGNED NOT NULL,
  `total_weight_kg` decimal(10,2) NOT NULL DEFAULT 0.00,
  `grade` enum('A','B','C') NOT NULL DEFAULT 'B',
  `status` enum('ADA','KOSONG','RENDAH') NOT NULL DEFAULT 'ADA',
  `estimated_harvest` varchar(100) DEFAULT NULL,
  `last_updated` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `stock_inventories_product_id_foreign` (`product_id`),
  CONSTRAINT `stock_inventories_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `stock_inventories` (`id`, `product_id`, `total_weight_kg`, `grade`, `status`, `estimated_harvest`, `last_updated`) VALUES
(1, 1, 1240.00, 'A', 'ADA', NULL, '2026-04-21 09:30:00'),
(2, 2, 850.00, 'A', 'ADA', NULL, '2026-04-21 09:30:00'),
(3, 3, 2100.00, 'B', 'ADA', NULL, '2026-04-21 09:30:00');

-- =====================================================
-- HARVEST LOGS
-- =====================================================
CREATE TABLE `harvest_logs` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `worker_id` int(11) UNSIGNED NOT NULL,
  `product_id` int(11) UNSIGNED NOT NULL,
  `yield_kg` decimal(10,2) NOT NULL DEFAULT 0.00,
  `grade` enum('A','B','C') NOT NULL DEFAULT 'A',
  `harvest_date` date DEFAULT NULL,
  `verification_status` enum('pending','invoiced','sold') NOT NULL DEFAULT 'pending',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `harvest_logs_worker_id_foreign` (`worker_id`),
  KEY `harvest_logs_product_id_foreign` (`product_id`),
  CONSTRAINT `harvest_logs_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `harvest_logs_worker_id_foreign` FOREIGN KEY (`worker_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `harvest_logs` (`id`, `worker_id`, `product_id`, `yield_kg`, `grade`, `harvest_date`, `verification_status`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 150.50, 'A', '2026-04-20', 'invoiced', '2026-04-20 10:00:00', '2026-04-20 10:00:00'),
(2, 2, 2, 200.00, 'B', '2026-04-21', 'pending', '2026-04-21 09:15:00', '2026-04-21 09:15:00'),
(3, 1, 3, 120.00, 'A', '2026-04-21', 'pending', '2026-04-21 11:30:00', '2026-04-21 11:30:00');

-- =====================================================
-- COURIERS (Kurir/Logistik)
-- =====================================================
CREATE TABLE `couriers` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `region` varchar(100) NOT NULL,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_courier_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `couriers` (`id`,`name`,`phone`,`region`,`status`,`created_at`,`updated_at`) VALUES
(1,'Budi Kurniawan','081234500001','Jakarta','active',NOW(),NOW()),
(2,'Andi Pratama','081234500002','Bandung','active',NOW(),NOW()),
(3,'Slamet Riyadi','081234500003','Bogor','active',NOW(),NOW());

-- =====================================================
-- ORDERS (enum status AKS 2.0 + payment_method + payment_proof + courier)
-- =====================================================
CREATE TABLE `orders` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `order_number` varchar(50) NOT NULL,
  `customer_id` int(11) UNSIGNED NOT NULL,
  `total_amount` decimal(15,2) NOT NULL DEFAULT 0.00,
  `delivery_address` text DEFAULT NULL,
  `logistic_status` enum('Menunggu Konfirmasi','Dikonfirmasi','Sedang Dipanen','Dalam Pengiriman','Selesai','Dibatalkan') NOT NULL DEFAULT 'Menunggu Konfirmasi',
  `payment_status` enum('pending','paid','failed') NOT NULL DEFAULT 'pending',
  `payment_method` enum('cod','transfer') NOT NULL DEFAULT 'transfer',
  `payment_proof` varchar(255) DEFAULT NULL,
  `courier_id` int(11) UNSIGNED DEFAULT NULL,
  `tracking_number` varchar(50) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `order_number` (`order_number`),
  KEY `orders_customer_id_foreign` (`customer_id`),
  KEY `fk_orders_courier` (`courier_id`),
  CONSTRAINT `orders_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_orders_courier` FOREIGN KEY (`courier_id`) REFERENCES `couriers` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `orders` (`id`, `order_number`, `customer_id`, `total_amount`, `delivery_address`, `logistic_status`, `payment_status`, `payment_method`, `payment_proof`, `created_at`, `updated_at`) VALUES
(1, 'ORD-20260421-0001', 5, 45000.00, 'Jl. Sudirman No 12, Jakarta', 'Dalam Pengiriman', 'paid', 'transfer', NULL, '2026-04-21 08:30:00', '2026-04-21 14:00:00'),
(2, 'ORD-20260421-0002', 5, 85000.00, 'Koperasi Tani Mandiri, Bandung', 'Menunggu Konfirmasi', 'pending', 'cod', NULL, '2026-04-21 09:45:00', '2026-04-21 09:45:00');

-- =====================================================
-- ORDER ITEMS
-- =====================================================
CREATE TABLE `order_items` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `order_id` int(11) UNSIGNED NOT NULL,
  `product_id` int(11) UNSIGNED NOT NULL,
  `qty` int(11) NOT NULL DEFAULT 1,
  `subtotal` decimal(15,2) NOT NULL DEFAULT 0.00,
  PRIMARY KEY (`id`),
  KEY `order_items_order_id_foreign` (`order_id`),
  KEY `order_items_product_id_foreign` (`product_id`),
  CONSTRAINT `order_items_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `order_items_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `qty`, `subtotal`) VALUES
(1, 1, 1, 3, 45000.00),
(2, 2, 2, 5, 60000.00),
(3, 2, 3, 1, 25000.00);

-- =====================================================
-- ORDER STATUS HISTORY (AKS 2.0)
-- =====================================================
CREATE TABLE `order_status_history` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `order_id` int(11) UNSIGNED NOT NULL,
  `old_status` varchar(50) DEFAULT NULL,
  `new_status` varchar(50) NOT NULL,
  `changed_by` int(11) UNSIGNED DEFAULT NULL,
  `changed_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_order_id` (`order_id`),
  CONSTRAINT `fk_osh_order` FOREIGN KEY (`order_id`) REFERENCES `orders`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- =====================================================
-- REPORTS
-- =====================================================
CREATE TABLE `reports` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `report_type` enum('panen','keuangan','distribusi','umum') NOT NULL DEFAULT 'umum',
  `title` varchar(255) NOT NULL,
  `period_start` date NOT NULL,
  `period_end` date NOT NULL,
  `content` text NOT NULL,
  `status` enum('submitted','reviewed','approved') NOT NULL DEFAULT 'submitted',
  `created_by` int(11) UNSIGNED DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
