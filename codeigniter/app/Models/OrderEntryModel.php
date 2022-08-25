<?php

namespace App\Models;

use CodeIgniter\Model;

class OrderEntryModel extends Model
{

    protected $primaryKey = "id";
    protected $table = 'order_entry';
    protected $allowedFields = [
        'id',
        'order_id',
        'product_id',
        'shop_id',
        'quantity',
        'price_per_unit',
        'product_name',
        'completed'
    ];

    public function getEntriesForOrder(int $orderId) {
        $result = $this->select("order_entry.id, order_entry.product_id, order_entry.shop_id, order_entry.quantity, order_entry.price_per_unit, order_entry.completed, order_entry.product_name as product_title_backup, product.title as product_title, product.main_media as product_media, shop_media.mimetype as product_media_mimetype, shop.name as shop_name")
            ->join("product", "order_entry.product_id = product.id", "left")
            ->join("shop_media", "product.main_media = shop_media.id")
            ->join("shop", "order_entry.shop_id = shop.id")
            ->where([ "order_id" => $orderId])
            ->findAll();

        OrderEntryModel::updateProductDetails($result);

        return $result;
    }

    public function addNewEntry(int $orderId, int $productId, int $shopId, int $quantity, float $pricePerUnit, string $productName) {
        $result = $this->insert([
            "order_id" => $orderId,
            "product_id" => $productId,
            "shop_id" => $shopId,
            "quantity" => $quantity,
            "price_per_unit" => $pricePerUnit,
            "product_name" => $productName
        ]);

        return $result;
    }

    public function completeEntries(array $entryIds) {
        return $this->set('completed', 1)->whereIn("id", $entryIds)->update();
    }

    private static function updateProductDetails(array &$products) {
        foreach($products as &$product) {
            [ $isVideo, $thumbnailId ] = ShopMediaModel::getThumbnailInfo($product['product_media'], $product['product_media_mimetype']);

            $product['media_thumbnail_id'] = $thumbnailId ?? $product['product_media'];
            $product['is_deleted'] = !$product['product_title'];
        }
    } 
}