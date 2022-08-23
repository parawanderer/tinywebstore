<?php

namespace App\Controllers;

class AppBaseController extends BaseController
{
    protected function getUserTemplateParams() {
        $session = \Config\Services::session();

        $params = [ 
            'logged_in' => !!$session->get('username'),
            'user' => [
                'first_name' => $session->get('first_name'),
                'last_name' => $session->get('last_name'),
                'username' => $session->get('username'),
                'has_shop' => $session->get('has_shop'),
                'shop_name' => $session->get('shop_name')
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
}