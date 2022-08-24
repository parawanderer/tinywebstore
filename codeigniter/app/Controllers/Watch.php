<?php

namespace App\Controllers;

use App\Models\ProductModel;
use App\Models\WatchlistModel;
use CodeIgniter\Exceptions\PageNotFoundException;
use Exception;

class Watch extends AppBaseController
{
    public function watch(int $productId) {
        if (!$this->loggedIn()) return redirect()->to("/account/login");

        if ($productId <= -1)
            throw new Exception("Bad request");

        /** @var \App\Models\ProductModel */
        $productModel = model(ProductModel::class);

        $product = $productModel->getById($productId);
        if (!$product) throw new PageNotFoundException("Product does not exist");


        /** @var \App\Models\WatchlistModel */
        $watchlistModel = model(WatchlistModel::class);
        $watchlistModel->addToWatchlist($this->getCurrentUserId(), $product['id'], $product['title']);


        $referrer = $this->request->header("Referer");

        if ($referrer) {
            return redirect()->to($referrer->getValue(), 302, 'refresh'); // send back
        }

        return redirect()->to("/");
    }

    public function unwatch(int $productId) {
        if (!$this->loggedIn()) return redirect()->to("/account/login");

        if ($productId <= -1)
            throw new Exception("Bad request");

        /** @var \App\Models\WatchlistModel */
        $watchlistModel = model(WatchlistModel::class);
        $watchlistModel->removeFromWatchList($this->getCurrentUserId(), $productId);

        
        $referrer = $this->request->header("Referer");

        if ($referrer) {
            return redirect()->to($referrer->getValue(), 302, 'refresh'); // send back
        }

        return redirect()->to("/");
    }
}