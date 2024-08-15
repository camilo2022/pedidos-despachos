<div class="modal fade bd-example-modal" id="CreateBusinessModal" tabindex="-1" role="dialog"
    aria-labelledby="exampleModalLabel" data-backdrop="static" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header w-100">
                <div class="text-center w-100" style="background: white;">
                    <label style="font-size:20px;font-weight:bold;">CREAR SUCURSAL</label>
                </div>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-lg-6">
                        <div class="form-group c_form_group">
                            <label for="name_c">NOMBRE</label>
                            <input type="text" class="form-control" id="name_c" name="name_c">
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="form-group c_form_group">
                            <label for="branch_c">SUCURSAL</label>
                            <input type="text" class="form-control" id="branch_c" name="branch_c">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-6">
                        <div class="form-group c_form_group">
                            <label for="number_document_c">NUMERO DE DOCUMENTO</label>
                            <input type="text" class="form-control" id="number_document_c" name="number_document_c">
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="form-group c_form_group">
                            <label for="country_c">PAIS</label>
                            <select class="form-control select2" name="country_c" id="country_c" onchange="CreateBusinessModalCountryGetDepartament(this)">
                                <option value="">Seleccione</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-6">
                        <div class="form-group c_form_group">
                            <label for="departament_c">DEPARTAMENTO</label>
                            <select class="form-control select2" name="departament_c" id="departament_c" onchange="CreateBusinessModalDepartamentGetCity(this)">
                                <option value="">Seleccione</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="form-group c_form_group">
                            <label for="city_c">CIUDAD</label>
                            <select class="form-control select2" name="city_c" id="city_c">
                                <option value="">Seleccione</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12">
                        <div class="form-group c_form_group">
                            <label for="address_c">DIRECCION</label>
                            <input type="text" class="form-control" id="address_c" name="address_c">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-6">
                        <div class="form-group c_form_group">
                            <label for="order_footer_c">TEXTO - PEDIDO</label>
                            <textarea class="form-control" name="order_footer_c" id="order_footer_c" cols="30" rows="7"></textarea>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="form-group c_form_group">
                            <label for="dispatch_footer_c">TEXTO - ORDEN DE DESPACHO</label>
                            <textarea class="form-control" name="dispatch_footer_c" id="dispatch_footer_c" cols="30" rows="7"></textarea>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-6">
                        <div class="form-group c_form_group">
                            <label for="packing_footer_c">TEXTO - ROTULO</label>
                            <textarea class="form-control" name="packing_footer_c" id="packing_footer_c" cols="30" rows="7"></textarea>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="form-group c_form_group">
                            <label for="letterhead_c">MEMBRETE PEDIDO</label>
                            <div class="input-group">
                                <input type="file" class="form-control dropify" id="letterhead_c" name="letterhead_c"
                                accept=".jpg, .jpeg, .png" data-default-file="">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal" title="Cerrar ventana.">
                    <i class="fas fa-xmark"></i>
                </button>
                <button type="button" class="btn btn-primary" id="CreateBusinessButton" onclick="CreateBusiness()" title="Guardar sucursal de la empresa.">
                    <i class="fas fa-floppy-disk"></i>
                </button>
            </div>
        </div>
    </div>
</div>
