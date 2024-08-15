@extends('Templates.Dashboard')
@section('content')
    <section class="content">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0 text-dark">Usuarios</h1>
                    </div><!-- /.col -->
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item">Dashboard</li>
                            <li class="breadcrumb-item">Users</li>
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
                                    <a class="nav-link active" type="button" onclick="CreateUserModal()" title="Agregar usuario">
                                        <i class="fas fa-user-plus"></i>
                                    </a>
                                </li>
                            </ul>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table id="users" class="table table-bordered table-hover dataTable dtr-inline nowrap w-100">
                                    <thead class="thead-dark">
                                        <tr>
                                            <th colspan="11">INFORMACION PERSONAL</th>
                                            <th colspan="5">GESTIONAR USUARIO</th>
                                            <th colspan="2">ROLES Y PERMISOS</th>
                                        </tr>
                                        <tr>
                                            <th>#</th>
                                            <th>NOMBRES</th>
                                            <th>APELLIDOS</th>
                                            <th>DOCUMENTO</th>
                                            <th>TELEFONO</th>
                                            <th>DIRECCION</th>
                                            <th>CORREO ELECTRONICO</th>
                                            <th>TITULO</th>
                                            <th>ZONA</th>
                                            <th>SUCURSAL</th>
                                            <th>ESTADO</th>
                                            <th>BODEGA</th>
                                            <th>CONTRASEÑA</th>
                                            <th>EDITAR</th>
                                            <th>ELIMINAR</th>
                                            <th>RESTAURAR</th>
                                            <th>ASIGNAR</th>
                                            <th>REMOVER</th>
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
        @include('Dashboard.Users.Create')
        @include('Dashboard.Users.Edit')
        @include('Dashboard.Users.Password')
        @include('Dashboard.Users.AssignRoleAndPermission')
        @include('Dashboard.Users.RemoveRoleAndPermission')
        @include('Dashboard.Users.Warehouses')
    </section>
@endsection
@section('script')
    <script src="{{ asset('js/Dashboard/Users/DataTableIndex.js') }}"></script>
    <script src="{{ asset('js/Dashboard/Users/Create.js') }}"></script>
    <script src="{{ asset('js/Dashboard/Users/Edit.js') }}"></script>
    <script src="{{ asset('js/Dashboard/Users/Password.js') }}"></script>
    <script src="{{ asset('js/Dashboard/Users/Delete.js') }}"></script>
    <script src="{{ asset('js/Dashboard/Users/Restore.js') }}"></script>
    <script src="{{ asset('js/Dashboard/Users/AssignRoleAndPermissions.js') }}"></script>
    <script src="{{ asset('js/Dashboard/Users/RemoveRoleAndPermissions.js') }}"></script>
    <script src="{{ asset('js/Dashboard/Users/Warehouses.js') }}"></script>
@endsection
