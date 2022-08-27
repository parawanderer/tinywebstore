<?php

namespace App\Models;

use CodeIgniter\Model;

class MessageChainModel extends Model
{
    protected $primaryKey = "id";
    protected $table = 'message_chain';
    protected $allowedFields = [
        'id',
        'user_id',
        'shop_id',
        'timestamp',
        'updated'
    ];

    public function getConversation(int $userId, int $shopId) {
        $result = $this->where([ "user_id" => $userId, "shop_id" => $shopId ])->orderBy("id", "desc")->first();
        return $result;
    }

    public function getConversationById(int $conversationId) {
        $result = $this->where([ "id" => $conversationId])->first();
        return $result;
    }

    public function getMessageChainsForUser(int $userId) {
        $result = $this->select("message_chain.id, message_chain.user_id, message_chain.shop_id, message_chain.timestamp as created, message_chain.updated, shop.name as shop_name, shop.shop_logo_img, account.first_name as user_first_name, account.last_name as user_last_name")
            ->join("account", "message_chain.user_id = account.id", "left")
            ->join("shop", "message_chain.shop_id = shop.id")
            ->where([
                "message_chain.user_id" => $userId,
            ])
            ->orderBy("message_chain.updated", "desc")
            ->orderBy("message_chain.timestamp", "desc")
            ->findAll();
        
        MessageChainModel::extendShopMedia($result);

        return $result;
    }

    public function getMessageChainsForStore(int $shopId) {
        $result = $this->select("message_chain.id, message_chain.user_id, message_chain.shop_id, message_chain.timestamp as created, message_chain.updated, shop.name as shop_name, shop.shop_logo_img, account.first_name as user_first_name, account.last_name as user_last_name")
            ->join("account", "message_chain.user_id = account.id", "left")
            ->join("shop", "message_chain.shop_id = shop.id")
            ->where([
                "message_chain.shop_id" => $shopId
            ])
            ->where("message_chain.updated !=", NULL)
            ->orderBy("message_chain.updated", "desc")
            ->orderBy("message_chain.timestamp", "desc")
            ->findAll();
        
        return $result;
    }

    public function updateLastMessageTime(int $conversationId, int $time) {
        $result = $this->update($conversationId, [
            "updated" => date('Y-m-d H:i:s', $time)
        ]);

        return $result;
    }

    public function startNewChain(int $userId, int $shopId) {
        $result = $this->insert([
            "user_id" => $userId,
            "shop_id" => $shopId,
            "timestamp" => date('Y-m-d H:i:s'),
            "updated" => null
        ]);

        return $result;
    }

    private static function extendShopMedia(array &$results) {
        foreach($results as &$record) {
            ShopModel::extendShopMedia($record);
        }
    }
}