@extends('Templates.Dashboard')
@section('content')
    <section class="content">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0 text-dark">EMPRESAS</h1>
                    </div><!-- /.col -->
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item">Dashboard</li>
                            <li class="breadcrumb-item">Businesses</li>
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
                                    <a class="nav-link active" type="button" onclick="CreateBusinessModal()" title="Agregar empresa.">
                                        <i class="fas fa-plus"></i>
                                    </a>
                                </li>
                            </ul>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table id="businesses" class="table table-bordered table-hover dataTable dtr-inline nowrap w-100">
                                    <thead class="thead-dark">
                                        <tr>
                                            <th>#</th>
                                            <th>NOMBRE</th>
                                            <th>SUCURSAL</th>
                                            <th>NIT</th>
                                            <th>PAIS</th>
                                            <th>DEPARTAMENTO</th>
                                            <th>CIUDAD</th>
                                            <th>DIRECCION</th>
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
        @include('Dashboard.Businesses.Create')
        @include('Dashboard.Businesses.Edit')
        @include('Dashboard.Businesses.Warehouses')
    </section>
@endsection
@section('script')
    <script src="{{ asset('js/Dashboard/Businesses/DataTableIndex.js') }}"></script>
    <script src="{{ asset('js/Dashboard/Businesses/Create.js') }}"></script>
    <script src="{{ asset('js/Dashboard/Businesses/Edit.js') }}"></script>
    <script src="{{ asset('js/Dashboard/Businesses/Delete.js') }}"></script>
    <script src="{{ asset('js/Dashboard/Businesses/Restore.js') }}"></script>
    <script src="{{ asset('js/Dashboard/Businesses/Warehouses.js') }}"></script>
@endsection
