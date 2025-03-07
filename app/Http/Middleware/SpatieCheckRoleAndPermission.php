<?php

namespace App\Http\Middleware;

use App\Traits\ApiResponser;
use Closure;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SpatieCheckRoleAndPermission
{
    use ApiResponser;

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next, $role, $permission)
    {
        if (!Auth::user()->hasRole($role)) {
            $message = "No está autorizado para realizar esta acción. Falta el rol: $role.
            Contacte al administrador para obtener asistencia o solicitar autorización.";
            if($request->ajax()) {
                return $this->errorResponse(
                    [
                        'message' => $message
                    ],
                    403
                );
            }

            return redirect('/Dashboard')->with('danger', $message);

            throw new AuthorizationException();
        }

        if (!Auth::user()->hasDirectPermission($permission)) {
            $message = "No está autorizado para realizar esta acción. Falta el permiso: $permission.
            Contacte al administrador para obtener asistencia o solicitar autorización.";
            if($request->ajax()) {
                return $this->errorResponse(
                    [
                        'message' => $message
                    ],
                    403
                );
            }

            return redirect('/Dashboard')->with('danger', $message);

            throw new AuthorizationException();
        }

        return $next($request);
    }
}
