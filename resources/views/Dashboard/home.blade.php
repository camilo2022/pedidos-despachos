@extends('Templates.Dashboard')
@section('content')
<div class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1 class="m-0 text-dark">Dashboard</h1>
        </div>
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item">Home</li>
            <li class="breadcrumb-item">Dashboard</li>
          </ol>
        </div>
      </div>
    </div>
</div>
<section class="content">
    <div class="container-fluid">
      
        <div class="row">
          <div class="col-lg-3 col-6">
            <div class="small-box bg-info">
              <div class="inner">
                <h3>{{ $orders }}</h3>
                <p><b>PEDIDOS</b></p>
              </div>
              <div class="icon">
                <i class="ion ion-bag"></i>
              </div>
              <a href="#" class="small-box-footer">Mas informacion <i class="fas fa-arrow-circle-right"></i></a>
            </div>
          </div>
          
          <div class="col-lg-3 col-6">
            <div class="small-box bg-success">
              <div class="inner">
                <h3>{{ $products }}</h3>
                <p><b>PRODUCTOS</b></p>
              </div>
              <div class="icon">
                <i class="ion ion-stats-bars"></i>
              </div>
              <a href="#" class="small-box-footer">Mas informacion <i class="fas fa-arrow-circle-right"></i></a>
            </div>
          </div>
          
          <div class="col-lg-3 col-6">
            <div class="small-box bg-warning">
              <div class="inner">
                <h3>{{ $users }}</h3>
                <p><b>USUARIOS</b></p>
              </div>
              <div class="icon">
                <i class="ion ion-person-add"></i>
              </div>
              <a href="#" class="small-box-footer">Mas informacion <i class="fas fa-arrow-circle-right"></i></a>
            </div>
          </div>
          
          <div class="col-lg-3 col-6">
            <div class="small-box bg-danger">
              <div class="inner">
                <h3>{{ $clients }}</h3>
                <p><b>CLIENTES</b></p>
              </div>
              <div class="icon">
                <i class="ion ion-pie-graph"></i>
              </div>
              <a href="#" class="small-box-footer">Mas informacion <i class="fas fa-arrow-circle-right"></i></a>
            </div>
          </div>
          
        </div>
        
        <div class="row">
          <section class="col-md-12 col-sm-12 col-lg-6 connectedSortable">
            
            <div class="card">
              <div class="card-header">
                <h3 class="card-title">
                  <i class="fas fa-chart-simple mr-1"></i>
                  <b>GRAFICA VENTAS VENDEDORES</b>
                </h3>
                <div class="card-tools">
                  
                </div>
              </div>
              <div class="card-body">
                <div class="tab-content p-0">
                  
                  <div class="chart tab-pane active" id="revenue-chart" style="position: relative; text-align: center; vertical-align: middle;">
                    <div class="table-responsive">
                      <img src="{{ $chartSellers }}" height="300px" style="width: auto%;">
                    </div>
                  </div>

                </div>
              </div>
            </div>

          </section>
          <section class="col-lg-3 col-md-6 col-sm-12 connectedSortable">
            
            <div class="card">
              <div class="card-header">
                <h3 class="card-title">
                  <i class="fas fa-chart-pie mr-1"></i>
                  <b>GRAFICA VENTAS MARCAS</b>
                </h3>
                <div class="card-tools">
                  
                </div>
              </div>
              <div class="card-body">
                <div class="tab-content p-0">
                  
                  <div class="chart tab-pane active" id="revenue-chart" style="position: relative; text-align: center; vertical-align: middle;">
                    <div class="table-responsive">
                      <img src="{{ $chartTrademarks }}" height="300px" style="width: auto;">
                    </div>
                  </div>

                </div>
              </div>
            </div>

          </section>
          <section class="col-lg-3 col-md-6 col-sm-12 connectedSortable">
            
            <div class="card">
              <div class="card-header">
                <h3 class="card-title">
                  <i class="fas fa-chart-pie mr-1"></i>
                  <b>GRAFICA VENTAS ESTADOS</b>
                </h3>
                <div class="card-tools">
                  
                </div>
              </div>
              <div class="card-body">
                <div class="tab-content p-0">
                  
                  <div class="chart tab-pane active" id="revenue-chart" style="position: relative; text-align: center; vertical-align: middle;">
                    <div class="table-responsive">
                      <img src="{{ $chartStatus }}" height="300px" style="width: auto;">
                    </div>
                  </div>

                </div>
              </div>
            </div>

          </section>
        </div>
        
        <div class="row">
          <div class="col-md-3 col-sm-6 col-12">
            <div class="info-box">
              <span class="info-box-icon bg-info"><i class="far fa-arrows-rotate"></i></span>
              <div class="info-box-content">
                <span class="info-box-number">PENDIENTE</span>
                <span class="info-box-text"><b>{{ $details->where('order.seller_status', 'Pendiente')->pluck('order_id')->unique()->count() }}</b> PEDIDOS | <b>{{ $details->where('status', 'Pendiente')->sum('TOTAL') }}</b> UNIDADES</span>
              </div>
            </div>
          </div>
          <div class="col-md-3 col-sm-6 col-12">
            <div class="info-box">
              <span class="info-box-icon bg-orange"><i class="far fa-dollar text-white"></i></span>
              <div class="info-box-content">
                <span class="info-box-number">EN MORA</span>
                <span class="info-box-text"><b>{{ $details->where('status', 'Suspendido')->where('order.wallet_status', 'En mora')->pluck('order_id')->unique()->count() }}</b> PEDIDOS | <b>{{ $details->where('status', 'Suspendido')->where('order.wallet_status', 'En mora')->sum('TOTAL') }}</b> UNIDADES</span>
              </div>
            </div>
          </div>
          <div class="col-md-3 col-sm-6 col-12">
            <div class="info-box">
              <span class="info-box-icon bg-danger"><i class="far fa-xmark"></i></span>
              <div class="info-box-content">
                <span class="info-box-number">CANCELADO</span>
                <span class="info-box-text"><b>{{ $details->where('status', 'Cancelado')->where('order.seller_status', 'Cancelado')->pluck('order_id')->unique()->count() }}</b> PEDIDOS | <b>{{ $details->where('status', 'Cancelado')->where('order.seller_status', 'Cancelado')->sum('TOTAL') }}</b> UNIDADES</span>
              </div>
            </div>
          </div>
          <div class="col-md-3 col-sm-6 col-12">
            <div class="info-box">
              <span class="info-box-icon bg-success"><i class="far fa-check"></i></span>
              <div class="info-box-content">
                <span class="info-box-number">APROBADO</span>
                <span class="info-box-text"><b>{{ $details->where('status', 'Aprobado')->pluck('order_id')->unique()->count() }}</b> PEDIDOS | <b>{{ $details->where('status', 'Aprobado')->sum('TOTAL') }}</b> UNIDADES</span>
              </div>
            </div>
          </div>
        </div>
        
        <div class="row">
          <div class="col-md-3 col-sm-6 col-12">
            <div class="info-box">
              <span class="info-box-icon bg-warning"><i class="far fa-ban text-white"></i></span>
              <div class="info-box-content">
                <span class="info-box-number">RECHAZADO</span>
                <span class="info-box-text"><b>{{ $details->where('status', 'Cancelado')->where('order.wallet_status', 'Cancelado')->pluck('order_id')->unique()->count() }}</b> PEDIDOS | <b>{{ $details->where('status', 'Cancelado')->where('order.wallet_status', 'Cancelado')->sum('TOTAL') }}</b> UNIDADES</span>
              </div>
            </div>
          </div>
          <div class="col-md-3 col-sm-6 col-12">
            <div class="info-box">
              <span class="info-box-icon bg-secondary"><i class="far fa-clock-rotate-left"></i></span>
              <div class="info-box-content">
                <span class="info-box-number">SUSPENDIDO</span>
                <span class="info-box-text"><b>{{ $details->where('status', 'Suspendido')->where('order.wallet_status', 'Suspendido')->pluck('order_id')->unique()->count() }}</b> PEDIDOS | <b>{{ $details->where('status', 'Suspendido')->where('order.wallet_status', 'Suspendido')->sum('TOTAL') }}</b> UNIDADES</span>
              </div>
            </div>
          </div>
          <div class="col-md-3 col-sm-6 col-12">
            <div class="info-box">
              <span class="info-box-icon bg-purple"><i class="far fa-filter"></i></span>
              <div class="info-box-content">
                <span class="info-box-number">COMPROMETIDO</span>
                <span class="info-box-text"><b>{{ $details->where('status', 'Comprometido')->where('order.dispatch_status', '!=', 'Despachado')->pluck('order_id')->unique()->count() }}</b> PEDIDOS | <b>{{ $details->where('status', 'Comprometido')->where('order.dispatch_status', '!=', 'Despachado')->sum('order_dispatch_detail.TOTAL') }}</b> UNIDADES</span>
              </div>
            </div>
          </div>
          <div class="col-md-3 col-sm-6 col-12">
            <div class="info-box">
              <span class="info-box-icon bg-primary"><i class="far fa-share-all"></i></span>
              <div class="info-box-content">
                <span class="info-box-number">DESPACHADO</span>
                <span class="info-box-text"><b>{{ $details->where('status', 'Despachado')->where('order.dispatch_status', 'Despachado')->pluck('order_id')->unique()->count() }}</b> PEDIDOS | <b>{{ $details->where('status', 'Despachado')->where('order.dispatch_status', 'Despachado')->sum('order_dispatch_detail.TOTAL') }}</b> UNIDADES</span>
              </div>
            </div>
          </div>
        </div>
        
        <div class="row">
          <section class="col-md-12 col-sm-12 col-lg-12 connectedSortable">
            
            <div class="card">
              <div class="card-header">
                <h3 class="card-title">
                  <i class="fas fa-chart-network mr-1"></i>
                  <b>REPORTE GRAFICA CORRERIA</b>
                </h3>
                <div class="card-tools">
                  <div class="btn-group">
                    <button type="button" class="btn btn-light btn-sm dropdown-toggle" data-toggle="dropdown" data-offset="-52">
                      <i class="fas fa-bars"></i>
                    </button>
                    <div class="dropdown-menu" role="menu">
                      <a type="button" class="dropdown-item" onclick="GraficDashboardCorreria()">TODAS LAS CORRERIAS</a>
                      <div class="dropdown-divider"></div>
                      @foreach ($correrias as $correria)
                      <a type="button" class="dropdown-item" onclick="GraficDashboardCorreria({{ $correria->id }})">{{ $correria->name }} | {{ $correria->code }}</a>
                      @endforeach
                    </div>
                  </div>
                </div>
              </div>
              <div class="card-body">
                <div class="tab-content p-0">
                  
                  <div class="chart tab-pane active" id="revenue-chart" style="position: relative;">
                    <div class="table-responsive" id="ChartCorreria">
                      <img src="{{ $chartCorreria }}" height="300px" style="width: auto;">
                    </div>
                  </div>

                </div>
              </div>
            </div>

          </section>
        </div>
    </div>
</section>
@endsection
@section('script')
  <script src="{{ asset('js/Dashboard/Dashboard/Grafic.js') }}"></script>
    {{-- <script src="{{ asset('js/dist/js/pages/dashboard.js') }}"></script> --}}
@endsection
