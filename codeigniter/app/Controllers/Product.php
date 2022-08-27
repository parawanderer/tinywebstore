<?php

namespace App\Controllers;

use App\Helpers\AlertHelper;
use App\Helpers\HtmlSanitizer;
use App\Helpers\MediaFile;
use App\Models\OrderModel;
use App\Models\ProductModel;
use App\Models\ReviewModel;
use App\Models\ShopMediaModel;
use App\Models\ShopModel;
use App\Models\WatchlistModel;
use CodeIgniter\Exceptions\PageNotFoundException;
use Exception;

class Product extends ShopDataControllerBase
{
    private const PRODUCT_HISTORY_LIMIT = 6;

    public function product(int $productId = -1) {
        /** @var \App\Models\ProductModel */
        $productModel = model(ProductModel::class);
        $product = $productModel->getById($productId);

        if (!$product) throw new PageNotFoundException("Product does not exist");
        $this->rememberSeenProduct($product['id']);

        $similarProducts = $productModel->getForShopExcluding($product['shop_id'], [$productId], 9);

        /** @var \App\Models\ShopModel */
        $shopModel = model(ShopModel::class);
        $shop = $shopModel->getShop($product['shop_id']);


        /** @var \App\Models\ReviewModel */
        $reviewModel = model(ReviewModel::class);
        $reviews = $reviewModel->getForProduct($productId);


        /** @var \App\Models\ShopMediaModel */
        $mediaModel = model(ShopMediaModel::class);
        $mediaForProduct = $mediaModel->getForProduct($productId);
        Product::sortMediaForProduct($mediaForProduct, $product['main_media']);


        $isWatched = false;
        $canReview = false;
        if ($this->loggedIn()) {
            /** @var \App\Models\WatchlistModel */
            $watchModel = model(WatchlistModel::class);
            $isWatched = $watchModel->isWatched($this->getCurrentUserId(), $product['id']); 

            /** @var \App\Models\ReviewModel */
            $reviewModel = model(ReviewModel::class);
            /** @var \App\Models\OrderModel */
            $orderModel = model(OrderModel::class);

            $canReview = !$reviewModel->hasReviewedBefore($this->getCurrentUserId(), $product['id']) && $orderModel->hasPurchasedBefore($this->getCurrentUserId(), $product['id']);
        }

        $templateParams = $this->getUserTemplateParams();
        $templateParams['shop'] = $shop;
        $templateParams['product'] = $product;
        $templateParams['media'] = $mediaForProduct;
        $templateParams['primary_media'] = Product::getMediaPrimary($mediaForProduct, $product['main_media']);
        $templateParams['reviews'] = $reviews;
        $templateParams['description_safe'] = HtmlSanitizer::sanitize($product['description']);
        $templateParams['is_shop_owner'] = $this->ownsShop($product['shop_id']);
        $templateParams['average_score'] = Product::getAverageScore($reviews);
        $templateParams['similar_products'] = $similarProducts;
        $templateParams['is_watched'] = $isWatched;
        $templateParams['can_review'] = $canReview;
        $templateParams['title'] = "Product | {$product['title']}";

        return view('templates/header', $templateParams)
            . view('templates/top_bar', $templateParams)
            . view('product/view', $templateParams)
            . view('templates/footer');
    }

