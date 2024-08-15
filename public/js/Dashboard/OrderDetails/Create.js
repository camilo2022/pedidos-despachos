function CreateOrderDetailModal() {
    $.ajax({
        url: `/Dashboard/Orders/Details/Create`,
        type: 'POST',
        data: {
            '_token': $('meta[name="csrf-token"]').attr('content')
        },
        success: function (response) {
            $('#IndexOrderDetail').trigger('click');
            CreateOrderDetailModalCleaned();
            CreateOrderDetailModalProduct(response.data.products);
            CreateOrderDetailAjaxSuccess(response);
            $('#CreateOrderDetailModal').modal('show');
        },
        error: function (xhr, textStatus, errorThrown) {
            $('#IndexOrderDetail').trigger('click');
            CreateOrderDetailAjaxError(xhr);
        }
    });
}

function CreateOrderDetailModalCleaned() {
    CreateOrderDetailModalResetSelect('product_id_c');
    RemoveIsValidClassCreateOrderDetail();
    RemoveIsInvalidClassCreateOrderDetail();
    $('#price_c').val('');
    $('#seller_observation_c').val('');
    
    let sizes = ['04', '06', '08', '10', '12', '14', '16', '18', '20', '22', '24', '26', '28', '30', '32', '34', '36', '38', 'XXS', 'XS', 'S', 'M', 'L', 'XL', 'XXL'];
    $.each(sizes, function(index, size) {
        $(`#div_t${size}_c`).show();
        $(`#et${size}_c`).text(0);
        $(`#dt${size}_c`).text(0);
        $(`#t${size}_c`).val(0);
    });
}

function CreateOrderDetailModalResetSelect(id) {
    $(`#${id}`).html('');
    $(`#${id}`).append(new Option('Seleccione', '', false, false));
    $(`#${id}`).trigger('change');
}

function CreateOrderDetailModalProduct(products) {
    $.each(products, function(index, product) {
        $('#product_id_c').append(new Option(product.code, product.id, false, false));
    });
}

function CreateOrderDetailProductGetColor(select) {
    if($(select).val() == '') {
        CreateOrderDetailModalResetSelect('color_id_c');
    } else {
        $.ajax({
            url: `/Dashboard/Orders/Details/Create`,
            type: 'POST',
            data: {
                '_token': $('meta[name="csrf-token"]').attr('content'),
                'product_id':  $(select).val()
            },
            success: function(response) {
                CreateOrderDetailModalResetSelect('color_id_c');
                CreateOrderDetailModalColor(response.data.colors);
                $('#price_c').attr('data-price', response.data.product.price);
                $('#price_c').val(response.data.product.price);
            },
            error: function(xhr, textStatus, errorThrown) {
                CreateOrderDetailAjaxError(xhr);
            }
        });
    }
}

function CreateOrderDetailModalColor(colors) {
    colors.forEach(color => {
        $('#color_id_c').append(new Option(`${color.name} - ${color.code}`, color.id, false, false));
    });
}

function CreateOrderDetailColorGetQuantity(select) {
    if($(select).val() == '') {
        let sizes = ['04', '06', '08', '10', '12', '14', '16', '18', '20', '22', '24', '26', '28', '30', '32', '34', '36', '38', 'XXS', 'XS', 'S', 'M', 'L', 'XL', 'XXL'];
        $.each(sizes, function(index, size) {
            $(`#div_t${size}_c`).show();
            $(`#et${size}_c`).text(0);
            $(`#dt${size}_c`).text(0);
            $(`#t${size}_c`).val(0);
        });
    } else {
        $.ajax({
            url: `/Dashboard/Orders/Details/Create`,
            type: 'POST',
            data: {
                '_token': $('meta[name="csrf-token"]').attr('content'),
                'product_id':  $('#product_id_c').val(),
                'color_id':  $(select).val()
            },
            success: function(response) {
                CreateOrderDetailModalSizes(response.data);
            },
            error: function(xhr, textStatus, errorThrown) {
                CreateOrderDetailAjaxError(xhr);
            }
        });
    }
}

