<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\OrderDispatch\OrderDispatchApproveRequest;
use App\Http\Requests\OrderDispatch\OrderDispatchCancelRequest;
use App\Http\Requests\OrderDispatch\OrderDispatchIndexQueryRequest;
use App\Http\Requests\OrderDispatch\OrderDispatchInvoiceRequest;
use App\Http\Requests\OrderDispatch\OrderDispatchPackingRequest;
use App\Http\Requests\OrderDispatch\OrderDispatchPendingRequest;
use App\Http\Requests\OrderDispatch\OrderDispatchPickingRequest;
use App\Http\Resources\OrderDispatch\OrderDispatchIndexQueryCollection;
use App\Models\File;
use App\Models\Invoice;
use App\Models\OrderDispatch;
use App\Models\OrderPacking;
use App\Models\OrderPicking;
use App\Models\OrderPickingDetail;
use App\Models\Size;
use App\Traits\ApiMessage;
use App\Traits\ApiResponser;
use Barryvdh\DomPDF\Facade\Pdf as PDF;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class OrderDispatchController extends Controller
{
    use ApiResponser;
    use ApiMessage;
    
    public function __construct()
    {
        $this->middleware('check.order.picking');
        $this->middleware('check.order.packing');
    }

    public function index()
    {
        try {
            return view('Dashboard.OrderDispatches.Index');
        } catch (Exception $e) {
            return back()->with('danger', 'Ocurrió un error al cargar la vista: ' . $e->getMessage());
        }
    }

    public function indexQuery(OrderDispatchIndexQueryRequest $request)
    {
        try {
            $start_date = Carbon::parse($request->input('start_date'))->startOfDay();
            $end_date = Carbon::parse($request->input('end_date'))->endOfDay();
            //Consulta por nombre
            $orderDispatches = OrderDispatch::with([
                'order_dispatch_details.order',
                'order_dispatch_details.order_detail.product' => fn($query) => $query->withTrashed(),
                'order_dispatch_details.order_detail.color' => fn($query) => $query->withTrashed(),
                'client' => fn($query) => $query->withTrashed(),
                'dispatch_user' => fn($query) => $query->withTrashed(),
                'invoice_user' => fn($query) => $query->withTrashed(),
                'correria' => fn($query) => $query->withTrashed(),
                'business' => fn($query) => $query->withTrashed(),
                'order_picking.picking_user' => fn($query) => $query->withTrashed(),
                'order_packing.packing_user' => fn($query) => $query->withTrashed()
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
            ->when(in_array(Auth::user()->title, ['SUPER ADMINISTRADOR', 'ADMINISTRADOR', 'COORDINADOR BODEGA']),
                function ($query) {
                    $query->whereIn('dispatch_status', ['Pendiente', 'Cancelado', 'Alistamiento', 'Revision', 'Empacado', 'Facturacion', 'Despachado']);
                }
            )
            ->when(in_array(Auth::user()->title, ['FILTRADOR']),
                function ($query) {
                    $query->whereIn('dispatch_status', ['Pendiente', 'Cancelado', 'Alistamiento', 'Despachado']);
                }
            )
            ->when(in_array(Auth::user()->title, ['BODEGA']),
                function ($query) {
                    $query->whereIn('dispatch_status', ['Alistamiento', 'Empacado']);
                }
            )
            ->when(in_array(Auth::user()->title, ['FACTURADOR']),
                function ($query) {
                    $query->whereIn('dispatch_status', ['Facturacion', 'Despachado']);
                }
            )
            ->when(in_array(Auth::user()->title, ['VENDEDOR', 'VENDEDOR ESPECIAL', 'CARTERA', 'PROMOTORA', 'COORDINADOR PROMOTORA', 'USUARIO']),
                function ($query) {
                    $query->whereNotIn('dispatch_status', ['Pendiente', 'Cancelado', 'Alistamiento', 'Revision', 'Empacado', 'Facturacion', 'Despachado']);
                }
            )
            ->orderBy($request->input('column'), $request->input('dir'))
            ->paginate($request->input('perPage'));

            return $this->successResponse(
                new OrderDispatchIndexQueryCollection($orderDispatches),
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

    public function pending(OrderDispatchPendingRequest $request)
    {
        try {
            $orderDispatch = OrderDispatch::with('order_dispatch_details.order_detail.order')->findOrFail($request->input('id'));

            $orderDispatch->order_dispatch_details()->whereIn('status', ['Alistamiento'])->update(['status' => 'Pendiente', 'date' => Carbon::now()->format('Y-m-d H:i:s')]);

            $orderDispatch->dispatch_status = 'Pendiente';
            $orderDispatch->save();

            return $this->successResponse(
                [
                    'orderDispatch' => $orderDispatch
                ],
                'La orden de despacho fue devuelta exitosamente.',
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

    public function approve(OrderDispatchApproveRequest $request)
    {
        try {
            $orderDispatch = OrderDispatch::with('order_dispatch_details')->findOrFail($request->input('id'));

            $orderDispatch->order_dispatch_details()->whereIn('status', ['Pendiente'])->update(['status' => 'Alistamiento', 'date' => Carbon::now()->format('Y-m-d H:i:s')]);

            $orderDispatch->dispatch_status = 'Alistamiento';
            $orderDispatch->save();

            return $this->successResponse(
                [
                    'orderDispatch' => $orderDispatch
                ],
                'La orden de despacho fue aprobada exitosamente.',
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

    public function cancel(OrderDispatchCancelRequest $request)
    {
        try {
            $orderDispatch = OrderDispatch::with('order_dispatch_details.order_detail.order')->findOrFail($request->input('id'));

            foreach ($orderDispatch->order_dispatch_details as $order_dispatch_detail) {
                $order_dispatch_detail->order_detail->status = 'Aprobado';
                $order_dispatch_detail->order_detail->save();

                $order_dispatch_detail->status = 'Cancelado';
                $order_dispatch_detail->save();
                
                DB::statement('CALL order_dispatch_status(?)', [$order_dispatch_detail->order_detail->order->id]);
            }

            $orderDispatch->dispatch_status = 'Cancelado';
            $orderDispatch->save();

            return $this->successResponse(
                [
                    'orderDispatch' => $orderDispatch
                ],
                'La orden de despacho fue cancelada exitosamente.',
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

    public function picking(OrderDispatchPickingRequest $request)
    {
        try {
            $orderDispatch = OrderDispatch::with('order_dispatch_details')->findOrFail($request->input('id'));

            $orderPicking = new OrderPicking();
            $orderPicking->order_dispatch_id = $orderDispatch->id;
            $orderPicking->picking_user_id = Auth::user()->id;
            $orderPicking->picking_date = Carbon::now()->format('Y-m-d H:i:s');
            $orderPicking->save();

            foreach ($orderDispatch->order_dispatch_details->whereIn('status', ['Alistamiento']) as $order_dispatch_detail) {
                $orderPickingDetail = new OrderPickingDetail();
                $orderPickingDetail->order_picking_id = $orderPicking->id;
                $orderPickingDetail->order_dispatch_detail_id = $order_dispatch_detail->id;
                $orderPickingDetail->date = Carbon::now()->format('Y-m-d H:i:s');
                $orderPickingDetail->save();
            }

            return $this->successResponse(
                [
                    'orderDispatch' => $orderDispatch,
                    'urlOrderPicking' => URL::route('Dashboard.Pickings.Index', ['id' => $orderPicking->id])
                ],
                'La orden de alistamiento creada exitosamente.',
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

    public function review($id)
    {
        try {
            $orderDispatch = OrderDispatch::with([
                    'client' => fn($query) => $query->withTrashed(), 
                    'dispatch_user' => fn($query) => $query->withTrashed(),
                    'invoice_user' => fn($query) => $query->withTrashed(),
                    'correria' => fn($query) => $query->withTrashed(), 
                    'business' => fn($query) => $query->withTrashed(),
                    'order_picking.picking_user' => fn($query) => $query->withTrashed(),
                    'order_packing.packing_user' => fn($query) => $query->withTrashed(),
                    'order_packing.order_picking_details.order_dispatch_detail.order_detail.product',
                    'order_packing.order_picking_details.order_dispatch_detail.order_detail.color',
                    'order_packing.order_picking_details.order_dispatch_detail.order',
                    'order_dispatch_details' => fn($query) => $query->whereNotIn('status', ['Cancelado']),
                    'order_dispatch_details.order_detail.product',
                    'order_dispatch_details.order_detail.color',
                    'order_dispatch_details.order'
                ])->findOrFail($id);

            $orderDispatchSizes = collect([]);

            $sizes = Size::all();
            
            foreach($sizes as $size) {
                if($orderDispatch->order_dispatch_details->pluck("T{$size->code}")->sum() > 0) {
                    $orderDispatchSizes = $orderDispatchSizes->push($size);
                }
            }

            return view('Dashboard.OrderDispatches.Review', compact('orderDispatch', 'orderDispatchSizes'));
        } catch (ModelNotFoundException $e) {
            return back()->with('danger', 'Ocurrió un error al buscar la orden de despacho: ' . $this->getMessage('ModelNotFoundException'));
        } catch (Exception $e) {
            return back()->with('danger', 'Ocurrió un error al cargar la vista: ' . $e->getMessage());
        }
    }

    public function packing(OrderDispatchPackingRequest $request)
    {
        try {
            $orderDispatch = OrderDispatch::with('order_dispatch_details')->findOrFail($request->input('id'));

            $orderPacking = new OrderPacking();            
            $orderPacking->order_dispatch_id = $orderDispatch->id;
            $orderPacking->packing_user_id = Auth::user()->id;
            $orderPacking->packing_date = Carbon::now()->format('Y-m-d H:i:s');
            $orderPacking->save();

            return $this->successResponse(
                [
                    'orderDispatch' => $orderDispatch,
                    'urlOrderPacking' => URL::route('Dashboard.Packings.Index', ['id' => $orderPacking->id])
                ],
                'La orden de empacado creada exitosamente.',
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
            $orderDispatch = OrderDispatch::with([
                    'client' => fn($query) => $query->withTrashed(), 
                    'dispatch_user' => fn($query) => $query->withTrashed(),
                    'invoice_user' => fn($query) => $query->withTrashed(),
                    'correria' => fn($query) => $query->withTrashed(), 
                    'business' => fn($query) => $query->withTrashed(),
                ])->findOrFail($id);

                return $this->successResponse(
                    [
                        'orderDispatch' => $orderDispatch
                    ],
                    'La orden de alistamiento fue encontrada exitosamente.',
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

    public function invoice(OrderDispatchInvoiceRequest $request)
    {
        try {
            $orderDispatch = OrderDispatch::with('order_dispatch_details.order_detail.order')->findOrFail($request->input('id'));

            foreach($request->input('invoices') as $index => $item){
                $invoice = new Invoice();
                $invoice->model_id = $request->input('id');
                $invoice->model_type = OrderDispatch::class;
                $invoice->reference = strtoupper($item['reference']);
                $invoice->save();
                if(!is_null($request->invoices[$index]['supports'])) {
                    foreach($request->invoices[$index]['supports'] as $support) {
                        $file = new File();
                        $file->model_type = Invoice::class;
                        $file->model_id = $invoice->id;
                        $file->type = 'FACTURA';
                        $file->name = $support->getClientOriginalName();
                        $file->path = $support->store('Invoices/' . $invoice->id, 'public');
                        $file->mime = $support->getMimeType();
                        $file->extension = $support->getClientOriginalExtension();
                        $file->size = $support->getSize();
                        $file->user_id = Auth::user()->id;
                        $file->metadata = json_encode((array) stat($support));
                        $file->save();
                    }
                }
            }

            foreach($orderDispatch->order_dispatch_details->whereIn('status', ['Facturacion']) as $order_dispatch_detail) {
                $order_dispatch_detail->order_detail->dispatch_user_id = $orderDispatch->dispatch_user_id;
                $order_dispatch_detail->order_detail->dispatch_date = Carbon::now()->format('Y-m-d H:i:s');
                $order_dispatch_detail->order_detail->status = 'Despachado';
                $order_dispatch_detail->order_detail->save();

                $order_dispatch_detail->status = 'Despachado';
                $order_dispatch_detail->date = Carbon::now()->format('Y-m-d H:i:s');
                $order_dispatch_detail->save();
                
                DB::statement('CALL order_dispatch_status(?)', [$order_dispatch_detail->order_detail->order->id]);
            }

            $orderDispatch->dispatch_status = 'Despachado';
            $orderDispatch->dispatch_date = Carbon::now()->format('Y-m-d H:i:s');
            $orderDispatch->invoice_user_id = Auth::user()->id;
            $orderDispatch->invoice_date = Carbon::now()->format('Y-m-d H:i:s');
            $orderDispatch->save();

            return $this->successResponse(
                [
                    'orderDispatch' => $orderDispatch,
                    'urlDispatchDownload' => URL::route('Dashboard.Dispatches.Download', ['id' => $orderDispatch->id])
                ],
                'Se guardaron las facturas y se asociaron a la orden de despacho y esta fue marcada como despachada exitosamente.',
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

    public function print($id)
    {
        try {
            $orderDispatch = OrderDispatch::with([
                    'order_dispatch_details.order.seller_user',
                    'order_dispatch_details.order_detail',
                    'order_dispatch_details.order_detail.product',
                    'order_dispatch_details.order_detail.color',
                    'order_dispatch_details' => fn($query) => $query->whereNotIn('status', ['Cancelado']),
                    'client' => fn($query) => $query->withTrashed(),
                    'dispatch_user' => fn($query) => $query->withTrashed(),
                    'correria' => fn($query) => $query->withTrashed(),
                ])
                ->findOrFail($id);
                
            $orderDispatchSizes = collect([]);
    
            $sizes = Size::all();
                
            foreach($sizes as $size) {
                if($orderDispatch->order_dispatch_details->pluck("T{$size->code}")->sum() > 0) {
                    $orderDispatchSizes = $orderDispatchSizes->push($size);
                }
            }

            $pdf = PDF::loadView('Dashboard.OrderDispatches.Print', compact('orderDispatch', 'orderDispatchSizes'))->setPaper('a4', 'landscape')->setOptions(['isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true]);

            return $pdf->stream("ORDEN N° {$orderDispatch->consecutive}.pdf");
        } catch (ModelNotFoundException $e) {
            return back()->with('danger', 'Ocurrió un error al cargar el pdf de la orden de despacho del pedido: ' . $this->getMessage('ModelNotFoundException'));
        } catch (Exception $e) {
            return back()->with('danger', 'Ocurrió un error al cargar la vista: ' . $e->getMessage());
        }
    }

    public function download($id)
    {
        try {
            $orderDispatch = OrderDispatch::with([
                    'order_dispatch_details.order_detail.order',
                    'order_packing.order_packages.order_packing_details',
                    'order_packing.packing_user' => fn($query) => $query->withTrashed(),
                    'client' => fn($query) => $query->withTrashed(),
                    'dispatch_user' => fn($query) => $query->withTrashed(),
                    'invoice_user' => fn($query) => $query->withTrashed(),
                    'correria' => fn($query) => $query->withTrashed(),
                    'business' => fn($query) => $query->withTrashed()
                ])
                ->findOrFail($id);
    
            $sizes = Size::all();

            foreach($orderDispatch->order_packing->order_packages as $index => $orderPackage) {
                $encryptedId = Crypt::encrypt($orderPackage->id);
                $url = URL::route('Public.Packages.Detail', ['token' => $encryptedId]);
                $orderPackage->qrCode = QrCode::size(100)->generate($url);
            }

            $pdf = PDF::loadView('Dashboard.OrderDispatches.Download', compact('orderDispatch', 'sizes'))->setOptions(['isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true]);

            return $pdf->stream("ROTULO N° {$orderDispatch->consecutive}.pdf");
        } catch (ModelNotFoundException $e) {
            return back()->with('danger', 'Ocurrió un error al cargar el pdf de la orden de despacho del pedido: ' . $this->getMessage('ModelNotFoundException'));
        } catch (Exception $e) {
            return back()->with('danger', 'Ocurrió un error al cargar la vista: ' . $e->getMessage());
        }
    }
}
