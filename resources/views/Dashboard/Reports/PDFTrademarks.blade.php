<!doctype html>
<html class="no-js " lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=Edge">
        <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
        <meta name="description" content="Responsive Bootstrap 4 and web Application ui kit.">
        <title>REPORTE MARCAS</title>
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
            p {
                font-size: 13px;
            }
            .center {
                text-align: center !important; 
                vertical-align: middle !important;
            }
            .items thead th:nth-child(1),tbody th:nth-child(1) {
                width: 25%;
            }
            .items thead th:nth-child(2),tbody th:nth-child(2) {
                width: 25%;
            }
            .items thead th:nth-child(3),tbody th:nth-child(3) {
                width: 25%;
            }
            .items thead th:nth-child(4),tbody th:nth-child(4) {
                width: 25%;
            }
            .fz-11 {
                font-size: 11px !important;
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
    @foreach ($trademarks as $trademark => $item)
        <body>
            <div class="col-lg-12">
                <table class="items">
                    <thead>
                        <tr>
                            <th colspan="4" class="center">
                                <label class="fz-20">
                                    {{ collect($item['TRADEMARKS'])->where('DATA.TOTAL', '>', 0)->count() == 0 ? $trademark : implode(', ', array_keys(collect($item['TRADEMARKS'])->where('DATA.TOTAL', '>', 0)->toArray())) }}
                                </label>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <th colspan="4" class="center">
                                <label class="fz-16">NACIONAL</label> | ({{ $item['DATA']['NACIONAL']['TOTAL'] }} UNIDADES)
                            </th>
                        </tr>
                        <tr>
                            <th class="fz-11">AGOTADO:</th>
                            <td class="fz-14">{{ $item['DATA']['NACIONAL']['AGOTADO'] }}</td>
                            <th class="fz-11">PENDIENTE VENDEDOR:</th>
                            <td class="fz-14">{{ $item['DATA']['NACIONAL']['PENDIENTE_VENDEDOR'] }}</td>
                        </tr>
                        <tr>
                            <th class="fz-11">CANCELADO VENDEDOR:</th>
                            <td class="fz-14">{{ $item['DATA']['NACIONAL']['CANCELADO_VENDEDOR'] }}</td>
                            <th class="fz-11">PENDIENTE CARTERA:</th>
                            <td class="fz-14">{{ $item['DATA']['NACIONAL']['PENDIENTE_CARTERA'] }}</td>
                        </tr>
                        <tr>
                            <th class="fz-11">APROBADO CARTERA:</th>
                            <td class="fz-14">{{ $item['DATA']['NACIONAL']['APROBADO_CARTERA'] }}</td>
                            <th class="fz-11">RECHAZADO CARTERA:</th>
                            <td class="fz-14">{{ $item['DATA']['NACIONAL']['RECHAZADO_CARTERA'] }}</td>
                        </tr>
                        <tr>
                            <th class="fz-11">SUSPENDIDO CARTERA:</th>
                            <td class="fz-14">{{ $item['DATA']['NACIONAL']['SUSPENDIDO_CARTERA'] }}</td>
                            <th class="fz-11">EN MORA CARTERA:</th>
                            <td class="fz-14">{{ $item['DATA']['NACIONAL']['EN_MORA_CARTERA'] }}</td>
                        </tr>
                        <tr>
                            <th class="fz-11">PENDIENTE DESPACHO:</th>
                            <td class="fz-14">{{ $item['DATA']['NACIONAL']['PENDIENTE_DESPACHO'] }}</td>
                            <th class="fz-11">DESPACHADO:</th>
                            <td class="fz-14">{{ $item['DATA']['NACIONAL']['DESPACHADO'] }}</td>
                        </tr>
                        <tr>
                            <th colspan="4" class="center">
                                <label class="fz-16">MEDELLIN</label> | ({{ $item['DATA']['MEDELLIN']['TOTAL'] }} UNIDADES)
                            </th>
                        </tr>
                        <tr>
                            <th class="fz-11">AGOTADO:</th>
                            <td class="fz-14">{{ $item['DATA']['MEDELLIN']['AGOTADO'] }}</td>
                            <th class="fz-11">PENDIENTE VENDEDOR:</th>
                            <td class="fz-14">{{ $item['DATA']['MEDELLIN']['PENDIENTE_VENDEDOR'] }}</td>
                        </tr>
                        <tr>
                            <th class="fz-11">CANCELADO VENDEDOR:</th>
                            <td class="fz-14">{{ $item['DATA']['MEDELLIN']['CANCELADO_VENDEDOR'] }}</td>
                            <th class="fz-11">PENDIENTE CARTERA:</th>
                            <td class="fz-14">{{ $item['DATA']['MEDELLIN']['PENDIENTE_CARTERA'] }}</td>
                        </tr>
                        <tr>
                            <th class="fz-11">APROBADO CARTERA:</th>
                            <td class="fz-14">{{ $item['DATA']['MEDELLIN']['APROBADO_CARTERA'] }}</td>
                            <th class="fz-11">RECHAZADO CARTERA:</th>
                            <td class="fz-14">{{ $item['DATA']['MEDELLIN']['RECHAZADO_CARTERA'] }}</td>
                        </tr>
                        <tr>
                            <th class="fz-11">SUSPENDIDO CARTERA:</th>
                            <td class="fz-14">{{ $item['DATA']['MEDELLIN']['SUSPENDIDO_CARTERA'] }}</td>
                            <th class="fz-11">EN MORA CARTERA:</th>
                            <td class="fz-14">{{ $item['DATA']['MEDELLIN']['EN_MORA_CARTERA'] }}</td>
                        </tr>
                        <tr>
                            <th class="fz-11">PENDIENTE DESPACHO:</th>
                            <td class="fz-14">{{ $item['DATA']['MEDELLIN']['PENDIENTE_DESPACHO'] }}</td>
                            <th class="fz-11">DESPACHADO:</th>
                            <td class="fz-14">{{ $item['DATA']['MEDELLIN']['DESPACHADO'] }}</td>
                        </tr>
                        <tr>
                            <th colspan="4" style="width: 100%;">
                                <div class="col-lg-12 center" style="display: flex; justify-content: center; align-items: center;">
                                    <img width="85%" src="{{ asset($item['DATA']['CHART_BAR_DISTRIBUTION']) }}">
                                </div>                                
                            </th>
                        </tr>
                        <tr>
                            <th colspan="2" style="width: 50%;">
                                <div class="col-lg-12 center" style="display: flex; justify-content: center; align-items: center;">
                                    <img width="85%" src="{{ asset($item['DATA']['CHART_PIE_DISTRIBUTION_NACIONAL']) }}">
                                </div>                                
                            </th>
                            <th colspan="2" style="width: 50%;">
                                <div class="col-lg-12 center" style="display: flex; justify-content: center; align-items: center;">
                                    <img width="85%" src="{{ asset($item['DATA']['CHART_PIE_DISTRIBUTION_MEDELLIN']) }}">
                                </div>    
                            </th>
                        </tr>
                        @if(collect($item['TRADEMARKS'])->where('DATA.TOTAL', '>', 0)->count() > 1)
                        <tr>
                            @php($index = 1)
                            <td colspan="4">
                            @foreach (collect($item['TRADEMARKS'])->where('DATA.TOTAL', '>', 0) as $subTrademark => $subItem)
                            <br>
                            <table class="subItems mp-2">
                                <thead>
                                    <tr>
                                        <th width="15%" class="center">
                                            <img width="40%" src="{{ asset($subItem['IMG']) }}">
                                        </th>
                                        <th width="85%" colspan="3" class="center">
                                            <label class="fz-20">{{ $subTrademark }}</label> | ({{ $subItem['DATA']['TOTAL'] }} UNIDADES)
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <th colspan="4" class="center">
                                            <label class="fz-16">NACIONAL</label> | ({{ $subItem['DATA']['NACIONAL']['TOTAL'] }} UNIDADES)
                                        </th>
                                    </tr>
                                    <tr>
                                        <th class="fz-11">AGOTADO:</th>
                                        <td class="fz-14">{{ $subItem['DATA']['NACIONAL']['AGOTADO'] }}</td>
                                        <th class="fz-11">PENDIENTE VENDEDOR:</th>
                                        <td class="fz-14">{{ $subItem['DATA']['NACIONAL']['PENDIENTE_VENDEDOR'] }}</td>
                                    </tr>
                                    <tr>
                                        <th class="fz-11">CANCELADO VENDEDOR:</th>
                                        <td class="fz-14">{{ $subItem['DATA']['NACIONAL']['CANCELADO_VENDEDOR'] }}</td>
                                        <th class="fz-11">PENDIENTE CARTERA:</th>
                                        <td class="fz-14">{{ $subItem['DATA']['NACIONAL']['PENDIENTE_CARTERA'] }}</td>
                                    </tr>
                                    <tr>
                                        <th class="fz-11">APROBADO CARTERA:</th>
                                        <td class="fz-14">{{ $subItem['DATA']['NACIONAL']['APROBADO_CARTERA'] }}</td>
                                        <th class="fz-11">RECHAZADO CARTERA:</th>
                                        <td class="fz-14">{{ $subItem['DATA']['NACIONAL']['RECHAZADO_CARTERA'] }}</td>
                                    </tr>
                                    <tr>
                                        <th class="fz-11">SUSPENDIDO CARTERA:</th>
                                        <td class="fz-14">{{ $subItem['DATA']['NACIONAL']['SUSPENDIDO_CARTERA'] }}</td>
                                        <th class="fz-11">EN MORA CARTERA:</th>
                                        <td class="fz-14">{{ $subItem['DATA']['NACIONAL']['EN_MORA_CARTERA'] }}</td>
                                    </tr>
                                    <tr>
                                        <th class="fz-11">PENDIENTE DESPACHO:</th>
                                        <td class="fz-14">{{ $subItem['DATA']['NACIONAL']['PENDIENTE_DESPACHO'] }}</td>
                                        <th class="fz-11">DESPACHADO:</th>
                                        <td class="fz-14">{{ $subItem['DATA']['NACIONAL']['DESPACHADO'] }}</td>
                                    </tr>
                                    <tr>
                                        <th colspan="4" class="center">
                                            <label class="fz-16">MEDELLIN</label> | ({{ $subItem['DATA']['MEDELLIN']['TOTAL'] }} UNIDADES)
                                        </th>
                                    </tr>
                                    <tr>
                                        <th class="fz-11">AGOTADO:</th>
                                        <td class="fz-14">{{ $subItem['DATA']['MEDELLIN']['AGOTADO'] }}</td>
                                        <th class="fz-11">PENDIENTE VENDEDOR:</th>
                                        <td class="fz-14">{{ $subItem['DATA']['MEDELLIN']['PENDIENTE_VENDEDOR'] }}</td>
                                    </tr>
                                    <tr>
                                        <th class="fz-11">CANCELADO VENDEDOR:</th>
                                        <td class="fz-14">{{ $subItem['DATA']['MEDELLIN']['CANCELADO_VENDEDOR'] }}</td>
                                        <th class="fz-11">PENDIENTE CARTERA:</th>
                                        <td class="fz-14">{{ $subItem['DATA']['MEDELLIN']['PENDIENTE_CARTERA'] }}</td>
                                    </tr>
                                    <tr>
                                        <th class="fz-11">APROBADO CARTERA:</th>
                                        <td class="fz-14">{{ $subItem['DATA']['MEDELLIN']['APROBADO_CARTERA'] }}</td>
                                        <th class="fz-11">RECHAZADO CARTERA:</th>
                                        <td class="fz-14">{{ $subItem['DATA']['MEDELLIN']['RECHAZADO_CARTERA'] }}</td>
                                    </tr>
                                    <tr>
                                        <th class="fz-11">SUSPENDIDO CARTERA:</th>
                                        <td class="fz-14">{{ $subItem['DATA']['MEDELLIN']['SUSPENDIDO_CARTERA'] }}</td>
                                        <th class="fz-11">EN MORA CARTERA:</th>
                                        <td class="fz-14">{{ $subItem['DATA']['MEDELLIN']['EN_MORA_CARTERA'] }}</td>
                                    </tr>
                                    <tr>
                                        <th class="fz-11">PENDIENTE DESPACHO:</th>
                                        <td class="fz-14">{{ $subItem['DATA']['MEDELLIN']['PENDIENTE_DESPACHO'] }}</td>
                                        <th class="fz-11">DESPACHADO:</th>
                                        <td class="fz-14">{{ $subItem['DATA']['MEDELLIN']['DESPACHADO'] }}</td>
                                    </tr>
                                    <tr>
                                        <th colspan="2" style="width: 50%;">
                                            <div class="col-lg-12 center" style="display: flex; justify-content: center; align-items: center;">
                                                <img width="100%" src="{{ asset($subItem['DATA']['CHART_PIE_DISTRIBUTION_NACIONAL']) }}">
                                            </div>                                
                                        </th>
                                        <th colspan="2" style="width: 50%;">
                                            <div class="col-lg-12 center" style="display: flex; justify-content: center; align-items: center;">
                                                <img width="100%" src="{{ asset($subItem['DATA']['CHART_PIE_DISTRIBUTION_MEDELLIN']) }}">
                                            </div>    
                                        </th>
                                    </tr>
                                </tbody>
                            </table>
                            @if(collect($item['TRADEMARKS'])->where('DATA.TOTAL', '>', 0)->count() > $index)<div style="page-break-after: always;"></div>@endif
                            @php($index += 1)
                            @endforeach
                            </td>
                        </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </body>
    @endforeach
</html>