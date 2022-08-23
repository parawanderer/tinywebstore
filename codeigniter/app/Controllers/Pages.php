<?php

namespace App\Controllers;

class Pages extends BaseController
{
    public function index()
    {
        return view('welcome_message');
    }


    public function view($page = 'index') {

        $data['title'] = ucfirst($page);

        return view('templates/header', $data) . view('pages/home') . view('templates/footer');
    }
}