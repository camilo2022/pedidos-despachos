<?php

namespace App\Http\Middleware;

use App\Traits\ApiResponser;
use Closure;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;

class SpatieCheckPermission
{
    use ApiResponser;

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next, $permission)
    {
        if (!auth()->user()->hasDirectPermission($permission)) {
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
            $redirectCount = session('redirect_count', 0);

            if ($redirectCount < 5) {
                session(['redirect_count' => $redirectCount + 1]);
                return back()->with('danger', $message);
            } else if($redirectCount >= 5) {
                session(['redirect_count' => 0]);
                return redirect('/Dashboard');
            }

            throw new AuthorizationException();
        }
        session(['redirect_count' => 0]);
        return $next($request);
    }
}
