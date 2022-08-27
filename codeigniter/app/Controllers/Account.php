<?php

namespace App\Controllers;

use App\Helpers\AlertHelper;
use App\Models\AccountModel;
use App\Models\MessageChainModel;
use App\Models\MessageModel;
use App\Models\OrderModel;
use App\Models\ShopModel;
use CodeIgniter\Exceptions\PageNotFoundException;
use Exception;

class Account extends AppBaseController
{
    public function login() {
        $loginRules = [
            'loginEmail' => 'required|valid_email',
            'loginPassword' => 'required'
        ];

        if ($this->request->getMethod() === 'post' && $this->validate($loginRules)) {
            /** @var \App\Models\AccountModel */
            $model = model(AccountModel::class);
            
            $loginUsername = $this->request->getPost('loginEmail');
            $loginPassword = $this->request->getPost('loginPassword');

            $user = $model->login($loginUsername, $loginPassword);
                
            if ($user) {
                $this->loginUser($user);
                $topBar['name'] = $user['first_name'];
            }

            return redirect()->to("/");
        }

        $templateParams = $this->getUserTemplateParams();
        $templateParams['title'] = 'Login';

        return view('templates/header', $templateParams)
            . view('templates/top_bar', $templateParams)
            . view('account/login')
            . view('templates/footer');
    }

    public function logout() {
        $session = \Config\Services::session();
        $session->destroy();

        return redirect()->to('/account/login');
    }

    public function register() {
        $redirectCreateEmailRules = [
            'email' => 'valid_email'
        ];

        $createUserRules = [
            'email' => 'required|valid_email',
            'firstName' => 'required',
            'lastName' => 'required',
            'address' => 'required',
            'city' => 'required',
            'zipCode' => 'required|numeric',
            'password' => 'required|strong_password',
            'repeatPassword' => 'required|matches[password]'
        ];

        $registerParams = [
            'already_exists' => false,
            'email' => null,
            'error' => false,
            'success' => false,
        ];

        if ($this->validate($redirectCreateEmailRules)) {
            /** @var \App\Models\AccountModel */
            $model = model(AccountModel::class);
            $moreThanOneParam = count($this->request->getPost()) > 1;
            $email = $this->request->getPost('email');
            $registerParams['email'] = $email;

            if ($model->accountExists($email)) {
                $registerParams['already_exists'] = true;
            } 
            else if ($moreThanOneParam) {
                // handle register
                
                if ($this->validate($createUserRules)) {
                    // register
                    $address = $this->request->getPost('address') . ', ' . $this->request->getPost('zipCode') . ' ' . $this->request->getPost('city');

                    $user = $model->register(
                        $email,
                        $this->request->getPost('firstName'),
                        $this->request->getPost('lastName'),
                        $address,
                        $this->request->getPost('password')
                    );

                    $this->loginUser($user);
                    $registerParams['success'] = true;

                } else {
                    $registerParams['error'] = true;
                }
            }
        }

        $templateParams = $this->getUserTemplateParams();
        $templateParams['title'] = 'Register Account';

        return view('templates/header', $templateParams)
            . view('templates/top_bar', $templateParams)
            . view('account/register', $registerParams)
            . view('templates/footer');
    }

    public function index() {
        if (!$this->loggedIn()) return redirect()->to('/account/login');

        /** @var \App\Models\AccountModel */
        $model = model(AccountModel::class);

        $currentUser = $model->getUser($this->getCurrentUsername());

        $templateParams = $this->getUserTemplateParams();
        $templateParams['page'] = '';
        $templateParams['user_address'] = $currentUser['address'];
        $templateParams['title'] = 'Account Info';

        return view('templates/header', $templateParams)
            . view('templates/top_bar', $templateParams)
            . view('account/index', $templateParams)
            . view('templates/footer');
    }

    public function orders() {
        if (!$this->loggedIn()) return redirect()->to('/account/login');

        /** @var \App\Models\OrderModel */
        $orderModel = model(OrderModel::class);

        $orderHistory = $orderModel->getOrdersForUser($this->getCurrentUserId());

        $templateParams = $this->getUserTemplateParams();
        $templateParams['page'] = 'orders';
        $templateParams['orders'] = $orderHistory;
        $templateParams['title'] = 'Account | My Orders';

        return view('templates/header', $templateParams)
            . view('templates/top_bar', $templateParams)
            . view('account/orders', $templateParams)
            . view('templates/footer');
    }

