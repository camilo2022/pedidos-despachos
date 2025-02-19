<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\Libranza\LibranzaConfirmRequest;
use App\Models\Libranza;
use App\Traits\ApiMessage;
use App\Traits\ApiResponser;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\URL;

class LibranzaController extends Controller
{
    use ApiResponser;
    use ApiMessage;

    public function confirm(LibranzaConfirmRequest $request, $id)
    {
        try {
            $libranza = Libranza::with('invoice')->findOrFail($id);
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
                'La libranza fue confirmada exitosamente.',
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
}
