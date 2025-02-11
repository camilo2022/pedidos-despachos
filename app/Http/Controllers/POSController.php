<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\PaymentMethod;
use Exception;

class POSController extends Controller
{
    public function index()
    {
        try {
            $payment_methods = PaymentMethod::get();
            
            return view('Dashboard.POS.Index', compact('payment_methods'));
        } catch (Exception $e) {
            return back()->with('danger', 'Ocurrió un error al cargar la vista: ' . $e->getMessage());
        }
    }
}
