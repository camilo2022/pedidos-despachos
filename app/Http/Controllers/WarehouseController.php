<?php

namespace App\Http\Controllers;

use App\Http\Requests\Warehouse\WarehouseDeleteRequest;
use App\Http\Requests\Warehouse\WarehouseIndexQueryRequest;
use App\Http\Requests\Warehouse\WarehouseRestoreRequest;
use App\Http\Requests\Warehouse\WarehouseStoreRequest;
use App\Http\Requests\Warehouse\WarehouseUpdateRequest;
use App\Http\Resources\Warehose\WarehouseIndexQueryCollection;
use App\Models\Warehouse;
use App\Traits\ApiMessage;
use App\Traits\ApiResponser;
use Carbon\Carbon;
use Exception;
use GuzzleHttp\Client as GuzzleHttpClient;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;

class WarehouseController extends Controller
{
    use ApiResponser;
    use ApiMessage;

    public function index()
    {
        try {
            return view('Dashboard.Warehouses.Index');
        } catch (Exception $e) {
            return back()->with('danger', 'Ocurrió un error al cargar la vista: ' . $e->getMessage());
        }
    }

    public function indexQuery(WarehouseIndexQueryRequest $request)
    {
        try {
            $start_date = Carbon::parse($request->input('start_date'))->startOfDay();
            $end_date = Carbon::parse($request->input('end_date'))->endOfDay();
            $warehouses = Warehouse::when($request->filled('search'),
                    function ($query) use ($request) {
                        $query->search($request->input('search'));
                    }
                )
                ->when(!$request->filled('search'),
                    function ($query) {
                        $query->where('to_cut', true)
                        ->orWhere('to_transit', true)
                        ->orWhere('to_discount', true)
                        ->orWhere('to_exclusive', true);
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
                new WarehouseIndexQueryCollection($warehouses),
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

    public function create()
    {
        try {
            return $this->successResponse(
                '',
                'Ingrese los datos para hacer la validacion y registro.',
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

    public function store(WarehouseStoreRequest $request)
    {
        try {
            $warehouse = new Warehouse();
            $warehouse->name = $request->input('name');
            $warehouse->code = $request->input('code');
            $warehouse->to_cut = $request->input('to_cut');
            $warehouse->to_transit = $request->input('to_transit');
            $warehouse->to_discount = $request->input('to_discount');
            $warehouse->to_exclusive = $request->input('to_exclusive');
            $warehouse->save();

            return $this->successResponse(
                $warehouse,
                'La bodega fue registrada exitosamente.',
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

    public function edit($id)
    {
        try {
            return $this->successResponse(
                Warehouse::withTrashed()->findOrFail($id),
                'La bodega fue encontrada exitosamente.',
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

    public function update(WarehouseUpdateRequest $request, $id)
    {
        try {
            $warehouse = Warehouse::withTrashed()->findOrFail($id);
            $warehouse->name = $request->input('name');
            $warehouse->code = $request->input('code');
            $warehouse->to_cut = $request->input('to_cut');
            $warehouse->to_transit = $request->input('to_transit');
            $warehouse->to_discount = $request->input('to_discount');
            $warehouse->to_exclusive = $request->input('to_exclusive');
            $warehouse->save();

            return $this->successResponse(
                $warehouse,
                'La bodega fue actualizada exitosamente.',
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

    public function delete(WarehouseDeleteRequest $request)
    {
        try {
            $warehouse = Warehouse::withTrashed()->findOrFail($request->input('id'))->delete();
            return $this->successResponse(
                $warehouse,
                'La bodega fue eliminada exitosamente.',
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

    public function restore(WarehouseRestoreRequest $request)
    {
        try {
            $warehouse = Warehouse::withTrashed()->findOrFail($request->input('id'))->restore();
            return $this->successResponse(
                $warehouse,
                'La bodega fue restaurada exitosamente.',
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

            $query = $guzzleHttpClient->request('GET', 'http://45.76.251.153/API_GT/api/orgBless/getBodegas?CentroOperacion=001', [
                'headers' => [ 'Authorization' => "Bearer {$token}"],
            ]);

            $items = json_decode($query->getBody()->getContents());

            $items = empty($items->detail) ? [] : $items->detail;
            
            foreach($items as $item) {
                $warehouse = Warehouse::withTrashed()->where('code', $item->CodigoBodega)->first();
                $warehouse = $warehouse ? $warehouse : new Warehouse();
                $warehouse->name = $item->Nombre;
                $warehouse->code = $item->CodigoBodega;
                $warehouse->to_cut = false;
                $warehouse->to_transit = in_array($item->CodigoBodega, ['PPCNI', 'PPCNE', 'PPCOR', 'PPLV', 'LVEXT', 'PPTER', 'TER', 'PPTN', 'PTCOR', 'ZMED']);
                $warehouse->to_discount = in_array($item->CodigoBodega, ['PT', 'PT001', 'PDDIS', 'DIS', 'BMEC', 'BDM']);
                $warehouse->to_exclusive = in_array($item->CodigoBodega, ['BCAR']);
                $warehouse->save();
            }

            return $this->successResponse(
                '',
                'Las bodegas de Siesa fueron sincronizados exitosamente.',
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

    public function syncTns()
    {
        try {
            $items = DB::connection('firebird')->table('BODEGA')->get();
            
            foreach($items as $item) {
                $warehouse = Warehouse::withTrashed()->where('code', $item->CODIGO)->first();
                $warehouse = $warehouse ? $warehouse : new Warehouse();
                $warehouse->name = mb_convert_encoding($item->NOMBRE, 'ISO-8859-1', 'UTF-8');
                $warehouse->code = $item->CODIGO;
                $warehouse->to_cut = false;
                $warehouse->to_transit = in_array($item->CODIGO, ['PPCNI', 'PPCNE', 'PPCOR', 'PPLV', 'LVEXT', 'PPTER', 'TER', 'PPTN', 'PTCOR', 'ZMED']);
                $warehouse->to_discount = in_array($item->CODIGO, ['PT', 'PT001', 'PDDIS', 'DIS', 'BMEC', 'BDM']);
                $warehouse->to_exclusive = in_array($item->CODIGO, ['BCAR']);
                $warehouse->save();
            }
            
            return $this->successResponse(
                '',
                'Las bodegas de Tns fueron sincronizados exitosamente.',
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
}
