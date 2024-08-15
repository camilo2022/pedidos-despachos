@extends('Templates.Dashboard')
@section('content')
<link rel="stylesheet" href="{{ asset('css/plugins/filter/filter.css') }}">
    <section class="content">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0 text-dark"></h1>
                    </div><!-- /.col -->
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item">Dashboard</li>
                            <li class="breadcrumb-item">Filters</li>
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
            <div class="card">
                <div class="card-header text-center" style="background-color: #343a40; color:white; font-weigth:bold;">
                    FILTRADO DE REFERENCIAS
                </div>
                <div class="col-12">
                    <div class="card mt-2">
                        <div class="card-header p-2">
                            <ul class="nav nav-pills">
                                <li class="nav-item text-center">
                                    <div class="alert alert-info" id="siesa" style="color: #ffffff; width: 150px !important; height: 38px; padding: 3px; font-size: 20px;">
                                        <b>SIESA</b>
                                    </div>
                                </li>
                                <li class="nav-item ml-2 text-center">
                                    <div class="alert alert-info" id="tns" style="color: #ffffff; width: 150px !important; height: 38px; padding: 3px; font-size: 20px;">
                                        <b>VISUAL TNS</b>
                                    </div>
                                </li>
                                <li class="nav-item ml-2 text-center">
                                    <div class="alert alert-info" id="bmi" style="color: #ffffff; width: 150px !important; height: 38px; padding: 3px; font-size: 20px;">
                                        <b>BMI</b>
                                    </div>
                                </li>
                                <li class="nav-item ml-2 text-center">
                                    <div class="alert alert-info" style="color: #ffffff; width: 150px !important; height: 38px; padding: 3px; font-size: 20px;">
                                        <label id="currentPositionReference">x</label> <b>/</b> <label id="totalPositionReference">x</label>
                                    </div>
                                </li>
                                <li class="nav-item ml-auto">
                                    <a class="btn btn-primary text-white" type="button" data-toggle="modal" data-target="#GraficFilterModal" onclick="IndexFilterGraficReference()" title="Graficar referencia." id="graficReference">
                                        <i class="fas fa-chart-line mr-2"></i> <b>GRAFICAR</b>
                                    </a>
                                </li>
                                <li class="nav-item ml-2">
                                    <a class="btn btn-success text-white" type="button" onclick="IndexFilterReference()" title="Filtrar referencia." id="filterReference">
                                        <i class="fas fa-filters mr-2"></i> <b>FILTRAR</b>
                                    </a>
                                </li>
                                <li class="nav-item ml-2">
                                    <a class="btn btn-warning text-white" type="button" data-toggle="modal" data-target="#PrioritizeFilterModal" title="Priorizar referencia." id="prioritizeReference">
                                        <i class="fas fa-magnifying-glass mr-2"></i> <b>PRIORIZAR</b>
                                    </a>
                                </li>
                                <li class="nav-item ml-2">
                                    <div class="btn-group" role="group" aria-label="Basic example">
                                        <a class="btn btn-secondary text-white" type="button" onclick="IndexFilterBeforeReference()" title="Referencia anterior." id="beforeReference">
                                            <i class="fas fa-arrow-left mr-2"></i> <b>ANTERIOR</b>
                                        </a>
                                        <a class="btn btn-secondary text-white" type="button" onclick="IndexFilterAfterReference()" title="Referencia siguiente." id="afterReference">
                                            <i class="fas fa-arrow-right mr-2"></i> <b>SIGUIENTE</b>
                                        </a>
                                    </div>
                                </li>
                                <li class="nav-item ml-2">
                                    <a class="btn btn-danger text-white" type="button" data-toggle="modal" data-target="#FilteredFilterModal" title="Listado referencias filtradas." id="listReference">
                                        <i class="fas fa-list mr-2"></i> <b>REFERENCIAS</b>
                                    </a>
                                </li>
                                <li class="nav-item ml-2">
                                    <a class="btn btn-info text-white" type="button" data-toggle="modal" data-target="#UploadFilterModal" title="Cargar corte inicial." id="cuttedReference">
                                        <i class="fas fa-cut mr-2"></i> <b>CORTE</b>
                                    </a>
                                </li>
                            </ul>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-12 center">
                                    <div class="information text-center">
                                        <span id="reference" class="reference" data-id="">-</span>
                                        <div class="row">
                                            <span id="referenceDescription" class="desc text-center">-</span>
                                        </div>
                                    </div>
                                    <div class="information text-center">
                                        <span id="color" class="color" data-id="">-</span>
                                        <div class="row">
                                            <span id="colorDescription" class="desc text-center">-</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-12">
                    <div class="card mt-2">
                        <div class="card-header text-center" style="background-color: #343a40; color:white; font-weigth:bold;">
                            LISTADOS DE PEDIDOS PARA FILTRAR
                        </div>
                        <div class="card-body">
                            <div id="loading">
                                <img src="{{ asset('images/cargando.gif') }}" alt="CARGANDO...">
                            </div>
                            <div class="table-responsive" id="table" style="display: none;">
                                <div class="row">
                                    <table id="tableExistencia" class="table text-center tableExistencia">
                                        <thead style="background-color: #343a40; color:white; font-weigth:bold;">
                                            <tr>
                                                <th colspan="4">BODEGAS</th>
                                                <th>04</th>
                                                <th>06</th>
                                                <th>08</th>
                                                <th>10</th>
                                                <th>12</th>
                                                <th>14</th>
                                                <th>16</th>
                                                <th>18</th>
                                                <th>20</th>
                                                <th>22</th>
                                                <th>24</th>
                                                <th>26</th>
                                                <th>28</th>
                                                <th>30</th>
                                                <th>32</th>
                                                <th>34</th>
                                                <th>36</th>
                                                <th>38</th>
                                                <th>XXS</th>
                                                <th>XS</th>
                                                <th>S</th>
                                                <th>M</th>
                                                <th>L</th>
                                                <th>XL</th>
                                                <th>XXL</th>
                                                <th>TOTAL</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr id="columnsTransit">
                                                
                                            </tr>
                                            <tr id="columnsCut">
                                                
                                            </tr>
                                            <tr id="columnsDiscount">
                                                
                                            </tr>
                                            <tr id="columnsCommitted">
                                                
                                            </tr>
                                            <tr id="columnsAvailabled">
                                                
                                            </tr>
                                            <tr id="columnsFiltered">
                                                
                                            </tr>
                                        </tbody>
                                        <tfoot>
                                            <tr id="columnsPercentage">
                                                
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                                <div class="row">
                                    <table id="tableClientes" class="table text-center tableClientes">
                                        <thead style="background-color: #343a40; color:white; font-weigth:bold;">
                                            <tr>
                                                <th>OD</th>
                                                <th>PED</th>
                                                <th>CLIENTE</th>
                                                <th>OBSERVACIONES</th>
                                                <th>04</th>
                                                <th>06</th>
                                                <th>08</th>
                                                <th>10</th>
                                                <th>12</th>
                                                <th>14</th>
                                                <th>16</th>
                                                <th>18</th>
                                                <th>20</th>
                                                <th>22</th>
                                                <th>24</th>
                                                <th>26</th>
                                                <th>28</th>
                                                <th>30</th>
                                                <th>32</th>
                                                <th>34</th>
                                                <th>36</th>
                                                <th>38</th>
                                                <th>XXS</th>
                                                <th>XS</th>
                                                <th>S</th>
                                                <th>M</th>
                                                <th>L</th>
                                                <th>XL</th>
                                                <th>XXL</th>
                                                <th>TOTAL</th>
                                            </tr>
                                        </thead>
                                        <tbody id="bodyClients">
                                        </tbody>
                                        <tfoot id="footClients" style="background-color: #343a40; color:white; font-weigth:bold;">
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @include('Dashboard.Filters.Prioritize')
        @include('Dashboard.Filters.Grafic')
        @include('Dashboard.Filters.Filtered')
        @include('Dashboard.Filters.Upload')
    </section>
@endsection
@section('script')
    <script src="{{ asset('js/Dashboard/Filters/Index.js') }}"></script>
    <script src="{{ asset('js/Dashboard/Filters/Upload.js') }}"></script>
@endsection
