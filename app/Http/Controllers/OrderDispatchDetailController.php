<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\OrderDispatchDetail\OrderDispatchDetailCancelRequest;
use App\Http\Requests\OrderDispatchDetail\OrderDispatchDetailIndexQueryRequest;
use App\Http\Requests\OrderDispatchDetail\OrderDispatchDetailPendingRequest;
use App\Models\OrderDispatch;
use App\Models\OrderDispatchDetail;
use App\Models\Size;
use App\Traits\ApiMessage;
use App\Traits\ApiResponser;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;

class OrderDispatchDetailController extends Controller
{
    use ApiResponser;
    use ApiMessage;

    public function __construct()
    {
        $this->middleware('check.order.picking');
        $this->middleware('check.order.packing');
    }

    public function index($id)
    {
        try {
            $orderDispatch = OrderDispatch::with([ 'invoices', 'order_dispatch_details.order_detail',
                'client' => fn($query) => $query->withTrashed(),
                'dispatch_user' => fn($query) => $query->withTrashed(),
                'invoice_user' => fn($query) => $query->withTrashed(),
                'correria' => fn($query) => $query->withTrashed(),
                'business' => fn($query) => $query->withTrashed(),
                'order_picking.picking_user' => fn($query) => $query->withTrashed(),
                'order_packing.packing_user' => fn($query) => $query->withTrashed()
            ])->findOrFail($id);

            return view('Dashboard.OrderDispatchDetails.Index', compact('orderDispatch'));
        } catch (ModelNotFoundException $e) {
            return back()->with('danger', 'Ocurrió un error al buscar la orden de despacho: ' . $this->getMessage('ModelNotFoundException'));
        } catch (Exception $e) {
            return back()->with('danger', 'Ocurrió un error al cargar la vista: ' . $e->getMessage());
        }
    }

    public function indexQuery(OrderDispatchDetailIndexQueryRequest $request)
    {
        try {
            $orderDispatch = OrderDispatch::with([
                    'order_packing', 'invoices',
                    'order_dispatch_details.order.seller_user' => fn($query) => $query->withTrashed(),
                    'order_dispatch_details.order.wallet_user' => fn($query) => $query->withTrashed(),
                    'client' => fn($query) => $query->withTrashed(),
                    'correria' => fn($query) => $query->withTrashed(),
                    'dispatch_user' => fn($query) => $query->withTrashed(),
                    'invoice_user' => fn($query) => $query->withTrashed(),
                    'order_dispatch_details.order_detail.product' => fn($query) => $query->withTrashed(),
                    'order_dispatch_details.order_detail.color' => fn($query) => $query->withTrashed(),
                    'order_dispatch_details.order_detail.seller_user' => fn($query) => $query->withTrashed(),
                    'order_dispatch_details.order_detail.wallet_user' => fn($query) => $query->withTrashed()
                ])
                ->findOrFail($request->input('order_dispatch_id'));

            $orderDispatchSizes = collect([]);

            $sizes = Size::all();

            foreach($sizes as $size) {
                if($orderDispatch->order_dispatch_details->pluck("T{$size->code}")->sum() > 0) {
                    $orderDispatchSizes = $orderDispatchSizes->push($size);
                }
            }

            return $this->successResponse(
                [
                    'orderDispatch' => $orderDispatch,
                    'sizes' => $orderDispatchSizes->isNotEmpty() ? $orderDispatchSizes : $sizes
                ],
                $this->getMessage('Success'),
                200
            );
        } catch (QueryException $e) {
            // Manejar la excepción de la base de datos
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

    public function pending(OrderDispatchDetailPendingRequest $request)
    {
        try {
            $orderDispatchDetail = OrderDispatchDetail::with('order_detail')->findOrFail($request->input('id'));
            $orderDispatchDetail->order_detail->status = 'Comprometido';
            $orderDispatchDetail->order_detail->save();
            $orderDispatchDetail->status = 'Pendiente';
            $orderDispatchDetail->save();

            return $this->successResponse(
                [
                    'orderDispatchDetail' => $orderDispatchDetail
                ],
                'El detalle orden de despacho fue devuelto exitosamente.',
                200
            );
        } catch (ModelNotFoundException $e) {
            return $this->errorResponse(
                [
                    'message' => $this->getMessage('ModelNotFoundException'),
                    'error' => $e->getMessage()
                ],
                404
            );
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

    public function cancel(OrderDispatchDetailCancelRequest $request)
    {
        try {
            $orderDispatchDetail = OrderDispatchDetail::with('order_detail.order')->findOrFail($request->input('id'));
            $orderDispatchDetail->order_detail->status = 'Aprobado';
            $orderDispatchDetail->order_detail->save();
            $orderDispatchDetail->status = 'Cancelado';
            $orderDispatchDetail->save();

            DB::statement('CALL order_dispatch_status(?)', [$orderDispatchDetail->order_detail->order->id]);

            return $this->successResponse(
                [
                    'orderDispatchDetail' => $orderDispatchDetail
                ],
                'El detalle orden de despacho fue cancelado exitosamente.',
                200
            );
        } catch (ModelNotFoundException $e) {
            return $this->errorResponse(
                [
                    'message' => $this->getMessage('ModelNotFoundException'),
                    'error' => $e->getMessage()
                ],
                404
            );
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
