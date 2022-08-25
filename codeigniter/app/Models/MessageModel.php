<?php

namespace App\Models;

use CodeIgniter\Model;

class MessageModel extends Model
{
    protected $primaryKey = "id";
    protected $table = 'messages';
    protected $allowedFields = [
        'id',
        'chain_id',
        'from_user',
        'user_name',
        'shop_name',
        'timestamp',
        'message'
    ];

    public function getLastMessage(int $chainId)
    {
        $result = $this->where(["chain_id" => $chainId])->orderBy("timestamp", "asc")->first();
        return $result;
    }

    public function getChainMessages(int $chainId) {
        $result = $this->where(["chain_id" => $chainId])->orderBy("timestamp", "asc")->findAll();
        return $result;
    }

    public function addMessage(int $chainId, bool $isFromUser, string $currentUserName, string $currentShopName, string $message, int $time) {
        $result = $this->insert([
            "chain_id" => $chainId,
            "from_user" => $isFromUser,
            "user_name" => $currentUserName,
            "shop_name" => $currentShopName,
            "timestamp" => date('Y-m-d H:i:s', $time),
            "message" => $message
        ]);
        
        return $result;
    }
}