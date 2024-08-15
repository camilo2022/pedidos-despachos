<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\OrderDetail;
use App\Models\Size;
use App\Traits\ApiMessage;
use App\Traits\ApiResponser;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ReportProductionsController extends Controller
{
    use ApiResponser;
    use ApiMessage;

    public function index()
    {
        try {
            $sizes = Size::all();
            return view('Dashboard.Reports.IndexProductions', compact('sizes'));
        } catch (Exception $e) {
            return back()->with('danger', 'Ocurrió un error al cargar la vista: ' . $e->getMessage());
        }
    }

    public function indexQuery()
    {
        try {
            $productions = OrderDetail::select(
                'order_details.*', DB::raw("(T04 + T06 + T08 + T10 + T12 + T14 + T16 + T18 + T20 + T22 + T24 + T26 + T28 + T30 + T32 + T34 + T36 + T38 + TXXS + TXS + TS + TM + TL + TXL + TXXL) AS TOTAL")
            )
            ->with([
                'product', 'color', 'seller_user', 'wallet_user', 'dispatch_user', 'order.client', 'order.seller_user', 'order.wallet_user', 'order.correria',
                'order_dispatch_detail.order_dispatch.dispatch_user', 'order_dispatch_detail.order_dispatch.invoice_user', 'order_dispatch_detail.user',
                'order_dispatch_detail' => fn($query) => $query->select('order_dispatch_details.*', DB::raw("(T04 + T06 + T08 + T10 + T12 + T14 + T16 + T18 + T20 + T22 + T24 + T26 + T28 + T30 + T32 + T34 + T36 + T38 + TXXS + TXS + TS + TM + TL + TXL + TXXL) AS TOTAL")),
            ])
            ->whereHas('order', fn($query) => $query->where('business_id', Auth::user()->business_id))
            ->get();

            return datatables()->of($productions)->toJson();
            
        } catch (QueryException $e) {
            return $this->errorResponse(
                [
                    'message' => $this->getMessage('QueryException'),
                    'error' => $e->getMessage()
                ],
                500
            );
        } catch (Exception $e) {
            return $this->errorResponse(
                [
                    'message' => $this->getMessage('Exception'),
                    'error' => $e->getMessage()
                ],
                500
            );
        }
    }
}
