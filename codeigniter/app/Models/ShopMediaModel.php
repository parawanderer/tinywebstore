<?php

namespace App\Models;

use CodeIgniter\Model;

class ShopMediaModel extends Model
{
    public const SHOP_MEDIA_PATH = "shop/media/";

    protected $primaryKey = "id";
    protected $table = 'shop_media';
    protected $allowedFields = [
        'id',
        'shop_id',
        'mimetype',
        'created',
        "product_id",
    ];

    public function getForShop(int $shopId) {
        $results = $this->where([
            "shop_id" => $shopId, 
            "product_id" => null
            ])
            ->findAll(20);

        ShopMediaModel::updateExtraInfo($results);
        return $results;
    }

    public function getForProduct(int $productId, int $limit = 20) {
        $results = $this->where([
            "product_id" => $productId
            ])
            ->findAll($limit);

        ShopMediaModel::updateExtraInfo($results);
        return $results;
    }

    public function getOneForProduct(int $productId) {
        $result = $this->getForProduct($productId, 1);

        if (count($result) > 0) {
            return $result[0];
        }
        
        return null;
    }

    public function getById(string $mediaId) {
        return $this->where(["id" => $mediaId])->first();
    }

    public function deleteById(string $mediaId) {
        return $this->delete($mediaId);
    }

    public function addForShop(string $mediaId, string $mimeType, int $shopId, int $productId = null) {
        return $this->insert([
            "id" => $mediaId,
            "mimetype" => $mimeType,
            "shop_id" => $shopId,
            "product_id" => $productId,
            "created" => date('Y-m-d H:i:s')
        ]);
    }

    private static function updateExtraInfo(array &$mediaItems) {
        foreach ($mediaItems as &$item) {
            [$isVideo, $thumbnailId] = ShopMediaModel::getThumbnailInfo($item['id'], $item['mimetype']);

            $item['is_video'] = $isVideo;
            $item['thumbnail_id'] = $thumbnailId;
        }
    }

    public static function getThumbnailInfo(?string $mediaItemId, ?string $mimeType) {
        $isVideo = $mimeType === 'video/mp4'; // only mp4 supported in this project

        $isVideo = false;
        $thumbnailId = null;

        if ($isVideo) {
            $isVideo = true;
            $thumbnailId = substr($mediaItemId, 0, strrpos($mediaItemId, ".")) . ".jpg";
        }

        return [ $isVideo, $thumbnailId ];
    }
}