@extends('Templates.Dashboard')
@section('content')
    <section class="content">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0 text-dark">ORDEN N° {{ $orderPacking->order_dispatch->consecutive }} - {{ $orderPacking->order_dispatch->client->client_name }}</h1>
                    </div><!-- /.col -->
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item">Dashboard</li>
                            <li class="breadcrumb-item">Packings</li>
                            <li class="breadcrumb-item">Index</li>
                        </ol>
                    </div><!-- /.col -->
                </div><!-- /.row -->
            </div><!-- /.container-fluid -->
        </div>
    </section>

    @include('Dashboard.Alerts.Success')
    @include('Dashboard.Alerts.Info')
    @include('Dashboard.Alerts.Question')
    @include('Dashboard.Alerts.Warning')
    @include('Dashboard.Alerts.Danger')

    <section class="content">
        <div class="container-fluid">
            <div class="card">
                <div class="card-header text-center" style="background-color: #343a40; color:white; font-weigth:bold;">
                    INFORMACION DE LA ORDEN DE EMPACADO
                </div>
                <div class="col-12">
                    <div class="card mt-2">
                        <div class="card-header p-2">
                            <ul class="nav nav-pills">
                                <li class="nav-item">
                                    <a class="btn btn-info text-white" id="IndexOrderPacking" data-id="{{ $orderPacking->id }}" onclick="IndexOrderPacking({{ $orderPacking->id }})" type="button" title="Orden de EMPACADO.">
                                        <b>ORDEN DE EMPACADO: {{ $orderPacking->order_dispatch->consecutive }}</b>
                                    </a>
                                </li>
                            </ul>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table width="100%" class="order-table" cellpadding="0" cellspacing="0">
                                    <tbody>
                                        <tr>
                                            <th width="10%" class="order">NIT:</th>
                                            <td width="20%" class="order">{{ $orderPacking->order_dispatch->client->client_number_document }}-{{ $orderPacking->order_dispatch->client->client_branch_code }}</td>
                                            <th width="11%" class="order">CLIENTE:</th>
                                            <td width="18%" class="order">{{ $orderPacking->order_dispatch->client->client_name }}</td>
                                            <th width="13%" class="order">EMPACADOR: </th>
                                            <td width="26%" class="order">{{ strtoupper($orderPacking->packing_user->name . ' ' . $orderPacking->packing_user->last_name) }}</td>
                                        </tr>
                                        <tr>
                                            <th class="order">CIUDAD:</th>
                                            <td class="order">{{ $orderPacking->order_dispatch->client->departament }} - {{ $orderPacking->order_dispatch->client->city }}</td>
                                            <th class="order">DIRECCION:</th>
                                            <td class="order">{{ $orderPacking->order_dispatch->client->client_branch_address }}</td>
                                            <th class="order">FECHA EMPACADO:</th>
                                            <td class="order">
                                                <span class="badge badge-pill badge-secondary">{{ Carbon::parse($orderPacking->packing_date)->format('Y-m-d H:i:s') }}</span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th class="order">ZONA:</th>
                                            <td class="order">{{ $orderPacking->order_dispatch->client->zone }}</td>
                                            <th class="order">TELEFONO:</th>
                                            <td class="order">{{ $orderPacking->order_dispatch->client->client_branch_number_phone }}</td>
                                            <th class="order">ESTADO:</th>
                                            <td class="order">                                            
                                                @switch($orderPacking->packing_status)
                                                    @case('En curso')
                                                        <span class="badge badge-pill badge-info"><i class="fas fa-arrows-rotate mr-2"></i>En curso</span>
                                                        @break
                                                    @case('Cancelado')
                                                        <span class="badge badge-pill badge-danger text-white" style="color:white !important;"><i class="fas fa-xmark mr-2 text-white"></i>Cancelado</span>
                                                        @break
                                                    @case('Revision')
                                                        <span class="badge badge-pill badge-warning" style="color:white !important;"><i class="fas fa-gear mr-2 text-white"></i>Revision</span>
                                                        @break
                                                    @case('Aprobado')
                                                        <span class="badge badge-pill badge-success"><i class="fas fa-check mr-2"></i>Aprobado</span>
                                                        @break
                                                    @default
                                                        <span class="badge badge-pill badge-info"><i class="fas fa-arrows-rotate mr-2"></i>En curso</span>
                                                @endswitch
                                            </td>
                                        </tr>
                                        <tr>
                                            <th class="order">TELEFONO:</th>
                                            <td class="order">{{ $orderPacking->order_dispatch->client->client_number_phone }}</td>
                                            <th class="order">CORREO:</th>
                                            <td class="order">{{ $orderPacking->order_dispatch->client->email }}</td>
                                            <th class="order">CORRERIA:</th>
                                            <td class="order">
                                                {{ $orderPacking->order_dispatch->correria->name }} - {{ $orderPacking->order_dispatch->correria->code }} | {{ $orderPacking->order_dispatch->correria->start_date }} - {{ $orderPacking->order_dispatch->correria->end_date }}
                                            </td>
                                        </tr>
                                        <tr>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="card">
                <div class="card-header text-center" style="background-color: #343a40; color:white; font-weigth:bold;" id="TitleOrderPacking">
                    DETALLES DE LA ORDEN DE EMPACADO
                </div>
                <div class="col-12">
                    <div class="card mt-2">
                        <div class="card-header p-2">
                            <ul class="nav nav-pills" id="ButtonsOrderPacking">
                            </ul>
                        </div>
                        <div class="card-body">
                            <div class="row" id="OrderPackages">
                                
                            </div>
                            <div class="row" id="OrderPackingDetails">
                                
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @include('Dashboard.OrderPackingDetails.Add')
    </section>
@endsection
@section('script')
    <script src="{{ asset('js/Dashboard/OrderPackingDetails/Add.js') }}"></script>

    <script src="{{ asset('js/Dashboard/OrderPackings/Index.js') }}"></script>
    <script src="{{ asset('js/Dashboard/OrderPackings/Store.js') }}"></script>
    <script src="{{ asset('js/Dashboard/OrderPackings/Open.js') }}"></script>
    <script src="{{ asset('js/Dashboard/OrderPackings/Close.js') }}"></script>
@endsection
