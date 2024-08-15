<div class="modal fade bd-example-modal" id="EditBusinessModal" tabindex="-1" role="dialog"
    aria-labelledby="exampleModalLabel" data-backdrop="static" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header w-100">
                <div class="text-center w-100" style="background: white;">
                    <label style="font-size:20px;font-weight:bold;">EDITAR SUCURSAL</label>
                </div>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-lg-6">
                        <div class="form-group c_form_group">
                            <label for="name_e">NOMBRE</label>
                            <input type="text" class="form-control" id="name_e" name="name_e">
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="form-group c_form_group">
                            <label for="branch_e">SUCURSAL</label>
                            <input type="text" class="form-control" id="branch_e" name="branch_e">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-6">
                        <div class="form-group c_form_group">
                            <label for="number_document_e">NUMERO DE DOCUMENTO</label>
                            <input type="text" class="form-control" id="number_document_e" name="number_document_e">
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="form-group c_form_group">
                            <label for="country_e">PAIS</label>
                            <select class="form-control select2" name="country_e" id="country_e" onchange="EditBusinessModalCountryGetDepartament(this)">
                                <option value="">Seleccione</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-6">
                        <div class="form-group c_form_group">
                            <label for="departament_e">DEPARTAMENTO</label>
                            <select class="form-control select2" name="departament_e" id="departament_e" onchange="EditBusinessModalDepartamentGetCity(this)">
                                <option value="">Seleccione</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="form-group c_form_group">
                            <label for="city_e">CIUDAD</label>
                            <select class="form-control select2" name="city_e" id="city_e">
                                <option value="">Seleccione</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12">
                        <div class="form-group c_form_group">
                            <label for="address_e">DIRECCION</label>
                            <input type="text" class="form-control" id="address_e" name="address_e">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-6">
                        <div class="form-group c_form_group">
                            <label for="order_footer_e">TEXTO - PEDIDO</label>
                            <textarea class="form-control" name="order_footer_e" id="order_footer_e" cols="30" rows="7"></textarea>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="form-group c_form_group">
                            <label for="dispatch_footer_e">TEXTO - ORDEN DE DESPACHO</label>
                            <textarea class="form-control" name="dispatch_footer_e" id="dispatch_footer_e" cols="30" rows="7"></textarea>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-6">
                        <div class="form-group c_form_group">
                            <label for="packing_footer_e">TEXTO - ROTULO</label>
                            <textarea class="form-control" name="packing_footer_e" id="packing_footer_e" cols="30" rows="7"></textarea>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="form-group c_form_group">
                            <label for="letterhead_e">MEMBRETE PEDIDO</label>
                            <div class="input-group">
                                <input type="file" class="form-control dropify" id="letterhead_e" name="letterhead_e"
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
                <button type="button" class="btn btn-primary" id="EditBusinessButton" onclick="" title="Actualizar sucursal de la empresa.">
                    <i class="fas fa-floppy-disk"></i>
                </button>
            </div>
        </div>
    </div>
</div>
