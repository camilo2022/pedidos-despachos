@extends('Templates.Dashboard')
@section('content')
    <section class="content">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0 text-dark">LIBRANZAS</h1>
                    </div><!-- /.col -->
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item">Dashboard</li>
                            <li class="breadcrumb-item">Libranzas</li>
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
                                @if(in_array(Auth::user()->title, ['SUPER ADMINISTRADOR', 'ADMINISTRADOR', 'TIENDAS']))
                                    <li class="nav-item ml-auto">
                                        <a class="nav-link active" type="button" onclick="CreateOrderModal()" title="Agregar libranza.">
                                            <i class="fas fa-plus"></i>
                                        </a>
                                    </li>
                                @endif
                            </ul>
                        </div>
                        <div class="container-fluid text-center">
                            <div class="row">
                                <div class="col-lg-4 col-md-4 col-12 col-xs-12">
                                    <i class="fas fa-dollar-sign" style="color: rgb(0, 123, 255); font-size: 2.5em !important;"></i>
                                    <strong style="color: rgb(0, 123, 255); font-size: 2.5em !important;" id="total">0</strong>
                                    <small>Total</small>
                                </div>
                                <div class="col-lg-4 col-md-4 col-12 col-xs-12">
                                    <i class="fas fa-dollar-sign" style="color: rgb(112, 225, 1); font-size: 2.5em !important;"></i>
                                    <strong style="color: rgb(112, 225, 1); font-size: 2.5em !important;" id="pay">0</strong>
                                    <small>Pagados</small>
                                </div>
                                <div class="col-lg-4 col-md-4 col-12 col-xs-12">
                                    <i class="fas fa-dollar-sign" style="color: rgb(255, 0, 0); font-size: 2.5em !important;"></i>
                                    <strong style="color: rgb(255, 0, 0); font-size: 2.5em !important;" id="debt">0</strong>
                                    <small>Deudas</small>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table id="orders" class="table table-bordered table-hover dataTable dtr-inline nowrap w-100">
                                    <thead class="thead-dark">
                                        <tr>
                                            <th>#</th>
                                            <th>FACTURA</th>
                                            <th>DOCUMENTO</th>
                                            <th>EMPLEADO</th>
                                            <th>FECHA</th>
                                            <th>TIENDA</th>
                                            <th>MONTO</th>
                                            <th>ABONADO</th>
                                            <th>DEUDA</th>
                                            <th>CUOTAS</th>
                                            <th>PAGOS</th>
                                            <th>VALOR CUOTA</th>
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
    </section>
@endsection
@section('script')
    <script src="{{ asset('js/Dashboard/Libranzas/DataTableIndex.js') }}"></script>
@endsection