    public function productCreateEdit(int $productId = -1) {
        if (!$this->loggedIn() || !$this->isShopOwner()) return $this->redirectToLoginPreAuth();
        $isCreating = $productId === -1;

        /** @var \App\Models\ProductModel */
        $productModel = model(ProductModel::class);

        // default for create
        $product = [
            'title' => null,
            'price' => 1.0,
            'availability' => 100,
            'description' => null,
            'main_media' => null
        ];
        $mediaForProduct = [];
        $validationRules = [
            'productTitle' => 'required',
            'productPrice' => 'required|decimal|greater_than_equal_to[0.0]',
            'productAvailability' => 'required|integer|greater_than_equal_to[0]',
        ];

        $templateParams = $this->getUserTemplateParams();
        $templateParams['error'] = false;
        $templateParams['is_edit'] = false;

        if (!$isCreating) {
            $product = $productModel->getById($productId);
            if (!$product) throw new PageNotFoundException("Product does not exist");
            if ($product['shop_id'] != $this->getOwnedShopId()) throw new Exception("No access");
            $templateParams['is_edit'] = true;

            /** @var \App\Models\ShopMediaModel */
            $mediaModel = model(ShopMediaModel::class);
            $mediaForProduct = $mediaModel->getForProduct($productId);
            Product::sortMediaForProduct($mediaForProduct, $product['main_media']);
        }

        if ($this->request->getMethod() === 'post') {
            if ($this->validate($validationRules)) {
                // update and save or insert
                $product['shop_id'] = $this->getOwnedShopId();

                $product['title'] = $this->request->getPost("productTitle");
                $product['price'] = $this->request->getPost("productPrice");
                $product['availability'] = $this->request->getPost("productAvailability");
                $product['description'] = $this->request->getPost("productDescription");

                $product['main_media'] = $this->request->getPost("productPrimaryImage");
                if (!$product['main_media'] || ctype_space($product['main_media']))
                    $product['main_media'] = null;

                $result = $productModel->save($product);

                if (!$result) throw new Exception("Failed to save");

                $resultId = $product['id'] ?? $productModel->db->insertID();

                // if creating, redirect to edit page to maybe add media
                if ($isCreating)
                    return redirect()->to("/product/edit/{$resultId}");        
                    
                if ($product['availability'] > 0) {
                    // alerts
                    $alertHelper = new AlertHelper();
                    $alertHelper->watchlistItemAvailableAlert($resultId, $product['title']);
                }

                return redirect()->to("/product/{$resultId}");

            } else {
                $templateParams['error'] = true;
            }
        }

        $templateParams['product'] = $product;
        $templateParams['media'] = $mediaForProduct;
        $templateParams['primary_media'] = Product::getMediaPrimary($mediaForProduct, $product['main_media']);
        $templateParams['title'] = $isCreating ? "Product | Create New" : "Product | {$product['title']} | Edit";

        return view('templates/header', $templateParams)
            . view('templates/top_bar', $templateParams)
            . view('product/edit', $templateParams)
            . view('templates/footer');
    }

    public function productDelete() {
        if (!$this->loggedIn() || !$this->isShopOwner()) return $this->redirectToLoginPreAuth();

        $validationRules = [
            'deleteProductId' => 'required|integer',
        ];

        if ($this->validate($validationRules)) {
            $deleteId = $this->request->getPost("deleteProductId");

            /** @var \App\Models\ProductModel */
            $productModel = model(ProductModel::class);

            $product = $productModel->getById($deleteId);

            if (!$product) throw new PageNotFoundException("Product does ot exist");
            if ($product['shop_id'] != $this->getOwnedShopId()) throw new Exception("No access");

            // delete it and all its linked resources....
            // media + product (not watchlist, purchases)

            /** @var \App\Models\ShopMediaModel */
            $mediaModel = model(ShopMediaModel::class);

            $media = $mediaModel->getForProduct($deleteId);
            $this->deleteMediaForProduct($media);
            $productModel->deleteCascase($deleteId);

            return redirect()->to("/shop/inventory");
        }

        throw new Exception("Bad request");
    }

    public function productAddMedia(int $productId = -1) {
        if (!$this->loggedIn()) throw new Exception("No access");
        if (!$this->isShopOwner()) throw new Exception("No access");

        /** @var \App\Models\ProductModel */
        $productModel = model(ProductModel::class);

        $product = $productModel->getById($productId);
        if (!$product) throw new PageNotFoundException("Product does not exist");
        if ($product['shop_id'] != $this->getOwnedShopId()) throw new Exception("No access");

        $resultId = $this->handleAddMedia($product['id']);

        if (!$resultId) {
            throw new Exception("Bad request");
        }

        // add primary media item if we don't have one yet
        if (!$product['main_media']) {
            $product['main_media'] = $resultId;
            $productModel->save($product);
        }

        return redirect()->to("/product/edit/{$product['id']}");
    }

