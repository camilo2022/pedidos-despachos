<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\Libranza\LibranzaApproveRequest;
use App\Http\Requests\Libranza\LibranzaCancelRequest;
use App\Http\Requests\Libranza\LibranzaDiscountRequest;
use App\Http\Requests\Libranza\LibranzaIndexQueryRequest;
use App\Http\Requests\Libranza\LibranzaStoreRequest;
use App\Http\Resources\Libranza\LibranzaIndexQueryCollection;
use App\Models\Invoice;
use App\Models\Libranza;
use App\Models\LibranzaDiscount;
use App\Models\Person;
use App\Models\User;
use App\Traits\ApiMessage;
use App\Traits\ApiResponser;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;

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
                'libranza_discounts', 'invoice.model', 'invoice.cash_register.store',
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

    public function create()
    {
        try {
            $people = Person::with('invoices', 'employee')->has('employee')->get();

            return $this->successResponse(
                [
                    'people' => $people
                ],
                'Ingrese los datos para hacer la validacion y registro.',
                200
            );
        } catch (ModelNotFoundException $e) {
            return $this->errorResponse(
                [
                    'message' => $this->getMessage('ModelNotFoundException'),
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

    public function store(LibranzaStoreRequest $request)
    {
        try {
            $invoice = new Invoice();
            $invoice->model_id = $request->input('person_id');
            $invoice->model_type = Person::class;
            $invoice->reference = $request->input('reference');
            $invoice->save();

            $libranza = new Libranza();
            $libranza->invoice_id = $invoice->id;
            $libranza->value = $request->input('value');
            $libranza->code = Str::upper(Str::random(8));
            $libranza->sms = $this->sms($libranza->code, $invoice->reference, $request->input('value'), $invoice->model->phone_number, Auth::user()->stores->first()?->name ?? 'SISTEMAS');
            $libranza->store_id = Auth::user()->stores->first()?->id;
            $libranza->user_id = Auth::user()->id;
            $libranza->save();

            return $this->successResponse(
                $libranza,
                'El pedido fue registrado exitosamente.',
                201
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
            $libranza = Libranza::with('invoice.invoice_details')->findOrFail($request->input('id'));
            $libranza->share = $request->input('share');
            $libranza->status = 'Aprobado';
            $libranza->invoice->status = 'Pagado';
            $libranza->save();
            $libranza->invoice->save();

            return $this->successResponse(
                [
                    'libranza' => $libranza,
                    'url' => $libranza->invoice->invoice_details ? URL::route('Dashboard.Invoices.Ticket', ['id' => $libranza->invoice->id]) : null,
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

    public function discount(LibranzaDiscountRequest $request)
    {
        try {
            $libranza_discount = new LibranzaDiscount();
            $libranza_discount->libranza_id = $request->input('id');
            $libranza_discount->value = $request->input('value');
            $libranza_discount->user_id = Auth::user()->id;
            $libranza_discount->save();

            $libranza = Libranza::with('libranza_discounts')->findOrFail($request->input('id'));

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

    public function audit($id)
    {
        try {
            $libranza = Libranza::findOrFail($id);

            $relations = [
                'invoice_id' => [Invoice::class, ['reference', 'status', 'created_at']],
                'user_id' => [User::class, ['name', 'last_name', 'title']]
            ];

            $audits = $libranza->audits()->with('user')->get()->map(function ($audit) use ($relations) {
                $old_values = $audit->old_values;
                $new_values = $audit->new_values;

                foreach (['old_values', 'new_values'] as $valueType) {
                    foreach ($relations as $key => [$model, $fields]) {
                        if (isset($$valueType[$key])) {
                            $$valueType[str_replace('_id', '', $key)] = $model::select($fields)->find($$valueType[$key]);
                        }
                    }
                }

                return [
                    'id' => $audit->id,
                    'user_type' => $audit->user_type,
                    'user_id' => $audit->user_id,
                    'event' => $audit->event,
                    'auditable_type' => $audit->auditable_type,
                    'auditable_id' => $audit->auditable_id,
                    'old_values' => $old_values,
                    'new_values' => $new_values,
                    'url' => $audit->url,
                    'ip_address' => $audit->ip_address,
                    'user_agent' => $audit->user_agent,
                    'tags' => $audit->tags,
                    'created_at' => $audit->created_at,
                    'updated_at' => $audit->updated_at,
                    'user' => $audit->user,
                ];
            });

            return $this->successResponse(
                [
                    'libranza' => $libranza,
                    'audits' => $audits
                ],
                'El pedido fue encontrado exitosamente.',
                200
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

    private function sms($code, $reference, $value, $phone, $store)
    {
        try {
            return 'MTczOTY4MTAzNA==|xVxHlrsNKLRUEpwJOS05yiWhM';
            $url = "https://api103.hablame.co/api/sms/v3/send/priority";

            $response = Http::withHeaders([
                'Accept' => 'application/json',
                'Account' => env('SMS_ACCOUNT'),
                'ApiKey' => env('SMS_API_KEY'),
                'Content-Type' => 'application/json',
                'Token' => env('SMS_TOKEN'),
            ])->post($url, [
                'toNumber' => "57$phone",
                'sms' => "$store Libranza Monto: $ $value. Este es tu código de firma de libranza $code de la factura $reference.",
                'flash' => '0',
                'sc' => '899991',
                'request_dlvr_rcpt' => '0',
            ]);

            $data = (object) $response->json();

            if (isset($data->status) && $data->status == '1x000') {
                return $data->smsId;
            } else {
                return 'Ha ocurrido un error: ' . ($data->error_description ?? 'Desconocido') . ' (' . ($data->status ?? 'Sin código') . ')';
            }
        } catch (Exception $e) {
            return 'Ha ocurrido un error: Desconocido (Sin código)';
        }
    }
}
