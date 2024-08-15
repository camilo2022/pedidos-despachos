<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\OrderPackingDetail\OrderPackingDetailAddRequest;
use App\Models\OrderPackingDetail;
use App\Traits\ApiMessage;
use App\Traits\ApiResponser;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;

class OrderPackingDetailController extends Controller
{
    use ApiResponser;
    use ApiMessage;

    public function add(OrderPackingDetailAddRequest $request)
    {
        try {
            $orderPackingDetail = OrderPackingDetail::with('order_dispatch_detail.order_packings_details')->findOrFail($request->input('id'));
            $orderPackingDetail->{"T{$request->input('size')}"} = $request->input('quantity');
            $orderPackingDetail->date = Carbon::now()->format('Y-m-d H:i:s');
            $orderPackingDetail->save();

            return $this->successResponse(
                [
                    'orderPackingDetail' => $orderPackingDetail,
                ],
                'Unidad añadida al empaque de la orden de empacado exitosamente.',
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
