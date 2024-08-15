<div class="modal" id="EditClientModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" data-backdrop="static" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable" role="document">
        <div class="modal-content">
            <div class="modal-header w-100">
                <div class="text-center w-100" style="background: white;">
                    <label style="font-size:20px;font-weight:bold;">EDITAR CLIENTE</label>
                </div>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-lg-6">
                        <div class="form-group c_form_group">
                            <label for="client_name_e">RAZON SOCIAL</label>
                            <input type="text" class="form-control" id="client_name_e" name="client_name_e" onblur="Trim(this)" onkeyup="UpperCase(this)">
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="form-group c_form_group">
                            <label for="client_address_e">DIRECCION</label>
                            <input type="text" class="form-control" id="client_address_e" name="client_address_e" onblur="Trim(this)" onkeyup="UpperCase(this)">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-6">
                        <div class="form-group c_form_group">
                            <label for="client_number_document_e">NUMERO DE DOCUMENTO</label>
                            <input type="number" class="form-control" id="client_number_document_e" name="client_number_document_e" onblur="Trim(this)" onkeyup="UpperCase(this)">
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="form-group c_form_group">
                            <label for="client_branch_code_e">CODIGO DE SUCURSAL</label>
                            <input type="number" class="form-control" id="client_branch_code_e" name="client_branch_code_e" onblur="Trim(this)" onkeyup="UpperCase(this)">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-6">
                        <div class="form-group c_form_group">
                            <label for="client_number_phone_e">TELEFONO CLIENTE</label>
                            <input type="number" class="form-control" id="client_number_phone_e" name="client_number_phone_e" onblur="Trim(this)" onkeyup="UpperCase(this)">
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="form-group c_form_group">
                            <label for="client_branch_name_e">SUCURSAL</label>
                            <input type="text" class="form-control" id="client_branch_name_e" name="client_branch_name_e" onblur="Trim(this)" onkeyup="UpperCase(this)">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-6">
                        <div class="form-group c_form_group">
                            <label for="client_branch_address_e">DIRECCION DE DESPACHO</label>
                            <input type="text" class="form-control" id="client_branch_address_e" name="client_branch_address_e" onblur="Trim(this)" onkeyup="UpperCase(this)">
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="form-group c_form_group">
                            <label for="client_branch_number_phone_e">TELEFONO SUCURSAL</label>
                            <input type="text" class="form-control" id="client_branch_number_phone_e" name="client_branch_number_phone_e" onblur="Trim(this)" onkeyup="UpperCase(this)">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-6">
                        <div class="form-group c_form_group">
                            <label for="country_e">PAIS</label>
                            <select class="form-control select2" name="country_e" id="country_e" onchange="EditClientModalCountryGetDepartament(this)">
                                <option value="">Seleccione</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="form-group c_form_group">
                            <label for="departament_e">DEPARTAMENTO</label>
                            <select class="form-control select2" name="departament_e" id="departament_e" onchange="EditClientModalDepartamentGetCity(this)">
                                <option value="">Seleccione</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-6">
                        <div class="form-group c_form_group">
                            <label for="city_e">CIUDAD</label>
                            <select class="form-control select2" name="city_e" id="city_e">
                                <option value="">Seleccione</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="form-group c_form_group">
                            <label for="number_phone_e">TELEFONO</label>
                            <input type="number" class="form-control" id="number_phone_e" name="number_phone_e" onblur="Trim(this)" onkeyup="UpperCase(this)">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-6">
                        <div class="form-group c_form_group">
                            <label for="zone_e">ZONA</label>
                            <input type="text" class="form-control" id="zone_e" name="zone_e" onblur="Trim(this)" onkeyup="UpperCase(this)">
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="form-group c_form_group">
                            <label for="email_e">CORREO ELECTRONICO</label>
                            <input type="email" class="form-control" id="email_e" name="email_e" onblur="Trim(this)" onkeyup="UpperCase(this)">
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal" title="Cerrar ventana.">
                    <i class="fas fa-xmark"></i>
                </button>
                <button type="button" class="btn btn-primary" id="EditClientButton" onclick="" title="Actualizar cliente.">
                    <i class="fas fa-floppy-disk"></i>
                </button>
            </div>
        </div>
    </div>
</div>
