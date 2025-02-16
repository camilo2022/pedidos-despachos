@extends('Templates.Dashboard')
@section('content')
    <section class="content">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0 text-dark">CAJA REGISTRADORA - POS</h1>
                    </div><!-- /.col -->
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item">Dashboard</li>
                            <li class="breadcrumb-item">POS</li>
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
                    <div class="card collapsed-card">
                        <div class="card-header p-2">
                            <ul class="nav nav-pills">
                                <li class="nav-item ml-2">
                                    <h3>Metodo de Pago</h3>
                                </li>
                                <li class="nav-item ml-auto">
                                    <div class="card-tools">
                                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                            <i class="fas fa-plus mt-3"></i>
                                        </button>
                                    </div>
                                </li>
                            </ul>
                        </div>
                        <div class="card-body">
                            @foreach (['is_cash', 'is_libranza', 'is_transfer', 'is_card'] as $type)
                                @forelse ($payment_methods->where($type, true) as $payment_method)
                                @php($payment_method->settings = json_decode($payment_method->settings))
                                <div class="row">
                                    <div class="col-lg-12">
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">
                                                    <div class="icheck-warning d-inline">
                                                        <input type="checkbox" id="{{ str_replace(' ', '_', strtolower($payment_method->settings->name)).'_check' }}" {{ $payment_method->settings->default ? 'checked' : '' }} {{ $payment_method->is_libranza ? 'disabled' : '' }} onchange="IndexPOSChangePaymentMethod('{{ str_replace(' ', '_', strtolower($payment_method->settings->name)) }}', '{{ $payment_method->is_cash ? 'cash' : '' }}')">
                                                        <label for="{{ str_replace(' ', '_', strtolower($payment_method->settings->name)).'_check' }}"></label>
                                                    </div>
                                                </span>
                                            </div>
                                            <span class="form-control d-flex align-items-center"
                                                style="height: auto; border-left: none;">
                                                <i class="{{ $payment_method->settings->icon }} mr-2"></i> <b>{{ $payment_method->name }}</b>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <hr>
                                @empty

                                @endforelse
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header p-2">
                            <ul class="nav nav-pills">
                                <li class="nav-item ml-2">
                                    <h3>Información del cliente</h3>
                                </li>
                                <li class="nav-item ml-auto">
                                    <button type="button" class="btn btn-primary" id="CreatePerson" onclick="CreatePersonModal(false)" title="Agregar persona.">
                                        <i class="fas fa-plus"></i>
                                    </button>
                                </li>
                                <li class="nav-item ml-2">
                                    <button type="button" class="btn btn-primary" id="CreatePersonButton" onclick="CreatePerson()" title="Guardar persona.">
                                        <i class="fas fa-floppy-disk"></i>
                                    </button>
                                </li>
                                <li class="nav-item ml-2">
                                    <button type="button" class="btn btn-primary" id="EditPerson" onclick="EditPersonModal(0, false)" title="Editar persona.">
                                        <i class="fas fa-pen text-white"></i>
                                    </button>
                                </li>
                                <li class="nav-item ml-2">
                                    <button type="button" class="btn btn-primary" id="EditPersonButton" onclick="EditPerson(0)" title="Actualizar persona.">
                                        <i class="fas fa-floppy-disk"></i>
                                    </button>
                                </li>
                                <li class="nav-item ml-2">
                                    <div class="card-tools">
                                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                            <i class="fas fa-minus mt-3"></i>
                                        </button>
                                    </div>
                                </li>
                            </ul>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-8">
                                    <div class="form-group">
                                        <div class="input-group mb-3">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text bg-info">
                                                    <i class="fa fa-search"></i>
                                                </span>
                                            </div>
                                            <div class="typeahead__container client" style="width: 90% !important;">
                                                <div class="typeahead__field" style="width: 100% !important;">
                                                    <div class="typeahead__query">
                                                        <input type="text" id="search_person" class="form-control input-search"  style="width: 100% !important;"
                                                            placeholder="Buscar cliente" autocomplete="off" onkeyup="IndexPOSSearchPerson()">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-4 text-center">
                                    <div class="form-group py-2">
                                        <div class="form-check form-switch">
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" id="search_client_number_document"
                                                    data-bootstrap-switch data-off-color="danger" data-on-color="success">
                                                <label class="form-check-label"
                                                    for="search_client_number_document">Documento</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12 text-center">
                                    <h4>PERSONA</h4>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group row">
                                        <label class="control-label text-right col-md-3">Documento:</label>
                                        <div class="col-md-9">
                                            <input type="text" class="form-control" disabled id="person_number_document" data-person_id="">
                                            <small class="form-control-feedback"> Número de documento del cliente. </small>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group row">
                                        <label class="control-label text-right col-md-3">Nombres:</label>
                                        <div class="col-md-9">
                                            <input type="text" class="form-control" disabled id="person_name">
                                            <small class="form-control-feedback"> Nombres del cliente. </small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group row">
                                        <label class="control-label text-right col-md-3">Apellidos:</label>
                                        <div class="col-md-9">
                                            <input type="text" class="form-control" disabled id="person_last_name">
                                            <small class="form-control-feedback"> Apellidos del cliente. </small>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group row">
                                        <label class="control-label text-right col-md-3">Telefono:</label>
                                        <div class="col-md-9">
                                            <input type="number" class="form-control" disabled id="person_phone_number">
                                            <small class="form-control-feedback"> Numero de Telefono del cliente. </small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group row">
                                        <label class="control-label text-right col-md-3">Correo:</label>
                                        <div class="col-md-9">
                                            <input type="email" class="form-control" disabled id="person_email">
                                            <small class="form-control-feedback"> Correo electronico del cliente. </small>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group row">
                                        <label class="control-label text-right col-md-3">Direccion:</label>
                                        <div class="col-md-9">
                                            <input type="text" class="form-control" disabled id="person_address">
                                            <small class="form-control-feedback"> Direccion de residencia del cliente.
                                            </small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12 text-center">
                                    <h4>EMPLEADO</h4>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group row">
                                        <label class="control-label text-right col-md-3">Cupo:</label>
                                        <div class="col-md-9">
                                            <input type="number" class="form-control" disabled id="employee_quota">
                                            <small class="form-control-feedback"> Monto aprobado para libranza. </small>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group row">
                                        <label class="control-label text-right col-md-3">Deuda:</label>
                                        <div class="col-md-9">
                                            <input type="number" class="form-control" disabled id="employee_debt">
                                            <small class="form-control-feedback"> Monto por pagar en libranza. </small>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group row">
                                        <label class="control-label text-right col-md-3">Disponible:</label>
                                        <div class="col-md-9">
                                            <input type="number" class="form-control" disabled id="employee_available">
                                            <small class="form-control-feedback"> Monto disponible para comprar. </small>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group row">
                                        <label class="control-label text-right col-md-3">Libranza:</label>
                                        <div class="col-md-9">
                                            <h2 id="green" style="display: none;">✔</h2>
                                            <h2 id="red" style="display: none;">❌</h2>
                                            <h2 id="black" style="display: block;">➖</h2>
                                            <small class="form-control-feedback" id="text"> No es empleado. </small>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header p-2">
                            <ul class="nav nav-pills">
                                <li class="nav-item ml-2">
                                    <h3>Detalles de Venta</h3>
                                </li>
                            </ul>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-8">
                                    <div class="form-group">
                                        <div class="input-group mb-3">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text bg-info">
                                                    <i class="fa fa-search"></i>
                                                </span>
                                            </div>
                                            <div class="typeahead__container product" style="width: 90% !important;">
                                                <div class="typeahead__field" style="width: 100% !important;">
                                                    <div class="typeahead__query">
                                                        <input type="text" id="search_product" class="form-control input-search"  style="width: 100% !important;"
                                                            placeholder="Buscar producto" autocomplete="off" onkeyup="IndexPOSSearchProduct()">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4 text-center">
                                    <div class="form-group py-2">
                                        <div class="form-check form-switch">
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox"
                                                    id="search_product_code_bar" data-bootstrap-switch
                                                    data-off-color="danger" data-on-color="success">
                                                <label class="form-check-label" for="search_product_code_bar">Codigo
                                                    Barras</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-12">
                                    <table id="detalles" style="width:100%"
                                        class="table table-bordered table-sm table-hover text-center">
                                        <thead class="bg-dark">
                                            <tr>
                                                <th scope="col" width="15%">Cod</th>
                                                <th scope="col" width="10%">Precio</th>
                                                <th scope="col" width="10%">Cant</th>
                                                <th scope="col" width="15%">Desc</th>
                                                <th scope="col" width="10%">Tipo. Desc</th>
                                                <th scope="col" width="15%">Subtotal</th>
                                                <th scope="col" width="15%">Total</th>
                                                <th scope="col" width="10%">Acciones</th>
                                            </tr>
                                        </thead>
                                        <tbody id="invoice_details">
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-lg-12">

                                    <div class="pull-right m-t-30 text-right">
                                        <h4><p>Sub - Total: <span id="invoice_subtotal">$ 0</span></p></h4>
                                        <h4><p>Descuento: <span id="invoice_descuento">$ 0</span></p></h4>
                                        <h3><b>Total :</b> <span id="invoice_total">$ 0</span></h3>
                                    </div>
                                    <div class="clearfix"></div>
                                    <hr>
                                    @foreach ($payment_methods as $payment_method)
                                    <div class="row {{ $payment_method->is_cash ? 'cash' : '' }}" id="{{ str_replace(' ', '_', strtolower($payment_method->settings->name)).'_div' }}" style="display: {{ $payment_method->settings->default ? 'block' : 'none' }};">
                                        <div class="col-md-12">
                                            <div class="form-group row">
                                                <label class="control-label text-right col-md-9">{{ $payment_method->settings->name }}:</label>
                                                <div class="col-md-3">
                                                    @if (isset($payment_method->settings->options))
                                                    @foreach (collect($payment_method->settings->options)->chunk(2) as $row)
                                                        <div class="row">
                                                            @foreach ($row as $option)
                                                                <div class="col-md-6">
                                                                    <div class="icheck-primary">
                                                                        <input type="radio" id="{{ str_replace(' ', '_', strtolower($payment_method->settings->name)).'_'.str_replace(' ', '_', strtolower($option->name)) }}" {{ $option->default ? 'checked' : '' }}>
                                                                        <label for="{{ str_replace(' ', '_', strtolower($payment_method->settings->name)).'_'.str_replace(' ', '_', strtolower($option->name)) }}">
                                                                            {{ $option->name }}
                                                                        </label>
                                                                    </div>
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                    @endforeach
                                                    @endif
                                                    <div class="input-group">
                                                        <div class="input-group-prepend"  style="height: {{ ($payment_method->settings->verify ?? false) or ($payment_method->settings->employee ?? false) ? '76' : '100' }}%;">
                                                            <span class="input-group-text">$</span>
                                                        </div>
                                                        <input type="number" class="form-control"  id="{{ str_replace(' ', '_', strtolower($payment_method->settings->name)) }}" onkeyup="IndexPOSCalculateCashChange()" style="font-size: 25px !important;">
                                                        @if(($payment_method->settings->verify ?? false))
                                                        @php($name = '')
                                                        @if (isset($payment_method->settings->employee))
                                                            @php($name = 'employee')
                                                        @elseif (isset($payment_method->settings->verify))
                                                            @php($name = 'verify')
                                                        @endif
                                                        <div class="input-group-append">
                                                            <div class="input-group-text d-flex align-items-center" style="height: 76%;">
                                                                <div class="icheck-success d-inline ml-2">
                                                                    <input type="checkbox" id="{{ str_replace(' ', '_', strtolower($payment_method->settings->name)).'_'.$name }}" style="transform: scale(0.7); width: 14px; height: 14px;" disabled>
                                                                    <label for="{{ str_replace(' ', '_', strtolower($payment_method->settings->name)).'_'.$name }}" style="transform: scale(0.7);"></label>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    @endforeach

                                    <div class="row cash" id="change_div">
                                        <div class="col-md-12">
                                            <div class="form-group row">
                                                <label class="control-label text-right col-md-9">Cambio:</label>
                                                <div class="col-md-3">

                                                    <h3><span id="change" data-change="0">$ 0</span></h3>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <hr>
                                    <div class="text-right">
                                        <button type="button" class="btn btn-danger" id="CancelPOSInvoiceButton">Cancelar</button>
                                        <button type="button" class="btn btn-success" id="CreatePOSInvoiceButton" onclick="CreatePOSInvoice()">Vender</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
    </section>
@endsection
@section('script')
    <script>
        let payment_methods = @json($payment_methods);
        let promotions = @json($promotions);
    </script>
    <script src="{{ asset('js/Dashboard/POS/Index.js') }}"></script>
    <script src="{{ asset('js/Dashboard/POS/Create.js') }}"></script>
@endsection
