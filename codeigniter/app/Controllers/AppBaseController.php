<?php

namespace App\Controllers;

use App\Models\ProductModel;

class AppBaseController extends BaseController
{
    private $cart = null;

    protected function getUserTemplateParams() {
        $session = \Config\Services::session();

        $ownShopUrl = null;

        if ($session->get('shop_id') > 0) {
            $ownShopUrl = "/shop/{$session->get('shop_id')}";
        }

        $cart = $this->buildCart();

        $params = [ 
            'logged_in' => !!$session->get('username'),
            'user' => [
                'id' => $session->get('user_id'),
                'first_name' => $session->get('first_name'),
                'last_name' => $session->get('last_name'),
                'username' => $session->get('username'),
                'has_shop' => $session->get('has_shop'),
                'shop_name' => $session->get('shop_name'),
                'shop_url' => $ownShopUrl
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