<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Inventory;
use App\Models\OrderDetail;
use App\Models\Size;
use App\Models\Warehouse;
use App\Traits\ApiMessage;
use App\Traits\ApiResponser;
use Barryvdh\DomPDF\Facade\Pdf as PDF;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ReportProductsController extends Controller
{
    use ApiResponser;
    use ApiMessage;public function index()
    {
        try {
            $sizes = Size::all();

            return view('Dashboard.Reports.IndexProducts', compact('sizes'));
        } catch (Exception $e) {
            return back()->with('danger', 'Ocurrió un error al cargar la vista: ' . $e->getMessage());
        }
    }

    public function indexQuery()
    {
        try {
            $sizes = Size::all();
            $saleSumColumns = []; 
            $sales = OrderDetail::select('products.trademark', 'products.code AS product', 'colors.code AS color', DB::raw("CASE WHEN clients.client_number_document = '88242589' THEN 'VENTA MEDELLIN' ELSE 'VENTA NACIONAL' END AS type"));
            foreach ($sizes as $size) {
                $sales->addSelect(DB::raw("COALESCE(SUM(COALESCE(order_dispatch_details.T$size->code, order_details.T$size->code)) * -1, 0) AS T$size->code"));
                $saleSumColumns[] = "COALESCE(order_dispatch_details.T$size->code, order_details.T$size->code)";
            }
            $saleSumColumnsString = implode(' + ', $saleSumColumns);
            $sales->addSelect(DB::raw("SUM($saleSumColumnsString) * -1 AS TOTAL"))
            ->join('products', 'order_details.product_id', '=', 'products.id')
            ->join('colors', 'order_details.color_id', '=', 'colors.id')
            ->leftJoin('order_dispatch_details', 'order_details.id', '=', 'order_dispatch_details.order_detail_id')
            ->join('orders', 'order_details.order_id', '=', 'orders.id')
            ->join('clients', 'orders.client_id', '=', 'clients.id')
            ->join('correrias', 'orders.correria_id', '=', 'correrias.id')
            ->where('orders.business_id', Auth::user()->business_id)
            ->whereNull('correrias.deleted_at')
            ->groupBy('products.trademark', 'products.code', 'colors.code', DB::raw("CASE WHEN clients.client_number_document = '88242589' THEN 'VENTA MEDELLIN' ELSE 'VENTA NACIONAL' END"));

            $inventorySumColumns = [];
            $inventories = Inventory::select('products.trademark', 'products.code AS product', 'colors.code AS color', DB::raw("'INVENTARIO' AS type"));
            foreach ($sizes as $size) {
                $inventories->addSelect(DB::raw("COALESCE(SUM(CASE WHEN sizes.code = '$size->code' THEN inventories.quantity ELSE 0 END), 0) AS T$size->code"));
                $inventorySumColumns[] = "COALESCE(SUM(CASE WHEN sizes.code = '$size->code' THEN inventories.quantity ELSE 0 END), 0)";
            }
            $inventorySumColumnsString = implode(' + ', $inventorySumColumns);
            $inventories->addSelect(DB::raw("$inventorySumColumnsString AS TOTAL"))
            ->join('warehouses', 'warehouses.id', 'inventories.warehouse_id')
            ->join('products', 'products.id', 'inventories.product_id')
            ->join('colors', 'colors.id', 'inventories.color_id')
            ->join('sizes', 'sizes.id', 'inventories.size_id')
            ->where('warehouses.to_discount', true)
            ->where('inventories.quantity', '>', 0)
            ->groupBy('products.trademark', 'products.code', 'colors.code', 'type');

            $products = $sales->union($inventories)->get();

            return datatables()->of($products)->toJson();
            
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

    public function download()
    {
        try {
            $warehouses = Warehouse::with('businesses')->whereHas('businesses', fn($query) => $query->where('businesses.id', Auth::user()->business_id))->where('to_discount', true)->get();
            
            $inventories = Inventory::whereIn('warehouse_id', $warehouses->pluck('id')->toArray())->get();

            $sales = OrderDetail::select('order_details.*',
                DB::raw("(T04 + T06 + T08 + T10 + T12 + T14 + T16 + T18 + T20 + T22 + T24 + T26 + T28 + T30 + T32 + T34 + T36 + T38 + TXXS + TXS + TS + TM + TL + TXL + TXXL) AS TOTAL")
            )
            ->with([ 'order.correria' => fn($query) => $query->withTrashed(),
                'order_dispatch_detail' => fn($query) => $query->select('order_dispatch_details.*', DB::raw("(T04 + T06 + T08 + T10 + T12 + T14 + T16 + T18 + T20 + T22 + T24 + T26 + T28 + T30 + T32 + T34 + T36 + T38 + TXXS + TXS + TS + TM + TL + TXL + TXXL) AS TOTAL")),
            ])
            ->whereIn('status', ['Pendiente', 'Aprobado', 'Autorizado', 'Comprometido', 'Despachado'])
            ->whereHas('order', fn($query) => $query->where('business_id', Auth::user()->business_id))
            ->whereHas('order.correria', fn($query) => $query->whereNull('deleted_at'))
            ->get();

            $products = Inventory::with('product', 'color')
                ->groupBy('product_id', 'color_id')
                ->select('product_id', 'color_id')
                ->get();

            $trademarks = $products->pluck('product')->pluck('trademark')->unique()->values();

            $collect = collect([]);

            foreach($trademarks as $trademark) {
                $items = $products->where('product.trademark', $trademark)->values();

                $object = (object) [
                    'TRADEMARK' => $trademark,
                    'INVENTARIO' => 0,
                    'VENTA' => 0,
                    'DISPONIBLE' => 0,
                    'CANTIDAD' => 0,
                    'REFERENCIAS' => collect([])
                ];

                foreach($items as $item) {
                    $inventory = $inventories->where('product_id', $item->product_id)->where('color_id', $item->color_id)->pluck('quantity')->sum();

                    $sale = $sales->where('product_id', $item->product_id)->where('color_id', $item->color_id)->whereIn('status', ['Pendiente', 'Aprobado', 'Autorizado'])->sum('TOTAL');
                    $sale += $sales->where('product_id', $item->product_id)->where('color_id', $item->color_id)->whereIn('status', ['Comprometido', 'Despachado'])->sum('order_dispatch_detail.TOTAL');

                    $subObject = (object) [
                        'PRODUCT' => $item->product,
                        'COLOR' => $item->color,
                        'INVENTARIO' => $inventory,
                        'VENTA' => $sale,
                        'DISPONIBLE' => $inventory - $sale,
                        'CANTIDAD' => $inventory + $sale
                    ];

                    $object->REFERENCIAS = $object->REFERENCIAS->push($subObject);
                }

                $object->INVENTARIO = $object->REFERENCIAS->sum('INVENTARIO');
                $object->VENTA = $object->REFERENCIAS->sum('VENTA');
                $object->DISPONIBLE = $object->REFERENCIAS->sum('DISPONIBLE');
                $object->CANTIDAD = $object->REFERENCIAS->sum('CANTIDAD');

                $object->REFERENCIAS = $object->REFERENCIAS->sortByDesc('VENTA')->values();

                $collect = $collect->push($object);
            }
            $collect = $collect->sortByDesc('VENTA')->values();

            $pdf = PDF::loadView('Dashboard.Reports.PDFProducts', compact('collect'))->setOptions(['isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true]);
            
            return $pdf->stream("REPORTE CONSOLIDADOR PRODUCTOS.pdf");
        } catch (Exception $e) {
            return back()->with('danger', 'Ocurrió un error al cargar el reporte de pdf por marcas - referencias: ' . $e->getMessage());
        }
    }
}
