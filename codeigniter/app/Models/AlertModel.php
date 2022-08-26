<?php

namespace App\Models;

use CodeIgniter\Model;

// this is a very crude and not very efficient events queue/distribution implementation that is going to require basic polling
class AlertModel extends Model
{
    public const TYPE_WATCHLIST_AVAILABLE = 0;
    public const TYPE_PRODUCT_ORDER_COMPLETED = 1;

    protected $primaryKey = "id";
    protected $table = 'alert';
    protected $allowedFields = [
        'id',
        'user_id',
        'timestamp',
        'seen',
        'subject_id',
        'subject_name',
        'type'
    ];

    public function createAlert(int $userId, int $subjectId, string $subjectName, int $type = AlertModel::TYPE_WATCHLIST_AVAILABLE) {
        return $this->insert([
            "user_id" => $userId,
            "seen" => false,
            "timestamp" => date('Y-m-d H:i:s'),
            "subject_id" => $subjectId,
            "subject_name" => $subjectName,
            "type" => $type
        ]);
    }

    public function getUnseenAlerts(int $userId, int $limit, bool $markOffSeen = true) {
        // update and return
        $result = $this->where([ "user_id" => $userId, "seen" => false ])->orderBy("timestamp", "ASC")->findAll($limit);

        if (!empty($result) && $markOffSeen) {
            $ids = AlertModel::getAlertIds($result);
            $this->set("seen", true)->whereIn("id", $ids)->update();
        }
        
        return $result;
    }

    public function markRead(int $alertId, int $userId) {
        $result = $this->set("seen", true)->where([ "id" => $alertId, "user_id" => $userId ])->update();
        return $result;
    }

    private static function getAlertIds(array &$alerts) {
        $ids = [];

        foreach($alerts as &$alert) {
            $ids[] = $alert['id'];
        }

        return $ids;
    }
}