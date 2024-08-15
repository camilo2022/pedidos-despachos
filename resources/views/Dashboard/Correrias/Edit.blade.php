<div class="modal fade bd-example-modal-lg" id="EditCorreriaModal" tabindex="-1" role="dialog"
    aria-labelledby="exampleModalLabel" data-backdrop="static" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header w-100">
                <div class="text-center w-100" style="background: white;">
                    <label style="font-size:20px;font-weight:bold;">EDITAR CORRERIA</label>
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
                            <input type="text" class="form-control" id="name_e" name="name_e" onblur="Trim(this)" onkeyup="UpperCase(this)" placeholder="Nombre">
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="form-group c_form_group">
                            <label for="code_e">CODIGO</label>
                            <input type="text" class="form-control" id="code_e" name="code_e" onblur="Trim(this)" onkeyup="UpperCase(this)" placeholder="Codigo">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-6">
                        <div class="form-group c_form_group">
                            <label for="start_date_e">FECHA INICIO</label>
                            <input type="date" class="form-control" id="start_date_e" name="start_date_e">
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="form-group c_form_group">
                            <label for="end_date_e">FECHA FIN</label>
                            <input type="date" class="form-control" id="end_date_e" name="end_date_e">
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal" title="Cerrar ventana.">
                    <i class="fas fa-xmark"></i>
                </button>
                <button type="button" class="btn btn-primary" id="EditCorreriaButton" onclick="" title="Actualizar correria y coleccion.">
                    <i class="fas fa-floppy-disk"></i>
                </button>
            </div>
        </div>
    </div>
</div>
