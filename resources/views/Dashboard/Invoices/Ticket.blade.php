<!DOCTYPE html>
<html lang="es">
<head>
    <link rel="icon" href="{{ asset('images/favicon.png') }}" type="image/x-icon">
    <meta charset="UTF-8">
    <title>{{ $invoice->reference }}</title>
    <style>
        html{
            background-repeat: no-repeat;
            background-attachment: fixed;
            background-position: center center;
            background-size: 100%;
            margin: 0px;
        }
        body {
            font-family: 'Courier New', Courier, monospace;
            font-size: 12px;
            width: 81mm;
            margin: 0;
            padding: 0;
            text-align: center;
        }
        .ticket {
            max-width: 81mm;
            padding: 10px;
            margin: 0 auto;
        }
        .header, .footer {
            text-align: center;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 12px;
        }
        th, td {
            padding: 2px 0;
        }
        td {
            font-family: 'Courier New', Courier, monospace;
            white-space: pre;
            text-align: center !important;
        }
        .barcode {
            text-align: center;
            margin-top: 5px;
        }
        hr {
            border: none;
            border-top: 1px dashed black;
            margin: 5px 0;
        }
        h2 {
            font-size: 35px;
            font-weight: bold;
            font-family: Arial, sans-serif;
            letter-spacing: 13px;
            text-transform: uppercase;
            padding-top: 0;
            margin-top: 0;
            padding-bottom: 0;
            margin-bottom: 0;
        }
    </style>
</head>
<body>
    <div class="ticket">
        <div class="header">
            <h2 class="text-center w-100">{{ strtoupper($invoice->cash_register->store->name) }}</h2>
            <p>{{ strtoupper($invoice->cash_register->store->business->name) }}</p>
            <p>NIT: {{ $invoice->cash_register->store->document_number }}</p>
            <p>TELEFONO: {{ $invoice->cash_register->store->phone_number }}</p>
            <p>{{ strtoupper($invoice->cash_register->store->address) }}</p>
            <p><strong>No se aceptan cambios ni devoluciones</strong></p>
            <hr>
        </div>

        <p><strong>No. Ticket:</strong> {{ strtoupper($invoice->reference) }} - <strong># Prod:</strong>{{ $invoice->invoice_details->sum('quantity') }}</p>
        <p><strong>Fecha:</strong> {{ Carbon::parse($invoice->created_at)->format('d/m/Y h:i A') }}</p>
        <p><strong>Caja:</strong> {{ strtoupper($invoice->cash_register->name) }} - <strong>Cajero:</strong> {{ strtoupper($invoice->cash_register->user->name) }}</p>
        <p><strong>Cliente:</strong> {{ strtoupper($invoice->model->name . ' ' . $invoice->model->last_name) }}</p>
        <p><strong>Doc:</strong> {{ $invoice->model->number_document }} - <strong>Tel:</strong>{{ $invoice->model->phone_number }}</p>
        <p><strong>Correo:</strong> {{ $invoice->model->email }}</p>
        <hr>
        <p><strong>Detalles de la compra</strong></p>
        <table>
            <thead>
                <tr>
                    <th width="40%">Cod</th>
                    <th width="10%">Can</th>
                    <th width="20%">Val</th>
                    <th width="30%">Tot</th>
                </tr>
            </thead>
            <tbody>
                @foreach($invoice->invoice_details as $detail)
                    <tr>
                        <td>{{ strtoupper($detail->product->code.($detail->size?'-'.$detail->size->code:'').($detail->color?'-'.$detail->color->code:'')) }}</td>
                        <td>{{ $detail->quantity }}</td>
                        <td>{{ number_format($detail->price, 0) }}</td>
                        <td>{{ number_format($detail->total, 0) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <hr>
        <p><strong>Subtotal:</strong> {{ number_format($invoice->invoice_details->sum('total'), 0) }}</p>
        <p><strong>Descuento:</strong> {{ number_format($invoice->invoice_details->sum('discount'), 0) }}</p>
        <p><strong>Total a Pagar:</strong> {{ number_format($invoice->invoice_details->sum('total'), 0) }}</p>
        <hr>
        @foreach ($payment_methods as $payment_method)
            @if ($invoice->invoice_details->pluck('invoice_detail_payments')->flatten()->where('payment_method_id', $payment_method->id)->sum('payment') > 0)
                <p><strong>{{ $payment_method->settings->name }}:</strong> {{ number_format($invoice->invoice_details->pluck('invoice_detail_payments')->flatten()->where('payment_method_id', $payment_method->id)->sum('payment'), 0) }}</p>
            @endif
        @endforeach
        <hr>
        @if(!is_null($invoice->libranza))
        <p><strong>Detalles de Libranza</strong></p>
        <table>
            <thead>
                <tr>
                    <th width="10%">N°</th>
                    <th width="45%">Fecha</th>
                    <th width="45%">Descuento</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($discounts as $discount)
                <tr>
                    <td>{{ $discount->number }}</td>
                    <td>{{ $discount->date }}</td>
                    <td>{{ $discount->value }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <hr>
        @endif
        <div class="barcode">
            <p>GRACIAS POR SU COMPRA</p>
            <img src="data:image/png;base64,{{ $codeBar }}" width="150">
            <p>*{{ $invoice->reference }}*</p>
        </div>
    </div>
</body>
</html>
