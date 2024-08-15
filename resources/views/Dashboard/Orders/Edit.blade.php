<div class="modal" id="EditOrderModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" data-backdrop="static" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable" role="document">
        <div class="modal-content">
            <div class="modal-header w-100">
                <div class="text-center w-100" style="background: white;">
                    <label style="font-size:20px;font-weight:bold;">EDITAR PEDIDO</label>
                </div>
                <button type="button" class="close close_modal" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group c_form_group">
                    <label for="client_id_e">CLIENTE</label>
                    <select class="form-control select2" id="client_id_e" name="client_id_e" onchange="EditOrderGetClient(this)">
                        <option value="">Seleccione</option>
                    </select>
                </div>
                <div class="form-group c_form_group">
                    <label for="seller_observation_e">OBSERVACION</label>
                    <textarea class="form-control" id="seller_observation_e" name="seller_observation_e" cols="30" rows="3"></textarea>
                </div>
                <div class="form-group c_form_group">
                    <label for="dispatch_type_e">CUANDO DESPACHAR</label>
                    <select class="form-control" id="dispatch_type_e" name="dispatch_type_e" onchange="EditOrderModalDispatchGetDispatchDate(this)">
                        <option value="">Seleccione</option>
                        <option value="De inmediato">De inmediato</option>
                        <option value="Antes de">Antes de</option>
                        <option value="Despues de">Despues de</option>
                        <option value="Total">Total</option>
                        <option value="Semanal">Semanal</option>
                    </select>
                </div>
                <div class="form-group c_form_group" id="div_dispatch_date_e">
                    <label for="dispatch_date_e">FECHA DESPACHAR</label>
                    <input type="date" class="form-control" name="dispatch_date_e" id="dispatch_date_e">
                </div>
                <div class="row">
                    <div class="col-lg-6">
                        <div class="form-group c_form_group">
                            <label for="seller_dispatch_official_e">PORCENTAJE DESPACHO OFICIAL</label>
                            <input type="number" class="form-control" name="seller_dispatch_official_e" id="seller_dispatch_official_e" pattern="[0-9]+" value="100">
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="form-group c_form_group">
                            <label for="seller_dispatch_document_e">PORCENTAJE DESPACHO DOCUMENTO</label>
                            <input type="number" class="form-control" name="seller_dispatch_document_e" id="seller_dispatch_document_e" pattern="[0-9]+" value="0">
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary close_modal" data-dismiss="modal" title="Cerrar ventana.">
                    <i class="fas fa-xmark"></i>
                </button>
                <button type="button" class="btn btn-primary" id="EditOrderButton" onclick="" title="Actualizar pedido.">
                    <i class="fas fa-floppy-disk"></i>
                </button>
            </div>
        </div>
    </div>
</div>
