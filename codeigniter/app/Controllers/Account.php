<?php

namespace App\Controllers;

use Error;
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
        }

        $templateParams = $this->getUserTemplateParams();

        return view('templates/header')
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

            if ($moreThanOneParam) {
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

                    if (empty($user)) {
                        throw new Exception("Internal Server Error occurred. Try again later");
                    }

                    $this->loginUser($user);
                    $registerParams['success'] = true;

                } else {
                    $registerParams['error'] = true;
                }

            } else {

                if ($model->accountExists($email)) {
                    $registerParams['already_exists'] = true;
                }

                $registerParams['email'] = $email;
            }
        }

        $templateParams = $this->getUserTemplateParams();
        return view('templates/header')
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

        $templateParams['user_address'] = $currentUser['address'];

        return view('templates/header')
            . view('templates/top_bar', $templateParams)
            . view('account/index', $templateParams)
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
            'shop_name' => $userData['shop_name']
        ]);
    }
}