<!doctype html>
<html class="no-js " lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=Edge">
        <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
        <meta name="description" content="Responsive Bootstrap 4 and web Application ui kit.">
        <title>REPORTE CONSOLIDADOR PRODUCTOS</title>
        <link rel="icon" href="" type="image/x-icon"> <!-- Favicon-->

        <!-- CSRF Token -->
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <!-- Tempusdominus Bbootstrap 4 -->
        <link rel="stylesheet" href="{{ asset('css/plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css') }}">
        <!-- SweetAlert2 -->
        <link rel="stylesheet" href="{{ asset('css/plugins/sweetalert2-theme-bootstrap-4/bootstrap-4.min.css') }}">
        <!-- Custom Css -->
        <link rel="stylesheet" href="{{ asset('css/plugins/pdf/style.min.css') }}">

        <!-- jQuery -->
        <script src="{{ asset('js/jquery/jquery.min.js') }}"></script>
        <!-- jQuery UI 1.11.4 -->
        <script src="{{ asset('js/jquery-ui/jquery-ui.min.js') }}"></script>
        <!-- ChartJS -->
        <script src="{{ asset('js/chart.js/Chart.min.js') }}"></script>
        <!-- jQuery Knob Chart -->
        <script src="{{ asset('js/jquery-knob/jquery.knob.min.js') }}"></script>
        <!-- FLOT CHARTS -->
        <script src="{{ asset('js/flot/jquery.flot.js') }}"></script>
        <script src="{{ asset('js/flot-old/jquery.flot.resize.min.js') }}"></script>
        <script src="{{ asset('js/flot-old/jquery.flot.pie.min.js') }}"></script>

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
                text-align: center !important; 
                vertical-align: middle !important;
            }
            .fz-14 {
                font-size: 14px !important;
            }
            .fz-16 {
                font-size: 16px !important;
            }
            .fz-20 {
                font-size: 20px !important;
            }
        </style>
    </head>
    @foreach ($collect->where('CANTIDAD', '>', 0) as $item)
    <body>
        <div class="col-lg-12">
            <table>
                <thead>
                    <tr>
                        <th rowspan="2" colspan="2" class="fz-20 center">{{ $item->TRADEMARK }}</th>
                        <th class="fz-16 center">INVENTARIO</th>
                        <th class="fz-16 center">VENTA</th>
                        <th class="fz-16 center">DISPONIBLE</th>
                    </tr>
                    <tr>
                        <th class="fz-14 center">{{ $item->INVENTARIO }}</th>
                        <th class="fz-14 center">{{ $item->VENTA }}</th>
                        <th class="fz-14 center">{{ $item->DISPONIBLE }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($item->REFERENCIAS->where('CANTIDAD', '>', 0) as $subItem)
                    <tr>
                        <td class="fz-14">{{ $subItem->PRODUCT->code }}</td>
                        <td class="fz-14">{{ $subItem->COLOR->name }} - {{ $subItem->COLOR->code }}</td>
                        <td class="fz-14 center">{{ $subItem->INVENTARIO }}</td>
                        <td class="fz-14 center">{{ $subItem->VENTA }}</td>
                        <td class="fz-14 center">{{ $subItem->DISPONIBLE }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </body>
    @endforeach
</html>