<?php

namespace App\Controllers;

use CodeIgniter\Exceptions\PageNotFoundException;
use Exception;

class Cart extends AppBaseController
{
    public function index()
    {
        $templateParams = $this->getUserTemplateParams();

        return view('templates/header')
            . view('templates/top_bar', $templateParams)
            . view('cart/index', $templateParams)
            . view('templates/footer');
    }

    public function add() {
        $validationRules = [
            "productQuantity" => "required|integer|greater_than_equal_to[1]",
            "productId" => "required|integer"
        ];

        if (!$this->validate($validationRules))
            throw new Exception("Bad request");

        $productId = $this->request->getPost("productId");
        $quantity = $this->request->getPost("productQuantity");

        /** @var \App\Models\ProductModel */
        $productModel = model(ProductModel::class);

        $product = $productModel->getById($productId);
        if (!$product) throw new PageNotFoundException("Product does not exist");

        if ($product['availability'] == 0)
            return redirect()->to("/product/{$productId}");

        $session = \Config\Services::session();

        $cart = $session->get("cart") ?? [];
        $cart[$productId] = ($cart[$productId] ?? 0 ) + $quantity;
        $session->set("cart", $cart);

        return redirect()->to("/product/{$productId}");
    }

    public function remove(int $productId = -1) {
        if ($productId <= -1)
            throw new Exception("Bad request");

        $session = \Config\Services::session();

        $cart = $session->get("cart") ?? [];
        unset($cart[$productId]);
        $session->set("cart", $cart);

        $referrer = $this->request->header("Referer");

        if ($referrer) {
            return redirect()->to($referrer->getValue(), 302, 'refresh'); // send back
        }

        return redirect()->to("/cart");
    }
}
