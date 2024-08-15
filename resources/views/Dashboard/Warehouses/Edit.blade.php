<div class="modal" id="EditWarehouseModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" data-backdrop="static" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header w-100">
                <div class="text-center w-100" style="background: white;">
                    <label style="font-size:20px;font-weight:bold;">Edicion de Bodega</label>
                </div>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="col-lg-12">
                    <div class="form-group c_form_group">
                        <label for="name_e">Nombre</label>
                        <input type="text" class="form-control" id="name_e" name="name_e" onblur="Trim(this)" onkeyup="UpperCase(this)" placeholder="Nombre">
                    </div>
                </div>
                <div class="col-lg-12">
                    <div class="form-group c_form_group">
                        <label for="code_e">Codigo</label>
                        <input type="text" class="form-control" id="code_e" name="code_e" onblur="Trim(this)" onkeyup="UpperCase(this)" placeholder="Codigo">
                    </div>
                </div>
                <div class="col-lg-12">
                    <div class="form-group c_form_group">
                        <div class="icheck-primary"><input type="checkbox" id="to_cut_e" name="to_cut_e"><label for="to_cut_e">¿Bodega para cargar el corte incial para aplicar reglas de filtrado?</label></div>
                    </div>
                </div>
                <div class="col-lg-12">
                    <div class="form-group c_form_group">
                        <div class="icheck-primary"><input type="checkbox" id="to_transit_e" name="to_transit_e"><label for="to_transit_e">¿El inventario de esta bodega se monstrará en el filtro como unidades en proceso?</label></div>
                    </div>
                </div>
                <div class="col-lg-12">
                    <div class="form-group c_form_group">
                        <div class="icheck-primary"><input type="checkbox" id="to_discount_e" name="to_discount_e"><label for="to_discount_e">¿El inventario de esta bodega estará disponible para pedidos y filtro?</label></div>
                    </div>
                </div>
                <div class="col-lg-12">
                    <div class="form-group c_form_group">
                        <div class="icheck-primary"><input type="checkbox" id="to_exclusive_e" name="to_exclusive_e"><label for="to_exclusive_e">¿El inventario de esta bodega estará disponible para pedidos especiales?</label></div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal" title="Cerrar ventana">
                    <i class="fas fa-xmark"></i>
                </button>
                <button type="button" class="btn btn-primary" id="EditWarehouseButton" onclick="" title="Actualizar bodega.">
                    <i class="fas fa-floppy-disk"></i>
                </button>
            </div>
        </div>
    </div>
</div>
