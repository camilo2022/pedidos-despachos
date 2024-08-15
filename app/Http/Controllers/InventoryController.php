<?php

namespace App\Http\Controllers;

use App\Exports\InventoryExport;
use App\Http\Controllers\Controller;
use App\Http\Requests\Inventory\InventoryIndexQueryRequest;
use App\Http\Requests\Inventory\InventorySyncBmiRequest;
use App\Http\Requests\Inventory\InventoryUploadRequest;
use App\Http\Resources\Inventory\InventoryIndexQueryCollection;
use App\Imports\Inventory\InventoryImport;
use App\Imports\Inventory\InventorySyncBmi;
use App\Models\Color;
use App\Models\Inventory;
use App\Models\Product;
use App\Models\Size;
use App\Models\User;
use App\Models\Warehouse;
use App\Traits\ApiMessage;
use App\Traits\ApiResponser;
use App\Traits\Trademark;
use Carbon\Carbon;
use Exception;
use GuzzleHttp\Client as GuzzleHttpClient;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class InventoryController extends Controller
{
    use ApiResponser;
    use ApiMessage;
    use Trademark;

    public function index()
    {
        try {
            $sizes = Size::all();               

            return view('Dashboard.Inventories.Index', compact('sizes'));
        } catch (Exception $e) {
            return back()->with('danger', 'Ocurrió un error al cargar la vista: ' . $e->getMessage());
        }
    }

    public function indexQuery(InventoryIndexQueryRequest $request)
    {
        try {
            $start_date = Carbon::parse($request->input('start_date'))->startOfDay();
            $end_date = Carbon::parse($request->input('end_date'))->endOfDay();
            
            $sizes = Size::all();  
            $inventories = Inventory::select(
                'warehouses.name AS BODEGA', 'warehouses.code AS CODBOD', 
                'products.trademark AS MARCA', 'products.code AS REFERENCIA', 
                'colors.name AS COLOR', 'colors.code AS CODCOL', 'inventories.system AS SISTEMA'
            );
            foreach ($sizes as $size) {
                $inventories->addSelect(DB::raw("COALESCE(SUM(CASE WHEN sizes.code = '$size->code' THEN inventories.quantity ELSE 0 END), 0) AS T$size->code"));
            }
            $inventories->when($request->filled('search'),
                function ($query) use ($request) {
                    $query->search($request->input('search'));
                }
            )
            ->when($request->filled('start_date') && $request->filled('end_date'),
                function ($query) use ($start_date, $end_date) {
                    $query->filterByDate($start_date, $end_date);
                }
            )
            ->join('warehouses', 'warehouses.id', 'inventories.warehouse_id')
            ->join('products', 'products.id', 'inventories.product_id')
            ->join('colors', 'colors.id', 'inventories.color_id')
            ->join('sizes', 'sizes.id', 'inventories.size_id')
            ->where('warehouses.to_cut', false)
            ->where('quantity', '>', 0)
            ->when(in_array(Auth::user()->title, ['VENDEDOR', 'VENDEDOR ESPECIAL']),
                function ($query) {
                    $query->whereIn('warehouse_id', User::with('warehouses')->findOrFail(Auth::user()->id)->warehouses->pluck('id')->toArray());
                }
            )
            ->groupBy(
                'warehouses.name', 'warehouses.code', 
                'products.trademark', 'products.code', 
                'colors.name', 'colors.code', 'inventories.system'
            );
            $inventories = $inventories->orderBy($request->input('column'), $request->input('dir'))
            ->paginate($request->input('perPage'));

            return $this->successResponse(
                new InventoryIndexQueryCollection($inventories),
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

    public function uploadQuery()
    {
        try {
            return $this->successResponse(
                '',
                'Cargue el archivo para hacer la validacion y registro.',
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

    public function upload(InventoryUploadRequest $request)
    {
        try {
            $items = Excel::toCollection(new InventoryImport, $request->file('inventories'))->first();

            $sizes = Size::all();

            foreach ($items as $item) {
                $product = Product::where('code', $item['referencia'])->first();
                $color = Color::where('code', $item['color'])->first();
                $warehouse = Warehouse::with('businesses')->where('to_discount', true)->where('code', $item['bodega'])->whereHas('businesses', fn($query) => $query->where('businesses.id', Auth::user()->business_id))->firstOrFail();

                if($product && $color && $warehouse) {
                    foreach ($sizes as $size) {
                        $inventory = Inventory::where('warehouse_id', $warehouse->id)->where('product_id', $product->id)->where('size_id', $size->id)->where('color_id', $color->id)->where('system', 'PROYECCION')->first();
                        $inventory = $inventory ? $inventory : new Inventory();
                        $inventory->warehouse_id = $warehouse->id;
                        $inventory->product_id = $product->id;
                        $inventory->size_id = $size->id;
                        $inventory->color_id = $color->id;
                        $inventory->quantity = $inventory ? $inventory->quantity + $item["T$size->code"] : $item["T$size->code"];
                        $inventory->system = 'PROYECCION';
                        if(!is_null($item["T$size->code"])) {
                            $inventory->save();
                        }
                    }
                }
            }

            return $this->successResponse(
                '',
                'La proyeccion de las referencias fueron cargados exitosamente.',
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

    public function download()
    {
        try {
            $inventories = Inventory::select(
                    'warehouses.name AS BODEGA', 'warehouses.code AS CODBOD', 'products.trademark AS MARCA', 'products.code AS REFERENCIA', 
                    'colors.name AS COLOR', 'colors.code AS CODCOL', 'sizes.code AS TALLA', 'inventories.quantity AS CANTIDAD', 'inventories.system AS SISTEMA'
                )
                ->join('warehouses', 'warehouses.id', 'inventories.warehouse_id')
                ->join('products', 'products.id', 'inventories.product_id')
                ->join('colors', 'colors.id', 'inventories.color_id')
                ->join('sizes', 'sizes.id', 'inventories.size_id')
                ->where('warehouses.to_cut', false)
                ->where('quantity', '>', 0)
                ->get();

            return Excel::download(new InventoryExport($inventories), "INVENTARIOS.xlsx");
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

    public function syncSiesa()
    {
        try {
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
            
            $query = $guzzleHttpClient->request('GET', 'http://45.76.251.153/API_GT/api/orgBless/getInvPorBodega?CentroOperacion=001', [
                'headers' => [ 'Authorization' => "Bearer {$token}"],
            ]);

            $items = json_decode($query->getBody()->getContents());

            $items = empty($items->detail) ? collect([]) : collect($items->detail);

            $items = $items->map(function ($item) {
                return $this->transformDataSiesa($item);
            });

            $codes = $items->pluck('Referencia')->unique()->values();
            
            foreach($codes as $code) {
                $referencia = trim(collect(explode('-', $code))->last());

                $auth = $guzzleHttpClient->request('POST', '/API_GT/api/login/authenticate', [
                    'form_params' => [
                        'Username' => $user,
                        'Password' => $password,
                    ]
                ]);
                
                $token = str_replace('"', '', $auth->getBody()->getContents());

                $query = $guzzleHttpClient->request('GET', "http://45.76.251.153/API_GT/api/orgBless/getInfoReferencia?Referencia={$referencia}", [
                    'headers' => [ 'Authorization' => "Bearer {$token}"],
                ]);

                $item = json_decode($query->getBody()->getContents());

                $item = empty($item->detail) ? collect([]) : collect($item->detail); 

                /* $item = collect([]); */

                $search = $items->where('Referencia', $code)->first();
                
                $product = Product::where('code', $this->cleaned($code))->first();
                $product = $product ? $product : new Product();
                $product->item = $item->first() ? $item->first()->Item : $search->Item;
                $product->code = $this->cleaned($code);
                $product->category = trim(collect(explode('-', $search->Categoria))->last()) ?? 'N/N';
                $product->trademark = $this->trademark($this->cleaned($code));
                $product->price = $search->Precio > 0 ? (float) $search->Precio : 79900.00 ;
                $product->description = $item->first() ? trim($item->first()->DescItem) : 'NO ENCONTRADO';
                $product->save();

                $sizesProduct = $items->where('Referencia', $code)->pluck('IdExtension1')->unique()->values();
                $colorsProduct = $items->where('Referencia', $code)->pluck('IdExtension2')->unique()->values();
                $warehousesProduct = $items->where('Referencia', $code)->pluck('IdBodega')->unique()->values();

                $sizes = Size::whereIn('code', $sizesProduct)->get();
                $colors = Color::whereIn('code', $colorsProduct)->get();
                $warehouses = Warehouse::whereIn('code', $warehousesProduct)->where(fn($query) => $query->where('to_transit', true)->orWhere('to_discount', true)->orWhere('to_exclusive', true))->get();

                Inventory::with('warehouse')->where('product_id', $product->id)->whereHas('warehouse', fn($query) => $query->where('to_cut', false))->where('system', 'SIESA')->update(['quantity' => 0]);

                foreach($warehouses as $warehouse) {
                    foreach($sizes as $size) {
                        foreach($colors as $color) {
                            $inventory = Inventory::where('warehouse_id', $warehouse->id)->where('product_id', $product->id)->where('size_id', $size->id)->where('color_id', $color->id)->where('system', 'SIESA')->first();
                            $inventory = $inventory ? $inventory : new Inventory();
                            $inventory->warehouse_id = $warehouse->id;
                            $inventory->product_id = $product->id;
                            $inventory->size_id = $size->id;
                            $inventory->color_id = $color->id;
                            $inventory->quantity = $items->where('IdBodega', $warehouse->code)->where('Referencia', $code)->where('IdExtension1', $size->code)->where('IdExtension2', $color->code)->pluck('Existencia')->sum();
                            $inventory->system = 'SIESA';
                            $inventory->save();
                        }
                    }
                }
            }

            return $this->successResponse(
                '',
                'Los productos de Siesa fueron sincronizados exitosamente.',
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

    public function syncTns()
    {
        try {
            $items = DB::connection('firebird')
            ->table('MATERIAL as M')
            ->select(
                'B.CODIGO AS CODBOD', 'B.NOMBRE AS NOMBOD', 'M.DESCRIP AS DESCRIPCION', 'GM.DESCRIP AS CATEGORIA', 'SM.EXISTENC AS EXISTENCIA',
                DB::raw("CHAR_LENGTH(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(M.CODIGO, 'PPD1-', ''), 'PPD2-', ''), 'PPCF-', ''), 'PPCT-', ''), 'PPCNI-', ''), 'PPCOR-', ''), 'PPLV-', ''), 'LVEXT-', ''), 'PPCNE-', ''), 'PPTER-', ''), 'PTN-', ''), 'TER-', ''), 'PDDIS-', ''), 'DIS-', '')) - CHAR_LENGTH(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(M.CODIGO, 'PPD1-', ''), 'PPD2-', ''), 'PPCF-', ''), 'PPCT-', ''), 'PPCNI-', ''), 'PPCOR-', ''), 'PPLV-', ''), 'LVEXT-', ''), 'PPCNE-', ''), 'PPTER-', ''), 'PTN-', ''), 'TER-', ''), 'PDDIS-', ''), 'DIS-', ''), '-', '')) AS CONTAR_GUION"),
                DB::raw("REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(M.CODIGO, 'PPD1-', ''), 'PPD2-', ''), 'PPCF-', ''), 'PPCT-', ''), 'PPCNI-', ''), 'PPCOR-', ''), 'PPLV-', ''), 'LVEXT-', ''), 'PPCNE-', ''), 'PPTER-', ''), 'PTN-', ''), 'TER-', ''), 'PDDIS-', ''), 'DIS-', '') AS CODIGO")
            )
            ->leftJoin('SALMATERIAL as SM', 'SM.MATID', 'M.MATID')
            ->leftJoin('BODEGA as B', 'B.BODID', 'SM.BODID')
            ->leftJoin('GRUPMAT as GM', 'GM.GRUPMATID', 'M.GRUPMATID')
            ->whereIn('B.CODIGO', Warehouse::where('to_transit', true)->orWhere('to_discount', true)->get()->pluck('code')->values())
            ->whereRaw("CHAR_LENGTH(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(M.CODIGO, 'PPD1-', ''), 'PPD2-', ''), 'PPCF-', ''), 'PPCT-', ''), 'PPCNI-', ''), 'PPCOR-', ''), 'PPLV-', ''), 'LVEXT-', ''), 'PPCNE-', ''), 'PPTER-', ''), 'PTN-', ''), 'TER-', ''), 'PDDIS-', ''), 'DIS-', '')) - CHAR_LENGTH(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(M.CODIGO, 'PPD1-', ''), 'PPD2-', ''), 'PPCF-', ''), 'PPCT-', ''), 'PPCNI-', ''), 'PPCOR-', ''), 'PPLV-', ''), 'LVEXT-', ''), 'PPCNE-', ''), 'PPTER-', ''), 'PTN-', ''), 'TER-', ''), 'PDDIS-', ''), 'DIS-', ''), '-', '')) >= 2")
            ->get()->map(function ($item) {
                return $this->transformDataTns($item);
            });

            $codes = $items->pluck('REFERENCIA')->unique()->values();

            foreach($codes as $code) {

                $search = $items->where('REFERENCIA', $code)->first();
                
                $product = Product::where('code', $this->cleaned($code))->first();
                $product = $product ? $product : new Product();
                $product->item = is_null($product->item) ? '-' : $product->item;
                $product->code = $this->cleaned($code);
                $product->category = trim(collect(explode('-', mb_convert_encoding($search->CATEGORIA, 'ISO-8859-1', 'UTF-8')))->last()) ?? 'N/N';
                $product->trademark = $this->trademark($this->cleaned($code));
                $product->price = is_null($product->price) ? 79900.00 : $product->price;
                $product->description = $search ? trim(mb_convert_encoding($search->DESCRIPCION, 'ISO-8859-1', 'UTF-8')) : 'NO ENCONTRADO';
                $product->save();
                
                $sizesProduct = $items->where('REFERENCIA', $code)->pluck('TALLA')->unique()->values();
                $colorsProduct = $items->where('REFERENCIA', $code)->pluck('COLOR')->unique()->values();
                $warehousesProduct = $items->where('REFERENCIA', $code)->pluck('CODBOD')->unique()->values();

                $sizes = Size::whereIn('code', $sizesProduct)->get();
                $colors = Color::whereIn('code', $colorsProduct)->get();
                $warehouses = Warehouse::whereIn('code', $warehousesProduct)->where(fn($query) => $query->where('to_transit', true)->orWhere('to_discount', true)->orWhere('to_exclusive', true))->get();

                Inventory::with('warehouse')->where('product_id', $product->id)->whereHas('warehouse', fn($query) => $query->where('to_cut', false))->where('system', 'VISUAL TNS')->update(['quantity' => 0]);

                foreach($warehouses as $warehouse) {
                    foreach($sizes as $size) {
                        foreach($colors as $color) {
                            $inventory = Inventory::where('warehouse_id', $warehouse->id)->where('product_id', $product->id)->where('size_id', $size->id)->where('color_id', $color->id)->where('system', 'VISUAL TNS')->first();
                            $inventory = $inventory ? $inventory : new Inventory();
                            $inventory->warehouse_id = $warehouse->id;
                            $inventory->product_id = $product->id;
                            $inventory->size_id = $size->id;
                            $inventory->color_id = $color->id;
                            $inventory->quantity = $items->where('CODBOD', $warehouse->code)->where('REFERENCIA', $code)->where('TALLA', $size->code)->where('COLOR', $color->code)->pluck('EXISTENCIA')->sum();
                            $inventory->system = 'VISUAL TNS';
                            $inventory->save();
                        }
                    }
                }
            }

            return $this->successResponse(
                '',
                'Los productos de Tns fueron sincronizados exitosamente.',
                200
            );
        } catch (Exception $e) {
            return$e->getMessage();
            return $this->errorResponse(
                [
                    'message' => $this->getMessage('Exception'),
                    'error' => $e->getMessage()
                ],
                500
            );
        }
    }

    public function syncBmiQuery()
    {
        try {
            return $this->successResponse(
                '',
                'Cargue el archivo para hacer la validacion y registro.',
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

    public function syncBmi(InventorySyncBmiRequest $request)
    {
        try {
            Inventory::with('warehouse')->whereHas('warehouse', fn($query) => $query->where('to_discount', true))->where('system', 'BMI')->update(['quantity' => 0]);

            $items = Excel::toCollection(new InventorySyncBmi, $request->file('inventories'))->first();

            $items = $items->map(function ($item) {
                return (object) json_decode(json_encode($this->transformDataBmi($item)), true);
            });
            
            $codes = $items->pluck('Referencia')->unique()->values();
            
            foreach($codes as $code) {
                
                $product = Product::where('code', $this->cleaned($code))->first();

                if($product) {
                    $sizesProduct = $items->where('Referencia', $code)->pluck('Talla')->unique()->values();
                    $colorsProduct = $items->where('Referencia', $code)->pluck('Color')->unique()->values();
                    $warehousesProduct = $items->where('Referencia', $code)->pluck('CodBodega')->unique()->values();
    
                    $sizes = Size::whereIn('code', $sizesProduct)->get();
                    $colors = Color::whereIn('code', $colorsProduct)->get();
                    $warehouses = Warehouse::whereIn('code', $warehousesProduct)->where(fn($query) => $query->where('to_transit', true)->orWhere('to_discount', true))->get();
    
                    foreach($warehouses as $warehouse) {
                        foreach($sizes as $size) {
                            foreach($colors as $color) {
                                $inventory = Inventory::where('warehouse_id', $warehouse->id)->where('product_id', $product->id)->where('size_id', $size->id)->where('color_id', $color->id)->where('system', 'BMI')->first();
                                $inventory = $inventory ? $inventory : new Inventory();
                                $inventory->warehouse_id = $warehouse->id;
                                $inventory->product_id = $product->id;
                                $inventory->size_id = $size->id;
                                $inventory->color_id = $color->id;
                                $inventory->quantity = $items->where('CodBodega', $warehouse->code)->where('Referencia', $code)->where('Talla', $size->code)->where('Color', $color->code)->pluck('Disponible')->sum();
                                $inventory->system = 'BMI';
                                $inventory->save();
                            }
                        }
                    }
                }
            }

            return $this->successResponse(
                '',
                'El inventario de Bmi fueron cargados exitosamente.',
                201
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

    private function cleaned($string)
    {
        try {
            $string = strtoupper($string);
            $string = str_replace(['.', ' ', 'PPTN-', 'PPD1-', 'PPD2-', 'PPCF-', 'PPCT-', 'PPCNI-', 'PPCOR-', 'PPLV-', 'LVEXT-', 'PPCNE-', 'PPTER-', 'PTN-', 'TER-', 'PDDIS-', 'DIS-', 'PPBL-'], '', $string);
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
            $item->Referencia = $this->cleaned($item->Referencia);
            $item->Categoria = $this->cleaned($item->Categoria);
            $item->Marca = $this->trademark($this->cleaned($item->Referencia));
            $item->Precio = $item->Precio == 0 ? 79900.00 : $item->Precio;
            
            return $item;
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
                    $item->TALLA = $array[$item->CONTAR_GUION];
                    $item->COLOR = '';
                    break;
                case 3:
                    $item->REFERENCIA = "{$array[0]}-{$array[1]}";
                    $item->TALLA = $array[$item->CONTAR_GUION - 1];
                    $item->COLOR = $array[$item->CONTAR_GUION];
                    break;
                case 4:
                    $item->REFERENCIA = "{$array[0]}-{$array[1]}-{$array[2]}";
                    $item->TALLA = $array[$item->CONTAR_GUION - 1];
                    $item->COLOR = $array[$item->CONTAR_GUION];
                    break;
                default:
                    $item->REFERENCIA = "{$array[0]}";
                    $item->TALLA = $array[$item->CONTAR_GUION];
                    $item->COLOR = '';
                    break;
            }
            
            return $item;
        } catch (Exception $e) {
            return $item;
        }
    }

    private function transformDataBmi($item) 
    {
        try {
            $item['Item'] = $this->cleaned($item['Item']);
            $array = explode('-', $item['Item']);
            $item['Referencia'] = '';
            if(count($array) > 2) {
                switch (count($array)) {
                    case 3:
                        $item['Referencia'] = "{$array[0]}";
                        break;
                    case 4:
                        $item['Referencia'] = "{$array[0]}-{$array[1]}";
                        break;
                    case 5:
                        $item['Referencia'] = "{$array[0]}-{$array[1]}-{$array[2]}";
                        break;
                    default:
                    $item['Referencia'] = "{$array[0]}";
                        break;
                }
                $item['Talla'] = $array[count($array) - 2];
                $item['Color'] = $array[count($array) - 1];
            } else {
                $item['Referencia'] = "{$array[0]}";
                $item['Talla'] = $array[count($array) - 1];
                $item['Color'] = '';
            }

            return $item;
        } catch (Exception $e) {
            return $item;
        }
    }
}
