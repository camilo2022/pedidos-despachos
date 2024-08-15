<div class="modal fade" id="AddOrderPackingDetailModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" data-backdrop="static" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header w-100">
                <div class="text-center w-100" style="background: white;">
                    <label style="font-size:20px;font-weight:bold;">EMPACAR UNIDADES</label>
                </div>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-lg-4">
                        <div class="form-group c_form_group">
                            <label for="reference_a">REFERENCIA</label>
                            <input type="text" class="form-control" id="reference_a" name="reference_a" readonly>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="form-group c_form_group">
                            <label for="size_a">TALLA</label>
                            <input type="text" class="form-control" id="size_a" name="size_a" readonly>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="form-group c_form_group">
                            <label for="color_a">COLOR</label>
                            <input type="text" class="form-control" id="color_a" name="color_a" readonly>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12">
                        <div class="form-group c_form_group">
                            <label for="quantity_a">CANTIDAD ( PENDIENTE : <span id="missing_a">0</span> )</label>
                            <input type="number" class="form-control" id="quantity_a" name="quantity_a">
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal" title="Cerrar ventana.">
                    <i class="fas fa-xmark"></i>
                </button>
                <button type="button" class="btn btn-primary" id="AddOrderPackingDetailButton" onclick="" title="Guardar unidades al empaque de la orden de empacado.">
                    <i class="fas fa-floppy-disk"></i>
                </button>
            </div>
        </div>
    </div>
</div>