function CreateOrderDetailModalSizes(data) {
    $.each(data.sizes, function(index, size) {
        if(data.inventory[`T${size.code}`] > 0 || data.committed[`T${size.code}`] > 0) {
            $(`#div_t${size.code}_c`).show();
            $(`#et${size.code}_c`).text(data.inventory[`T${size.code}`]);
            $(`#dt${size.code}_c`).text(data.inventory[`T${size.code}`] - data.committed[`T${size.code}`]);
            $(`#t${size.code}_c`).val(0);
        } else {
            $(`#div_t${size.code}_c`).hide();
            $(`#et${size.code}_c`).text(0);
            $(`#dt${size.code}_c`).text(0);
            $(`#t${size.code}_c`).val(0);
        }
    });
}

function CreateOrderDetail() {
    Swal.fire({
        title: '¿Desea guardar el detalle del pedido?',
        text: 'El detalle del pedido será creado.',
        icon: 'warning',
        showCancelButton: true,
        cancelButtonColor: '#DD6B55',
        confirmButtonColor: '#3085d6',
        confirmButtonText: 'Si, guardar!',
        cancelButtonText: 'No, cancelar!',
    }).then((result) => {
        if (result.value) {
            $.ajax({
                url: `/Dashboard/Orders/Details/Store`,
                type: 'POST',
                data: {
                    '_token': $('meta[name="csrf-token"]').attr('content'),
                    'order_id': $('#IndexOrderDetail').attr('data-id'),
                    'product_id': $('#product_id_c').val(),
                    'color_id': $('#color_id_c').val(),
                    'price': $('#price_c').attr('data-price'),
                    'negotiated_price': $('#price_c').val(),
                    'T04': $('#t04_c').val(),
                    'T06': $('#t06_c').val(),
                    'T08': $('#t08_c').val(),
                    'T10': $('#t10_c').val(),
                    'T12': $('#t12_c').val(),
                    'T14': $('#t14_c').val(),
                    'T16': $('#t16_c').val(),
                    'T18': $('#t18_c').val(),
                    'T20': $('#t20_c').val(),
                    'T22': $('#t22_c').val(),
                    'T24': $('#t24_c').val(),
                    'T26': $('#t26_c').val(),
                    'T28': $('#t28_c').val(),
                    'T30': $('#t30_c').val(),
                    'T32': $('#t32_c').val(),
                    'T34': $('#t34_c').val(),
                    'T36': $('#t36_c').val(),
                    'T38': $('#t38_c').val(),
                    'TXXS': $('#tXXS_c').val(),
                    'TXS': $('#tXS_c').val(),
                    'TS': $('#tS_c').val(),
                    'TM': $('#tM_c').val(),
                    'TL': $('#tL_c').val(),
                    'TXL': $('#tXL_c').val(),
                    'TXXL': $('#tXXL_c').val(),
                    'seller_observation': $('#seller_observation_c').val()
                },
                success: function (response) {
                    $('#IndexOrderDetail').trigger('click');
                    CreateOrderDetailAjaxSuccess(response);
                },
                error: function (xhr, textStatus, errorThrown) {
                    $('#IndexOrderDetail').trigger('click');
                    CreateOrderDetailAjaxError(xhr);
                }
            });
        } else {
            toastr.info('El detalle del pedido no fue creado.')
        }
    });
}

function CreateOrderDetailAjaxSuccess(response) {
    if (response.status === 204) {
        toastr.info(response.message);
        $('#CreateOrderDetailModal').modal('hide');
    }

    if (response.status === 201) {
        toastr.success(response.message);
        $('#CreateOrderDetailModal').modal('hide');
    }
}

