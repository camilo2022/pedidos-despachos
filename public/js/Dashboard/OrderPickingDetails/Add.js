let sizes = [];

function AddOrderPickingDetail(id) {
    $.ajax({
        url: `/Dashboard/Pickings/Index/Query`,
        type: 'POST',
        data: {
            '_token': $('meta[name="csrf-token"]').attr('content'),
            'id': id
        },
        success: function(response) {
            sizes = response.data.sizes;
            AddOrderPickingDetailAjaxSuccess(response);
        },
        error: function(xhr, textStatus, errorThrown) {
            AddOrderPickingDetailAjaxError(xhr);
        }
    });
}

function AddOrderPickingDetailModal(id, reference, size, colorCode, colorName, identifySize = '', identifyTotal = '') {
    $('#reference_a').val(reference);
    $('#size_a').val(size);
    $('#color_a').val(`${colorCode} - ${colorName}`);

    let quantity = $(`#OA-${reference}-${colorCode}-T${size}-${identifySize}`).text();

    $('#quantity_a').text(quantity);

    $('#AddUpOrderPickingDetailButton').attr('onclick', `AddOrderPickingDetail(this, event, false, ${id}, '${reference}', '${size}', '${colorCode}', 1, '${identifySize}', '${identifyTotal}')`);
    $('#AddDownOrderPickingDetailButton').attr('onclick', `AddOrderPickingDetail(this, event, false, ${id}, '${reference}', '${size}', '${colorCode}', -1, '${identifySize}', '${identifyTotal}')`);

    $('#AddOrderPickingDetailModal').modal('show');
}

