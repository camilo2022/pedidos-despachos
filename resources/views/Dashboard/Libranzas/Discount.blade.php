<div class="modal fade" id="DiscountLibranzaModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel">
    <div class="modal-dialog modal-lg modal-dialog-scrollable" role="document">
        <div class="modal-content">
            <div class="modal-header w-100">
                <div class="text-center w-100" style="background: white;">
                    <label style="font-size:20px;font-weight:bold;">DESCUENTO LIBRANZA</label>
                </div>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row text-center">
                    <div class="col-lg-3">
                        <i class="fas fa-dollar-sign" style="color: rgb(0, 123, 255); font-size: 2.0em !important;"></i>
                        <strong style="color: rgb(0, 123, 255); font-size: 2.0em !important;" id="total_d" data-value="0">0</strong>
                        <small>Total</small>
                    </div>
                    <div class="col-lg-3">
                        <i class="fas fa-dollar-sign" style="color: rgb(112, 225, 1); font-size: 2.0em !important;"></i>
                        <strong style="color: rgb(112, 225, 1); font-size: 2.0em !important;" id="pay_d" data-value="0">0</strong>
                        <small>Pagado</small>
                    </div>
                    <div class="col-lg-3">
                        <i class="fas fa-dollar-sign" style="color: rgb(255, 0, 0); font-size: 2.0em !important;"></i>
                        <strong style="color: rgb(255, 0, 0); font-size: 2.0em !important;" id="debt_d" data-value="0">0</strong>
                        <small>Deuda</small>
                    </div>
                    <div class="col-lg-3">
                        <i class="fas fa-dollar-sign" style="color: rgb(52, 58, 64); font-size: 2.0em !important;"></i>
                        <strong style="color: rgb(52, 58, 64); font-size: 2.0em !important;" id="share_d" data-value="0">0</strong>
                        <small>Cuota</small>
                    </div>
                </div>
                <div class="form-group c_form_group">
                    <label for="value_d">VALOR A DESCONTAR</label>
                    <input type="number" class="form-control" name="value_d" id="value_d" onkeyup="DiscountLibranzaCalculate(this)">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal" title="Cerrar ventana.">
                    <i class="fas fa-xmark"></i>
                </button>
                <button type="button" class="btn btn-primary" id="DiscountLibranzaButton" onclick="" title="Descontar libranza.">
                    <i class="fas fa-floppy-disk"></i>
                </button>
            </div>
        </div>
    </div>
</div>
