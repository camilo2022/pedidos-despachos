<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Traits\ApiMessage;
use App\Traits\ApiResponser;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    use ApiResponser;
    use ApiMessage;

    public function store(Request $request)
    {
        return $request;
    }
}
