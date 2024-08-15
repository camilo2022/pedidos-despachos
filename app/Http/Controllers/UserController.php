<?php
namespace App\Http\Controllers;
use Carbon\Carbon;
use App\Models\User;
use App\Traits\ApiResponser;
use App\Http\Controllers\Controller;
use App\Http\Requests\User\UserAssignRoleAndPermissionsQueryRequest;
use App\Http\Requests\User\UserAssignRoleAndPermissionsRequest;
use App\Http\Requests\User\UserAssignWarehousesRequest;
use App\Http\Requests\User\UserRemoveRoleAndPermissionsQueryRequest;
use App\Http\Requests\User\UserRemoveRolesAndPermissionsRequest;
use App\Http\Requests\User\UserIndexQueryRequest;
use App\Http\Resources\User\UserIndexQueryCollection;
use App\Http\Requests\User\UserDeleteRequest;
use App\Http\Requests\User\UserPasswordRequest;
use App\Http\Requests\User\UserRemoveWarehousesRequest;
use App\Http\Requests\User\UserRestoreRequest;
use App\Http\Requests\User\UserStoreRequest;
use App\Http\Requests\User\UserUpdateRequest;
use App\Models\ModelWarehouse;
use App\Models\Warehouse;
use App\Traits\ApiMessage;
use App\Traits\Titles;
use App\Traits\Zones;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    use ApiResponser;
    use ApiMessage;
    use Titles;
    use Zones;

    public function index()
    {
        try {
            return view('Dashboard.Users.Index');
        } catch (Exception $e) {
            return back()->with('danger', 'Ocurrió un error al cargar la vista: ' . $e->getMessage());
        }
    }

    public function indexQuery(UserIndexQueryRequest $request)
    {
        try {
            $start_date = Carbon::parse($request->input('start_date'))->startOfDay();
            $end_date = Carbon::parse($request->input('end_date'))->endOfDay();

            $users = User::with('roles', 'permissions', 'business')
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
                new UserIndexQueryCollection($users),
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
            $titles = $this->titles();
            $zones = $this->zones();

            return $this->successResponse(
                [
                    'titles' => $titles,
                    'zones' => $zones
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

    public function store(UserStoreRequest $request)
    {
        try {
            $user = new User();
            $user->name = $request->input('name');
            $user->last_name = $request->input('last_name');
            $user->document_number = $request->input('document_number');
            $user->phone_number = $request->input('phone_number');
            $user->address = $request->input('address');
            $user->email = $request->input('email');
            $user->password = Hash::make($request->input('password'));
            $user->title = $request->input('title');
            $user->zone = $request->input('zone');
            $user->business_id = Auth::user()->business_id;
            $user->save();

            $user->assignRole(['Dashboard']);
            $user->givePermissionTo('Dashboard');

            return $this->successResponse(
                $user,
                'El usuario fue registrado exitosamente.',
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
            $user = User::findOrFail($id);
            $titles = $this->titles();
            $zones = $this->zones();

            return $this->successResponse(
                [
                    'user' => $user,
                    'titles' => $titles,
                    'zones' => $zones
                ],
                'El usuario fue encontrado exitosamente.',
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

    public function update(UserUpdateRequest $request, $id)
    {
        try {
            $user = User::findOrFail($id);
            $user->name = $request->input('name');
            $user->last_name = $request->input('last_name');
            $user->document_number = $request->input('document_number');
            $user->phone_number = $request->input('phone_number');
            $user->address = $request->input('address');
            $user->email = $request->input('email');
            $user->title = $request->input('title');
            $user->zone = $request->input('zone');
            $user->save();

            return $this->successResponse(
                $user,
                'El usuario fue actualizado exitosamente.',
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

    public function show($id)
    {
        try {
            $user = User::findOrFail($id);

            return $this->successResponse(
                [
                    'user' => $user
                ],
                'El usuario fue encontrado exitosamente.',
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

    public function password(UserPasswordRequest $request, $id)
    {
        try {
            $user = User::findOrFail($id);
            $user->password = Hash::make($request->input('password'));
            $user->save();

            return $this->successResponse(
                $user,
                'La contraseña del usuario fue actualizada exitosamente.',
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

    public function delete(UserDeleteRequest $request)
    {
        try {
            $user = User::findOrFail($request->input('id'))->delete();
            return $this->successResponse(
                $user,
                'El usuario fue eliminado exitosamente.',
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

    public function restore(UserRestoreRequest $request)
    {
        try {
            $user = User::withTrashed()->findOrFail($request->input('id'))->restore();
            return $this->successResponse(
                $user,
                'El usuario fue restaurado exitosamente.',
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

    public function assignRoleAndPermissionsQuery(UserAssignRoleAndPermissionsQueryRequest $request)
    {
        try {
            $user = User::findOrFail($request->input('id'));
            $roles = Role::with('permissions')->get();

            $rolesWithMissingPermissions = [];

            foreach ($roles as $role) {
                $missingPermissions = [];
                foreach (collect($role->permissions)->pluck('name') as $permission) {
                    if (!$user->hasRole($role->name) || !$user->hasDirectPermission($permission)) {
                        $missingPermissions[] = $permission;
                    }
                }
                if (!empty($missingPermissions)) {
                    $rolesWithMissingPermissions[] = (object) [
                        'role' => $role->name,
                        'permissions' => $missingPermissions
                    ];
                }
            }

            return $this->successResponse(
                $rolesWithMissingPermissions,
                'La consulta para asignar rol y permisos realizada exitosamente.',
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

    public function assignRoleAndPermissions(UserAssignRoleAndPermissionsRequest $request)
    {
        try {
            $role = Role::where('name', $request->input('role'))->firstOrFail();
            $user = User::findOrFail($request->input('id'));

            if (!$user->hasRole($request->input('role'))) {
                $user->assignRole([$role]);
            }

            $user->givePermissionTo($request->input('permissions'));
            return $this->successResponse(
                $user,
                'El rol y los permisos fueron asignados al usuario exitosamente.',
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

    public function removeRoleAndPermissionsQuery(UserRemoveRoleAndPermissionsQueryRequest $request)
    {
        try {
            $user = User::with('roles.permissions','permissions')->findOrFail($request->input('id'));

            $rolesWithMissingPermissions = [];

            foreach($user->roles as $role){
                $missingPermissions = [];
                foreach (collect($role->permissions)->pluck('name') as $permission) {
                    if ($user->hasDirectPermission($permission)) {
                        $missingPermissions[] = $permission;
                    }
                }
                if (!empty($missingPermissions)) {
                    $rolesWithMissingPermissions[] = (object) [
                        'role' => $role->name,
                        'permissions' => $missingPermissions
                    ];
                }
            }

            return $this->successResponse(
                $rolesWithMissingPermissions,
                'La consulta para remover rol y permisos realizada exitosamente.',
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

    public function removeRoleAndPermissions(UserRemoveRolesAndPermissionsRequest $request)
    {
        try {
            $role = Role::with('permissions')->where('name', $request->input('role'))->firstOrFail();
            $user = User::findOrFail($request->input('id'));

            $user->revokePermissionTo($request->input('permissions'));

            $missingPermissions = [];

            foreach (collect($role->permissions)->pluck('name') as $permission) {
                if ($user->hasDirectPermission($permission)) {
                    $missingPermissions[] = $permission;
                }
            }

            if(empty($missingPermissions)) {
                $user->removeRole($request->input('role'));
            }

            return $this->successResponse(
                $user,
                'El rol y los permisos fueron removidos al usuario exitosamente.',
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

    public function warehouses($id)
    {
        try {
            $user = User::with('warehouses')->findOrFail($id);
            $warehouses = Warehouse::with('users', 'businesses')->whereHas('businesses', fn($query) => $query->where('businesses.id', Auth::user()->business_id))
            ->when(in_array($user->title, ['VENDEDOR']),
                function ($query) {
                    $query->where('to_discount', true);
                }
            )
            ->when(in_array($user->title, ['VENDEDOR ESPECIAL']),
                function ($query) {
                    $query->where('to_exclusive', true);
                }
            )
            ->get();

            foreach ($warehouses as $warehouse) {
                $usersId = $warehouse->users->pluck('id')->all();
                $warehouse->admin = in_array($id, $usersId);
            }

            return $this->successResponse(
                [
                    'user' => $user,
                    'warehouses' => $warehouses
                ],
                'El usuario fue encontrado exitosamente.',
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

    public function assignWarehouses(UserAssignWarehousesRequest $request)
    {
        try {
            $modelWarehouse = new ModelWarehouse();
            $modelWarehouse->model_type = User::class;
            $modelWarehouse->model_id = $request->input('user_id');
            $modelWarehouse->warehouse_id = $request->input('warehouse_id');
            $modelWarehouse->save();

            return $this->successResponse(
                $modelWarehouse,
                'La bodega fue asignada al usuario exitosamente.',
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

    public function removeWarehouses(UserRemoveWarehousesRequest $request)
    {
        try {
            $modelWarehouse = ModelWarehouse::whereHasMorph('model', [User::class], fn($query) => $query->where('model_id', $request->input('user_id')))
            ->where('warehouse_id', $request->input('warehouse_id'))->delete();

            return $this->successResponse(
                $modelWarehouse,
                'La bodega fue removida al usuario exitosamente.',
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
