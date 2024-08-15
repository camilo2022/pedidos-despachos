@extends('Templates.Dashboard')
@section('content')
    <section class="content">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0 text-dark">PRODUCTOS</h1>
                    </div><!-- /.col -->
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item">Dashboard</li>
                            <li class="breadcrumb-item">Products</li>
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
                                <li class="nav-item ml-auto">
                                    <form action="{{ route('Dashboard.Products.Download') }}" method="POST" name="DownloadProducts">
                                        @csrf
                                        <a class="nav-link active" type="button" onclick="DownloadProduct()" title="Descargar productos">
                                            <i class="fas fa-download"></i>
                                        </a>
                                    </form>
                                </li>
                            </ul>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table id="products" class="table table-bordered table-hover dataTable dtr-inline w-100">
                                    <thead class="thead-dark">
                                        <tr>
                                            <th></th>
                                            <th>#</th>
                                            <th>CODIGO</th>
                                            <th>CATEGORIA</th>
                                            <th>MARCA</th>
                                            <th>PRECIO</th>
                                            <th>DESCRIPCION</th>
                                            <th>BODEGAS</th>
                                            <th>COLORES</th>
                                            <th>TALLAS</th>
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
        @include('Dashboard.Products.Show')
    </section>
@endsection
@section('script')
    <script src="{{ asset('js/Dashboard/Products/DataTableIndex.js') }}"></script>
    <script src="{{ asset('js/Dashboard/Products/Show.js') }}"></script>
    <script src="{{ asset('js/Dashboard/Products/Delete.js') }}"></script>
    <script src="{{ asset('js/Dashboard/Products/Restore.js') }}"></script>
    <script src="{{ asset('js/Dashboard/Products/Download.js') }}"></script>
    <script src="{{ asset('js/Dashboard/Products/Sync.js') }}"></script>
@endsection
