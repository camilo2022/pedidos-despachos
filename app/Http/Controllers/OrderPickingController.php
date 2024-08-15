<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\OrderPicking\OrderPickingApproveRequest;
use App\Http\Requests\OrderPicking\OrderPickingCancelRequest;
use App\Http\Requests\OrderPicking\OrderPickingIndexQueryRequest;
use App\Http\Requests\OrderPicking\OrderPickingReviewRequest;
use App\Models\OrderPicking;
use App\Models\Size;
use App\Traits\ApiMessage;
use App\Traits\ApiResponser;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\URL;

class OrderPickingController extends Controller
{
    use ApiResponser;
    use ApiMessage;

    public function index($id)
    {
        try {
            $orderPicking = OrderPicking::with([
                'order_dispatch.correria',
                'order_dispatch.client' => fn($query) => $query->withTrashed(), 
                'order_dispatch.order_dispatch_details.order_detail.product' => fn($query) => $query->withTrashed(), 
                'order_dispatch.order_dispatch_details.order_detail.color' => fn($query) => $query->withTrashed(),
                'picking_user' => fn($query) => $query->withTrashed()
            ])->findOrFail($id);

            return view('Dashboard.OrderPickings.Index', compact('orderPicking'));
        } catch (Exception $e) {
            return back()->with('danger', 'Ocurrió un error al cargar la vista: ' . $e->getMessage());
        }
    }

    public function indexQuery(OrderPickingIndexQueryRequest $request)
    {
        try {
            $orderPicking = OrderPicking::with([
                    'order_picking_details.order_dispatch_detail.order_detail.product' => fn($query) => $query->withTrashed(),
                    'order_picking_details.order_dispatch_detail.order_detail.color' => fn($query) => $query->withTrashed(),
                    'order_dispatch.order_dispatch_details'
                ])
                ->findOrFail($request->input('id'));

            $orderDispatchSizes = collect([]);

            $sizes = Size::all();

            foreach($sizes as $size) {
                if($orderPicking->order_dispatch->order_dispatch_details->pluck("T{$size->code}")->sum() > 0) {
                    $orderDispatchSizes = $orderDispatchSizes->push($size);
                }
            }

            return $this->successResponse(
                [
                    'orderPicking' => $orderPicking,
                    'sizes' => $orderDispatchSizes->isNotEmpty() ? $orderDispatchSizes : $sizes
                ],
                $this->getMessage('Success'),
                204
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

    public function approve(OrderPickingApproveRequest $request)
    {
        try {
            $orderPicking = OrderPicking::with('order_picking_details.order_dispatch_detail', 'order_dispatch.order_dispatch_details')->findOrFail($request->input('id'));

            $sizes = Size::all();

            foreach ($orderPicking->order_picking_details as $order_picking_detail) {
                $boolean = true;
                foreach($sizes as $size) {
                    if($order_picking_detail->{"T{$size->code}"} < $order_picking_detail->order_dispatch_detail->{"T{$size->code}"} && $boolean) {
                        $boolean = false;
                    }
                    $order_picking_detail->order_dispatch_detail->{"T{$size->code}"} = $order_picking_detail->{"T{$size->code}"};
                }
                $order_picking_detail->order_dispatch_detail->status = 'Empacado';
                $order_picking_detail->order_dispatch_detail->save();

                $order_picking_detail->status = $boolean ? 'Aprobado' : 'Revision';
                $order_picking_detail->save();
            }

            $orderPicking->order_dispatch->dispatch_status = 'Empacado';
            $orderPicking->order_dispatch->save();

            $orderPicking->picking_status = 'Aprobado';
            $orderPicking->picking_date = Carbon::now()->format('Y-m-d H:i:s');
            $orderPicking->save();

            return $this->successResponse(
                [
                    'orderPicking' => $orderPicking,
                    'urlOrderDispatches' => URL::route('Dashboard.Dispatches.Index')
                ],
                'La orden de alistamiento fue aprobada exitosamente.',
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

    public function review(OrderPickingReviewRequest $request)
    {
        try {
            $orderPicking = OrderPicking::with('order_picking_details.order_dispatch_detail', 'order_dispatch.order_dispatch_details')->findOrFail($request->input('id'));
            
            $orderPicking->order_picking_details()->update(['status' => 'Revision', 'date' => Carbon::now()->format('Y-m-d H:i:s')]);

            $orderPicking->order_dispatch->dispatch_status = 'Revision';
            $orderPicking->order_dispatch->save();

            $orderPicking->order_dispatch->order_dispatch_details()->where('status', 'Alistamiento')->update(['status' => 'Revision', 'date' => Carbon::now()->format('Y-m-d H:i:s')]);

            $orderPicking->picking_status = 'Revision';
            $orderPicking->picking_date = Carbon::now()->format('Y-m-d H:i:s');
            $orderPicking->save();

            return $this->successResponse(
                [
                    'orderPicking' => $orderPicking,
                    'urlOrderDispatches' => URL::route('Dashboard.Dispatches.Index')
                ],
                'La orden de alistamiento fue a revision exitosamente.',
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

    public function cancel(OrderPickingCancelRequest $request)
    {
        try {
            $orderPicking = OrderPicking::with('order_picking_details.order_dispatch_detail', 'order_dispatch.order_dispatch_details')->findOrFail($request->input('id'));

            $orderPicking->order_dispatch->dispatch_status = 'Alistamiento';
            $orderPicking->order_dispatch->save();

            $orderPicking->order_dispatch->order_dispatch_details()->whereIn('status', ['Alistamiento', 'Revision'])->update(['status' => 'Alistamiento', 'date' => Carbon::now()->format('Y-m-d H:i:s')]);

            foreach ($orderPicking->order_picking_details as $order_picking_detail) {
                $order_picking_detail->status = 'Cancelado';
                $order_picking_detail->save();
                $order_picking_detail->delete();
            }

            $orderPicking->picking_status = 'Cancelado';
            $orderPicking->picking_date = Carbon::now()->format('Y-m-d H:i:s');
            $orderPicking->save();
            $orderPicking->delete();

            return $this->successResponse(
                [
                    'orderPicking' => $orderPicking,
                    'urlOrderDispatches' => URL::route('Dashboard.Dispatches.Index')
                ],
                'La orden de alistamiento fue cancelada exitosamente.',
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
