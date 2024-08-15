<div class="modal" id="UploadFilterModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" data-backdrop="static" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable" role="document">
        <div class="modal-content" id="content">
            <div class="modal-header w-100">
                <div class="text-center w-100" style="background: white;">
                    <label style="font-size:20px;font-weight:bold;">CARGAR CORTE INICIAL</label>
                </div>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="form-group">
                            <label for="file_u">ARCHIVO</label>
                            <div class="input-group">
                                <input type="file" class="form-control dropify" id="file_u" name="file_u" accept=".csv, .xls, .xlsx">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <a type="button" href="{{ asset('Formato_CORTE.xlsx') }}" class="btn btn-success" title="Formato de excel para cargar corte.">
                    <i class="fas fa-file-excel"></i>
                </a>
                <button type="button" class="btn btn-secondary" data-dismiss="modal" title="Cerrar ventana.">
                    <i class="fas fa-xmark"></i>
                </button>
                <button type="button" class="btn btn-primary" id="UploadFilterButton" onclick="UploadFilter()" title="Cargar cortes.">
                    <i class="fas fa-floppy-disk"></i>
                </button>
            </div>
        </div>
    </div>
</div>
