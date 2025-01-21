<?php
namespace App\Services;

use App\Http\Requests\User\UserAssignRoleAndPermissionsQueryRequest;
use App\Http\Requests\User\UserAssignRoleAndPermissionsRequest;
use App\Http\Requests\User\UserDeleteRequest;
use App\Http\Requests\User\UserIndexQueryRequest;
use App\Http\Requests\User\UserPasswordRequest;
use App\Http\Requests\User\UserRemoveRoleAndPermissionsQueryRequest;
use App\Http\Requests\User\UserRemoveRolesAndPermissionsRequest;
use App\Http\Requests\User\UserRestoreRequest;
use App\Http\Requests\User\UserStoreRequest;
use App\Http\Requests\User\UserUpdateRequest;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserService
{
    private $user;

    public function __construct()
    {
        $this->user = new User();
    }

    public function getAllUsers(UserIndexQueryRequest $request)
    {
        return $this->user->with('roles', 'permissions', 'business')
        ->when($request->filled('search'),
            function ($query) use ($request) {
                $query->search($request->input('search'));
            }
        )
        ->withTrashed()
        ->when(Auth::user()->title !== 'SUPER ADMINISTRADOR', function ($query) {
            $query->where('business_id', Auth::user()->business_id);
        })
        ->orderBy($request->input('column'), $request->input('dir'))
        ->paginate($request->input('perPage'));
    }

    public function getUserById($id)
    {
        $this->user = User::with('roles.permissions','permissions')->withTrashed()->findOrFail($id);
        return $this->user;
    }

    public function createUser(UserStoreRequest $request)
    {
        $this->user->name = $request->input('name');
        $this->user->last_name = $request->input('last_name');
        $this->user->document_number = $request->input('document_number');
        $this->user->phone_number = $request->input('phone_number');
        $this->user->address = $request->input('address');
        $this->user->email = $request->input('email');
        $this->user->password = Hash::make($request->input('password'));
        $this->user->title = $request->input('title');
        $this->user->zone = $request->input('zone');
        $this->user->business_id = Auth::user()->business_id;
        $this->user->save();

        return $this->user;
    }

    public function updateUser(UserUpdateRequest $request, $id)
    {
        $this->user = $this->getUserById($id);
        $this->user->name = $request->input('name');
        $this->user->last_name = $request->input('last_name');
        $this->user->document_number = $request->input('document_number');
        $this->user->phone_number = $request->input('phone_number');
        $this->user->address = $request->input('address');
        $this->user->email = $request->input('email');
        $this->user->title = $request->input('title');
        $this->user->zone = $request->input('zone');
        $this->user->save();

        return $this->user;
    }

    public function passwordUser(UserPasswordRequest $request, $id)
    {
        $this->user = $this->getUserById($id);
        $this->user->password = Hash::make($request->input('password'));
        $this->user->save();

        return $this->user;
    }

    public function deleteUser(UserDeleteRequest $request)
    {
        return $this->getUserById($request->input('id'))->delete();
    }

    public function restoreUser(UserRestoreRequest $request)
    {
        return $this->getUserById($request->input('id'))->restore();
    }

    public function getRolesWithMissingPermissionsByUser(UserAssignRoleAndPermissionsQueryRequest $request)
    {

    }

    public function assignRoleAndPermissions(UserAssignRoleAndPermissionsRequest $request)
    {

    }

    public function getRolesWithAssignedPermissionsByUser(UserRemoveRoleAndPermissionsQueryRequest $request)
    {

    }

    public function removeRoleAndPermissions(UserRemoveRolesAndPermissionsRequest $request)
    {

    }

}
