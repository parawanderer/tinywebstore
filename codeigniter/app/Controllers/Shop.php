<?php

namespace App\Controllers;

use App\Helpers\HtmlSanitizer;
use App\Helpers\ContrastRatioChecker;
use App\Helpers\FFMPregHelper;
use App\Models\OrderModel;
use App\Models\ProductModel;
use App\Models\ReviewModel;
use App\Models\ShopMediaModel;
use App\Models\ShopModel;
use App\Models\WatchlistModel;
use CodeIgniter\Exceptions\PageNotFoundException;
use CodeIgniter\Files\Exceptions\FileNotFoundException;
use CodeIgniter\Files\File;
use CodeIgniter\HTTP\Files\UploadedFile;
use Exception;

class Shop extends AppBaseController
{
    public function index(int $shopId = -1) {
        $templateParams = $this->getUserTemplateParams();

        /** @var \App\Models\ShopModel */
        $model = model(ShopModel::class);
        $shop = $model->getShop($shopId);

        if (!$shop) {
            throw new PageNotFoundException("The shop does not exist");
        }

        /** @var \App\Models\ShopMediaModel */
        $mediaModel = model(ShopMediaModel::class);

        $media = $mediaModel->getForShop($shopId);

        /** @var \App\Models\ProductModel */
        $productModel = model(ProductModel::class);
        $products = $productModel->getForShop($shopId, 8);

        $templateParams['owns_shop'] = $this->ownsShop($shop['id']);
        $templateParams['shop'] = $shop;
        $templateParams['description_safe'] = HtmlSanitizer::sanitize($shop['description']);
        $templateParams['icon_color'] = ContrastRatioChecker::getDarkerColor($shop['font_color'], $shop['theme_color']);

        $templateParams['products'] = $products;

        $templateParams['media'] = $media;

        return view('templates/header')
            . view('templates/top_bar', $templateParams)
            . view('shop/index', $templateParams)
            . view('templates/footer');
    }

    public function edit() {
        $ownedShopId = $this->getOwnedShopId();
        if (!$ownedShopId) return redirect()->to("/");

        /** @var \App\Models\ShopModel */
        $model = model(ShopModel::class);
        $shop = $model->getShop($ownedShopId);

        $templateParams = $this->getUserTemplateParams();
        $templateParams['shop'] = $shop;
        $templateParams['error'] = false;


        $shopEditRules = [
            'shopName' => 'required',
            'supportEmail' => 'valid_email',
            'backgroundColor' => 'hexcolor',
            'textColor' => 'hexcolor',
            'shopBanner' => 'is_image[shopBanner]|mime_in[shopBanner,image/jpg,image/jpeg,image/gif,image/png,image/webp]',
            'shopLogo' => 'is_image[shopLogo]|mime_in[shopLogo,image/jpg,image/jpeg,image/gif,image/png,image/webp]',
        ];


        if ($this->request->getMethod() === 'post') {        
            
            if ($this->validate($shopEditRules) && $this->validateColorContrast()) {
                $shopLogo = $this->request->getFile('shopLogo');
                $shopBanner = $this->request->getFile('shopBanner');

                [ $shopLogoName, $shopBannerName ] = $this->replaceShopImages(
                    $shop, 
                    boolval($this->request->getPost('removeCurrentBanner')),
                    boolval($this->request->getPost('removeCurrentLogo')),
                    $shopLogo, 
                    $shopBanner
                );
    
                $model->update($ownedShopId, [
                    'name' => $this->request->getPost('shopName'),
                    'support_email' => $this->request->getPost('supportEmail'),
                    'address' => $this->request->getPost('address'),
                    'phone_number' => $this->request->getPost('phoneNumber'),
                    'description' => $this->request->getPost('description'),
                    'theme_color' => $this->request->getPost('backgroundColor'),
                    'font_color' => $this->request->getPost('textColor'),
                    'shop_logo_img' => $shopLogoName,
                    'shop_banner_img' => $shopBannerName,
                ]);
    
                return redirect()->to("/shop/{$ownedShopId}");
            } else {
                $templateParams['error'] = true;
            }   
        }

        return view('templates/header')
            . view('templates/top_bar', $templateParams)
            . view('shop/edit', $templateParams)
            . view('templates/footer');
    }

    public function deleteMedia() {
        if (!$this->loggedIn()) throw new Exception("No access");

        $deletedMedia = $this->handleDeleteMedia();

        if ($deletedMedia) {
            return redirect()->to("/shop/{$deletedMedia['shop_id']}");
        }

        throw new Exception("Bad request");
    }

