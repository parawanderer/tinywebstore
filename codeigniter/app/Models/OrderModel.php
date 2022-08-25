<?php

namespace App\Models;

use CodeIgniter\Model;
use Exception;

class OrderModel extends Model
{
    public const TYPE_DELIVERY = 0;
    public const TYPE_PICKUP = 1;

    public const STATUS_PENDING = 0;
    public const STATUS_COMPLETE = 1;
    public const STATUS_CANCELLED = 2;

    protected $primaryKey = "id";
    protected $table = 'order';
    // not going to bother trying to optimize indexing on dates for this toy project....
    protected $allowedFields = [
        'id',
        'user_id',
        'created',
        'type',
        'price_total',
        'address',
        'pickup_datetime',
        'status'
    ];

    public function startOrderTransaction() {
        $this->db->transStart();
    }

    public function completeOrderTransaction() {
        $this->db->transComplete();

        return $this->db->transStatus();
    }

    public function createOrder(int $userId, float $priceTotal, array &$purchaseEntries, int $type = OrderModel::TYPE_DELIVERY, string $address = null, int $pickupDateTime = null) {
        $resultId = $this->insert([
            "user_id" => $userId,
            "created" => date('Y-m-d H:i:s'),
            "status" => OrderModel::STATUS_PENDING,
            "type" => $type,
            "price_total" => $priceTotal,
            "address" => $address,
            "pickup_datetime" => $pickupDateTime ? date('Y-m-d H:i:s', $pickupDateTime) : null,
        ]);

        /** @var \App\Models\OrderEntryModel */
        $entryModel = model(OrderEntryModel::class);

        foreach($purchaseEntries as &$purchaseRow) {
            $entryModel->addNewEntry(
                $resultId,
                $purchaseRow['product_id'],
                $purchaseRow['shop_id'],
                $purchaseRow['quantity'],
                $purchaseRow['price_per_unit'],
                $purchaseRow['product_name']
            );
        }
    }

    public function getOrdersForUser(int $userId, int $limit = 20) {
        $result = $this->where([ "user_id" => $userId ])->orderBy("created", "desc")->findAll($limit);
        return $result;
    }

    public function getOrderDetails(int $orderId) {
        $order = $this->where(["id" => $orderId])->first();

        if (!$order) return null;

        // get records
        /** @var \App\Models\OrderEntryModel */
        $entryModel = model(OrderEntryModel::class);

        $entries = $entryModel->getEntriesForOrder($orderId);

        $order['entries'] = $entries;

        return $order;
    }

    public function hasPurchasedBefore(int $userId, int $productId) {
        $result = $this->select("1")
                    ->join("order_entry", "order.id = order_entry.order_id", "inner")
                    ->where(["order.user_id" => $userId, "order_entry.product_id" => $productId, "status" => OrderModel::STATUS_COMPLETE ])
                    ->first();

        return !!$result;
    }

    public function cancelOrder(int $orderId) {
        $result = $this->update($orderId, [
            "status" => OrderModel::STATUS_CANCELLED
        ]);

        return $result;
    }

    public function updateStoreOrderCompletion(int $orderId, array $completedEntryIds, bool $completeFullOrder) {
        /** @var \App\Models\OrderEntryModel */
        $entryModel = model(OrderEntryModel::class);
        $result = $entryModel->completeEntries($completedEntryIds);

        if ($completeFullOrder) {
            return $this->update($orderId, [ "status" => OrderModel::STATUS_COMPLETE ]);
        }

        return $result;
    }

    public function getLatestOrdersForShop(int $shopId, int $limit = 50) {
        $query = $this->db->query("SELECT o.id, o.user_id, o.created, o.`type`, o.`status`, SUM(e.completed) = COUNT(e.id) AS 'completed', SUM(e.quantity * e.price_per_unit) AS 'shop_total_price'
            FROM `order` o 
            INNER JOIN `order_entry` e ON e.order_id = o.id 
            WHERE e.shop_id = :shop_id:
            GROUP BY o.id
            ORDER BY o.`type` DESC, o.`created` DESC
            LIMIT :lim:", 
            [
                "lim" => $limit,
                "shop_id" => $shopId
            ]);

        $result = $query->getResultArray();
        
        return $result;
    }

    public function getOrderDetailsForShop(int $shopId, int $orderId) {
        $query = $this->db->query("SELECT o.id AS 'order_id', o.user_id, o.created, o.`type`, o.address, o.pickup_datetime, o.`status`, 
            e.completed, e.quantity, e.price_per_unit, e.price_per_unit * e.quantity AS 'profit_total', e.product_id, e.product_name as 'product_title_fallback',
            p.title as 'product_title', p.main_media AS 'product_main_media',
            m.mimetype
            FROM `order` o 
            INNER JOIN `order_entry` e ON e.order_id = o.id 
            LEFT JOIN product p ON e.product_id = p.id
            LEFT JOIN shop_media m ON p.main_media = m.id
            WHERE o.id = :order_id: AND e.shop_id = :shop_id:", 
            [
                "order_id" => $orderId,
                "shop_id" => $shopId
            ]);
        
        $result = $query->getResultArray();
        $converted = OrderModel::convertOrderDetailStructure($result);

        return $converted;
    }

