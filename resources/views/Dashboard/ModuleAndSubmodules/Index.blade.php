@extends('Templates.Dashboard')
@section('content')
    <section class="content">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0 text-dark">MODULOS Y SUBMODULOS</h1>
                    </div><!-- /.col -->
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item">Dashboard</li>
                            <li class="breadcrumb-item">ModulesAndSubmodules</li>
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
                                    <a class="nav-link active" type="button" onclick="CreateModuleAndSubmodulesModal()" title="Agregar modulo y submodulos">
                                        <i class="fas fa-shield-plus"></i>
                                    </a>
                                </li>
                            </ul>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table id="modulesAndSubmodules" class="table table-bordered table-hover dataTable dtr-inline w-100">
                                    <thead class="thead-dark">
                                        <tr>
                                            <th>#</th>
                                            <th>MODULO</th>
                                            <th>ICONO</th>
                                            <th>ROL DE ACCESO</th>
                                            <th>SUB MODULOS</th>
                                            <th>RUTAS</th>
                                            <th>ICONOS</th>
                                            <th>PERMISOS DE ACCESO</th>
                                            <th>EDITAR</th>
                                            <th>ELIMINAR</th>
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
        @include('Dashboard.ModuleAndSubmodules.Create')
        @include('Dashboard.ModuleAndSubmodules.Edit')
    </section>
@endsection
@section('script')
    <script src="{{ asset('js/Dashboard/ModuleAndSubmodules/DataTableIndex.js') }}"></script>
    <script src="{{ asset('js/Dashboard/ModuleAndSubmodules/Create.js') }}"></script>
    <script src="{{ asset('js/Dashboard/ModuleAndSubmodules/Edit.js') }}"></script>
    <script src="{{ asset('js/Dashboard/ModuleAndSubmodules/Delete.js') }}"></script>
    <script src="{{ asset('js/Dashboard/ModuleAndSubmodules/Suggestion.js') }}"></script>
@endsection
