<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class VendorIntegrationController extends Controller
{
    public function index()
    {
        return view('admin.vendors');
    }
}