function AddOrderPickingDetail(input, event, boolean = true, idPicking = 0, referencePicking = '', sizePicking = '', colorPicking = '', quantityPicking = 1, identifySize = '', identifyTotal = '') {
    if(event.which == 13 && boolean){

        let value = $.trim($(input).val()).toUpperCase();

        if(value == '' || value == null || value == undefined) {
            toastr.warning('Debe ingresar un codigo de referencia. El formato del codigo debe ser REFERENCIA - TALLA - COLOR.');
            $(input).val('');
            return false;
        }

        let array = value.split('-');

        let referenciaArray = '';
        let tallaArray = '';
        let colorArray = '';

        if(array.length < 3) {
            toastr.warning('El formato del codigo ingresado es invalido. El formato del codigo debe ser REFERENCIA - TALLA - COLOR.');
            $(input).val('');
            return false;
        }

        switch (array.length) {
            case 3:
                referenciaArray = `${array[0]}`;
                break;
            case 4:
                referenciaArray = `${array[0]}-${array[1]}`;
                break;
            case 5:
                referenciaArray = `${array[0]}-${array[1]}-${array[2]}`;
                break;
            case 6:
                referenciaArray = `${array[0]}-${array[1]}-${array[2]}-${array[3]}`;
                break;
            default:
                referenciaArray = `${array[0]}`;
                break;
        }

        tallaArray = `${array[array.length - 2]}`;

        colorArray = `${array[array.length - 1]}`;

        if($(input).data('product') != referenciaArray) {
            toastr.error(`La referencia esperada en este campo es la ${$(input).data('product')} y esta ingresando la ${referenciaArray}.`);
            $(input).val('');
            return false;
        }

        if($(input).data('color') != colorArray) {
            toastr.error(`El codigo de color esperado en este campo es el ${$(input).data('color')} y esta ingresando el ${colorArray}.`);
            $(input).val('');
            return false;
        }

        let picking = parseInt($(`#${$(input).data('id')}-${referenciaArray}-${colorArray}-${tallaArray}-CA`).text());
        let quantity = parseInt($(`#${$(input).data('id')}-${referenciaArray}-${colorArray}-${tallaArray}-CT`).text());

        if(picking == quantity) {
            toastr.error(`Las unidades de la talla ${tallaArray} ya fueron alistadas en su totalidad.`);
            $(input).val('');
            return false;
        }

        $(`#${$(input).data('id')}-${referenciaArray}-${colorArray}-${tallaArray}-CA`).text(++picking);

        let quantityMissing = parseInt($(`#${$(input).data('id')}-${$(input).data('product')}-${$(input).data('color')}-quantity-missing`).text());
        let quantityTotal = parseInt($(`#${$(input).data('id')}-${$(input).data('product')}-${$(input).data('color')}-quantity-total`).text());

        $(`#${$(input).data('id')}-${$(input).data('product')}-${$(input).data('color')}-quantity-missing`).text(++quantityMissing);

        let badge = $(`#${$(input).data('id')}-${$(input).data('product')}-${$(input).data('color')}-badge`);

        quantityMissing == quantityTotal ? badge.removeClass('badge-danger').addClass('badge-success').text('Completado') : badge.removeClass('badge-success').addClass('badge-danger').text('Hace falta') ;

        $.ajax({
            url: `/Dashboard/Pickings/Details/Add`,
            type: 'PUT',
            data: {
                '_token': $('meta[name="csrf-token"]').attr('content'),
                'id': $(input).data('id'),
                'size': tallaArray,
                'quantity': quantityPicking
            },
            success: function(response) {
                AddOrderPickingDetailAjaxSuccess(response);
            },
            error: function(xhr, textStatus, errorThrown) {
                AddOrderPickingDetailAjaxError(xhr);
                $('#IndexOrderPicking').trigger('click');
            }
        });

        $(input).val('');
    } else if(!boolean) {
        let quantity = parseInt($(`#OA-${referencePicking}-${colorPicking}-T${sizePicking}-${identifySize}`).text());
        let quantityTotal = parseInt($(`#OA-${referencePicking}-${colorPicking}-TOTAL-${identifyTotal}`).text());
        let quantitySize = parseInt($(`#OA-T${sizePicking}-TOTAL`).text());
        let quantities = parseInt($('#OA-TOTAL').text());

        let quantityDispatchSize = $(`#OD-${referencePicking}-${colorPicking}-T${sizePicking}-${identifySize}`);
        let quantityPickingSize = $(`#OA-${referencePicking}-${colorPicking}-T${sizePicking}-${identifySize}`);
        
        let quantityDispatchTotal = $(`#OD-${referencePicking}-${colorPicking}-TOTAL-${identifyTotal}`);
        let quantityPickingTotal = $(`#OA-${referencePicking}-${colorPicking}-TOTAL-${identifyTotal}`);

        quantity += quantityPicking;
        quantityTotal += quantityPicking;
        quantitySize += quantityPicking;
        quantities += quantityPicking;

        if(quantity < 0) {
            toastr.error(`No se pueden dismuir mas unidades en la referencia ${referencePicking} en el color ${colorPicking} en la talla ${sizePicking} porque el minimo de unidades es 0.`);
            return false;
        }

        $(`#OA-${referencePicking}-${colorPicking}-T${sizePicking}-${identifySize}`).text(quantity);
        $(`#OA-${referencePicking}-${colorPicking}-TOTAL-${identifyTotal}`).text(quantityTotal);
        $(`#OA-T${sizePicking}-TOTAL`).text(quantitySize);
        $('#OA-TOTAL').text(quantities);
        $('#quantity_a').text(quantity);

        if(parseInt($(quantityPickingSize).text()) == parseInt($(quantityDispatchSize).text())) {
            $(quantityPickingSize).attr('class', 'bg-success');
        } else if(parseInt($(quantityPickingSize).text()) > parseInt($(quantityDispatchSize).text())) {
            $(quantityPickingSize).attr('class', 'bg-primary');
        } else if(parseInt($(quantityPickingSize).text()) < parseInt($(quantityDispatchSize).text())) {
            $(quantityPickingSize).attr('class', 'bg-danger');
        }

        if(parseInt($(quantityPickingTotal).text()) == parseInt($(quantityDispatchTotal).text())) {
            $(quantityPickingTotal).attr('class', 'bg-success');
        } else if(parseInt($(quantityPickingTotal).text()) > parseInt($(quantityDispatchTotal).text())) {
            $(quantityPickingTotal).attr('class', 'bg-primary');
        } else if(parseInt($(quantityPickingTotal).text()) < parseInt($(quantityDispatchTotal).text())) {
            $(quantityPickingTotal).attr('class', 'bg-danger');
        }

        $.ajax({
            url: `/Dashboard/Pickings/Details/Add`,
            type: 'PUT',
            data: {
                '_token': $('meta[name="csrf-token"]').attr('content'),
                'id': idPicking,
                'size': sizePicking,
                'quantity': quantityPicking
            },
            success: function(response) {
                AddOrderPickingDetailAjaxSuccess(response);
            },
            error: function(xhr, textStatus, errorThrown) {
                AddOrderPickingDetailAjaxError(xhr);

                quantity -= quantityPicking;
                quantityTotal -= quantityPicking;
                quantitySize -= quantityPicking;
                quantities -= quantityPicking;

                $(`#OA-${referencePicking}-${colorPicking}-T${sizePicking}`).text(quantity);
                $(`#OA-${referencePicking}-${colorPicking}-TOTAL`).text(quantityTotal);
                $(`#OA-T${sizePicking}-TOTAL`).text(quantitySize);
                $('#OA-TOTAL').text(quantities);
                $('#quantity_a').text(quantity);

                if(parseInt($(quantityPickingSize).text()) == parseInt($(quantityDispatchSize).text())) {
                    $(quantityPickingSize).attr('class', 'bg-success');
                } else if(parseInt($(quantityPickingSize).text()) > parseInt($(quantityDispatchSize).text())) {
                    $(quantityPickingSize).attr('class', 'bg-primary');
                } else if(parseInt($(quantityPickingSize).text()) < parseInt($(quantityDispatchSize).text())) {
                    $(quantityPickingSize).attr('class', 'bg-danger');
                }

                if(parseInt($(quantityPickingTotal).text()) == parseInt($(quantityDispatchTotal).text())) {
                    $(quantityPickingTotal).attr('class', 'bg-success');
                } else if(parseInt($(quantityPickingTotal).text()) > parseInt($(quantityDispatchTotal).text())) {
                    $(quantityPickingTotal).attr('class', 'bg-primary');
                } else if(parseInt($(quantityPickingTotal).text()) < parseInt($(quantityDispatchTotal).text())) {
                    $(quantityPickingTotal).attr('class', 'bg-danger');
                }
            }
        });

        $(input).val('');
    }
}

function AddOrderPickingDetailAjaxSuccess(response) {
    if(response.status === 200) {
        toastr.success(response.message);
    }
}

function AddOrderPickingDetailAjaxError(xhr) {
    if(xhr.status === 403) {
        toastr.error(xhr.responseJSON.error ? xhr.responseJSON.error.message : xhr.responseJSON.message);
    }

    if(xhr.status === 404) {
        toastr.error(xhr.responseJSON.error ? xhr.responseJSON.error.message : xhr.responseJSON.message);
    }

    if(xhr.status === 419) {
        toastr.error(xhr.responseJSON.error ? xhr.responseJSON.error.message : xhr.responseJSON.message);
    }

    if(xhr.status === 422){
        $.each(xhr.responseJSON.errors, function(field, messages) {
            $.each(messages, function(index, message) {
                toastr.error(message);
            });
        });
    }

    if(xhr.status === 500){
        toastr.error(xhr.responseJSON.error ? xhr.responseJSON.error.message : xhr.responseJSON.message);
    }
}
