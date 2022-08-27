<?php

namespace App\Controllers;

use App\Models\ProductModel;

class Home extends AppBaseController
{
    public function index()
    {
        /** @var \App\Models\ProductModel */
        $productModel = model(ProductModel::class);

        $products = $productModel->getNewest(8);

        $seenProductIds = $this->getSeenProductIds();
        $seenProducts = $productModel->getSortedByIds($seenProductIds);

        $templateParams = $this->getUserTemplateParams();
        $templateParams['products'] = $products;
        $templateParams['products_seen'] = $seenProducts;
        $templateParams['title'] = 'Home';

        return view('templates/header', $templateParams)
            . view('templates/top_bar', $templateParams)
            . view('index', $templateParams)
            . view('templates/footer');
    }

    private function getSeenProductIds() {
        $session = \Config\Services::session();
        return $session->get("product_history") ?? [];
    }
}
