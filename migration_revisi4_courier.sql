-- =====================================================
-- MIGRASI DATABASE: Revisi 4.0 — Kelola Kurir & Resi
-- GreenHouse Management System
-- Jalankan via phpMyAdmin atau:
--   /opt/lampp/bin/mysql -uroot db_greenhouse < migration_revisi4_courier.sql
-- =====================================================

-- LANGKAH 1: Tabel master kurir/driver
CREATE TABLE IF NOT EXISTS `couriers` (
    `id`          INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `name`        VARCHAR(100) NOT NULL,
    `phone`       VARCHAR(25)  DEFAULT NULL,
    `address`     VARCHAR(255) DEFAULT NULL,
    `region`      VARCHAR(100) NOT NULL,           -- wilayah tangguhan kurir (mis. "Bekasi Timur")
    `status`      ENUM('active','inactive') NOT NULL DEFAULT 'active',
    `created_at`  DATETIME DEFAULT NULL,
    `updated_at`  DATETIME DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `idx_courier_region` (`region`),
    KEY `idx_courier_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- LANGKAH 2: Kolom penugasan kurir & resi pada tabel orders
ALTER TABLE `orders`
    ADD COLUMN `courier_id`      INT(11) UNSIGNED DEFAULT NULL AFTER `payment_proof`,
    ADD COLUMN `tracking_number` VARCHAR(50)      DEFAULT NULL AFTER `courier_id`;

-- LANGKAH 3: Index + foreign key kurir (SET NULL bila kurir dihapus)
ALTER TABLE `orders`
    ADD KEY `idx_order_courier` (`courier_id`),
    ADD CONSTRAINT `fk_order_courier` FOREIGN KEY (`courier_id`)
        REFERENCES `couriers`(`id`) ON DELETE SET NULL ON UPDATE CASCADE;

-- LANGKAH 4: Data contoh kurir (opsional — boleh dihapus)
INSERT INTO `couriers` (`name`, `phone`, `address`, `region`, `status`, `created_at`, `updated_at`) VALUES
('Budi Santoso',  '081234567001', 'Jl. Mawar No. 1', 'Bekasi Timur', 'active', NOW(), NOW()),
('Andi Wijaya',   '081234567002', 'Jl. Melati No. 2', 'Bekasi Barat', 'active', NOW(), NOW());
