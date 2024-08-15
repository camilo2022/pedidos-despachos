@extends('Templates.Dashboard')
@section('content')
    <section class="content">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0 text-dark">ORDENES DE DESPACHO</h1>
                    </div><!-- /.col -->
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item">Dashboard</li>
                            <li class="breadcrumb-item">Dispatches</li>
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
                            </ul>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table id="orderDispatches" class="table table-bordered table-hover dataTable dtr-inline nowrap w-100">
                                    <thead class="thead-dark">
                                        <tr>
                                            <th></th>
                                            <th>#</th>
                                            <th>CONSECUTIVO</th>
                                            <th>NIT</th>
                                            <th>NOMBRE</th>
                                            <th>CIUDAD</th>
                                            <th>DIRECCION</th>
                                            <th>CREACION</th>
                                            <th>FECHA</th>
                                            <th>FILTRADOR</th>
                                            <th>ESTADO</th>
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
        @include('Dashboard.OrderDispatches.Invoice')
    </section>
@endsection
@section('script')
<script src="{{ asset('js/Dashboard/OrderDispatches/DataTableIndex.js') }}"></script>
<script src="{{ asset('js/Dashboard/OrderDispatches/Approve.js') }}"></script>
<script src="{{ asset('js/Dashboard/OrderDispatches/Cancel.js') }}"></script>
<script src="{{ asset('js/Dashboard/OrderDispatches/Pending.js') }}"></script>
<script src="{{ asset('js/Dashboard/OrderDispatches/Picking.js') }}"></script>
<script src="{{ asset('js/Dashboard/OrderDispatches/Packing.js') }}"></script>
<script src="{{ asset('js/Dashboard/OrderDispatches/Invoice.js') }}"></script>
@endsection
