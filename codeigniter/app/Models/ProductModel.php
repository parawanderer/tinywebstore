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
        'main_media',
        'description'
    ];

    public function getForShop(int $shopId, int $limit = 200) {
        return $this->where(["shop_id" => $shopId])->findAll($limit);
    }

    public function getForShopExcluding(int $shopId, array $excludeIds, int $limit = 200) {
        return $this->where(["shop_id" => $shopId])->whereNotIn("id", $excludeIds)->findAll($limit);
    }

    public function getById(int $productId) {
        return $this->where(["id" => $productId])->first();
    }
}