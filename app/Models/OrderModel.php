<?php

namespace App\Models;

use CodeIgniter\Model;

class OrderModel extends Model
{
    protected $table            = 'orders';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'order_number', 'customer_id', 'total_amount', 'delivery_address',
        'logistic_status', 'payment_status', 'payment_method', 'payment_proof',
        'courier_id', 'tracking_number',
        'created_at', 'updated_at'
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    public function getOrdersWithUser()
    {
        return $this->select('orders.*, users.full_name, users.phone, users.email')
                    ->join('users', 'users.id = orders.customer_id')
                    ->orderBy('orders.created_at', 'DESC')
                    ->findAll();
    }

    /**
     * Ambil pesanan 1 bulan terakhir (default) dengan data user
     */
    public function getRecentOrdersWithUser($months = 1)
    {
        $cutoff = date('Y-m-d', strtotime("-{$months} months"));
        return $this->select('orders.*, users.full_name, users.phone, users.email, couriers.name as courier_name, couriers.region as courier_region')
                    ->join('users', 'users.id = orders.customer_id')
                    ->join('couriers', 'couriers.id = orders.courier_id', 'left')
                    ->where('DATE(orders.created_at) >=', $cutoff)
                    ->orderBy('orders.created_at', 'DESC')
                    ->findAll();
    }

    /**
     * Hitung total berat pesanan hari ini (kg) — untuk kapasitas kurir
     */
    public function getTodayTotalWeight(): float
    {
        $db = \Config\Database::connect();
        $result = $db->query("
            SELECT COALESCE(SUM(oi.qty), 0) as total_kg
            FROM orders o
            JOIN order_items oi ON oi.order_id = o.id
            WHERE DATE(o.created_at) = CURDATE()
            AND o.logistic_status != 'Dibatalkan'
        ")->getRow();
        return (float)($result->total_kg ?? 0);
    }
}
