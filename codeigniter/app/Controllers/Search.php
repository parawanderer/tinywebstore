<?php

namespace App\Controllers;

use App\Models\ProductModel;
use Exception;

class Search extends AppBaseController
{
    public function index() {
        helper('text');

        $validationRules = [
            'q' => 'required',
            'stock' => 'if_exist|integer|in_list[0, 1]', // must be "In stock" or not
            'rc' => 'if_exist|integer|greater_than_equal_to[0]', // Review Count
            'rs' => 'if_exist|integer|in_list[1, 2, 3, 4, 5]', // review min score
            'cmin' => 'if_exist|integer|greater_than_equal_to[0]', // price/cost minimum
            'cmax' => 'if_exist|integer|greater_than_equal_to[0]', // price maximum
            'sort' => 'if_exist|integer|in_list[0, 1, 2, 3]' // sort order (see SORT_... constants)
        ];

        if (!$this->validate($validationRules)) {
            return redirect()->to("/");
        }

        $title = trim($this->request->getGet("q"));

        if (empty($title))
            throw new Exception("Bad request");

        $mustBeInStock = $this->request->getGet("stock");
        $reviewCount = $this->request->getGet("rc");
        $reviewScoreMin = $this->request->getGet("rs");
        // i'm not validating min < max
        // if someone were to get around the validating JS and request min > max
        // then it's their own fault they get 0 results in this toy project
        // in the real world however we may want to save the query by erroring or w/e
        $costMin = $this->request->getGet("cmin");
        $costMax = $this->request->getGet("cmax");
        $sortOption = $this->request->getGet("sort");


        /** @var \App\Models\ProductModel */
        $productModel = model(ProductModel::class);
        [$results, $totalMatches] = $productModel->nameSearchExtended($title, 20, [
            'mustBeInStock' => $mustBeInStock ?? false,
            'reviewCount' => $reviewCount ?? -1,
            'reviewScoreMin' => $reviewScoreMin ?? -1,
            'costMin' => $costMin ?? -1,
            'costMax' => $costMax ?? -1,
            'sortOption' => $sortOption ?? ProductModel::SORT_ALPHABETICAL
        ]);

        $templateParams = $this->getUserTemplateParams();
        $templateParams['results'] = $results;
        $templateParams['total'] = $totalMatches;
        $templateParams['query'] = [
            'term' => $title,
            'mustBeInStock' => $mustBeInStock,
            'reviewCount' => $reviewCount,
            'reviewScoreMin' => $reviewScoreMin,
            'costMin' => $costMin,
            'costMax' => $costMax,
            'sortOption' => $sortOption
        ];
        $templateParams['title'] = 'Search';

        return view('templates/header', $templateParams)
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