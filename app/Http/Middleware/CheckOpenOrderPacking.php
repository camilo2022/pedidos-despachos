<?php

namespace App\Http\Middleware;

use App\Models\OrderPacking;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;

class CheckOpenOrderPacking
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $orderPacking = OrderPacking::where('packing_user_id', Auth::user()->id)
                                    ->where('packing_status', 'En curso')
                                    ->first();

        if ($orderPacking) {
            return Redirect::route('Dashboard.Packings.Index', ['id' => $orderPacking->id]);
        }

        return $next($request);
    }
}
