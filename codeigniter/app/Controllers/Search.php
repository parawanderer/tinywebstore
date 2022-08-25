<?php

namespace App\Controllers;

use App\Models\ProductModel;

class Search extends AppBaseController
{
    public function index() {
        if (!$this->validate(['q' => 'required'])) {
            return redirect()->to("/");
        }

        $title = $this->request->getGet("q");

        /** @var \App\Models\ProductModel */
        $productModel = model(ProductModel::class);
        $results = $productModel->nameSearch($title, 20);

        $templateParams = $this->getUserTemplateParams();
        $templateParams['results'] = $results;

        return view('templates/header')
            . view('templates/top_bar', $templateParams)
            . view('search/index', $templateParams)
            . view('templates/footer');
    }

    // ajax query, i'll just have this render HTML...
    public function quickSearch() {
        if (!$this->validate(['q' => 'required'])) {
            return redirect()->to("/");
        }

        $title = $this->request->getGet("q");

        /** @var \App\Models\ProductModel */
        $productModel = model(ProductModel::class);
        $results = $productModel->nameSearch($title, 6);


        $templateParams = [
            'results' => $results
        ];

        return view('search/quicksearch', $templateParams);
    }
}