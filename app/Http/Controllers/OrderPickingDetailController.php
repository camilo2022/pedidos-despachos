<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\OrderPickingDetail\OrderPickingDetailAddRequest;
use App\Models\OrderPickingDetail;
use App\Traits\ApiMessage;
use App\Traits\ApiResponser;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;

class OrderPickingDetailController extends Controller
{
    use ApiResponser;
    use ApiMessage;

    public function add(OrderPickingDetailAddRequest $request)
    {
        try {
            $orderPickingDetail = OrderPickingDetail::with('order_picking', 'order_dispatch_detail')->findOrFail($request->input('id'));
            $orderPickingDetail->{"T{$request->input('size')}"} += $request->input('quantity');
            $orderPickingDetail->date = Carbon::now()->format('Y-m-d H:i:s');
            $orderPickingDetail->save();

            return $this->successResponse(
                [
                    'orderPickingDetail' => $orderPickingDetail,
                ],
                'Unidad añadida al detalle de la orden de alistamiento exitosamente.',
                204
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