    public function productDeleteMedia(int $productId = -1) {
        if (!$this->loggedIn()) throw new Exception("No access");
        if (!$this->isShopOwner()) throw new Exception("No access");

        $deleteRules = [
            'deleteMediaId' => 'required'
        ];

        /** @var \App\Models\ProductModel */
        $productModel = model(ProductModel::class);

        $product = $productModel->getById($productId);
        if (!$product) throw new PageNotFoundException("Product does not exist");
        if ($product['shop_id'] != $this->getOwnedShopId()) throw new Exception("No access");

        if (!$this->validate($deleteRules))
            throw new Exception("Bad request");

        $mediaId = $this->request->getPost('deleteMediaId');

        /** @var \App\Models\ShopMediaModel */
        $mediaModel = model(ShopMediaModel::class);
        $media = $mediaModel->getById($mediaId);

        if ($media['product_id'] !== $product['id']) 
            throw new Exception("Media does not belong to product");

        $resultMedia = $this->handleDeleteMedia();

        if (!$resultMedia)
            throw new Exception("Internal Server Error");

        // need to replace with any other item as main item
        if ($product['main_media'] === $resultMedia['id']) {
            $anyOtherMedia = $mediaModel->getOneForProduct($product['id']);

            if ($anyOtherMedia) {
                $product['main_media'] = $anyOtherMedia['id'];
            } else {
                $product['main_media'] = null;
            }
            
            $productModel->save($product);
        }

        return redirect()->to("/product/edit/{$product['id']}");
    }

    public function productReview(int $productId = -1) {
        if (!$this->loggedIn()) throw new Exception("No access");

        $validationRules = [
            "reviewTitle" => "required",
            "reviewContent" => "required",
            "starRating" => "required|integer|in_list[1, 2, 3, 4, 5]"
        ];

        if (!$this->validate($validationRules)) throw new Exception("Bad request");

        /** @var \App\Models\ProductModel */
        $productModel = model(ProductModel::class);

        $product = $productModel->getById($productId);
        if (!$product) throw new PageNotFoundException("Product does not exist");

        /** @var \App\Models\ReviewModel */
        $reviewModel = model(ReviewModel::class);
        /** @var \App\Models\OrderModel */
        $orderModel = model(OrderModel::class);

        $canReview = !$reviewModel->hasReviewedBefore($this->getCurrentUserId(), $product['id'])
                        && $orderModel->hasPurchasedBefore($this->getCurrentUserId(), $product['id']);

        if (!$canReview) 
            throw new Exception("Bad request");
        
        $reviewTitle = $this->request->getPost("reviewTitle");
        $reviewContent = $this->request->getPost("reviewContent");
        $starRating = $this->request->getPost("starRating");

        $reviewModel->addReview($this->getCurrentUserId(), $product['id'], $reviewTitle, $starRating, $reviewContent);

        return redirect()->to("/product/{$product['id']}#reviews");
    }

    private function deleteMediaForProduct(array &$media) {
        foreach($media as &$mediaEntry) {
            $media = new MediaFile($mediaEntry['id'], MediaFile::TYPE_MEDIA, $mediaEntry['mimetype']);
            $media->deleteAllFiles();
        }
    }

    private function rememberSeenProduct(int $productId) {
        $session = \Config\Services::session();

        $history = $session->get("product_history") ?? [];
        $newHistory = [$productId];

        $i = 1;
        foreach($history as $hProdId) {
            if ($i >= Product::PRODUCT_HISTORY_LIMIT) break;

            if ($hProdId != $productId) {
                $newHistory[] = $hProdId;
                $i++;
            }
        }

        $session->set("product_history", $newHistory);
    }

    private static function sortMediaForProduct(array &$mediaItems, ?string $primaryMediaId) {
        if (count($mediaItems) == 0) return;

        usort($mediaItems, function($a, $b) use ($primaryMediaId) {
            if ($a['id'] === $primaryMediaId) return -1;
            if ($b['id'] === $primaryMediaId) return 1;

            return strtotime($a['created']) - strtotime($b['created']);
        });
    }
    
    private static function getAverageScore(array &$reviews) {
        if (count($reviews) === 0) return 0;

        $total = 0;
        foreach($reviews as &$review) {
            $total += $review['rating'];
        }

        return $total / count($reviews);
    }

    private static function getMediaPrimary(array &$mediaItems, ?string $primaryMediaId) {
        if (!$primaryMediaId || empty($primaryMediaId) || !$mediaItems || count($mediaItems) === 0) return null;

        foreach ($mediaItems as &$mediaItem) {
            if ($mediaItem['id'] === $primaryMediaId) {
                return $mediaItem;
            }
        }
        return null;
    }
}