-- =====================================================
-- MIGRASI DATABASE: Dokumen AKS Revisi 2.0
-- GreenHouse Management System
-- =====================================================

-- LANGKAH 1: Ubah tipe kolom ke VARCHAR dulu agar bisa update bebas
ALTER TABLE `orders` MODIFY COLUMN `logistic_status` VARCHAR(50) NOT NULL DEFAULT 'Menunggu Konfirmasi';

-- LANGKAH 2: Update data lama ke status baru (HARUS SEBELUM enum diubah)
UPDATE `orders` SET `logistic_status` = 'Menunggu Konfirmasi' WHERE `logistic_status` = 'Pesanan Masuk';
UPDATE `orders` SET `logistic_status` = 'Dikonfirmasi' WHERE `logistic_status` = 'Proses Sortir';
UPDATE `orders` SET `logistic_status` = 'Dalam Pengiriman' WHERE `logistic_status` = 'Pengiriman';
UPDATE `orders` SET `logistic_status` = 'Selesai' WHERE `logistic_status` = 'Diterima';

-- LANGKAH 3: Sekarang aman ubah ke enum baru (semua data sudah cocok)
ALTER TABLE `orders` 
MODIFY COLUMN `logistic_status` enum(
    'Menunggu Konfirmasi',
    'Dikonfirmasi',
    'Sedang Dipanen',
    'Dalam Pengiriman',
    'Selesai',
    'Dibatalkan'
) NOT NULL DEFAULT 'Menunggu Konfirmasi';

-- LANGKAH 4: Tambah kolom metode pembayaran (COD / Transfer Bank)
ALTER TABLE `orders`
ADD COLUMN `payment_method` enum('cod','transfer') NOT NULL DEFAULT 'transfer' AFTER `payment_status`;

-- LANGKAH 5: Tambah kolom estimasi panen pada stok
ALTER TABLE `stock_inventories`
ADD COLUMN `estimated_harvest` varchar(100) DEFAULT NULL AFTER `status`;

-- LANGKAH 6: Buat tabel histori perubahan status pesanan
CREATE TABLE IF NOT EXISTS `order_status_history` (
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

-- LANGKAH 7: Set payment_method default 'transfer' untuk data lama yang punya bukti bayar
UPDATE `orders` SET `payment_method` = 'transfer' WHERE `payment_proof` IS NOT NULL;
