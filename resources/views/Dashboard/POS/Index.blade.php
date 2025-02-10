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
                                <li class="nav-item ml-2 w-100" data-card-widget="collapse" style="cursor: pointer;">
                                    <h3>Metodo de Pago</h3>
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
                                        <span class="form-control d-flex align-items-center" style="height: auto; border-left: none;">
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
                                    <button type="button" class="btn btn-primary" id="CreatePersonButton" onclick="CreateOrder()" title="Agregar persona.">
                                        <i class="fas fa-plus"></i>
                                    </button>
                                </li>
                                <li class="nav-item ml-2">
                                    <button type="button" class="btn btn-primary" id="CreatePersonButton" onclick="CreateOrder()" title="Guardar persona.">
                                        <i class="fas fa-floppy-disk"></i>
                                    </button>
                                </li>
                                <li class="nav-item ml-2">
                                    <button type="button" class="btn btn-primary" id="EditPerson" onclick="CreateOrder()" title="Editar persona.">
                                        <i class="fas fa-pen text-white"></i>
                                    </button>
                                </li>
                                <li class="nav-item ml-2">
                                    <button type="button" class="btn btn-primary" id="EditPersonButton" onclick="CreateOrder()" title="Actualizar persona.">
                                        <i class="fas fa-floppy-disk"></i>
                                    </button>
                                </li>
                                <li class="nav-item ml-2">
                                    <div class="card-tools">
                                        <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus mt-3"></i></button>
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
                                            <input type="text" id="search_person" class="form-control input-search" placeholder="Buscar cliente" autocomplete="off">
                                            <ul id="autocompleteventa" tabindex='1' class="list-group"></ul>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4 text-center">
                                    <div class="form-group py-2">
                                        <div class="form-check form-switch">
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" id="search_client_document" data-bootstrap-switch data-off-color="danger" data-on-color="success">
                                                <label class="form-check-label" for="search_client_document">Documento</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                  <div class="form-group row">
                                    <label class="control-label text-right col-md-3">Documento:</label>
                                    <div class="col-md-9">
                                      <input type="text" class="form-control" disabled id="number_document_person">
                                      <small class="form-control-feedback"> Número de documento del cliente. </small> </div>
                                  </div>
                                </div>

                                <div class="col-md-6">
                                  <div class="form-group row">
                                    <label class="control-label text-right col-md-3">Nombres:</label>
                                    <div class="col-md-9">
                                      <input type="text" class="form-control" disabled id="nombre_cliente">
                                      <small class="form-control-feedback"> Nombres del cliente. </small> </div>
                                  </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                  <div class="form-group row">
                                    <label class="control-label text-right col-md-3">Apellidos:</label>
                                    <div class="col-md-9">
                                      <input type="text" class="form-control" disabled id="number_document_person">
                                      <small class="form-control-feedback"> Apellidos del cliente. </small> </div>
                                  </div>
                                </div>

                                <div class="col-md-6">
                                  <div class="form-group row">
                                    <label class="control-label text-right col-md-3">Telefono:</label>
                                    <div class="col-md-9">
                                      <input type="number" class="form-control" disabled id="nombre_cliente">
                                      <small class="form-control-feedback"> Numero de Telefono del cliente. </small> </div>
                                  </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                  <div class="form-group row">
                                    <label class="control-label text-right col-md-3">Correo:</label>
                                    <div class="col-md-9">
                                      <input type="email" class="form-control" disabled id="number_document_person">
                                      <small class="form-control-feedback"> Correo electronico del cliente. </small> </div>
                                  </div>
                                </div>

                                <div class="col-md-6">
                                  <div class="form-group row">
                                    <label class="control-label text-right col-md-3">Direccion:</label>
                                    <div class="col-md-9">
                                      <input type="text" class="form-control" disabled id="nombre_cliente">
                                      <small class="form-control-feedback"> Direccion de residencia del cliente. </small> </div>
                                  </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
    </section>
    <section class="content" id="main_content_sale">
        <div class="row">
            <div class="col-md-9 " style="">
                <div class="card card-primary card-outline div_radius">
                    <div class="card-header">
                        <form method="POST" action="https://posventa10.rogercode.com/ventas/venta/create"
                            accept-charset="UTF-8" id="save_producto_venta"><input name="_token" type="hidden"
                                value="dajPZRRCJPB7SlbyCvMMon2rewKHKaaXTpZAakyl">
                            <div class="row">
                                <!--Input que tiene el id del usuario identificado-->
                                <input type="text" size="4" id="id_user" name="id_user" value="1"
                                    hidden="true">

                                <div class="col-md-9">
                                    <div class="form-group">
                                        <div class="input-group mb-3 ">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text bg-info" id="basic-addon1"><i
                                                        class="fa fa-search" aria-hidden="true"></i></span>
                                            </div>
                                            <input type="text" id="BuscarVentaProducto" name="BuscarVentaProducto"
                                                class="form-control input-search" placeholder="Buscar por el nombre"
                                                autocomplete="off">
                                            <ul id="autocompleteventa" tabindex='1' class="list-group"></ul>
                                            <!--NOMBRE DEL ARTICULO-->
                                            <input type="text" placeholder="nombre articulo" id="NombreArticulo"
                                                name="NombreArticulo" size="4" hidden="true">
                                            <!--EL ID DEL PRODUCTO-->
                                            <input type="text" name="idarticulo" placeholder="id producro"
                                                id="idarticulo" size="4" hidden="true">
                                            <!--EL CODIGO DEL ARTICULO -->
                                            <input type="text" name="CodigoArticulo" placeholder="codigo producto"
                                                id="CodigoArticulo" size="4" hidden="true">
                                            <!--EL INPUT DEL IVA -->
                                            <input type="text" name="iva" placeholder="iva" id="iva"
                                                size="4" hidden="true">
                                            <!--EL NPUT DEL CODIGO-->
                                            <input type="text" name="cod_user" id="cod_user" value="1"
                                                placeholder="codigo del usuario" hidden="true">

                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3 text-center">
                                    <div class="form-group py-2">
                                        <div class="form-check form-switch">
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" id="barcodeChecked">
                                                <label class="form-check-label" for="barcodeChecked">Codigo de
                                                    barras</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="">Cantidad</label>
                                        <input type="number" class="form-control input-sm" id="pcantidad"
                                            name="pvcantidad" min="0" step="0.00" placeholder="0.00"
                                            onkeypress="return filterFloat(event,this);">
                                        <!-- <input type="text" name="moneda nac" id="moneda_nac" value="10" onkeypress="return filterFloat(event,this);"/> -->
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="">Stock</label>
                                        <input type="number" class="form-control input-sm" id="pvstock"
                                            name="pvstock" min="0" readonly step="0.00" placeholder="0.00">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="">P. venta</label>
                                        <input type="number" class="form-control input-sm" id="pvprecio_venta"
                                            name="pvprecio_venta" readonly min="0" step="0.04"
                                            placeholder="0.00">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="">Descuento</label>
                                        <input type="number" class="form-control input-sm" id="pvdescuento"
                                            name="pvdescuento" min="0" step="0.00" placeholder="0.00" readonly
                                            onkeypress="return filterFloatdecimal2(event,this);">
                                    </div>
                                </div>
                            </div>
                            <button type="button" class="btn btn-default btn1" id="btn_add_prod_tem_vent">
                                <i class="fas fa-check-circle text-success"></i>
                                Agregar
                            </button>
                            <div class="alert alert-danger print-save-error-msg" style="display:none">
                                <ul></ul>
                            </div>
                        </form>
                    </div>
                    <div class="card-body" style="margin-top: -18px;">
                        <!-- <h5 class="card-title">Special title treatment</h5> -->
                        <form method="POST" action="https://posventa10.rogercode.com/ventas/venta/create"
                            accept-charset="UTF-8" id="save_venta_total"><input name="_token" type="hidden"
                                value="dajPZRRCJPB7SlbyCvMMon2rewKHKaaXTpZAakyl">
                            <div class="tableFixHead">
                                <!--ID USER PARA LAVENTA-->
                                <input type="text" size="4" id="id_user_vent" name="id_user_vent"
                                    value="1" hidden="true">
                                <!--EL ID DE LA CAJA QUE EL CAJERO TIENE ACCESO-->
                                <input type="number" name="inicioapertura" value="340" id="inicioapertura"
                                    hidden="true">
                                <table id="detalles" style="width:100%"
                                    class="table table-bordered table-sm table-hover text-center">
                                    <thead>
                                        <tr>
                                            <th>Num</th>
                                            <th>Articulo</th>
                                            <th>Cantidad</th>
                                            <th>P. venta</th>
                                            <th>Descuento</th>
                                            <th>Subtotal</th>
                                            <th scope="col"><i class="fas fa-trash-alt"></i></th>
                                        </tr>
                                    </thead>
                                    <tbody id="tabla_venta_productos_temp">
                                    </tbody>
                                </table>
                                <!-- /.table -->
                            </div>
                            <!--TERMINACION DEL DIV DEL SCROLL DE LA TABLA-->
                            <!--INICIO DEL DIV DONDE SE PRESENTAN LOS TOTALES GLOBALES-->
                            <div class="container" style="border:1px solid #A9A9A9;">
                                <div class="row">
                                    <div class="col-md-3" style="">
                                        <div class="btn-group" role="group"
                                            style="width:100%;height:100%;margin-left: -7px;">
                                            <button type="button" id="cancelventaproducto"
                                                class="btn btn-danger btn-block btn-flat">
                                                Cancelar venta
                                            </button>
                                        </div>
                                    </div>
                                    <div class="col-md-3" style=""></div>
                                    <div class="col-md-3  text-right" style="">
                                        <h5 class="" style="margin-top:8px;"><strong>Total $</strong></h5>
                                    </div>
                                    <div class="col-md-3">

                                        <input type="text" name="" id="inputventatotal"
                                            class="form-control input_style_total" placeholder="00.00" readonly>

                                    </div>
                                </div>
                            </div>
                    </div>
                </div>
            </div>
            <!-- /.col -->
            <div class="col-md-3 ">
                <!-- /.card-body -->
                <div class="card div_radius">
                    <div class="card-header">
                        <h3 class="card-title">Datos de la venta</h3>

                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-card-widget="collapse"><i
                                    class="fas fa-minus"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div class="container">
                            <div class="mb-3">
                                <label for="nombre">Cliente</label>
                                <div class="input-group">
                                    <input type="text" class="form-control" value="Publico en General"
                                        id="nomcliente" placeholder="Nombre del cliente" autocomplete="off" readonly>
                                    <input type="hidden" name="ventidcliente" value="1" id="ventidcliente"
                                        size="3" placeholder="id del cliente" autocomplete="off">
                                    <button type="button" class="btn btn6" data-bs-toggle="modal"
                                        data-bs-target="#exampleModal"><i class="fas fa-users"></i></button>
                                </div>
                            </div>
                            <div class="mb-2">
                                <div class="row">
                                    <div class="col">
                                        <div class="group">
                                            <label for="">Comprobante</label>
                                            <select name="venttipo_comprobante" class="form-control" id="">
                                                <option value="Ticket">Ticket</option>
                                                <option value="Factura">Factura</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col">
                                        <div class="group">
                                            <label for="">Folio</label>
                                            <input type="text" name="ventfolio" id="ventfonio" value="2025283401"
                                                class="form-control" placeholder="num de folio" readonly>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                    <!-- /.card-body -->
                    <br>
                </div>
                <!-- /.card -->
                <div class="card div_radius">
                    <div class="card-header">
                        <h3 class="card-title">Realizar venta</h3>

                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-card-widget="collapse"><i
                                    class="fas fa-minus"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div class="container">
                            <div class="mb-3">
                                <div class="form-group">
                                    <input type="text" name="venttotal_venta"
                                        class="form-control text-center input_style_total" id="venttotal_venta"
                                        placeholder="00.00" readonly>
                                </div>
                            </div>
                            <div class="mb-3">
                                <div class="row">
                                    <div class="col">
                                        <div class="form-group">
                                            <label for="">Cantidad</label>
                                            <input type="text" id="ventdinero" name="ventdinero"
                                                class="form-control input_style" placeholder="$ 0.00"
                                                onkeypress="return filterFloatdecimal2(event,this);" autocomplete="off">
                                        </div>
                                    </div>
                                    <div class="col">
                                        <div class="form-group">
                                            <label for="">Cambio</label>
                                            <input type="text" id="ventsuelto" name="ventsuelto"
                                                class="form-control input_style" readonly placeholder="$ 0.00">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="mb-3">
                                <div class="form-group">
                                    <button type="submit" id="venta_productos_realizada"
                                        class="btn btn-default btn-block btn1">
                                        <i class="fas fa-check-circle text-success"></i>
                                        Aceptar
                                    </button>
                                </div>
                            </div>
                        </div>
                        <!--<ul class="nav nav-pills flex-column">
                      <li class="nav-item">
                        <div class="form-group">
                          <input type="text" name="venttotal_venta" class="form-control text-center input_style_total" id="venttotal_venta" placeholder="00.00" readonly>
                        </div>
                      </li>
                      <li class="nav-item">
                        <div class="row">
                          <div class="col">
                            <div class="form-group">
                              <label for="">Cantidad</label>
                              <input type="text" id="ventdinero" name="ventdinero" class="form-control input_style" placeholder="$ 0.00" onkeypress="return filterFloatdecimal2(event,this);" autocomplete="off">
                            </div>
                          </div>
                          <div class="col">
                            <di class="form-group">
                            <label for="">Cambio</label>
                            <input type="text" id="ventsuelto" name="ventsuelto" class="form-control input_style" readonly placeholder="$ 0.00">
                            </di>
                          </div>
                        </div>
                      </li>
                      <li class="nav-item">
                        <br>
                        <button type="submit" id="venta_productos_realizada" class="btn btn-default btn-block btn1">
                          <i class="fas fa-check-circle text-success"></i>
                          Aceptar
                        </button>
                      </li>
                    </ul>-->
                    </div> <!-- /.fin  de card-body -->
                </div> <!-- /.fin card -->
                </form>
            </div><!-- /.fin del col-md-3 -->
        </div><!-- /. fin del row -->
    </section><!-- /.fin de la seccion -->
@endsection
@section('script')
@endsection
