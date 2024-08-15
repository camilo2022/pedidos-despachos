<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\OrderPackage;
use App\Models\Product;
use App\Models\Size;
use App\Traits\ApiMessage;
use App\Traits\ApiResponser;
use Barryvdh\DomPDF\Facade\Pdf as PDF;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Crypt;

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
            $products = Product::with('files')->whereHas('files', fn($query) => $query->where('type', 'PORTADA'))->get();

            return view('Public.Catalogo.Index', compact('products'));
        } catch (Exception $e) {
            return 'Ocurrió un error al cargar el listado de referencias.' . $e->getMessage();
        }
    }

    public function referencia($referecia)
    {
        try {
            $product = Product::with('files')->where('code', $referecia)->firstOrFail();

            return view('Public.Catalogo.Referencia', compact('product'));
        } catch (ModelNotFoundException $e) {
            return 'Ocurrió un error al cargar las imagenes de la referencia: ' . $this->getMessage('ModelNotFoundException');
        } catch (Exception $e) {
            return 'Ocurrió un error al cargar las imagenes de la referencia.';
        }
    }
}