    public function addMedia() {
        if (!$this->loggedIn()) throw new Exception("No access");
        if (!$this->isShopOwner()) throw new Exception("No access");
        // add media to own shop

        $result = $this->handleAddMedia();

        if (!$result) {
            throw new Exception("Bad request");
        }

        return redirect()->to("/shop/{$this->getOwnedShopId()}");
    }

    public function inventory() {
        if (!$this->loggedIn()) return redirect()->to('/account/login');
        if (!$this->isShopOwner()) return redirect()->to('/account/login');

        $ownedShopId = $this->getOwnedShopId();

        /** @var \App\Models\ShopModel */
        $model = model(ShopModel::class);
        $shop = $model->getShop($ownedShopId);


        /** @var \App\Models\ProductModel */
        $productModel = model(ProductModel::class);
        $products = $productModel->getForShop($ownedShopId);

        $templateParams = $this->getUserTemplateParams();
        $templateParams['shop'] = $shop;
        $templateParams['page'] = 'inventory';
        $templateParams['products'] = $products;

        return view('templates/header')
            . view('templates/top_bar', $templateParams)
            . view('shop/inventory', $templateParams)
            . view('templates/footer');
    }

    public function product(int $productId = -1) {
        /** @var \App\Models\ProductModel */
        $productModel = model(ProductModel::class);
        $product = $productModel->getById($productId);

        if (!$product) throw new PageNotFoundException("Product does not exist");

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
        Shop::sortMediaForProduct($mediaForProduct, $product['main_media']);


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
        $templateParams['primary_media'] = Shop::getMediaPrimary($mediaForProduct, $product['main_media']);
        $templateParams['reviews'] = $reviews;
        $templateParams['description_safe'] = HtmlSanitizer::sanitize($product['description']);
        $templateParams['is_shop_owner'] = $this->ownsShop($product['shop_id']);
        $templateParams['average_score'] = Shop::getAverageScore($reviews);
        $templateParams['similar_products'] = $similarProducts;
        $templateParams['is_watched'] = $isWatched;
        $templateParams['can_review'] = $canReview;

        return view('templates/header')
            . view('templates/top_bar', $templateParams)
            . view('product/view', $templateParams)
            . view('templates/footer');
    }

