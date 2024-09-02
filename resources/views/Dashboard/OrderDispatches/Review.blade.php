@extends('Templates.Dashboard')
@section('content')
    <section class="content">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0 text-dark">ORDEN N° {{ $orderDispatch->consecutive }} - {{ $orderDispatch->client->client_name }}</h1>
                    </div><!-- /.col -->
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item">Dashboard</li>
                            <li class="breadcrumb-item">Dispatches</li>
                            <li class="breadcrumb-item">Review</li>
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
                    INFORMACION DE LA ORDEN DE DESPACHO
                </div>
                <div class="col-12">
                    <div class="card mt-2">
                        <div class="card-header p-2">
                            <ul class="nav nav-pills">
                                <li class="nav-item">
                                    <a class="btn btn-info text-white" id="ReviewOrderDispatch" type="button" title="Orden de despacho.">
                                        <b>ORDEN DE DESPACHO: {{ $orderDispatch->consecutive }}</b>
                                    </a>
                                </li>
                                @if (in_array(Auth::user()->title, ['SUPER ADMINISTRADOR', 'ADMINISTRADOR', 'COORDINADOR BODEGA']))
                                    <li class="nav-item ml-auto">
                                        <a class="btn btn-success text-white" type="button" onclick="ApproveOrderPicking({{ $orderDispatch->order_picking->id }}, false)" title="Aprobar orden de alistamiento de la orden de despacho.">
                                            <i class="fas fa-check mr-2"></i> <b>APROBAR</b>
                                        </a>
                                    </li>
                                    <li class="nav-item ml-2">
                                        <a class="btn btn-danger text-white" type="button" onclick="CancelOrderPicking({{ $orderDispatch->order_picking->id }}, false)" title="Cancelar orden de alistamiento de la orden de despacho.">
                                            <i class="fas fa-xmark mr-2"></i> <b>CANCELAR</b>
                                        </a>
                                    </li>
                                @endif
                            </ul>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table width="100%" class="order-table" cellpadding="0" cellspacing="0">
                                    <tbody>
                                        <tr>
                                            <th width="10%" class="order">NIT:</th>
                                            <td width="29%" class="order">{{ $orderDispatch->client->client_number_document }}-{{ $orderDispatch->client->client_branch_code }}</th>
                                            <th width="11%" class="order">CLIENTE:</th>
                                            <td width="18%" class="order">{{ $orderDispatch->client->client_name }}</th>
                                            <th width="13%"  class="order">CIUDAD:</th>
                                            <td width="24%"  class="order">{{ $orderDispatch->client->departament }} - {{ $orderDispatch->client->city }}</th>
                                        </tr>
                                        <tr>
                                            <th class="order">ZONA:</th>
                                            <td class="order">{{ $orderDispatch->client->zone }}</th>
                                            <th class="order">DIRECCION:</th>
                                            <td class="order">{{ $orderDispatch->client->client_branch_address }}</th>
                                            <th class="order">FECHA ALISTAMIENTO:</th>
                                            <td class="order">
                                                <span class="badge badge-primary">{{ Carbon::parse($orderDispatch->order_picking->picking_date)->format('Y-m-d H:i:s') }}</span>
                                            </th>
                                        </tr>
                                        <tr>
                                            <th class="order">TELEFONOS:</th>
                                            <td class="order">{{ $orderDispatch->client->client_number_phone }} - {{ $orderDispatch->client->client_branch_number_phone }}</th>
                                            <th class="order">CORREO:</th>
                                            <td class="order">{{ $orderDispatch->client->email }}</th>
                                            <th class="order">ALISTADOR: </th>
                                            <td class="order">{{ strtoupper($orderDispatch->order_picking->picking_user->name . ' ' . $orderDispatch->order_picking->picking_user->last_name) }}</th>
                                        </tr>
                                        <tr>
                                            <th class="order">CORRERIA:</th>
                                            <td class="order" colspan="3">
                                                {{ $orderDispatch->correria->name }} - {{ $orderDispatch->correria->code }} | {{ $orderDispatch->correria->start_date }} - {{ $orderDispatch->correria->end_date }}
                                            </th>
                                            <th class="order">ESTADO:</th>
                                            <td class="order">
                                                @switch($orderDispatch->dispatch_status)
                                                    @case('Pendiente')
                                                        <span class="badge badge-info"><i class="fas fa-arrows-rotate mr-2"></i>Pendiente</span>
                                                        @break
                                                    @case('Cancelado')
                                                        <span class="badge badge-danger text-white" style="color:white !important;"><i class="fas fa-xmark mr-2 text-white"></i>Cancelado</span>
                                                        @break
                                                    @case('Alistamiento')
                                                        <span class="badge badge-primary text-white"><i class="fas fa-barcode-read mr-2"></i>Alistamiento</span>
                                                        @break
                                                    @case('Revision')
                                                        <span class="badge badge-warning" style="color:white !important;"><i class="fas fa-gear mr-2 text-white"></i>Revision</span>
                                                        @break
                                                    @case('Empacado')
                                                        <span class="badge badge-secondary"><i class="fas fa-box-open-full mr-2"></i>Empacado</span>
                                                        @break
                                                    @case('Facturacion')
                                                        <span class="badge bg-orange" style="color:white !important;"><i class="fas fa-money-bill mr-2 text-white"></i>Facturacion</span>
                                                        @break
                                                    @case('Despachado')
                                                        <span class="badge badge-success"><i class="fas fa-share-all mr-2"></i>Despachado</span>
                                                        @break
                                                    @default
                                                        <span class="badge badge-info"><i class="fas fa-arrows-rotate mr-2"></i>Pendiente</span>
                                                @endswitch
                                            </th>
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
                <div class="card-header text-center" style="background-color: #343a40; color:white; font-weigth:bold;">
                    DETALLES DE LA ORDEN DE DESPACHO VS ORDEN DE ALISTAMIENTO
                </div>
                <div class="col-12">
                    <div class="card mt-2">
                        <div class="card-header p-2">
                            <ul class="nav nav-pills">
                            </ul>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <div class="col-lg-12">
                                    <div>
                                        <button type="button" class="mb-2 btn w-100 collapsed btn-info" data-toggle="collapse" data-target="#collapseOrderDispatch" aria-expanded="false" aria-controls="#collapseOrderDispatch">
                                            <b>CURVA FILTRADA PARA DESPACHAR</b>
                                        </button>
                                        <div class="table-responsive collapse" id="collapseOrderDispatch">
                                            <div class="col-12 pt-2">
                                                <div class="table-responsive">
                                                    <table width="100%" class="table table-bordered dataTable dtr-inline nowrap w-100 text-center" cellpadding="0" cellspacing="0">
                                                        <thead style="background-color: #343a40; color: white;">
                                                            <tr>
                                                                <th>PEDIDO</th>
                                                                <th>AMARRADOR</th>
                                                                <th>REFERENCIA</th>
                                                                <th>COLOR</th>
                                                                @foreach ($orderDispatchSizes as $size)
                                                                <th>{{ "T{$size->code}" }}</th>
                                                                @endforeach
                                                                <th>TOTAL</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @php($quantitiesTotal = 0)
                                                            @foreach ($orderDispatch->order_dispatch_details as $index => $order_dispatch_detail)
                                                                @php($quantities = 0)
                                                                <tr>
                                                                    <th>{{ $order_dispatch_detail->order_id }}</th>
                                                                    <th>{{ $order_dispatch_detail->order_detail_id }}</th>
                                                                    <th>{{ $order_dispatch_detail->order_detail->product->code }}</th>
                                                                    <th>{{ $order_dispatch_detail->order_detail->color->name }} - {{ $order_dispatch_detail->order_detail->color->code }}</th>

                                                                    @foreach ($orderDispatchSizes as $size)

                                                                    <th id="{{ "OD-{$order_dispatch_detail->order_detail->product->code}-{$order_dispatch_detail->order_detail->color->code}-T{$size->code}-$order_dispatch_detail->id" }}">
                                                                        {{ $order_dispatch_detail->{"T{$size->code}"} }} @php($quantities += $order_dispatch_detail->{"T{$size->code}"})
                                                                    </th>

                                                                    @endforeach

                                                                    @php($quantitiesTotal += $quantities)

                                                                    <th id="{{ "OD-{$order_dispatch_detail->order_detail->product->code}-{$order_dispatch_detail->order_detail->color->code}-TOTAL-$index" }}">{{ $quantities }}</th>
                                                                </tr>
                                                            @endforeach
                                                        </tbody>
                                                        <tfoot style="background-color: #343a40; color: white;">
                                                            <tr>
                                                                <th>PEDIDO</th>
                                                                <th>AMARRADOR</th>
                                                                <th>REFERENCIA</th>
                                                                <th>COLOR</th>
                                                                @foreach ($orderDispatchSizes as $size)
                                                                <th>{{ $orderDispatch->order_dispatch_details->pluck("T{$size->code}")->sum() }}</th>
                                                                @endforeach
                                                                <th>{{ $quantitiesTotal }}</th>
                                                            </tr>
                                                        </tfoot>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-12 pt-4">
                                    <div>
                                        <button type="button" class="mb-2 btn w-100 collapsed btn-primary" data-toggle="collapse" data-target="#collapseOrderPicking" aria-expanded="false" aria-controls="#collapseOrderPicking">
                                            <b>CURVA ALISTADA PARA DESPACHAR</b>
                                        </button>
                                        <div class="table-responsive collapse" id="collapseOrderPicking">
                                            <div class="col-12 pt-2">
                                                <div class="table-responsive">
                                                    <table width="100%" class="table table-bordered dataTable dtr-inline nowrap w-100 text-center" cellpadding="0" cellspacing="0">
                                                        <thead style="background-color: #343a40; color: white;">
                                                            <tr>
                                                                <th>PEDIDO</th>
                                                                <th>AMARRADOR</th>
                                                                <th>REFERENCIA</th>
                                                                <th>COLOR</th>
                                                                @foreach ($orderDispatchSizes as $size)
                                                                <th>{{ "T{$size->code}" }}</th>
                                                                @endforeach
                                                                <th>TOTAL</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @php($quantitiesTotalOrderDispatch = 0)
                                                            @php($quantitiesTotalOrderPicking = 0)
                                                            @foreach ($orderDispatch->order_picking->order_picking_details as $index => $order_picking_detail)
                                                                @php($quantitiesDispatch = 0)
                                                                @php($quantitiesPicking = 0)
                                                                <tr>
                                                                    <th>{{ $order_picking_detail->order_dispatch_detail->order_id }}</th>
                                                                    <th>{{ $order_picking_detail->order_dispatch_detail->order_detail_id }}</th>
                                                                    <th>{{ $order_picking_detail->order_dispatch_detail->order_detail->product->code }}</th>
                                                                    <th>{{ $order_picking_detail->order_dispatch_detail->order_detail->color->name }} - {{ $order_picking_detail->order_dispatch_detail->order_detail->color->code }}</th>
                                                                    @php($order_dispatch_detail = $order_picking_detail->order_dispatch_detail)
                                                                    @foreach ($orderDispatchSizes as $size)

                                                                    <th id="{{ "OA-{$order_picking_detail->order_dispatch_detail->order_detail->product->code}-{$order_picking_detail->order_dispatch_detail->order_detail->color->code}-T{$size->code}-$order_dispatch_detail->id" }}"
                                                                        class="@if ($order_picking_detail->{"T{$size->code}"} == $order_picking_detail->order_dispatch_detail->{"T{$size->code}"}) bg-success @elseif ($order_picking_detail->{"T{$size->code}"} > $order_picking_detail->order_dispatch_detail->{"T{$size->code}"}) bg-primary @elseif ($order_picking_detail->{"T{$size->code}"} < $order_picking_detail->order_dispatch_detail->{"T{$size->code}"}) bg-danger @endif" style="cursor: pointer;"
                                                                        onclick="AddOrderPickingDetailModal({{ $order_picking_detail->id }}, '{{ $order_picking_detail->order_dispatch_detail->order_detail->product->code }}', '{{ $size->code }}', '{{ $order_picking_detail->order_dispatch_detail->order_detail->color->code }}', '{{ $order_picking_detail->order_dispatch_detail->order_detail->color->name }}', '{{ $order_picking_detail->order_dispatch_detail->id }}', '{{ $index }}')">
                                                                        {{ $order_picking_detail->{"T{$size->code}"} }}

                                                                        @php($quantitiesDispatch += $order_picking_detail->order_dispatch_detail->{"T{$size->code}"})
                                                                        @php($quantitiesPicking += $order_picking_detail->{"T{$size->code}"})
                                                                    </th>

                                                                    @endforeach

                                                                    @php($quantitiesTotalOrderDispatch += $quantitiesDispatch)
                                                                    @php($quantitiesTotalOrderPicking += $quantitiesPicking)

                                                                    <th id="{{ "OA-{$order_picking_detail->order_dispatch_detail->order_detail->product->code}-{$order_picking_detail->order_dispatch_detail->order_detail->color->code}-TOTAL-$index" }}"
                                                                        class="@if ($quantitiesPicking == $quantitiesDispatch) bg-success @elseif ($quantitiesPicking > $quantitiesDispatch) bg-primary @elseif ($quantitiesPicking < $quantitiesDispatch) bg-danger @endif">
                                                                        {{ $quantitiesPicking }}
                                                                    </th>
                                                                </tr>
                                                            @endforeach
                                                        </tbody>
                                                        <tfoot style="background-color: #343a40; color: white;">
                                                            <tr>
                                                                <th>PEDIDO</th>
                                                                <th>AMARRADOR</th>
                                                                <th>REFERENCIA</th>
                                                                <th>COLOR</th>
                                                                @foreach ($orderDispatchSizes as $size)
                                                                <th id="{{ "OA-T{$size->code}-TOTAL" }}">
                                                                    {{ $orderDispatch->order_picking->order_picking_details->pluck("T{$size->code}")->sum() }}
                                                                </th>
                                                                @endforeach
                                                                <th id="OA-TOTAL">{{ $quantitiesTotalOrderPicking }}</th>
                                                            </tr>
                                                        </tfoot>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @include('Dashboard.OrderPickingDetails.Add')
    </section>
@endsection
@section('script')
    <script src="{{ asset('js/Dashboard/OrderPickingDetails/Add.js') }}"></script>

    <script src="{{ asset('js/Dashboard/OrderPickings/Approve.js') }}"></script>
    <script src="{{ asset('js/Dashboard/OrderPickings/Cancel.js') }}"></script>
@endsection
