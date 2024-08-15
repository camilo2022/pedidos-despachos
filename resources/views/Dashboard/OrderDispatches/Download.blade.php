<!doctype html>
<html class="no-js " lang="en">


<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=Edge">
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <meta name="description" content="Responsive Bootstrap 4 and web Application ui kit.">
    <title>ROTULO N° {{ $orderDispatch->consecutive }}</title>
    <link rel="icon" href="" type="image/x-icon"> <!-- Favicon-->

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- Tempusdominus Bbootstrap 4 -->
    <link rel="stylesheet" href="{{ asset('css/plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css') }}">
    <!-- SweetAlert2 -->
    <link rel="stylesheet" href="{{ asset('css/plugins/sweetalert2-theme-bootstrap-4/bootstrap-4.min.css') }}">
    <!-- Custom Css -->
    <link rel="stylesheet" href="{{ asset('css/plugins/pdf/style.min.css') }}">
    
    <link rel="stylesheet" href="{{ asset('css/plugins/overlayScrollbars/css/OverlayScrollbars.min.css') }}">

    <style>
        table {
            width: 100% !important;
            border-collapse: collapse;
        }
        th, td {
            border: 1px solid #a7a7a7;
            text-align: left;
            padding: 8px;
            font-size: 12px;
        }
        .center {
            text-align: center; 
            vertical-align: middle;
        }
    </style>
</head>
    @foreach($orderDispatch->order_packing->order_packages as $index => $orderPackage)
        <body>
            <div class="col-lg-12">
                <table>
                    <thead>
                        <tr>
                            <th class="text-center" width="20%">   
                                <img src="{{ asset('images/logo-bless.jpg') }}">
                            </th>
                            <th colspan="4" width="60%" class="center">
                                {{ $orderDispatch->business->name }} <br>
                                {{ $orderDispatch->business->number_document }} <br>
                                {{ $orderDispatch->business->address }} <br>
                                {{ $orderDispatch->business->city }} - {{ $orderDispatch->business->departament }}
                            </th>
                            <th class="text-center" width="20%" class="center">
                                <img src="data:image/png;base64,{{ base64_encode($orderPackage->qrCode) }}">
                            </th>
                        </tr>
                        <tr>
                            <th colspan="6" class="center" style="font-size: 11px;">
                                ¡IMPORTANTE! Bajo este rotulo se encuentra su factura fisica. ABRIR CON CUIDADO.
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <th>FACTURAS:</th>
                            <td colspan="3">
                                {{ implode(' | ', $orderDispatch->invoices->pluck('reference')->toArray()) }}
                            </td>
                            <th>FECHA:</th>
                            <td>{{ Carbon::parse($orderDispatch->dispatch_date)->format('Y-m-d H:i:s') }}</td>
                        </tr>
                        <tr>
                            <th>EMPAQUE ({{ $orderPackage->package_type->name }}):</th>
                            <td class="text-center">{{ $index + 1}}</td>
                            <th class="text-center">DE</th>
                            <td class="text-center">{{ $orderDispatch->order_packing->order_packages->count() }}</td>
                            <th class="text-center">PESO - PRENDAS: </th>
                            @php($quantities = 0)
                            @foreach ($orderPackage->order_packing_details as $order_packing_detail)
                                @foreach ($sizes as $size)
                                    @php($quantities += $order_packing_detail->{"T{$size->code}"})
                                @endforeach
                            @endforeach
                            <td class="text-center">{{ $orderPackage->weight }} - {{ $quantities }} UNDS</td>
                        </tr>
                        <tr>
                            <th>DESTINATARIO: </th>
                            <td colspan="3">{{ strtoupper($orderDispatch->client->client_name) }}</td>
                            <th>NIT: </th>
                            <td>{{ $orderDispatch->client->client_number_document }}-{{ $orderDispatch->client->client_branch_code }}</td>
                        </tr>
                        <tr>
                            <th>DIRECCION: </th>
                            <td colspan="3">{{ strtoupper($orderDispatch->client->client_branch_address) }}</td>
                            <th>TELEFONO: </th>
                            <td>{{ $orderDispatch->client->client_number_phone ?? $orderDispatch->client->client_branch_number_phone }}</td>
                        </tr>
                        <tr>
                            <th>DEPARTAMENTO: </th>
                            <td colspan="3">{{ strtoupper($orderDispatch->client->departament) }}</td>
                            <th>CIUDAD: </th>
                            <td>{{ $orderDispatch->client->city }}</td>
                        </tr>
                        <tr>
                            <th>DESPACHADO POR: </th>
                            <td colspan="3">{{ strtoupper($orderDispatch->dispatch_user->name . ' ' . $orderDispatch->dispatch_user->last_name) }}</td>
                            <th>N° DESPACHO:</th>
                            <td>{{ $orderDispatch->consecutive }}</td>
                        </tr>
                        <tr>
                            <th>FACTURADO POR: </th>
                            <td colspan="3">{{ strtoupper($orderDispatch->invoice_user->name . ' ' . $orderDispatch->invoice_user->last_name) }}</td>
                            <td colspan="2" rowspan="2"><b>PEDIDOS:</b> <span style="font-size: 14px;">{{ implode(' | ', $orderDispatch->order_dispatch_details->pluck('order_detail')->pluck('order_id')->toArray()) }}</span></td>
                        </tr>
                        <tr>
                            <th>REVISADO POR: </th>
                            <td colspan="3">{{ strtoupper($orderDispatch->order_packing->packing_user->name . ' ' . $orderDispatch->order_packing->packing_user->last_name) }}</td>
                        </tr>
                        <tr>
                            <th colspan="6" class="center" style="font-size: 11px;">
                                {{ $orderDispatch->business->packing_footer }}
                            </th>
                        </tr>
                        <tr>
                            <th colspan="6" class="center">
                                <img style="width:90%;" src="{{asset('images/dian.png')}}"> 
                            </th> 
                        </tr>
                        <tr>
                            <th colspan="6" class="center">www.organizacionbless.com.co</th>
                        </tr>
                    </tbody>
                </table>
            </div>
        </body>
    @endforeach

</html>