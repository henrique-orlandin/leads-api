<?php

namespace App\Controllers;

class Home extends BaseController
{
    public function index(): string
    {
        return view('import_leads_form');
    }
}
