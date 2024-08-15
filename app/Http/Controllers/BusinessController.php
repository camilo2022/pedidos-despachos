<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\Business\BusinessAssignWarehousesRequest;
use App\Http\Requests\Business\BusinessCreateRequest;
use App\Http\Requests\Business\BusinessDeleteRequest;
use App\Http\Requests\Business\BusinessIndexQueryRequest;
use App\Http\Requests\Business\BusinessRemoveWarehousesRequest;
use App\Http\Requests\Business\BusinessRestoreRequest;
use App\Http\Requests\Business\BusinessStoreRequest;
use App\Http\Requests\Business\BusinessUpdateRequest;
use App\Http\Resources\Business\BusinessIndexQueryCollection;
use App\Models\Business;
use App\Models\City;
use App\Models\Country;
use App\Models\Departament;
use App\Models\File;
use App\Models\ModelWarehouse;
use App\Models\Warehouse;
use App\Traits\ApiMessage;
use App\Traits\ApiResponser;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class BusinessController extends Controller
{
    use ApiResponser;
    use ApiMessage;

    public function index()
    {
        try {
            /* $cities = DB::connection('firebird')->table('CIUDANE')->select('PAIS.NOMBRE AS PAIS', 'CIUDANE.DEPARTAMENTO AS DEPARTAMENTO', 'CIUDANE.NOMBRE AS CIUDAD', 'CIUDANE.CODIGO AS CODIGO')
            ->where('CIUDANEID', '>', 12)
            ->where('CIUDANEID', '<>', 1133)
            ->join('PAIS', 'PAIS.PAISID', '=', 'CIUDANE.PAISID')->get();
            
            foreach($cities->groupBy('DEPARTAMENTO') as $departament => $items) {
                $depa = Departament::where('name', $departament)->first();
                $depa = $depa ? $depa : new Departament();
                $depa->name = mb_convert_encoding($departament, 'ISO-8859-1', 'UTF-8');
                $depa->country_id = 1;
                $depa->save();

                foreach($items as $item){
                    $city = City::where('name', $item->CIUDAD)->where('departament_id', $depa->id)->first();
                    $city = $city ? $city : new City();
                    $city->departament_id = $depa->id;
                    $city->name = mb_convert_encoding($item->CIUDAD, 'ISO-8859-1', 'UTF-8');
                    $city->code = $item->CODIGO;
                    $city->save();
                }
            } */

            return view('Dashboard.Businesses.Index');
        } catch (Exception $e) {
            return back()->with('danger', 'Ocurrió un error al cargar la vista: ' . $e->getMessage());
        }
    }

    public function indexQuery(BusinessIndexQueryRequest $request)
    {
        try {
            $start_date = Carbon::parse($request->input('start_date'))->startOfDay();
            $end_date = Carbon::parse($request->input('end_date'))->endOfDay();
            $businesses = Business::when($request->filled('search'),
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
                new BusinessIndexQueryCollection($businesses),
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

    public function create(BusinessCreateRequest $request)
    {
        try {
            if($request->filled('country')) {
                $departaments = Departament::with('country')->whereHas('country', fn($query) => $query->where('name', $request->input('country')))->get();
                
                return $this->successResponse(
                    [
                        'departaments' => $departaments
                    ],
                    'Departamentos encontrados exitosamente.',
                    204
                );
            }

            if($request->filled('departament')) {
                $cities = City::with('departament')->whereHas('departament', fn($query) => $query->where('name', $request->input('departament')))->get();
                
                return $this->successResponse(
                    [
                        'cities' => $cities
                    ],
                    'Ciudades encontradas exitosamente.',
                    204
                );
            }

            $countries = Country::all();

            return $this->successResponse(
                [
                    'countries' => $countries
                ],
                'Ingrese los datos para hacer la validacion y registro.',
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

    public function store(BusinessStoreRequest $request)
    {
        try {
            $business = new Business();
            $business->name = $request->input('name');
            $business->branch = $request->input('branch');
            $business->number_document = $request->input('number_document');
            $business->country = $request->input('country');
            $business->departament = $request->input('departament');
            $business->city = $request->input('city');
            $business->address = $request->input('address');
            $business->order_footer = $request->input('order_footer');
            $business->order_notify_email = $request->input('order_notify_email');
            $business->dispatch_footer = $request->input('dispatch_footer');
            $business->packing_footer = $request->input('packing_footer');
            $business->save();            

            if ($request->hasFile('letterhead')) {
                $file = new File();
                $file->model_type = Business::class;
                $file->model_id = $business->id;
                $file->type = 'MEMBRETE';
                $file->name = $request->file('letterhead')->getClientOriginalName();
                $file->path = $request->file('letterhead')->store('Businesses/' . $business->id, 'public');
                $file->mime = $request->file('letterhead')->getMimeType();
                $file->extension = $request->file('letterhead')->getClientOriginalExtension();
                $file->size = $request->file('letterhead')->getSize();
                $file->user_id = Auth::user()->id;
                $file->metadata = json_encode((array) stat($request->file('letterhead')));
                $file->save();
            }

            return $this->successResponse(
                $business,
                'La sucursal de la empresa fue registrada exitosamente.',
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
            $business = Business::withTrashed()->findOrFail($id);

            return $this->successResponse(
                [
                    'business' => $business
                ],
                'La sucursal de la empresa fue encontrada exitosamente.',
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

    public function update(BusinessUpdateRequest $request, $id)
    {
        try {
            $business = Business::withTrashed()->findOrFail($id);
            $business->name = $request->input('name');
            $business->branch = $request->input('branch');
            $business->nit = $request->input('nit');
            $business->country = $request->input('country');
            $business->departament = $request->input('departament');
            $business->city = $request->input('city');
            $business->address = $request->input('address');
            $business->order_footer = $request->input('order_footer');
            $business->dispatch_footer = $request->input('dispatch_footer');
            $business->packing_footer = $request->input('packing_footer');
            $business->save();

            return $this->successResponse(
                $business,
                'La sucursal de la empresa fue actualizada exitosamente.',
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

    public function delete(BusinessDeleteRequest $request)
    {
        try {
            $business = Business::withTrashed()->findOrFail($request->input('id'))->delete();
            return $this->successResponse(
                $business,
                'La sucursal de la empresa fue eliminada exitosamente.',
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

    public function restore(BusinessRestoreRequest $request)
    {
        try {
            $business = Business::withTrashed()->findOrFail($request->input('id'))->restore();
            return $this->successResponse(
                $business,
                'La sucursal de la empresa fue restaurada exitosamente.',
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

    public function warehouses($id)
    {
        try {
            $warehouses = Warehouse::with('businesses')->where(fn($query) => $query->doesntHave('businesses')->orWhereHas('businesses', fn($query) => $query->where('businesses.id', $id)))
            ->where(fn($query) => $query->where('to_cut', true)->orWhere('to_transit', true)->orWhere('to_discount', true)->orWhere('to_exclusive', true))->get();
            $business = Business::with('warehouses')->findOrFail($id);

            foreach ($warehouses as $warehouse) {
                $businessesId = $warehouse->businesses->pluck('id')->all();
                $warehouse->admin = in_array($id, $businessesId);
            }

            return $this->successResponse(
                [
                    'business' => $business,
                    'warehouses' => $warehouses
                ],
                'La sucursal de la empresa fue encontrada exitosamente.',
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

    public function assignWarehouses(BusinessAssignWarehousesRequest $request)
    {
        try {
            $modelWarehouse = new ModelWarehouse();
            $modelWarehouse->model_type = Business::class;
            $modelWarehouse->model_id = $request->input('business_id');
            $modelWarehouse->warehouse_id = $request->input('warehouse_id');
            $modelWarehouse->save();

            return $this->successResponse(
                $modelWarehouse,
                'La bodega fue asignada a la sucursal de la empresa exitosamente.',
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

    public function removeWarehouses(BusinessRemoveWarehousesRequest $request)
    {
        try {
            $modelWarehouse = ModelWarehouse::whereHasMorph('model', [Business::class], fn($query) => $query->where('model_id', $request->input('business_id')))
            ->where('warehouse_id', $request->input('warehouse_id'))->delete();

            return $this->successResponse(
                $modelWarehouse,
                'La bodega fue removida a la sucursal de la empresa exitosamente.',
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
