<div class="modal" id="CreateWarehouseModal" tabindex="-1" role="dialog"
    aria-labelledby="exampleModalLabel" data-backdrop="static" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header w-100">
                <div class="text-center w-100" style="background: white;">
                    <label style="font-size:20px;font-weight:bold;">Creacion de Bodega</label>
                </div>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="col-lg-12">
                    <div class="form-group c_form_group">
                        <label for="name_c">Nombre</label>
                        <input type="text" class="form-control" id="name_c" name="name_c" onblur="Trim(this)" onkeyup="UpperCase(this)" placeholder="Nombre">
                    </div>
                </div>
                <div class="col-lg-12">
                    <div class="form-group c_form_group">
                        <label for="code_c">Codigo</label>
                        <input type="text" class="form-control" id="code_c" name="code_c" onblur="Trim(this)" onkeyup="UpperCase(this)" placeholder="Codigo">
                    </div>
                </div>
                <div class="col-lg-12">
                    <div class="form-group c_form_group">
                        <div class="icheck-primary"><input type="checkbox" id="to_cut_c" name="to_cut_c"><label for="to_cut_c">¿Bodega para cargar el corte incial para aplicar reglas de filtrado?</label></div>
                    </div>
                </div>
                <div class="col-lg-12">
                    <div class="form-group c_form_group">
                        <div class="icheck-primary"><input type="checkbox" id="to_transit_c" name="to_transit_c"><label for="to_transit_c">¿El inventario de esta bodega se monstrará en el filtro como unidades en proceso?</label></div>
                    </div>
                </div>
                <div class="col-lg-12">
                    <div class="form-group c_form_group">
                        <div class="icheck-primary"><input type="checkbox" id="to_discount_c" name="to_discount_c"><label for="to_discount_c">¿El inventario de esta bodega estará disponible para pedidos y filtro?</label></div>
                    </div>
                </div>
                <div class="col-lg-12">
                    <div class="form-group c_form_group">
                        <div class="icheck-primary"><input type="checkbox" id="to_exclusive_c" name="to_exclusive_c"><label for="to_exclusive_c">¿El inventario de esta bodega estará disponible para pedidos especiales?</label></div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal" title="Cerrar ventana">
                    <i class="fas fa-xmark"></i>
                </button>
                <button type="button" class="btn btn-primary" id="CreateWarehouseButton" onclick="CreateWarehouse()" title="Guardar bodega.">
                    <i class="fas fa-floppy-disk"></i>
                </button>
            </div>
        </div>
    </div>
</div>
