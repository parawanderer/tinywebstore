<?php

namespace App\Models;

use CodeIgniter\Model;

class ReviewModel extends Model
{
    public const SHOP_LOGO_PATH = "shop/logo/";
    public const SHOP_BANNER_PATH = "shop/banner/";

    protected $primaryKey = "id";
    protected $table = 'review';
    protected $allowedFields = [
        'id',
        'author_id',
        'product_id',
        'timestamp',
        'title',
        'rating',
        'content'
    ];

    public function getForProduct(int $productId) {
        $this->select("review.id, review.author_id, account.first_name, account.last_name, review.timestamp, review.title, review.rating, review.content")
            ->join('account', 'account.id = review.author_id')
            ->where("review.product_id", $productId)
            ->orderBy("review.timestamp", "desc");

        $result = $this->findAll();

        return $result;
    }

    public function hasReviewedBefore(int $userId, int $productId) {
        $result = $this->select("1")->where([ "author_id" => $userId, "product_id" => $productId ])->first();
        return !!$result;
    }

    public function addReview(int $userId, int $productId, string $title, int $rating, string $content) {
        $result = $this->insert([
            "author_id" => $userId,
            "product_id" => $productId,
            "timestamp" => date('Y-m-d H:i:s'),
            "title" => $title,
            "rating" => $rating,
            "content" => $content
        ]);

        return $result;
    }
}