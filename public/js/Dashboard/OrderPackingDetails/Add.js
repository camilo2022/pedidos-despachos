let sizes = [];
function AddOrderPackingDetail(id) {
    $.ajax({
        url: `/Dashboard/Pickings/Index/Query`,
        type: 'POST',
        data: {
            '_token': $('meta[name="csrf-token"]').attr('content'),
            'id': id
        },
        success: function(response) {
            sizes = response.data.sizes;
            AddOrderPackingDetailAjaxSuccess(response);
        },
        error: function(xhr, textStatus, errorThrown) {
            AddOrderPackingDetailAjaxError(xhr);
        }
    });
}

function AddOrderPackingDetailModal(id, reference, size, colorCode, colorName) {

    console.log(id, reference, size, colorCode, colorName);
    
    $('#reference_a').val(reference);
    $('#size_a').val(size);
    $('#color_a').val(`${colorCode} - ${colorName}`);

    let quantity = parseInt($(`#${id}-${reference}-${colorCode}-${size}-CE`).text());
    let missing = parseInt($(`#${id}-${reference}-${colorCode}-${size}-CT`).text());

    $('#quantity_a').val(quantity);
    $('#missing_a').text(missing - quantity);
    
    $('#AddOrderPackingDetailButton').attr('onclick', `AddOrderPackingDetail(${id}, '${reference}', '${size}', '${colorCode}')`);

    $('#AddOrderPackingDetailModal').modal('show');
}

function AddOrderPackingDetail(id, reference, size, color) {
    let quantityPackingSize = parseInt($(`#${id}-${reference}-${color}-${size}-CE`).text());
    let quantityDispatchSize = parseInt($(`#${id}-${reference}-${color}-${size}-CT`).text());
    
    let quantity = parseInt($(`#quantity_a`).val());

    if(quantity < 0) {
        toastr.error(`No se pueden ingresar unidades negativas.`);
        return false;
    }
    
    if(quantity > quantityDispatchSize) {
        toastr.error(`No se pueden empacar mas unidades en la referencia ${reference} en el color ${color} en la talla ${size} porque el maximo de unidades disponibles es ${quantityDispatchSize}.`);
        return false;
    }

    $(`#${id}-${reference}-${color}-${size}-CE`).text(quantity);
    
    $.ajax({
        url: `/Dashboard/Packings/Details/Add`,
        type: 'PUT',
        data: {
            '_token': $('meta[name="csrf-token"]').attr('content'),
            'id': id,
            'size': size,
            'quantity': quantity
        },
        success: function(response) {
            $('#AddOrderPackingDetailModal').modal('hide');
            AddOrderPackingDetailAjaxSuccess(response);
        },
        error: function(xhr, textStatus, errorThrown) {
            $(`#${id}-${reference}-${color}-${size}-CE`).text(quantityPackingSize);
            AddOrderPackingDetailAjaxError(xhr);
        }
    });
}

function AddOrderPackingDetailAjaxSuccess(response) {
    if(response.status === 200) {
        toastr.success(response.message);
    }
}

function AddOrderPackingDetailAjaxError(xhr) {
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
