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
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class InvoiceController extends Controller
{
    use ApiResponser;
    use ApiMessage;

    public function store(Request $request)
    {
        $consecutive = DB::selectOne('CALL store_consecutive(?)', [Auth::user()->cash_register->store_id])->next_invoice;

        $value_libranza = 0;
        $has_libranza = PaymentMethod::whereIn('id', collect($request->input('payments'))->pluck('payment_method_id'))->where('is_libranza', true)->first();
        if($has_libranza){
            $value_libranza = collect($request->input('payments'))->where('payment_method_id', $has_libranza->id)->sum('payment');
        }

        $invoice = new Invoice();
        $invoice->model_id = $request->input('person_id');
        $invoice->model_type = Person::class;
        $invoice->reference = $consecutive;
        $invoice->status = $has_libranza ? 'Pendiente' : 'Aprobado' ;
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

        if($has_libranza) {
            $libranza = new Libranza();
        }

        return Invoice::with('invoice_details.invoice_detail_payments')->findOrFail($invoice->id);
    }
}
