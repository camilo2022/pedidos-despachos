@extends('Templates.Dashboard')
@section('content')
    <section class="content">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0 text-dark">CLIENTES</h1>
                    </div><!-- /.col -->
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item">Dashboard</li>
                            <li class="breadcrumb-item">Clients</li>
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
                                    <a class="nav-link active" type="button" onclick="CreateClientModal()" title="Agregar cliente.">
                                        <i class="fas fa-plus"></i>
                                    </a>
                                </li>
                                <li class="nav-item ml-auto">
                                    <a class="nav-link active" type="button" onclick="SyncClient()" title="Sincronizar clientes.">
                                        <i class="fas fa-rotate"></i>
                                    </a>
                                </li>
                                <li class="nav-item ml-2">
                                    <a class="nav-link active" type="button" onclick="UploadClientModal()" title="Cargar carteras clientes.">
                                        <i class="fas fa-upload"></i>
                                    </a>
                                </li>
                            </ul>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table id="clients" class="table table-bordered table-hover dataTable dtr-inline nowrap w-100">
                                    <thead class="thead-dark">
                                        <tr>
                                            <th>#</th>
                                            <th>RAZON SOCIAL</th>
                                            <th>DIRECCION</th>
                                            <th>DOCUMENTO</th>
                                            <th>TELEFONO</th>
                                            <th>CODIGO</th>
                                            <th>SUCURSAL</th>
                                            <th>DIRECCION DESPACHO</th>
                                            <th>TELEFONO</th>
                                            <th>DEPARTAMENTO</th>
                                            <th>CIUDAD</th>
                                            <th>TELEFONO</th>
                                            <th>CORREO</th>
                                            <th>ZONA</th>
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
        @include('Dashboard.Clients.Create')
        @include('Dashboard.Clients.Edit')
        @include('Dashboard.Clients.Data')
        @include('Dashboard.Clients.Wallet')
        @include('Dashboard.Clients.Upload')
    </section>
@endsection
@section('script')
<script src="{{ asset('js/Dashboard/Clients/DataTableIndex.js') }}"></script>
<script src="{{ asset('js/Dashboard/Clients/Create.js') }}"></script>
<script src="{{ asset('js/Dashboard/Clients/Edit.js') }}"></script>
<script src="{{ asset('js/Dashboard/Clients/Data.js') }}"></script>
<script src="{{ asset('js/Dashboard/Clients/Wallet.js') }}"></script>
<script src="{{ asset('js/Dashboard/Clients/Delete.js') }}"></script>
<script src="{{ asset('js/Dashboard/Clients/Restore.js') }}"></script>
<script src="{{ asset('js/Dashboard/Clients/Upload.js') }}"></script>
<script src="{{ asset('js/Dashboard/Clients/Sync.js') }}"></script>
@endsection
