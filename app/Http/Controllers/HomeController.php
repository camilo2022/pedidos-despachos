<?php

namespace App\Http\Controllers;

use App\Http\Requests\Dashboard\DashboardChartCorreriaRequest;
use App\Models\Client;
use App\Models\Correria;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Product;
use App\Models\User;
use App\Traits\ApiMessage;
use App\Traits\ApiResponser;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use QuickChart;

class HomeController extends Controller
{
    use ApiMessage;
    use ApiResponser;

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $user_id = Auth::user()->id;

        $clients = Client::count();
        $users = User::count();
        $orders = Order::count();
        $products = Product::count();

        $correrias = Correria::withTrashed()->where(function ($query) {
            $query->where('start_date', 'LIKE', '%' . Carbon::now()->format('Y') .'%')
            ->orWhere('end_date', 'LIKE', '%' . Carbon::now()->format('Y') .'%');
        })->where('business_id', Auth::user()->business_id)->orderBy('id', 'DESC')->get();

        $sellers = Order::with([
            'correria', 'seller_user' => fn ($query) => $query->withTrashed()
        ])
            ->whereHas('correria', fn ($query) => $query->whereNull('deleted_at'))
            ->get()->pluck('seller_user')->unique()->values()->map(function ($item) {
                return (object) [
                    'id' => $item->id,
                    'name' => strtoupper("{$item->name} {$item->last_name}")
                ];
            });

        $details = OrderDetail::select(
            'order_details.*',
            DB::raw("(T04 + T06 + T08 + T10 + T12 + T14 + T16 + T18 + T20 + T22 + T24 + T26 + T28 + T30 + T32 + T34 + T36 + T38 + TXXS + TXS + TS + TM + TL + TXL + TXXL) AS TOTAL")
        )
        ->with([
            'order.seller_user', 'product', 'order.correria' => fn ($query) => $query->withTrashed(),
            'order_dispatch_detail' => fn ($query) => $query->select('order_dispatch_details.*', DB::raw("(T04 + T06 + T08 + T10 + T12 + T14 + T16 + T18 + T20 + T22 + T24 + T26 + T28 + T30 + T32 + T34 + T36 + T38 + TXXS + TXS + TS + TM + TL + TXL + TXXL) AS TOTAL")),
        ])
        ->whereHas('order', fn ($query) => $query->where('business_id', Auth::user()->business_id))
        ->whereHas('order.correria', fn ($query) => $query->whereNull('deleted_at'))
        ->get();

        $chartSellers = $this->chartSellers($sellers, $details);

        $chartTrademarks = $this->chartTrademarks($details);

        $chartStatus = $this->chartStatus($details);

        $chartCorreria = "storage/Charts/CHART_BAR_CORRERIA_{$user_id}.png";

