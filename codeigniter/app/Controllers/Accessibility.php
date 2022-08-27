<?php

namespace App\Controllers;

class Accessibility extends AppBaseController
{
    public function index()
    {
        $templateParams = $this->getUserTemplateParams();
        $templateParams['title'] = 'Accessibility Statement';

        return view('templates/header', $templateParams)
            . view('templates/top_bar', $templateParams)
            . view('accessibility', $templateParams)
            . view('templates/footer');
    }
}
