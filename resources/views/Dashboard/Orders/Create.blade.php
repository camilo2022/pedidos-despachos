<div class="modal fade" id="CreateOrderModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" data-backdrop="static" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable" role="document">
        <div class="modal-content">
            <div class="modal-header w-100">
                <div class="text-center w-100" style="background: white;">
                    <label style="font-size:20px;font-weight:bold;">CREAR PEDIDO</label>
                </div>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group c_form_group">
                    <label for="client_id_c">CLIENTE</label>
                    <select class="form-control select2" id="client_id_c" name="client_id_c" onchange="CreateOrderGetClient(this)">
                        <option value="">Seleccione</option>
                    </select>
                </div>
                <div class="form-group c_form_group">
                    <label for="seller_observation_c">OBSERVACION</label>
                    <textarea class="form-control" id="seller_observation_c" name="seller_observation_c" cols="30" rows="3"></textarea>
                </div>
                <div class="form-group c_form_group">
                    <label for="dispatch_type_c">CUANDO DESPACHAR</label>
                    <select class="form-control" id="dispatch_type_c" name="dispatch_type_c" onchange="CreateOrderModalDispatchGetDispatchDate(this)">
                        <option value="">Seleccione</option>
                        <option value="De inmediato">De inmediato</option>
                        <option value="Antes de">Antes de</option>
                        <option value="Despues de">Despues de</option>
                        <option value="Total">Total</option>
                        <option value="Semanal">Semanal</option>
                    </select>
                </div>
                <div class="form-group c_form_group" id="div_dispatch_date_c">
                    <label for="dispatch_date_c">FECHA DESPACHAR</label>
                    <input type="date" class="form-control" name="dispatch_date_c" id="dispatch_date_c">
                </div>
                <div class="row">
                    <div class="col-lg-6">
                        <div class="form-group c_form_group">
                            <label for="seller_dispatch_official_c">PORCENTAJE DESPACHO OFICIAL</label>
                            <input type="number" class="form-control" name="seller_dispatch_official_c" id="seller_dispatch_official_c" pattern="[0-9]+" value="100">
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="form-group c_form_group">
                            <label for="seller_dispatch_document_c">PORCENTAJE DESPACHO DOCUMENTO</label>
                            <input type="number" class="form-control" name="seller_dispatch_document_c" id="seller_dispatch_document_c" pattern="[0-9]+" value="0">
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal" title="Cerrar ventana.">
                    <i class="fas fa-xmark"></i>
                </button>
                <button type="button" class="btn btn-primary" id="CreateOrderButton" onclick="CreateOrder()" title="Guardar pedido.">
                    <i class="fas fa-floppy-disk"></i>
                </button>
            </div>
        </div>
    </div>
</div>