    public function order(int $orderId = -1) {
        if (!$this->loggedIn()) return redirect()->to('/account/login');

        /** @var \App\Models\OrderModel */
        $orderModel = model(OrderModel::class);

        $orderDetails = $orderModel->getOrderDetails($orderId);
        if (!$orderDetails || $orderDetails['user_id'] != $this->getCurrentUserId())
            throw new PageNotFoundException("Order could not be found");

        $templateParams = $this->getUserTemplateParams();
        $templateParams['page'] = 'orders';
        $templateParams['order'] = $orderDetails;
        $templateParams['title'] = "Account | Order #$orderId";
        
        return view('templates/header', $templateParams)
            . view('templates/top_bar', $templateParams)
            . view('account/order', $templateParams)
            . view('templates/footer');
    }

    //the count update should also have been a background process in a real app...
    public function orderCancel(int $orderId = -1) {
        if (!$this->loggedIn()) return redirect()->to('/account/login');

        /** @var \App\Models\OrderModel */
        $orderModel = model(OrderModel::class);

        $orderDetails = $orderModel->getOrderDetails($orderId);
        if (!$orderDetails || $orderDetails['user_id'] != $this->getCurrentUserId())
            throw new PageNotFoundException("Order could not be found");

        if ($orderDetails['status'] != 0)
            throw new Exception("Bad request");

        $orderModel->cancelOrder($orderId);
        $countMap = Account::getOrderEntriesToCountMap($orderDetails);

        // TRANSACTION: reset counts
        $orderModel->startOrderTransaction();

        /** @var \App\Models\ProductModel */
        $productModel = model(ProductModel::class);
        $products = $productModel->getProductsByIdsForUpdate(array_keys($countMap));
        
        $outOfStockResets = Account::mapNewAvailabilities($countMap, $products);
        $productModel->incrementProductAvailabilities($countMap);

        $result = $orderModel->completeOrderTransaction();
        // TRANSACTION END        

        if (!$result)
            throw new Exception("Internal server error");
        
        $alertHelper = new AlertHelper();
        $alertHelper->bulkWatchlistItemAvailableAlert($outOfStockResets);

        $referrer = $this->request->header("Referer");
        if ($referrer) {
            return redirect()->to($referrer->getValue(), 302, 'refresh'); // send back
        }

        return redirect()->to("/");
    }

    public function watchlist() {
        if (!$this->loggedIn()) return redirect()->to('/account/login');

        /** @var \App\Models\WatchlistModel */
        $watchlistModel = model(WatchlistModel::class);
        $watchlist = $watchlistModel->getUserWatchlist($this->getCurrentUserId());


        $templateParams = $this->getUserTemplateParams();
        $templateParams['page'] = 'watchlist';
        $templateParams['watchlist'] = $watchlist;
        $templateParams['title'] = 'Account | My Watchlist';

        return view('templates/header', $templateParams)
            . view('templates/top_bar', $templateParams)
            . view('account/watchlist', $templateParams)
            . view('templates/footer');
    }

    public function messages() {
        if (!$this->loggedIn()) return redirect()->to('/account/login');

        /** @var \App\Models\MessageChainModel */
        $model = model(MessageChainModel::class);

        $chains = [];

        if ($this->isShopOwner()) {
            $chains = $model->getMessageChainsForStore($this->getOwnedShopId());
        } else {
            $chains = $model->getMessageChainsForUser($this->getCurrentUserId());
        }

        $templateParams = $this->getUserTemplateParams();
        $templateParams['messages'] = $chains;
        $templateParams['is_shop'] = $this->isShopOwner();
        $templateParams['page'] = 'messages';
        $templateParams['title'] = 'Account | Messages';

        return view('templates/header', $templateParams)
            . view('templates/top_bar', $templateParams)
            . view('account/messages', $templateParams)
            . view('templates/footer');
    }

