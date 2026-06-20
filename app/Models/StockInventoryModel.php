<?php

namespace App\Models;

use CodeIgniter\Model;

class StockInventoryModel extends Model
{
    protected $table            = 'stock_inventories';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['product_id', 'total_weight_kg', 'grade', 'status', 'estimated_harvest', 'last_updated'];

    // Kita buat logic join agar mudah ditarik di UI
    public function getStockWithProducts()
    {
        return $this->select('stock_inventories.*, products.name, products.category, products.image_path, products.sku, products.price_per_kg')
                    ->join('products', 'products.id = stock_inventories.product_id')
                    ->findAll();
    }
}
