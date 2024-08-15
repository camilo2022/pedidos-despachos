<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Inventory;
use App\Models\OrderDetail;
use App\Models\Size;
use App\Traits\ApiMessage;
use App\Traits\ApiResponser;
use App\Traits\Trademark;
use Barryvdh\DomPDF\Facade\Pdf as PDF;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use QuickChart;

class ReportTrademarksController extends Controller
{
    use ApiResponser;
    use ApiMessage;
    use Trademark;

    public function index()
    {
        try {
            $sizes = Size::all();

            return view('Dashboard.Reports.IndexTrademarks', compact('sizes'));
        } catch (Exception $e) {
            return back()->with('danger', 'Ocurrió un error al cargar la vista: ' . $e->getMessage());
        }
    }

    public function indexQuery()
    {
        try {
            $sizes = Size::all();
            $saleSumColumns = []; 
            $sales = OrderDetail::select('products.trademark', DB::raw("CASE WHEN clients.client_number_document = '88242589' THEN 'VENTA MEDELLIN' ELSE 'VENTA NACIONAL' END AS type"));
            foreach ($sizes as $size) {
                $sales->addSelect(DB::raw("COALESCE(SUM(COALESCE(order_dispatch_details.T$size->code, order_details.T$size->code)) * -1, 0) AS T$size->code"));
                $saleSumColumns[] = "COALESCE(order_dispatch_details.T$size->code, order_details.T$size->code)";
            }
            $saleSumColumnsString = implode(' + ', $saleSumColumns);
            $sales->addSelect(DB::raw("SUM($saleSumColumnsString) * -1 AS TOTAL"))
            ->join('products', 'order_details.product_id', '=', 'products.id')
            ->leftJoin('order_dispatch_details', 'order_details.id', '=', 'order_dispatch_details.order_detail_id')
            ->join('orders', 'order_details.order_id', '=', 'orders.id')
            ->join('clients', 'orders.client_id', '=', 'clients.id')
            ->join('correrias', 'orders.correria_id', '=', 'correrias.id')
            ->where('orders.business_id', Auth::user()->business_id)
            ->whereNull('correrias.deleted_at')
            ->groupBy('products.trademark', DB::raw("CASE WHEN clients.client_number_document = '88242589' THEN 'VENTA MEDELLIN' ELSE 'VENTA NACIONAL' END"));

            $inventorySumColumns = [];
            $inventories = Inventory::select('products.trademark', DB::raw("'INVENTARIO' AS type"));
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
            ->groupBy('products.trademark', 'type');

            $trademarks = $sales->union($inventories)->get();

            return datatables()->of($trademarks)->toJson();
            
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
            $trademarks = $this->trademarks();

            $items = OrderDetail::select(
                'order_details.*', DB::raw("(T04 + T06 + T08 + T10 + T12 + T14 + T16 + T18 + T20 + T22 + T24 + T26 + T28 + T30 + T32 + T34 + T36 + T38 + TXXS + TXS + TS + TM + TL + TXL + TXXL) AS TOTAL")
            )
            ->with([
                'product', 'color', 'seller_user', 'wallet_user', 'dispatch_user', 'order.client', 'order.seller_user', 'order.wallet_user', 'order.correria' => fn($query) => $query->withTrashed(),
                'order_dispatch_detail.order_dispatch.dispatch_user', 'order_dispatch_detail.order_dispatch.invoice_user', 'order_dispatch_detail.user',
                'order_dispatch_detail' => fn($query) => $query->select('order_dispatch_details.*', DB::raw("(T04 + T06 + T08 + T10 + T12 + T14 + T16 + T18 + T20 + T22 + T24 + T26 + T28 + T30 + T32 + T34 + T36 + T38 + TXXS + TXS + TS + TM + TL + TXL + TXXL) AS TOTAL")),
            ])
            ->whereHas('order', fn($query) => $query->where('business_id', Auth::user()->business_id))
            ->whereHas('order.correria', fn($query) => $query->whereNull('deleted_at'))
            ->get();

            Storage::disk('public')->deleteDirectory('Charts');
            Storage::disk('public')->makeDirectory('Charts');

            foreach ($trademarks as $trademark => &$item) {

                $item_nacional = $items->whereIn('product.trademark', array_keys($item['TRADEMARKS']))->where('order.client.client_number_document', '!=', '88242589');
                $item_medellin = $items->whereIn('product.trademark', array_keys($item['TRADEMARKS']))->where('order.client.client_number_document', '=', '88242589');

                $item['DATA']['NACIONAL']['AGOTADO'] = $item_nacional->where('status', 'Agotado')->pluck('TOTAL')->sum();
                $item['DATA']['NACIONAL']['PENDIENTE_VENDEDOR'] = $item_nacional->where('order.seller_status', 'Pendiente')->where('status', 'Pendiente')->pluck('TOTAL')->sum();
                $item['DATA']['NACIONAL']['CANCELADO_VENDEDOR'] = $item_nacional->where('order.seller_status', 'Cancelado')->where('status', 'Cancelado')->pluck('TOTAL')->sum();
                $item['DATA']['NACIONAL']['PENDIENTE_CARTERA'] = $item_nacional->where('order.seller_status', 'Aprobado')->where('order.wallet_status', 'Pendiente')->where('status', 'Pendiente')->pluck('TOTAL')->sum();
                $item['DATA']['NACIONAL']['APROBADO_CARTERA'] = $item_nacional->whereIn('order.wallet_status', ['Parcialmente Aprobado', 'Aprobado', 'Autorizado'])->whereIn('status', ['Autorizado', 'Aprobado'])->pluck('TOTAL')->sum();
                $item['DATA']['NACIONAL']['RECHAZADO_CARTERA'] = $item_nacional->where('order.seller_status', 'Aprobado')->where('status', 'Cancelado')->pluck('TOTAL')->sum();
                $item['DATA']['NACIONAL']['SUSPENDIDO_CARTERA'] = $item_nacional->whereNotIn('order.wallet_status', ['En mora'])->where('status', 'Suspendido')->pluck('TOTAL')->sum();
                $item['DATA']['NACIONAL']['EN_MORA_CARTERA'] = $item_nacional->where('order.wallet_status', 'En mora')->where('status', 'Suspendido')->pluck('TOTAL')->sum();
                $item['DATA']['NACIONAL']['PENDIENTE_DESPACHO'] = $item_nacional->where('status', 'Comprometido')->pluck('order_dispatch_detail')->pluck('TOTAL')->sum();
                $item['DATA']['NACIONAL']['DESPACHADO'] = $item_nacional->where('status', 'Despachado')->pluck('order_dispatch_detail')->pluck('TOTAL')->sum();
                $item['DATA']['NACIONAL']['TOTAL'] = $item_nacional->whereNotIn('status', ['Comprometido', 'Despachado'])->pluck('TOTAL')->sum() + $item_nacional->whereIn('status', ['Comprometido', 'Despachado'])->pluck('order_dispatch_detail')->pluck('TOTAL')->sum();
                
                $item['DATA']['MEDELLIN']['AGOTADO'] = $item_medellin->where('status', 'Agotado')->pluck('TOTAL')->sum();
                $item['DATA']['MEDELLIN']['PENDIENTE_VENDEDOR'] = $item_medellin->where('order.seller_status', 'Pendiente')->where('status', 'Pendiente')->pluck('TOTAL')->sum();
                $item['DATA']['MEDELLIN']['CANCELADO_VENDEDOR'] = $item_medellin->where('order.seller_status', 'Cancelado')->where('status', 'Cancelado')->pluck('TOTAL')->sum();
                $item['DATA']['MEDELLIN']['PENDIENTE_CARTERA'] = $item_medellin->where('order.seller_status', 'Aprobado')->where('order.wallet_status', 'Pendiente')->where('status', 'Pendiente')->pluck('TOTAL')->sum();
                $item['DATA']['MEDELLIN']['APROBADO_CARTERA'] = $item_medellin->whereIn('order.wallet_status', ['Parcialmente Aprobado', 'Aprobado', 'Autorizado'])->whereIn('status', ['Autorizado', 'Aprobado'])->pluck('TOTAL')->sum();
                $item['DATA']['MEDELLIN']['RECHAZADO_CARTERA'] = $item_medellin->where('order.seller_status', 'Aprobado')->where('status', 'Cancelado')->pluck('TOTAL')->sum();
                $item['DATA']['MEDELLIN']['SUSPENDIDO_CARTERA'] = $item_medellin->whereNotIn('order.wallet_status', ['En mora'])->where('status', 'Suspendido')->pluck('TOTAL')->sum();
                $item['DATA']['MEDELLIN']['EN_MORA_CARTERA'] = $item_medellin->where('order.wallet_status', 'En mora')->where('status', 'Suspendido')->pluck('TOTAL')->sum();
                $item['DATA']['MEDELLIN']['PENDIENTE_DESPACHO'] = $item_medellin->where('status', 'Comprometido')->pluck('order_dispatch_detail')->pluck('TOTAL')->sum();
                $item['DATA']['MEDELLIN']['DESPACHADO'] = $item_medellin->where('status', 'Despachado')->pluck('order_dispatch_detail')->pluck('TOTAL')->sum();
                $item['DATA']['MEDELLIN']['TOTAL'] = $item_medellin->whereNotIn('status', ['Comprometido', 'Despachado'])->pluck('TOTAL')->sum() + $item_medellin->whereIn('status', ['Comprometido', 'Despachado'])->pluck('order_dispatch_detail')->pluck('TOTAL')->sum();

                $item['DATA']['TOTAL'] = $item['DATA']['NACIONAL']['TOTAL'] + $item['DATA']['MEDELLIN']['TOTAL'];

                foreach ($item['TRADEMARKS'] as $subTrademark => &$subItem) {

                    $subitem_nacional = $items->where('product.trademark', $subTrademark)->where('order.client.client_number_document', '!=', '88242589');
                    $subitem_medellin = $items->where('product.trademark', $subTrademark)->where('order.client.client_number_document', '=', '88242589');

                    $subItem['DATA']['NACIONAL']['AGOTADO'] = $subitem_nacional->where('status', 'Agotado')->pluck('TOTAL')->sum();
                    $subItem['DATA']['NACIONAL']['PENDIENTE_VENDEDOR'] = $subitem_nacional->where('order.seller_status', 'Pendiente')->where('status', 'Pendiente')->pluck('TOTAL')->sum();
                    $subItem['DATA']['NACIONAL']['CANCELADO_VENDEDOR'] = $subitem_nacional->where('order.seller_status', 'Cancelado')->where('status', 'Cancelado')->pluck('TOTAL')->sum();
                    $subItem['DATA']['NACIONAL']['PENDIENTE_CARTERA'] = $subitem_nacional->where('order.seller_status', 'Aprobado')->where('order.wallet_status', 'Pendiente')->where('status', 'Pendiente')->pluck('TOTAL')->sum();
                    $subItem['DATA']['NACIONAL']['APROBADO_CARTERA'] = $subitem_nacional->whereIn('order.wallet_status', ['Parcialmente Aprobado', 'Aprobado', 'Autorizado'])->whereIn('status', ['Autorizado', 'Aprobado'])->pluck('TOTAL')->sum();
                    $subItem['DATA']['NACIONAL']['RECHAZADO_CARTERA'] = $subitem_nacional->where('order.seller_status', 'Aprobado')->where('status', 'Cancelado')->pluck('TOTAL')->sum();
                    $subItem['DATA']['NACIONAL']['SUSPENDIDO_CARTERA'] = $subitem_nacional->whereNotIn('order.wallet_status', ['En mora'])->where('status', 'Suspendido')->pluck('TOTAL')->sum();
                    $subItem['DATA']['NACIONAL']['EN_MORA_CARTERA'] = $subitem_nacional->where('order.wallet_status', 'En mora')->where('status', 'Suspendido')->pluck('TOTAL')->sum();
                    $subItem['DATA']['NACIONAL']['PENDIENTE_DESPACHO'] = $subitem_nacional->where('status', 'Comprometido')->pluck('order_dispatch_detail')->pluck('TOTAL')->sum();
                    $subItem['DATA']['NACIONAL']['DESPACHADO'] = $subitem_nacional->where('status', 'Despachado')->pluck('order_dispatch_detail')->pluck('TOTAL')->sum();
                    $subItem['DATA']['NACIONAL']['TOTAL'] = $subitem_nacional->whereNotIn('status', ['Comprometido', 'Despachado'])->pluck('TOTAL')->sum() + $subitem_nacional->whereIn('status', ['Comprometido', 'Despachado'])->pluck('order_dispatch_detail')->pluck('TOTAL')->sum();
                    
                    $subItem['DATA']['MEDELLIN']['AGOTADO'] = $subitem_medellin->where('status', 'Agotado')->pluck('TOTAL')->sum();
                    $subItem['DATA']['MEDELLIN']['PENDIENTE_VENDEDOR'] = $subitem_medellin->where('order.seller_status', 'Pendiente')->where('status', 'Pendiente')->pluck('TOTAL')->sum();
                    $subItem['DATA']['MEDELLIN']['CANCELADO_VENDEDOR'] = $subitem_medellin->where('order.seller_status', 'Cancelado')->where('status', 'Cancelado')->pluck('TOTAL')->sum();
                    $subItem['DATA']['MEDELLIN']['PENDIENTE_CARTERA'] = $subitem_medellin->where('order.seller_status', 'Aprobado')->where('order.wallet_status', 'Pendiente')->where('status', 'Pendiente')->pluck('TOTAL')->sum();
                    $subItem['DATA']['MEDELLIN']['APROBADO_CARTERA'] = $subitem_medellin->whereIn('order.wallet_status', ['Parcialmente Aprobado', 'Aprobado', 'Autorizado'])->whereIn('status', ['Autorizado', 'Aprobado'])->pluck('TOTAL')->sum();
                    $subItem['DATA']['MEDELLIN']['RECHAZADO_CARTERA'] = $subitem_medellin->where('order.seller_status', 'Aprobado')->where('status', 'Cancelado')->pluck('TOTAL')->sum();
                    $subItem['DATA']['MEDELLIN']['SUSPENDIDO_CARTERA'] = $subitem_medellin->whereNotIn('order.wallet_status', ['En mora'])->where('status', 'Suspendido')->pluck('TOTAL')->sum();
                    $subItem['DATA']['MEDELLIN']['EN_MORA_CARTERA'] = $subitem_medellin->where('order.wallet_status', 'En mora')->where('status', 'Suspendido')->pluck('TOTAL')->sum();
                    $subItem['DATA']['MEDELLIN']['PENDIENTE_DESPACHO'] = $subitem_medellin->where('status', 'Comprometido')->pluck('order_dispatch_detail')->pluck('TOTAL')->sum();
                    $subItem['DATA']['MEDELLIN']['DESPACHADO'] = $subitem_medellin->where('status', 'Despachado')->pluck('order_dispatch_detail')->pluck('TOTAL')->sum();
                    $subItem['DATA']['MEDELLIN']['TOTAL'] = $subitem_medellin->whereNotIn('status', ['Comprometido', 'Despachado'])->pluck('TOTAL')->sum() + $subitem_medellin->whereIn('status', ['Comprometido', 'Despachado'])->pluck('order_dispatch_detail')->pluck('TOTAL')->sum();

                    $subItem['DATA']['TOTAL'] = $subItem['DATA']['NACIONAL']['TOTAL'] + $subItem['DATA']['MEDELLIN']['TOTAL'];

                    $subItem['DATA']['CHART_PIE_DISTRIBUTION_NACIONAL'] = $this->chartDistributionNacional($trademark, $subItem['DATA']['NACIONAL']);
                    $subItem['DATA']['CHART_PIE_DISTRIBUTION_MEDELLIN'] = $this->chartDistributionMedellin($trademark, $subItem['DATA']['MEDELLIN']);
                }
                
                $labelsBar = json_encode(array_keys(collect($item['TRADEMARKS'])->where('DATA.TOTAL', '>', 0)->toArray()));

                $datasetBarNacional = json_encode(collect($item['TRADEMARKS'])->where('DATA.TOTAL', '>', 0)->pluck('DATA.NACIONAL.TOTAL')->toArray());
                $datasetBarMedellin = json_encode(collect($item['TRADEMARKS'])->where('DATA.TOTAL', '>', 0)->pluck('DATA.MEDELLIN.TOTAL')->toArray());

                $item['DATA']['CHART_BAR_DISTRIBUTION'] = $this->chartDistribution($trademark, $labelsBar, $datasetBarNacional, $datasetBarMedellin);
                $item['DATA']['CHART_PIE_DISTRIBUTION_NACIONAL'] = $this->chartDistributionNacional($trademark, $item['DATA']['NACIONAL']);
                $item['DATA']['CHART_PIE_DISTRIBUTION_MEDELLIN'] = $this->chartDistributionMedellin($trademark, $item['DATA']['MEDELLIN']);
            }
            
            // return view('Dashboard.Reports.PDFTrademarks', compact('trademarks'));
            $pdf = PDF::loadView('Dashboard.Reports.PDFTrademarks', compact('trademarks'))->setOptions(['isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true]);
            
            return $pdf->stream("REPORTE MARCAS.pdf");
        } catch (Exception $e) {
            return back()->with('danger', 'Ocurrió un error al cargar el reporte de pdf por marcas: ' . $e->getMessage());
        }
    }

    private function chartDistribution($trademark, $labelsBar, $datasetBarNacional = [], $datasetBarMedellin = [])
    {
        try {
            $chartBar = new QuickChart(array(
                'width' => 800,
                'height' => 350
            ));

            $chartBar->setConfig("{
                type: 'bar',
                data: {
                  labels: $labelsBar,
                  datasets: [{
                    label: 'NACIONAL',
                    data: $datasetBarNacional
                  },{
                    label: 'MEDELLIN',
                    data: $datasetBarMedellin
                  }]
                },
                options: {
                    legend: {
                        display: true,
                        position: 'top',
                        align: 'center',
                        fullWidth: true,
                        reverse: false,
                        labels: {
                            fontSize: 12,
                            fontFamily: 'sans-serif',
                            fontColor: '#000',
                            fontStyle: 'bold',
                            padding: 10,
                            usePointStyle: true
                        }
                    },
                    plugins: {
                        datalabels: {
                            display: true,
                            align: 'center',
                            anchor: 'center',
                            backgroundColor: '#eee',
                            borderColor: '#ddd',
                            borderRadius: 6,
                            borderWidth: 1,
                            padding: 4,
                            color: '#0d0d0d',
                            font: {
                                family: 'sans-serif',
                                size: 12,
                                style: 'bold'
                            }
                        }
                    },
                    title: {
                        display: true,
                        text: 'DISTRIBUCION GENERAL DE UNIDADES EN MARCAS $trademark',
                        fontSize: 16,
                        fontColor: '#000',
                        fontFamily: 'sans-serif',
                        fontStyle: 'bold',
                        padding: 20
                    },
                    scales: {
                        xAxes: [{
                            ticks: {
                                fontFamily: 'sans-serif',
                                fontColor: '#000',
                                fontStyle: 'bold',
                            }
                        }],
                        yAxes: [{
                            ticks: {
                                fontFamily: 'sans-serif',
                                fontColor: '#000',
                                fontStyle: 'bold',
                            }
                        }]
                    }
                }
            }");

            $string = Str::random(30);
            $path = "CHART_{$string}_ITEM.png";

            $imageData = file_get_contents($chartBar->getUrl());
            Storage::disk('public')->put('Charts/' . $path, $imageData);
             
            return "storage/Charts/$path";
        } catch (Exception $e) {
            return '';
        }
    }

    private function chartDistributionNacional($trademark, $array)
    {
        try {
            $chartPie = new QuickChart(array(
                'width' => 500,
                'height' => 500
            ));

            $labels = array();
            $datasets = array();

            foreach ($array as $key => $item) {
                if($key != 'TOTAL') {
                    $label = str_replace('_', ' ', $key);
                    $dataset = $item;

                    array_push($labels, $label);
                    array_push($datasets, $dataset);
                }
            }

            $labels = json_encode($labels);
            $datasets = json_encode($datasets);

            $chartPie->setConfig("{
                type: 'pie',
                data: {
                  labels: $labels,
                  datasets: [{
                    label: 'NACIONAL',
                    data: $datasets,
                    backgroundColor: [
                        '#4E79A7',
                        '#F28E2B',
                        '#E15759',
                        '#76B7B2',
                        '#59A14F',
                        '#EDC948',
                        '#B07AA1',
                        '#FF9DA7',
                        '#9C755F',
                        '#BAB0AC',
                    ]
                  }]
                },
                options: {
                    legend: {
                        display: true,
                        position: 'top',
                        align: 'center',
                        fullWidth: true,
                        reverse: false,
                        labels: {
                            fontSize: 14,
                            fontFamily: 'sans-serif',
                            fontColor: '#000',
                            fontStyle: 'bold',
                            padding: 10,
                            usePointStyle: true
                        }
                    },
                    plugins: {
                        datalabels: {
                            display: true,
                            align: 'center',
                            anchor: 'center',
                            backgroundColor: '#eee',
                            borderColor: '#ddd',
                            borderRadius: 6,
                            borderWidth: 1,
                            padding: 4,
                            color: '#0d0d0d',
                            font: {
                                family: 'sans-serif',
                                size: 12,
                                style: 'bold'
                            }
                        }
                    },
                    title: {
                        display: true,
                        text: 'DISTRIBUCION GENERAL DE UNIDADES NACIONAL | $trademark',
                        fontSize: 16,
                        fontColor: '#000',
                        fontFamily: 'sans-serif',
                        fontStyle: 'bold',
                        padding: 20
                    }
                }
            }");

            $string = Str::random(30);
            $path = "CHART_{$string}_ITEM.png";

            $imageData = file_get_contents($chartPie->getUrl());
            Storage::disk('public')->put('Charts/' . $path, $imageData);
             
            return "storage/Charts/$path";
        } catch (Exception $e) {
            return '';
        }
    }

    private function chartDistributionMedellin($trademark, $array)
    {
        try {
            $chartPie = new QuickChart(array(
                'width' => 500,
                'height' => 500
            ));

            $labels = array();
            $datasets = array();

            foreach ($array as $key => $item) {
                if($key != 'TOTAL') {
                    $label = str_replace('_', ' ', $key);
                    $dataset = $item;

                    array_push($labels, $label);
                    array_push($datasets, $dataset);
                }
            }

            $labels = json_encode($labels);
            $datasets = json_encode($datasets);

            $chartPie->setConfig("{
                type: 'pie',
                data: {
                  labels: $labels,
                  datasets: [{
                    label: 'MEDELLIN',
                    data: $datasets,
                    backgroundColor: [
                        '#4E79A7',
                        '#F28E2B',
                        '#E15759',
                        '#76B7B2',
                        '#59A14F',
                        '#EDC948',
                        '#B07AA1',
                        '#FF9DA7',
                        '#9C755F',
                        '#BAB0AC',
                    ]
                  }]
                },
                options: {
                    legend: {
                        display: true,
                        position: 'top',
                        align: 'center',
                        fullWidth: true,
                        reverse: false,
                        labels: {
                            fontSize: 14,
                            fontFamily: 'sans-serif',
                            fontColor: '#000',
                            fontStyle: 'bold',
                            padding: 10,
                            usePointStyle: true
                        }
                    },
                    plugins: {
                        datalabels: {
                            display: true,
                            align: 'center',
                            anchor: 'center',
                            backgroundColor: '#eee',
                            borderColor: '#ddd',
                            borderRadius: 6,
                            borderWidth: 1,
                            padding: 4,
                            color: '#0d0d0d',
                            font: {
                                family: 'sans-serif',
                                size: 12,
                                style: 'bold'
                            }
                        }
                    },
                    title: {
                        display: true,
                        text: 'DISTRIBUCION GENERAL DE UNIDADES MEDELLIN | $trademark',
                        fontSize: 16,
                        fontColor: '#000',
                        fontFamily: 'sans-serif',
                        fontStyle: 'bold',
                        padding: 20
                    }
                }
            }");

            $string = Str::random(30);
            $path = "CHART_{$string}_ITEM.png";

            $imageData = file_get_contents($chartPie->getUrl());
            Storage::disk('public')->put('Charts/' . $path, $imageData);
             
            return "storage/Charts/$path";
        } catch (Exception $e) {
            return '';
        }
    }
}
