<div class="modal" id="PasswordUserModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
    data-backdrop="static" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header w-100">
                <div class="text-center w-100" style="background: white;">
                    <label style="font-size:20px;font-weight:bold;">Cambiar Contraseña</label>
                </div>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group c_form_group">
                    <label for="email_p">Correo Electronico</label>
                    <input type="text" class="form-control" id="email_p" name="email_p" readonly>
                </div>
                <div class="form-group c_form_group">
                    <label for="password_p">Nueva contraseña</label>
                    <div class="input-group">
                        <input type="password" class="form-control" id="password_p" name="password_p" placeholder="Contraseña">
                        <div class="input-group-append">
                            <span class="input-group-text" id="password-toggle" onclick="PasswordUserVisibility('password_p')">
                                <i class="fas fa-eye"></i>
                            </span>
                        </div>
                    </div>
                </div>
                <div class="form-group c_form_group">
                    <label for="password_confirmation_p">Confirmacion contraseña</label>
                    <div class="input-group">
                        <input type="password" class="form-control" id="password_confirmation_p" name="password_confirmation_p" placeholder="Confirmacion de contraseña">
                        <div class="input-group-append">
                            <span class="input-group-text" id="password-toggle" onclick="PasswordUserVisibility('password_confirmation_p')">
                                <i class="fas fa-eye"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal" title="Cerrar ventana.">
                    <i class="fas fa-xmark"></i>
                </button>
                <button type="button" class="btn btn-primary" id="PasswordUserButton" onclick="" title="Guardar contraseña.">
                    <i class="fas fa-floppy-disk"></i>
                </button>
            </div>
        </div>
    </div>
</div>
