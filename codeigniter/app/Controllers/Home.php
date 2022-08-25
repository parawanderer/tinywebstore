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

        return view('templates/header')
            . view('templates/top_bar', $templateParams)
            . view('index', $templateParams)
            . view('templates/footer');
    }
}
