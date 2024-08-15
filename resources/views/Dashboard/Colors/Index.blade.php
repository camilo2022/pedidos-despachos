@extends('Templates.Dashboard')
@section('content')
    <section class="content">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0 text-dark">COLORES</h1>
                    </div><!-- /.col -->
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item">Dashboard</li>
                            <li class="breadcrumb-item">Colors</li>
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
                                    <a class="nav-link active" type="button" onclick="CreateColorModal()" title="Agregar color de producto.">
                                        <i class="fas fa-plus"></i>
                                    </a>
                                </li>
                                <li class="nav-item ml-auto">
                                    <a class="nav-link active" type="button" onclick="SyncColor()" title="Sincronizar colores.">
                                        <i class="fas fa-rotate"></i>
                                    </a>
                                </li>
                            </ul>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table id="colors" class="table table-bordered table-hover dataTable dtr-inline nowrap w-100">
                                    <thead class="thead-dark">
                                        <tr>
                                            <th>#</th>
                                            <th>NOMBRE</th>
                                            <th>CODIGO</th>
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
        @include('Dashboard.Colors.Create')
        @include('Dashboard.Colors.Edit')
    </section>
@endsection
@section('script')
    <script src="{{ asset('js/Dashboard/Colors/DataTableIndex.js') }}"></script>
    <script src="{{ asset('js/Dashboard/Colors/Create.js') }}"></script>
    <script src="{{ asset('js/Dashboard/Colors/Edit.js') }}"></script>
    <script src="{{ asset('js/Dashboard/Colors/Delete.js') }}"></script>
    <script src="{{ asset('js/Dashboard/Colors/Restore.js') }}"></script>
    <script src="{{ asset('js/Dashboard/Colors/Sync.js') }}"></script>
@endsection
