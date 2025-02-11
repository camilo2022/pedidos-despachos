<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\POS\PersonPOSRequest;
use App\Models\Inventory;
use App\Models\PaymentMethod;
use App\Models\Person;
use App\Traits\ApiMessage;
use App\Traits\ApiResponser;
use Exception;
use Illuminate\Database\QueryException;

class POSController extends Controller
{
    use ApiResponser;
    use ApiMessage;

    public function index()
    {
        try {
            $payment_methods = PaymentMethod::get();

            return view('Dashboard.POS.Index', compact('payment_methods'));
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

    public function product(PersonPOSRequest $request)
    {
        try {
            $inventories = Inventory::with('')
            ->get();
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
}
