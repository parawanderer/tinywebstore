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

        $templateParams = $this->getUserTemplateParams();
        $templateParams['products'] = $products;
        $templateParams['title'] = 'Home';

        return view('templates/header', $templateParams)
            . view('templates/top_bar', $templateParams)
            . view('index', $templateParams)
            . view('templates/footer');
    }
}
