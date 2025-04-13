<?php

namespace App\Controllers;

class Home extends BaseController
{
    public function index(): string
    {
        $pageData = [
            'page_title' => 'True Path Leads API',
        ];
        return view('api_ui', $pageData);
    }
}
