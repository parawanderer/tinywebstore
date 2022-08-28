<?php

namespace App\Models;

use App\Helpers\MediaFile;
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
            ->join("shop_media", "product.main_media = shop_media.id", "left")
            ->join("shop", "order_entry.shop_id = shop.id", "left")
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
            $product['is_deleted'] = !$product['product_title'];
            $product['media_thumbnail_id'] = $product['product_media'];
            $product['media_thumbnail_id_xs'] = null;
            $product['media_thumbnail_id_s'] = null;
            $product['media_thumbnail_id_m'] = null;
            $product['media_thumbnail_id_l'] = null;

            if ($product['product_title'] && $product['product_media']) {
                $media = new MediaFile($product['product_media'], MediaFile::TYPE_MEDIA, $product['product_media_mimetype']);
                $product['media_thumbnail_id'] = $media->getThumbnailId();

                $product['media_thumbnail_id_xs'] = $media->getThumbnailId(MediaFile::SIZE_XS);
                $product['media_thumbnail_id_s'] = $media->getThumbnailId(MediaFile::SIZE_S);
                $product['media_thumbnail_id_m'] = $media->getThumbnailId(MediaFile::SIZE_M);
                $product['media_thumbnail_id_l'] = $media->getThumbnailId(MediaFile::SIZE_L);
            }
        }
    } 
}