<?php

namespace App\Models;

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

    private static function updateMediaInfo(array &$products) {
        foreach($products as &$product) {
            [ $isVideo, $thumbnailId ] = ShopMediaModel::getThumbnailInfo($product['main_media'], $product['mimetype']);

            $product['media_thumbnail_id'] = $thumbnailId ?? $product['main_media'];
        }
    }
}