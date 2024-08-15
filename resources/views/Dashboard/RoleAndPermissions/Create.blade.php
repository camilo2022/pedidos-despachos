<div class="modal fade" id="CreateRoleAndPermissionsModal" tabindex="-1" role="dialog"
    aria-labelledby="exampleModalLabel" data-backdrop="static" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable" role="document">
        <div class="modal-content">
            <div class="modal-header w-100">
                <div class="text-center w-100" style="background: white;">
                    <label style="font-size:20px;font-weight:bold;">Creacion de Rol y permisos</label>
                </div>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group c_form_group">
                    <label for="role_c">Rol</label>
                    <i class="ml-2 far fa-circle-question" onclick="SuggestionRole()"></i>
                    <input type="text" class="form-control" id="role_c" name="role_c" placeholder="Rol">
                </div>
                <div class="form-group c_form_group">
                    <label for="">Permisos</label>
                    <i class="ml-2 far fa-circle-question" onclick="SuggestionPermissions()"></i>
                    <div class="permissions_c">
                        <div class="form-group">
                            <input type="text" class="form-control" id="permission_c0" name="permissions_c[]" placeholder="Permiso">
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-success" id="CreateRoleAndPermissionsAddPermissionButton" data-count="1" onclick="CreateRoleAndPermissionsAddPermission(this)" title="Agregar permiso.">
                    <i class="fas fa-plus"></i>
                </button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal" title="Cerrar ventana.">
                    <i class="fas fa-xmark"></i>
                </button>
                <button type="button" class="btn btn-primary" id="CreateRoleAndPermissionsButton" onclick="CreateRoleAndPermissions()" title="Guardar Rol y permisos.">
                    <i class="fas fa-floppy-disk"></i>
                </button>
            </div>
        </div>
    </div>
</div>
