<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\OrderDetail\OrderDetailAllowRequest;
use App\Http\Requests\OrderDetail\OrderDetailApproveRequest;
use App\Http\Requests\OrderDetail\OrderDetailAuthorizeRequest;
use App\Http\Requests\OrderDetail\OrderDetailCancelRequest;
use App\Http\Requests\OrderDetail\OrderDetailCloneRequest;
use App\Http\Requests\OrderDetail\OrderDetailCreateRequest;
use App\Http\Requests\OrderDetail\OrderDetailIndexQueryRequest;
use App\Http\Requests\OrderDetail\OrderDetailPendingRequest;
use App\Http\Requests\OrderDetail\OrderDetailStoreRequest;
use App\Http\Requests\OrderDetail\OrderDetailSuspendRequest;
use App\Http\Requests\OrderDetail\OrderDetailUpdateRequest;
use App\Models\Color;
use App\Models\Inventory;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Product;
use App\Models\Size;
use App\Models\User;
use App\Traits\ApiMessage;
use App\Traits\ApiResponser;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OrderDetailController extends Controller
{
    use ApiResponser;
    use ApiMessage;

    public function index($id)
    {
        try {
            $order = Order::with('order_details', 'seller_user', 'client', 'wallet_user', 'business')->findOrFail($id);
            return view('Dashboard.OrderDetails.Index', compact('order'));
        } catch (ModelNotFoundException $e) {
            return back()->with('danger', 'Ocurrió un error al cargar el pedido: ' . $this->getMessage('ModelNotFoundException'));
        } catch (Exception $e) {
            return back()->with('danger', 'Ocurrió un error al cargar la vista: ' . $e->getMessage());
        }
    }

    public function indexQuery(OrderDetailIndexQueryRequest $request)
    {
        try {
            $order = Order::with([ 'seller_user',
                'client' => fn($query) => $query->withTrashed(),
                'order_details.product' => fn($query) => $query->withTrashed(),
                'order_details.color' => fn($query) => $query->withTrashed(),
                'order_details.seller_user' => fn($query) => $query->withTrashed(),
                'order_details.wallet_user' => fn($query) => $query->withTrashed(),
                'order_details.dispatch_user' => fn($query) => $query->withTrashed()
            ])
            ->findOrFail($request->input('order_id'));

            $orderSizes = collect([]);

            $sizes = Size::all();

            foreach($sizes as $size) {
                if($order->order_details->pluck("T$size->code")->sum() > 0) {
                    $orderSizes = $orderSizes->push($size);
                }
            }

            return $this->successResponse(
                [
                    'order' => $order,
                    'sizes' => $orderSizes->isNotEmpty() ? $orderSizes : $sizes
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

    public function create(OrderDetailCreateRequest $request)
    {
        try {
            if($request->filled('product_id') && $request->filled('color_id')) {

                $sizes = Size::all();

                $user = User::with('warehouses')->findOrFail(Auth::user()->id);

                $users = User::with('warehouses')->whereHas('warehouses', fn($query) => $query->whereIn('warehouses.id', $user->warehouses->pluck('id')->toArray()))->get();

                $inventory = $this->inventory($user->id, $request->input('product_id'), $request->input('color_id'));
                $committed = $this->committed(Auth::user()->title, Auth::user()->business_id, $request->input('product_id'), $request->input('color_id'), $users->pluck('id')->toArray());

                return $this->successResponse(
                    [
                        'inventory' => $inventory,
                        'committed' => $committed,
                        'sizes' => $sizes
                    ],
                    'Inventario encontrado exitosamente.',
                    200
                );
            }

            if($request->filled('product_id')) {

                $product = Product::findOrFail($request->input('product_id'));
                $colors = Color::with('inventories')->whereHas('inventories', fn($subQuery) => $subQuery->whereIn('warehouse_id', User::with('warehouses')->findOrFail(Auth::user()->id)->warehouses->pluck('id')->toArray())->where('product_id', $request->input('product_id')))->get();

                return $this->successResponse(
                    [
                        'product' => $product,
                        'colors' => $colors
                    ],
                    'Colores del producto encontrados exitosamente.',
                    200
                );
            }

            $products = Product::with('inventories')->whereHas('inventories', fn($subQuery) => $subQuery->whereIn('warehouse_id', User::with('warehouses')->findOrFail(Auth::user()->id)->warehouses->pluck('id')->toArray()))->orderBy('code', 'ASC')->get();

            return $this->successResponse(
                [
                    'products' => $products
                ],
                'Ingrese los datos para hacer la validacion y registro.',
                204
            );
        } catch (Exception $e) {
            // Devolver una respuesta de error en caso de excepción
            return $this->errorResponse(
                [
                    'message' => $this->getMessage('Exception'),
                    'error' => $e->getMessage()
                ],
                500
            );
        }
    }

    public function store(OrderDetailStoreRequest $request)
    {
        try {
            $order = Order::findOrFail($request->input('order_id'));

            if($order->seller_status == 'Aprobado') {
                $this->discount($request,  $request->input('product_id'), $request->input('color_id'));
            }

            $orderDetail = new OrderDetail();
            $orderDetail->order_id = $request->input('order_id');
            $orderDetail->product_id = $request->input('product_id');
            $orderDetail->color_id = $request->input('color_id');
            $orderDetail->price = $request->input('price');
            $orderDetail->negotiated_price = $request->input('negotiated_price');
            $orderDetail->T04 = $request->input('T04');
            $orderDetail->T06 = $request->input('T06');
            $orderDetail->T08 = $request->input('T08');
            $orderDetail->T10 = $request->input('T10');
            $orderDetail->T12 = $request->input('T12');
            $orderDetail->T14 = $request->input('T14');
            $orderDetail->T16 = $request->input('T16');
            $orderDetail->T18 = $request->input('T18');
            $orderDetail->T20 = $request->input('T20');
            $orderDetail->T22 = $request->input('T22');
            $orderDetail->T24 = $request->input('T24');
            $orderDetail->T26 = $request->input('T26');
            $orderDetail->T28 = $request->input('T28');
            $orderDetail->T30 = $request->input('T30');
            $orderDetail->T32 = $request->input('T32');
            $orderDetail->T34 = $request->input('T34');
            $orderDetail->T36 = $request->input('T36');
            $orderDetail->T38 = $request->input('T38');
            $orderDetail->TXXS = $request->input('TXXS');
            $orderDetail->TXS = $request->input('TXS');
            $orderDetail->TS = $request->input('TS');
            $orderDetail->TM = $request->input('TM');
            $orderDetail->TL = $request->input('TL');
            $orderDetail->TXL = $request->input('TXL');
            $orderDetail->TXXL = $request->input('TXXL');
            $orderDetail->seller_user_id = Auth::user()->id;
            $orderDetail->seller_date = Carbon::now()->format('Y-m-d H:i:s');
            $orderDetail->seller_observation = $request->input('seller_observation');
            $orderDetail->status = $order->wallet_status == 'Aprobado' ? 'Aprobado' : 'Pendiente';
            $orderDetail->save();

            return $this->successResponse(
                $orderDetail,
                'El detalle del pedido fue registrado por el asesor exitosamente.',
                201
            );
        } catch (ModelNotFoundException $e) {
            return $this->errorResponse(
                [
                    'message' => $this->getMessage('ModelNotFoundException'),
                    'error' => $e->getMessage()
                ],
                500
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

    public function edit(OrderDetailCreateRequest $request, $id)
    {
        try {
            if($request->filled('product_id') && $request->filled('color_id')) {

                $sizes = Size::all();

                $user = User::with('warehouses')->findOrFail(Auth::user()->id);

                $users = User::with('warehouses')->whereHas('warehouses', fn($query) => $query->whereIn('warehouses.id', $user->warehouses->pluck('id')->toArray()))->get();

                $inventory = $this->inventory($user->id, $request->input('product_id'), $request->input('color_id'));
                $committed = $this->committed(Auth::user()->title, Auth::user()->business_id, $request->input('product_id'), $request->input('color_id'), $users->pluck('id')->toArray());

                return $this->successResponse(
                    [
                        'inventory' => $inventory,
                        'committed' => $committed,
                        'sizes' => $sizes
                    ],
                    'Inventario encontrado exitosamente.',
                    200
                );
            }

            if($request->filled('product_id')) {

                $product = Product::findOrFail($request->input('product_id'));
                $colors = Color::with('inventories')->whereHas('inventories', fn($subQuery) => $subQuery->whereIn('warehouse_id', User::with('warehouses')->findOrFail(Auth::user()->id)->warehouses->pluck('id')->toArray())->where('product_id', $request->input('product_id')))->get();

                return $this->successResponse(
                    [
                        'product' => $product,
                        'colors' => $colors
                    ],
                    'Colores del producto encontrados exitosamente.',
                    200
                );
            }

            $products = Product::with('inventories')->whereHas('inventories', fn($subQuery) => $subQuery->whereIn('warehouse_id', User::with('warehouses')->findOrFail(Auth::user()->id)->warehouses->pluck('id')->toArray()))->orderBy('code', 'ASC')->get();

            return $this->successResponse(
                [
                    'products' => $products,
                    'orderDetail' => OrderDetail::findOrFail($id)
                ],
                'El detalle del pedido fue encontrado exitosamente.',
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

    public function update(OrderDetailUpdateRequest $request, $id)
    {
        try {
            $orderDetail = OrderDetail::findOrFail($id);
            $orderDetail->order_id = $request->input('order_id');
            $orderDetail->product_id = $request->input('product_id');
            $orderDetail->color_id = $request->input('color_id');
            $orderDetail->price = $request->input('price');
            $orderDetail->negotiated_price = $request->input('negotiated_price');
            $orderDetail->T04 = $request->input('T04');
            $orderDetail->T06 = $request->input('T06');
            $orderDetail->T08 = $request->input('T08');
            $orderDetail->T10 = $request->input('T10');
            $orderDetail->T12 = $request->input('T12');
            $orderDetail->T14 = $request->input('T14');
            $orderDetail->T16 = $request->input('T16');
            $orderDetail->T18 = $request->input('T18');
            $orderDetail->T20 = $request->input('T20');
            $orderDetail->T22 = $request->input('T22');
            $orderDetail->T24 = $request->input('T24');
            $orderDetail->T26 = $request->input('T26');
            $orderDetail->T28 = $request->input('T28');
            $orderDetail->T30 = $request->input('T30');
            $orderDetail->T32 = $request->input('T32');
            $orderDetail->T34 = $request->input('T34');
            $orderDetail->T36 = $request->input('T36');
            $orderDetail->T38 = $request->input('T38');
            $orderDetail->TXXS = $request->input('TXXS');
            $orderDetail->TXS = $request->input('TXS');
            $orderDetail->TS = $request->input('TS');
            $orderDetail->TM = $request->input('TM');
            $orderDetail->TL = $request->input('TL');
            $orderDetail->TXL = $request->input('TXL');
            $orderDetail->TXXL = $request->input('TXXL');
            $orderDetail->seller_date = Carbon::now()->format('Y-m-d H:i:s');
            $orderDetail->seller_observation = $request->input('seller_observation');
            $orderDetail->status = $orderDetail->status == 'Agotado' ? 'Pendiente' : $orderDetail->status ;
            $orderDetail->save();

            return $this->successResponse(
                $orderDetail,
                'El detalle del pedido fue actualizado por el asesor exitosamente.',
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

    public function show($id)
    {
        try {
            $products = Product::with('inventories.color')->whereHas('inventories', fn($subQuery) => $subQuery->whereIn('warehouse_id', User::with('warehouses')->findOrFail(Auth::user()->id)->warehouses->pluck('id')->toArray()))
            ->orderBy('code', 'ASC')->get()->map(function ($item) {
                $item->colors = $item->inventories->pluck('color')->unique();
                return $item;
            });

            return $this->successResponse(
                [
                    'products' => $products,
                    'orderDetail' => OrderDetail::findOrFail($id)
                ],
                'El detalle del pedido fue encontrado exitosamente.',
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

    public function clone(OrderDetailCloneRequest $request)
    {
        try {
            $detail = OrderDetail::with('order')->findOrFail($request->input('order_detail_id'));

            foreach($request->input('items') as $item) {
                $orderDetail = new OrderDetail();
                $orderDetail->order_id = $detail->order_id;
                $orderDetail->product_id = $item->product_id;
                $orderDetail->color_id = $item->color_id;
                $orderDetail->price = $detail->price;
                $orderDetail->negotiated_price = $detail->negotiated_price;
                $orderDetail->T04 = $detail->T04;
                $orderDetail->T06 = $detail->T06;
                $orderDetail->T08 = $detail->T08;
                $orderDetail->T10 = $detail->T10;
                $orderDetail->T12 = $detail->T12;
                $orderDetail->T14 = $detail->T14;
                $orderDetail->T16 = $detail->T16;
                $orderDetail->T18 = $detail->T18;
                $orderDetail->T20 = $detail->T20;
                $orderDetail->T22 = $detail->T22;
                $orderDetail->T24 = $detail->T24;
                $orderDetail->T26 = $detail->T26;
                $orderDetail->T28 = $detail->T28;
                $orderDetail->T30 = $detail->T30;
                $orderDetail->T32 = $detail->T32;
                $orderDetail->T34 = $detail->T34;
                $orderDetail->T36 = $detail->T36;
                $orderDetail->T38 = $detail->T38;
                $orderDetail->TXXS = $detail->TXXS;
                $orderDetail->TXS = $detail->TXS;
                $orderDetail->TS = $detail->TS;
                $orderDetail->TM = $detail->TM;
                $orderDetail->TL = $detail->TL;
                $orderDetail->TXL = $detail->TXL;
                $orderDetail->TXXL = $detail->TXXL;
                $orderDetail->seller_user_id = Auth::user()->id;
                $orderDetail->seller_date = Carbon::now()->format('Y-m-d H:i:s');
                $orderDetail->save();
            }

            return $this->successResponse(
                '',
                'El detalle del pedido fue registrado por el asesor exitosamente.',
                201
            );
        } catch (ModelNotFoundException $e) {
            return $this->errorResponse(
                [
                    'message' => $this->getMessage('ModelNotFoundException'),
                    'error' => $e->getMessage()
                ],
                500
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

    public function pending(OrderDetailPendingRequest $request)
    {
        try {
            $orderDetail = OrderDetail::findOrFail($request->input('id'));
            $orderDetail->status = 'Pendiente';
            $orderDetail->seller_user_id = $orderDetail->seller_user_id == Auth::user()->id ? Auth::user()->id : $orderDetail->seller_user_id ;
            $orderDetail->seller_date = Carbon::now()->format('Y-m-d H:i:s');
            $orderDetail->wallet_user_id = in_array(Auth::user()->title, ['SUPER ADMINISTRADOR', 'ADMINISTRADOR', 'CARTERA']) ? Auth::user()->id : null ;
            $orderDetail->wallet_date = in_array(Auth::user()->title, ['SUPER ADMINISTRADOR', 'ADMINISTRADOR', 'CARTERA']) ? Carbon::now()->format('Y-m-d H:i:s') : null;
            $orderDetail->save();

            return $this->successResponse(
                $orderDetail,
                'El detalle del pedido fue pendiente exitosamente.',
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

    public function authorized(OrderDetailAuthorizeRequest $request)
    {
        try {
            $orderDetail = OrderDetail::with('order')->findOrFail($request->input('id'));

            $boolean = true;

            $sizes = Size::all();

            if(in_array($orderDetail->status, ['Suspendido', 'Cancelado'])) {
                $inventory = $this->inventory($orderDetail->order->seller_user_id, $orderDetail->product_id, $orderDetail->color_id);
                $committed = $this->committed($orderDetail->order->seller_user->title, $orderDetail->order->business_id, $orderDetail->product_id, $orderDetail->color_id);

                foreach($sizes as $size) {
                    if($orderDetail->{"T$size->code"} > ($inventory->{"T$size->code"} - $committed->{"T$size->code"})) {
                        $boolean = false;
                        break;
                    }
                }
            }

            $orderDetail->status = $boolean ? 'Autorizado' : 'Agotado';
            $orderDetail->wallet_user_id = Auth::user()->id;
            $orderDetail->wallet_date = Carbon::now()->format('Y-m-d H:i:s');
            $orderDetail->save();

            DB::statement('CALL order_wallet_status(?)', [$orderDetail->order->id]);

            return $this->successResponse(
                $orderDetail,
                $boolean ? 'El detalle del pedido fue autorizado exitosamente.' : 'El detalle del pedido no fue autorizado ya que no hay inventario disponible.',
                $boolean ? 200 : 422
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

    public function approve(OrderDetailApproveRequest $request)
    {
        try {
            $orderDetail = OrderDetail::with('order')->findOrFail($request->input('id'));

            $boolean = true;

            $sizes = Size::all();

            if(in_array($orderDetail->status, ['Suspendido', 'Cancelado', 'Agotado'])) {
                $inventory = $this->inventory($orderDetail->order->seller_user_id, $orderDetail->product_id, $orderDetail->color_id);
                $committed = $this->committed($orderDetail->order->seller_user->title, $orderDetail->order->business_id, $orderDetail->product_id, $orderDetail->color_id);

                foreach($sizes as $size) {
                    if($orderDetail->{"T$size->code"} > ($inventory->{"T$size->code"} - $committed->{"T$size->code"})) {
                        $boolean = false;
                        break;
                    }
                }

                if($boolean) {
                    $this->discount($orderDetail,  $orderDetail->product_id, $orderDetail->color_id);
                }
            }

            $orderDetail->status = $boolean ? 'Aprobado' : 'Agotado';
            $orderDetail->wallet_user_id = Auth::user()->id;
            $orderDetail->wallet_date = Carbon::now()->format('Y-m-d H:i:s');
            $orderDetail->save();

            DB::statement('CALL order_wallet_status(?)', [$orderDetail->order->id]);

            return $this->successResponse(
                $orderDetail,
                $boolean ? 'El detalle del pedido fue aprobado exitosamente.' : 'El detalle del pedido no fue aprobado ya que no hay inventario disponible.',
                $boolean ? 200 : 422
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

    public function allow(OrderDetailAllowRequest $request)
    {
        try {
            $orderDetail = OrderDetail::with('order')->findOrFail($request->input('id'));
            $orderDetail->status = in_array($orderDetail->order->wallet_status, ['Parcialmente Aprobado', 'Aprobado']) ? 'Aprobado' : 'Autorizado';
            $orderDetail->wallet_user_id = Auth::user()->id;
            $orderDetail->wallet_date = Carbon::now()->format('Y-m-d H:i:s');
            $orderDetail->save();

            $this->discount($orderDetail,  $orderDetail->product_id, $orderDetail->color_id);

            DB::statement('CALL order_wallet_status(?)', [$orderDetail->order->id]);

            return $this->successResponse(
                $orderDetail,
                'El detalle del pedido fue aprobado exitosamente.',
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

    public function cancel(OrderDetailCancelRequest $request)
    {
        try {
            $orderDetail = OrderDetail::findOrFail($request->input('id'));
            $orderDetail->status = 'Cancelado';
            $orderDetail->seller_user_id = $orderDetail->seller_user_id == Auth::user()->id ? Auth::user()->id : $orderDetail->seller_user_id ;
            $orderDetail->seller_date = Carbon::now()->format('Y-m-d H:i:s');
            $orderDetail->wallet_user_id = in_array(Auth::user()->title, ['SUPER ADMINISTRADOR', 'ADMINISTRADOR', 'CARTERA']) ? Auth::user()->id : null ;
            $orderDetail->wallet_date = in_array(Auth::user()->title, ['SUPER ADMINISTRADOR', 'ADMINISTRADOR', 'CARTERA']) ? Carbon::now()->format('Y-m-d H:i:s') : null;
            $orderDetail->save();

            DB::statement('CALL order_seller_status(?)', [$orderDetail->order->id]);

            return $this->successResponse(
                $orderDetail,
                'El detalle del pedido fue cancelado exitosamente.',
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

    public function suspend(OrderDetailSuspendRequest $request)
    {
        try {
            $orderDetail = OrderDetail::with('order')->findOrFail($request->input('id'));
            $orderDetail->status = 'Suspendido';
            $orderDetail->wallet_user_id = Auth::user()->id;
            $orderDetail->wallet_date = Carbon::now()->format('Y-m-d H:i:s');
            $orderDetail->save();

            DB::statement('CALL order_wallet_status(?)', [$orderDetail->order->id]);

            return $this->successResponse(
                $orderDetail,
                'El detalle del pedido fue suspendido exitosamente.',
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

    private function inventory($seller_user_id, $product_id, $color_id)
    {
        try {
            $sizes = Size::all();
            $inventory = Inventory::select('products.trademark AS MARCA', 'products.code AS REFERENCIA', 'colors.name AS COLOR');
            foreach ($sizes as $size) {
                $inventory->addSelect(DB::raw("COALESCE(SUM(CASE WHEN sizes.code = '$size->code' THEN inventories.quantity ELSE 0 END), 0) AS T$size->code"));
            }
            $inventory->join('warehouses', 'warehouses.id', 'inventories.warehouse_id')
            ->join('products', 'products.id', 'inventories.product_id')
            ->join('colors', 'colors.id', 'inventories.color_id')
            ->join('sizes', 'sizes.id', 'inventories.size_id')
            ->where('quantity', '>', 0)
            ->whereIn('warehouse_id', User::with('warehouses')->findOrFail($seller_user_id)->warehouses->pluck('id')->toArray())
            ->where('product_id', $product_id)
            ->where('color_id', $color_id)
            ->groupBy('products.trademark', 'products.code', 'colors.name');

            $inventory = $inventory->first();

            if(empty($inventory)) {
                $inventory = (object) [];
                $product = Product::findOrFail($product_id);
                $color = Color::findOrFail($color_id);
                $inventory->MARCA = $product->trademark;
                $inventory->REFERENCIA = $product->code;
                $inventory->COLOR = $color->name;
                foreach ($sizes as $size) {
                    $inventory->{"T$size->code"} = 0;
                }
            }

            return $inventory;
        } catch (Exception $e) {
            if(empty($inventory)) {
                $inventory = (object) [];
                $product = Product::findOrFail($product_id);
                $color = Color::findOrFail($color_id);
                $inventory->MARCA = $product->trademark;
                $inventory->REFERENCIA = $product->code;
                $inventory->COLOR = $color->name;
                foreach ($sizes as $size) {
                    $inventory->{"T$size->code"} = 0;
                }
            }

            return $inventory;
        }
    }

    private function committed($title, $business_id, $product_id, $color_id, $users_id = [])
    {
        try {
            $sizes = Size::all();
            $committed = OrderDetail::select('products.trademark AS MARCA', 'products.code AS REFERENCIA', 'colors.name AS COLOR');
            foreach ($sizes as $size) {
                $committed->addSelect(DB::raw("SUM(T$size->code) as T$size->code"));
            }
            $committed->join('orders', 'orders.id', 'order_details.order_id')

            ->join('users', 'users.id', 'orders.seller_user_id')
            ->join('products', 'products.id', 'order_details.product_id')
            ->join('colors', 'colors.id', 'order_details.color_id')
            ->where('product_id', $product_id)
            ->where('color_id', $color_id)
            ->where('orders.business_id', $business_id)
            ->when(in_array($title, ['VENDEDOR ESPECIAL']),
                function ($query) {
                    $query->whereIn('orders.seller_status', ['Aprobado'])
                    ->whereIn('orders.wallet_status', ['Pendiente', 'Autorizado'])
                    ->whereIn('order_details.status', ['Pendiente', 'Autorizado'])
                    ->where('users.title', 'VENDEDOR ESPECIAL');
                }
            )
            ->when(!in_array($title, ['VENDEDOR ESPECIAL']),
                function ($query) {
                    $query->whereIn('orders.seller_status', ['Aprobado'])
                    ->whereIn('orders.wallet_status', ['Pendiente', 'Aprobado', 'Parcialmente Aprobado'])
                    ->whereIn('order_details.status', ['Pendiente', 'Aprobado', 'Comprometido'])
                    ->whereNot('users.title', 'VENDEDOR ESPECIAL');
                }
            )
            ->when(count($users_id) > 0,
                function ($query) use ($users_id) {
                    $query->whereIn('orders.seller_user_id', $users_id);
                }
            )
            ->groupBy('products.trademark', 'products.code', 'colors.name');

            $committed = $committed->first();

            if(empty($committed)) {
                $committed = (object) [];
                $product = Product::findOrFail($product_id);
                $color = Color::findOrFail($color_id);
                $committed->MARCA = $product->trademark;
                $committed->REFERENCIA = $product->code;
                $committed->COLOR = $color->name;
                foreach ($sizes as $size) {
                    $committed->{"T$size->code"} = 0;
                }
            }

            return $committed;
        } catch (Exception $e) {
            if(empty($committed)) {
                $committed = (object) [];
                $product = Product::findOrFail($product_id);
                $color = Color::findOrFail($color_id);
                $committed->MARCA = $product->trademark;
                $committed->REFERENCIA = $product->code;
                $committed->COLOR = $color->name;
                foreach ($sizes as $size) {
                    $committed->{"T$size->code"} = 0;
                }
            }

            return $committed;
        }
    }

    private function discount($item, $product_id, $color_id)
    {
        try {
            $sizes = Size::all();
            foreach($sizes as $size) {
                $inventories = Inventory::where('product_id', $product_id)->where('size_id', $size->id)->where('color_id', $color_id)->where('system', 'PROYECCION')->get();
                $quantity = $item->{"T$size->code"};
                foreach($inventories as $inventory) {
                    if($inventory->quantity == $quantity || $inventory->quantity > $quantity) {
                        $inventory->quantity -= $quantity;
                        $inventory->save();
                        $quantity = 0;
                    } else if($inventory->quantity < $quantity) {
                        $aux = $quantity - $inventory->quantity;
                        $inventory->quantity -= $inventory->quantity;
                        $inventory->save();
                        $quantity = $aux;
                    }

                    if($quantity == 0){
                        break;
                    }
                }
            }
        } catch (Exception $e) {

        }
    }
}
