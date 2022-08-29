<?php

namespace App\Controllers;

use App\Models\OrderModel;
use App\Models\ProductModel;

class AppBaseController extends BaseController
{
    private const TIME_BETWEEN_REFETCH_ORDERS = 60 * 5; // 5 minutes
    private $cart = null;

    protected function getUserTemplateParams() {
        $session = \Config\Services::session();

        $ownShopUrl = null;
        $lastOrderCount = null;
        $isLoggedIn = !!$session->get('username');

        if ($session->get('shop_id') > 0) {
            $ownShopUrl = "/shop/{$session->get('shop_id')}";
            $lastOrderCount = $this->getUnprocessedShopOrdersCached();
        }

        $cart = $this->buildCart();

        $params = [ 
            'logged_in' => $isLoggedIn,
            'user' => [
                'id' => $session->get('user_id'),
                'first_name' => $session->get('first_name'),
                'last_name' => $session->get('last_name'),
                'username' => $session->get('username'),
                'has_shop' => $session->get('has_shop'),
                'shop_name' => $session->get('shop_name'),
                'shop_url' => $ownShopUrl,
                'shop_order_count' => $lastOrderCount
            ],
            'cart' => $cart
        ];

        return $params;
    }

    protected function loggedIn() {
        $session = \Config\Services::session();
        return !!$session->get('username');
    }

    protected function getCurrentUsername() {
        $session = \Config\Services::session();
        return $session->get('username');
    }

    protected function getCurrentUserId() {
        $session = \Config\Services::session();
        return $session->get('user_id');
    }

    protected function ownsShop(int $shopId) {
        $session = \Config\Services::session();
        return $session->get('shop_id') == $shopId;
    }

    protected function isShopOwner() {
        return $this->getOwnedShopId() != null;
    }

    protected function getOwnedShopId() {
        $session = \Config\Services::session();
        return $session->get('shop_id');
    }

    protected function getCart() {
        return $this->buildCart();
    }

    protected function getCartItemsToCounts() {
        $session = \Config\Services::session();
        return $session->get("cart") ?? [];
    }
    
    protected function redirectToLoginPreAuth() {
        // remember where we wanted to go
        $session = \Config\Services::session();
        $intendedTarget = $this->request->getPath();
        $session->setFlashdata("intended_target", $intendedTarget);
        return redirect()->to('/account/login');
    }

    protected function persistLoginTargetPath() {
        $session = \Config\Services::session();
        $intendedTarget = $session->getFlashdata("intended_target");

        if ($intendedTarget) {
            $session->setFlashdata("intended_target", $intendedTarget);
        }
    }

    protected function getPostLoginTargetPath() {
        $session = \Config\Services::session();
        $intendedTarget = $session->getFlashdata("intended_target");
        return $intendedTarget ?? "/";
    }

    protected function getUnprocessedShopOrdersCached(bool $forceReload = false) {
        if (!$this->isShopOwner()) return null;
        $session = \Config\Services::session();

        $lastOrdersCheckAt = $session->get("shop_orders_count_time") ?? null;
        $lastOrderCount = null;
        if ($forceReload || !$lastOrdersCheckAt || (time() - $lastOrdersCheckAt) > self::TIME_BETWEEN_REFETCH_ORDERS) {

            /** @var \App\Models\OrderModel */
            $model = model(OrderModel::class);
            $count = $model->getUnprocessedOrderCountForShop($session->get('shop_id'));

            $session->set("shop_orders_count", $count);
            $lastOrderCount = $count;
        } else {
            $lastOrderCount = $session->get("shop_orders_count");
        }

        return $lastOrderCount;
    }

    private function buildCart() {
        if ($this->cart === null) { // "cached"
            $session = \Config\Services::session();

            $cart = $session->get("cart") ?? [];

            if (count($cart) === 0) {
                $this->cart = [];
                return $this->cart;
            }
            
            /** @var \App\Models\ProductModel */
            $productModel = model(ProductModel::class);
            $products = $productModel->getProductsByIds(array_keys($cart));
            
            foreach($products as &$product) {
                $product['quantity'] = $cart[$product['id']];
            }

            $this->cart = $products;
        }

        return $this->cart;
    }
}