function EditOrderDetailModal(id) {
    $.ajax({
        url: `/Dashboard/Orders/Details/Edit/${id}`,
        type: 'POST',
        data: {
            '_token': $('meta[name="csrf-token"]').attr('content')
        },
        success: function (response) {
            $('#IndexOrderDetail').trigger('click');
            EditOrderDetailModalCleaned(response.data.orderDetail);
            EditOrderDetailModalProduct(response.data.products);
            EditOrderDetailAjaxSuccess(response);
            $('#EditOrderDetailModal').modal('show');
        },
        error: function (xhr, textStatus, errorThrown) {
            $('#IndexOrderDetail').trigger('click');
            EditOrderDetailAjaxError(xhr);
        }
    });
}

function EditOrderDetailModalCleaned(orderDetail) {
    EditOrderDetailModalResetSelect('product_id_e');
    RemoveIsValidClassEditOrderDetail();
    RemoveIsInvalidClassEditOrderDetail();
    $('#price_e').val(orderDetail.negotiated_price);
    $('#seller_observation_e').val(orderDetail.seller_observation);

    $('#EditOrderDetailButton').attr('onclick', `EditOrderDetail(${orderDetail.id})`);
    $('#EditOrderDetailButton').attr('data-id', orderDetail.id);
    $('#EditOrderDetailButton').attr('data-product_id', orderDetail.product_id);
    $('#EditOrderDetailButton').attr('data-product', orderDetail.product_id);
    $('#EditOrderDetailButton').attr('data-color_id', orderDetail.color_id);
    $('#EditOrderDetailButton').attr('data-color', orderDetail.color_id);
    $('#EditOrderDetailButton').attr('data-price', orderDetail.price);
    $('#EditOrderDetailButton').attr('data-negotiated_price', orderDetail.negotiated_price);

    let sizes = ['04', '06', '08', '10', '12', '14', '16', '18', '20', '22', '24', '26', '28', '30', '32', '34', '36', '38', 'XXS', 'XS', 'S', 'M', 'L', 'XL', 'XXL'];

    $.each(sizes, function(index, size) {
        $('#EditOrderDetailButton').attr(`data-t${size}`, orderDetail[`T${size}`]);

        $(`#div_t${size}_e`).show();
        $(`#et${size}_e`).text(0);
        $(`#dt${size}_e`).text(0);
        $(`#t${size}_e`).val(orderDetail[`T${size}`]);
    });
}

function EditOrderDetailModalResetSelect(id) {
    $(`#${id}`).html('')
    $(`#${id}`).append(new Option('Seleccione', '', false, false));
    $(`#${id}`).trigger('change');
}

function EditOrderDetailModalProduct(products) {
    $.each(products, function(index, product) {
        $('#product_id_e').append(new Option(product.code, product.id, false, false));
    });

    let product_id = $('#EditOrderDetailButton').attr('data-product_id');

    if(product_id != '') {
        $("#product_id_e").val(product_id).trigger('change');
        $('#EditOrderDetailButton').attr('data-product_id', '');
    }
}

function EditOrderDetailProductGetColor(select) {
    if($(select).val() == '') {
        EditOrderDetailModalResetSelect('color_id_e');
    } else {
        let id = $('#EditOrderDetailButton').attr('data-id');
        $.ajax({
            url: `/Dashboard/Orders/Details/Edit/${id}`,
            type: 'POST',
            data: {
                '_token': $('meta[name="csrf-token"]').attr('content'),
                'product_id':  $(select).val()
            },
            success: function(response) {
                EditOrderDetailModalResetSelect('color_id_e');
                EditOrderDetailModalColor(response.data.colors);
                $('#price_e').attr('data-price', $(select).val() == $('#EditOrderDetailButton').attr('data-product') ? $('#EditOrderDetailButton').attr('data-price') : response.data.product.price);
                $('#price_e').val($(select).val() == $('#EditOrderDetailButton').attr('data-product') ? $('#EditOrderDetailButton').attr('data-negotiated_price') : response.data.product.price);
            },
            error: function(xhr, textStatus, errorThrown) {
                EditOrderDetailAjaxError(xhr);
            }
        });
    }
}

function EditOrderDetailModalColor(colors) {
    $.each(colors, function(index, color) {
        $('#color_id_e').append(new Option(`${color.name} - ${color.code}`, color.id, false, false));
    });

    let color_id = $('#EditOrderDetailButton').attr('data-color_id');

    if(color_id != '') {
        $("#color_id_e").val(color_id).trigger('change');
        $('#EditOrderDetailButton').attr('data-color_id', '');
    }
}

