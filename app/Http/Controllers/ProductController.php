<?php

namespace App\Http\Controllers;

use App\Exports\ProductExport;
use App\Http\Controllers\Controller;
use App\Http\Requests\Product\ProductChargeRequest;
use App\Http\Requests\Product\ProductDeleteRequest;
use App\Http\Requests\Product\ProductDestroyRequest;
use App\Http\Requests\Product\ProductIndexQueryRequest;
use App\Http\Requests\Product\ProductRestoreRequest;
use App\Http\Requests\Product\ProductSyncSiesaRequest;
use App\Http\Requests\Product\ProductSyncTnsRequest;
use App\Http\Resources\Product\ProductIndexQueryCollection;
use App\Models\Color;
use App\Models\Product;
use App\Models\File;
use App\Models\Inventory;
use App\Models\Size;
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
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class ProductController extends Controller
{
    use ApiResponser;
    use ApiMessage;
    use Trademark;

    public function index()
    {
        try {
            return view('Dashboard.Products.Index');
        } catch (Exception $e) {
            return back()->with('danger', 'Ocurrió un error al cargar la vista: ' . $e->getMessage());
        }
    }

    public function indexQuery(ProductIndexQueryRequest $request)
    {
        try {
            $start_date = Carbon::parse($request->input('start_date'))->startOfDay();
            $end_date = Carbon::parse($request->input('end_date'))->endOfDay();
            //Consulta por nombre
            $products = Product::with('inventories.color', 'inventories.size', 'inventories.warehouse')
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
                ->withTrashed()                 
                ->orderBy($request->input('column'), $request->input('dir'))
                ->paginate($request->input('perPage'));

            return $this->successResponse(
                new ProductIndexQueryCollection($products),
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

    public function show($id)
    {
        try {
            $product = Product::with('files.user')->withTrashed()->findOrFail($id);

            foreach ($product->files as &$file) {
                $file->path = asset("storage/$file->path");
            }

            return $this->successResponse(
                [
                    'product' => $product
                ],
                'El producto fue encontrado exitosamente.',
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

    public function charge(ProductChargeRequest $request)
    {
        try {
            $messages = (object) array('success' => array(), 'warning' => array(), 'error' => array());
            if ($request->hasFile('photo')) {
                $photo = File::where('model_type', Product::class)->where('model_id', $request->input('product_id'))->where('type', 'PORTADA')->first();
                if ($photo) {
                    array_push($messages->warning, 'La imagen de portada fue reemplazada por la imagen que cargaste recientemente.');
                    if (Storage::disk('public')->exists($photo->path)) {
                        Storage::disk('public')->delete($photo->path);
                    }
                    $photo->delete();
                }

                $response = $this->file($request->file('photo'), $request->input('product_id'), 'PORTADA', 'Products/');
                array_push($messages->{$response->type}, $response->message);
            }
            
            if ($request->hasFile('photos')) {
                foreach ($request->file('photos') as $photo) {
                    $response = $this->file($photo, $request->input('product_id'), 'IMAGEN', 'Products/');
                    array_push($messages->{$response->type}, $response->message);
                }
            }
            
            if ($request->hasFile('videos')) {
                foreach ($request->file('videos') as $video) {
                    $response = $this->file($video, $request->input('product_id'), 'VIDEO', 'Products/');
                    array_push($messages->{$response->type}, $response->message);
                }
            }

            return $this->successResponse(
                [
                    'messages' => $messages
                ],
                'Las imagenes y videos del producto fueron cargadas exitosamente.',
                200
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

    private function file($document, $model_id, $type, $folder)
    {
        try {
            $name = $document->getClientOriginalName();
            $file = new File();
            $file->model_type = Product::class;
            $file->model_id = $model_id;
            $file->type = $type;
            $file->name = $document->getClientOriginalName();
            $file->path = Storage::disk('public')->put($folder . $model_id, $document);
            $file->mime = $document->getMimeType();
            $file->extension = $document->getClientOriginalExtension();
            $file->size = $document->getSize();
            $file->user_id = Auth::user()->id;
            $file->metadata = json_encode((array) stat($document));
            $file->save();

            return (object) [
                'type' => 'success',
                'message' => "El archivo $name de tipo $type fue guardado exitosamente. "
            ];
        } catch (QueryException $e) {
            return (object) [
                'type' => 'error',
                'message' => "Ocurrio un error al guardar los datos del archivo $name de tipo $type (" . $e->getMessage() . "). "
            ];
        } catch (Exception $e) {
            return (object) [
                'type' => 'error',
                'message' => "Ocurrio un error al guardar el archivo $name de tipo $type (" . $e->getMessage() . "). "
            ];
        }
    }

    public function destroy(ProductDestroyRequest $request)
    {
        try {
            $file = File::findOrFail($request->input('id'));
            if (Storage::disk('public')->exists($file->path)) {
                Storage::disk('public')->delete($file->path);
            }
            $file->delete();

            return $this->successResponse(
                $file,
                'La imagen del producto fue eliminado exitosamente.',
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

    public function delete(ProductDeleteRequest $request)
    {
        try {
            $product = Product::withTrashed()->findOrFail($request->input('id'))->delete();
            return $this->successResponse(
                $product,
                'El producto fue eliminado exitosamente.',
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

    public function restore(ProductRestoreRequest $request)
    {
        try {
            $product = Product::withTrashed()->findOrFail($request->input('id'))->restore();
            return $this->successResponse(
                $product,
                'El producto fue restaurado exitosamente.',
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

    public function download()
    {
        try {
            $products = Product::withTrashed()->select('code AS CODIGO', 'category AS CATEGORIA', 'trademark AS MARCA', 'price AS PRECIO', 'description AS DESCRIPCION')->get();

            return Excel::download(new ProductExport($products), "PRODUCTOS.xlsx");
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

    public function syncSiesa(ProductSyncSiesaRequest $request)
    {
        try {
            $referencia = strtoupper(trim($request->referencia));

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

            $query = $guzzleHttpClient->request('GET', "http://45.76.251.153/API_GT/api/orgBless/getInvPorBodega?Referencia={$referencia}&CentroOperacion=001", [
                'headers' => [ 'Authorization' => "Bearer {$token}"],
            ]);

            $items = json_decode($query->getBody()->getContents());

            $items = empty($items->detail) ? collect([]) : collect($items->detail);

            $items = $items->map(function ($item) {
                return $this->transformDataSiesa($item);
            });

            $codes = $items->pluck('Referencia')->unique()->values();
            
            foreach($codes as $code) {

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

                $search = $items->where('Referencia', $code)->first();
                
                $product = Product::where('code', $this->cleaned($code))->withTrashed()->first();
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
                $warehouses = Warehouse::whereIn('code', $warehousesProduct)->where(fn($query) => $query->where('to_transit', true)->orWhere('to_discount', true))->get();
    
                Inventory::with('warehouse')->where('product_id', $product->id)->whereHas('warehouse', fn($query) => $query->where('code', '<>', 'CUTCOR'))->where('system', 'SIESA')->update(['quantity' => 0]);

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
                'El inventario de Siesa de la referencia ' . $referencia . ' fue sincronizado exitosamente.',
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

    public function syncTns(ProductSyncTnsRequest $request)
    {
        try {
            $referencia = strtoupper(trim($request->referencia));

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
            ->where('M.CODIGO', 'LIKE', '%' . $referencia . '%')
            ->whereRaw("CHAR_LENGTH(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(M.CODIGO, 'PPD1-', ''), 'PPD2-', ''), 'PPCF-', ''), 'PPCT-', ''), 'PPCNI-', ''), 'PPCOR-', ''), 'PPLV-', ''), 'LVEXT-', ''), 'PPCNE-', ''), 'PPTER-', ''), 'PTN-', ''), 'TER-', ''), 'PDDIS-', ''), 'DIS-', '')) - CHAR_LENGTH(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(M.CODIGO, 'PPD1-', ''), 'PPD2-', ''), 'PPCF-', ''), 'PPCT-', ''), 'PPCNI-', ''), 'PPCOR-', ''), 'PPLV-', ''), 'LVEXT-', ''), 'PPCNE-', ''), 'PPTER-', ''), 'PTN-', ''), 'TER-', ''), 'PDDIS-', ''), 'DIS-', ''), '-', '')) >= 2")
            ->get()->map(function ($item) {
                return $this->transformDataTns($item);
            });

            $codes = $items->where('REFERENCIA', $referencia)->pluck('REFERENCIA')->unique()->values();

            foreach($codes as $code) {

                $search = $items->where('REFERENCIA', $code)->first();
                
                $product = Product::where('code', $this->cleaned($code))->withTrashed()->first();
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
                $warehouses = Warehouse::whereIn('code', $warehousesProduct)->where(fn($query) => $query->where('to_transit', true)->orWhere('to_discount', true))->get();
    
                Inventory::with('warehouse')->where('product_id', $product->id)->whereHas('warehouse', fn($query) => $query->where('code', '<>', 'CUTCOR'))->where('system', 'VISUAL TNS')->update(['quantity' => 0]);

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
                'El inventario de Tns de la referencia ' . $referencia . ' fue sincronizado exitosamente.',
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
            $item->Referencia = $this->cleaned($item->Referencia);
            $item->Categoria = $this->cleaned($item->Categoria);
            $item->Marca = $this->trademark($this->cleaned($item->Referencia));
            $item->Precio = $item->Precio == 0 || !$item->Precio ? 79900.00 : $item->Precio;
            
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
    
    public function sync()
    {
        try {
            $codes = [];

            foreach($codes as $code) {
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

                $query = $guzzleHttpClient->request('GET', "http://45.76.251.153/API_GT/api/orgBless/getInfoReferencia?Referencia={$code}", [
                    'headers' => [ 'Authorization' => "Bearer {$token}" ],
                ]);

                $items = json_decode($query->getBody()->getContents());

                $items = empty($items->detail) ? collect([]) : collect($items->detail);

                if($items->first()) {
                    $product = Product::where('code', $this->cleaned($code))->withTrashed()->first();
                    $product = $product ? $product : new Product();
                    $product->item = $items->first()->Item;
                    $product->code = $this->cleaned($code);
                    $product->category = '';
                    $product->trademark = $this->trademark($this->cleaned($code));
                    $product->description = trim($items->first()->DescItem);
                    $product->save();
                }
            }            

            return 'SUCCESS';
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }
}
