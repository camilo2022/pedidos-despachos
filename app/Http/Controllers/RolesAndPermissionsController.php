<?php
namespace App\Http\Controllers;
use Carbon\Carbon;
use App\Traits\ApiResponser;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use App\Http\Controllers\Controller;
use Spatie\Permission\Models\Permission;
use App\Http\Resources\RolesAndPermissions\RolesAndPermissionsIndexQueryCollection;
use App\Http\Requests\RolesAndPermissions\RolesAndPermissionsIndexQueryRequest;
use App\Http\Requests\RolesAndPermissions\RolesAndPermissionsStoreRequest;
use App\Http\Requests\RolesAndPermissions\RolesAndPermissionsDeleteRequest;
use App\Http\Requests\RolesAndPermissions\RolesAndPermissionsUpdateRequest;
use App\Traits\ApiMessage;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Auth;

class RolesAndPermissionsController extends Controller
{
    use ApiResponser;
    use ApiMessage;

    public function index()
    {
        try {
            return view('Dashboard.RoleAndPermissions.Index');
        } catch (Exception $e) {
            return back()->with('danger', 'Ocurrió un error al cargar la vista: ' . $e->getMessage());
        }
    }

    public function indexQuery(RolesAndPermissionsIndexQueryRequest $request)
    {
        try{
            $start_date = Carbon::parse($request->input('start_date'))->startOfDay();
            $end_date = Carbon::parse($request->input('end_date'))->endOfDay();

            $rolesAndPermissions = Role::with('permissions')
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
                new RolesAndPermissionsIndexQueryCollection($rolesAndPermissions),
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
            return $this->errorResponse(
                [
                    'message' => $this->getMessage('Exception'),
                    'error' => $e->getMessage()
                ],
                500
            );
        }
    }

    public function store(RolesAndPermissionsStoreRequest $request)
    {
        try {
            DB::beginTransaction();
            $role = new Role();
            $role->name = $request->input('role');
            $role->save();
            
            $permissions = collect($request->input('permissions'))->map(function ($permission) {
                return Permission::firstOrCreate([
                    'name' => $permission
                ]);
            });
            
            $role->syncPermissions($permissions);
            
            DB::commit();
            
            return $this->successResponse(
                $role,
                'El rol y sus permisos fueron creados exitosamente.',
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
            DB::rollback();
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
        $roleAndPermissions = Role::with('permissions')->findOrFail($id);
        try {
            return $this->successResponse(
                [
                    'roleAndPermissions' => $roleAndPermissions
                ],
                'El rol y los permisos fueron encontrados exitosamente.',
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

    public function update(RolesAndPermissionsUpdateRequest $request, $roleId)
    {
        try {
            DB::beginTransaction();
            
            $role = Role::findOrFail($roleId);

            $currentPermissions = collect($request->input('permissions'));

            $existingPermissions = $role->permissions->pluck('name');
            
            $newPermissions = $currentPermissions->diff($existingPermissions);
            
            foreach ($newPermissions as $permissionName) {
                $permission = Permission::firstOrCreate([
                    'name' => $permissionName
                ]);
                $currentPermissions->push($permission->name);
            }
            
            $role->syncPermissions($currentPermissions);
            
            $removedPermissions = $existingPermissions->diff($currentPermissions);
            
            foreach ($removedPermissions as $permissionName) {
                $permission = Permission::where('name', $permissionName)->first();
                if ($permission) {
                    $role->revokePermissionTo($permission);
                    if ($permission->roles->isEmpty()) {
                        $permission->delete();
                    }
                }
            }
            
            $role->syncPermissions($currentPermissions);

            $role->name = $request->input('role');
            $role->save();

            DB::commit();
            
            return $this->successResponse(
                $role,
                'El rol y sus permisos fueron actualizados exitosamente.',
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
            DB::rollback();
            return $this->errorResponse(
                [
                    'message' => $this->getMessage('Exception'),
                    'error' => $e->getMessage()
                ],
                500
            );
        }
    }

    public function delete(RolesAndPermissionsDeleteRequest $request)
    {
        try {
            
            DB::beginTransaction();
            
            Role::whereIn('id', $request->input('role_id'))->delete();
            Permission::whereIn('id', $request->input('permission_id'))->delete();
            
            DB::commit();
            
            return $this->successResponse(
                '',
                'El rol y sus permisos fueron eliminados exitosamente.',
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
            DB::rollback();
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
