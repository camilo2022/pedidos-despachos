<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Product;
use App\Models\User;
use App\Traits\ApiMessage;
use App\Traits\ApiResponser;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    use ApiMessage;
    use ApiResponser;

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $user_id = Auth::user()->id;

        $clients = Client::count();
        $users = User::count();
        $orders = Order::count();
        $products = Product::count();

        $details = OrderDetail::select(
            'order_details.*',
            DB::raw("(T04 + T06 + T08 + T10 + T12 + T14 + T16 + T18 + T20 + T22 + T24 + T26 + T28 + T30 + T32 + T34 + T36 + T38 + TXXS + TXS + TS + TM + TL + TXL + TXXL) AS TOTAL")
        )
        ->with([
            'order.seller_user', 'product', 'order.correria' => fn ($query) => $query->withTrashed(),
            'order_dispatch_detail' => fn ($query) => $query->select('order_dispatch_details.*', DB::raw("(T04 + T06 + T08 + T10 + T12 + T14 + T16 + T18 + T20 + T22 + T24 + T26 + T28 + T30 + T32 + T34 + T36 + T38 + TXXS + TXS + TS + TM + TL + TXL + TXXL) AS TOTAL")),
        ])
        ->whereHas('order', fn ($query) => $query->where('business_id', Auth::user()->business_id))
        ->whereHas('order.correria', fn ($query) => $query->whereNull('deleted_at'))
        ->get();

        return view('Dashboard.home', compact('clients', 'users', 'orders', 'products', 'details'));
    }
}
