<?php

namespace App\Http\Controllers;

use App\Http\Requests\ModulesAndSubmodules\ModulesAndSubmodulesCreateRequest;
use App\Http\Requests\ModulesAndSubmodules\ModulesAndSubmodulesIndexQueryRequest;
use App\Http\Requests\ModulesAndSubmodules\ModulesAndSubmodulesDeleteRequest;
use App\Http\Requests\ModulesAndSubmodules\ModulesAndSubmodulesEditRequest;
use App\Http\Requests\ModulesAndSubmodules\ModulesAndSubmodulesStoreRequest;
use App\Http\Requests\ModulesAndSubmodules\ModulesAndSubmodulesUpdateRequest;
use App\Http\Resources\ModulesAndSubmodules\ModulesAndSubmodulesIndexQueryCollection;
use App\Models\Module;
use App\Models\Submodule;
use App\Traits\ApiMessage;
use App\Traits\ApiResponser;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Spatie\Permission\Models\Role;

class ModulesAndSubmodulesController extends Controller
{
    use ApiResponser;
    use ApiMessage;

    public function index()
    {
        try {
            return view('Dashboard.ModuleAndSubmodules.Index');
        } catch (Exception $e) {
            return back()->with('danger', 'Ocurrió un error al cargar la vista: ' . $e->getMessage());
        }
    }

    public function indexQuery(ModulesAndSubmodulesIndexQueryRequest $request)
    {
        try{
            $start_date = Carbon::parse($request->input('start_date'))->startOfDay();
            $end_date = Carbon::parse($request->input('end_date'))->endOfDay();
            
            $modulesAndSubmodules = Module::with('roles', 'submodules.permission')
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
                ->orderBy($request->input('column'), $request->input('dir'))
                ->paginate($request->input('perPage'));

            return $this->successResponse(
                new ModulesAndSubmodulesIndexQueryCollection($modulesAndSubmodules),
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

    public function create(ModulesAndSubmodulesCreateRequest $request)
    {
        try {
            if($request->filled('role')) {
                $permissions = Role::with('permissions')->where('name', $request->input('role'))->firstOrFail();

                return $this->successResponse(
                    [
                        'permissions' => $permissions->permissions
                    ],
                    'Los permisos del rol fueron encontrados exitosamente.',
                    200
                );
            }

            $roles = Role::whereDoesntHave('modules')->get();

            return $this->successResponse(
                [
                    'roles' => $roles
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

    public function store(ModulesAndSubmodulesStoreRequest $request)
    {
        try {
            $module = new Module();
            $module->name = $request->input('module');
            $module->icon = $request->input('icon');
            $module->save();

            $module->roles()->sync($request->input('roles'));

            $submodules = collect($request->input('submodules'))->map(function ($submodule) use ($module){
                $submodule = (object) $submodule;
                $submoduleNew = new Submodule();
                $submoduleNew->name = $submodule->submodule;
                $submoduleNew->url = $submodule->url;
                $submoduleNew->icon = $submodule->icon;
                $submoduleNew->permission_id = $submodule->permission_id;
                $submoduleNew->module_id = $module->id;
                $submoduleNew->save();
            });

            return $this->successResponse(
                $module,
                'Modulo y submodulos creados exitosamente.',
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

    public function edit(ModulesAndSubmodulesEditRequest $request, $id)
    {
        try {
            if($request->filled('role')) {
                $permissions = Role::with('permissions')->where('name', $request->input('role'))->firstOrFail();

                return $this->successResponse(
                    [
                        'permissions' => $permissions->permissions
                    ],
                    'El Modulo y submodulos fueron encontrados exitosamente.',
                    204
                );
            }

            $module = Module::with('roles', 'submodules.permission')->findOrFail($id);
            $roles = Role::whereDoesntHave('modules')->get();

            return $this->successResponse(
                [
                    'module' => $module,
                    'roles' => $roles
                ],
                'El Modulo y submodulos fueron encontrados exitosamente.',
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

    public function update(ModulesAndSubmodulesUpdateRequest $request, $id)
    {
        try {
            $module = Module::findOrFail($id);
            $module->name = $request->input('module');
            $module->icon = $request->input('icon');
            $module->save();

            $module->roles()->sync($request->input('roles'));

            $submodules = collect($request->input('submodules'))->map(function ($submodule) use ($module){
                $submodule = (object) $submodule;
                $submoduleNew = isset($submodule->id) ? Submodule::findOrFail($submodule->id) : new Submodule();
                $submoduleNew->name = $submodule->submodule;
                $submoduleNew->url = $submodule->url;
                $submoduleNew->icon = $submodule->icon;
                $submoduleNew->permission_id = $submodule->permission_id;
                $submoduleNew->module_id = $module->id;
                $submoduleNew->save();
                return $submoduleNew->id;
            });

            $module->submodules()->whereNotIn('id', $submodules)->delete();

            return $this->successResponse(
                $module,
                'Modulo y submodulos actualizados exitosamente.',
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

    public function delete(ModulesAndSubmodulesDeleteRequest $request)
    {
        try {
            $module = Module::findOrFail($request->input('id'))->delete();
            
            return $this->successResponse(
                $module,
                'Modulo y submodulos eliminados exitosamente.',
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
