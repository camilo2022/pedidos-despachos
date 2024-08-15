<?php

namespace App\Http\Middleware;

use App\Models\OrderPicking;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;

class CheckOpenOrderPicking
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
        $orderPicking = OrderPicking::where('picking_user_id', Auth::user()->id)
                                    ->where('picking_status', 'En curso')
                                    ->first();

        if ($orderPicking) {
            return Redirect::route('Dashboard.Pickings.Index', ['id' => $orderPicking->id]);
        }

        return $next($request);
    }
}
