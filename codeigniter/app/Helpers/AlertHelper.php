<?php
namespace App\Helpers;

use App\Models\AlertModel;
use App\Models\WatchlistModel;

class AlertHelper {
    private AlertModel $alertModel;
    private WatchlistModel $watchlistModel;

    public function __construct()
    {
        $this->alertModel = model(AlertModel::class);
        $this->watchlistModel = model(WatchlistModel::class);
    }

    public function orderCompleteAlert(array &$orderDetails) {
        $userId = $orderDetails['user_id'];

        foreach($orderDetails['entries'] as $orderRecord) {
            $productId = $orderRecord['product_id'];
            $productName = $orderRecord['product_title'];
            $this->alertModel->createAlert($userId, $productId, $productName, AlertModel::TYPE_PRODUCT_ORDER_COMPLETED);
        }
    }

    // this one is extremely inefficient for any large app. in a real app this should at least have been in a background process
    // or some kind of async call depending o the app size
    public function watchlistItemAvailableAlert(int $productId, string $productName) {
        $watchers = $this->watchlistModel->getUsersWatching($productId);

        foreach($watchers as $userId) {
            $this->alertModel->createAlert($userId, $productId, $productName, AlertModel::TYPE_WATCHLIST_AVAILABLE);
        }
    }

    // this too should really be a background process....
    public function bulkWatchlistItemAvailableAlert(array &$productIdsToNames) {
        foreach($productIdsToNames as $productId => $productName) {
            $this->watchlistItemAvailableAlert($productId, $productName);
        }
    }
}