<?php

namespace App\Controllers;

use App\Models\AccountModel;
use App\Models\OrderModel;
use App\Models\ShopModel;
use CodeIgniter\Exceptions\PageNotFoundException;
use Exception;

class Cart extends AppBaseController
{
    public function index()
    {
        $userDetails = null;
        $shopDetails = [];

        if ($this->loggedIn()) {
            /** @var \App\Models\AccountModel */
            $accountModel = model(AccountModel::class);
            $userDetails = $accountModel->getUserById($this->getCurrentUserId());

            /** @var \App\Models\ShopModel */
            $shopModel = model(ShopModel::class); 
            $shopDetails = $shopModel->getShops($this->getShopsInCartIDs());
        }

        $templateParams = $this->getUserTemplateParams();
        $templateParams['userDetails'] = $userDetails;
        $templateParams['shops'] = $shopDetails;
        $templateParams['priceTotal'] = $this->countTotalForAvailability();

        return view('templates/header')
            . view('templates/top_bar', $templateParams)
            . view('cart/index', $templateParams)
            . view('templates/footer');
    }

    
    private function success() {
        // unused
        $templateParams = $this->getUserTemplateParams();

        return view('templates/header')
            . view('templates/top_bar', $templateParams)
            . view('cart/success', $templateParams)
            . view('templates/footer');
    }

    public function checkout() {
        if (!$this->loggedIn()) 
            throw new Exception("Not logged in");

        $validationRules = [
            "deliveryType" => "required|integer|in_list[0, 1]",
            "address" => "required_without[pickupTime]",
            "pickupTime" => "required_without[address]|regex_match[/^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}$|^$/]"
        ];

        if (!$this->validate($validationRules))
            throw new Exception("Bad request");

        [ $deliveryType, $deliveryAddress, $deliveryPickupTime ] = $this->validateDeliveryData();
        
        // has cart items?
        $cartItemsToCounts = $this->getCartItemsToCounts();
        if (count($cartItemsToCounts) === 0) 
            throw new Exception("Cannot check out no items");

        /** @var \App\Models\OrderModel */
        $orderModel = model(OrderModel::class);
        $orderModel->startOrderTransaction();

        // take what we can (may be weird if the count is less than was initially shown in cart page. but since this isn't a real project i'll keep it this way)
        // I'm also not paying too much attention to the DB transaction logic here. I don't think thi is part of the exercise
        // the optimal solution in this case in my opinion is potentially:
        // - row-based locking on the products (essentially done here)
        // - serialized snapshot isolation to handle conflicts "automagically" and abort the later-completed "invalidated" transactions
        
        [$purchaseEntries, $purchaseValue] = $this->updateProductAvailabilities($cartItemsToCounts);

        // add order
        $orderModel->createOrder(
            $this->getCurrentUserId(),
            $purchaseValue,
            $purchaseEntries,
            $deliveryType,
            $deliveryAddress,
            $deliveryPickupTime
        );

        // complete transaction
        $result = $orderModel->completeOrderTransaction();


        if (!$result)
            throw new Exception("Failed to complete purchase");

        // reset cart
        $session = \Config\Services::session();
        $session->set("cart", []);

        $templateParams = $this->getUserTemplateParams();
        $templateParams['order_value'] = $purchaseValue;

        return view('templates/header')
            . view('templates/top_bar', $templateParams)
            . view('cart/success', $templateParams)
            . view('templates/footer');
    }

    private function updateProductAvailabilities(array &$cartItemsToCounts) {
        // get final counts on needed products

        /** @var \App\Models\ProductModel */
        $productModel = model(ProductModel::class);
        $products = $productModel->getProductsByIdsForUpdate(array_keys($cartItemsToCounts));

        $purchases = [];
        $totalValue = 0.0;

        $productsToNewAvailabilities = [];
        foreach($products as $product) {
            $purchaseTotal = $cartItemsToCounts[$product['id']];
            
            if ($purchaseTotal > $product['availability']) {
                $purchaseTotal = $product['availability'];
            }

            // for us
            $purchases[] = [
                'quantity' => $purchaseTotal,
                'product_id' => $product['id'],
                'shop_id' => $product['shop_id'],
                'price_per_unit' => $product['price'],
                'product_name' => $product['title']
            ];
            $totalValue += ($purchaseTotal * $product['price']);

            // for them
            $productsToNewAvailabilities[$product['id']] = $product['availability'] - $purchaseTotal;
        }

        // decrement totals
        $productModel->updateProductAvailabilities($productsToNewAvailabilities);

        return [$purchases, $totalValue];
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

    private function getShopsInCartIDs() {
        $cart = $this->getCart();

        // set imitation because composer is being difficult
        $stores = [];

        foreach($cart as $productInfo) {
            $stores[$productInfo['shop_id']] = true;
        }

        return array_keys($stores);
    }

    private function validateDeliveryData() {
        // validate inputs
        $deliveryType = $this->request->getPost("deliveryType");
        $deliveryAddress = $this->request->getPost("address");
        $deliveryPickupTime = $this->request->getPost("pickupTime");

        if ($deliveryType == OrderModel::TYPE_DELIVERY) {
            if (empty($deliveryAddress)) throw new Exception("Bad request");
            $deliveryPickupTime = null;
        }
            
        if ($deliveryType == OrderModel::TYPE_PICKUP) {
            if (empty($deliveryPickupTime)) throw new Exception("Bad request");
            $deliveryAddress = null;
            $deliveryPickupTime = strtotime($deliveryPickupTime);
        }

        return [$deliveryType, $deliveryAddress, $deliveryPickupTime];
    }

    private function countTotalForAvailability() {
        $cart = $this->getCart();
        $total = 0.0;
        
        foreach($cart as $productInfo) {
            $total += ($productInfo['price'] * $productInfo['quantity']);
        }

        return $total;
    }
}
