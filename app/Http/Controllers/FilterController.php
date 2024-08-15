<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\Filter\FilterQueryRequest;
use App\Http\Requests\Filter\FilterSaveRequest;
use App\Http\Requests\Filter\FilterUploadRequest;
use App\Imports\Filter\FilterImport;
use App\Models\Color;
use App\Models\Inventory;
use App\Models\OrderDetail;
use App\Models\OrderDispatch;
use App\Models\OrderDispatchDetail;
use App\Models\Product;
use App\Models\Size;
use App\Models\Warehouse;
use App\Traits\ApiMessage;
use App\Traits\ApiResponser;
use Carbon\Carbon;
use Exception;
use GuzzleHttp\Client as GuzzleHttpClient;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use QuickChart;

class FilterController extends Controller
{
    use ApiResponser;
    use ApiMessage;

    public function index()
    {
        try {
            return view('Dashboard.Filters.Index');
        } catch (Exception $e) {
            return back()->with('danger', 'Ocurrió un error al cargar la vista: ' . $e->getMessage());
        }
    }

    public function indexQuery()
    {
        try {
            $warehouses = Warehouse::with('businesses')->whereHas('businesses', fn($query) => $query->where('businesses.id', Auth::user()->business_id))->where('to_discount', true)->get();

            $items = OrderDetail::with('order', 'product', 'color')
                ->whereHas('order',
                    function ($query) {
                        $query->whereIn('seller_status', ['Aprobado'])
                        ->whereIn('wallet_status', ['Parcialmente Aprobado', 'Aprobado'])
                        ->where('business_id', Auth::user()->business_id);
                    }
                )
                ->whereIn('status', ['Aprobado'])
                ->groupBy('product_id', 'color_id')
                ->select('order_details.product_id', 'order_details.color_id')
                ->get();

            $products = collect([]);

            foreach($items as $item) {
                $inventory = Inventory::where('product_id', $item->product->id)->where('color_id', $item->color->id)->whereIn('warehouse_id', $warehouses->pluck('id')->toArray())->get();

                $object = (object) [
                    'product' => $item->product,
                    'color' => $item->color,
                    'inventory' => $inventory->pluck('quantity')->sum()
                ];

                $products = $products->push($object);
            }
        
            return $this->successResponse(
                [
                    'products' => $products->sortByDesc('inventory')->values()
                ],
                'Referencias consultadas de los pedidos aprobados y pendientes por filtrar exitosamente.',
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

    public function query(FilterQueryRequest $request)
    {
        try {
            $product = Product::findOrFail($request->input('product_id'));
            $color = Color::findOrFail($request->input('color_id'));
            $sizes = Size::all();
            $warehouses = Warehouse::with('businesses')->whereHas('businesses', fn($query) => $query->where('businesses.id', Auth::user()->business_id))->get();

            $inventorySiesa = $this->inventorySiesa($product, $color, $sizes, $warehouses);
            $inventoryTns = $this->inventoryTns($product, $color, $sizes, $warehouses);
            $inventoryBmi = $this->inventoryBmi($product, $color, $sizes, $warehouses);
            $cutted = $this->cutted($product, $color, $sizes, $warehouses);
            $committed = $this->committed($product, $color, $sizes);
            $requested = $this->requested($product, $color, $sizes);
            $availabled = $this->availabled($product, $color, collect([$inventorySiesa->finished, $inventoryTns->finished, $inventoryBmi->finished]), $committed, $sizes);
            $filtered = $this->filtered();

            $processed = (object) [
                'product' => $product->code,
                'color' => $color->code
            ];

            $finished = (object) [
                'product' => $product->code,
                'color' => $color->code
            ];

            $processedTotal = 0;
            $finishedTotal = 0;

            foreach ($sizes as $size) {
                $processed->{"T{$size->code}"} = $inventorySiesa->processed->{"T{$size->code}"} + $inventoryTns->processed->{"T{$size->code}"} + $inventoryBmi->processed->{"T{$size->code}"};
                $finished->{"T{$size->code}"} = $inventorySiesa->finished->{"T{$size->code}"} + $inventoryTns->finished->{"T{$size->code}"} + $inventoryBmi->finished->{"T{$size->code}"};
                $processedTotal += $processed->{"T{$size->code}"};
                $finishedTotal += $finished->{"T{$size->code}"};
            }

            $processed->TOTAL = $processedTotal;
            $finished->TOTAL = $finishedTotal;

            return $this->successResponse(
                [
                    'sizes' => $sizes,
                    'processed' => $processed,
                    'finished' => $finished,
                    'cutted' => $cutted,
                    'committed' => $committed,
                    'availabled' => $availabled,
                    'requested' => $requested,
                    'filtered' => $filtered,
                    'siesaAlert' => $inventorySiesa->alert,
                    'tnsAlert' => $inventoryTns->alert,
                    'bmiAlert' => $inventoryBmi->alert
                ],
                'Pedidos, Inventarios y unidades comprometidas consultadas exitosamente.',
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

    public function save(FilterSaveRequest $request)
    {
        try {
            $product = Product::findOrFail($request->input('product_id'));
            $color = Color::findOrFail($request->input('color_id'));
            foreach($request->input('order_details') as $detail) {
                $detail = (object) $detail;
                $orderDetail = OrderDetail::with('order.client', 'order.seller_user', 'order.correria')->findOrFail($detail->order_detail_id);
                $orderDetail->status = 'Comprometido';
                $orderDetail->save();
                
                DB::statement('CALL order_dispatch_status(?)', [$orderDetail->order->id]);

                $orderDispatch = OrderDispatch::where('client_id', $orderDetail->order->client_id)->where('business_id', $orderDetail->order->seller_user->business_id)->where('correria_id', $orderDetail->order->correria_id)->where('dispatch_status', 'Pendiente')->first();
                if(!$orderDispatch) {
                    $orderDispatch = new OrderDispatch();
                    $orderDispatch->client_id = $orderDetail->order->client_id;
                    $orderDispatch->consecutive = DB::selectOne('CALL order_dispatches()')->consecutive;
                    $orderDispatch->dispatch_user_id = Auth::user()->id;
                    $orderDispatch->correria_id = $orderDetail->order->correria_id;
                    $orderDispatch->business_id = $orderDetail->order->seller_user->business_id;
                    $orderDispatch->save();
                }

                $orderDispatchDetail = OrderDispatchDetail::where('order_id', $orderDetail->order_id)->where('order_detail_id', $orderDetail->id)->whereIn('status', ['Pendiente', 'Cancelado'])->first();
                $orderDispatchDetail = $orderDispatchDetail ? $orderDispatchDetail : new OrderDispatchDetail();
                $orderDispatchDetail->order_dispatch_id = $orderDispatch->id;
                $orderDispatchDetail->order_id = $orderDetail->order_id;
                $orderDispatchDetail->order_detail_id = $orderDetail->id;
                $orderDispatchDetail->T04 = $detail->T04;
                $orderDispatchDetail->T06 = $detail->T06;
                $orderDispatchDetail->T08 = $detail->T08;
                $orderDispatchDetail->T10 = $detail->T10;
                $orderDispatchDetail->T12 = $detail->T12;
                $orderDispatchDetail->T14 = $detail->T14;
                $orderDispatchDetail->T16 = $detail->T16;
                $orderDispatchDetail->T18 = $detail->T18;
                $orderDispatchDetail->T20 = $detail->T20;
                $orderDispatchDetail->T22 = $detail->T22;
                $orderDispatchDetail->T24 = $detail->T24;
                $orderDispatchDetail->T26 = $detail->T26;
                $orderDispatchDetail->T28 = $detail->T28;
                $orderDispatchDetail->T30 = $detail->T30;
                $orderDispatchDetail->T32 = $detail->T32;
                $orderDispatchDetail->T34 = $detail->T34;
                $orderDispatchDetail->T36 = $detail->T36;
                $orderDispatchDetail->T38 = $detail->T38;
                $orderDispatchDetail->TXXS = $detail->TXXS;
                $orderDispatchDetail->TXS = $detail->TXS;
                $orderDispatchDetail->TS = $detail->TS;
                $orderDispatchDetail->TM = $detail->TM;
                $orderDispatchDetail->TL = $detail->TL;
                $orderDispatchDetail->TXL = $detail->TXL;
                $orderDispatchDetail->TXXL = $detail->TXXL;
                $orderDispatchDetail->user_id = Auth::user()->id;
                $orderDispatchDetail->status = 'Pendiente';
                $orderDispatchDetail->date = Carbon::now()->format('Y-m-d H:i:s');
                $orderDispatchDetail->save();
            }

            return $this->successResponse(
                [
                    'product' => $product,
                    'color' => $color
                ],
                "Los pedidos de la referencia {$product->code} y color {$color->name} - {$color->code} fueron filtrados y guardados exitosamente.",
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

    private function inventorySiesa($product, $color, $sizes, $warehouses)
    {
        try {
            $alert = 'alert-success';

            $user = env('API_SIESA_USER');
            $password = env('API_SIESA_PASSWORD');

            $guzzleHttpClient = new GuzzleHttpClient(['base_uri' => 'http://45.76.251.153']);

            $auth = $guzzleHttpClient->request('POST', '/API_GT/api/login/authenticate', [
                'form_params' => [
                    'Username' => $user,
                    'Password' => $password,
                ]
            ]);
            
            $token = str_replace('"', '', $auth->getBody()->getContents());

            $query = $guzzleHttpClient->request('GET', "http://45.76.251.153/API_GT/api/orgBless/getInvPorBodega?Referencia={$product->code}&CentroOperacion=001&Extension2={$color->code}", [
                'headers' => [ 'Authorization' => "Bearer {$token}"],
            ]);

            $items = json_decode($query->getBody()->getContents());

            $items = empty($items->detail) ? collect([]) : collect($items->detail);

            $items = $items->map(function ($item) {
                return $this->transformDataSiesa($item);
            });

            $processedProduct = $items->whereIn('BODEGA', $warehouses->where('to_transit', true)->pluck('code')->toArray());

            $finishedProduct = $items->whereIn('BODEGA', $warehouses->where('to_discount', true)->pluck('code')->toArray());

            $processed = (object) [
                'product' => $product->code,
                'color' => $product->code
            ];

            $finished = (object) [
                'product' => $product->code,
                'color' => $product->code
            ];

            $processedTotal = 0;
            $finishedTotal = 0;

            foreach ($sizes as $size) {
                $processed->{"T{$size->code}"} = $processedProduct->where('TALLA', $size->code)->pluck('DISPONIBLE')->sum();
                $finished->{"T{$size->code}"} = $finishedProduct->where('TALLA', $size->code)->pluck('DISPONIBLE')->sum();
                $processedTotal += $processed->{"T{$size->code}"};
                $finishedTotal += $finished->{"T{$size->code}"};
            }

            $processed->TOTAL = $processedTotal;
            $finished->TOTAL = $finishedTotal;

            return (object) [
                'processed' => $processed,
                'finished' => $finished,
                'alert' => $alert
            ];
        } catch (Exception $e) {
            $alert = 'alert-warning';

            $items = Inventory::select('products.code AS product', 'colors.name AS color', 'inventories.warehouse_id AS warehouse');
            foreach ($sizes as $size) {
                $items->addSelect(DB::raw("COALESCE(SUM(CASE WHEN sizes.code = '$size->code' THEN inventories.quantity ELSE 0 END), 0) AS T$size->code"));
            }
            $items->join('warehouses', 'warehouses.id', 'inventories.warehouse_id')
            ->join('products', 'products.id', 'inventories.product_id')
            ->join('colors', 'colors.id', 'inventories.color_id')
            ->join('sizes', 'sizes.id', 'inventories.size_id')
            ->where('quantity', '>', 0)
            ->whereIn('warehouse_id', $warehouses->where('to_transit', true)->pluck('id')->merge($warehouses->where('to_discount', true)->pluck('id'))->toArray())
            ->where('product_id', $product->id)
            ->where('color_id', $color->id)
            ->where('system', 'SIESA')
            ->groupBy('products.code', 'colors.name', 'inventories.warehouse_id');
            
            $items = $items->get();

            $processedProduct = $items->whereIn('warehouse', $warehouses->where('to_transit', true)->pluck('id')->toArray());

            $finishedProduct = $items->whereIn('warehouse', $warehouses->where('to_discount', true)->pluck('id')->toArray());

            $processed = (object) [
                'product' => $product->code,
                'color' => $color->code
            ];

            $finished = (object) [
                'product' => $product->code,
                'color' => $color->code
            ];

            $processedTotal = 0;
            $finishedTotal = 0;

            foreach ($sizes as $size) {
                $processed->{"T{$size->code}"} = $processedProduct->pluck("T{$size->code}")->sum();
                $finished->{"T{$size->code}"} = $finishedProduct->pluck("T{$size->code}")->sum();
                $processedTotal += $processed->{"T{$size->code}"};
                $finishedTotal += $finished->{"T{$size->code}"};
            }

            $processed->TOTAL = $processedTotal;
            $finished->TOTAL = $finishedTotal;

            $alert = $processedProduct->isEmpty() && $finishedProduct->isEmpty() ? 'alert-danger' : 'alert-warning';

            return (object) [
                'processed' => $processed,
                'finished' => $finished,
                'alert' => $alert
            ];
        }
    }

    private function inventoryTns($product, $color, $sizes, $warehouses)
    {
        try {
            $alert = 'alert-success';

            $items = DB::connection('firebird')
            ->table('MATERIAL as M')
            ->select(
                'B.CODIGO AS CODBOD', 'B.NOMBRE AS NOMBOD', 'M.DESCRIP AS DESCRIPCION', 'GM.DESCRIP AS CATEGORIA', 'SM.EXISTENC AS EXISTENCIA',
                DB::raw("CHAR_LENGTH(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(M.CODIGO, 'PPD1-', ''), 'PPD2-', ''), 'PPCF-', ''), 'PPCT-', ''), 'PPCNI-', ''), 'PPCOR-', ''), 'PPLV-', ''), 'LVEXT-', ''), 'PPCNE-', ''), 'PPTER-', ''), 'PTN-', ''), 'TER-', ''), 'PDDIS-', ''), 'DIS-', '')) - CHAR_LENGTH(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(M.CODIGO, 'PPD1-', ''), 'PPD2-', ''), 'PPCF-', ''), 'PPCT-', ''), 'PPCNI-', ''), 'PPCOR-', ''), 'PPLV-', ''), 'LVEXT-', ''), 'PPCNE-', ''), 'PPTER-', ''), 'PTN-', ''), 'TER-', ''), 'PDDIS-', ''), 'DIS-', ''), '-', '')) AS CONTAR_GUION"),
                DB::raw("REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(M.CODIGO, 'PPD1-', ''), 'PPD2-', ''), 'PPCF-', ''), 'PPCT-', ''), 'PPCNI-', ''), 'PPCOR-', ''), 'PPLV-', ''), 'LVEXT-', ''), 'PPCNE-', ''), 'PPTER-', ''), 'PTN-', ''), 'TER-', ''), 'PDDIS-', ''), 'DIS-', '') AS CODIGO")
            )
            ->join('SALMATERIAL as SM', 'SM.MATID', 'M.MATID')
            ->join('BODEGA as B', 'B.BODID', 'SM.BODID')
            ->join('GRUPMAT as GM', 'GM.GRUPMATID', 'M.GRUPMATID')
            ->whereIn('B.CODIGO', $warehouses->where('to_transit', true)->pluck('code')->merge($warehouses->where('to_discount', true)->pluck('code'))->toArray())
            ->where('M.CODIGO', 'LIKE', "%{$product->code}%")
            ->whereRaw("CHAR_LENGTH(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(M.CODIGO, 'PPD1-', ''), 'PPD2-', ''), 'PPCF-', ''), 'PPCT-', ''), 'PPCNI-', ''), 'PPCOR-', ''), 'PPLV-', ''), 'LVEXT-', ''), 'PPCNE-', ''), 'PPTER-', ''), 'PTN-', ''), 'TER-', ''), 'PDDIS-', ''), 'DIS-', '')) - CHAR_LENGTH(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(M.CODIGO, 'PPD1-', ''), 'PPD2-', ''), 'PPCF-', ''), 'PPCT-', ''), 'PPCNI-', ''), 'PPCOR-', ''), 'PPLV-', ''), 'LVEXT-', ''), 'PPCNE-', ''), 'PPTER-', ''), 'PTN-', ''), 'TER-', ''), 'PDDIS-', ''), 'DIS-', ''), '-', '')) >= 2")
            ->get()->map(function ($item) {
                return $this->transformDataTns($item);
            });

            $processedProduct = $items->where('REFERENCIA', $product->code)->where('COLOR', $color->code)->whereIn('CODBOD', $warehouses->where('to_transit', true)->pluck('code')->toArray());

            $finishedProduct = $items->where('REFERENCIA', $product->code)->where('COLOR', $color->code)->whereIn('CODBOD', $warehouses->where('to_discount', true)->pluck('code')->toArray());

            $processed = (object) [
                'product' => $product->code,
                'color' => $color->code
            ];

            $finished = (object) [
                'product' => $product->code,
                'color' => $color->code
            ];

            $processedTotal = 0;
            $finishedTotal = 0;

            foreach ($sizes as $size) {
                $processed->{"T{$size->code}"} = $processedProduct->where('TALLA', $size->code)->pluck('EXISTENCIA')->sum();
                $finished->{"T{$size->code}"} = $finishedProduct->where('TALLA', $size->code)->pluck('EXISTENCIA')->sum();
                $processedTotal += $processed->{"T{$size->code}"};
                $finishedTotal += $finished->{"T{$size->code}"};
            }

            $processed->TOTAL = $processedTotal;
            $finished->TOTAL = $finishedTotal;

            return (object) [
                'processed' => $processed,
                'finished' => $finished,
                'alert' => $alert
            ];
        } catch (Exception $e) {
            $alert = 'alert-warning';

            $items = Inventory::select('products.code AS product', 'colors.name AS color', 'inventories.warehouse_id AS warehouse');
            foreach ($sizes as $size) {
                $items->addSelect(DB::raw("COALESCE(SUM(CASE WHEN sizes.code = '$size->code' THEN inventories.quantity ELSE 0 END), 0) AS T$size->code"));
            }
            $items->join('warehouses', 'warehouses.id', 'inventories.warehouse_id')
            ->join('products', 'products.id', 'inventories.product_id')
            ->join('colors', 'colors.id', 'inventories.color_id')
            ->join('sizes', 'sizes.id', 'inventories.size_id')
            ->where('quantity', '>', 0)
            ->whereIn('warehouse_id', $warehouses->where('to_transit', true)->pluck('id')->merge($warehouses->where('to_discount', true)->pluck('id'))->toArray())
            ->where('product_id', $product->id)
            ->where('color_id', $color->id)
            ->where('system', 'VISUAL TNS')
            ->groupBy('products.code', 'colors.name', 'inventories.warehouse_id');
            
            $items = $items->get();

            $processedProduct = $items->whereIn('warehouse', $warehouses->where('to_transit', true)->pluck('id')->toArray());

            $finishedProduct = $items->whereIn('warehouse', $warehouses->where('to_discount', true)->pluck('id')->toArray());

            $processed = (object) [
                'product' => $product->code,
                'color' => $color->code
            ];

            $finished = (object) [
                'product' => $product->code,
                'color' => $color->code
            ];

            $processedTotal = 0;
            $finishedTotal = 0;

            foreach ($sizes as $size) {
                $processed->{"T{$size->code}"} = $processedProduct->pluck("T{$size->code}")->sum();
                $finished->{"T{$size->code}"} = $finishedProduct->pluck("T{$size->code}")->sum();
                $processedTotal += $processed->{"T{$size->code}"};
                $finishedTotal += $finished->{"T{$size->code}"};
            }

            $processed->TOTAL = $processedTotal;
            $finished->TOTAL = $finishedTotal;

            $alert = $processedProduct->isEmpty() && $finishedProduct->isEmpty() ? 'alert-danger' : 'alert-warning';

            return (object) [
                'processed' => $processed,
                'finished' => $finished,
                'alert' => $alert
            ];
        }
    }

    private function inventoryBmi($product, $color, $sizes, $warehouses)
    {
        try {
            $alert = 'alert-success';

            $items = Inventory::select('products.code AS product', 'colors.name AS color', 'inventories.warehouse_id AS warehouse');
            foreach ($sizes as $size) {
                $items->addSelect(DB::raw("COALESCE(SUM(CASE WHEN sizes.code = '$size->code' THEN inventories.quantity ELSE 0 END), 0) AS T$size->code"));
            }
            $items->join('warehouses', 'warehouses.id', 'inventories.warehouse_id')
            ->join('products', 'products.id', 'inventories.product_id')
            ->join('colors', 'colors.id', 'inventories.color_id')
            ->join('sizes', 'sizes.id', 'inventories.size_id')
            ->where('quantity', '>', 0)
            ->whereIn('warehouse_id', $warehouses->where('to_transit', true)->pluck('id')->merge($warehouses->where('to_discount', true)->pluck('id'))->toArray())
            ->where('product_id', $product->id)
            ->where('color_id', $color->id)
            ->where('system', 'BMI')
            ->groupBy('products.code', 'colors.name', 'inventories.warehouse_id');
            
            $items = $items->get();

            $processedProduct = $items->whereIn('warehouse', $warehouses->where('to_transit', true)->pluck('id')->toArray());

            $finishedProduct = $items->whereIn('warehouse', $warehouses->where('to_discount', true)->pluck('id')->toArray());

            $processed = (object) [
                'product' => $product->code,
                'color' => $color->code
            ];

            $finished = (object) [
                'product' => $product->code,
                'color' => $color->code
            ];

            $processedTotal = 0;
            $finishedTotal = 0;

            foreach ($sizes as $size) {
                $processed->{"T{$size->code}"} = $processedProduct->pluck("T{$size->code}")->sum();
                $finished->{"T{$size->code}"} = $finishedProduct->pluck("T{$size->code}")->sum();
                $processedTotal += $processed->{"T{$size->code}"};
                $finishedTotal += $finished->{"T{$size->code}"};
            }

            $processed->TOTAL = $processedTotal;
            $finished->TOTAL = $finishedTotal;

            $alert = $processedProduct->isEmpty() && $finishedProduct->isEmpty() ? 'alert-warning' : 'alert-success';

            return (object) [
                'processed' => $processed,
                'finished' => $finished,
                'alert' => $alert
            ];
        } catch (Exception $e) {
            $alert = 'alert-danger';

            $processed = (object) [
                'product' => $product->code,
                'color' => $color->code
            ];

            $finished = (object) [
                'product' => $product->code,
                'color' => $color->code
            ];

            foreach ($sizes as $size) {
                $processed->{"T{$size->code}"} = 0;
                $finished->{"T{$size->code}"} = 0;
            }

            $processed->TOTAL = 0;
            $finished->TOTAL = 0;

            return (object) [
                'processed' => $processed,
                'finished' => $finished,
                'alert' => $alert
            ];
        }
    }

    private function cutted($product, $color, $sizes, $warehouses)
    {
        try {
            $items = Inventory::select('products.code AS product', 'colors.name AS color');
            foreach ($sizes as $size) {
                $items->addSelect(DB::raw("COALESCE(SUM(CASE WHEN sizes.code = '$size->code' THEN inventories.quantity ELSE 0 END), 0) AS T$size->code"));
            }
            $items->join('warehouses', 'warehouses.id', 'inventories.warehouse_id')
            ->join('products', 'products.id', 'inventories.product_id')
            ->join('colors', 'colors.id', 'inventories.color_id')
            ->join('sizes', 'sizes.id', 'inventories.size_id')
            ->where('quantity', '>', 0)
            ->whereIn('warehouse_id', $warehouses->where('to_cut', true)->pluck('id')->toArray())
            ->where('product_id', $product->id)
            ->where('color_id', $color->id)
            ->groupBy('products.code', 'colors.name');
            
            $items = $items->get();

            $cutted = (object) [
                'product' => $product->code,
                'color' => $color->code
            ];

            $cuttedTotal = 0;

            foreach ($sizes as $size) {
                $cutted->{"T{$size->code}"} = $items->pluck("T{$size->code}")->sum();
                $cuttedTotal += $cutted->{"T{$size->code}"};
            }

            $cutted->TOTAL = $cuttedTotal;

            return $cutted;
        } catch (Exception $e) {
            $cutted = (object) [
                'product' => $product->code,
                'color' => $color->code
            ];

            foreach ($sizes as $size) {
                $cutted->{"T{$size->code}"} = 0;
            }

            $cutted->TOTAL = 0;

            return $cutted;
        }
    }

    private function committed($product, $color, $sizes)
    {
        try {
            $committed = OrderDispatchDetail::select('products.code AS product', 'colors.name AS color');
            foreach ($sizes as $size) {
                $committed->addSelect(DB::raw("SUM(order_dispatch_details.T$size->code) as T$size->code"));
            }
            $committed->join('order_dispatches', 'order_dispatches.id', 'order_dispatch_details.order_dispatch_id')
            ->join('order_details', 'order_details.id', 'order_dispatch_details.order_detail_id')
            ->join('products', 'products.id', 'order_details.product_id')
            ->join('colors', 'colors.id', 'order_details.color_id')
            ->where('order_details.product_id', $product->id)
            ->where('order_details.color_id', $color->id)
            ->whereIn('order_dispatch_details.status', ['Pendiente', 'Aprobado', 'Alistamiento', 'Empacado', 'Facturacion'])
            ->where('order_dispatches.business_id', Auth::user()->business_id)
            ->groupBy('products.code', 'colors.name');

            $committed = $committed->first();

            if(empty($committed)) {
                $committed = (object) [];
                $committed->product = $product->code;
                $committed->color = $color->name;
                foreach ($sizes as $size) {
                    $committed->{"T{$size->code}"} = 0;
                }
                $committed->TOTAL = 0;
            } else {
                $committedTotal = 0;
                foreach ($sizes as $size) {
                    $committedTotal += $committed->{"T{$size->code}"};
                }
                $committed->TOTAL = 0;
            }

            return $committed;
        } catch (Exception $e) {
            if(empty($committed)) {
                $committed = (object) [];
                $committed->product = $product->code;
                $committed->color = $color->name;
                foreach ($sizes as $size) {
                    $committed->{"T{$size->code}"} = 0;
                }
                $committed->TOTAL = 0;
            }

            return $committed;
        }
    }

    private function requested($product, $color, $sizes)
    {
        try {
            $requested = OrderDetail::with('order.client', 'order.correria', 'product', 'color')
            ->select('order_details.*', DB::raw('T04 + T06 + T08 + T10 + T12 + T14 + T16 + T18 + T20 + T22 + T24 + T26 + T28 + T30 + T32 + T34 + T36 + T38 + TXXS + TXS + TS + TM + TL + TXL + TXXL as TOTAL'))
            ->where('product_id', $product->id)
            ->where('color_id', $color->id)
            ->whereHas('order', fn($query) => $query->whereIn('seller_status', ['Aprobado'])->whereIn('wallet_status', ['Aprobado', 'Parcialmente Aprobado'])->where('business_id', Auth::user()->business_id))
            ->whereIn('order_details.status', ['Aprobado'])
            ->get();

            return $requested;
        } catch (Exception $e) {
            return collect([]);
        }
    }

    private function availabled($product, $color, $inventory, $committed, $sizes)
    {
        try {
            $availabled = (object) [];
            $availabled->product = $product->code;
            $availabled->color = $color->name;

            $availabledTotal = 0;

            foreach ($sizes as $size) {
                $availabled->{"T{$size->code}"} = $inventory->pluck("T{$size->code}")->sum() - $committed->{"T{$size->code}"};
                $availabledTotal += $availabled->{"T{$size->code}"};
            }

            $availabled->TOTAL = $availabledTotal;

            return $availabled;
        } catch (Exception $e) {
            $availabled = (object) [];
            $availabled->product = $product->code;
            $availabled->color = $color->name;
            foreach ($sizes as $size) {
                $availabled->{"T{$size->code}"} = 0;
            }
            $committed->TOTAL = 0;

            return $availabled;
        }
    }

    private function filtered()
    {
        try {
            $filtered = OrderDispatchDetail::with('order_detail.product', 'order_detail.color')
            ->whereBetween('created_at', [Carbon::now()->startOfDay(), Carbon::now()->endOfDay()])
            ->get()->pluck('order_detail')->unique(function ($orderDetail) {
                return $orderDetail->product_id . '-' . $orderDetail->color_id;
            })
            ->map(function ($orderDetail) {
                return [
                    'product' => $orderDetail->product,
                    'color' => $orderDetail->color,
                ];
            })
            ->values();

            return $filtered;
        } catch (Exception $e) {
            return [];
        }
    }

    private function cleaned($string)
    {
        try {
            $string = strtoupper($string);
            $string = str_replace(['.', ' ', 'PPTN-', 'PPD1-', 'PPD2-', 'PPCF-', 'PPCT-', 'PPCNI-', 'PPCOR-', 'PPLV-', 'LVEXT-', 'PPCNE-', 'PPTER-', 'PTN-', 'TER-', 'PDDIS-', 'DIS-'], '', $string);
            $string = str_replace(["\r", "\n", "\t"], '', $string);
            $string = trim($string);

            return $string;
        } catch (Exception $e) {
            return $string;
        }
    }

    private function transformDataSiesa($item) 
    {
        try {
            $object = (object) [
                'BODEGA' => $this->cleaned($item->IdBodega),
                'CODIGO' => $this->cleaned($item->Referencia),
                'EXISTENCIA' => $item->Existencia,
                'DISPONIBLE' => $item->Disponible,
                'TALLA' => $this->cleaned($item->IdExtension1),
                'COLOR' => $this->cleaned($item->IdExtension2)
            ];
            
            return $object;
        } catch (Exception $e) {
            return $item;
        }
    }

    private function transformDataTns($item) 
    {
        try {
            $item->CODIGO = $this->cleaned($item->CODIGO);
            $array = explode('-', $item->CODIGO);
            $item->REFERENCIA = '';
            switch ($item->CONTAR_GUION) {
                case 2:
                    $item->REFERENCIA = "{$array[0]}";
                    break;
                case 3:
                    $item->REFERENCIA = "{$array[0]}-{$array[1]}";
                    break;
                case 4:
                    $item->REFERENCIA = "{$array[0]}-{$array[1]}-{$array[2]}";
                    break;
                default:
                    $item->REFERENCIA = "{$array[0]}";
                    break;
            }
            $item->TALLA = $array[$item->CONTAR_GUION - 1];
            $item->COLOR = $array[$item->CONTAR_GUION];
            
            return $item;
        } catch (Exception $e) {
            return $item;
        }
    }

    public function grafic(Request $request)
    {
        try {            
            $sizes = json_encode($request->input('chosenSizes'));
            $cutted = json_encode($request->input('cutted'));
            $filtered = json_encode($request->input('filtered'));

            $chartBar = new QuickChart(array(
                'width' => 750,
                'height' => 350
            ));

            $chartBar->setConfig("{
                type: 'line',
                data: {
                  labels: $sizes,
                  datasets: [{
                        label: 'CORTE',
                        backgroundColor: '#E15759',
                        borderColor: '#E15759',
                        data: $cutted,
                        fill: false,
                        lineTension: 0.4
                    },{
                        label: 'FILTRADO',
                        backgroundColor: '#59A14F',
                        borderColor: '#59A14F',
                        data: $filtered,
                        fill: false,
                        lineTension: 0.4
                    }],
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
                        text: 'GRAFICA REPRESENTATIVA CURVA CORTE | CURVA RESTANTE FILTRADA',
                        fontSize: 16,
                        fontColor: '#000',
                        fontFamily: 'sans-serif',
                        fontStyle: 'bold',
                        padding: 20
                    }
                }
            }");

            $path = "CHART_LINE_CUTTED_FILTERED.png";

            $imageData = file_get_contents($chartBar->getUrl());
            Storage::disk('public')->put('Charts/' . $path, $imageData);

            return $this->successResponse(
                [
                    'chart' => asset("storage/Charts/$path")
                ],
                "Grafica representativa curva corte vs curva filtrada generada exitosamente.",
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

    public function upload(FilterUploadRequest $request)
    {
        try {
            $items = Excel::toCollection(new FilterImport, $request->file('cuts'))->first();

            $warehouse = Warehouse::with('businesses')->where('to_cut', true)->whereHas('businesses', fn($query) => $query->where('businesses.id', Auth::user()->business_id))->firstOrFail();

            $sizes = Size::all();

            foreach ($items as $item) {
                $product = Product::where('code', $item['referencia'])->first();
                $color = Color::where('code', $item['color'])->first();

                if($product && $color) {
                    foreach ($sizes as $size) {
                        $inventory = Inventory::where('warehouse_id', $warehouse->id)->where('product_id', $product->id)->where('size_id', $size->id)->where('color_id', $color->id)->first();
                        $inventory = $inventory ? $inventory : new Inventory();
                        $inventory->warehouse_id = $warehouse->id;
                        $inventory->product_id = $product->id;
                        $inventory->size_id = $size->id;
                        $inventory->color_id = $color->id;
                        $inventory->quantity = $item["T$size->code"];
                        if(!is_null($item["T$size->code"])) {
                            $inventory->save();
                        }
                    }
                }
            }

            return $this->successResponse(
                '',
                'El corte inicial de las referencias fueron cargados exitosamente.',
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
