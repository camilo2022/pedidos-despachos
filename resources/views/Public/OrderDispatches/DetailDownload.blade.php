<!doctype html>
<html class="no-js " lang="en">


<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=Edge">
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <meta name="description" content="Responsive Bootstrap 4 and web Application ui kit.">
    <title>{{ $orderPackage->order_packing->order_dispatch->consecutive }} - {{ $orderPackage->package_type->name }}</title>
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
<body>
    <div class="col-lg-12">
        <table>
            <thead>
                <tr>
                    <th class="text-center" width="20%">   
                        <img src="{{ asset('images/logo-bless.jpg') }}">
                    </th>
                    <th colspan="4" width="60%" class="center">
                        {{ $orderPackage->order_packing->order_dispatch->business->name }} <br>
                        {{ $orderPackage->order_packing->order_dispatch->business->number_document }} <br>
                        {{ $orderPackage->order_packing->order_dispatch->business->address }} <br>
                        {{ $orderPackage->order_packing->order_dispatch->business->city }} - {{ $orderPackage->order_packing->order_dispatch->business->departament }}
                    </th>
                    <th class="center" width="20%">
                        @php(Carbon::setLocale('es'))
                        {{ Carbon::now()->isoFormat('D [de] MMMM [de] YYYY') }}
                        {{ Carbon::now()->format('h:i:s A') }}
                    </th>
                </tr>
                <tr>
                    <th colspan="6" class="center" style="font-size: 11px;">
                        INFORMACION DETALLADA DEL CONTENIDO DEL PAQUETE.
                    </th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <th>DESTINATARIO: </th>
                    <td colspan="3">{{ strtoupper($orderPackage->order_packing->order_dispatch->client->client_name) }}</td>
                    <th>NIT: </th>
                    <td>{{ $orderPackage->order_packing->order_dispatch->client->client_number_document }}-{{ $orderPackage->order_packing->order_dispatch->client->client_branch_code }}</td>
                </tr>
                <tr>
                    <th>DIRECCION: </th>
                    <td colspan="3">{{ strtoupper($orderPackage->order_packing->order_dispatch->client->client_branch_address) }}</td>
                    <th>TELEFONO: </th>
                    <td>{{ $orderPackage->order_packing->order_dispatch->client->client_number_phone ?? $orderPackage->order_packing->order_dispatch->client->client_branch_number_phone }}</td>
                </tr>
                <tr>
                    <th>DEPARTAMENTO: </th>
                    <td colspan="3">{{ strtoupper($orderPackage->order_packing->order_dispatch->client->departament) }}</td>
                    <th>CIUDAD: </th>
                    <td>{{ $orderPackage->order_packing->order_dispatch->client->city }}</td>
                </tr>
                <tr>
                    <th>DESPACHADO POR: </th>
                    <td colspan="3">{{ strtoupper($orderPackage->order_packing->order_dispatch->dispatch_user->name . ' ' . $orderPackage->order_packing->order_dispatch->dispatch_user->last_name) }}</td>
                    <th>FECHA DESPACHO:</th>
                    <td>{{ Carbon::parse($orderPackage->order_packing->order_dispatch->dispatch_date)->format('Y-m-d H:i:s') }}</td>
                </tr>
                <tr>
                    <th>FACTURADO POR: </th>
                    <td colspan="3">{{ strtoupper($orderPackage->order_packing->order_dispatch->invoice_user->name . ' ' . $orderPackage->order_packing->order_dispatch->invoice_user->last_name) }}</td>
                    <th>N° DESPACHO:</th>
                    <td>{{ $orderPackage->order_packing->order_dispatch->consecutive }}</td>
                </tr>
                <tr>
                    <th>REVISADO POR: </th>
                    <td colspan="3">{{ strtoupper($orderPackage->order_packing->packing_user->name . ' ' . $orderPackage->order_packing->packing_user->last_name) }}</td>
                    <th>TIPO EMPAQUE:</th>
                    <td>{{ $orderPackage->package_type->name }}</td>
                </tr>
                <tr>
                    <th colspan="6" class="center" style="font-size: 11px;">
                        {{ $orderPackage->order_packing->order_dispatch->business->packing_footer }}
                    </th>
                </tr>
            </tbody>
        </table>
        <table style="padding-top:1cm !important;">
            <thead>
                <tr>
                    <th colspan="{{ $orderPackageSizes->count() + 3 }}" class="center">DETALLES DEL EMPAQUE ({{ $orderPackage->package_type->name }})</th>
                </tr>
                <tr>
                    <th class="center">REFERENCIA</th>
                    <th class="center">COLOR</th>
                    @foreach ($orderPackageSizes as $size)
                    <th class="center">{{ "T{$size->code}" }}</th>
                    @endforeach
                    <th class="center">TOTAL</th>
                </tr>
            </thead>
            <tbody>
                @php($quantites = 0)
                @foreach ($orderPackage->order_packing_details as $order_packing_detail)
                    @php($quantity = 0)
                    <td class="center">{{ $order_packing_detail->order_dispatch_detail->order_detail->product->code }}</td>
                    <td class="center">{{ $order_packing_detail->order_dispatch_detail->order_detail->color->name }} - {{ $order_packing_detail->order_dispatch_detail->order_detail->color->code }}</td>
                    @foreach ($orderPackageSizes as $size)
                    <td class="center">{{ $order_packing_detail->{"T{$size->code}"} }}</td>
                    @php($quantity += $order_packing_detail->{"T{$size->code}"})
                    @endforeach
                    <td class="center">{{ $quantity }}</td>
                    @php($quantites += $quantity)
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <th colspan="2" class="center">TOTAL</th>
                    @foreach ($orderPackageSizes as $size)
                    <th class="center">{{ $orderPackage->order_packing_details->sum("T{$size->code}") }}</th>
                    @endforeach
                    <th class="center">{{ $quantites }}</th>
                </tr>
            </tfoot>
        </table>
    </div>
</body>
</html>