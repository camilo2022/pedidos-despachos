<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\Libranza\LibranzaApproveRequest;
use App\Http\Requests\Libranza\LibranzaCancelRequest;
use App\Http\Requests\Libranza\LibranzaDiscountRequest;
use App\Http\Requests\Libranza\LibranzaIndexQueryRequest;
use App\Http\Resources\Libranza\LibranzaIndexQueryCollection;
use App\Models\Libranza;
use App\Models\LibranzaDiscount;
use App\Traits\ApiMessage;
use App\Traits\ApiResponser;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;

class LibranzaController extends Controller
{
    use ApiResponser;
    use ApiMessage;

    public function index()
    {
        try {
            return view('Dashboard.Libranzas.Index');
        } catch (Exception $e) {
            return back()->with('danger', 'Ocurrió un error al cargar la vista: ' . $e->getMessage());
        }
    }

    public function indexQuery(LibranzaIndexQueryRequest $request)
    {
        try {
            $start_date = Carbon::parse($request->input('start_date'))->startOfDay();
            $end_date = Carbon::parse($request->input('end_date'))->endOfDay();

            $libranzas = Libranza::with([
                'libranza_discounts', 'invoice',
                'user' => fn($query) => $query->withTrashed()
            ])
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
            ->when(!in_array(Auth::user()->title, ['SUPER ADMINISTRADOR', 'ADMINISTRADOR']),
                function ($query) {
                    $query->where('user_id', Auth::user()->id);
                }
            )
            ->orderBy($request->input('column'), $request->input('dir'))
            ->paginate($request->input('perPage'));

            $values = Libranza::whereIn('status', ['Aprobado', 'Proceso', 'Pagado'])
                ->select([
                    DB::raw('COALESCE(SUM(value), 0) as total'),
                    DB::raw('(SELECT COALESCE(SUM(libranza_discounts.value), 0) FROM libranza_discounts WHERE libranza_discounts.libranza_id IN (SELECT id FROM libranzas WHERE status IN ("Aprobado", "Proceso", "Pagado"))) as pay'),
                    DB::raw('COALESCE(SUM(value), 0) - (SELECT COALESCE(SUM(libranza_discounts.value), 0) FROM libranza_discounts WHERE libranza_discounts.libranza_id IN (SELECT id FROM libranzas WHERE status IN ("Aprobado", "Proceso", "Pagado"))) as debt')
                ])
                ->first();

            return $this->successResponse(
                [
                    'values' => $values,
                    'libranzas' => new LibranzaIndexQueryCollection($libranzas)
                ],
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

    public function show($id)
    {
        try {
            $libranza = Libranza::with([
                'libranza_discounts', 'invoice',
                'user' => fn($query) => $query->withTrashed()
            ])
            ->findOrFail($id);

            return $this->successResponse(
                $libranza,
                'La libranza fue encontrada exitosamente.',
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

    public function approve(LibranzaApproveRequest $request)
    {
        try {
            $libranza = Libranza::with('invoice')->findOrFail($request->input('id'));
            $libranza->share = $request->input('share');
            $libranza->status = 'Aprobado';
            $libranza->invoice->status = 'Pagado';
            $libranza->save();
            $libranza->invoice->save();

            return $this->successResponse(
                [
                    'libranza' => $libranza,
                    'url' => URL::route('Dashboard.Invoices.Ticket', ['id' => $libranza->invoice->id]),
                ],
                'La libranza fue aprobada exitosamente.',
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

    public function discount(LibranzaDiscountRequest $request)
    {
        try {
            $libranza = Libranza::with('libranza_discounts')->findOrFail($request->input('id'));

            $libranza_discount = new LibranzaDiscount();
            $libranza_discount->libranza_id = $libranza->id;
            $libranza_discount->value = $request->input('value');
            $libranza_discount->user_id = Auth::user()->id;
            $libranza_discount->save();

            if($libranza->value == $libranza->libranza_discounts->sum('value')){
                $libranza->status = 'Pagado';
            } else {
                $libranza->status = 'Proceso';
            }

            $libranza->save();

            return $this->successResponse(
                $libranza,
                'El descuento de Libranza fue aplicado exitosamente exitosamente.',
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

    public function cancel(LibranzaCancelRequest $request)
    {
        try {
            $libranza = Libranza::with('invoice')->findOrFail($request->input('id'));
            $libranza->status = 'Cancelado';
            $libranza->invoice->status = 'Anulado';
            $libranza->save();
            $libranza->invoice->save();

            return $this->successResponse(
                $libranza,
                'La libranza fue cancelada exitosamente.',
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
}
