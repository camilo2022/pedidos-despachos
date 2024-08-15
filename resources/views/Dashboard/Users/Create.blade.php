<div class="modal fade bd-example-modal-lg" id="CreateUserModal" tabindex="-1" role="dialog"
    aria-labelledby="exampleModalLabel" data-backdrop="static" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header w-100">
                <div class="text-center w-100" style="background: white;">
                    <label style="font-size:20px;font-weight:bold;">Creacion de Usuario</label>
                </div>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-lg-6">
                        <div class="form-group c_form_group">
                            <label for="name_c">Nombres</label>
                            <input type="text" class="form-control" id="name_c" name="name_c" onblur="Trim(this)" onkeyup="UpperCase(this)" placeholder="Nombres">
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="form-group c_form_group">
                            <label for="last_name_c">Apellidos</label>
                            <input type="text" class="form-control" id="last_name_c" name="last_name_c" onblur="Trim(this)" onkeyup="UpperCase(this)" placeholder="Apellidos">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-6">
                        <div class="form-group c_form_group">
                            <label for="document_number_c">Numero de documento</label>
                            <input type="text" class="form-control" id="document_number_c" name="document_number_c" onkeypress="Numbers(event)" onblur="Trim(this)" placeholder="Numero de documento">
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="form-group c_form_group">
                            <label for="phone_number_c">Numero de telefono</label>
                            <input type="text" class="form-control" id="phone_number_c" name="phone_number_c" onkeypress="Numbers(event)" onblur="Trim(this)" placeholder="Numero de telefono">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-6">
                        <div class="form-group c_form_group">
                            <label for="address_c">Direccion</label>
                            <input type="text" class="form-control" id="address_c" name="address_c" onblur="Trim(this)" onkeyup="UpperCase(this)" placeholder="Direccion">
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="form-group c_form_group">
                            <label for="email_c">Correo Electronico</label>
                            <input type="email" class="form-control" id="email_c" name="email_c" onblur="Trim(this)" onkeyup="UpperCase(this)" placeholder="Correo Electronico">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-6">
                        <div class="form-group c_form_group">
                            <label for="title_c">Titulo</label>
                            <select class="form-control" name="title_c" id="title_c">
                                <option value="">Seleccione</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="form-group c_form_group">
                            <label for="password_c">Contraseña</label>
                            <div class="input-group">
                                <input type="password" class="form-control" id="password_c" name="password_c" placeholder="Contraseña">
                                <div class="input-group-append">
                                    <span class="input-group-text" id="password-toggle" onclick="PasswordUserVisibility('password_c')">
                                        <i class="fas fa-eye"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-6">
                        <div class="form-group c_form_group">
                            <label for="zone_c">Zona</label>
                            <select class="form-control" name="zone_c" id="zone_c">
                                <option value="">Seleccione</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="form-group c_form_group">
                            <label for="password_confirmation_c">Contraseña</label>
                            <div class="input-group">
                                <input type="password" class="form-control" id="password_confirmation_c" name="password_confirmation_c" placeholder="Confirmacion de contraseña">
                                <div class="input-group-append">
                                    <span class="input-group-text" id="password-toggle" onclick="PasswordUserVisibility('password_confirmation_c')">
                                        <i class="fas fa-eye"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal" title="Cerrar ventana.">
                    <i class="fas fa-xmark"></i>
                </button>
                <button type="button" class="btn btn-primary" id="CreateUserButton" onclick="CreateUser()" title="Guardar usuario.">
                    <i class="fas fa-floppy-disk"></i>
                </button>
            </div>
        </div>
    </div>
</div>
