<div class="modal fade" id="CreateOrderDetailModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" data-backdrop="static" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable" role="document">
        <div class="modal-content">
            <div class="modal-header w-100">
                <div class="text-center w-100" style="background: white;">
                    <label style="font-size:20px;font-weight:bold;">CREAR DETALLE DEL PEDIDO</label>
                </div>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group c_form_group">    
                    <label for="product_id_c">REFERENCIA</label>
                    <select class="form-control select2" id="product_id_c" name="product_id_c" onchange="CreateOrderDetailProductGetColor(this)">
                        <option value="">Seleccione</option>
                    </select>
                </div>
                <div class="form-group c_form_group">
                    <label for="color_id_c">COLOR</label>
                    <select class="form-control select2" id="color_id_c" name="color_id_c" onchange="CreateOrderDetailColorGetQuantity(this)">
                        <option value="">Seleccione</option>
                    </select>
                </div>
                <div class="form-group c_form_group">
                    <label for="price_c">PRECIO</label>
                    <input type="number" class="form-control" id="price_c" name="price_c" data-price="" readonly>
                </div>
                <div class="form-group c_form_group" id="div_t04_c">
                    <label for="t04_c">TALLA 04 ( EXISTENCIA : <span id="et04_c">0</span> ) | ( DISPONIBLE : <span id="dt04_c">0</span> )</label>
                    <input type="number" class="form-control" id="t04_c" name="t04_c">
                </div>
                <div class="form-group c_form_group" id="div_t06_c">
                    <label for="t06_c">TALLA 06 ( EXISTENCIA : <span id="et06_c">0</span> ) | ( DISPONIBLE : <span id="dt06_c">0</span> )</label>
                    <input type="number" class="form-control" id="t06_c" name="t06_c">
                </div>
                <div class="form-group c_form_group" id="div_t08_c">
                    <label for="t08_c">TALLA 08 ( EXISTENCIA : <span id="et08_c">0</span> ) | ( DISPONIBLE : <span id="dt08_c">0</span> )</label>
                    <input type="number" class="form-control" id="t08_c" name="t08_c">
                </div>
                <div class="form-group c_form_group" id="div_t10_c">
                    <label for="t10_c">TALLA 10 ( EXISTENCIA : <span id="et10_c">0</span> ) | ( DISPONIBLE : <span id="dt10_c">0</span> )</label>
                    <input type="number" class="form-control" id="t10_c" name="t10_c">
                </div>
                <div class="form-group c_form_group" id="div_t12_c">
                    <label for="t12_c">TALLA 12 ( EXISTENCIA : <span id="et12_c">0</span> ) | ( DISPONIBLE : <span id="dt12_c">0</span> )</label>
                    <input type="number" class="form-control" id="t12_c" name="t12_c">
                </div>
                <div class="form-group c_form_group" id="div_t14_c">
                    <label for="t14_c">TALLA 14 ( EXISTENCIA : <span id="et14_c">0</span> ) | ( DISPONIBLE : <span id="dt14_c">0</span> )</label>
                    <input type="number" class="form-control" id="t14_c" name="t14_c">
                </div>
                <div class="form-group c_form_group" id="div_t16_c">
                    <label for="t16_c">TALLA 16 ( EXISTENCIA : <span id="et16_c">0</span> ) | ( DISPONIBLE : <span id="dt16_c">0</span> )</label>
                    <input type="number" class="form-control" id="t16_c" name="t16_c">
                </div>
                <div class="form-group c_form_group" id="div_t18_c">
                    <label for="t18_c">TALLA 18 ( EXISTENCIA : <span id="et18_c">0</span> ) | ( DISPONIBLE : <span id="dt18_c">0</span> )</label>
                    <input type="number" class="form-control" id="t18_c" name="t18_c">
                </div>
                <div class="form-group c_form_group" id="div_t20_c">
                    <label for="t20_c">TALLA 20 ( EXISTENCIA : <span id="et20_c">0</span> ) | ( DISPONIBLE : <span id="dt20_c">0</span> )</label>
                    <input type="number" class="form-control" id="t20_c" name="t20_c">
                </div>
                <div class="form-group c_form_group" id="div_t22_c">
                    <label for="t22_c">TALLA 22 ( EXISTENCIA : <span id="et22_c">0</span> ) | ( DISPONIBLE : <span id="dt22_c">0</span> )</label>
                    <input type="number" class="form-control" id="t22_c" name="t22_c">
                </div>
                <div class="form-group c_form_group" id="div_t24_c">
                    <label for="t24_c">TALLA 24 ( EXISTENCIA : <span id="et24_c">0</span> ) | ( DISPONIBLE : <span id="dt24_c">0</span> )</label>
                    <input type="number" class="form-control" id="t24_c" name="t24_c">
                </div>
                <div class="form-group c_form_group" id="div_t26_c">
                    <label for="t26_c">TALLA 26 ( EXISTENCIA : <span id="et26_c">0</span> ) | ( DISPONIBLE : <span id="dt26_c">0</span> )</label>
                    <input type="number" class="form-control" id="t26_c" name="t26_c">
                </div>
                <div class="form-group c_form_group" id="div_t28_c">
                    <label for="t28_c">TALLA 28 ( EXISTENCIA : <span id="et28_c">0</span> ) | ( DISPONIBLE : <span id="dt28_c">0</span> )</label>
                    <input type="number" class="form-control" id="t28_c" name="t28_c">
                </div>
                <div class="form-group c_form_group" id="div_t30_c">
                    <label for="t30_c">TALLA 30 ( EXISTENCIA : <span id="et30_c">0</span> ) | ( DISPONIBLE : <span id="dt30_c">0</span> )</label>
                    <input type="number" class="form-control" id="t30_c" name="t30_c">
                </div>
                <div class="form-group c_form_group" id="div_t32_c">
                    <label for="t32_c">TALLA 32 ( EXISTENCIA : <span id="et32_c">0</span> ) | ( DISPONIBLE : <span id="dt32_c">0</span> )</label>
                    <input type="number" class="form-control" id="t32_c" name="t32_c">
                </div>
                <div class="form-group c_form_group" id="div_t34_c">
                    <label for="t34_c">TALLA 34 ( EXISTENCIA : <span id="et34_c">0</span> ) | ( DISPONIBLE : <span id="dt34_c">0</span> )</label>
                    <input type="number" class="form-control" id="t34_c" name="t34_c">
                </div>
                <div class="form-group c_form_group" id="div_t36_c">
                    <label for="t36_c">TALLA 36 ( EXISTENCIA : <span id="et36_c">0</span> ) | ( DISPONIBLE : <span id="dt36_c">0</span> )</label>
                    <input type="number" class="form-control" id="t36_c" name="t36_c">
                </div>
                <div class="form-group c_form_group" id="div_t38_c">
                    <label for="t38_c">TALLA 38 ( EXISTENCIA : <span id="et38_c">0</span> ) | ( DISPONIBLE : <span id="dt38_c">0</span> )</label>
                    <input type="number" class="form-control" id="t38_c" name="t38_c">
                </div>
                <div class="form-group c_form_group" id="div_tXXS_c">
                    <label for="tXXS_c">TALLA XXS ( EXISTENCIA : <span id="etXXS_c">0</span> ) | ( DISPONIBLE : <span id="dtXXS_c">0</span> )</label>
                    <input type="number" class="form-control" id="tXXS_c" name="tXXS_c">
                </div>
                <div class="form-group c_form_group" id="div_tXS_c">
                    <label for="tXS_c">TALLA XS ( EXISTENCIA : <span id="etXS_c">0</span> ) | ( DISPONIBLE : <span id="dtXS_c">0</span> )</label>
                    <input type="number" class="form-control" id="tXS_c" name="tXS_c">
                </div>
                <div class="form-group c_form_group" id="div_tS_c">
                    <label for="tS_c">TALLA S ( EXISTENCIA : <span id="etS_c">0</span> ) | ( DISPONIBLE : <span id="dtS_c">0</span> )</label>
                    <input type="number" class="form-control" id="tS_c" name="tS_c">
                </div>
                <div class="form-group c_form_group" id="div_tM_c">
                    <label for="tM_c">TALLA M ( EXISTENCIA : <span id="etM_c">0</span> ) | ( DISPONIBLE : <span id="dtM_c">0</span> )</label>
                    <input type="number" class="form-control" id="tM_c" name="tM_c">
                </div>
                <div class="form-group c_form_group" id="div_tL_c">
                    <label for="tL_c">TALLA L ( EXISTENCIA : <span id="etL_c">0</span> ) | ( DISPONIBLE : <span id="dtL_c">0</span> )</label>
                    <input type="number" class="form-control" id="tL_c" name="tL_c">
                </div>
                <div class="form-group c_form_group" id="div_tXL_c">
                    <label for="tXL_c">TALLA XL ( EXISTENCIA : <span id="etXL_c">0</span> ) | ( DISPONIBLE : <span id="dtXL_c">0</span> )</label>
                    <input type="number" class="form-control" id="tXL_c" name="tXL_c">
                </div>
                <div class="form-group c_form_group" id="div_tXXL_c">
                    <label for="tXXL_c">TALLA XXL ( EXISTENCIA : <span id="etXXL_c">0</span> ) | ( DISPONIBLE : <span id="dtXXL_c">0</span> )</label>
                    <input type="number" class="form-control" id="tXXL_c" name="tXXL_c">
                </div>
                <div class="form-group c_form_group">
                    <label for="seller_observation_c">OBSERVACION</label>
                    <textarea class="form-control" id="seller_observation_c" name="seller_observation_c" cols="30" rows="3"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal" title="Cerrar ventana.">
                    <i class="fas fa-xmark"></i>
                </button>
                <button type="button" class="btn btn-primary" id="CreateOrderDetailButton" onclick="CreateOrderDetail()" title="Guardar detalle del pedido.">
                    <i class="fas fa-floppy-disk"></i>
                </button>
            </div>
        </div>
    </div>
</div>
