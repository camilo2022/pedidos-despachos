@extends('Templates.Dashboard')
@section('content')
<section class="content">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0 text-dark">PEDIDO N° {{ $order->id }} - {{ $order->client->client_name }}</h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item">Dashboard</li>
                        <li class="breadcrumb-item">Orders</li>
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
                INFORMACION DEL PEDIDO
            </div>
            <div class="col-12">
                <div class="card mt-2">
                    <div class="card-header p-2">
                        <ul class="nav nav-pills">
                            <li class="nav-item">
                                <a class="btn btn-info text-white" id="IndexOrderDetail" data-id="{{ $order->id }}" onclick="IndexOrderDetail({{ $order->id }})" type="button" title="Pedido.">
                                    <b>ORDEN DE PEDIDO: {{ $order->id }}</b>
                                </a>
                            </li>
                            @if ($order->seller_status == 'Pendiente' && in_array(Auth::user()->title, ['SUPER ADMINISTRADOR', 'ADMINISTRADOR', 'CARTERA', 'FILTRADOR', 'VENDEDOR', 'VENDEDOR ESPECIAL']) && $order->seller_user_id == Auth::user()->id)
                                <li class="nav-item ml-auto">
                                    <a class="btn btn-success text-white" type="button" onclick="AssentOrder({{ $order->id }}, false)" title="Realizar pedido.">
                                        <i class="fas fa-check mr-2"></i> <b>REALIZAR</b>
                                    </a>
                                </li>
                                <li class="nav-item ml-2">
                                    <a class="btn btn-danger text-white" type="button" onclick="CancelOrder({{ $order->id }}, false)" title="Cancelar pedido.">
                                        <i class="fas fa-xmark mr-2"></i> <b>CANCELAR</b>
                                    </a>
                                </li>
                            @elseif ($order->seller_status == 'Aprobado' && $order->wallet_status == 'Pendiente' && in_array(Auth::user()->title, ['SUPER ADMINISTRADOR', 'ADMINISTRADOR', 'CARTERA']))
                                <li class="nav-item ml-auto">
                                    <a class="btn btn-info text-white" type="button" onclick="PendingOrder({{ $order->id }}, false)" title="Devolver pedido.">
                                        <i class="fas fa-arrows-rotate mr-2"></i> <b>DEVOLVER</b>
                                    </a>
                                </li>
                                @if (in_array($order->seller_user->title, ['VENDEDOR ESPECIAL']))
                                    <li class="nav-item ml-2">
                                        <a class="btn btn-success text-white" type="button" onclick="AuthorizeOrder({{ $order->id }}, false)" title="Autorizar pedido.">
                                            <i class="fas fa-check-double mr-2"></i> <b>AUTORIZAR</b>
                                        </a>
                                    </li>
                                @else
                                    <li class="nav-item ml-2">
                                        <a class="btn btn-success text-white" type="button" onclick="ApproveOrder({{ $order->id }}, false)" title="Aprobar pedido.">
                                            <i class="fas fa-check-double mr-2"></i> <b>APROBAR</b>
                                        </a>
                                    </li>
                                    <li class="nav-item ml-2">
                                        <a class="btn btn-warning text-white" type="button" onclick="PartiallyApproveOrder({{ $order->id }}, false)" title="Aprobar parcialmente pedido.">
                                            <i class="fas fa-check mr-2"></i> <b>APROBAR PARCIAL</b>
                                        </a>
                                    </li>
                                @endif
                                <li class="nav-item ml-2">
                                    <a class="btn btn-danger text-white" type="button" onclick="DeclineOrder({{ $order->id }}, false)" title="Rechazar pedido.">
                                        <i class="fas fa-xmark mr-2"></i> <b>RECHAZAR</b>
                                    </a>
                                </li>
                                <li class="nav-item ml-2">
                                    <a class="btn btn-secondary text-white" type="button" onclick="SuspendOrder({{ $order->id }}, false)" title="Suspender pedido.">
                                        <i class="fas fa-solid fa-clock-rotate-left text-white mr-2"></i> <b>SUSPENDER</b>
                                    </a>
                                </li>
                                <li class="nav-item ml-2">
                                    <a class="btn bg-orange text-white" style="color: white !important;" type="button" onclick="DelayOrder({{ $order->id }}, false)" title="En mora pedido.">
                                        <i class="fas fa-dollar-sign text-white mr-2"></i> <b>EN MORA</b>
                                    </a>
                                </li>
                            @elseif ($order->seller_status == 'Aprobado' && $order->wallet_status == 'Parcialmente Aprobado' && in_array(Auth::user()->title, ['SUPER ADMINISTRADOR', 'ADMINISTRADOR', 'CARTERA']))
                                @if (in_array($order->seller_user->title, ['VENDEDOR ESPECIAL']))
                                    <li class="nav-item ml-auto">
                                        <a class="btn btn-success text-white" type="button" onclick="AuthorizeOrder({{ $order->id }}, false)" title="Autorizar pedido.">
                                            <i class="fas fa-check-double mr-2"></i> <b>AUTORIZAR</b>
                                        </a>
                                    </li>
                                @else
                                    <li class="nav-item ml-auto">
                                        <a class="btn btn-success text-white" type="button" onclick="ApproveOrder({{ $order->id }}, false)" title="Aprobar pedido.">
                                            <i class="fas fa-check-double mr-2"></i> <b>APROBAR</b>
                                        </a>
                                    </li>
                                @endif
                                <li class="nav-item ml-2">
                                    <a class="btn btn-danger text-white" type="button" onclick="DeclineOrder({{ $order->id }}, false)" title="Rechazar pedido.">
                                        <i class="fas fa-xmark mr-2"></i> <b>RECHAZAR</b>
                                    </a>
                                </li>
                                <li class="nav-item ml-2">
                                    <a class="btn btn-secondary text-white" type="button" onclick="SuspendOrder({{ $order->id }}, false)" title="Suspender pedido.">
                                        <i class="fas fa-solid fa-clock-rotate-left text-white mr-2"></i> <b>SUSPENDER</b>
                                    </a>
                                </li>
                                <li class="nav-item ml-2">
                                    <a class="btn bg-orange text-white" style="color: white !important;" type="button" onclick="DelayOrder({{ $order->id }}, false)" title="En mora pedido.">
                                        <i class="fas fa-dollar-sign text-white mr-2"></i> <b>EN MORA</b>
                                    </a>
                                </li>
                            @elseif ($order->seller_status == 'Aprobado' && $order->wallet_status == 'Suspendido' && in_array(Auth::user()->title, ['SUPER ADMINISTRADOR', 'ADMINISTRADOR', 'CARTERA']))
                                <li class="nav-item ml-auto">
                                    <a class="btn btn-info text-white" type="button" onclick="PendingOrder({{ $order->id }}, false)" title="Devolver pedido.">
                                        <i class="fas fa-arrows-rotate mr-2"></i> <b>DEVOLVER</b>
                                    </a>
                                </li>
                                @if (in_array($order->seller_user->title, ['VENDEDOR ESPECIAL']))
                                    <li class="nav-item ml-2">
                                        <a class="btn btn-success text-white" type="button" onclick="AuthorizeOrder({{ $order->id }}, false)" title="Autorizar pedido.">
                                            <i class="fas fa-check-double mr-2"></i> <b>AUTORIZAR</b>
                                        </a>
                                    </li>
                                @else
                                    <li class="nav-item ml-2">
                                        <a class="btn btn-success text-white" type="button" onclick="ApproveOrder({{ $order->id }}, false)" title="Aprobar pedido.">
                                            <i class="fas fa-check-double mr-2"></i> <b>APROBAR</b>
                                        </a>
                                    </li>
                                    <li class="nav-item ml-2">
                                        <a class="btn btn-warning text-white" type="button" onclick="PartiallyApproveOrder({{ $order->id }}, false)" title="Aprobar parcialmente pedido.">
                                            <i class="fas fa-check mr-2"></i> <b>APROBAR PARCIAL</b>
                                        </a>
                                    </li>
                                @endif
                                <li class="nav-item ml-2">
                                    <a class="btn btn-danger text-white" type="button" onclick="DeclineOrder({{ $order->id }}, false)" title="Rechazar pedido.">
                                        <i class="fas fa-xmark mr-2"></i> <b>RECHAZAR</b>
                                    </a>
                                </li>
                                <li class="nav-item ml-2">
                                    <a class="btn bg-orange text-white" style="color: white !important;" type="button" onclick="DelayOrder({{ $order->id }}, false)" title="En mora pedido.">
                                        <i class="fas fa-dollar-sign text-white mr-2"></i> <b>EN MORA</b>
                                    </a>
                                </li>
                            @elseif ($order->seller_status == 'Aprobado' && $order->wallet_status == 'En mora' && in_array(Auth::user()->title, ['SUPER ADMINISTRADOR', 'ADMINISTRADOR', 'CARTERA']))
                                @if (in_array($order->seller_user->title, ['VENDEDOR ESPECIAL']))
                                    <li class="nav-item ml-auto">
                                        <a class="btn btn-success text-white" type="button" onclick="AuthorizeOrder({{ $order->id }}, false)" title="Autorizar pedido.">
                                            <i class="fas fa-check-double mr-2"></i> <b>AUTORIZAR</b>
                                        </a>
                                    </li>
                                @else
                                    <li class="nav-item ml-auto">
                                        <a class="btn btn-success text-white" type="button" onclick="ApproveOrder({{ $order->id }}, false)" title="Aprobar pedido.">
                                            <i class="fas fa-check-double mr-2"></i> <b>APROBAR</b>
                                        </a>
                                    </li>
                                    <li class="nav-item ml-2">
                                        <a class="btn btn-warning text-white" type="button" onclick="PartiallyApproveOrder({{ $order->id }}, false)" title="Aprobar parcialmente pedido.">
                                            <i class="fas fa-check mr-2"></i> <b>APROBAR PARCIAL</b>
                                        </a>
                                    </li>
                                @endif
                                <li class="nav-item ml-2">
                                    <a class="btn btn-danger text-white" type="button" onclick="DeclineOrder({{ $order->id }}, false)" title="Rechazar pedido.">
                                        <i class="fas fa-xmark mr-2"></i> <b>RECHAZAR</b>
                                    </a>
                                </li>
                            @elseif ($order->seller_status == 'Aprobado' && in_array($order->wallet_status, ['Aprobado', 'Autorizado']) && $order->dispatch_status == 'Pendiente' && in_array(Auth::user()->title, ['SUPER ADMINISTRADOR', 'ADMINISTRADOR', 'CARTERA']))
                                <li class="nav-item ml-auto">
                                    <a class="btn btn-danger text-white" type="button" onclick="DeclineOrder({{ $order->id }}, false)" title="Rechazar pedido.">
                                        <i class="fas fa-xmark mr-2"></i> <b>RECHAZAR</b>
                                    </a>
                                </li>
                                <li class="nav-item ml-2">
                                    <a class="btn btn-secondary text-white" type="button" onclick="SuspendOrder({{ $order->id }}, false)" title="Suspender pedido.">
                                        <i class="fas fa-solid fa-clock-rotate-left text-white mr-2"></i> <b>SUSPENDER</b>
                                    </a>
                                </li>
                                <li class="nav-item ml-2">
                                    <a class="btn bg-orange text-white" style="color: white !important;" type="button" onclick="DelayOrder({{ $order->id }}, false)" title="En mora pedido.">
                                        <i class="fas fa-dollar-sign text-white mr-2"></i> <b>EN MORA</b>
                                    </a>
                                </li>
                                @if (in_array($order->seller_user->title, ['SUPER ADMINISTRADOR', 'ADMINISTRADOR', 'FACTURADOR']) && $order->wallet_status == 'Autorizado')
                                    <li class="nav-item ml-2">
                                        <a class="btn btn-primary text-white" type="button" onclick="DispatchOrder({{ $order->id }}, false)" title="Despachar pedido.">
                                            <i class="fas fa-share-all mr-2"></i> <b>DESPACHAR</b>
                                        </a>
                                    </li>
                                @endif
                            @endif
                        </ul>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table width="100%" class="order-table" cellpadding="0" cellspacing="0">
                                <tbody>
                                    <tr>
                                        <th width="15%" class="order">NIT:</th>
                                        <td width="30%" class="order">{{ $order->client->client_number_document }}-{{ $order->client->client_branch_code }}</td>
                                        <th width="9%" class="order">FECHA:</th>
                                        <td width="18%" class="order">{{ Carbon::parse($order->created_at)->format('Y-m-d H:i:s') }}</td>
                                        <th width="13%" class="order">TIPO DESPACHO: </th>
                                        <td width="24%" class="order">
                                            <span class="badge badge-info">{{ $order->dispatch_type }}</span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th class="order">CLIENTE:</th>
                                        <td class="order">{{ $order->client->client_name }}</td>
                                        <th class="order">CIERRE:</th>
                                        <td class="order">{{ $order->seller_date }}</td>
                                        <th class="order">FECHA DESPACHO: </th>
                                        <td class="order">
                                            <span class="badge bg-dark">{{ $order->dispatch_date }}</span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th class="order">CIUDAD:</th>
                                        <td class="order">{{ $order->client->departament }} - {{ $order->client->city }}</td>
                                        <th class="order">DIRECCION:</th>
                                        <td class="order">{{ $order->client->client_branch_address }}</td>
                                        <th class="order">ESTADO VENDEDOR:</th>
                                        <td class="order">
                                            @switch($order->seller_status)
                                                @case('Pendiente')
                                                    <span class="badge badge-info"><i class="fas fa-arrows-rotate mr-2"></i>Pendiente</span>
                                                    @break
                                                @case('Aprobado')
                                                    <span class="badge badge-success"><i class="fas fa-check mr-2"></i>Aprobado</span>
                                                    @break
                                                @case('Cancelado')
                                                    <span class="badge badge-danger text-white"><i class="fas fa-xmark mr-2 text-white"></i>Cancelado</span>
                                                    @break
                                                @default
                                                    <span class="badge badge-info"><i class="fas fa-arrows-rotate mr-2"></i>Pendiente</span>
                                            @endswitch
                                        </td>
                                    </tr>
                                    <tr>
                                        <th class="order">ZONA:</th>
                                        <td class="order">{{ $order->client->zone }}</td>
                                        <th class="order">TELEFONO:</th>
                                        <td class="order">{{ $order->client->client_number_phone }}</td>
                                        <th class="order">ESTADO CARTERA:</th>
                                        <td class="order">
                                            @switch($order->wallet_status)
                                                @case('Pendiente')
                                                    <span class="badge badge-info"><i class="fas fa-arrows-rotate mr-2"></i>Pendiente</span>
                                                    @break
                                                @case('Cancelado')
                                                    <span class="badge badge-danger text-white"><i class="fas fa-xmark mr-2 text-white"></i>Cancelado</span>
                                                    @break
                                                @case('Suspendido')
                                                    <span class="badge badge-secondary text-white"><i class="fa-solid fa-solid fa-clock-rotate-left mr-2 text-white"></i>Suspendido</span>
                                                    @break
                                                @case('En mora')
                                                    <span class="badge bg-orange" style="color: white !important;"><i class="fas fa-dollar-sign mr-2 text-white"></i>En mora</span>
                                                    @break
                                                @case('Parcialmente Aprobado')
                                                    <span class="badge badge-warning text-white"><i class="fas fa-check mr-2 text-white"></i>Parcialmente Aprobado</span>
                                                    @break
                                                @case('Aprobado')
                                                    <span class="badge badge-success"><i class="fas fa-check-double mr-2"></i>Aprobado</span>
                                                    @break
                                                @case('Autorizado')
                                                    <span class="badge badge-success"><i class="fas fa-check-double mr-2"></i>Autorizado</span>
                                                    @break
                                                @default
                                                    <span class="badge badge-info"><i class="fas fa-arrows-rotate mr-2"></i>Pendiente</span>
                                            @endswitch
                                        </td>
                                    </tr>
                                    <tr>
                                        <th class="order">CORREO:</th>
                                        <td class="order">{{ $order->client->email }}</td>
                                        <th class="order">TELEFONO:</th>
                                        <td class="order">{{ $order->client->client_branch_number_phone }}</td>
                                        <th class="order">ESTADO DESPACHO:</th>
                                        <td class="order">
                                            @switch($order->dispatch_status)
                                                @case('Pendiente')
                                                    <span class="badge badge-info"><i class="fas fa-arrows-rotate mr-2"></i>Pendiente</span>
                                                    @break
                                                @case('Cancelado')
                                                    <span class="badge badge-danger text-white" style="color:white !important;"><i class="fas fa-xmark mr-2 text-white"></i>Cancelado</span>
                                                    @break
                                                @case('Parcialmente Aprobado')
                                                    <span class="badge badge-warning text-white"><i class="fas fa-check mr-2 text-white"></i>Parcialmente Aprobado</span>
                                                    @break
                                                @case('Aprobado')
                                                    <span class="badge badge-success"><i class="fas fa-check-double mr-2"></i>Aprobado</span>
                                                    @break
                                                @case('Parcialmente Despachado')
                                                    <span class="badge badge-secondary text-white" style="color:white !important;"><i class="fas fa-share mr-2 text-white"></i>Parcialmente Despachado</span>
                                                    @break
                                                @case('Despachado')
                                                    <span class="badge badge-primary"><i class="fas fa-share-all mr-2"></i>Despachado</span>
                                                    @break
                                                @default
                                                    <span class="badge badge-info"><i class="fas fa-arrows-rotate mr-2"></i>Pendiente</span>
                                            @endswitch
                                        </td>
                                    </tr>
                                    <tr>
                                        <th class="order">OBSERVACION VENDEDOR:</th>
                                        <td class="order">{{ $order->seller_observation }}</td>
                                        <th class="order">VEN. OFC - DCO:</th>
                                        <td class="order">{{ $order->seller_dispatch_official . ' % - ' . $order->seller_dispatch_document . ' %' }}</td>
                                        <th class="order">VENDEDOR:</th>
                                        <td class="order">{{ strtoupper($order->seller_user->name . ' ' . $order->seller_user->last_name) }}</td>
                                    </tr>
                                    <tr>
                                        <th class="order">OBSERVACION CARTERA:</th>
                                        @if ($order->seller_status == 'Aprobado' && in_array($order->wallet_status, ['Pendiente', 'Parcialmente Aprobado']) && in_array(Auth::user()->title, ['SUPER ADMINISTRADOR', 'ADMINISTRADOR', 'CARTERA']))
                                            <td>
                                                <div class="form-group c_form_group">
                                                    <textarea class="form-control" id="wallet_observation_c" name="wallet_observation_c" cols="30" rows="3">{{ $order->wallet_observation }}</textarea>
                                                </div>
                                            </td>
                                        @else
                                            <td class="order">{{ $order->wallet_observation }}</td>
                                        @endif
                                        <th class="order">CAR. OFC - DCO:</th>
                                        @if ($order->seller_status == 'Aprobado' && in_array($order->wallet_status, ['Pendiente', 'Parcialmente Aprobado']) && in_array(Auth::user()->title, ['SUPER ADMINISTRADOR', 'ADMINISTRADOR', 'CARTERA']))
                                            <td class="order">
                                                <div class="row">
                                                    <div class="col-lg-6">
                                                        <div class="form-group c_form_group">
                                                            <label for="wallet_dispatch_official_c">OFC</label>
                                                            <input type="number" class="form-control" name="wallet_dispatch_official_c" id="wallet_dispatch_official_c" pattern="[0-9]+" value="{{ $order->wallet_dispatch_official ?? $order->seller_dispatch_official }}">
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-6">
                                                        <div class="form-group c_form_group">
                                                            <label for="wallet_dispatch_document_c">DCO</label>
                                                            <input type="number" class="form-control" name="wallet_dispatch_document_c" id="wallet_dispatch_document_c" pattern="[0-9]+" value="{{ $order->wallet_dispatch_document ?? $order->seller_dispatch_document }}">
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                        @else
                                            <td class="order">{{ is_null($order->wallet_dispatch_official) || is_null($order->wallet_dispatch_document) ? '-' : $order->wallet_dispatch_official . ' % - ' . $order->wallet_dispatch_document . ' %' }}</td>
                                        @endif
                                        @if ($order->seller_status == 'Aprobado' && in_array($order->wallet_status, ['Pendiente', 'Parcialmente Aprobado']) && in_array(Auth::user()->title, ['SUPER ADMINISTRADOR', 'ADMINISTRADOR', 'CARTERA']))
                                            <th class="order text-center" colspan="2">
                                                <button type="button" class="btn btn-primary" id="ObservationOrderButton" onclick="ObservationOrder({{ $order->id }})" title="Guardar observacion y OFC - DCO cartera.">
                                                    <i class="fas fa-floppy-disk mr-2"></i> <b>GUARDAR OBSERVACION OFC - DCO</b>
                                                </button>
                                            </th>
                                        @else
                                            <th class="order">CARTERA:</th>
                                            <td class="order">{{ is_null($order->wallet_user) ? '-' : strtoupper($order->wallet_user->name . ' ' . $order->wallet_user->last_name) }}</td>
                                        @endif
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
                DETALLES DEL PEDIDO
            </div>
            <div class="col-12">
                <div class="card mt-2">
                    <div class="card-header p-2">
                        <ul class="nav nav-pills">
                            @if((in_array($order->seller_status, ['Pendiente', 'Aprobado']) && in_array($order->wallet_status, ['Pendiente', 'Parcialmente Aprobado', 'Suspendido', 'En mora', 'Aprobado'])) && (in_array(Auth::user()->title, ['SUPER ADMINISTRADOR', 'ADMINISTRADOR', 'CARTERA', 'VENDEDOR']) || $order->seller_user_id == Auth::user()->id))
                                <li class="nav-item ml-auto">
                                </li>
                                @if($order->order_details->where('status', 'Cancelado')->count() > 0 && in_array($order->seller_status, ['Pendiente', 'Aprobado']) && in_array($order->wallet_status, ['Pendiente']))
                                <li class="nav-item ml-2">
                                    <a class="nav-link active bg-info" type="button" onclick="PendingOrderDetails()" title="Devolver detalles de pedido.">
                                        <i class="fas fa-arrows-rotate mr-2"></i> <b>DEVOLVER</b>
                                    </a>
                                </li>
                                @endif
                                @if($order->order_details->whereIn('status', ['Agotado'])->count() > 0 && in_array($order->seller_status, ['Aprobado']) && in_array($order->wallet_status, ['Pendiente', 'Parcialmente Aprobado', 'Aprobado']) && !in_array($order->dispatch_status, ['Despachado']) && in_array(Auth::user()->title, ['SUPER ADMINISTRADOR', 'ADMINISTRADOR', 'CARTERA']))
                                <li class="nav-item ml-2">
                                    <a class="nav-link active bg-warning" style="color: white !important;" type="button" onclick="AllowOrderDetails()" title="Permitir detalles de pedido.">
                                        <i class="fas fa-key-skeleton mr-2"></i> <b>PERMITIR</b>
                                    </a>
                                </li>
                                @endif
                                @if($order->order_details->whereIn('status', ['Pendiente', 'Cancelado', 'Suspendido'])->count() > 0 && in_array($order->wallet_status, ['Parcialmente Aprobado', 'Aprobado']) && in_array(Auth::user()->title, ['SUPER ADMINISTRADOR', 'ADMINISTRADOR', 'CARTERA']))
                                <li class="nav-item ml-2">
                                    <a class="nav-link active bg-success" type="button" onclick="ApproveOrderDetails()" title="Aprobar detalles de pedido.">
                                        <i class="fas fa-check mr-2"></i> <b>APROBAR</b>
                                    </a>
                                </li>
                                @endif
                                @if($order->order_details->whereIn('status', ['Pendiente', 'Suspendido', 'Aprobado'])->count() > 0)
                                <li class="nav-item ml-2">
                                    <a class="nav-link active bg-danger" type="button" onclick="CancelOrderDetails()" title="Cancelar detalles de pedido.">
                                        <i class="fas fa-xmark mr-2"></i> <b>CANCELAR</b>
                                    </a>
                                </li>
                                @endif
                                @if($order->order_details->whereIn('status', ['Pendiente', 'Aprobado'])->count() > 0 && in_array($order->wallet_status, ['Pendiente', 'Parcialmente Aprobado', 'Aprobado', 'Suspendido', 'En mora']) && in_array(Auth::user()->title, ['SUPER ADMINISTRADOR', 'ADMINISTRADOR', 'CARTERA']))
                                <li class="nav-item ml-2">
                                    <a class="nav-link active bg-secondary" type="button" onclick="SuspendOrderDetails()" title="Suspender detalles de pedido.">
                                        <i class="fas fa-clock-rotate-left mr-2"></i> <b>SUSPENDER</b>
                                    </a>
                                </li>
                                @endif
                                @if(in_array($order->seller_status, ['Pendiente']))
                                <li class="nav-item ml-2">
                                    <a class="nav-link active" type="button" onclick="CreateOrderDetailModal()" title="Agregar detalle de pedido.">
                                        <i class="fas fa-plus mr-2"></i> <b>AGREGAR</b>
                                    </a>
                                </li>
                                @endif
                            @endif
                        </ul>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive" id="orderDetails">
                            <table id="details" class="table table-bordered table-hover dataTable dtr-inline nowrap w-100">
                                <thead id="OrderDetailHead" style="background-color: #343a40; color: white;">
                                </thead>
                                <tbody id="OrderDetailBody">
                                </tbody>
                                <tfoot id="OrderDetailFoot" style="background-color: #343a40; color: white;">
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @include('Dashboard.OrderDetails.Create')
    @include('Dashboard.OrderDetails.Edit')
    @include('Dashboard.OrderDetails.Clone')
