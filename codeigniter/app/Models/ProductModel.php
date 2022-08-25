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
        $result = $this->select("product.id, product.shop_id, product.title, product.price, product.availability, product.main_media, product.description, shop_media.mimetype as media_mimetype, shop.name as shop_name, shop.id as shop_id")
                ->join('shop_media', 'product.main_media = shop_media.id', 'left')
                ->join('shop', 'product.shop_id = shop.id')
                ->whereIn("product.id", $itemIds)
                ->findAll();
        
        ProductModel::updateMediaInfo($result);

        return $result;
    }

    public function getProductsByIdsForUpdate(array $itemIds) {
        $sql = "SELECT product.id, product.shop_id, product.title, product.price, product.availability, product.main_media, product.description 
            FROM product WHERE product.id IN :itemIds: FOR UPDATE;";
        
        /** @var \CodeIgniter\Database\BaseResult */
        $query = $this->db->query($sql, [
            "itemIds" => $itemIds
        ]);
        $results = $query->getResultArray();

        return $results;
    }

    public function updateProductAvailabilities(array &$productIdToAvailability) {
        foreach($productIdToAvailability as $productId => $newAvailability) {
            $this->update($productId, [
                "availability" => $newAvailability
            ]);
        }
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