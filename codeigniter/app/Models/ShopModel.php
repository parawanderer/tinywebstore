<?php

namespace App\Models;

use App\Helpers\MediaFile;
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
        $shop = $this->where(["id" => $shopId])->first();
        ShopModel::extendShopMedia($shop);

        return $shop;
    }

    public function getShops(array $shopIds) {
        $shop = $this->whereIn("id", $shopIds)->findAll();
        ShopModel::extendShopMedia($shop);

        return $shop;
    }

    public static function extendShopMedia(array &$shop) {
        if (!$shop || empty($shop)) return;
        if (!$shop['shop_logo_img']) return;

        $media = new MediaFile($shop['shop_logo_img'], MediaFile::TYPE_LOGO, null);
        $thumbnails = $media->getThumbnails();


        $shop['shop_logo_img_l'] = $thumbnails['l'];
        $shop['shop_logo_img_m'] = $thumbnails['m'];
        $shop['shop_logo_img_s'] = $thumbnails['s'];
        $shop['shop_logo_img_xs'] = $thumbnails['xs'];
    }
}