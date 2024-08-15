<div class="modal" id="DataClientModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" data-backdrop="static" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable" role="document">
        <div class="modal-content">
            <div class="modal-header w-100">
                <div class="text-center w-100" style="background: white;">
                    <label style="font-size:20px;font-weight:bold;">REFERENCIAS PERSONAL/COMERCIAL Y DOCUMENTOS DEL CLIENTE</label>
                </div>
                <button type="button" class="close close_modal" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-lg-6">
                        <label id="compra_d">REFERENCIA COMPRAS</label>
                        <div class="btn-group">
                            <button type="button" class="btn btn-light btn-sm dropdown-toggle" data-toggle="dropdown" data-offset="-52" aria-expanded="false">
                                <i class="fas fa-bars"></i>
                            </button>
                            <div class="dropdown-menu" role="menu" style="">
                                <a type="button" onclick="DataClientModalResetReference('compra', 'cartera')" class="dropdown-item">CARTERA</a>
                                <div class="dropdown-divider"></div>
                                <a type="button" onclick="DataClientModalResetReference('compra', 'bodega')" class="dropdown-item">BODEGA</a>
                                <div class="dropdown-divider"></div>
                                <a type="button" onclick="DataClientModalResetReference('compra', 'administrador')" class="dropdown-item">ADMINISTRACION</a>
                            </div>
                        </div>
                        <div class="form-group c_form_group">
                            <label for="compra_name_d">NOMBRE</label>
                            <input type="text" class="form-control" id="compra_name_d" name="compra_name_d" onblur="Trim(this)" onkeyup="UpperCase(this)">
                        </div>
                        <div class="form-group c_form_group">
                            <label for="compra_last_name_d">APELLIDO</label>
                            <input type="text" class="form-control" id="compra_last_name_d" name="compra_last_name_d" onblur="Trim(this)" onkeyup="UpperCase(this)">
                        </div>
                        <div class="form-group c_form_group">
                            <label for="compra_phone_number_d">TELEFONO</label>
                            <input type="text" class="form-control" id="compra_phone_number_d" name="compra_phone_number_d" onblur="Trim(this)" onkeyup="UpperCase(this)">
                        </div>
                        <div class="form-group c_form_group">
                            <label for="compra_email_d">CORREO ELECTRONICO</label>
                            <input type="text" class="form-control" id="compra_email_d" name="compra_email_d" onblur="Trim(this)" onkeyup="UpperCase(this)">
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <label id="cartera_d">REFERENCIA CARTERA</label>
                        <div class="btn-group">
                            <button type="button" class="btn btn-light btn-sm dropdown-toggle" data-toggle="dropdown" data-offset="-52" aria-expanded="false">
                                <i class="fas fa-bars"></i>
                            </button>
                            <div class="dropdown-menu" role="menu" style="">
                                <a type="button" onclick="DataClientModalResetReference('cartera', 'compra')" class="dropdown-item">COMPRAS</a>
                                <div class="dropdown-divider"></div>
                                <a type="button" onclick="DataClientModalResetReference('cartera', 'bodega')" class="dropdown-item">BODEGA</a>
                                <div class="dropdown-divider"></div>
                                <a type="button" onclick="DataClientModalResetReference('cartera', 'administrador')" class="dropdown-item">ADMINISTRACION</a>
                            </div>
                        </div>
                        <div class="form-group c_form_group">
                            <label for="cartera_name_d">NOMBRE</label>
                            <input type="text" class="form-control" id="cartera_name_d" name="cartera_name_d" onblur="Trim(this)" onkeyup="UpperCase(this)">
                        </div>
                        <div class="form-group c_form_group">
                            <label for="cartera_last_name_d">APELLIDO</label>
                            <input type="text" class="form-control" id="cartera_last_name_d" name="cartera_last_name_d" onblur="Trim(this)" onkeyup="UpperCase(this)">
                        </div>
                        <div class="form-group c_form_group">
                            <label for="cartera_phone_number_d">TELEFONO</label>
                            <input type="text" class="form-control" id="cartera_phone_number_d" name="cartera_phone_number_d" onblur="Trim(this)" onkeyup="UpperCase(this)">
                        </div>
                        <div class="form-group c_form_group">
                            <label for="cartera_email_d">CORREO ELECTRONICO</label>
                            <input type="text" class="form-control" id="cartera_email_d" name="cartera_email_d" onblur="Trim(this)" onkeyup="UpperCase(this)">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-6">
                        <label id="bodega_d">REFERENCIA BODEGA</label>
                        <div class="btn-group">
                            <button type="button" class="btn btn-light btn-sm dropdown-toggle" data-toggle="dropdown" data-offset="-52" aria-expanded="false">
                                <i class="fas fa-bars"></i>
                            </button>
                            <div class="dropdown-menu" role="menu" style="">
                                <a type="button" onclick="DataClientModalResetReference('bodega', 'compra')" class="dropdown-item">COMPRAS</a>
                                <div class="dropdown-divider"></div>
                                <a type="button" onclick="DataClientModalResetReference('bodega', 'cartera')" class="dropdown-item">CARTERA</a>
                                <div class="dropdown-divider"></div>
                                <a type="button" onclick="DataClientModalResetReference('bodega', 'administrador')" class="dropdown-item">ADMINISTRACION</a>
                            </div>
                        </div>
                        <div class="form-group c_form_group">
                            <label for="bodega_name_d">NOMBRE</label>
                            <input type="text" class="form-control" id="bodega_name_d" name="bodega_name_d" onblur="Trim(this)" onkeyup="UpperCase(this)">
                        </div>
                        <div class="form-group c_form_group">
                            <label for="bodega_last_name_d">APELLIDO</label>
                            <input type="text" class="form-control" id="bodega_last_name_d" name="bodega_last_name_d" onblur="Trim(this)" onkeyup="UpperCase(this)">
                        </div>
                        <div class="form-group c_form_group">
                            <label for="bodega_phone_number_d">TELEFONO</label>
                            <input type="text" class="form-control" id="bodega_phone_number_d" name="bodega_phone_number_d" onblur="Trim(this)" onkeyup="UpperCase(this)">
                        </div>
                        <div class="form-group c_form_group">
                            <label for="bodega_email_d">CORREO ELECTRONICO</label>
                            <input type="text" class="form-control" id="bodega_email_d" name="bodega_email_d" onblur="Trim(this)" onkeyup="UpperCase(this)">
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <label id="administrador_d">REFERENCIA ADMINISTRADOR</label>
                        <div class="btn-group">
                            <button type="button" class="btn btn-light btn-sm dropdown-toggle" data-toggle="dropdown" data-offset="-52" aria-expanded="false">
                                <i class="fas fa-bars"></i>
                            </button>
                            <div class="dropdown-menu" role="menu" style="">
                                <a type="button" onclick="DataClientModalResetReference('administrador', 'compra')" class="dropdown-item">COMPRAS</a>
                                <div class="dropdown-divider"></div>
                                <a type="button" onclick="DataClientModalResetReference('administrador', 'cartera')" class="dropdown-item">CARTERA</a>
                                <div class="dropdown-divider"></div>
                                <a type="button" onclick="DataClientModalResetReference('administrador', 'bodega')" class="dropdown-item">BODEGA</a>
                            </div>
                        </div>
                        <div class="form-group c_form_group">
                            <label for="administrador_name_d">NOMBRE</label>
                            <input type="text" class="form-control" id="administrador_name_d" name="administrador_name_d" onblur="Trim(this)" onkeyup="UpperCase(this)">
                        </div>
                        <div class="form-group c_form_group">
                            <label for="administrador_last_name_d">APELLIDO</label>
                            <input type="text" class="form-control" id="administrador_last_name_d" name="administrador_last_name_d" onblur="Trim(this)" onkeyup="UpperCase(this)">
                        </div>
                        <div class="form-group c_form_group">
                            <label for="administrador_phone_number_d">TELEFONO</label>
                            <input type="text" class="form-control" id="administrador_phone_number_d" name="administrador_phone_number_d" onblur="Trim(this)" onkeyup="UpperCase(this)">
                        </div>
                        <div class="form-group c_form_group">
                            <label for="administrador_email_d">CORREO ELECTRONICO</label>
                            <input type="text" class="form-control" id="administrador_email_d" name="administrador_email_d" onblur="Trim(this)" onkeyup="UpperCase(this)">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-6" id="div_chamber_of_commerce_d">
                        <div class="form-group c_form_group">
                            <label for="chamber_of_commerce_d">CAMARA DE COMERCIO (PDF)</label>
                            <div class="input-group">
                                <input type="file" class="form-control dropify" id="chamber_of_commerce_d" name="chamber_of_commerce_d"
                                accept=".pdf" data-default-file="">
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6" id="div_rut_d">
                        <div class="form-group c_form_group">
                            <label for="rut_d">RUT (PDF)</label>
                            <div class="input-group">
                                <input type="file" class="form-control dropify" id="rut_d" name="rut_d"
                                accept=".pdf" data-default-file="">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-6" id="div_identity_card_d">
                        <div class="form-group c_form_group">
                            <label for="identity_card_d">DOCUMENTO DE IDENTIFICACION (JPEG, JPG, PNG, PDF)</label>
                            <div class="input-group">
                                <input type="file" class="form-control dropify" id="identity_card_d" name="identity_card_d"
                                accept=".jpeg, .jpg, .png, .pdf" data-default-file="">
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6" id="div_signature_warranty_d">
                        <div class="form-group c_form_group">
                            <label for="signature_warranty_d">FIRMA DE GARANTIA (JPEG, JPG, PNG, PDF)</label>
                            <div class="input-group">
                                <input type="file" class="form-control dropify" id="signature_warranty_d" name="signature_warranty_d"
                                accept=".jpeg, .jpg, .png, .pdf" data-default-file="">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12" class="table-responsive" id="div_tableReferences_d">

                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12" class="table-responsive" id="div_tableFiles_d">

                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary close_modal" data-dismiss="modal" title="Cerrar ventana.">
                    <i class="fas fa-xmark"></i>
                </button>
                <button type="button" class="btn btn-primary" id="DataClientButton" onclick="" title="Actualizar referencias y documentos del cliente cliente.">
                    <i class="fas fa-floppy-disk"></i>
                </button>
            </div>
        </div>
    </div>
</div>
