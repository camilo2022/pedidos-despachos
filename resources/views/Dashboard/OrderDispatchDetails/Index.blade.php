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
                            <li class="breadcrumb-item">Details</li>
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
                    INFORMACION DE LA ORDEN DE DESPACHO
                </div>
                <div class="col-12">
                    <div class="card mt-2">
                        <div class="card-header p-2">
                            <ul class="nav nav-pills">
                                <li class="nav-item">
                                    <a class="btn btn-info text-white" id="IndexOrderDispatchDetail" data-id="{{ $orderDispatch->id }}" onclick="IndexOrderDispatchDetail({{ $orderDispatch->id }})" type="button" title="Orden de despacho.">
                                        <b>ORDEN DE DESPACHO: {{ $orderDispatch->consecutive }}</b>
                                    </a>
                                </li>
                                <li class="nav-item ml-auto">
                                    <a class="btn bg-purple text-white" type="button" href=" {{ route('Dashboard.Dispatches.Print', $orderDispatch->id) }}" target="_blank" title="Imprimir pdf de la orden de despacho.">
                                        <i class="fas fa-print text-white mr-2"></i> <b>IMPRIMIR</b>
                                    </a>
                                </li>
                                @if ($orderDispatch->dispatch_status == 'Pendiente' && in_array(Auth::user()->title, ['SUPER ADMINISTRADOR', 'ADMINISTRADOR', 'FILTRADOR', 'COORDINADOR BODEGA']))
                                    <li class="nav-item ml-2">
                                        <a class="btn btn-success text-white" type="button" onclick="ApproveOrderDispatch({{ $orderDispatch->id }}, false)" title="Aprobar orden de despacho.">
                                            <i class="fas fa-check mr-2"></i> <b>APROBAR</b>
                                        </a>
                                    </li>
                                    <li class="nav-item ml-2">
                                        <a class="btn btn-danger text-white" type="button" onclick="CancelOrderDispatch({{ $orderDispatch->id }}, false)" title="Cancelar orden de despacho.">
                                            <i class="fas fa-xmark mr-2"></i> <b>CANCELAR</b>
                                        </a>
                                    </li>
                                @elseif ($orderDispatch->dispatch_status == 'Alistamiento' && is_null($orderDispatch->order_picking))
                                    @if (in_array(Auth::user()->title, ['SUPER ADMINISTRADOR', 'ADMINISTRADOR', 'FILTRADOR', 'COORDINADOR BODEGA']))
                                        <li class="nav-item ml-2">
                                            <a class="btn btn-info text-white" type="button" onclick="PendingOrderDispatch({{ $orderDispatch->id }}, false)" title="Devolver orden de despacho.">
                                                <i class="fas fa-arrows-rotate mr-2"></i> <b>DEVOLVER</b>
                                            </a>
                                        </li>
                                    @endif
                                    @if (in_array(Auth::user()->title, ['SUPER ADMINISTRADOR', 'ADMINISTRADOR', 'BODEGA']))
                                        <li class="nav-item ml-2">
                                            <a class="btn btn-primary text-white" type="button" onclick="PickingOrderDispatch({{ $orderDispatch->id }}, false)" title="Alistar orden de despacho.">
                                                <i class="fas fa-barcode-read mr-2"></i> <b>ALISTAR</b>
                                            </a>
                                        </li>
                                    @endif
                                @elseif ($orderDispatch->dispatch_status == 'Revision' && in_array(Auth::user()->title, ['SUPER ADMINISTRADOR', 'ADMINISTRADOR', 'COORDINADOR BODEGA']))
                                    <li class="nav-item ml-2">
                                        <a class="btn btn-warning text-white" type="button" href=" {{ route('Dashboard.Dispatches.Review', $orderDispatch->id) }}" title="Revisar alistamiento orden de despacho.">
                                            <i class="fas fa-gear mr-2"></i> <b>REVISAR</b>
                                        </a>
                                    </li>
                                @elseif ($orderDispatch->dispatch_status == 'Empacado' && in_array(Auth::user()->title, ['SUPER ADMINISTRADOR', 'ADMINISTRADOR', 'BODEGA']) && is_null($orderDispatch->order_packing))
                                    <li class="nav-item ml-2">
                                        <a class="btn btn-secondary text-white" type="button" onclick="PackingOrderDispatch({{ $orderDispatch->id }}, false)" title="Empacar orden de despacho.">
                                            <i class="fas fa-box-open-full mr-2"></i> <b>EMPACAR</b>
                                        </a>
                                    </li>
                                @elseif ($orderDispatch->dispatch_status == 'Facturacion' && in_array(Auth::user()->title, ['SUPER ADMINISTRADOR', 'ADMINISTRADOR', 'FACTURADOR']))
                                    <li class="nav-item ml-2">
                                        <a class="btn bg-orange text-white" style="color: white !important;" type="button" onclick="InvoiceOrderDispatchModal({{ $orderDispatch->id }})" title="Facturar orden de despacho.">
                                            <i class="fas fa-money-bill text-white mr-2"></i> <b>FACTURAR</b>
                                        </a>
                                    </li>
                                @elseif ($orderDispatch->dispatch_status == 'Despachado' && !is_null($orderDispatch->order_packing) && in_array(Auth::user()->title, ['SUPER ADMINISTRADOR', 'ADMINISTRADOR', 'FILTRADOR', 'COORDINADOR BODEGA', 'FACTURADOR']))
                                    <li class="nav-item ml-2">
                                        <a class="btn bg-dark text-white" type="button" href=" {{ route('Dashboard.Dispatches.Download', $orderDispatch->id) }}" target="_blank" title="Descargar pdf rotulo orden de despacho.">
                                            <i class="fas fa-file-pdf text-white mr-2"></i> <b>DESCARGAR</b>
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
                                            <td width="25%" class="order">{{ $orderDispatch->client->client_number_document }}-{{ $orderDispatch->client->client_branch_code }}</td>
                                            <th width="15%" class="order">FECHA FILTRADO:</th>
                                            <td width="18%" class="order">
                                                <span class="badge badge-pill badge-info">{{ Carbon::parse($orderDispatch->created_at)->format('Y-m-d H:i:s') }}</span>
                                            </td>
                                            <th width="13%" class="order">FILTRADOR: </th>
                                            <td width="24%" class="order">{{ strtoupper($orderDispatch->dispatch_user->name . ' ' . $orderDispatch->dispatch_user->last_name) }}</td>
                                        </tr>
                                        <tr>
                                            <th class="order">CLIENTE:</th>
                                            <td class="order">{{ $orderDispatch->client->client_name }}</td>
                                            <th class="order">FECHA ALISTAMIENTO:</th>
                                            <td class="order">
                                                <span class="badge badge-pill badge-primary">{{ is_null($orderDispatch->order_picking) ? '-' : Carbon::parse($orderDispatch->order_picking->picking_date)->format('Y-m-d H:i:s') }}</span>
                                            </td>
                                            <th class="order">ALISTADOR: </th>
                                            <td class="order">{{ is_null($orderDispatch->order_picking) ? '-' : strtoupper($orderDispatch->order_picking->picking_user->name . ' ' . $orderDispatch->order_picking->picking_user->last_name) }}</td>
                                        </tr>
                                        <tr>
                                            <th class="order">CIUDAD:</th>
                                            <td class="order">{{ $orderDispatch->client->departament }} - {{ $orderDispatch->client->city }}</td>
                                            <th class="order">FECHA EMPACADO:</th>
                                            <td class="order">
                                                <span class="badge badge-pill badge-secondary">{{ is_null($orderDispatch->order_packing) ? '-' : Carbon::parse($orderDispatch->order_packing->packing_date)->format('Y-m-d H:i:s') }}</span>
                                            </td>
                                            <th class="order">EMPACADOR: </th>
                                            <td class="order">{{ is_null($orderDispatch->order_packing) ? '-' : strtoupper($orderDispatch->order_packing->packing_user->name . ' ' . $orderDispatch->order_packing->packing_user->last_name) }}</td>
                                        </tr>
                                        <tr>
                                            <th class="order">DIRECCION:</th>
                                            <td class="order">{{ $orderDispatch->client->client_branch_address }}</td>
                                            <th class="order">FECHA FACTURACION:</th>
                                            <td class="order">
                                                <span class="badge badge-pill bg-orange" style="color: white !important;">{{ is_null($orderDispatch->invoice_date) ? '-' : Carbon::parse($orderDispatch->invoice_date)->format('Y-m-d H:i:s') }}</span>
                                            </td>
                                            <th class="order">FACTURADOR: </th>
                                            <td class="order">{{ is_null($orderDispatch->invoice_user) ? '-' : strtoupper($orderDispatch->dispatch_user->name . ' ' . $orderDispatch->dispatch_user->last_name) }}</td>
                                        </tr>
                                        <tr>
                                            <th class="order">ZONA:</th>
                                            <td class="order">{{ $orderDispatch->client->zone }}</td>
                                            <th class="order">FACTURAS:</th>
                                            <td class="order">
                                                @forelse ($orderDispatch->invoices as $invoice)
                                                    <span class="badge badge-pill bg-dark">{{ $invoice->reference }}</span>
                                                @empty
                                                    <span class="badge badge-pill bg-dark">-</span>
                                                @endforelse
                                            </td>
                                            <th class="order">ESTADO:</th>
                                            <td class="order">                                            
                                                @switch($orderDispatch->dispatch_status)
                                                    @case('Pendiente')
                                                        <span class="badge badge-pill badge-info"><i class="fas fa-arrows-rotate mr-2"></i>Pendiente</span>
                                                        @break
                                                    @case('Cancelado')
                                                        <span class="badge badge-pill badge-danger text-white" style="color:white !important;"><i class="fas fa-xmark mr-2 text-white"></i>Cancelado</span>
                                                        @break
                                                    @case('Alistamiento')
                                                        <span class="badge badge-pill badge-primary text-white"><i class="fas fa-barcode-read mr-2"></i>Alistamiento</span>
                                                        @break
                                                    @case('Revision')
                                                        <span class="badge badge-pill badge-warning" style="color:white !important;"><i class="fas fa-gear mr-2 text-white"></i>Revision</span>
                                                        @break
                                                    @case('Empacado')
                                                        <span class="badge badge-pill badge-secondary"><i class="fas fa-box-open-full mr-2"></i>Empacado</span>
                                                        @break
                                                    @case('Facturacion')
                                                        <span class="badge badge-pill bg-orange" style="color:white !important;"><i class="fas fa-money-bill mr-2 text-white"></i>Facturacion</span>
                                                        @break
                                                    @case('Despachado')
                                                        <span class="badge badge-pill badge-success"><i class="fas fa-share-all mr-2"></i>Despachado</span>
                                                        @break
                                                    @default
                                                        <span class="badge badge-pill badge-info"><i class="fas fa-arrows-rotate mr-2"></i>Pendiente</span>
                                                @endswitch
                                            </td>
                                        </tr>
                                        <tr>
                                            <th class="order">TELEFONOS:</th>
                                            <td class="order">{{ $orderDispatch->client->client_number_phone }} - {{ $orderDispatch->client->client_branch_number_phone }}</td>
                                            <th class="order">{{ $orderDispatch->dispatch_status == 'Cancelado' ? 'FECHA CANCELADO' : 'FECHA DESPACHADO' }}:</th>
                                            <td class="order">
                                                <span class="badge badge-pill {{ $orderDispatch->dispatch_status == 'Cancelado' ? 'badge-danger' : 'badge-success' }}">{{ is_null($orderDispatch->dispatch_date) ? '-' : Carbon::parse($orderDispatch->dispatch_date)->format('Y-m-d H:i:s') }}</span>
                                            </td>
                                            <th class="order">{{ $orderDispatch->dispatch_status == 'Cancelado' ? 'CANCELO' : 'DESPACHO' }}:</th>
                                            <td class="order">{{ is_null($orderDispatch->dispatch_date) ? '-' : strtoupper($orderDispatch->dispatch_user->name . ' ' . $orderDispatch->dispatch_user->last_name) }}</td>
                                        </tr>
                                        <tr>
                                            <th class="order">CORREO:</th>
                                            <td class="order">{{ $orderDispatch->client->email }}</td>
                                            <th class="order">CORRERIA:</th>
                                            <td class="order" colspan="3">
                                                {{ $orderDispatch->correria->name }} - {{ $orderDispatch->correria->code }} | {{ $orderDispatch->correria->start_date }} - {{ $orderDispatch->correria->end_date }}
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
                <div class="card-header text-center" style="background-color: #343a40; color:white; font-weigth:bold;">
                    DETALLES DE LA ORDEN DE DESPACHO
                </div>
                <div class="col-12">
                    <div class="card mt-2">
                        <div class="card-header p-2">
                            <ul class="nav nav-pills">
                            </ul>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table id="orderDispatchs" class="table table-bordered table-hover dataTable dtr-inline nowrap w-100">
                                    <thead id="OrderDispatchDetailHead" style="background-color: #343a40; color: white;">
                                    </thead>
                                    <tbody id="OrderDispatchDetailBody">
                                    </tbody>
                                    <tfoot id="OrderDispatchDetailFoot" style="background-color: #343a40; color: white;">
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @include('Dashboard.OrderDispatches.Invoice')
    </section>
@endsection
@section('script')
    <script src="{{ asset('js/Dashboard/OrderDispatchDetails/Index.js') }}"></script>
    <script src="{{ asset('js/Dashboard/OrderDispatchDetails/Approve.js') }}"></script>
    <script src="{{ asset('js/Dashboard/OrderDispatchDetails/Cancel.js') }}"></script>
    <script src="{{ asset('js/Dashboard/OrderDispatchDetails/Decline.js') }}"></script>
    <script src="{{ asset('js/Dashboard/OrderDispatchDetails/Pending.js') }}"></script>

    <script src="{{ asset('js/Dashboard/OrderDispatches/Approve.js') }}"></script>
    <script src="{{ asset('js/Dashboard/OrderDispatches/Cancel.js') }}"></script>
    <script src="{{ asset('js/Dashboard/OrderDispatches/Pending.js') }}"></script>
    <script src="{{ asset('js/Dashboard/OrderDispatches/Picking.js') }}"></script>
    <script src="{{ asset('js/Dashboard/OrderDispatches/Packing.js') }}"></script>
    <script src="{{ asset('js/Dashboard/OrderDispatches/Invoice.js') }}"></script>
@endsection