</section>
@endsection
@section('script')
    <script src="{{ asset('js/Dashboard/OrderDetails/Index.js') }}"></script>
    <script src="{{ asset('js/Dashboard/OrderDetails/Create.js') }}"></script>
    <script src="{{ asset('js/Dashboard/OrderDetails/Edit.js') }}"></script>
    <script src="{{ asset('js/Dashboard/OrderDetails/Clone.js') }}"></script>
    <script src="{{ asset('js/Dashboard/OrderDetails/Pending.js') }}"></script>
    <script src="{{ asset('js/Dashboard/OrderDetails/Authorize.js') }}"></script>
    <script src="{{ asset('js/Dashboard/OrderDetails/Approve.js') }}"></script>
    <script src="{{ asset('js/Dashboard/OrderDetails/Allow.js') }}"></script>
    <script src="{{ asset('js/Dashboard/OrderDetails/Cancel.js') }}"></script>
    <script src="{{ asset('js/Dashboard/OrderDetails/Suspend.js') }}"></script>

    <script src="{{ asset('js/Dashboard/Orders/Assent.js') }}"></script>
    <script src="{{ asset('js/Dashboard/Orders/Observation.js') }}"></script>
    <script src="{{ asset('js/Dashboard/Orders/Pending.js') }}"></script>
    <script src="{{ asset('js/Dashboard/Orders/Dispatch.js') }}"></script>
    <script src="{{ asset('js/Dashboard/Orders/PartiallyApprove.js') }}"></script>
    <script src="{{ asset('js/Dashboard/Orders/Authorize.js') }}"></script>
    <script src="{{ asset('js/Dashboard/Orders/Approve.js') }}"></script>
    <script src="{{ asset('js/Dashboard/Orders/Cancel.js') }}"></script>
    <script src="{{ asset('js/Dashboard/Orders/Decline.js') }}"></script>
    <script src="{{ asset('js/Dashboard/Orders/Suspend.js') }}"></script>
    <script src="{{ asset('js/Dashboard/Orders/Delay.js') }}"></script>
@endsection