    public function initMessageChain() {
        if (!$this->loggedIn()) return redirect()->to('/account/login');

        $validationRules = [
            'to' => 'required|integer' 
        ];

        if (!$this->validate($validationRules))
            throw new Exception("Bad request");

        $targetShopId = $this->request->getGet("to");
        
        /** @var \App\Models\ShopModel */
        $shopModel = model(ShopModel::class);
        $shop = $shopModel->getShop($targetShopId);

        if (!$shop) throw new PageNotFoundException("Shop does not exist");

        /** @var \App\Models\MessageChainModel */
        $messageChainModel = model(MessageChainModel::class);
        $conversation = $messageChainModel->getConversation($this->getCurrentUserId(), $targetShopId);

        // existing
        if ($conversation) {
            return redirect()->to("/account/message/{$conversation['id']}");
        }

        // create new chain (not visible in shop's list until user messages the shop)
        $newConversationId = $messageChainModel->startNewChain($this->getCurrentUserId(), $targetShopId);
        return redirect()->to("/account/message/{$newConversationId}");
    }

    public function message(int $conversationId = -1) {
        if (!$this->loggedIn()) return redirect()->to('/account/login');
        
        /** @var \App\Models\MessageChainModel */
        $messageChainModel = model(MessageChainModel::class);
        $conversation = $messageChainModel->getConversationById($conversationId);

        if ($this->isShopOwner() && $conversation['shop_id'] != $this->getOwnedShopId())
            throw new Exception("Bad request");

        if (!$this->isShopOwner() && $conversation['user_id'] != $this->getCurrentUserId())
            throw new Exception("Bad request");
        
        /** @var \App\Models\MessageModel */
        $messageModel = model(MessageModel::class);

        /** @var \App\Models\ShopModel */
        $shopModel = model(ShopModel::class);
        $shop = $shopModel->getShop($conversation['shop_id']);

        /** @var \App\Models\AccountModel */
        $accountModel = model(AccountModel::class);
        $sender = $accountModel->getUserById($conversation['user_id']);

        if ($this->request->getMethod() === 'post' && $this->validate(['contentInput' => 'required'])) {
            // add message
            $now = time();
            $messageContent = $this->request->getPost('contentInput');

            $messageModel->addMessage(
                $conversationId, 
                $this->getCurrentUserId() == $conversation['user_id'],
                $sender['first_name'] . ' ' . $sender['last_name'],
                $shop['name'],
                $messageContent,
                $now
            );
            $messageChainModel->updateLastMessageTime($conversationId, $now);
        }

        $messages = $messageModel->getChainMessages($conversationId);

        $templateParams = $this->getUserTemplateParams();
        $templateParams['conversation'] = $conversation;
        $templateParams['shop'] = $shop;
        $templateParams['sender'] = $sender;
        $templateParams['messages'] = $messages;
        $templateParams['is_shop'] = $this->isShopOwner();
        $templateParams['page'] = 'messages';
        $templateParams['title'] = $this->isShopOwner() ? "Account | Messaging {$sender['first_name']} {$sender['last_name']}" : "Account | Messaging {$shop['name']}";

        return view('templates/header', $templateParams)
            . view('templates/top_bar', $templateParams)
            . view('account/message', $templateParams)
            . view('templates/footer');
    }

    private function loginUser(array $userData) {
        $session = \Config\Services::session();

        $session->set([
            'user_id' =>  $userData['id'],
            'username' => $userData['username'],
            'first_name' => $userData['first_name'],
            'last_name' => $userData['last_name'],
            'has_shop' => $userData['has_shop'],
            'shop_name' => $userData['shop_name'],
            'shop_id' => $userData['shop_id']
        ]);
    }

    private static function getOrderEntriesToCountMap(array &$orderDetails) {
        $result = [];

        foreach($orderDetails['entries'] as &$entry) {
            $result[$entry['product_id']] = $entry['quantity'];
        }

        return $result;
    }

    private static function mapNewAvailabilities(array &$countMap, array &$productDetails) {
        $nonZeroUpdateMap = [];

        foreach($productDetails as &$product) {
            $newAvailability = $product['availability'] + $countMap[$product['id']];
            $countMap[$product['id']] = $newAvailability;

            if ($product['availability'] == 0 && $newAvailability > 0) {
                $nonZeroUpdateMap[$product['id']] = $product['title'];
            }
        }

        return $nonZeroUpdateMap;
    }
}