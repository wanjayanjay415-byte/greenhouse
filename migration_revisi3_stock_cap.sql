-- =====================================================
-- MIGRASI DATABASE: Revisi 3.0 — Cap Stok & COD Fix
-- GreenHouse Management System
-- =====================================================

-- LANGKAH 1: Reset stok yang melebihi 30 Kg ke nilai realistis
UPDATE `stock_inventories` SET `total_weight_kg` = 25.00 WHERE `total_weight_kg` > 30;

-- LANGKAH 2: Update status stok sesuai nilai baru
UPDATE `stock_inventories` SET `status` = 'ADA' WHERE `total_weight_kg` > 20;
UPDATE `stock_inventories` SET `status` = 'RENDAH' WHERE `total_weight_kg` <= 20 AND `total_weight_kg` > 0;
UPDATE `stock_inventories` SET `status` = 'KOSONG' WHERE `total_weight_kg` <= 0;

-- LANGKAH 3: Set payment_status = 'paid' untuk semua pesanan COD yang masih pending
UPDATE `orders` SET `payment_status` = 'paid' WHERE `payment_method` = 'cod' AND `payment_status` = 'pending';

-- LANGKAH 4: Verifikasi data
-- SELECT id, product_id, total_weight_kg, status FROM stock_inventories;
-- SELECT id, order_number, payment_method, payment_status FROM orders WHERE payment_method = 'cod';
