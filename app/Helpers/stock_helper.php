<?php

/**
 * Helper status stok terpusat.
 * Ambang batas RENDAH (low stock) default 20 Kg.
 */

if (!defined('STOCK_LOW_THRESHOLD')) {
    define('STOCK_LOW_THRESHOLD', 20);
}

if (!defined('MAX_STOCK_PER_PRODUCT')) {
    define('MAX_STOCK_PER_PRODUCT', 30);
}

if (!function_exists('stock_status')) {
    /**
     * Tentukan status stok berdasarkan berat (Kg).
     * <= 0       => KOSONG
     * <= ambang  => RENDAH
     * selebihnya => ADA
     */
    function stock_status(float $weightKg): string
    {
        if ($weightKg <= 0) {
            return 'KOSONG';
        }
        if ($weightKg <= STOCK_LOW_THRESHOLD) {
            return 'RENDAH';
        }
        return 'ADA';
    }
}
