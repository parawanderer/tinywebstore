<?php

namespace App\Controllers;

use App\Helpers\AlertHelper;
use App\Helpers\HtmlSanitizer;
use App\Helpers\ContrastRatioChecker;
use App\Helpers\MediaFile;
use App\Models\AccountModel;
use App\Models\OrderModel;
use App\Models\ProductModel;
use App\Models\ShopMediaModel;
use App\Models\ShopModel;
use CodeIgniter\Exceptions\PageNotFoundException;
use CodeIgniter\HTTP\Files\UploadedFile;
use Exception;

class Shop extends ShopDataControllerBase
{
    private const DAYS_30 = 2_592_000;
    private const DAYS_7 = 604_800;
    private const DAYS_1 = 86_400;

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
        $templateParams['title'] = "Shop | {$shop['name']}";

        return view('templates/header', $templateParams)
            . view('templates/top_bar', $templateParams)
            . view('shop/index', $templateParams)
            . view('templates/footer');
    }

    public function edit() {
        $ownedShopId = $this->getOwnedShopId();
        if (!$ownedShopId) return $this->redirectToLoginPreAuth();

        /** @var \App\Models\ShopModel */
        $model = model(ShopModel::class);
        $shop = $model->getShop($ownedShopId);

        $templateParams = $this->getUserTemplateParams();
        $templateParams['shop'] = $shop;
        $templateParams['error'] = false;
        $templateParams['title'] = "Shop | {$shop['name']} | Edit";


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

        return view('templates/header', $templateParams)
            . view('templates/top_bar', $templateParams)
            . view('shop/edit', $templateParams)
            . view('templates/footer');
    }

    public function deleteMedia() {
        if (!$this->loggedIn()) throw new Exception("No access");

        $deletedMedia = $this->handleDeleteMedia();

        if ($deletedMedia) {
            return redirect()->to("/shop/{$deletedMedia['shop_id']}#media");
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

        return redirect()->to("/shop/{$this->getOwnedShopId()}#media");
    }

    public function inventory() {
        if (!$this->loggedIn() || !$this->isShopOwner()) return $this->redirectToLoginPreAuth();

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
        $templateParams['title'] = "Shop | {$shop['name']} | Inventory";

        return view('templates/header', $templateParams)
            . view('templates/top_bar', $templateParams)
            . view('shop/inventory', $templateParams)
            . view('templates/footer');
    }

    public function orders() {
        if (!$this->loggedIn() || !$this->isShopOwner()) return $this->redirectToLoginPreAuth();

        $ownedShopId = $this->getOwnedShopId();

        /** @var \App\Models\ShopModel */
        $model = model(ShopModel::class);
        $shop = $model->getShop($ownedShopId);

        /** @var \App\Models\OrderModel */
        $orderModel = model(OrderModel::class);
        $latestOrders = $orderModel->getLatestOrdersForShop($ownedShopId, 50);

        $this->getUnprocessedShopOrdersCached(true);


        $templateParams = $this->getUserTemplateParams();
        $templateParams['shop'] = $shop;
        $templateParams['orders'] = $latestOrders;
        $templateParams['page'] = 'shop-orders';
        $templateParams['title'] = "Shop | {$shop['name']} | Orders";

        return view('templates/header', $templateParams)
            . view('templates/top_bar', $templateParams)
            . view('shop/orders', $templateParams)
            . view('templates/footer');
    }

    public function order(int $orderId = -1) {
        if (!$this->loggedIn() || !$this->isShopOwner()) return $this->redirectToLoginPreAuth();

        $ownedShopId = $this->getOwnedShopId();

        /** @var \App\Models\ShopModel */
        $model = model(ShopModel::class);
        $shop = $model->getShop($ownedShopId);

        /** @var \App\Models\OrderModel */
        $orderModel = model(OrderModel::class);
        $filteredOrderDetails = $orderModel->getOrderDetailsForShop($ownedShopId, $orderId);

        if (!$filteredOrderDetails || count($filteredOrderDetails) === 0)
            throw new Exception("Bad request");

        /** @var \App\Models\AccountModel */
        $accountModel = model(AccountModel::class);
        $orderer = $accountModel->getUserById($filteredOrderDetails['user_id']);

        $templateParams = $this->getUserTemplateParams();
        $templateParams['shop'] = $shop;
        $templateParams['order'] = $filteredOrderDetails;
        $templateParams['orderer'] = $orderer;
        $templateParams['page'] = 'shop-orders';
        $templateParams['title'] = "Shop | {$shop['name']} | Order #{$orderId}";

        return view('templates/header', $templateParams)
            . view('templates/top_bar', $templateParams)
            . view('shop/order', $templateParams)
            . view('templates/footer');
    }

    public function completeOrder() {
        if (!$this->loggedIn() || !$this->isShopOwner()) return $this->redirectToLoginPreAuth();

        if (!$this->validate(['orderId' => "required|integer"])) 
            throw new Exception("Bad request");

        $orderId = $this->request->getPost("orderId");
        $ownedShopId = $this->getOwnedShopId();

        /** @var \App\Models\OrderModel */
        $orderModel = model(OrderModel::class);
        $orderDetails = $orderModel->getOrderDetails($orderId);

        // already complete
        if ($orderDetails['status'] != OrderModel::STATUS_PENDING)
            throw new Exception("Bad request");

        // validate we even have any order belonging to this shop in this list?
        if (!Shop::validateOwnsAnyOrderEntries($orderDetails, $ownedShopId))
            throw new PageNotFoundException("Order does not exist");

        // assuming we do, we could just override the ones belonging to us.
        // we do want to see however if updating this order will result in the order going 
        // into the completed state
        [$completing, $updateStatus] = Shop::getCompletionDetails($orderDetails, $ownedShopId);

        $orderModel->updateStoreOrderCompletion($orderId, $completing, $updateStatus);
        
        $alertHelper = new AlertHelper();
        $alertOrders = Shop::filterOrderDetails($orderDetails['entries'], $completing);
        $alertHelper->orderCompleteAlert($alertOrders, $orderDetails['user_id']);

        return redirect()->to("/shop/order/{$orderId}");
    }

    public function stats() {
        // I would assume this exercise is not about me writing my own chart library so I'll use something open source for visuals...
        // of course, in the real world, you would cache this and probably generate this in a bg process...
        if (!$this->loggedIn() || !$this->isShopOwner()) return $this->redirectToLoginPreAuth();

        $ownedShopId = $this->getOwnedShopId();

        /** @var \App\Models\ShopModel */
        $model = model(ShopModel::class);
        $shop = $model->getShop($ownedShopId);

        /** @var \App\Models\OrderModel */
        $orderModel = model(OrderModel::class);
        $now = time();

        $uniqueCustomers30days = $orderModel->getUniqueCustomerCount($ownedShopId, $now - Shop::DAYS_30);
        [$total30d, $profit30d] = $orderModel->getTotalOrdersForShop($ownedShopId, $now - Shop::DAYS_30);
        $avgProfitPerOrder = $total30d > 0 ? $profit30d / $total30d : 0;

        $lastWeekdayStats = $this->getHistoryStats($ownedShopId, $now, 'D', Shop::DAYS_1, 7);
        $last8WeeksStats = $this->getHistoryStats($ownedShopId, $now, 'M jS', Shop::DAYS_7, 7);

        $top10Selling = $orderModel->getTopSoldItemsForShop($ownedShopId, 10, $now - Shop::DAYS_30);

        $templateParams = $this->getUserTemplateParams();
        $templateParams['page'] = 'stats';
        $templateParams['shop'] = $shop;
        $templateParams['customers30d'] = $uniqueCustomers30days;
        $templateParams['orders30d'] = $total30d;
        $templateParams['profit30d'] = $profit30d;
        $templateParams['avgProfit30d'] = $avgProfitPerOrder;
        $templateParams['last7dStats'] = $lastWeekdayStats;
        $templateParams['last8wStats'] = $last8WeeksStats;
        $templateParams['top10selling'] = $top10Selling;
        $templateParams['title'] = "Shop | {$shop['name']} | Statistics";

        return view('templates/header', $templateParams)
            . view('templates/top_bar', $templateParams)
            . view('shop/stats', $templateParams)
            . view('templates/footer');
    }

    private function getHistoryStats(int $shopId, int $now, string $dateTimeFormat = 'D', int $perStep = Shop::DAYS_1, int $stepsCount = 7) {
        /** @var \App\Models\OrderModel */
        $orderModel = model(OrderModel::class);
        $baseToday = strtotime(date('Y-m-d', $now));

        $result = [];

        $last = $now;
        $current = $baseToday;

        for ($i = 0; $i < $stepsCount; ++$i) {
            [$profit, $orders] = $orderModel->getTotalOrdersForShop($shopId, $current, $last);

            $result[] = [date($dateTimeFormat, $current), $profit, $orders];
            $last = $current;
            $current -= $perStep;
        }

        return $result;
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
            $media = new MediaFile($shop['shop_logo_img'], MediaFile::TYPE_LOGO, null);
            $media->deleteAllFiles();
            $shopLogoName = null;
        }

        if (($removeCurrentBanner || $shopBanner->isValid()) && $shop['shop_banner_img']) {
            $media = new MediaFile($shop['shop_banner_img'], MediaFile::TYPE_BANNER, null);
            $media->deleteAllFiles();
            $shopBannerName = null;
        }

        //add new
        if ($shopLogo->isValid() && !$shopLogo->hasMoved()) {
            $media = MediaFile::saveFromFile($shopLogo, MediaFile::TYPE_LOGO);
            $shopLogoName = $media->getId();
        }

        if ($shopBanner->isValid() && !$shopBanner->hasMoved()) {
            $media = MediaFile::saveFromFile($shopBanner, MediaFile::TYPE_BANNER);
            $shopBannerName = $media->getId();
        }

        return [$shopLogoName, $shopBannerName];
    }

    private static function getCompletionDetails(array &$orderDetails, int $shopId) {
        $completing = [];
        $incompleteBesidesOurs = 0;

        foreach($orderDetails['entries'] as &$det) {
            if ($det['shop_id'] == $shopId) $completing[] = $det['id']; // entry id

            if ($det['shop_id'] != $shopId && !$det['completed'])
                $incompleteBesidesOurs++;
        }

        return [$completing, $incompleteBesidesOurs === 0];
    }

    private static function validateOwnsAnyOrderEntries(array &$orderDetails, int $shopId) {
        foreach($orderDetails['entries'] as &$det) {
            if ($det['shop_id'] == $shopId) return true;
        }
        return false;
    }

    private static function filterOrderDetails(array &$entries, array &$completedEntryIds) {
        $ids = [];
        $result = [];

        foreach($completedEntryIds as $id) {
            $ids[$id] = 1;
        }

        foreach($entries as &$entry) {
            if (array_key_exists($entry['id'], $ids))
                $result[] = $entry;
        }

        return $result;
    }
}