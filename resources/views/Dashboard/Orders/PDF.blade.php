<!doctype html>
<html class="no-js " lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=Edge">
        <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
        <meta name="description" content="Responsive Bootstrap 4 and web Application ui kit.">
        <title>PEDIDO N° {{ $order->id }}</title>
        <link rel="icon" href="" type="image/x-icon"> <!-- Favicon-->

        <!-- CSRF Token -->
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <!-- Tempusdominus Bbootstrap 4 -->
        <link rel="stylesheet" href="{{ asset('css/plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css') }}">
        <!-- SweetAlert2 -->
        <link rel="stylesheet" href="{{ asset('css/plugins/sweetalert2-theme-bootstrap-4/bootstrap-4.min.css') }}">
        <!-- Custom Css -->
        <link rel="stylesheet" href="{{ asset('css/plugins/pdf/style.min.css') }}">

        <style>
            .table {
                width: 100% !important;
                border-collapse: collapse;
            }
            .cell {
                border: 1px solid #a7a7a7;
                padding: 8px;
                text-align: center;
            }
            .fz-12 {
                font-size: 12px;
            }
            .fz-10 {
                font-size: 10px;
            }
            body {
                background-image: url("{{ asset('images/membrete.jpg') }}");
                background-repeat: no-repeat;
                background-size: cover;
                margin: 0;
                padding: 0;
            }
            html{
                background-repeat: no-repeat;
                background-attachment: fixed;
                background-position: center center;
                background-size: 100%;
                margin: 0px;
            }
        </style>
    </head>
    <body style="padding-top:4cm !important; padding-bottom:2cm !important;">
        <div style="position: absolute; top: 1.3cm; right: 1cm; left: 13cm; text-align: center; color: white; z-index: 9999;">
            <b>PEDIDO N° {{ $order->id }}</b>
        </div>
        <table style="padding-left:1cm !important; padding-right:1cm !important;" class="table">
            <thead>
                <tr>
                    <th colspan="6" class="cell fz-12">
                        INFORMACION DESPACHO DEL PEDIDO
                    </th>
                </tr>
                <tr>
                    <th style="text-align: left;" class="cell fz-12">
                        SEÑORES:
                    </th>
                    <td style="text-align: left;" class="cell fz-12">
                        {{ strtoupper($order->client->client_name) }}
                    </td>
                    <th style="text-align: left;" class="cell fz-12">
                        DOCUMENTO:
                    </th>
                    <td style="text-align: left;" class="cell fz-12" colspan="3">
                        {{ $order->client->client_number_document . '-' . $order->client->client_branch_code }}
                    </td>
                </tr>
                <tr>
                    <th style="text-align: left;" class="cell fz-12">
                        UBICACION:
                    </th>
                    <td style="text-align: left;" class="cell fz-12">
                        {{ strtoupper($order->client->departament . ' - ' . $order->client->city) }}
                    </td>
                    <th style="text-align: left;" class="cell fz-12">
                        DIRECCION:
                    </th>
                    <td style="text-align: left;" class="cell fz-12" colspan="3">
                        {{ strtoupper($order->client->client_branch_address) }}
                    </td>
                </tr>
                <tr>
                    <th style="text-align: left;" class="cell fz-12">
                        TELEFONOS:
                    </th>
                    <td style="text-align: left;" class="cell fz-12">
                        {{ $order->client->client_number_phone . ' - ' . $order->client->client_branch_number_phone . ' - ' . $order->client->number_phone }}
                    </td>
                    <th style="text-align: left;" class="cell fz-12">
                        CORREO:
                    </th>
                    <td style="text-align: left;" class="cell fz-12" colspan="3">
                        {{ strtoupper($order->client->email) }}
                    </td>
                </tr>
                <tr>
                    <th style="text-align: left;" class="cell fz-12">
                        VENDEDOR:
                    </th>
                    <td style="text-align: left;" class="cell fz-12">
                        {{ strtoupper($order->seller_user->name . ' ' . $order->seller_user->last_name) }}
                    </td>
                    <th style="text-align: left;" class="cell fz-12">
                        FECHA:
                    </th>
                    <td style="text-align: left;" class="cell fz-12" colspan="3">
                        {{ $order->seller_date }}
                    </td>
                </tr>
                <tr>
                    <th style="text-align: left;" class="cell fz-12">
                        DESPACHO:
                    </th>
                    <td style="text-align: left;" class="cell fz-12">
                        {{  strtoupper(in_array($order->dispatch_type, ['Antes de', 'Despues de']) ? $order->dispatch_type . ' ' . $order->dispatch_date : $order->dispatch_type) }}
                    </td>
                    <th style="text-align: left;" class="cell fz-12">
                        OFC:
                    </th>
                    <td style="text-align: left;" class="cell fz-12">
                        {{ ($order->wallet_dispatch_official ?? $order->seller_dispatch_official) . ' %' }}
                    </td>
                    <th style="text-align: left;" class="cell fz-12">
                        DCO:
                    </th>
                    <td style="text-align: left;" class="cell fz-12">
                        {{ ($order->wallet_dispatch_document ?? $order->seller_dispatch_document) . ' %' }}
                    </td>
                </tr>
                <tr>
                    <th style="text-align: left;" class="cell fz-12">
                        OBSERVACION:
                    </th>
                    <td style="text-align: left;" class="cell fz-12" colspan="5">
                        {{ strtoupper($order->seller_observation) }}
                    </td>
                </tr>
            </thead>
        </table>
        <table style="padding-top:1cm !important; padding-left:1cm !important; padding-right:1cm !important;" class="table">
            <thead>
                <tr>
                    <th colspan="{{ $orderSizes->count() + 6 }}" class="cell fz-12">
                        DETALLES DEL PEDIDO
                    </th>
                </tr>
                <tr>
                    <th style="background-color: #d4d4d4;" class="cell fz-12">REF.</th>
                    <th style="background-color: #d4d4d4;" class="cell fz-12">COLOR</th>
                    @foreach ($orderSizes as $size)
                    <th style="background-color: #d4d4d4;" class="cell fz-12">{{ "T{$size->code}" }}</th>
                    @endforeach
                    <th style="background-color: #d4d4d4;" class="cell fz-12">TOT</th>
                    <th style="background-color: #d4d4d4;" class="cell fz-12">P. UNIT.</th>
                    <th style="background-color: #d4d4d4;" class="cell fz-12">P. TOT.</th>
                    <th style="background-color: #d4d4d4;" class="cell fz-12">ESTADO</th>
                </tr>
            </thead>
            <tbody>
                @php($priceTotal = 0)
                @php($quantitiesTotal = 0)
                @foreach ($order->order_details as $order_detail)
                    @php($price = 0)
                    @php($quantities = 0)
                    <tr>
                        <td class="cell fz-10">
                            <a href="https://catalogo.orgbless.com/{{ $order_detail->product->code }}">{{ $order_detail->product->code }}</a>
                        </td>
                        <td class="cell fz-10">
                            {{ $order_detail->color->name . ' - ' . $order_detail->color->code }}
                        </td>

                        @foreach ($orderSizes as $size)
                        <td class="cell fz-12">
                            {{ $order_detail->{"T{$size->code}"} }} @php($quantities += !in_array($order_detail->status, ['Suspendido', 'Agotado', 'Cancelado']) ? $order_detail->{"T{$size->code}"} : 0)
                        </td>
                        @endforeach

                        @php($price = !in_array($order_detail->status, ['Suspendido', 'Agotado', 'Cancelado']) ? $quantities * $order_detail->negotiated_price : 0)
                        @php($priceTotal += !in_array($order_detail->status, ['Suspendido', 'Agotado', 'Cancelado']) ? $quantities * $order_detail->negotiated_price : 0)

                        <th style="background-color: #d4d4d4;" class="cell fz-12">
                            {{ $quantities }}
                        </th>
                        <th style="background-color: #d4d4d4;" class="cell fz-10">
                            {{ number_format($order_detail->negotiated_price, 0, ',', '.') }}
                        </th>
                        <th style="background-color: #d4d4d4;" class="cell fz-10">
                            {{ number_format($price, 0, ',', '.') }}
                        </th>
                        @switch($order_detail->status)
                            @case('Pendiente')
                                <th class="bg-success cell fz-10">Pedido</th>
                                @break
                            @case('Cancelado')
                                <th class="bg-danger cell fz-10">Cancelado</th>
                                @break
                            @case('Aprobado')
                                <th class="bg-success cell fz-10">Pedido</th>
                                @break
                            @case('Autorizado')
                                <th class="bg-success cell fz-10">Pedido</th>
                                @break
                            @case('Agotado')
                                <th class="bg-warning cell fz-10">Agotado</th>
                                @break
                            @case('Suspendido')
                                <th class="bg-info cell fz-10">Suspendido</th>
                                @break
                            @case('Comprometido')
                                <th class="bg-success cell fz-10">Pedido</th>
                                @break
                            @case('Despachado')
                                <th class="bg-primary cell fz-10">Despachado</th>
                                @break
                            @default
                                <th class="bg-success cell fz-10">Pedido</th>
                        @endswitch
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <th colspan="2" class="cell fz-10">TOTAL</th>
                    @foreach ($orderSizes as $size)
                    <td class="cell fz-12">{{ $order->order_details->whereNotIn('status', ['Suspendido', 'Agotado', 'Cancelado'])->pluck("T{$size->code}")->sum() }} @php($quantitiesTotal += $order->order_details->whereNotIn('status', ['Suspendido', 'Agotado', 'Cancelado'])->pluck("T{$size->code}")->sum()) </td>
                    @endforeach
                    <th style="background-color: #d4d4d4;" class="cell fz-12">{{ $quantitiesTotal }}</th>
                    <th style="background-color: #d4d4d4;" class="cell fz-10" colspan="3">{{ '$ ' . number_format(($priceTotal), 0, ',', '.') }}</th>
                </tr>
                <tr>
                    @php($formatter = new NumeroALetras())
                    @php($formatter->conector = 'Y')
                    @php($number = $formatter->toMoney($priceTotal, 0, 'PESOS'))
                    <th colspan="{{ $orderSizes->count() + 6 }}" style="text-align: left; background-color: #d4d4d4;" class="cell fz-10">{{ "SON: (EN LETRAS) {$number}" }}</th>
                </tr>
            </tfoot>
        </table>
        <div style="padding-left:1cm !important; padding-right:1cm !important; font-size: 14px;">
            <p>Nota: Puede darle click a la referencia para ver las fotos del producto que encargaste.</p>
        </div>
        <div style="padding-left:1cm !important; padding-right:1cm !important; font-size: 14px;">
            <p>{!! nl2br(e($order->business->order_footer)) !!}</p>
        </div>
    </body>
</html>