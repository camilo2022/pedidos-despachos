<div class="modal fade bd-example-modal-lg" id="AssignRoleAndPermissionUserModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
    data-backdrop="static" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable" role="document">
        <div class="modal-content">
            <div class="modal-header w-100">
                <div class="text-center w-100" style="background: white;">
                    <label style="font-size:20px;font-weight:bold;">Asignacion de Rol y Permisos</label>
                </div>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group c_form_group">
                    <label for="email_a">Correo Electronico</label>
                    <input type="text" class="form-control" id="email_a" name="email" readonly>
                </div>
                <div class="form-group c_form_group">
                    <label for="permissions-container-assign">Roles y Permisos</label>
                    <div id="permissions-container-assign">

                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal" title="Cerrar ventana.">
                    <i class="fas fa-xmark"></i>
                </button>
            </div>
        </div>
    </div>
</div>
