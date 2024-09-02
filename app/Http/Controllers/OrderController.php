<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\Order\OrderApproveRequest;
use App\Http\Requests\Order\OrderAssentRequest;
use App\Http\Requests\Order\OrderAuthorizeRequest;
use App\Http\Requests\Order\OrderCancelRequest;
use App\Http\Requests\Order\OrderCreateRequest;
use App\Http\Requests\Order\OrderDeclineRequest;
use App\Http\Requests\Order\OrderDelayRequest;
use App\Http\Requests\Order\OrderDispatchRequest;
use App\Http\Requests\Order\OrderEditRequest;
use App\Http\Requests\Order\OrderIndexQueryRequest;
use App\Http\Requests\Order\OrderObservationRequest;
use App\Http\Requests\Order\OrderPartiallyApproveRequest;
use App\Http\Requests\Order\OrderPendingRequest;
use App\Http\Requests\Order\OrderStoreRequest;
use App\Http\Requests\Order\OrderSuspendRequest;
use App\Http\Requests\Order\OrderUpdateRequest;
use App\Http\Resources\Order\OrderIndexQueryCollection;
use App\Mail\EmailNotify;
use App\Mail\EmailOrder;
use App\Mail\EmailWallet;
use App\Models\Client;
use App\Models\Color;
use App\Models\Inventory;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\OrderDispatch;
use App\Models\OrderDispatchDetail;
use App\Models\Product;
use App\Models\Size;
use App\Models\User;
use App\Traits\ApiMessage;
use App\Traits\ApiResponser;
use Barryvdh\DomPDF\Facade\Pdf as PDF;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\Mailer\Exception\TransportException;

class OrderController extends Controller
{
    use ApiResponser;
    use ApiMessage;

    public function index()
    {
        try {
            return view('Dashboard.Orders.Index');
        } catch (Exception $e) {
            return back()->with('danger', 'Ocurrió un error al cargar la vista: ' . $e->getMessage());
        }
    }

