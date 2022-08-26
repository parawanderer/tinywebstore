<?php

namespace App\Models;

use CodeIgniter\Model;
use Exception;

class ProductModel extends Model
{
    public const SORT_ALPHABETICAL = 0;
    public const SORT_PRICE_ASC = 1;
    public const SORT_PRICE_DESC = 2;
    public const SORT_REVIEW_DESC = 3;

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

    public function getNewest(int $limit = 8) {
        $result = $this->select("product.id, product.shop_id, product.title, product.price, product.availability, product.main_media, product.description, shop_media.mimetype as media_mimetype")
            ->join('shop_media', 'product.main_media = shop_media.id', 'left')
            ->orderBy("product.id", "desc")
            ->findAll($limit);

        ProductModel::updateMediaInfo($result);
        return $result;
    }

    public function nameSearch(string $titleSubstring, int $limit = 8) {
        $result = $this->select("product.id, product.shop_id, product.title, product.price, product.availability, product.main_media, product.description, shop_media.mimetype as media_mimetype")
            ->join('shop_media', 'product.main_media = shop_media.id', 'left')
            ->like('product.title', $titleSubstring)
            ->findAll($limit);

        ProductModel::updateMediaInfo($result);
        return $result;
    }

    public function nameSearchExtended(string $titleSubstring, int $limit = 8, array $options = []) {
        $binds = [
            "lim" => $limit,
            "search" => "%$titleSubstring%"
        ];

        $sql = "SELECT p.id, p.shop_id, p.title, p.price, p.availability, p.main_media, p.`description`, 
                s.`name` as shop_name, m.mimetype as media_mimetype, SUM(r.rating) / COUNT(r.rating) as `avg_rating`, COUNT(r.rating) as `rating_count`
                FROM product p
                LEFT JOIN shop_media m ON p.main_media = m.id
                INNER JOIN shop s ON p.shop_id = s.id
                LEFT JOIN review r ON p.id = r.product_id
                WHERE p.title LIKE :search: ";

        if ($options['mustBeInStock']) {
            $sql .= " AND p.availability > 0 ";
        }
        if ($options['costMin'] != -1) {
            $sql .= " AND price >= :costMin: ";
            $binds['costMin'] = $options['costMin'];
        }
        if ($options['costMax'] != -1) {
            $sql .= " AND price <= :costMax: ";
            $binds['costMax'] = $options['costMax'];
        }

        $sql .= " GROUP BY r.product_id, p.id ";
        
        if ($options['reviewCount'] != -1 || $options['reviewScoreMin'] != -1) {
            $sql .= " HAVING ";
            $needsAnd = false;

            if ($options['reviewCount'] != -1) {
                $sql .= " `rating_count` >= :reviewCount: ";
                $binds['reviewCount'] = $options['reviewCount'];
                $needsAnd = true;
            }
            if ($options['reviewScoreMin'] != -1) {
                if ($needsAnd) $sql .= ' AND ';
                $sql .= " `avg_rating` >= :reviewScoreMin: ";
                $binds['reviewScoreMin'] = $options['reviewScoreMin'];
                $needsAnd = true;
            }
        }

        switch(intval($options['sortOption'])) {
            case ProductModel::SORT_PRICE_ASC:
                $sql .= " ORDER BY p.price ASC ";
                break;
            case ProductModel::SORT_PRICE_DESC:
                $sql .= " ORDER BY p.price DESC ";
                break;
            case ProductModel::SORT_REVIEW_DESC:
                $sql .= " ORDER BY avg_rating DESC ";
                break;
            case ProductModel::SORT_ALPHABETICAL: default:
                $sql .= " ORDER BY p.title ASC ";
                break;
        }

        // this is inefficient, but I'm too tired to write something better. It'll do for this toy project
        $counterSQL =  "SELECT COUNT(*) as `count` FROM (" . $sql . ") AS t;";

        $sql .= " LIMIT :lim:";

        $query = $this->db->query($sql, $binds);
        $result = $query->getResultArray();

        $resultsTotalCount = $this->db->query($counterSQL, $binds)->getFirstRow('array')["count"] ?? 0;
        
        ProductModel::updateMediaInfo($result);
        return [$result, $resultsTotalCount];
    }

    public function countTotalNameSearchMatches(string $titleSubstring) {
        $result = $this->select("*")->like('title', $titleSubstring)->countAllResults();
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