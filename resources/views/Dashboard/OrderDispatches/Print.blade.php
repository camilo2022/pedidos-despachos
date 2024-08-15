<!doctype html>
<html class="no-js " lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=Edge">
        <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
        <meta name="description" content="Responsive Bootstrap 4 and web Application ui kit.">
        <title>ORDEN N° {{ $orderDispatch->consecutive }}</title>
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
                font-size: 14px;
            }
            .bg-gray {
                background-color: #d4d4d4;
            }
        </style>
    </head>
    <body>
        <div class="col-lg-12">
            <table class="table">
                <thead>
                    <tr>
                        <th colspan="4" class="cell">
                            INFORMACION ORDEN DE DESPACHO
                        </th>
                    </tr>
                    <tr>
                        <th class="cell" rowspan="2">
                            <img width="100px" height="auto" src="{{ asset('images/logo-bless.jpg') }}">
                        </th>
                        <th class="cell" rowspan="2" style="font-size: 15px;">{{ $orderDispatch->client->client_name }}</th>
                        <th class="cell bg-gray" colspan="2" style="font-size: 15px;">ORDEN DE DESPACHO N°</th>
                    </tr>
                    <tr>
                        <th class="cell bg-gray" colspan="2" style="font-size: 18px;">{{ $orderDispatch->consecutive }}</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <th style="text-align: left;" class="cell">NIT:</th>
                        <td style="text-align: left;" class="cell">{{ $orderDispatch->client->client_number_document . '-' . $orderDispatch->client->client_branch_code }}</td>
                        <th style="text-align: left;" class="cell">TELEFONO:</th>
                        <td style="text-align: left;" class="cell">{{ ($orderDispatch->client->client_number_phone ?? $orderDispatch->client->client_branch_number_phone) ?? $orderDispatch->client->number_phone }}</td>
                    </tr>
                    <tr>
                        <th style="text-align: left;" class="cell">CIUDAD:</th>
                        <td style="text-align: left;" class="cell">{{ $orderDispatch->client->city }}</td>
                        <th style="text-align: left;" class="cell">CORREO:</th>
                        <td style="text-align: left;" class="cell">{{ $orderDispatch->client->email }}</td>
                    </tr>
                    <tr>
                        <th style="text-align: left;" class="cell">DEPARTAMENTO:</th>
                        <td style="text-align: left;" class="cell">{{ $orderDispatch->client->departament }}</td>
                        <th style="text-align: left;" class="cell">DIRECCION:</th>
                        <td style="text-align: left;" class="cell">{{ $orderDispatch->client->client_branch_address }}</td>
                    </tr>
                </tbody>
            </table>
            <table style="padding-top: 1cm;" class="table">
                <thead>
                    <tr>
                        <th colspan="{{ $orderDispatchSizes->count() + 4 }}" class="cell">
                            DETALLES DE LA ORDEN DE DESPACHO
                        </th>
                    </tr>
                    <tr>
                        {{-- <th class="cell bg-gray">VENDEDOR</th> --}}
                        <th class="cell bg-gray">PEDIDO - ID</th>
                        <th class="cell bg-gray">REFERENCIA</th>
                        <th class="cell bg-gray">COLOR</th>
                        @foreach ($orderDispatchSizes as $size)
                        <th class="cell bg-gray">{{ $size->code }}</th>
                        @endforeach
                        <th class="cell bg-gray">TOTAL</th>
                    </tr>
                </thead>
                <tbody>
                    @php($quantitiesTotal = 0)
                    @foreach ($orderDispatch->order_dispatch_details as $order_dispatch_detail)
                        @php($quantities = 0)
                        <tr>
                            {{-- <td style="font-size: 12px;" class="cell">{{ strtoupper($order_dispatch_detail->order->seller_user->name . ' ' . $order_dispatch_detail->order->seller_user->last_name) }}</td> --}}
                            <td class="cell">{{ $order_dispatch_detail->order->id . ' - ' .$order_dispatch_detail->order_detail->id }}</td>
                            <td class="cell">{{ $order_dispatch_detail->order_detail->product->code }}</td>
                            <td class="cell">{{ $order_dispatch_detail->order_detail->color->name . ' - ' . $order_dispatch_detail->order_detail->color->code }}</td>
                            @foreach ($orderDispatchSizes as $size)
                            <td class="cell">{{ $order_dispatch_detail->{"T$size->code"} }} @php($quantities += $order_dispatch_detail->{"T{$size->code}"}) </td>
                            @endforeach
                            <td class="cell bg-gray">{{ $quantities }}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <th colspan="3" class="cell">TOTAL DE CANTIDADES POR TALLA</th>
                        @foreach ($orderDispatchSizes as $size)
                        <th class="cell bg-gray">{{ $orderDispatch->order_dispatch_details->pluck("T{$size->code}")->sum() }} @php($quantitiesTotal += $orderDispatch->order_dispatch_details->pluck("T{$size->code}")->sum()) </th>
                        @endforeach
                        <th class="cell bg-gray">{{ $quantitiesTotal }}</th>
                    </tr>
                </tfoot>
            </table>
        <table style="padding-top: 1cm;" class="table">
                <thead>
                    <tr>
                        <th colspan="6" class="cell">
                            INFORMACION DE LOS PEDIDOS DE LA ORDEN DE DESPACHO
                        </th>
                    </tr>
                    <tr>
                        <th class="cell bg-gray" width="7%">PEDIDO</th>
                        <th class="cell bg-gray" width="27%">VENDEDOR</th>
                        <th class="cell bg-gray" width="15%">FECHA PEDIDO</th>
                        <th class="cell bg-gray" width="37%">OBSERVACION</th>
                        <th class="cell bg-gray" width="7%">OFC</th>
                        <th class="cell bg-gray" width="7%">DCO</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($orderDispatch->order_dispatch_details->pluck('order')->unique() as $order)
                        <tr>
                            <td style="font-size: 12px;" class="cell">{{ $order->id }}</td>
                            <td style="font-size: 12px;" class="cell">{{ strtoupper($order->seller_user->name . ' ' . $order->seller_user->last_name) }}</td>
                            <td style="font-size: 12px;" class="cell">{{ $order->seller_date }}</td>
                            <td style="font-size: 12px;" class="cell">{{ $order->seller_observation }}</td>
                            <td style="font-size: 12px;" class="cell">{{ ($order_dispatch_detail->order->wallet_dispatch_official ?? $order_dispatch_detail->order->seller_dispatch_official) . ' %' }}</td>
                            <td style="font-size: 12px;" class="cell">{{ ($order_dispatch_detail->order->wallet_dispatch_document ?? $order_dispatch_detail->order->seller_dispatch_document) . ' %' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

    </body>
</html>
