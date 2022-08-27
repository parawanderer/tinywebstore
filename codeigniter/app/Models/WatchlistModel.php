<?php

namespace App\Models;

use App\Helpers\MediaFile;
use CodeIgniter\Model;

class WatchlistModel extends Model
{
    protected $primaryKey = "id";
    protected $table = 'watchlist';
    protected $allowedFields = [
        'id',
        'user_id',
        'product_id',
        'product_name',
        'created',
    ];

    public function addToWatchlist(int $userId, int $productId, string $currentProductName) {
        if ($this->isWatched($userId, $productId)) return true;

        return $this->save([
            "user_id" => $userId,
            "product_id" => $productId,
            "product_name" => $currentProductName,
            "created" => date('Y-m-d H:i:s')
        ]);
    }

    public function isWatched(int $userId, int $productId) {
        return $this->where(["user_id" => $userId, "product_id" => $productId])->countAllResults() == 1;
    }

    public function removeFromWatchList(int $userId, int $productId) {
        return $this->where(["user_id" => $userId, "product_id" => $productId])->delete();
    }

    public function getUserWatchlist(int $userId) {
        $result = $this->select("watchlist.id, watchlist.user_id, watchlist.product_id, watchlist.product_name as fallback_title, watchlist.created, product.title, product.price, product.availability, product.main_media, shop_media.mimetype")
            ->join("product", "product.id = watchlist.product_id", "left")
            ->join("shop_media", "product.main_media = shop_media.id", "left")
            ->where(["user_id" => $userId])
            ->findAll();

        WatchlistModel::updateMediaInfo($result);
        return $result;
    }

    public function getUsersWatching(int $productId) {
        $userIds = $this->select("user_id")->where(["product_id" => $productId])->findAll();
        $result = [];

        foreach($userIds as &$userRecord) {
            $result[] = $userRecord['user_id'];
        }

        return $result;
    }

    private static function updateMediaInfo(array &$products) {
        foreach($products as &$product) {
            $product['media_thumbnail_id'] = $product['main_media'];
            $product['media_thumbnail_id_xs'] = null;
            $product['media_thumbnail_id_s'] = null;
            $product['media_thumbnail_id_m'] = null;
            $product['media_thumbnail_id_l'] = null;

            if ($product['main_media'] && $product['mimetype']) {
                $media = new MediaFile($product['main_media'], MediaFile::TYPE_MEDIA, $product['mimetype']);
                $product['media_thumbnail_id'] = $media->getThumbnailId();

                $product['media_thumbnail_id_xs'] = $media->getThumbnailId(MediaFile::SIZE_XS);
                $product['media_thumbnail_id_s'] = $media->getThumbnailId(MediaFile::SIZE_S);
                $product['media_thumbnail_id_m'] = $media->getThumbnailId(MediaFile::SIZE_M);
                $product['media_thumbnail_id_l'] = $media->getThumbnailId(MediaFile::SIZE_L);
            }
        }
    }
}