    private static function convertOrderDetailStructure(array &$orderDetailsForShop) {
        if (!$orderDetailsForShop || count($orderDetailsForShop) === 0) return null;
        
        // container with main info
        $result = [
            "order_id" => $orderDetailsForShop[0]['order_id'],
            "user_id" => $orderDetailsForShop[0]['user_id'],
            "created" => $orderDetailsForShop[0]['created'],
            "type" => $orderDetailsForShop[0]['type'],
            "address" => $orderDetailsForShop[0]['address'],
            "pickup_datetime" => $orderDetailsForShop[0]['pickup_datetime'],
            "status" => $orderDetailsForShop[0]['status'],
            "entries" => []
        ];

        $overallCompletion = 0;
        $overallProfit = 0.0;

        foreach($orderDetailsForShop as &$detail) {
            $overallCompletion += $detail['completed'];
            $overallProfit += $detail['profit_total'];

            [ $isVideo, $thumbnailId ] = ShopMediaModel::getThumbnailInfo($detail['product_main_media'], $detail['mimetype']);

            $result['entries'][] = [
                "completed" => $detail['completed'],
                "profit_total" => $detail['profit_total'],
                "price_per_unit" => $detail['price_per_unit'],
                "quantity" => $detail['quantity'],
                "product_id" => $detail['product_id'],
                "product_title" => $detail['product_title'] ?? $detail['product_title_fallback'],
                "is_deleted" => !$detail['product_title'],
                "media_thumbnail_id" => $thumbnailId ?? $detail['product_main_media']
            ];
        }

        $result['completed'] = $overallCompletion === count($orderDetailsForShop);
        $result['profit'] = (float) $overallProfit;

        return $result;
    }

    public function getTotalOrdersForShop(int $shopId, int $startingFrom = 0, int $endingAt = -1) {
        // I'll interpret this as orders that included an order from this shop, with the items from this shop grouped together as one unit
        // so this is not the sum of the total items, but the count of the total orders that included items ordered from this shop
        $query = $this->db->query("SELECT COUNT(DISTINCT o.user_id) as 'count', SUM(e.price_per_unit * e.quantity) as 'profit'
            FROM `order` o 
            INNER JOIN `order_entry` e ON e.order_id = o.id 
            WHERE e.shop_id = :shop_id: AND o.`status` != 2 AND o.created >= :start_from: AND o.created < :ending_at:",
            [
                "shop_id" => $shopId,
                "start_from" => date('Y-m-d H:i:s', $startingFrom),
                "ending_at" => $endingAt !== -1 ? date('Y-m-d H:i:s', $endingAt) : date('Y-m-d H:i:s')
            ]);

        $row = $query->getFirstRow('array');

        if ($row) {
            return [$row['count'], $row['profit']];
        }

        return [0, 0];
    }

    public function getTotalUnitsSoldAndValue(int $shopId, int $productId, int $startingFrom = 0, int $endingAt = -1) {
        // Total units sold of product starting from date
        $query = $this->db->query("SELECT SUM(e.quantity) AS units_sold, SUM(e.quantity * e.price_per_unit) AS sales_value
            FROM `order` o 
            INNER JOIN `order_entry` e ON e.order_id = o.id 
            WHERE e.shop_id = :shop_id: AND o.`status` != 2 AND e.product_id = :product_id: o.created >= :start_from: AND o.created < :ending_at:
            GROUP BY e.product_id",
            [
                "shop_id" => $shopId,
                "product_id" => $productId,
                "start_from" => date('Y-m-d H:i:s', $startingFrom),
                "ending_at" => $endingAt !== -1 ? date('Y-m-d H:i:s', $endingAt) : date('Y-m-d H:i:s')
            ]);
        
        $result = $query->getFirstRow('array');
        return $result; // units_sold & sales_value
    }

    public function getTopSoldItemsForShop(int $shopId, int $top = 10, int $startingFrom = 0, int $endingAt = -1) {
        $query = $this->db->query("SELECT e.product_id, p.title, p.price, p.main_media, SUM(e.quantity) AS 'units_sold', 
            SUM(e.quantity * e.price_per_unit) AS 'sales_value', m.mimetype
            FROM `order` o 
            INNER JOIN `order_entry` e ON e.order_id = o.id 
            LEFT JOIN `product` p ON e.product_id = p.id
            LEFT JOIN `shop_media` m on p.main_media = m.id
            WHERE e.shop_id = :shop_id: AND o.`status` != 2 AND o.created >= :start_from:  AND o.created < :ending_at:
            GROUP BY e.product_id
            ORDER BY 'sales_value' DESC
            LIMIT :lim:",
            [
                "shop_id" => $shopId,
                "start_from" => date('Y-m-d H:i:s', $startingFrom),
                "ending_at" => $endingAt !== -1 ? date('Y-m-d H:i:s', $endingAt) : date('Y-m-d H:i:s'),
                "lim" => $top
            ]);
        
        $result = $query->getResultArray();
        
        OrderModel::convertTopDetailStructure($result);
        
        return $result;
    }

    private static function convertTopDetailStructure(array &$results) {
        foreach($results as &$detail) {
            [ $isVideo, $thumbnailId ] = ShopMediaModel::getThumbnailInfo($detail['main_media'], $detail['mimetype']);

            $detail['media_thumbnail_id'] = $thumbnailId ?? $detail['main_media'];
        }
    }

    public function getUniqueCustomerCount(int $shopId, int $startingFrom = 0, int $endingAt = -1) {
        $query = $this->db->query("SELECT COUNT(DISTINCT o.user_id) as 'count'
            FROM `order` o 
            INNER JOIN `order_entry` e ON e.order_id = o.id 
            WHERE e.shop_id = :shop_id: AND o.`status` != 2 AND o.created >= :start_from: AND o.created < :ending_at:",
            [
                "shop_id" => $shopId,
                "start_from" => date('Y-m-d H:i:s', $startingFrom),
                "ending_at" => $endingAt !== -1 ? date('Y-m-d H:i:s', $endingAt) : date('Y-m-d H:i:s')
            ]);
        
        $result = $query->getFirstRow('array')['count'];
        return $result;
    }
}