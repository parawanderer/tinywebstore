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
                    ->where(["order.user_id" => $userId, "order_entry.product_id" => $productId])
                    ->first();

        return !!$result;
    }

    public function getLatestOrdersForShop(int $shopId) {

    }
}