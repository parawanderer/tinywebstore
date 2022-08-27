<?php

namespace App\Models;

use App\Helpers\MediaFile;
use CodeIgniter\Model;
use Exception;

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

    public function getForProduct(int $productId, ?int $limit = 20) {
        $query = $this->where([
            "product_id" => $productId
        ]);
        $results = null;

        if ($limit !== null) {
            $results = $query->findAll($limit);
        } else {
            $results = $query->findAll();
        }

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
            $item['is_video'] = false;
            $item['thumbnail_id'] = $item['id'];
            $item['thumbnail_id_xs'] = null;
            $item['thumbnail_id_s'] = null;
            $item['thumbnail_id_m'] = null;
            $item['thumbnail_id_l'] = null;

            if ($item['id'] && $item['mimetype']) {
                $media = new MediaFile($item['id'], MediaFile::TYPE_MEDIA, $item['mimetype']);
                $item['is_video'] = $media->isVideoType();
                $item['thumbnail_id'] = $media->getThumbnailId();

                $item['thumbnail_id_xs'] = $media->getThumbnailId(MediaFile::SIZE_XS);
                $item['thumbnail_id_s'] = $media->getThumbnailId(MediaFile::SIZE_S);
                $item['thumbnail_id_m'] = $media->getThumbnailId(MediaFile::SIZE_M);
                $item['thumbnail_id_l'] = $media->getThumbnailId(MediaFile::SIZE_L);
            }
        }
    }

    public static function getThumbnailInfo(?string $mediaItemId, ?string $mimeType) {
        if (!$mediaItemId || !$mimeType) return [ false,  null ];

        $media = new MediaFile($mediaItemId, MediaFile::TYPE_MEDIA, $mimeType);

        return [ $media->isVideoType(), $media->getThumbnailId() ];
    }
}