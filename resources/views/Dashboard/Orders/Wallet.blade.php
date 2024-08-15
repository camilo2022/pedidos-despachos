<!doctype html>
<html class="no-js " lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=Edge">
        <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
        <meta name="description" content="Responsive Bootstrap 4 and web Application ui kit.">
        <title>RECORDATORIO DE PAGO DE DEUDA EN MORA</title>
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
            p {
                font-size: 13px;
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
                        <th width="15%" colspan="2" class="center">
                            <img width="90%" src="{{ asset('images/logo-bless.jpg') }}">
                        </th>
                        <th width="70%" colspan="4" class="center">
                            {{ $order->business->name }} <br>
                            {{ $order->business->number_document }} <br>
                            {{ $order->business->address }} <br>
                            {{ $order->business->city }} - {{ $order->business->departament }}
                        </th>
                        <th width="15%" colspan="2" class="center">
                            @php(Carbon::setLocale('es'))
                            {{ Carbon::now()->isoFormat('D [de] MMMM [de] YYYY') }}
                            {{ Carbon::now()->format('h:i:s A') }}
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <th width="13%">SEÑORES:</th>
                        <td width="37%" colspan="3">{{ strtoupper($order->client->client_name) }}</td>
                        <th width="13%">DIRECCION:</th>
                        <td width="37%" colspan="3">{{ strtoupper($order->client->client_address) }}</td>
                    </tr>
                    <tr>
                        <th>UBICACION:</th>
                        <td colspan="3">{{ strtoupper($order->client->city) }} - {{ strtoupper($order->client->departament) }}</td>
                        <th>DOCUMENTO:</th>
                        <td colspan="3">{{ strtoupper($order->client->client_number_document) }}</td>
                    </tr>
                    <tr>
                        <th>TELEFONO:</th>
                        <td colspan="3">{{ $order->client->client_number_phone }} - {{ $order->client->client_branch_number_phone }} - {{ $order->client->number_phone }}</td>
                        <th>CORREO:</th>
                        <td colspan="3">{{ strtoupper($order->client->email) }}</td>
                    </tr>
                    <tr>
                        <td colspan="8">
                            <p>Estimado Sr. {{ ucwords(strtolower($order->client->client_name)) }},</p>
                            <p>Asunto: Notificación de Pago Pendiente</p>
                            <p>Nos dirigimos a usted con el fin de informarle que, según nuestros registros, presenta un saldo pendiente en su cartera. A continuación, se detallan la deuda y la distribución de las edades de mora:</p>
                        </td>
                    </tr>
                    <tr>
                        <th width="50%" colspan="4">EDAD DE MORA</th>
                        <th width="50%" colspan="4">MONTO DE DEUDA EN MORA</th>
                    </tr>
                    @if($order->client->wallet->one_to_thirty > 0)
                        <tr>
                            <td colspan="4">1 a 30 días</td>
                            <td colspan="4">{{ '$ ' . number_format($order->client->wallet->one_to_thirty, 0, ',', '.') }}</td>
                        </tr>
                    @endif
                    @if($order->client->wallet->thirty_one_to_sixty > 0)
                        <tr>
                            <td colspan="4">31 a 60 días</td>
                            <td colspan="4">{{ '$ ' . number_format($order->client->wallet->thirty_one_to_sixty, 0, ',', '.') }}</td>
                        </tr>
                    @endif
                    @if($order->client->wallet->sixty_one_to_ninety > 0)
                        <tr>
                            <td colspan="4">61 a 90 días</td>
                            <td colspan="4">{{ '$ ' . number_format($order->client->wallet->sixty_one_to_ninety, 0, ',', '.') }}</td>
                        </tr>
                    @endif
                    @if($order->client->wallet->ninety_one_to_one_hundred_twenty > 0)
                        <tr>
                            <td colspan="4">91 a 120 días</td>
                            <td colspan="4">{{ '$ ' . number_format($order->client->wallet->ninety_one_to_one_hundred_twenty, 0, ',', '.') }}</td>
                        </tr>
                    @endif
                    @if($order->client->wallet->one_hundred_twenty_one_to_one_hundred_fifty > 0)
                        <tr>
                            <td colspan="4">121 a 150 días</td>
                            <td colspan="4">{{ '$ ' . number_format($order->client->wallet->one_hundred_twenty_one_to_one_hundred_fifty, 0, ',', '.') }}</td>
                        </tr>
                    @endif
                    @if($order->client->wallet->one_hundred_fifty_one_to_one_hundred_eighty_one > 0)
                        <tr>
                            <td colspan="4">151 a 180 días</td>
                            <td colspan="4">{{ '$ ' . number_format($order->client->wallet->one_hundred_fifty_one_to_one_hundred_eighty_one, 0, ',', '.') }}</td>
                        </tr>
                    @endif
                    @if($order->client->wallet->eldest_to_one_hundred_eighty_one > 0)
                        <tr>
                            <td colspan="4">Mayor a 181 días</td>
                            <td colspan="4">{{ '$ ' . number_format($order->client->wallet->eldest_to_one_hundred_eighty_one, 0, ',', '.') }}</td>
                        </tr>
                    @endif
                    <tr>
                        <th colspan="4">MONTO TOTAL EN MORA:</th>
                        <th colspan="4">{{ '$ ' . number_format($order->client->wallet->total - $order->client->wallet->zero_to_thirty, 0, ',', '.') }}</th>
                    </tr>
                    <tr>
                        <td colspan="8">
                            <p>{!! nl2br(e($order->business->order_footer)) !!}</p>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </body>
</html>