<?php

namespace App\Models;

use CodeIgniter\Model;

class ProductModel extends Model
{
    protected $primaryKey = "id";
    protected $table = 'product';
    protected $allowedFields = [
        'id',
        'shop_id',
        'title',
        'price',
        'availability',
        'main_media'
    ];

    public function getForShop(int $shopId, int $limit = 200) {
        return $this->where(["shop_id" => $shopId])->findAll($limit);
    }

    public function getById(int $productId) {
        return $this->where(["id" => $productId])->first();
    }
}