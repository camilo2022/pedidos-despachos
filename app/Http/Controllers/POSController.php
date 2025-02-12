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
            /*return Auth::user()->stores->first()->warehouses;*/
            $payment_methods = PaymentMethod::get();
            $promotions = Promotion::get();

            return view('Dashboard.POS.Index', compact('payment_methods', 'promotions'));
        } catch (Exception $e) {
            return $e->getMessage();
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
