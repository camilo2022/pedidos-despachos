<?php

namespace App\Http\Controllers;

use App\Http\Requests\Correria\CorreriaDeleteRequest;
use App\Http\Requests\Correria\CorreriaIndexQueryRequest;
use App\Http\Requests\Correria\CorreriaStoreRequest;
use App\Http\Requests\Correria\CorreriaUpdateRequest;
use App\Http\Resources\Correria\CorreriaIndexQueryCollection;
use App\Models\Collection;
use App\Models\Correria;
use App\Traits\ApiMessage;
use App\Traits\ApiResponser;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CorreriaController extends Controller
{
    use ApiResponser;
    use ApiMessage;

    public function index()
    {
        try {
            return view('Dashboard.Correrias.Index');
        } catch (Exception $e) {
            return back()->with('danger', 'Ocurrió un error al cargar la vista: ' . $e->getMessage());
        }
    }

    public function indexQuery(CorreriaIndexQueryRequest $request)
    {
        try {
            $start_date = Carbon::parse($request->input('start_date'))->startOfDay();
            $end_date = Carbon::parse($request->input('end_date'))->endOfDay();
            
            $correria = Correria::with('business')
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
                ->when(Auth::user()->title !== 'SUPER ADMINISTRADOR', function ($query) {
                    $query->where('business_id', Auth::user()->business_id);
                })
                ->orderBy($request->input('column'), $request->input('dir'))
                ->paginate($request->input('perPage'));

            return $this->successResponse(
                new CorreriaIndexQueryCollection($correria),
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
                204
            );
        } catch (Exception $e) {
            // Devolver una respuesta de error en caso de excepción
            return $this->errorResponse(
                [
                    'message' => $this->getMessage('Exception'),
                    'error' => $e->getMessage()
                ],
                500
            );
        }
    }

    public function store(CorreriaStoreRequest $request)
    {
        try {
            $correria = new Correria();
            $correria->name = $request->input('name');
            $correria->code = $request->input('code');
            $correria->start_date = Carbon::parse($request->input('start_date'))->startOfDay();
            $correria->end_date = Carbon::parse($request->input('end_date'))->endOfDay();
            $correria->business_id = Auth::user()->business_id;
            $correria->save();

            DB::statement('CALL correrias(?,?)', [Carbon::now()->format('Y-m-d H:i:s'), Auth::user()->business_id]);

            return $this->successResponse(
                $correria,
                'La correria fue registrada exitosamente.',
                201
            );
        } catch (ModelNotFoundException $e) {
            // Manejar la excepción de la base de datos
            return $this->errorResponse(
                [
                    'message' => $this->getMessage('ModelNotFoundException'),
                    'error' => $e->getMessage()
                ],
                500
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
            // Devolver una respuesta de error en caso de excepción
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
                Correria::withTrashed()->findOrFail($id),
                'La correria fue encontrado exitosamente.',
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

    public function update(CorreriaUpdateRequest $request, $id)
    {
        try {
            $correria = Correria::withTrashed()->findOrFail($id);
            $correria->name = $request->input('name');
            $correria->code = $request->input('code');
            $correria->start_date = Carbon::parse($request->input('start_date'))->startOfDay();
            $correria->end_date = Carbon::parse($request->input('end_date'))->endOfDay();
            $correria->save();

            DB::statement('CALL correrias(?,?)', [Carbon::now()->format('Y-m-d H:i:s'), Auth::user()->business_id]);

            return $this->successResponse(
                $correria,
                'La correria fue actualizada exitosamente.',
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

    public function delete(CorreriaDeleteRequest $request)
    {
        try {
            DB::statement('CALL correrias(?,?)', [Carbon::now()->format('Y-m-d H:i:s'), Auth::user()->business_id]);

            $deleted_at = Carbon::now()->format('Y-m-d H:i:s');
            $correria = Correria::withTrashed()->findOrFail($request->input('id'));
            $start_date = Carbon::parse($correria->start_date);
            $end_date = Carbon::parse($correria->end_date);

            if ($start_date->lte($deleted_at) && $end_date->gte($deleted_at)) {
                return $this->successResponse(
                    $correria,
                    'La correria no se puede eliminar porque está activa.',
                    422
                );
            }

            $correria->delete();

            return $this->successResponse(
                $correria,
                'La correria fue eliminada exitosamente.',
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
}
