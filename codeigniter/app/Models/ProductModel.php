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
        $result = $this->select("product.id, product.shop_id, product.title, product.price, product.availability, product.main_media, product.description, shop_media.mimetype as media_mimetype")
            ->join('shop_media', 'product.main_media = shop_media.id', 'left')
            ->where(["product.shop_id" => $shopId])
            ->findAll($limit);

        ProductModel::updateMediaInfo($result);

        return $result;
    }

    public function getForShopExcluding(int $shopId, array $excludeIds, int $limit = 200) {
        $result = $this->select("product.id, product.shop_id, product.title, product.price, product.availability, product.main_media, product.description, shop_media.mimetype as media_mimetype")
                ->join('shop_media', 'product.main_media = shop_media.id', 'left')
                ->where(["product.shop_id" => $shopId])
                ->whereNotIn("product.id", $excludeIds)
                ->findAll($limit);
        
        ProductModel::updateMediaInfo($result);

        return $result;
    }

    public function getProductsByIds(array $itemIds) {
        $result = $this->select("product.id, product.shop_id, product.title, product.price, product.availability, product.main_media, product.description, shop_media.mimetype as media_mimetype")
                ->join('shop_media', 'product.main_media = shop_media.id', 'left')
                ->whereIn("product.id", $itemIds)
                ->findAll();
        
        ProductModel::updateMediaInfo($result);

        return $result;
    }

    public function getById(int $productId) {
        return $this->where(["id" => $productId])->first();
    }

    public function deleteCascase(int $productId) {
        // delete media items for product
        $this->db->table("shop_media")->delete(["product_id" => $productId]);
        return $this->delete($productId);
    }

    private static function updateMediaInfo(array &$products) {
        foreach($products as &$product) {
            [ $isVideo, $thumbnailId ] = ShopMediaModel::getThumbnailInfo($product['main_media'], $product['media_mimetype']);

            $product['media_thumbnail_id'] = $thumbnailId ?? $product['main_media'];
        }
    }
}