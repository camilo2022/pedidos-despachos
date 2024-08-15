<div class="modal" id="WalletClientModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" data-backdrop="static" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable" role="document">
        <div class="modal-content">
            <div class="modal-header w-100">
                <div class="text-center w-100" style="background: white;">
                    <label style="font-size:20px;font-weight:bold;">CARTERA CLIENTE</label>
                </div>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-lg-6">
                        <div class="form-group c_form_group">
                            <label for="client_w">CLIENTE</label>
                            <input type="text" class="form-control" id="client_w" name="client_w">
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="form-group c_form_group">
                            <label for="document_w">DOCUMENTO</label>
                            <input type="text" class="form-control" id="document_w" name="document_w">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-6">
                        <div class="form-group c_form_group">
                            <label for="zero_to_thirty_w">0 - 30 DIAS</label>
                            <input type="text" class="form-control" id="zero_to_thirty_w" name="zero_to_thirty_w" onblur="WalletClientModalResetInput(this)" oninput="WalletClientModalFormatInput(this)">
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="form-group c_form_group">
                            <label for="one_to_thirty_w">1 - 30 DIAS</label>
                            <input type="text" class="form-control" id="one_to_thirty_w" name="one_to_thirty_w" onblur="WalletClientModalResetInput(this)" oninput="WalletClientModalFormatInput(this)">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-6">
                        <div class="form-group c_form_group">
                            <label for="thirty_one_to_sixty_w">30 - 60 DIAS</label>
                            <input type="text" class="form-control" id="thirty_one_to_sixty_w" name="thirty_one_to_sixty_w" onblur="WalletClientModalResetInput(this)" oninput="WalletClientModalFormatInput(this)">
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="form-group c_form_group">
                            <label for="sixty_one_to_ninety_w">61 - 90 DIAS</label>
                            <input type="text" class="form-control" id="sixty_one_to_ninety_w" name="sixty_one_to_ninety_w" onblur="WalletClientModalResetInput(this)" oninput="WalletClientModalFormatInput(this)">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-6">
                        <div class="form-group c_form_group">
                            <label for="ninety_one_to_one_hundred_twenty_w">91 - 120 DIAS</label>
                            <input type="text" class="form-control" id="ninety_one_to_one_hundred_twenty_w" name="ninety_one_to_one_hundred_twenty_w" onblur="WalletClientModalResetInput(this)" oninput="WalletClientModalFormatInput(this)">
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="form-group c_form_group">
                            <label for="one_hundred_twenty_one_to_one_hundred_fifty_w">121 - 150 DIAS</label>
                            <input type="text" class="form-control" id="one_hundred_twenty_one_to_one_hundred_fifty_w" name="one_hundred_twenty_one_to_one_hundred_fifty_w" onblur="WalletClientModalResetInput(this)" oninput="WalletClientModalFormatInput(this)">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-6">
                        <div class="form-group c_form_group">
                            <label for="one_hundred_fifty_one_to_one_hundred_eighty_one_w">151 - 180 DIAS</label>
                            <input type="text" class="form-control" id="one_hundred_fifty_one_to_one_hundred_eighty_one_w" name="one_hundred_fifty_one_to_one_hundred_eighty_one_w" onblur="WalletClientModalResetInput(this)" oninput="WalletClientModalFormatInput(this)">
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="form-group c_form_group">
                            <label for="eldest_to_one_hundred_eighty_one_w">MAYOR A 180 DIAS</label>
                            <input type="text" class="form-control" id="eldest_to_one_hundred_eighty_one_w" name="eldest_to_one_hundred_eighty_one_w" onblur="WalletClientModalResetInput(this)" oninput="WalletClientModalFormatInput(this)">
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal" title="Cerrar ventana.">
                    <i class="fas fa-xmark"></i>
                </button>
                <button type="button" class="btn btn-primary" id="WalletClientButton" onclick="" title="Actualizar cartera cliente.">
                    <i class="fas fa-floppy-disk"></i>
                </button>
            </div>
        </div>
    </div>
</div>
