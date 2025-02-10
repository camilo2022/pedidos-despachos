<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class POSController extends Controller
{
    public function index()
    {
        return view('Dashboard.POS.Index');
    }
}
