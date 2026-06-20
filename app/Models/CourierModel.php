<?php

namespace App\Models;

use CodeIgniter\Model;

class CourierModel extends Model
{
    protected $table            = 'couriers';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['name', 'phone', 'address', 'region', 'status', 'created_at', 'updated_at'];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    /**
     * Ambil kurir aktif yang wilayahnya cocok dengan alamat pesanan (case-insensitive, partial match).
     * Dipakai untuk menyarankan kurir di halaman kelola pesanan.
     */
    public function suggestByAddress(?string $address): array
    {
        $address = trim((string) $address);
        if ($address === '') {
            return [];
        }

        $couriers = $this->where('status', 'active')->findAll();
        $matches  = [];
        $lowerAddr = mb_strtolower($address);

        foreach ($couriers as $c) {
            $region = mb_strtolower(trim($c['region'] ?? ''));
            if ($region !== '' && str_contains($lowerAddr, $region)) {
                $matches[] = $c;
            }
        }

        return $matches;
    }
}
