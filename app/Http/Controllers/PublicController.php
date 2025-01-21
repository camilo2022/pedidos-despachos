<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Business;
use App\Models\Inventory;
use App\Models\OrderDetail;
use App\Models\OrderPackage;
use App\Models\Product;
use App\Models\Size;
use App\Traits\ApiMessage;
use App\Traits\ApiResponser;
use Barryvdh\DomPDF\Facade\Pdf as PDF;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;

class PublicController extends Controller
{
    use ApiResponser;
    use ApiMessage;

    public function packageDetail($token)
    {
        try {
            $id = Crypt::decrypt($token);

            $orderPackage = OrderPackage::with([
                    'order_packing_details.order_dispatch_detail.order_detail.product',
                    'order_packing_details.order_dispatch_detail.order_detail.color',
                    'order_packing.order_dispatch.dispatch_user',
                    'order_packing.order_dispatch.business',
                    'order_packing.order_dispatch.client',
                    'package_type', 'order_packing.packing_user'
                ])->findOrFail($id);

            $orderPackageSizes = collect([]);

            $sizes = Size::all();

            foreach($sizes as $size) {
                if($orderPackage->order_packing_details->pluck("T{$size->code}")->sum() > 0) {
                    $orderPackageSizes = $orderPackageSizes->push($size);
                }
            }

            $pdf = PDF::loadView('Public.OrderDispatches.DetailDownload', compact('orderPackage', 'orderPackageSizes'))->setOptions(['isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true]);

            return $pdf->stream("{$orderPackage->order_packing->order_dispatch->consecutive}-{$orderPackage->package_type->name}.pdf");
        } catch (ModelNotFoundException $e) {
            return 'Ocurrió un error al cargar el pdf del detalle de la orden de despacho del pedido: ' . $this->getMessage('ModelNotFoundException');
        } catch (Exception $e) {
            return 'Ocurrió un error al cargar el pdf del detalle de la orden de despacho del pedido.';
        }
    }

    public function catalogo()
    {
        try {
            $products = Product::with('files')->whereHas('files', fn($query) => $query->whereIn('type', ['PORTADA', 'IMAGEN']))->get();

            return view('Public.Catalogo.Index', compact('products'));
        } catch (Exception $e) {
            return 'Ocurrió un error al cargar el listado de referencias.' . $e->getMessage();
        }
    }

    public function referencia(string $referencia, int $business_id = 1)
    {
        try {
            $product = Product::with('files')->where('code', $referencia)->firstOrFail();
            $warehouses = Business::with('warehouses')->find($business_id);
            $to_transit = $warehouses?->warehouses->where('to_transit', true)->pluck('id')->toArray() ?? [];
            $to_discount = $warehouses?->warehouses->where('to_discount', true)->pluck('id')->toArray() ?? [];
            $inventory = (object) [];
            $inventory->to_transit = $this->inventory($to_transit, $referencia, ['SIESA', 'VISUAL TNS', 'BMI'], $to_discount);
            $inventory->to_discount = $this->inventory($to_discount, $referencia);
            $committed = $this->committed($referencia, $business_id);

            $referenciaSizes = collect([]);

            $sizes = Size::all();

            foreach($sizes as $size) {
                if($inventory->to_transit->pluck("T{$size->code}")->sum() + $inventory->to_discount->pluck("T{$size->code}")->sum() + $committed->pluck("T{$size->code}")->sum()) {
                    $referenciaSizes = $referenciaSizes->push($size);
                }
            }

            return view('Public.Catalogo.Referencia', compact('product', 'inventory', 'committed', 'referenciaSizes'));
        } catch (ModelNotFoundException $e) {
            return redirect()->route('Public.Catalogo.Index')->with('info', "La referencia $referencia no está registrada en nuestra base de datos.")->setStatusCode(404);
        } catch (Exception $e) {
            return redirect()->route('Public.Catalogo.Index')->with('error', "Ocurrió un error al cargar la referencia $referencia.")->setStatusCode(500);
        }
    }

    private function inventory(array $warehouses, string $referencia, array $systems = ['SIESA', 'VISUAL TNS', 'BMI'], array|bool $proyeccion = false)
    {
        try {
            $sizes = Size::all();
            $inventory = Inventory::select('products.code AS REFERENCIA', 'colors.name AS COLOR');
            foreach ($sizes as $size) {
                $inventory->addSelect(DB::raw("COALESCE(SUM(CASE WHEN sizes.code = '$size->code' THEN inventories.quantity ELSE 0 END), 0) AS T$size->code"));
            }
            $inventory->join('warehouses', 'warehouses.id', 'inventories.warehouse_id')
            ->join('products', 'products.id', 'inventories.product_id')
            ->join('colors', 'colors.id', 'inventories.color_id')
            ->join('sizes', 'sizes.id', 'inventories.size_id')
            ->where('quantity', '>', 0)
            ->where('products.code', $referencia)
            ->where(function ($query) use ($warehouses, $systems, $proyeccion) {
                $query->where(function ($subQuery) use ($warehouses, $systems) {
                    $subQuery->whereIn('warehouse_id', $warehouses)
                    ->whereIn('system', $systems);
                })
                ->when(is_array($proyeccion),
                    function ($query) use ($proyeccion) {
                        $query->orWhere(function ($subQuery) use ($proyeccion) {
                            $subQuery->whereIn('warehouse_id', $proyeccion)
                            ->whereIn('system', ['PROYECCION']);
                        }
                    );
                });
            })
            ->groupBy('products.code', 'colors.name');

            $inventory = $inventory->get();

            return $inventory;
        } catch (Exception $e) {
            return collect([]);
        }
    }

    private function committed(string $referencia, int $business_id)
    {
        try {
            $sizes = Size::all();
            $committed = OrderDetail::select('products.code AS REFERENCIA', 'colors.name AS COLOR');
            foreach ($sizes as $size) {
                $committed->addSelect(DB::raw("SUM(T$size->code) as T$size->code"));
            }
            $committed->join('orders', 'orders.id', 'order_details.order_id')
            ->join('users', 'users.id', 'orders.seller_user_id')
            ->join('products', 'products.id', 'order_details.product_id')
            ->join('colors', 'colors.id', 'order_details.color_id')
            ->where('products.code', $referencia)
            ->where('orders.business_id', $business_id)
            ->whereIn('orders.seller_status', ['Aprobado'])
            ->whereIn('orders.wallet_status', ['Pendiente', 'Aprobado', 'Parcialmente Aprobado'])
            ->whereIn('order_details.status', ['Pendiente', 'Aprobado', 'Comprometido'])
            ->whereNot('users.title', 'VENDEDOR ESPECIAL')
            ->groupBy('products.code', 'colors.name');

            $committed = $committed->get();

            return $committed;
        } catch (Exception $e) {
            return collect([]);
        }
    }
}
