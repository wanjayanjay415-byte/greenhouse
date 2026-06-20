<?php

namespace App\Models;

use CodeIgniter\Model;

class OrderStatusHistoryModel extends Model
{
    protected $table            = 'order_status_history';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['order_id', 'old_status', 'new_status', 'changed_by', 'changed_at'];

    protected $useTimestamps = false;

    /**
     * Ambil histori perubahan status untuk satu pesanan
     */
    public function getHistoryByOrderId(int $orderId): array
    {
        return $this->select('order_status_history.*, users.full_name as changed_by_name')
                    ->join('users', 'users.id = order_status_history.changed_by', 'left')
                    ->where('order_id', $orderId)
                    ->orderBy('changed_at', 'ASC')
                    ->findAll();
    }

    /**
     * Catat perubahan status
     */
    public function logStatusChange(int $orderId, ?string $oldStatus, string $newStatus, ?int $changedBy = null): void
    {
        $this->insert([
            'order_id'   => $orderId,
            'old_status' => $oldStatus,
            'new_status' => $newStatus,
            'changed_by' => $changedBy,
            'changed_at' => date('Y-m-d H:i:s'),
        ]);
    }
}