function EditOrderDetailColorGetQuantity() {
    if($('#color_id_e').val() == '') {
        let sizes = ['04', '06', '08', '10', '12', '14', '16', '18', '20', '22', '24', '26', '28', '30', '32', '34', '36', '38', 'XXS', 'XS', 'S', 'M', 'L', 'XL', 'XXL'];
        $.each(sizes, function(index, size) {
            $(`#div_t${size}_e`).show();
            $(`#et${size}_e`).text(0);
            $(`#dt${size}_e`).text(0);
            $(`#t${size}_e`).val(0);
        });
    } else {
        let id = $('#EditOrderDetailButton').attr('data-id');
        $.ajax({
            url: `/Dashboard/Orders/Details/Edit/${id}`,
            type: 'POST',
            data: {
                '_token': $('meta[name="csrf-token"]').attr('content'),
                'product_id':  $('#product_id_e').val(),
                'color_id':  $('#color_id_e').val(),
            },
            success: function(response) {
                EditOrderDetailModalSizes(response.data);
            },
            error: function(xhr, textStatus, errorThrown) {
                EditOrderDetailAjaxError(xhr);
            }
        });
    }
}

function EditOrderDetailModalSizes(data) {
    $.each(data.sizes, function(index, size) {
        if(data.inventory[`T${size.code}`] > 0 || data.committed[`T${size.code}`] > 0 || ($('#EditOrderDetailButton').attr(`data-t${size.code}`) > 0 && $('#product_id_e').val() == $('#EditOrderDetailButton').attr('data-product') && $('#color_id_e').val() == $('#EditOrderDetailButton').attr('data-color'))) {
            $(`#div_t${size.code}_e`).show();
            $(`#et${size.code}_e`).text(data.inventory[`T${size.code}`]);
            $(`#dt${size.code}_e`).text(data.inventory[`T${size.code}`] - data.committed[`T${size.code}`]);
            $(`#t${size.code}_e`).val($('#product_id_e').val() == $('#EditOrderDetailButton').attr('data-product') && $('#color_id_e').val() == $('#EditOrderDetailButton').attr('data-color') ? $('#EditOrderDetailButton').attr(`data-t${size.code}`) : 0);
        } else {
            $(`#div_t${size.code}_e`).hide();
            $(`#et${size.code}_e`).text(0);
            $(`#dt${size.code}_e`).text(0);
            $(`#t${size.code}_e`).val(0);
        }
    });
}

function EditOrderDetail(id) {
    Swal.fire({
        title: '¿Desea actualizar el detalle del pedido?',
        text: 'El detalle del pedido será actualizado.',
        icon: 'warning',
        showCancelButton: true,
        cancelButtonColor: '#DD6B55',
        confirmButtonColor: '#3085d6',
        confirmButtonText: 'Si, actualizar!',
        cancelButtonText: 'No, cancelar!',
    }).then((result) => {
        if (result.value) {
            $.ajax({
                url: `/Dashboard/Orders/Details/Update/${id}`,
                type: 'PUT',
                data: {
                    '_token': $('meta[name="csrf-token"]').attr('content'),
                    'order_id': $('#IndexOrderDetail').attr('data-id'),
                    'product_id': $('#product_id_e').val(),
                    'color_id': $('#color_id_e').val(),
                    'price': $('#price_e').attr('data-price'),
                    'negotiated_price': $('#price_e').val(),
                    'T04': $('#t04_e').val(),
                    'T06': $('#t06_e').val(),
                    'T08': $('#t08_e').val(),
                    'T10': $('#t10_e').val(),
                    'T12': $('#t12_e').val(),
                    'T14': $('#t14_e').val(),
                    'T16': $('#t16_e').val(),
                    'T18': $('#t18_e').val(),
                    'T20': $('#t20_e').val(),
                    'T22': $('#t22_e').val(),
                    'T24': $('#t24_e').val(),
                    'T26': $('#t26_e').val(),
                    'T28': $('#t28_e').val(),
                    'T30': $('#t30_e').val(),
                    'T32': $('#t32_e').val(),
                    'T34': $('#t34_e').val(),
                    'T36': $('#t36_e').val(),
                    'T38': $('#t38_e').val(),
                    'TXXS': $('#tXXS_e').val(),
                    'TXS': $('#tXS_e').val(),
                    'TS': $('#tS_e').val(),
                    'TM': $('#tM_e').val(),
                    'TL': $('#tL_e').val(),
                    'TXL': $('#tXL_e').val(),
                    'TXXL': $('#tXXL_e').val(),
                    'seller_observation': $('#seller_observation_e').val()
                },
                success: function (response) {
                    $('#IndexOrderDetail').trigger('click');
                    EditOrderDetailAjaxSuccess(response);
                },
                error: function (xhr, textStatus, errorThrown) {
                    $('#IndexOrderDetail').trigger('click');
                    EditOrderDetailAjaxError(xhr);
                }
            });
        } else {
            toastr.info('El detalle del pedido no fue actualizado.')
        }
    });
}

