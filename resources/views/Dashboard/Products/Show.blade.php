<div class="modal" id="ShowProductModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" data-backdrop="static" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable" role="document">
        <div class="modal-content">
            <div class="modal-header w-100">
                <div class="text-center w-100" style="background: white;">
                    <label style="font-size:20px;font-weight:bold;">CARGAR FOTOS Y VIDEOS</label>
                </div>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-lg-4">
                        <div class="form-group c_form_group">
                            <label for="photo_s">FOTO PRINCIPAL</label>
                            <div class="input-group">
                                <input type="file" class="form-control dropify" id="photo_s" name="photo_s"
                                accept=".jpg, .jpeg, .png" data-default-file="">
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="form-group c_form_group">
                            <label for="photos_s">FOTOS (MULTIPLE)</label>
                            <div class="input-group">
                                <input type="file" class="form-control dropify" id="photos_s" name="photos_s"
                                accept=".jpg, .jpeg, .png" data-default-file="" multiple>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="form-group c_form_group">
                            <label for="videos_s">VIDEOS (MULTIPLE)</label>
                            <div class="input-group">
                                <input type="file" class="form-control dropify" id="videos_s" name="videos_s"
                                accept=".mp4, .webm" data-default-file="" multiple>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12" id="tableFile_s">

                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal" title="Cerrar ventana">
                    <i class="fas fa-xmark"></i>
                </button>
                <button type="button" class="btn btn-primary" id="ShowProductChargeButton" onclick="" title="Guardar archivos del producto.">
                    <i class="fas fa-floppy-disk"></i>
                </button>
            </div>
        </div>
    </div>
</div>