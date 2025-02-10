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
                            <div class="row">
                                <div class="col-lg-6">
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">
                                                <div class="icheck-warning d-inline">
                                                    <input type="checkbox" id="checkboxPrimary1">
                                                    <label for="checkboxPrimary1"></label>
                                                </div>
                                            </span>
                                        </div>
                                        <span class="form-control d-flex align-items-center"
                                            style="height: auto; border-left: none;">
                                            <i class="fas fa-cash-register mr-2"></i> <b>Caja - pagos en efectivo.</b>
                                        </span>
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
                                    <h3>Información del cliente</h3>
                                </li>
                                <li class="nav-item ml-auto">
                                    <button type="button" class="btn btn-primary" id="CreatePersonButton"
                                        onclick="CreateOrder()" title="Agregar persona.">
                                        <i class="fas fa-plus"></i>
                                    </button>
                                </li>
                                <li class="nav-item ml-2">
                                    <button type="button" class="btn btn-primary" id="CreatePersonButton"
                                        onclick="CreateOrder()" title="Guardar persona.">
                                        <i class="fas fa-floppy-disk"></i>
                                    </button>
                                </li>
                                <li class="nav-item ml-2">
                                    <button type="button" class="btn btn-primary" id="EditPerson" onclick="CreateOrder()"
                                        title="Editar persona.">
                                        <i class="fas fa-pen text-white"></i>
                                    </button>
                                </li>
                                <li class="nav-item ml-2">
                                    <button type="button" class="btn btn-primary" id="EditPersonButton"
                                        onclick="CreateOrder()" title="Actualizar persona.">
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
                                            <input type="text" id="search_person" class="form-control input-search"
                                                placeholder="Buscar cliente" autocomplete="off">
                                            <ul id="autocompleteventa" tabindex='1' class="list-group"></ul>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4 text-center">
                                    <div class="form-group py-2">
                                        <div class="form-check form-switch">
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" id="search_client_document"
                                                    data-bootstrap-switch data-off-color="danger" data-on-color="success">
                                                <label class="form-check-label"
                                                    for="search_client_document">Documento</label>
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
                                            <input type="text" class="form-control" disabled
                                                id="number_document_person">
                                            <small class="form-control-feedback"> Número de documento del cliente. </small>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group row">
                                        <label class="control-label text-right col-md-3">Nombres:</label>
                                        <div class="col-md-9">
                                            <input type="text" class="form-control" disabled id="nombre_cliente">
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
                                            <input type="text" class="form-control" disabled
                                                id="number_document_person">
                                            <small class="form-control-feedback"> Apellidos del cliente. </small>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group row">
                                        <label class="control-label text-right col-md-3">Telefono:</label>
                                        <div class="col-md-9">
                                            <input type="number" class="form-control" disabled id="nombre_cliente">
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
                                            <input type="email" class="form-control" disabled
                                                id="number_document_person">
                                            <small class="form-control-feedback"> Correo electronico del cliente. </small>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group row">
                                        <label class="control-label text-right col-md-3">Direccion:</label>
                                        <div class="col-md-9">
                                            <input type="text" class="form-control" disabled id="nombre_cliente">
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
                                            <input type="text" class="form-control" disabled
                                                id="number_document_person">
                                            <small class="form-control-feedback"> Monto aprobado para libranza. </small>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group row">
                                        <label class="control-label text-right col-md-3">Deuda:</label>
                                        <div class="col-md-9">
                                            <input type="text" class="form-control" disabled id="nombre_cliente">
                                            <small class="form-control-feedback"> Monto por pagar en libranza. </small>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group row">
                                        <label class="control-label text-right col-md-3">Disponible:</label>
                                        <div class="col-md-9">
                                            <input type="text" class="form-control" disabled id="nombre_cliente">
                                            <small class="form-control-feedback"> Monto disponible para comprar. </small>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group row">
                                        <label class="control-label text-right col-md-3">Libranza:</label>
                                        <div class="col-md-9">
                                            <h2>❌</h2>
                                            <h2>✔</h2>
                                            <h2>➖</h2>
                                            <small class="form-control-feedback"> Puede solicitar libranza. </small>
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
                                            <input type="text" id="search_person" class="form-control input-search"
                                                placeholder="Buscar producto" autocomplete="off">
                                            <ul id="autocompleteventa" tabindex='1' class="list-group"></ul>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4 text-center">
                                    <div class="form-group py-2">
                                        <div class="form-check form-switch">
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox"
                                                    id="search_client_document" data-bootstrap-switch
                                                    data-off-color="danger" data-on-color="success">
                                                <label class="form-check-label" for="search_client_document">Codigo
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
                                        <thead>
                                            <tr>
                                                <th scope="col">Cod</th>
                                                <th scope="col">Ref</th>
                                                <th scope="col">Talla</th>
                                                <th scope="col">Color</th>
                                                <th scope="col">Cant</th>
                                                <th scope="col">Desc</th>
                                                <th scope="col">Tipo. Desc</th>
                                                <th scope="col">Subtotal</th>
                                                <th scope="col">Total</th>
                                                <th scope="col">Acciones</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-lg-12">

                                    <div class="pull-right m-t-30 text-right">
                                        <h4><p>Sub - Total: $ <span id="subtotal">0</span></p></h4>
                                        <h4><p>Descuento: $ <span id="descuento">0</span></p></h4>
                                        <h3><b>Total :</b> $<span id="total">0</span></h3>
                                    </div>
                                    <div class="clearfix"></div>
                                    <hr>
                                    <div class="row" id="input_efectivo_efectivo">
                                        <div class="col-md-12">
                                            <div class="form-group row">
                                                <label class="control-label text-right col-md-9">Efectivo:</label>
                                                <div class="col-md-3">
                                                    <div class="input-group">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text">$</span>
                                                        </div>
                                                        <input type="text" class="form-control">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row" id="input_efectivo_efectivo">
                                        <div class="col-md-12">
                                            <div class="form-group row">
                                                <label class="control-label text-right col-md-9">Libranza:</label>
                                                <div class="col-md-3">
                                                    <div class="input-group">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text">$</span>
                                                        </div>
                                                        <input type="text" class="form-control">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row" id="input_efectivo_efectivo">
                                        <div class="col-md-12">
                                            <div class="form-group row">
                                                <label class="control-label text-right col-md-9">Nequi:</label>
                                                <div class="col-md-3">
                                                    <div class="input-group">
                                                        <div class="col-md-6">
                                                            <div class="icheck-primary">
                                                                <input type="radio" id="someRadioId1" name="someGroupName" />
                                                                <label for="someRadioId1">Noti. Push</label>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="icheck-primary">
                                                                <input type="radio" id="someRadioId2" name="someGroupName" />
                                                                <label for="someRadioId2">Codigo QR</label>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="input-group">
                                                        <div class="input-group-prepend" style="height: 76%;">
                                                            <span class="input-group-text">$</span>
                                                        </div>
                                                        <input type="text" class="form-control" disabled>
                                                        <div class="input-group-append">
                                                            <div class="input-group-text d-flex align-items-center" style="height: 76%;">
                                                                <div class="icheck-success d-inline ml-2">
                                                                    <input type="checkbox" id="checkboxPrimary111" style="transform: scale(0.7); width: 14px; height: 14px;" disabled>
                                                                    <label for="checkboxPrimary111" style="transform: scale(0.7);"></label>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row" id="input_cambio">
                                        <div class="col-md-12">
                                            <div class="form-group row ">
                                                <label class="control-label text-right col-md-9">Cambio:</label>
                                                <div class="col-md-3">
                                                    <div class="input-group">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text">$</span>
                                                        </div>
                                                        <input type="text" class="form-control" disabled>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <hr>
                                    <div class="text-right">
                                        <button type="button" class="btn btn-danger">Cancelar</button>
                                        <button type="button" class="btn btn-success" id="vender_producto">Vender</button>
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
@endsection