function EditOrderDetailAjaxSuccess(response) {
    if (response.status === 204) {
        toastr.info(response.message);
        $('#EditOrderDetailModal').modal('hide');
    }

    if (response.status === 200) {
        toastr.success(response.message);
        $('#EditOrderDetailModal').modal('hide');
    }
}

function EditOrderDetailAjaxError(xhr) {
    if (xhr.status === 403) {
        toastr.error(xhr.responseJSON.error ? xhr.responseJSON.error.message : xhr.responseJSON.message);
        $('#EditOrderDetailModal').modal('hide');
    }

    if (xhr.status === 404) {
        toastr.error(xhr.responseJSON.error ? xhr.responseJSON.error.message : xhr.responseJSON.message);
        $('#EditOrderDetailModal').modal('hide');
    }

    if (xhr.status === 419) {
        toastr.error(xhr.responseJSON.error ? xhr.responseJSON.error.message : xhr.responseJSON.message);
        $('#EditOrderDetailModal').modal('hide');
    }

    if (xhr.status === 422) {
        RemoveIsValidClassEditOrderDetail();
        RemoveIsInvalidClassEditOrderDetail();
        $.each(xhr.responseJSON.errors, function (field, messages) {
            AddIsInvalidClassEditOrderDetail(field.toLowerCase());
            $.each(messages, function (index, message) {
                toastr.error(message);
            });
        });
        AddIsValidClassEditOrderDetail();
    }

    if (xhr.status === 500) {
        toastr.error(xhr.responseJSON.error ? xhr.responseJSON.error.message : xhr.responseJSON.message);
        $('#EditOrderDetailModal').modal('hide');
    }
}

function AddIsValidClassEditOrderDetail() {
    if (!$('#price_e').hasClass('is-invalid')) {
        $('#price_e').addClass('is-valid');
    }
    if (!$('#seller_observation_e').hasClass('is-invalid')) {
        $('#seller_observation_e').addClass('is-valid');
    }
    if (!$('span[aria-labelledby="select2-product_id_e-container"]').hasClass('is-invalid')) {
        $('span[aria-labelledby="select2-product_id_e-container"]').addClass('is-valid');
    }
    if (!$('span[aria-labelledby="select2-color_id_e-container"]').hasClass('is-invalid')) {
        $('span[aria-labelledby="select2-color_id_e-container"]').addClass('is-valid');
    }
    let sizes = ['04', '06', '08', '10', '12', '14', '16', '18', '20', '22', '24', '26', '28', '30', '32', '34', '36', '38', 'XXS', 'XS', 'S', 'M', 'L', 'XL', 'XXL'];
    $.each(sizes, function(index, size) {
        if (!$(`#t${size}_e`).hasClass('is-invalid')) {
            $(`#t${size}_e`).addClass('is-valid');
        }
    });
}

function RemoveIsValidClassEditOrderDetail() {
    $('#price_e').removeClass('is-valid');
    $('#seller_observation_e').removeClass('is-valid');
    $(`span[aria-labelledby="select2-product_id_e-container"]`).removeClass('is-valid');
    $(`span[aria-labelledby="select2-color_id_e-container"]`).removeClass('is-valid');
    let sizes = ['04', '06', '08', '10', '12', '14', '16', '18', '20', '22', '24', '26', '28', '30', '32', '34', '36', '38', 'XXS', 'XS', 'S', 'M', 'L', 'XL', 'XXL'];
    $.each(sizes, function(index, size) {
        $(`#t${size}_e`).removeClass('is-valid');
    });
}

function AddIsInvalidClassEditOrderDetail(input) {
    if (!$(`#${input}_e`).hasClass('is-valid')) {
        $(`#${input}_e`).addClass('is-invalid');
    }
    if (!$(`#t${input}_e`).hasClass('is-valid')) {
        $(`#t${input}_e`).addClass('is-invalid');
    }
    if (!$(`span[aria-labelledby="select2-${input}_e-container`).hasClass('is-valid')) {
        $(`span[aria-labelledby="select2-${input}_e-container"]`).addClass('is-invalid');
    }
}

function RemoveIsInvalidClassEditOrderDetail() {
    $('#price_e').removeClass('is-invalid');
    $('#seller_observation_e').removeClass('is-invalid');
    $(`span[aria-labelledby="select2-product_id_e-container"]`).removeClass('is-invalid');
    $(`span[aria-labelledby="select2-color_id_e-container"]`).removeClass('is-invalid');
    let sizes = ['04', '06', '08', '10', '12', '14', '16', '18', '20', '22', '24', '26', '28', '30', '32', '34', '36', '38', 'XXS', 'XS', 'S', 'M', 'L', 'XL', 'XXL'];
    $.each(sizes, function(index, size) {
        $(`#t${size}_e`).removeClass('is-invalid');
    });
}