        return view('Dashboard.home', compact('clients', 'users', 'orders', 'products', 'details', 'chartSellers', 'chartTrademarks', 'chartStatus', 'chartCorreria', 'sellers', 'correrias'));
    }

    private function chartSellers($sellers, $items)
    {
        try {
            $cancelados = array();
            $suspendidos = array();
            $pedidos = array();
            $comprometidos = array();
            $despachados = array();

            foreach ($sellers as $seller) {
                array_push($cancelados, $items->where('order.seller_user_id', $seller->id)->where('status', 'Cancelado')->sum('TOTAL'));
                array_push($suspendidos, $items->where('order.seller_user_id', $seller->id)->where('status', 'Suspendido')->sum('TOTAL'));
                array_push($pedidos, $items->where('order.seller_user_id', $seller->id)->whereIn('status', ['Pendiente', 'Aprobado', 'Autorizado'])->sum('TOTAL'));
                array_push($comprometidos, $items->where('order.seller_user_id', $seller->id)->where('status', 'Comprometido')->pluck('order_dispatch_detail')->sum('TOTAL'));
                array_push($despachados, $items->where('order.seller_user_id', $seller->id)->where('status', 'Despachado')->pluck('order_dispatch_detail')->sum('TOTAL'));
            }

            $sellers = json_encode($sellers->pluck('name')->toArray());

            $cancelados = json_encode($cancelados);
            $suspendidos = json_encode($suspendidos);
            $pedidos = json_encode($pedidos);
            $comprometidos = json_encode($comprometidos);
            $despachados = json_encode($despachados);

            $chartSellers = new QuickChart(array(
                'width' => 760,
                'height' => 300
            ));

            $chartSellers->setConfig("{
                type: 'bar',
                data: {
                    labels: $sellers,
                    datasets: [{
                        label: 'CANCELADO',
                        data: $cancelados,
                        backgroundColor: '#E15759'
                    },{
                        label: 'SUSPENDIDO',
                        data: $suspendidos,
                        backgroundColor: '#F28E2B'
                    },{
                        label: 'PEDIDO',
                        data: $pedidos,
                        backgroundColor: '#59A14F'
                    },{
                        label: 'POR DESPACHO',
                        data: $comprometidos,
                        backgroundColor: '#76B7B2'
                    },{
                        label: 'DESPACHADO',
                        data: $despachados,
                        backgroundColor: '#4E79A7'
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
                                size: 8,
                                style: 'bold'
                            }
                        }
                    },
                    title: {
                        display: true,
                        text: 'DISTRIBUCION DE UNIDADES EN VENDEDORES',
                        fontSize: 16,
                        fontColor: '#000',
                        fontFamily: 'sans-serif',
                        fontStyle: 'bold',
                        padding: 20
                    }
                }
            }");

            $user_id = Auth::user()->id;

            $path = "CHART_BAR_SELLERS_{$user_id}.png";

            $imageData = file_get_contents($chartSellers->getUrl());
            Storage::disk('public')->put('Charts/' . $path, $imageData);

            return "storage/Charts/$path";
        } catch (Exception $e) {
            return '';
        }
    }

    private function chartTrademarks($items)
    {
        try {
            $trademarks = $items->whereIn('status', ['Pendiente', 'Aprobado', 'Autorizado', 'Comprometido', 'Despachado'])->pluck('product.trademark')->unique()->values()->toArray();
            $quantites = array();

            foreach ($trademarks as $trademark) {
                array_push($quantites, $items->where('product.trademark', $trademark)->whereIn('status', ['Pendiente', 'Aprobado', 'Autorizado'])->sum('TOTAL') + $items->where('product.trademark', $trademark)->whereIn('status', ['Comprometido', 'Despachado'])->pluck('order_dispatch_detail')->sum('TOTAL'));
            }

            $trademarks = json_encode($trademarks);
            $quantites = json_encode($quantites);

            $chartTrademarks = new QuickChart(array(
                'width' => 500,
                'height' => 500
            ));

            $chartTrademarks->setConfig("{
                type: 'pie',
                data: {
                labels: $trademarks,
                datasets: [{
                    label: 'MEDELLIN',
                    data: $quantites
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
                        text: 'DISTRIBUCION DE UNIDADES EN MARCAS',
                        fontSize: 16,
                        fontColor: '#000',
                        fontFamily: 'sans-serif',
                        fontStyle: 'bold',
                        padding: 20
                    }
                }
            }");

            $user_id = Auth::user()->id;

            $path = "CHART_PIE_TRADEMARKS_{$user_id}.png";

            $imageData = file_get_contents($chartTrademarks->getUrl());
            Storage::disk('public')->put('Charts/' . $path, $imageData);

            return "storage/Charts/$path";
        } catch (Exception $e) {
            return '';
        }
    }

    private function chartStatus($items)
    {
        try {
            $status = array('AGOTADO', 'PENDIENTE VENDEDOR', 'CANCELADO VENDEDOR', 'PENDIENTE CARTERA', 'APROBADO CARTERA', 'RECHAZADO CARTERA', 'SUSPENDIDO CARTERA', 'MORA CARTERA', 'PENDIENTE DESPACHO', 'DESPACHADO');
            $array = array();

            array_push($array, $items->where('status', 'Agotado')->sum('TOTAL'));
            array_push($array, $items->where('order.seller_status', 'Pendiente')->where('status', 'Pendiente')->sum('TOTAL'));
            array_push($array, $items->where('order.seller_status', 'Cancelado')->where('status', 'Cancelado')->sum('TOTAL'));
            array_push($array, $items->where('order.seller_status', 'Aprobado')->where('order.wallet_status', 'Pendiente')->where('status', 'Pendiente')->sum('TOTAL'));
            array_push($array, $items->whereIn('order.wallet_status', ['Parcialmente Aprobado', 'Aprobado', 'Autorizado'])->whereIn('status', ['Autorizado', 'Aprobado'])->sum('TOTAL'));
            array_push($array, $items->where('order.seller_status', 'Aprobado')->where('status', 'Cancelado')->sum('TOTAL'));
            array_push($array, $items->whereNotIn('order.wallet_status', ['En mora'])->where('status', 'Suspendido')->sum('TOTAL'));
            array_push($array, $items->where('order.wallet_status', 'En mora')->where('status', 'Suspendido')->sum('TOTAL'));
            array_push($array, $items->where('status', 'Comprometido')->pluck('order_dispatch_detail')->sum('TOTAL'));
            array_push($array, $items->where('status', 'Despachado')->pluck('order_dispatch_detail')->sum('TOTAL'));

            $chartStatus = new QuickChart(array(
                'width' => 500,
                'height' => 500
            ));

            $status = json_encode($status);
            $array = json_encode($array);

            $chartStatus->setConfig("{
                type: 'pie',
                data: {
                labels: $status,
                datasets: [{
                    label: 'MEDELLIN',
                    data: $array,
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
                        text: 'DISTRIBUCION DE UNIDADES EN ESTADOS',
                        fontSize: 16,
                        fontColor: '#000',
                        fontFamily: 'sans-serif',
                        fontStyle: 'bold',
                        padding: 20
                    }
                }
            }");

            $user_id = Auth::user()->id;

            $path = "CHART_PIE_STATUS_{$user_id}.png";

            $imageData = file_get_contents($chartStatus->getUrl());
            Storage::disk('public')->put('Charts/' . $path, $imageData);

            return "storage/Charts/$path";
        } catch (Exception $e) {
            return '';
        }
    }

    public function chartCorreria(DashboardChartCorreriaRequest $request)
    {
        try {
            $user_id = Auth::user()->id;

            $correria_id = Order::max('correria_id');

            $details = OrderDetail::select(
                'order_details.*',
                DB::raw("(T04 + T06 + T08 + T10 + T12 + T14 + T16 + T18 + T20 + T22 + T24 + T26 + T28 + T30 + T32 + T34 + T36 + T38 + TXXS + TXS + TS + TM + TL + TXL + TXXL) AS TOTAL")
            )
            ->with([
                'order', 'order_dispatch_detail' => fn ($query) => $query->select('order_dispatch_details.*', DB::raw("(T04 + T06 + T08 + T10 + T12 + T14 + T16 + T18 + T20 + T22 + T24 + T26 + T28 + T30 + T32 + T34 + T36 + T38 + TXXS + TXS + TS + TM + TL + TXL + TXXL) AS TOTAL")),
            ])
            ->whereHas('order', fn ($query) => $query->where('correria_id', $request->filled('correria_id') ? $request->input('correria_id') : $correria_id))
            ->get();
            
            $correria = Correria::withTrashed()->findOrFail($request->filled('correria_id') ? $request->input('correria_id') : $correria_id);
            $correria->start_date = Carbon::parse($correria->start_date)->format('Y-m-d');
            $correria->end_date = Carbon::parse($correria->end_date)->format('Y-m-d');
            
            $groupedDates = [];
            foreach ($details as $detail) {
                Carbon::setLocale('es');
                $date = Carbon::parse($detail->created_at);
                
                $year = $date->year;
                $monthName = ucfirst($date->translatedFormat('M'));
                $weekOfMonth = intval(ceil($date->day / 7));
                $weekOfMonthPadded = sprintf('%02d', $weekOfMonth);
            
                $key = "{$year} {$monthName} Sem {$weekOfMonthPadded}";
            
                if (!isset($groupedDates[$key])) {
                    $groupedDates[$key] = [
                        'CANCELADO' => 0,
                        'SUSPENDIDO' => 0,
                        'PEDIDO' => 0,
                        'COMPROMETIDO' => 0,
                        'DESPACHADO' => 0
                    ];
                }
                if(in_array($detail->status, ['Cancelado'])){
                    $groupedDates[$key]['CANCELADO'] += $detail->TOTAL;
                }
                if(in_array($detail->status, ['Suspendido'])){
                    $groupedDates[$key]['SUSPENDIDO'] += $detail->TOTAL;
                }
                if(in_array($detail->status, ['Pendiente', 'Aprobado', 'Autorizado'])){
                    $groupedDates[$key]['PEDIDO'] += $detail->TOTAL;
                }
                if(in_array($detail->status, ['Comprometido'])){
                    $groupedDates[$key]['COMPROMETIDO'] += $detail->order_dispatch_detail->TOTAL;
                }
                if(in_array($detail->status, ['Despachado'])){
                    $groupedDates[$key]['DESPACHADO'] += $detail->order_dispatch_detail->TOTAL;
                }
            }

            $labels = json_encode(array_keys($groupedDates));
            $cancelados = json_encode(collect($groupedDates)->pluck('CANCELADO')->toArray());
            $suspendidos = json_encode(collect($groupedDates)->pluck('SUSPENDIDO')->toArray());
            $pedidos = json_encode(collect($groupedDates)->pluck('PEDIDO')->toArray());
            $comprometidos = json_encode(collect($groupedDates)->pluck('COMPROMETIDO')->toArray());
            $despachados = json_encode(collect($groupedDates)->pluck('DESPACHADO')->toArray());
            $chartLine = new QuickChart(array(
                'width' => 1560,
                'height' => 300
            ));

            $chartLine->setConfig("{
                type: 'line',
                data: {
                    labels: $labels,
                    datasets: [{
                            label: 'CANCELADO',
                            data: $cancelados,
                            backgroundColor: '#E15759',
                            borderColor: '#E15759',
                            fill: false,
                            lineTension: 0.4
                        },{
                            label: 'SUSPENDIDO',
                            data: $suspendidos,
                            backgroundColor: '#F28E2B',
                            borderColor: '#F28E2B',
                            fill: false,
                            lineTension: 0.4
                        },{
                            label: 'PEDIDO',
                            data: $pedidos,
                            backgroundColor: '#59A14F',
                            borderColor: '#59A14F',
                            fill: false,
                            lineTension: 0.4
                        },{
                            label: 'POR DESPACHO',
                            data: $comprometidos,
                            backgroundColor: '#76B7B2',
                            borderColor: '#76B7B2',
                            fill: false,
                            lineTension: 0.4
                        },{
                            label: 'DESPACHADO',
                            data: $despachados,
                            backgroundColor: '#4E79A7',
                            borderColor: '#4E79A7',
                            fill: false,
                            lineTension: 0.4
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
                                size: 8,
                                style: 'bold'
                            }
                        }
                    },
                    title: {
                        display: true,
                        text: 'DISTRIBUCION DE UNIDADES EN ESTADOS | {$correria->name} - DE: {$correria->start_date} HASTA: {$correria->end_date}',
                        fontSize: 16,
                        fontColor: '#000',
                        fontFamily: 'sans-serif',
                        fontStyle: 'bold',
                        padding: 20
                    }
                }
            }");

            $path = "CHART_LINE_CORRERIA_{$user_id}.png";
            $imageData = file_get_contents($chartLine->getUrl());
            Storage::disk('public')->put('Charts/' . $path, $imageData);

            return $this->successResponse(
                [
                    'chart' => asset("storage/Charts/$path")
                ],
                'Grafica generada exitosamente.',
                200
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

    public function chartSeller()
    {
    }
}
