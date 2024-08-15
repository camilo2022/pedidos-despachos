<div class="modal fade bd-example-modal-lg" id="EditUserModal" tabindex="-1" role="dialog"
    aria-labelledby="exampleModalLabel" data-backdrop="static" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header w-100">
                <div class="text-center w-100" style="background: white;">
                    <label style="font-size:20px;font-weight:bold;">Edicion de Usuario</label>
                </div>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-lg-6">
                        <div class="form-group c_form_group">
                            <label for="name_e">Nombres</label>
                            <input type="text" class="form-control" id="name_e" name="name_e" onblur="Trim(this)" onkeyup="UpperCase(this)" placeholder="Nombres">
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="form-group c_form_group">
                            <label for="last_name_e">Apellidos</label>
                            <input type="text" class="form-control" id="last_name_e" name="last_name_e" onblur="Trim(this)" onkeyup="UpperCase(this)" placeholder="Apellidos">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-6">
                        <div class="form-group c_form_group">
                            <label for="document_number_e">Numero de documento</label>
                            <input type="text" class="form-control" id="document_number_e" name="document_number_e" onkeypress="Numbers(event)" onblur="Trim(this)" placeholder="Numero de documento">
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="form-group c_form_group">
                            <label for="phone_number_e">Numero de telefono</label>
                            <input type="text" class="form-control" id="phone_number_e" name="phone_number_e" onkeypress="Numbers(event)" onblur="Trim(this)" placeholder="Numero de telefono">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-6">
                        <div class="form-group c_form_group">
                            <label for="address_e">Direccion</label>
                            <input type="text" class="form-control" id="address_e" name="address_e" onblur="Trim(this)" onkeyup="UpperCase(this)" placeholder="Direccion">
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="form-group c_form_group">
                            <label for="email_e">Correo Electronico</label>
                            <input type="email" class="form-control" id="email_e" name="email_e" onblur="Trim(this)" onkeyup="UpperCase(this)" placeholder="Correo Electronico">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-6">
                        <div class="form-group c_form_group">
                            <label for="title_e">Titulo</label>
                            <select class="form-control" name="title_e" id="title_e">
                                <option value="">Seleccione</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="form-group c_form_group">
                            <label for="zone_e">Zona</label>
                            <select class="form-control" name="zone_e" id="zone_e">
                                <option value="">Seleccione</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal" title="Cerrar ventana.">
                    <i class="fas fa-xmark"></i>
                </button>
                <button type="button" class="btn btn-primary" id="EditUserButton" onclick="" title="Actualizar usuario.">
                    <i class="fas fa-floppy-disk"></i>
                </button>
            </div>
        </div>
    </div>
</div>
