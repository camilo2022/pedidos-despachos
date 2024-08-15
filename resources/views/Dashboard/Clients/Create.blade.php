<div class="modal fade" id="CreateClientModal" tabindex="-1" role="dialog"
    aria-labelledby="exampleModalLabel" data-backdrop="static" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable" role="document">
        <div class="modal-content">
            <div class="modal-header w-100">
                <div class="text-center w-100" style="background: white;">
                    <label style="font-size:20px;font-weight:bold;">CREAR CLIENTE</label>
                </div>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-lg-6">
                        <div class="form-group c_form_group">
                            <label for="client_name_c">RAZON SOCIAL</label>
                            <input type="text" class="form-control" id="client_name_c" name="client_name_c" onblur="Trim(this)" onkeyup="UpperCase(this)">
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="form-group c_form_group">
                            <label for="client_address_c">DIRECCION</label>
                            <input type="text" class="form-control" id="client_address_c" name="client_address_c" onblur="Trim(this)" onkeyup="UpperCase(this)">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-6">
                        <div class="form-group c_form_group">
                            <label for="client_number_document_c">NUMERO DE DOCUMENTO</label>
                            <input type="number" class="form-control" id="client_number_document_c" name="client_number_document_c" onblur="Trim(this)" onkeyup="UpperCase(this)">
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="form-group c_form_group">
                            <label for="client_branch_code_c">CODIGO DE SUCURSAL</label>
                            <input type="number" class="form-control" id="client_branch_code_c" name="client_branch_code_c" onblur="Trim(this)" onkeyup="UpperCase(this)">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-6">
                        <div class="form-group c_form_group">
                            <label for="client_number_phone_c">TELEFONO CLIENTE</label>
                            <input type="number" class="form-control" id="client_number_phone_c" name="client_number_phone_c" onblur="Trim(this)" onkeyup="UpperCase(this)">
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="form-group c_form_group">
                            <label for="client_branch_name_c">SUCURSAL</label>
                            <input type="text" class="form-control" id="client_branch_name_c" name="client_branch_name_c" onblur="Trim(this)" onkeyup="UpperCase(this)">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-6">
                        <div class="form-group c_form_group">
                            <label for="client_branch_address_c">DIRECCION DE DESPACHO</label>
                            <input type="text" class="form-control" id="client_branch_address_c" name="client_branch_address_c" onblur="Trim(this)" onkeyup="UpperCase(this)">
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="form-group c_form_group">
                            <label for="client_branch_number_phone_c">TELEFONO SUCURSAL</label>
                            <input type="number" class="form-control" id="client_branch_number_phone_c" name="client_branch_number_phone_c" onblur="Trim(this)" onkeyup="UpperCase(this)">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-6">
                        <div class="form-group c_form_group">
                            <label for="country_c">PAIS</label>
                            <select class="form-control select2" name="country_c" id="country_c" onchange="CreateClientModalCountryGetDepartament(this)">
                                <option value="">Seleccione</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="form-group c_form_group">
                            <label for="departament_c">DEPARTAMENTO</label>
                            <select class="form-control select2" name="departament_c" id="departament_c" onchange="CreateClientModalDepartamentGetCity(this)">
                                <option value="">Seleccione</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-6">
                        <div class="form-group c_form_group">
                            <label for="city_c">CIUDAD</label>
                            <select class="form-control select2" name="city_c" id="city_c">
                                <option value="">Seleccione</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="form-group c_form_group">
                            <label for="number_phone_c">TELEFONO</label>
                            <input type="number" class="form-control" id="number_phone_c" name="number_phone_c" onblur="Trim(this)" onkeyup="UpperCase(this)">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-6">
                        <div class="form-group c_form_group">
                            <label for="zone_c">ZONA</label>
                            <input type="text" class="form-control" id="zone_c" name="zone_c" onblur="Trim(this)" onkeyup="UpperCase(this)">
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="form-group c_form_group">
                            <label for="email_c">CORREO ELECTRONICO</label>
                            <input type="email" class="form-control" id="email_c" name="email_c" onblur="Trim(this)" onkeyup="UpperCase(this)">
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal" title="Cerrar ventana.">
                    <i class="fas fa-xmark"></i>
                </button>
                <button type="button" class="btn btn-primary" id="CreateClientButton" onclick="CreateClient()" title="Guardar cliente.">
                    <i class="fas fa-floppy-disk"></i>
                </button>
            </div>
        </div>
    </div>
</div>