function CreateOrderDetailAjaxError(xhr) {
    if (xhr.status === 403) {
        toastr.error(xhr.responseJSON.error ? xhr.responseJSON.error.message : xhr.responseJSON.message);
        $('#CreateOrderDetailModal').modal('hide');
    }

    if (xhr.status === 404) {
        toastr.error(xhr.responseJSON.error ? xhr.responseJSON.error.message : xhr.responseJSON.message);
        $('#CreateOrderDetailModal').modal('hide');
    }

    if (xhr.status === 419) {
        toastr.error(xhr.responseJSON.error ? xhr.responseJSON.error.message : xhr.responseJSON.message);
        $('#CreateOrderDetailModal').modal('hide');
    }

    if (xhr.status === 422) {
        RemoveIsValidClassCreateOrderDetail();
        RemoveIsInvalidClassCreateOrderDetail();
        $.each(xhr.responseJSON.errors, function (field, messages) {
            AddIsInvalidClassCreateOrderDetail(field.toLowerCase());
            $.each(messages, function (index, message) {
                toastr.error(message);
            });
        });
        AddIsValidClassCreateOrderDetail();
    }

    if (xhr.status === 500) {
        toastr.error(xhr.responseJSON.error ? xhr.responseJSON.error.message : xhr.responseJSON.message);
        $('#CreateOrderDetailModal').modal('hide');
    }
}

function AddIsValidClassCreateOrderDetail() {
    if (!$('#price_c').hasClass('is-invalid')) {
        $('#price_c').addClass('is-valid');
    }
    if (!$('#seller_observation_c').hasClass('is-invalid')) {
        $('#seller_observation_c').addClass('is-valid');
    }
    if (!$('span[aria-labelledby="select2-product_id_c-container"]').hasClass('is-invalid')) {
        $('span[aria-labelledby="select2-product_id_c-container"]').addClass('is-valid');
    }
    if (!$('span[aria-labelledby="select2-color_id_c-container"]').hasClass('is-invalid')) {
        $('span[aria-labelledby="select2-color_id_c-container"]').addClass('is-valid');
    }
    let sizes = ['04', '06', '08', '10', '12', '14', '16', '18', '20', '22', '24', '26', '28', '30', '32', '34', '36', '38', 'XXS', 'XS', 'S', 'M', 'L', 'XL', 'XXL'];
    $.each(sizes, function(index, size) {
        if (!$(`#t${size}_c`).hasClass('is-invalid')) {
            $(`#t${size}_c`).addClass('is-valid');
        }
    });
}

function RemoveIsValidClassCreateOrderDetail() {
    $('#price_c').removeClass('is-valid');
    $('#seller_observation_c').removeClass('is-valid');
    $(`span[aria-labelledby="select2-product_id_c-container"]`).removeClass('is-valid');
    $(`span[aria-labelledby="select2-color_id_c-container"]`).removeClass('is-valid');
    let sizes = ['04', '06', '08', '10', '12', '14', '16', '18', '20', '22', '24', '26', '28', '30', '32', '34', '36', '38', 'XXS', 'XS', 'S', 'M', 'L', 'XL', 'XXL'];
    $.each(sizes, function(index, size) {
        $(`#t${size}_c`).removeClass('is-valid');
    });
}

function AddIsInvalidClassCreateOrderDetail(input) {
    if (!$(`#${input}_c`).hasClass('is-valid')) {
        $(`#${input}_c`).addClass('is-invalid');
    }
    if (!$(`span[aria-labelledby="select2-${input}_c-container`).hasClass('is-valid')) {
        $(`span[aria-labelledby="select2-${input}_c-container"]`).addClass('is-invalid');
    }
}

function RemoveIsInvalidClassCreateOrderDetail() {
    $('#price_c').removeClass('is-invalid');
    $('#seller_observation_c').removeClass('is-invalid');
    $(`span[aria-labelledby="select2-product_id_c-container"]`).removeClass('is-invalid');
    $(`span[aria-labelledby="select2-color_id_c-container"]`).removeClass('is-invalid');
    let sizes = ['04', '06', '08', '10', '12', '14', '16', '18', '20', '22', '24', '26', '28', '30', '32', '34', '36', '38', 'XXS', 'XS', 'S', 'M', 'L', 'XL', 'XXL'];
    $.each(sizes, function(index, size) {
        $(`#t${size}_c`).removeClass('is-invalid');
    });
}