    public function indexQuery(OrderIndexQueryRequest $request)
    {
        try {
            $start_date = Carbon::parse($request->input('start_date'))->startOfDay();
            $end_date = Carbon::parse($request->input('end_date'))->endOfDay();

            $orders = Order::with([
                'order_details.product' => fn($query) => $query->withTrashed(),
                'order_details.color' => fn($query) => $query->withTrashed(),
                'client' => fn($query) => $query->withTrashed(),
                'seller_user' => fn($query) => $query->withTrashed(),
                'wallet_user' => fn($query) => $query->withTrashed(),
                'correria' => fn($query) => $query->withTrashed()
            ])
            ->when($request->filled('search'),
                function ($query) use ($request) {
                    $query->search($request->input('search'));
                }
            )
            ->when($request->filled('start_date') && $request->filled('end_date'),
                function ($query) use ($start_date, $end_date) {
                    $query->filterByDate($start_date, $end_date);
                }
            )
            ->when(!in_array(Auth::user()->title, ['SUPER ADMINISTRADOR', 'ADMINISTRADOR', 'CARTERA']),
                function ($query) {
                    $query->where('seller_user_id', Auth::user()->id)
                    ->whereHas('correria', fn($query) => $query->whereNull('deleted_at'));
                }
            )
            ->when(in_array(Auth::user()->title, ['SUPER ADMINISTRADOR', 'ADMINISTRADOR', 'CARTERA']),
                function ($query) {
                    $query->where('seller_status', 'Aprobado')
                    ->whereIn('wallet_status', ['Pendiente', 'Suspendido', 'En mora', 'Parcialmente Aprobado', 'Aprobado', 'Autorizado'])
                    ->whereIn('dispatch_status', ['Pendiente', 'Parcialmente Aprobado', 'Aprobado', 'Parcialmente Empacado', 'Parcialmente Despachado', 'Despachado']);
                }
            )
            ->where('business_id', Auth::user()->business_id)
            ->orderBy($request->input('column'), $request->input('dir'))
            ->paginate($request->input('perPage'));

            return $this->successResponse(
                new OrderIndexQueryCollection($orders),
                $this->getMessage('Success'),
                200
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

    public function create(OrderCreateRequest $request)
    {
        try {
            $clients = Client::all();

            if($request->filled('client_id')) {
                $client = Client::with('wallet', 'compra', 'cartera', 'bodega', 'administrador', 'chamber_of_commerce', 'rut', 'identity_card', 'signature_warranty')->findOrFail($request->input('client_id'));

                return $this->successResponse(
                    [
                        'client' => $client
                    ],
                    'El cliente fue encontrado exitosamente.',
                    200
                );
            }

            return $this->successResponse(
                [
                    'clients' => $clients
                ],
                'Ingrese los datos para hacer la validacion y registro.',
                204
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

    public function store(OrderStoreRequest $request)
    {
        try {
            $order = new Order();
            $order->client_id = $request->input('client_id');
            $order->dispatch_type = $request->input('dispatch_type');
            $order->dispatch_date = Carbon::parse($request->input('dispatch_date'))->format('Y-m-d');
            $order->seller_user_id = Auth::user()->id;
            $order->seller_observation = $request->input('seller_observation');
            $order->seller_dispatch_official = $request->input('seller_dispatch_official');
            $order->seller_dispatch_document = $request->input('seller_dispatch_document');
            $order->correria_id = $request->input('correria_id');
            $order->business_id = Auth::user()->business_id;
            $order->save();

            if($request->filled('order_id')) {
                $items = OrderDetail::where('order_id', $request->input('order_id'))->get();
                foreach ($items as $item) {
                    $orderDetail = new OrderDetail();
                    $orderDetail->order_id = $order->id;
                    $orderDetail->product_id = $item->product_id;
                    $orderDetail->color_id = $item->color_id;
                    $orderDetail->price = $item->price;
                    $orderDetail->negotiated_price = $item->negotiated_price;
                    $orderDetail->T04 = $item->T04;
                    $orderDetail->T06 = $item->T06;
                    $orderDetail->T08 = $item->T08;
                    $orderDetail->T10 = $item->T10;
                    $orderDetail->T12 = $item->T12;
                    $orderDetail->T14 = $item->T14;
                    $orderDetail->T16 = $item->T16;
                    $orderDetail->T18 = $item->T18;
                    $orderDetail->T20 = $item->T20;
                    $orderDetail->T22 = $item->T22;
                    $orderDetail->T24 = $item->T24;
                    $orderDetail->T26 = $item->T26;
                    $orderDetail->T28 = $item->T28;
                    $orderDetail->T30 = $item->T30;
                    $orderDetail->T32 = $item->T32;
                    $orderDetail->T34 = $item->T34;
                    $orderDetail->T36 = $item->T36;
                    $orderDetail->T38 = $item->T38;
                    $orderDetail->TXXS = $item->TXXS;
                    $orderDetail->TXS = $item->TXS;
                    $orderDetail->TS = $item->TS;
                    $orderDetail->TM = $item->TM;
                    $orderDetail->TL = $item->TL;
                    $orderDetail->TXL = $item->TXL;
                    $orderDetail->TXXL = $item->TXXL;
                    $orderDetail->seller_user_id = Auth::user()->id;
                    $orderDetail->seller_date = Carbon::now()->format('Y-m-d H:i:s');
                    $orderDetail->seller_observation = $item->seller_observation;
                    $orderDetail->save();
                }
            }

            return $this->successResponse(
                [
                    'url' => URL::route('Dashboard.Orders.Details.Index', ['id' => $order->id]),
                    'order' => $order
                ],
                'El pedido fue registrado exitosamente.',
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

    public function edit(OrderEditRequest $request, $id)
    {
        try {
            $order = Order::findOrFail($id);
            $clients = Client::all();

            if($request->filled('client_id')) {
                $client = Client::with('wallet', 'compra', 'cartera', 'bodega', 'administrador', 'chamber_of_commerce','rut', 'identity_card', 'signature_warranty')->findOrFail($request->input('client_id'));

                return $this->successResponse(
                    [
                        'client' => $client
                    ],
                    'El cliente fue encontrado exitosamente.',
                    204
                );
            }

            return $this->successResponse(
                [
                    'order' => $order,
                    'clients' => $clients
                ],
                'El pedido fue encontrado exitosamente.',
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

    public function update(OrderUpdateRequest $request, $id)
    {
        try {
            $order = Order::findOrFail($id);
            $order->client_id = $request->input('client_id');
            $order->dispatch_type = $request->input('dispatch_type');
            $order->dispatch_date = Carbon::parse($request->input('dispatch_date'))->format('Y-m-d');
            $order->seller_observation = $request->input('seller_observation');
            $order->seller_dispatch_official = $request->input('seller_dispatch_official');
            $order->seller_dispatch_document = $request->input('seller_dispatch_document');
            $order->save();

            return $this->successResponse(
                $order,
                'El pedido fue actualizado exitosamente.',
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

    public function observation(OrderObservationRequest $request)
    {
        try {
            $order = Order::findOrFail($request->input('id'));
            $order->wallet_observation = $request->input('wallet_observation');
            $order->wallet_dispatch_official = $request->input('wallet_dispatch_official');
            $order->wallet_dispatch_document = $request->input('wallet_dispatch_document');
            $order->save();

            return $this->successResponse(
                $order,
                'La observacion de cartera fue actualizada exitosamente.',
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

    public function cancel(OrderCancelRequest $request)
    {
        try {
            $order = Order::with('order_details')->findOrFail($request->input('id'));

            $order->order_details()->whereIn('status', ['Pendiente'])->update(['status' => 'Cancelado']);

            $order->seller_date = Carbon::now()->format('Y-m-d H:i:s');
            $order->seller_status = 'Cancelado';
            $order->wallet_status = 'Cancelado';
            $order->dispatch_status = 'Cancelado';
            $order->save();

            return $this->successResponse(
                $order,
                'El pedido fue cancelado por el asesor exitosamente.',
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

    public function assent(OrderAssentRequest $request)
    {
        try {
            $order = Order::with('order_details', 'seller_user', 'business')->findOrFail($request->input('id'));

            $sizes = Size::all();

            $user = User::with('warehouses')->findOrFail($order->seller_user_id);

            $users = User::with('warehouses')->whereHas('warehouses', fn($query) => $query->whereIn('warehouses.id', $user->warehouses->pluck('id')->toArray()))->get();

            foreach($order->order_details->whereIn('status', ['Pendiente']) as $order_detail) {
                $inventory = $this->inventory($user->id, $order_detail->product_id, $order_detail->color_id);
                $committed = $this->committed($order->seller_user->title, $order->business_id, $order_detail->product_id, $order_detail->color_id, $users->pluck('id')->toArray());

                $boolean = true;
                foreach($sizes as $size) {
                    if($order_detail->{"T{$size->code}"} > ($inventory->{"T{$size->code}"} - $committed->{"T{$size->code}"})) {
                        $boolean = false;
                        break;
                    }
                }

                $order_detail->status = $boolean ? 'Pendiente' : 'Agotado';
                $order_detail->save();

                if($boolean) {
                    $this->discount($order_detail, $order_detail->product_id, $order_detail->color_id);
                }
            }

            $order->seller_date = Carbon::now()->format('Y-m-d H:i:s');
            $order->seller_status = 'Aprobado';
            $order->save();

            // DB::statement('CALL order_seller_status(?)', [$order->id]);

            $emails = [
                $order->business->order_notify_email
            ];

            Mail::to($emails)->send(new EmailNotify($order));

            return $this->successResponse(
                [
                    'order' => $order,
                    'urlEmail' => $request->input('email') ? URL::route('Dashboard.Orders.Email', ['id' => $order->id]) : null,
                    'urlDownload' => $request->input('download') ? URL::route('Dashboard.Orders.Download', ['id' => $order->id]) : null
                ],
                'El pedido fue asentado exitosamente.',
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

    public function pending(OrderPendingRequest $request)
    {
        try {
            $order = Order::with('order_details')->findOrFail($request->input('id'));
            $order->seller_status = 'Pendiente';
            $order->wallet_status = 'Pendiente';

            /* $order->order_details()->whereIn('status', ['Agotado', 'Suspendido'])->update(['status' => 'Pendiente']); */

            $order->save();

            return $this->successResponse(
                $order,
                'El pedido fue habilitado para el vendedor exitosamente.',
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

    public function suspend(OrderSuspendRequest $request)
    {
        try {
            $order = Order::with('order_details')->findOrFail($request->input('id'));

            $order->order_details()->whereIn('status', ['Pendiente', 'Aprobado', 'Autorizado'])->update(['status' => 'Suspendido', 'wallet_user_id' => Auth::user()->id, 'wallet_date' => Carbon::now()->format('Y-m-d H:i:s')]);

            $order->wallet_user_id = Auth::user()->id;
            $order->wallet_date = Carbon::now()->format('Y-m-d H:i:s');
            $order->wallet_status = 'Suspendido';
            $order->save();

            return $this->successResponse(
                [
                    'order' => $order
                ],
                'El pedido fue suspendido exitosamente.',
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

    public function delay(OrderDelayRequest $request)
    {
        try {
            $order = Order::with('order_details')->findOrFail($request->input('id'));

            $order->order_details()->whereIn('status', ['Pendiente', 'Aprobado', 'Autorizado'])->update(['status' => 'Suspendido', 'wallet_user_id' => Auth::user()->id, 'wallet_date' => Carbon::now()->format('Y-m-d H:i:s')]);

            $order->wallet_user_id = Auth::user()->id;
            $order->wallet_date = Carbon::now()->format('Y-m-d H:i:s');
            $order->wallet_status = 'En mora';
            $order->save();

            return $this->successResponse(
                [
                    'order' => $order
                ],
                'El pedido fue moroso exitosamente.',
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

    public function decline(OrderDeclineRequest $request)
    {
        try {
            $order = Order::with('order_details')->findOrFail($request->input('id'));

            $order->order_details()->whereIn('status', ['Pendiente', 'Aprobado', 'Autorizado', 'Suspendido'])->update(['status' => 'Cancelado', 'wallet_user_id' => Auth::user()->id, 'wallet_date' => Carbon::now()->format('Y-m-d H:i:s')]);

            $order->wallet_user_id = Auth::user()->id;
            $order->wallet_date = Carbon::now()->format('Y-m-d H:i:s');
            $order->wallet_status = 'Cancelado';
            $order->dispatch_status = 'Cancelado';
            $order->save();

            return $this->successResponse(
                $order,
                'El pedido fue rechazado exitosamente.',
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

    public function authorized(OrderAuthorizeRequest $request)
    {
        try {
            $order = Order::with('order_details', 'seller_user')->findOrFail($request->input('id'));

            $order->order_details()->whereIn('status', ['Pendiente'])->update(['status' => 'Autorizado', 'wallet_user_id' => Auth::user()->id, 'wallet_date' => Carbon::now()->format('Y-m-d H:i:s')]);

            $sizes = Size::all();

            $user = User::with('warehouses')->findOrFail($order->seller_user_id);

            $users = User::with('warehouses')->whereHas('warehouses', fn($query) => $query->whereIn('warehouses.id', $user->warehouses->pluck('id')->toArray()))->get();

            if($order->wallet_status == 'Suspendido') {
                foreach($order->order_details->whereIn('status', ['Suspendido']) as $order_detail) {
                    $inventory = $this->inventory($user->id, $order_detail->product_id, $order_detail->color_id);
                    $committed = $this->committed($order->seller_user->title, $order->business_id, $order_detail->product_id, $order_detail->color_id, $users->pluck('id')->toArray());

                    $boolean = true;
                    foreach($sizes as $size) {
                        if($order_detail->{"T{$size->code}"} > ($inventory->{"T{$size->code}"} - $committed->{"T{$size->code}"})) {
                            $boolean = false;
                            break;
                        }
                    }

                    $order_detail->status = $boolean ? 'Autorizado' : 'Agotado';
                    $order_detail->save();
                }
            }

            $order->wallet_dispatch_official = $order->wallet_dispatch_official ?? $order->seller_dispatch_official;
            $order->wallet_dispatch_document = $order->wallet_dispatch_document ?? $order->seller_dispatch_document;
            $order->wallet_user_id = Auth::user()->id;
            $order->wallet_date = Carbon::now()->format('Y-m-d H:i:s');
            $order->wallet_status = 'Autorizado';
            $order->save();

            return $this->successResponse(
                [
                    'order' => $order
                ],
                'El pedido fue autorizado exitosamente.',
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

    public function approve(OrderApproveRequest $request)
    {
        try {
            $order = Order::with('order_details', 'seller_user')->findOrFail($request->input('id'));

            $order->order_details()->whereIn('status', ['Pendiente'])->update(['status' => 'Aprobado', 'wallet_user_id' => Auth::user()->id, 'wallet_date' => Carbon::now()->format('Y-m-d H:i:s')]);

            $sizes = Size::all();

            $user = User::with('warehouses')->findOrFail($order->seller_user_id);

            $users = User::with('warehouses')->whereHas('warehouses', fn($query) => $query->whereIn('warehouses.id', $user->warehouses->pluck('id')->toArray()))->get();

            if($order->wallet_status == 'Suspendido') {
                foreach($order->order_details->whereIn('status', ['Suspendido']) as $order_detail) {
                    $inventory = $this->inventory($user->id, $order_detail->product_id, $order_detail->color_id);
                    $committed = $this->committed($order->seller_user->title, $order->business_id, $order_detail->product_id, $order_detail->color_id, $users->pluck('id')->toArray());

                    $boolean = true;
                    foreach($sizes as $size) {
                        if($order_detail->{"T{$size->code}"} > ($inventory->{"T{$size->code}"} - $committed->{"T{$size->code}"})) {
                            $boolean = false;
                            break;
                        }
                    }

                    $order_detail->status = $boolean ? 'Aprobado' : 'Agotado';
                    $order_detail->save();
                }
            }

            $order->wallet_dispatch_official = $order->wallet_dispatch_official ?? $order->seller_dispatch_official;
            $order->wallet_dispatch_document = $order->wallet_dispatch_document ?? $order->seller_dispatch_document;
            $order->wallet_user_id = Auth::user()->id;
            $order->wallet_date = Carbon::now()->format('Y-m-d H:i:s');
            $order->wallet_status = 'Aprobado';
            $order->save();

            return $this->successResponse(
                [
                    'order' => $order
                ],
                'El pedido fue aprobado exitosamente.',
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

    public function partiallyApprove(OrderPartiallyApproveRequest $request)
    {
        try {
            $order = Order::findOrFail($request->input('id'));
            $order->wallet_dispatch_official = $order->wallet_dispatch_official ?? $order->seller_dispatch_official;
            $order->wallet_dispatch_document = $order->wallet_dispatch_document ?? $order->seller_dispatch_document;
            $order->wallet_user_id = Auth::user()->id;
            $order->wallet_date = Carbon::now()->format('Y-m-d H:i:s');
            $order->wallet_status = 'Parcialmente Aprobado';
            $order->save();

            return $this->successResponse(
                [
                    'order' => $order
                ],
                'El pedido fue aprobado parcialmente exitosamente.',
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

    public function despatch(OrderDispatchRequest $request)
    {
        try {
            $order = Order::with('client', 'order_details')->findOrFail($request->input('id'));

            $order->order_details()->whereIn('status', ['Suspendido'])->update(['status' => 'Cancelado', 'wallet_user_id' => Auth::user()->id, 'wallet_date' => Carbon::now()->format('Y-m-d H:i:s')]);

            $order->wallet_dispatch_official = $order->wallet_dispatch_official ?? $order->seller_dispatch_official;
            $order->wallet_dispatch_document = $order->wallet_dispatch_document ?? $order->seller_dispatch_document;
            $order->dispatch_status = 'Despachado';
            $order->dispatched_date = Carbon::now()->format('Y-m-d H:i:s');
            $order->save();

            $orderDispatch = new OrderDispatch();
            $orderDispatch->client_id = $order->client_id;
            $orderDispatch->consecutive = DB::selectOne('CALL order_dispatches()')->consecutive;
            $orderDispatch->dispatch_user_id = Auth::user()->id;
            $orderDispatch->dispatch_status = 'Despachado';
            $orderDispatch->dispatch_date = Carbon::now()->format('Y-m-d H:i:s');
            $orderDispatch->invoice_user_id = Auth::user()->id;
            $orderDispatch->invoice_date = Carbon::now()->format('Y-m-d H:i:s');
            $orderDispatch->correria_id = $order->correria_id;
            $orderDispatch->business_id = $order->business_id;
            $orderDispatch->save();

            foreach($order->order_details->where('status', 'Autorizado') as $orderDetail) {

                $orderDetail->dispatch_user_id = Auth::user()->id;
                $orderDetail->dispatch_date = Carbon::now()->format('Y-m-d H:i:s');
                $orderDetail->status = 'Despachado';
                $orderDetail->save();

                $orderDispatchDetail = new OrderDispatchDetail();
                $orderDispatchDetail->order_dispatch_id = $orderDispatch->id;
                $orderDispatchDetail->order_id = $orderDetail->order_id;
                $orderDispatchDetail->order_detail_id = $orderDetail->id;
                $orderDispatchDetail->T04 = $orderDetail->T04;
                $orderDispatchDetail->T06 = $orderDetail->T06;
                $orderDispatchDetail->T08 = $orderDetail->T08;
                $orderDispatchDetail->T10 = $orderDetail->T10;
                $orderDispatchDetail->T12 = $orderDetail->T12;
                $orderDispatchDetail->T14 = $orderDetail->T14;
                $orderDispatchDetail->T16 = $orderDetail->T16;
                $orderDispatchDetail->T18 = $orderDetail->T18;
                $orderDispatchDetail->T20 = $orderDetail->T20;
                $orderDispatchDetail->T22 = $orderDetail->T22;
                $orderDispatchDetail->T24 = $orderDetail->T24;
                $orderDispatchDetail->T26 = $orderDetail->T26;
                $orderDispatchDetail->T28 = $orderDetail->T28;
                $orderDispatchDetail->T30 = $orderDetail->T30;
                $orderDispatchDetail->T32 = $orderDetail->T32;
                $orderDispatchDetail->T34 = $orderDetail->T34;
                $orderDispatchDetail->T36 = $orderDetail->T36;
                $orderDispatchDetail->T38 = $orderDetail->T38;
                $orderDispatchDetail->TXXS = $orderDetail->TXXS;
                $orderDispatchDetail->TXS = $orderDetail->TXS;
                $orderDispatchDetail->TS = $orderDetail->TS;
                $orderDispatchDetail->TM = $orderDetail->TM;
                $orderDispatchDetail->TL = $orderDetail->TL;
                $orderDispatchDetail->TXL = $orderDetail->TXL;
                $orderDispatchDetail->TXXL = $orderDetail->TXXL;
                $orderDispatchDetail->user_id = Auth::user()->id;
                $orderDispatchDetail->status = 'Despachado';
                $orderDispatchDetail->date = Carbon::now()->format('Y-m-d H:i:s');
                $orderDispatchDetail->save();
            }

            return $this->successResponse(
                [
                    'order' => $order
                ],
                'El pedido fue despachado exitosamente.',
                201
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

    public function wallet($id)
    {
        try {
            $order = Order::with([
                    'seller_user', 'client.wallet',
                    'client' => fn($query) => $query->withTrashed(),
                    'business' => fn($query) => $query->withTrashed()
                ])->findOrFail($id);

            $pdf = PDF::loadView('Dashboard.Orders.Wallet', compact('order'))->setOptions(['isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true]);

            $pdf->setEncryption($order->client->client_number_document, $order->business->name, ['print']);

            // return $pdf->stream("CARTERA {$order->client->client_name}.pdf");

            $path = "Wallets/CARTERA_{$order->client->client_number_document}.pdf";

            if (Storage::disk('public')->exists($path)) {
                Storage::disk('public')->delete($path);
            }

            Storage::disk('public')->put($path, $pdf->output());

            $file = Storage::disk('public')->path($path);

            $emails = [
                $order->client->email
            ];

            // return view('Dashboard.Emails.Wallet', compact('order'));

            Mail::to($emails)->send(new EmailWallet($order, $file));

            return $this->successResponse(
                [
                    'order' => $order
                ],
                'Se ha identificado una deuda pendiente del cliente, y se ha enviado una notificación via correo electrónico con la informacion detallada de la deuda en las edades de mora.',
                204
            );
        } catch (TransportException $e) {
            return $this->errorResponse(
                [
                    'message' => $this->getMessage('TransportException'),
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

    public function email($id)
    {
        try {
            $order = Order::with([
                    'order_details.product', 'order_details.color',
                    'client' => fn($query) => $query->withTrashed(),
                    'seller_user' => fn($query) => $query->withTrashed(),
                    'wallet_user' => fn($query) => $query->withTrashed(),
                    'correria' => fn($query) => $query->withTrashed(),
                    'business' => fn($query) => $query->withTrashed(),
                ])
                ->findOrFail($id);

            $orderSizes = collect([]);

            $sizes = Size::all();

            foreach($sizes as $size) {
                if($order->order_details->pluck("T{$size->code}")->sum() > 0) {
                    $orderSizes = $orderSizes->push($size);
                }
            }

            $pdf = PDF::loadView('Dashboard.Orders.PDF', compact('order', 'orderSizes'))->setOptions(['isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true]);

            $pdf->setEncryption($order->client->client_number_document, $order->business->name, ['print']);

            // return $pdf->stream("CARTERA {$order->client->client_name}.pdf");

            $path = "Orders/PEDIDO_N_{$order->id}.pdf";

            if (Storage::disk('public')->exists($path)) {
                Storage::disk('public')->delete($path);
            }

            Storage::disk('public')->put($path, $pdf->output());

            $file = Storage::disk('public')->path($path);

            $emails = [
                $order->client->email
            ];

            // return view('Dashboard.Emails.Order', compact('order'));

            Mail::to($emails)->send(new EmailOrder($order, $file));

            return $this->successResponse(
                [
                    'order' => $order
                ],
                'El correo de confirmación de la orden de pedido ha sido enviado al cliente exitosamente, incluyendo un PDF con la información detallada del pedido.',
                204
            );
        } catch (TransportException $e) {
            return $this->errorResponse(
                [
                    'message' => $this->getMessage('TransportException'),
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

    public function download($id)
    {
        try {
            $order = Order::with([
                    'order_details.product', 'order_details.color',
                    'client' => fn($query) => $query->withTrashed(),
                    'seller_user' => fn($query) => $query->withTrashed(),
                    'wallet_user' => fn($query) => $query->withTrashed(),
                    'correria' => fn($query) => $query->withTrashed(),
                    'business' => fn($query) => $query->withTrashed(),
                ])
                ->findOrFail($id);

            $orderSizes = collect([]);

            $sizes = Size::all();

            foreach($sizes as $size) {
                if($order->order_details->pluck("T{$size->code}")->sum() > 0) {
                    $orderSizes = $orderSizes->push($size);
                }
            }

            $pdf = PDF::loadView('Dashboard.Orders.PDF', compact('order', 'orderSizes'))->setOptions(['isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true]);
            //return $pdf->download("PEDIDO N° {$order->id}.pdf");
            return $pdf->stream("PEDIDO N° {$order->id}.pdf");
        } catch (ModelNotFoundException $e) {
            return back()->with('danger', 'Ocurrió un error al cargar el pdf del pedido: ' . $this->getMessage('ModelNotFoundException'));
        } catch (Exception $e) {
            return back()->with('danger', 'Ocurrió un error al cargar la vista: ' . $e->getMessage());
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
