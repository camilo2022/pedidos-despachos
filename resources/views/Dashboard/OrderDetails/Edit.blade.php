<div class="modal" id="EditOrderDetailModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" data-backdrop="static" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable" role="document">
        <div class="modal-content">
            <div class="modal-header w-100">
                <div class="text-center w-100" style="background: white;">
                    <label style="font-size:20px;font-weight:bold;">EDITAR DETALLE DEL PEDIDO</label>
                </div>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group c_form_group">    
                    <label for="product_id_e">REFERENCIA</label>
                    <select class="form-control select2" id="product_id_e" name="product_id_e" onchange="EditOrderDetailProductGetColor(this)">
                        <option value="">Seleccione</option>
                    </select>
                </div>
                <div class="form-group c_form_group">
                    <label for="color_id_e">COLOR</label>
                    <select class="form-control select2" id="color_id_e" name="color_id_e" onchange="EditOrderDetailColorGetQuantity(this)">
                        <option value="">Seleccione</option>
                    </select>
                </div>
                <div class="form-group c_form_group">
                    <label for="price_e">PRECIO</label>
                    <input type="number" class="form-control" id="price_e" name="price_e" data-price="" readonly>
                </div>
                <div class="form-group c_form_group" id="div_t04_e">
                    <label for="t04_e">TALLA 04 ( EXISTENCIA : <span id="et04_e">0</span> ) | ( DISPONIBLE : <span id="dt04_e">0</span> )</label>
                    <input type="number" class="form-control" id="t04_e" name="t04_e">
                </div>
                <div class="form-group c_form_group" id="div_t06_e">
                    <label for="t06_e">TALLA 06 ( EXISTENCIA : <span id="et06_e">0</span> ) | ( DISPONIBLE : <span id="dt06_e">0</span> )</label>
                    <input type="number" class="form-control" id="t06_e" name="t06_e">
                </div>
                <div class="form-group c_form_group" id="div_t08_e">
                    <label for="t08_e">TALLA 08 ( EXISTENCIA : <span id="et08_e">0</span> ) | ( DISPONIBLE : <span id="dt08_e">0</span> )</label>
                    <input type="number" class="form-control" id="t08_e" name="t08_e">
                </div>
                <div class="form-group c_form_group" id="div_t10_e">
                    <label for="t10_e">TALLA 10 ( EXISTENCIA : <span id="et10_e">0</span> ) | ( DISPONIBLE : <span id="dt10_e">0</span> )</label>
                    <input type="number" class="form-control" id="t10_e" name="t10_e">
                </div>
                <div class="form-group c_form_group" id="div_t12_e">
                    <label for="t12_e">TALLA 12 ( EXISTENCIA : <span id="et12_e">0</span> ) | ( DISPONIBLE : <span id="dt12_e">0</span> )</label>
                    <input type="number" class="form-control" id="t12_e" name="t12_e">
                </div>
                <div class="form-group c_form_group" id="div_t14_e">
                    <label for="t14_e">TALLA 14 ( EXISTENCIA : <span id="et14_e">0</span> ) | ( DISPONIBLE : <span id="dt14_e">0</span> )</label>
                    <input type="number" class="form-control" id="t14_e" name="t14_e">
                </div>
                <div class="form-group c_form_group" id="div_t16_e">
                    <label for="t16_e">TALLA 16 ( EXISTENCIA : <span id="et16_e">0</span> ) | ( DISPONIBLE : <span id="dt16_e">0</span> )</label>
                    <input type="number" class="form-control" id="t16_e" name="t16_e">
                </div>
                <div class="form-group c_form_group" id="div_t18_e">
                    <label for="t18_e">TALLA 18 ( EXISTENCIA : <span id="et18_e">0</span> ) | ( DISPONIBLE : <span id="dt18_e">0</span> )</label>
                    <input type="number" class="form-control" id="t18_e" name="t18_e">
                </div>
                <div class="form-group c_form_group" id="div_t20_e">
                    <label for="t20_e">TALLA 20 ( EXISTENCIA : <span id="et20_e">0</span> ) | ( DISPONIBLE : <span id="dt20_e">0</span> )</label>
                    <input type="number" class="form-control" id="t20_e" name="t20_e">
                </div>
                <div class="form-group c_form_group" id="div_t22_e">
                    <label for="t22_e">TALLA 22 ( EXISTENCIA : <span id="et22_e">0</span> ) | ( DISPONIBLE : <span id="dt22_e">0</span> )</label>
                    <input type="number" class="form-control" id="t22_e" name="t22_e">
                </div>
                <div class="form-group c_form_group" id="div_t24_e">
                    <label for="t24_e">TALLA 24 ( EXISTENCIA : <span id="et24_e">0</span> ) | ( DISPONIBLE : <span id="dt24_e">0</span> )</label>
                    <input type="number" class="form-control" id="t24_e" name="t24_e">
                </div>
                <div class="form-group c_form_group" id="div_t26_e">
                    <label for="t26_e">TALLA 26 ( EXISTENCIA : <span id="et26_e">0</span> ) | ( DISPONIBLE : <span id="dt26_e">0</span> )</label>
                    <input type="number" class="form-control" id="t26_e" name="t26_e">
                </div>
                <div class="form-group c_form_group" id="div_t28_e">
                    <label for="t28_e">TALLA 28 ( EXISTENCIA : <span id="et28_e">0</span> ) | ( DISPONIBLE : <span id="dt28_e">0</span> )</label>
                    <input type="number" class="form-control" id="t28_e" name="t28_e">
                </div>
                <div class="form-group c_form_group" id="div_t30_e">
                    <label for="t30_e">TALLA 30 ( EXISTENCIA : <span id="et30_e">0</span> ) | ( DISPONIBLE : <span id="dt30_e">0</span> )</label>
                    <input type="number" class="form-control" id="t30_e" name="t30_e">
                </div>
                <div class="form-group c_form_group" id="div_t32_e">
                    <label for="t32_e">TALLA 32 ( EXISTENCIA : <span id="et32_e">0</span> ) | ( DISPONIBLE : <span id="dt32_e">0</span> )</label>
                    <input type="number" class="form-control" id="t32_e" name="t32_e">
                </div>
                <div class="form-group c_form_group" id="div_t34_e">
                    <label for="t34_e">TALLA 34 ( EXISTENCIA : <span id="et34_e">0</span> ) | ( DISPONIBLE : <span id="dt34_e">0</span> )</label>
                    <input type="number" class="form-control" id="t34_e" name="t34_e">
                </div>
                <div class="form-group c_form_group" id="div_t36_e">
                    <label for="t36_e">TALLA 36 ( EXISTENCIA : <span id="et36_e">0</span> ) | ( DISPONIBLE : <span id="dt36_e">0</span> )</label>
                    <input type="number" class="form-control" id="t36_e" name="t36_e">
                </div>
                <div class="form-group c_form_group" id="div_t38_e">
                    <label for="t38_e">TALLA 38 ( EXISTENCIA : <span id="et38_e">0</span> ) | ( DISPONIBLE : <span id="dt38_e">0</span> )</label>
                    <input type="number" class="form-control" id="t38_e" name="t38_e">
                </div>
                <div class="form-group c_form_group" id="div_tXXS_e">
                    <label for="tXXS_e">TALLA XXS ( EXISTENCIA : <span id="etXXS_e">0</span> ) | ( DISPONIBLE : <span id="dtXXS_e">0</span> )</label>
                    <input type="number" class="form-control" id="tXXS_e" name="tXXS_e">
                </div>
                <div class="form-group c_form_group" id="div_tXS_e">
                    <label for="tXS_e">TALLA XS ( EXISTENCIA : <span id="etXS_e">0</span> ) | ( DISPONIBLE : <span id="dtXS_e">0</span> )</label>
                    <input type="number" class="form-control" id="tXS_e" name="tXS_e">
                </div>
                <div class="form-group c_form_group" id="div_tS_e">
                    <label for="tS_e">TALLA S ( EXISTENCIA : <span id="etS_e">0</span> ) | ( DISPONIBLE : <span id="dtS_e">0</span> )</label>
                    <input type="number" class="form-control" id="tS_e" name="tS_e">
                </div>
                <div class="form-group c_form_group" id="div_tM_e">
                    <label for="tM_e">TALLA M ( EXISTENCIA : <span id="etM_e">0</span> ) | ( DISPONIBLE : <span id="dtM_e">0</span> )</label>
                    <input type="number" class="form-control" id="tM_e" name="tM_e">
                </div>
                <div class="form-group c_form_group" id="div_tL_e">
                    <label for="tL_e">TALLA L ( EXISTENCIA : <span id="etL_e">0</span> ) | ( DISPONIBLE : <span id="dtL_e">0</span> )</label>
                    <input type="number" class="form-control" id="tL_e" name="tL_e">
                </div>
                <div class="form-group c_form_group" id="div_tXL_e">
                    <label for="tXL_e">TALLA XL ( EXISTENCIA : <span id="etXL_e">0</span> ) | ( DISPONIBLE : <span id="dtXL_e">0</span> )</label>
                    <input type="number" class="form-control" id="tXL_e" name="tXL_e">
                </div>
                <div class="form-group c_form_group" id="div_tXXL_e">
                    <label for="tXXL_e">TALLA XXL ( EXISTENCIA : <span id="etXXL_e">0</span> ) | ( DISPONIBLE : <span id="dtXXL_e">0</span> )</label>
                    <input type="number" class="form-control" id="tXXL_e" name="tXXL_e">
                </div>
                <div class="form-group c_form_group">
                    <label for="seller_observation_e">OBSERVACION</label>
                    <textarea class="form-control" id="seller_observation_e" name="seller_observation_e" cols="30" rows="3"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal" title="Cerrar ventana">
                    <i class="fas fa-xmark"></i>
                </button>
                <button type="button" class="btn btn-primary" id="EditOrderDetailButton" onclick="" title="Actualizar detalle del pedido.">
                    <i class="fas fa-floppy-disk"></i>
                </button>
            </div>
        </div>
    </div>
</div>
