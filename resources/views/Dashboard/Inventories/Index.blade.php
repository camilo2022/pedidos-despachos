@extends('Templates.Dashboard')
@section('content')
    <section class="content">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0 text-dark">INVENTARIOS</h1>
                    </div><!-- /.col -->
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item">Dashboard</li>
                            <li class="breadcrumb-item">Inventories</li>
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
                                    <a class="nav-link active" type="button" onclick="SyncInventory()" title="Sincronizar inventarios SIESA y TNS.">
                                        <i class="fas fa-rotate"></i>
                                    </a>
                                </li>
                                @if(in_array(Auth::user()->title, ['SUPER ADMINISTRADOR', 'ADMINISTRADOR', 'COORDINADOR BODEGA', 'FILTRADOR']))
                                    <li class="nav-item ml-2">
                                        <a class="nav-link active" type="button" onclick="SyncBmiInventoryModal()" title="Sincronizar inventarios BMI.">
                                            <i class="fas fa-arrows-spin"></i>
                                        </a>
                                    </li>
                                    <li class="nav-item ml-2">
                                        <a class="nav-link active" type="button" onclick="UploadInventoryModal()" title="Cargar proyecciones.">
                                            <i class="fas fa-upload"></i>
                                        </a>
                                    </li>
                                @endif
                                <li class="nav-item ml-2">
                                    <form action="{{ route('Dashboard.Inventories.Download') }}" method="POST" name="DownloadInventories">
                                        @csrf
                                        <a class="nav-link active" type="button" onclick="DownloadInventory()" title="Descargar inventarios.">
                                            <i class="fas fa-download"></i>
                                        </a>
                                    </form>
                                </li>
                            </ul>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table id="inventories" class="table table-bordered table-hover dataTable dtr-inline nowrap w-100">
                                    <thead class="thead-dark">
                                        <tr>
                                            <th>BODEGA</th>
                                            <th>MARCA</th>
                                            <th>REFERENCIA</th>
                                            <th>COLOR</th>
                                            @foreach ($sizes as $size)
                                            <th>{{ $size->code }}</th>
                                            @endforeach
                                            <th>SISTEMA</th>
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
        @include('Dashboard.Inventories.Upload')
        @include('Dashboard.Inventories.SyncBmi')
    </section>
@endsection
@section('script')
    <script src="{{ asset('js/Dashboard/Inventories/DataTableIndex.js') }}"></script>
    <script src="{{ asset('js/Dashboard/Inventories/Upload.js') }}"></script>
    <script src="{{ asset('js/Dashboard/Inventories/Download.js') }}"></script>
    <script src="{{ asset('js/Dashboard/Inventories/SyncBmi.js') }}"></script>
    <script src="{{ asset('js/Dashboard/Inventories/Sync.js') }}"></script>
@endsection