    public function productCreateEdit(int $productId = -1) {
        if (!$this->loggedIn()) return redirect()->to('/account/login');
        if (!$this->isShopOwner()) return redirect()->to('/account/login');
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

            Shop::sortMediaForProduct($mediaForProduct, $product['main_media']);
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

                return redirect()->to("/product/{$resultId}");

            } else {
                $templateParams['error'] = true;
            }
        }

        $templateParams['product'] = $product;
        $templateParams['media'] = $mediaForProduct;
        $templateParams['primary_media'] = Shop::getMediaPrimary($mediaForProduct, $product['main_media']);

        return view('templates/header')
            . view('templates/top_bar', $templateParams)
            . view('product/edit', $templateParams)
            . view('templates/footer');
    }

    public function productDelete() {
        if (!$this->loggedIn()) return redirect()->to('/account/login');
        if (!$this->isShopOwner()) return redirect()->to('/account/login');

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

    private function handleAddMedia(int $productId = null) {
        $mediaUploadRules = [
            'mediaFile' => 'uploaded[mediaFile]|mime_in[mediaFile,image/jpg,image/jpeg,image/gif,image/png,image/webp,video/mp4]',
        ];

        if ($this->validate($mediaUploadRules)) {
            $shopId = $this->getOwnedShopId();
            $mediaFile = $this->request->getFile('mediaFile');
            $mediaFileId = null;
            $mimeType = $mediaFile->getMimeType();
            // store media file
            if (!$mediaFile->hasMoved()) {
                $mediaFileId = $mediaFile->getRandomName();

                if (Shop::isVideoType($mimeType)) {
                    $this->handleSaveThumbnail($mediaFile, $mediaFileId);
                }

                $mediaFile->move(ROOTPATH . "public/uploads/" . ShopMediaModel::SHOP_MEDIA_PATH, $mediaFileId);
            } else {
                throw new Exception("Error processing request");
            }

            // link media file to store
            /** @var \App\Models\ShopMediaModel */
            $model = model(ShopMediaModel::class);
            $model->addForShop($mediaFileId, $mimeType, $shopId, $productId);

            return $mediaFileId;
        } 

        return null;
    }

    private function handleDeleteMedia() {
        $deleteRules = [
            'deleteMediaId' => 'required'
        ];

        if ($this->validate($deleteRules)) {
            $mediaId = $this->request->getPost('deleteMediaId');

            /** @var \App\Models\ShopMediaModel */
            $model = model(ShopMediaModel::class);
            $media = $model->getById($mediaId);

            if (!$media) {
                throw new PageNotFoundException("Media does not exist");
            }

            if ($media['shop_id'] != $this->getOwnedShopId()) {
                throw new Exception("No access");
            }

            $model->deleteById($mediaId);
            $this->deleteSingularMediaFiles($media);

            return $media;
        }

        return null;
    }

    private function handleSaveThumbnail(UploadedFile $file, string $mediaFileId) {
        $thumbnailName = Shop::getVideoThumnailPath($mediaFileId);
        FFMPregHelper::saveThumbnail( $file->getPathname(), $thumbnailName);
    }

    private function validateColorContrast() {
        $bg = $this->request->getPost('backgroundColor');
        $text = $this->request->getPost('textColor');

        if (!ContrastRatioChecker::isOkContrast($bg, $text)) {
            $this->validator->setError('backgroundColor', 'Insufficient contrast ratio with text color!');
            return false;
        }

        return true;
    }

    private function replaceShopImages(array $shop, bool $removeCurrentBanner, bool $removeCurrentLogo, UploadedFile $shopLogo = null, UploadedFile $shopBanner = null) {
        $shopLogoName = $shop['shop_logo_img'];
        $shopBannerName = $shop['shop_banner_img'];
        
        // delete old
        if (($removeCurrentLogo || $shopLogo->isValid()) && $shop['shop_logo_img']) {
            try {
                $oldShopImg = new File(ROOTPATH . "public/uploads/" . ShopModel::SHOP_LOGO_PATH . $shop['shop_logo_img'], true);
                unlink($oldShopImg->getPathname());
            } catch (FileNotFoundException $e) {}

            $shopLogoName = null;
        }

        if (($removeCurrentBanner || $shopBanner->isValid()) && $shop['shop_banner_img']) {
            try {
                $oldShopImg = new File(ROOTPATH . "public/uploads/" . ShopModel::SHOP_BANNER_PATH . $shop['shop_banner_img'], true);
                unlink($oldShopImg->getPathname());
            } catch (FileNotFoundException $e) {}

            $shopBannerName = null;
        }

        //add new
        if ($shopLogo->isValid() && !$shopLogo->hasMoved()) {
            $shopLogoName = $shopLogo->getRandomName();
            $shopLogo->move(ROOTPATH . "public/uploads/" . ShopModel::SHOP_LOGO_PATH, $shopLogoName);
        }

        if ($shopBanner->isValid() && !$shopBanner->hasMoved()) {
            $shopBannerName = $shopBanner->getRandomName();
            $shopBanner->move(ROOTPATH . "public/uploads/" . ShopModel::SHOP_BANNER_PATH, $shopBannerName);
        }

        return [$shopLogoName, $shopBannerName];
    }

    private function deleteMediaForProduct(array &$media) {
        foreach($media as &$mediaEntry) {
            $this->deleteSingularMediaFiles($mediaEntry);
        }
    }

    private function deleteSingularMediaFiles(array &$mediaItem) {
        unlink(ROOTPATH . "public/uploads/" . ShopMediaModel::SHOP_MEDIA_PATH . $mediaItem['id']);
        if (Shop::isVideoType($mediaItem['mimetype'])) {
            $thumbnailName = Shop::getVideoThumnailPath($mediaItem['id']);
            unlink($thumbnailName);
        }
    }

    private static function isVideoType(string $mimeType) {
        return $mimeType === "video/mp4"; // only mp4 supported for this implementation
    }

    private static function getVideoThumnailPath(string $fileName) {
        return ROOTPATH . "public/uploads/" . ShopMediaModel::SHOP_MEDIA_PATH . substr($fileName, 0, strrpos($fileName, ".")) . ".jpg";
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

    private static function sortMediaForProduct(array &$mediaItems, ?string $primaryMediaId) {
        if (count($mediaItems) == 0) return;

        usort($mediaItems, function($a, $b) use ($primaryMediaId) {
            if ($a['id'] === $primaryMediaId) return -1;
            if ($b['id'] === $primaryMediaId) return 1;

            return strtotime($a['created']) - strtotime($b['created']);
        });
    }
}