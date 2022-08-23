<?php

namespace App\Controllers;

class AppBaseController extends BaseController
{
    protected function getUserTemplateParams() {
        $session = \Config\Services::session();

        $ownShopUrl = null;

        if ($session->get('shop_id') > 0) {
            $ownShopUrl = "/shop/{$session->get('shop_id')}";
        }

        $params = [ 
            'logged_in' => !!$session->get('username'),
            'user' => [
                'first_name' => $session->get('first_name'),
                'last_name' => $session->get('last_name'),
                'username' => $session->get('username'),
                'has_shop' => $session->get('has_shop'),
                'shop_name' => $session->get('shop_name'),
                'shop_url' => $ownShopUrl
            ],
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
}