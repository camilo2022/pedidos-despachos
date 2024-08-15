@extends('Templates.Dashboard')
@section('content')
    <section class="content">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0 text-dark">REPORTE DE VENTAS</h1>
                    </div><!-- /.col -->
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item">Dashboard</li>
                            <li class="breadcrumb-item">Reports</li>
                            <li class="breadcrumb-item">Sales</li>
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
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table id="sales" class="table table-bordered table-hover dataTable dtr-inline nowrap w-100">
                                    <thead class="thead-dark">
                                        <tr>
                                            <th>TIPO</th>
                                            <th>PEDIDO</th>
                                            <th>AMARRADOR</th>
                                            <th>CLIENTE</th>
                                            <th>DOCUMENTO</th>
                                            <th>DIRECCION</th>
                                            <th>DEPARTAMENTO</th>
                                            <th>CIUDAD</th>
                                            <th>ZONA</th>
                                            <th>TIPO DESPACHO</th>
                                            <th>FECHA DESPACHAR</th>
                                            <th>MARCA</th>
                                            <th>CATEGORIA</th>
                                            <th>DESCRIPCION</th>
                                            <th>REFERENCIA</th>
                                            <th>COLOR</th>
                                            @foreach($sizes as $size)
                                            <th>{{ "T{$size->code}" }}</th>
                                            @endforeach
                                            <th>TOTAL</th>
                                            <th>FECHA DETALLE VENDEDOR</th>
                                            <th>OBSERVACION DETALLE VENDEDOR</th>
                                            <th>USUARIO DETALLE CARTERA</th>
                                            <th>FECHA DETALLE CARTERA</th>
                                            <th>USUARIO DETALLE FILTRADOR</th>
                                            <th>FECHA DETALLE FILTRADOR</th>
                                            <th>DETALLE ESTADO</th>
                                            <th>USUARIO VENDEDOR</th>
                                            <th>ESTADO VENDEDOR</th>
                                            <th>FECHA VENDEDOR</th>
                                            <th>OBSERVACION VENDEDOR</th>
                                            <th>OFICIAL VENDEDOR</th>
                                            <th>DOCUMENTO VENDEDOR</th>
                                            <th>USUARIO CARTERA</th>
                                            <th>ESTADO CARTERA</th>
                                            <th>FECHA CARTERA</th>
                                            <th>OBSERVACION CARTERA</th>
                                            <th>OFICIAL CARTERA</th>
                                            <th>DOCUMENTO CARTERA</th>                                            
                                            <th>ESTADO DESPACHO</th>
                                            <th>FECHA DESPACHO</th>
                                            <th>CORRERIA</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                    <tfoot class="thead-dark">
                                        <tr>
                                            <th>TIPO</th>
                                            <th>PEDIDO</th>
                                            <th>AMARRADOR</th>
                                            <th>CLIENTE</th>
                                            <th>DOCUMENTO</th>
                                            <th>DIRECCION</th>
                                            <th>DEPARTAMENTO</th>
                                            <th>CIUDAD</th>
                                            <th>ZONA</th>
                                            <th>TIPO DESPACHO</th>
                                            <th>FECHA DESPACHAR</th>
                                            <th>MARCA</th>
                                            <th>CATEGORIA</th>
                                            <th>DESCRIPCION</th>
                                            <th>REFERENCIA</th>
                                            <th>COLOR</th>
                                            @foreach($sizes as $size)
                                            <th></th>
                                            @endforeach
                                            <th></th>
                                            <th>FECHA DETALLE VENDEDOR</th>
                                            <th>OBSERVACION DETALLE VENDEDOR</th>
                                            <th>USUARIO DETALLE CARTERA</th>
                                            <th>FECHA DETALLE CARTERA</th>
                                            <th>USUARIO DETALLE FILTRADOR</th>
                                            <th>FECHA DETALLE FILTRADOR</th>
                                            <th>DETALLE ESTADO</th>
                                            <th>USUARIO VENDEDOR</th>
                                            <th>ESTADO VENDEDOR</th>
                                            <th>FECHA VENDEDOR</th>
                                            <th>OBSERVACION VENDEDOR</th>
                                            <th>OFICIAL VENDEDOR</th>
                                            <th>DOCUMENTO VENDEDOR</th>
                                            <th>USUARIO CARTERA</th>
                                            <th>ESTADO CARTERA</th>
                                            <th>FECHA CARTERA</th>
                                            <th>OBSERVACION CARTERA</th>
                                            <th>OFICIAL CARTERA</th>
                                            <th>DOCUMENTO CARTERA</th>                                            
                                            <th>ESTADO DESPACHO</th>
                                            <th>FECHA DESPACHO</th>
                                            <th>CORRERIA</th>
                                        </tr>
                                    </tfoot>
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
    <script src="{{ asset('js/Dashboard/Reports/DataTableIndexSales.js') }}"></script>
@endsection
