<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\OrderPacking\OrderPackingCloseRequest;
use App\Http\Requests\OrderPacking\OrderPackingIndexQueryRequest;
use App\Http\Requests\OrderPacking\OrderPackingOpenRequest;
use App\Http\Requests\OrderPacking\OrderPackingStoreRequest;
use App\Models\OrderPackage;
use App\Models\OrderPacking;
use App\Models\OrderPackingDetail;
use App\Models\PackageType;
use App\Models\Size;
use App\Traits\ApiMessage;
use App\Traits\ApiResponser;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\URL;

class OrderPackingController extends Controller
{
    use ApiResponser;
    use ApiMessage;

    public function index($id)
    {
        try {
            $orderPacking = OrderPacking::with([
                'order_dispatch.correria',
                'order_dispatch.client' => fn($query) => $query->withTrashed(), 
                'order_dispatch.order_dispatch_details.order_detail.product' => fn($query) => $query->withTrashed(), 
                'order_dispatch.order_dispatch_details.order_detail.color' => fn($query) => $query->withTrashed(),
                'packing_user' => fn($query) => $query->withTrashed()
            ])->findOrFail($id);

            return view('Dashboard.OrderPackings.Index', compact('orderPacking'));
        } catch (Exception $e) {
            return back()->with('danger', 'Ocurrió un error al cargar la vista: ' . $e->getMessage());
        }
    }

    public function indexQuery(OrderPackingIndexQueryRequest $request)
    {
        try {
            $orderPacking = OrderPacking::with([
                    'order_packages.order_packing_details.order_dispatch_detail.order_detail.product' => fn($query) => $query->withTrashed(),
                    'order_packages.order_packing_details.order_dispatch_detail.order_detail.color' => fn($query) => $query->withTrashed(),
                    'order_packages.package_type', 'order_dispatch.order_dispatch_details.order_packings_details',
                    'order_packages.order_packing_details.order_dispatch_detail.order_packings_details'
                ])
                ->findOrFail($request->input('id'));

            $orderDispatchSizes = collect([]);

            $sizes = Size::all();

            foreach($sizes as $size) {
                if($orderPacking->order_dispatch->order_dispatch_details->pluck("T{$size->code}")->sum() > 0) {
                    $orderDispatchSizes = $orderDispatchSizes->push($size);
                }
            }

            $orderPackage = $orderPacking->order_packages->where('package_status', 'Abierto')->first();

            if($orderPackage) {
                return $this->successResponse(
                    [
                        'orderPackage' => $orderPackage,
                        'sizes' => $orderDispatchSizes->isNotEmpty() ? $orderDispatchSizes : $sizes,
                        'status' => false
                    ],
                    $this->getMessage('Success'),
                    204
                );
            }
            
            $packageTypes = PackageType::all();

            return $this->successResponse(
                [
                    'orderPacking' => $orderPacking,
                    'sizes' => $orderDispatchSizes->isNotEmpty() ? $orderDispatchSizes : $sizes,
                    'status' => true,
                    'packageTypes' => $packageTypes
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

    public function store(OrderPackingStoreRequest $request)
    {
        try {
            $orderPacking = OrderPacking::with('order_dispatch.order_dispatch_details')->findOrFail($request->input('id'));

            $orderPackage = new OrderPackage();
            $orderPackage->order_packing_id = $orderPacking->id;
            $orderPackage->package_type_id = $request->input('package_type_id');
            $orderPackage->package_date = Carbon::now()->format('Y-m-d H:i:s');
            $orderPackage->save();

            foreach ($orderPacking->order_dispatch->order_dispatch_details->whereIn('status', ['Empacado']) as $order_dispatch_detail) {
                $orderPackingDetail = new OrderPackingDetail();
                $orderPackingDetail->order_package_id = $orderPackage->id;
                $orderPackingDetail->order_dispatch_detail_id = $order_dispatch_detail->id;
                $orderPackingDetail->date = Carbon::now()->format('Y-m-d H:i:s');
                $orderPackingDetail->save();
            }

            return $this->successResponse(
                [
                    'orderPackage' => $orderPackage
                ],
                'El empaque de la orden de empacado fue creado exitosamente.',
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

    public function open(OrderPackingOpenRequest $request)
    {
        try {
            $orderPackage = OrderPackage::findOrFail($request->input('order_package_id'));
            $orderPackage->weight = null;
            $orderPackage->package_status = 'Abierto';
            $orderPackage->package_date = Carbon::now()->format('Y-m-d H:i:s');
            $orderPackage->save();

            return $this->successResponse(
                [
                    'orderPackage' => $orderPackage
                ],
                'El empaque de la orden de empacado fue abierto exitosamente.',
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

    public function close(OrderPackingCloseRequest $request)
    {
        try {
            $orderPackage = OrderPackage::with('order_packing.order_dispatch.order_dispatch_details.order_packings_details')->findOrFail($request->input('order_package_id'));
            $orderPackage->weight = $request->input('weight');
            $orderPackage->package_status = 'Cerrado';
            $orderPackage->package_date = Carbon::now()->format('Y-m-d H:i:s');
            $orderPackage->save();

            $sizes = Size::all();
            $boolean = true;

            foreach($orderPackage->order_packing->order_dispatch->order_dispatch_details as $order_dispatch_detail) {
                foreach ($sizes as $size) {
                    if($order_dispatch_detail->{"T{$size->code}"} > $order_dispatch_detail->order_packings_details->pluck("T{$size->code}")->sum()) {
                        $boolean = false;
                        break;
                    }
                }
                if(!$boolean) {
                    break;
                }
            }

            if($boolean) {
                $orderPackage->order_packing->packing_status = 'Aprobado';
                $orderPackage->order_packing->packing_date = Carbon::now()->format('Y-m-d H:i:s');
                $orderPackage->order_packing->save();

                $orderPackage->order_packing->order_dispatch->dispatch_status = 'Facturacion';
                $orderPackage->order_packing->order_dispatch->save();

                $orderPackage->order_packing->order_dispatch->order_dispatch_details()->whereIn('status', ['Empacado'])->update(['status' => 'Facturacion', 'date' => Carbon::now()->format('Y-m-d H:i:s')]);
            }

            return $this->successResponse(
                [
                    'orderPackage' => $orderPackage,
                    'urlOrderDispatches' => $boolean ? URL::route('Dashboard.Dispatches.Index') : null
                ],
                $boolean ? 'El empaque de la orden de empacado fue cerrado exitosamente.' : 'La orden de despacho terminó de empecarse exitosamente.',
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
