<div class="modal" id="CreateColorModal" tabindex="-1" role="dialog"
    aria-labelledby="exampleModalLabel" data-backdrop="static" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header w-100">
                <div class="text-center w-100" style="background: white;">
                    <label style="font-size:20px;font-weight:bold;">CREAR COLOR</label>
                </div>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="col-lg-12">
                    <div class="form-group">
                        <label for="name_c">NOMBRE</label>
                        <input type="text" class="form-control" id="name_c" name="name_c">
                    </div>
                </div>
                <div class="col-lg-12">
                    <div class="form-group">
                        <label for="code_c">CODIGO</label>
                        <input type="text" class="form-control" id="code_c" name="code_c">
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal" title="Cerrar ventana.">
                    <i class="fas fa-xmark"></i>
                </button>
                <button type="button" class="btn btn-primary" id="CreateColorButton" onclick="CreateColor()" title="Guardar color.">
                    <i class="fas fa-floppy-disk"></i>
                </button>
            </div>
        </div>
    </div>
</div>
