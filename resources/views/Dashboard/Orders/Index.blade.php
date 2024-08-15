@extends('Templates.Dashboard')
@section('content')
    <section class="content">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0 text-dark">PEDIDOS</h1>
                    </div><!-- /.col -->
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item">Dashboard</li>
                            <li class="breadcrumb-item">Orders</li>
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
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header p-2">
                            <ul class="nav nav-pills">
                                @if(in_array(Auth::user()->title, ['SUPER ADMINISTRADOR', 'ADMINISTRADOR', 'CARTERA', 'FILTRADOR']))
                                    <li class="nav-item">
                                        <a class="nav-link active" type="button" onclick="CreateOrderModal()" title="Agregar pedido.">
                                            <i class="fas fa-plus"></i>
                                        </a>
                                    </li>
                                @endif
                            </ul>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table id="orders" class="table table-bordered table-hover dataTable dtr-inline nowrap w-100">
                                    <thead class="thead-dark">
                                        <tr>
                                            <th></th>
                                            <th>#</th>
                                            <th>NIT</th>
                                            <th>NOMBRE</th>
                                            <th>CIUDAD</th>
                                            <th>DIRECCION</th>
                                            <th>CREACION</th>
                                            <th>CIERRE</th>
                                            <th>VENDEDOR</th>
                                            <th>ESTADO</th>
                                            <th>REVISION</th>
                                            <th>DESPACHO</th>
                                            <th>CORRERIA</th>
                                            <th>ACCIONES</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @include('Dashboard.Orders.Create')
        @include('Dashboard.Orders.Edit')
        @include('Dashboard.Clients.Data')
    </section>
@endsection
@section('script')
    <script src="{{ asset('js/Dashboard/Orders/DataTableIndex.js') }}"></script>
    <script src="{{ asset('js/Dashboard/Orders/Create.js') }}"></script>
    <script src="{{ asset('js/Dashboard/Orders/Edit.js') }}"></script>
    <script src="{{ asset('js/Dashboard/Orders/Assent.js') }}"></script>
    <script src="{{ asset('js/Dashboard/Orders/Cancel.js') }}"></script>
    
    <script src="{{ asset('js/Dashboard/Clients/Data.js') }}"></script>
@endsection
