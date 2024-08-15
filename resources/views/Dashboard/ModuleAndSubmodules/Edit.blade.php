<div class="modal fade bd-example-modal-lg" id="EditModuleAndSubmodulesModal" tabindex="-1" role="dialog"
    aria-labelledby="exampleModalLabel" data-backdrop="static" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable" role="document">
        <div class="modal-content">
            <div class="modal-header w-100">
                <div class="text-center w-100" style="background: white;">
                    <label style="font-size:20px;font-weight:bold;">EDITAR MODULO Y SUBMODULOS</label>
                </div>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group c_form_group">
                    <label for="module">MODULO</label>
                    <i class="ml-2 far fa-circle-question" onclick="SuggestionModule()"></i>
                    <div class="input-group">
                        <input type="text" class="form-control" id="module_e" name="module">
                        <div class="input-group-append">
                            <span class="input-group-text">
                                <i class="fas fa-sliders"></i>
                            </span>
                        </div>
                    </div>
                </div>
                <div class="form-group c_form_group">
                    <label for="icon_e">ICONO MODULO</label>
                    <i class="ml-2 far fa-circle-question" onclick="SuggestionModuleIcon()"></i>
                    <div class="input-group">
                        <input type="text" class="form-control" id="icon_e" name="icon_e" onkeyup="EditModuleAndSubmodulesChangeClassIcon(this, 'icon_module_e')">
                        <div class="input-group-append">
                            <span class="input-group-text">
                                <i class="" id="icon_module_e"></i>
                            </span>
                        </div>
                    </div>
                </div>
                <div class="form-group c_form_group">
                    <label for="">ROLES DE ACCESO</label>
                    <i class="ml-2 far fa-circle-question" onclick="SuggestionModuleRoles()"></i>
                    <div class="card collapsed-card">
                        <div class="card-header border-0 ui-sortable-handle">
                            <h3 class="card-title mt-1">
                                ROLES
                            </h3>
                            <div class="card-tools">
                                <button type="button" class="btn btn-info btn-sm ml-2" data-card-widget="collapse">
                                    <i class="fas fa-plus"></i>
                                </button>
                                <button type="button" class="btn btn-danger btn-sm ml-2" data-card-widget="remove">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </div>
                        <div class="card-body" style="display: none;">
                            <div class="table-responsive pb-4" id="roles_access_e">

                            </div>
                        </div>
                    </div>
                </div>
                <div class="form-group c_form_group">
                    <label for="submodule">SUBMODULOS</label>
                    <i class="ml-2 far fa-circle-question" onclick="SuggestionSubmodule()"></i>
                    <div class="submodules_e">

                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-success" id="EditModuleAndSubmodulesAddSubmoduleButton" title="Agregar submodulo" onclick="EditModuleAndSubmodulesAddSubmodule(null, '', '', undefined, '', '')">
                    <i class="fas fa-plus"></i>
                </button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal" title="Cerrar ventana">
                    <i class="fas fa-xmark"></i>
                </button>
                <button type="button" class="btn btn-primary" id="EditModuleAndSubmodulesButton" onclick="" title="Guardar Modulo y submodulos">
                    <i class="fas fa-floppy-disk"></i>
                </button>
            </div>
        </div>
    </div>
</div>
