<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\InvoiceDetail;
use App\Models\InvoiceDetailPayment;
use App\Models\Libranza;
use App\Models\PaymentMethod;
use App\Models\Person;
use App\Traits\ApiMessage;
use App\Traits\ApiResponser;
use Barryvdh\DomPDF\Facade\Pdf as PDF;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;
use Milon\Barcode\DNS1D;

class InvoiceController extends Controller
{
    use ApiResponser;
    use ApiMessage;

    public function store(Request $request)
    {
        try {
            $consecutive = DB::selectOne('CALL store_consecutive(?)', [Auth::user()->cash_register->store_id])->next_invoice;

            $libranza = null;
            $value_libranza = 0;
            $has_libranza = PaymentMethod::whereIn('id', collect($request->input('payments'))->pluck('payment_method_id'))->where('is_libranza', true)->get();
            if($has_libranza->count() > 0){
                $value_libranza = collect($request->input('payments'))->whereIn('payment_method_id', $has_libranza->pluck('id')->toArray())->sum('payment');
            }

            $invoice = new Invoice();
            $invoice->model_id = $request->input('person_id');
            $invoice->model_type = Person::class;
            $invoice->reference = $consecutive;
            $invoice->status = $has_libranza->count() > 0 ? 'Pendiente' : 'Pagado' ;
            $invoice->user_id = Auth::user()->id;
            $invoice->cash_register_id = Auth::user()->cash_register->id;
            $invoice->save();

            foreach($request->input('invoice_details') as $item){
                $item = (object) $item;
                $invoice_detail = new InvoiceDetail();
                $invoice_detail->invoice_id = $invoice->id;
                $invoice_detail->warehouse_id = $item->warehouse_id;
                $invoice_detail->product_id = $item->product_id;
                $invoice_detail->size_id = $item->size_id;
                $invoice_detail->color_id = $item->color_id;
                $invoice_detail->quantity = $item->quantity;
                $invoice_detail->promotion_id = $item->promotion_id;
                $invoice_detail->price = $item->price;
                $invoice_detail->discount = $item->discount;
                $invoice_detail->subtotal = $item->subtotal;
                $invoice_detail->total = $item->total;
                $invoice_detail->save();

                $value = $item->total;
                foreach($request->input('payments') as &$payment){
                    $payment = (object) $payment;

                    if($payment->payment > 0){
                        $invoice_detail_payment = new InvoiceDetailPayment();
                        $invoice_detail_payment->invoice_detail_id = $invoice_detail->id;
                        $invoice_detail_payment->payment_method_id = $payment->payment_method_id;

                        if($payment->payment >= $value){
                            $invoice_detail_payment->payment = $value;
                            $payment->payment -= $value;
                            $invoice_detail_payment->save();
                            break;
                        } else {
                            $invoice_detail_payment->payment = $payment->payment;
                            $value -= $payment->payment;
                            $payment->payment -= 0;
                            $invoice_detail_payment->save();
                        }
                    }
                }
            }

            if($has_libranza->count() > 0) {
                $libranza = new Libranza();
                $libranza->invoice_id = $invoice->id;
                $libranza->value = $value_libranza;
                $libranza->code = Str::upper(Str::random(8));
                $libranza->sms = $this->sms($libranza->code, $invoice->reference, $value_libranza, $invoice->model->phone_number, $invoice->cash_register->store->name);
                $libranza->user_id = Auth::user()->id;
                $libranza->save();
            }

            return $this->successResponse(
                [
                    'invoice' => $invoice->load('invoice_details.invoice_detail_payments'),
                    'libranza' => $libranza,
                    'url' => URL::route('Dashboard.Invoices.Ticket', ['id' => $invoice->id]),
                ],
                'La factura fue registrada exitosamente.' . is_null($libranza) ? '' : ' La libranza se registró correctamente, ingresar el codigo enviado por SMS al telefono del cliente para cerrar la libranza.',
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

    private function sms($code, $reference, $value, $phone, $store)
    {
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
    }

    public function ticket($id)
    {
        $payment_methods = PaymentMethod::get();
        $invoice = Invoice::with(['model', 'cash_register.user', 'cash_register.store.business', 'invoice_details.invoice_detail_payments.payment_method', 'invoice_details.product', 'invoice_details.size', 'invoice_details.color'])->findOrFail($id);
        $codeBar = DNS1D::getBarcodePNG($invoice->reference, 'C39+', 5, 100);
        $pdf = PDF::loadView('Dashboard.Invoices.Ticket', compact('invoice', 'payment_methods', 'codeBar'));

        $pdf->setPaper([0, 0, 226.772, 550]);

        return $pdf->stream("{$invoice->reference}.pdf");
    }
}
