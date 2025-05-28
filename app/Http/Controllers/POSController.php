<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\POS\PersonPOSRequest;
use App\Http\Requests\POS\ProductPOSRequest;
use App\Models\Inventory;
use App\Models\PaymentMethod;
use App\Models\Person;
use App\Models\Promotion;
use App\Traits\ApiMessage;
use App\Traits\ApiResponser;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Auth;

class POSController extends Controller
{
    use ApiResponser;
    use ApiMessage;

    public function index()
    {
        try {
            if(!Auth::user()->cash_register){
                return back()->with('danger', 'Para acceder al POS, debe tener una caja asignada. Por favor, solicite la asignación de una caja antes de continuar.');
            }

            if(Auth::user()->cash_register->status == 'Activa' and Auth::user()->cash_register->store and Auth::user()->cash_register->store->status == 'Abierta'){
                if(Auth::user()->cash_register->cash_register_controls->where('status', 'Abierta')->where('date', '<>', Carbon::now()->format('Y-m-d'))->first()){
                    return back()->with('warning', 'Para acceder al POS, debe realizar los cierres de caja pendientes. Por favor, complete los cierres antes de continuar.');
                } else if(!Auth::user()->cash_register->cash_register_controls->where('status', 'Abierta')->where('date', Carbon::now()->format('Y-m-d'))->first()) {
                    return back()->with('info', 'Para acceder al POS, es necesario realizar una apertura de caja. Por favor, asegúrese de abrir una caja antes de continuar.');
                }
            }

            $payment_methods = PaymentMethod::get();
            $promotions = Promotion::get();

            return view('Dashboard.POS.Index', compact('payment_methods', 'promotions'))/*->with('success', "Acceso al POS exitoso. Su caja es {Auth::user()->cash_register->name} de la tienda {Auth::user()->cash_register->store->name}.")*/;
        } catch (Exception $e) {
            return back()->with('danger', 'Ocurrió un error al cargar la vista: ' . $e->getMessage());
        }
    }

    public function person(PersonPOSRequest $request)
    {
        try {
            $people = Person::with('employee', 'invoices')
                ->when($request->input('search'),
                    function ($query) use ($request) {
                        $query->where('number_document', 'LIKE', '%' . $request->input('value') . '%');
                    }
                )
                ->when(!$request->input('search'),
                    function ($query) use ($request) {
                        $query->where('name', 'LIKE', '%' . $request->input('value') . '%')
                        ->orWhere('last_name', 'LIKE', '%' . $request->input('value') . '%')
                        ->orWhere('number_document', 'LIKE', '%' . $request->input('value') . '%')
                        ->orWhere('phone_number', 'LIKE', '%' . $request->input('value') . '%')
                        ->orWhere('email', 'LIKE', '%' . $request->input('value') . '%')
                        ->orWhere('address', 'LIKE', '%' . $request->input('value') . '%');
                    }
                )
                ->get();

            return $this->successResponse(
                $people,
                $people->count() > 0 ? 'Coincidencias encontradas exitosamente.' : 'Ninguna coincidencia encontrada.',
                $people->count() > 0 ? 200 : 204
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

    public function product(ProductPOSRequest $request)
    {
        try {
            $inventories = Inventory::with('warehouse', 'product', 'size', 'color')
                ->where('warehouse_id', Auth::user()->stores->first()?->warehouses->first()?->id)
                ->where('system', 'TIENDA')
                ->where('quantity', '>', 0)
                ->whereHas('product', function ($query) use ($request) {
                    $query->when($request->input('search'),
                        function ($query) use ($request) {
                            $query->whereRaw("CONCAT(code, '-', (SELECT code FROM sizes WHERE sizes.id = inventories.size_id), '-', (SELECT code FROM colors WHERE colors.id = inventories.color_id)) LIKE ?", [$request->input('value')]);
                        }
                    )
                    ->when(!$request->input('search'),
                        function ($query) use ($request) {
                            $query->whereRaw("CONCAT(code, '-', (SELECT code FROM sizes WHERE sizes.id = inventories.size_id), '-', (SELECT code FROM colors WHERE colors.id = inventories.color_id)) LIKE ?", ['%' . $request->input('value') . '%']);
                        }
                    );
                })
                ->get();

            return $this->successResponse(
                $inventories,
                $inventories->count() > 0 ? 'Coincidencias encontradas exitosamente.' : 'Ninguna coincidencia encontrada.',
                $inventories->count() > 0 ? 200 : 204
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
}
