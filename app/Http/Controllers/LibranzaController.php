<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\Libranza\LibranzaApproveRequest;
use App\Models\Libranza;
use App\Models\LibranzaDiscount;
use App\Traits\ApiMessage;
use App\Traits\ApiResponser;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\URL;

class LibranzaController extends Controller
{
    use ApiResponser;
    use ApiMessage;

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

    public function show($id)
    {
        try {
            $libranza = Libranza::with('libranza_discounts')->findOrFail($id);

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

    public function payment(/* LibranzaCancelRequest */ $request)
    {
        try {
            $libranza_discount = new LibranzaDiscount();
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

    public function cancel(/* LibranzaCancelRequest */ $request)
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
