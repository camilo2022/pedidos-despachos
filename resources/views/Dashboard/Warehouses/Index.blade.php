@extends('Templates.Dashboard')
@section('content')
    <section class="content">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0 text-dark">Bodegas</h1>
                    </div><!-- /.col -->
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item">Dashboard</li>
                            <li class="breadcrumb-item">Warehouses</li>
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
                                <li class="nav-item">
                                    <a class="nav-link active" type="button" onclick="CreateWarehouseModal()" title="Agregar bodega.">
                                        <i class="fas fa-plus"></i>
                                    </a>
                                </li>
                                <li class="nav-item ml-auto">
                                    <a class="nav-link active" type="button" onclick="SyncWarehouse()" title="Sincronizar bodegas.">
                                        <i class="fas fa-rotate"></i>
                                    </a>
                                </li>
                            </ul>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table id="warehouses" class="table table-bordered table-hover dataTable dtr-inline w-100">
                                    <thead class="thead-dark">
                                        <tr>
                                            <th>#</th>
                                            <th>NOMBRE</th>
                                            <th>CODIGO</th>
                                            <th>CORTE ORIGINAL</th>
                                            <th>TRANSITORIAS</th>
                                            <th>PEDIDOS - FILTRO</th>
                                            <th>PEDIDOS ESPECIAL</th>
                                            <th>ESTADO</th>
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
        @include('Dashboard.Warehouses.Create')
        @include('Dashboard.Warehouses.Edit')
    </section>
@endsection
@section('script')
    <script src="{{ asset('js/Dashboard/Warehouses/DataTableIndex.js') }}"></script>
    <script src="{{ asset('js/Dashboard/Warehouses/Create.js') }}"></script>
    <script src="{{ asset('js/Dashboard/Warehouses/Edit.js') }}"></script>
    <script src="{{ asset('js/Dashboard/Warehouses/Delete.js') }}"></script>
    <script src="{{ asset('js/Dashboard/Warehouses/Restore.js') }}"></script>
    <script src="{{ asset('js/Dashboard/Warehouses/Sync.js') }}"></script>
@endsection
