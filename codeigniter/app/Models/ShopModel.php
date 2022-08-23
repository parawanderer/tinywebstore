<?php

namespace App\Models;

use CodeIgniter\Model;

class ShopModel extends Model
{
    public const SHOP_LOGO_PATH = "shop/logo/";
    public const SHOP_BANNER_PATH = "shop/banner/";

    protected $primaryKey = "id";
    protected $table = 'shop';
    protected $allowedFields = [
        'id',
        'user_id',
        'name',
        'description',
        'address',
        'phone_number',
        'support_email',
        'shop_logo_img',
        'shop_banner_img',
        'theme_color',
        'font_color'
    ];

    public function getShop(int $shopId) {
        return $this->where(["id" => $shopId])->first();
    }
}