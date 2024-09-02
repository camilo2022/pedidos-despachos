function CloneOrderDetailModal(id) {
    $.ajax({
        url: `/Dashboard/Orders/Details/Show/${id}`,
        type: 'POST',
        data: {
            '_token': $('meta[name="csrf-token"]').attr('content')
        },
        success: function (response) {
            $('#IndexOrderDetail').trigger('click');
            CloneOrderDetailModalCleaned(response.data.orderDetail);
            CloneOrderDetailModalProduct(response.data.products);
            CloneOrderDetailAjaxSuccess(response);
            $('#CloneOrderDetailModal').modal('show');
        },
        error: function (xhr, textStatus, errorThrown) {
            $('#IndexOrderDetail').trigger('click');
            CloneOrderDetailAjaxError(xhr);
        }
    });
}

function CloneOrderDetailModalCleaned(orderDetail) {
    CloneOrderDetailModalResetSelect('product_id_color_id_c');
    RemoveIsValidClassCloneOrderDetail();
    RemoveIsInvalidClassCloneOrderDetail();

    $('#CloneOrderDetailButton').attr('onclick', `CloneOrderDetail()`);
    $('#CloneOrderDetailButton').attr('data-id', orderDetail.id);
}

function CloneOrderDetailModalResetSelect(id) {
    $(`#${id}`).html('')
    $(`#${id}`).trigger('change');
}

function CloneOrderDetailModalProduct(products) {
    $.each(products, function(index, product) {
        $.each(product.colors, function(index, color) {
            $('#product_id_color_id_c').append(new Option(`${product.code} | ${color.name}: ${color.code}`, `${product.id}-${color.id}`, false, false));
        });
    });
}

$('#product_id_color_id_c').on('select2:select', function(e) {
    var selectedElement = e.params.data.id;
    var options = $(this).find('option');

    options.each(function() {
        if ($(this).val() == selectedElement) {
            $(this).prop('disabled', true);
        }
    });

    $(this).select2();
});

$('#product_id_color_id_c').on('select2:unselect', function(e) {
    var unselectedElement = e.params.data.id;
    var options = $(this).find('option');

    options.each(function() {
        if ($(this).val() == unselectedElement) {
            $(this).prop('disabled', false);
        }
    });
});

function CloneOrderDetail() {
    Swal.fire({
        title: '¿Desea clonar el detalle del pedido?',
        text: 'El detalle del pedido será clonado.',
        icon: 'warning',
        showCancelButton: true,
        cancelButtonColor: '#DD6B55',
        confirmButtonColor: '#3085d6',
        confirmButtonText: 'Si, clonar!',
        cancelButtonText: 'No, cancelar!',
    }).then((result) => {
        if (result.value) {
            $.ajax({
                url: `/Dashboard/Orders/Details/Clone`,
                type: 'POST',
                data: {
                    '_token': $('meta[name="csrf-token"]').attr('content'),
                    'order_id': $('#IndexOrderDetail').attr('data-id'),
                    'order_detail_id': $('#CloneOrderDetailButton').attr('data-id'),
                    'items': $('#product_id_color_id_c').find('option:selected').map(function() {
                        let array = $(this).val().split('-');
                        return {
                            product_id: parseInt(array[0]),
                            color_id: parseInt(array[1])
                        };
                    }).get()
                },
                success: function (response) {
                    $('#IndexOrderDetail').trigger('click');
                    CloneOrderDetailAjaxSuccess(response);
                },
                error: function (xhr, textStatus, errorThrown) {
                    $('#IndexOrderDetail').trigger('click');
                    CloneOrderDetailAjaxError(xhr);
                }
            });
        } else {
            toastr.info('El detalle del pedido no fue clonado.')
        }
    });
}

function CloneOrderDetailAjaxSuccess(response) {
    if (response.status === 204) {
        toastr.info(response.message);
        $('#CloneOrderDetailModal').modal('hide');
    }

    if (response.status === 201) {
        toastr.success(response.message);
        $('#CloneOrderDetailModal').modal('hide');
    }
}

function CloneOrderDetailAjaxError(xhr) {
    if (xhr.status === 403) {
        toastr.error(xhr.responseJSON.error ? xhr.responseJSON.error.message : xhr.responseJSON.message);
        $('#CloneOrderDetailModal').modal('hide');
    }

    if (xhr.status === 404) {
        toastr.error(xhr.responseJSON.error ? xhr.responseJSON.error.message : xhr.responseJSON.message);
        $('#CloneOrderDetailModal').modal('hide');
    }

    if (xhr.status === 419) {
        toastr.error(xhr.responseJSON.error ? xhr.responseJSON.error.message : xhr.responseJSON.message);
        $('#CloneOrderDetailModal').modal('hide');
    }

    if (xhr.status === 422) {
        RemoveIsValidClassCloneOrderDetail();
        RemoveIsInvalidClassCloneOrderDetail();
        $.each(xhr.responseJSON.errors, function (field, messages) {
            AddIsInvalidClassCloneOrderDetail(field.toLowerCase());
            $.each(messages, function (index, message) {
                toastr.error(message);
            });
        });
        AddIsValidClassCloneOrderDetail();
    }

    if (xhr.status === 500) {
        toastr.error(xhr.responseJSON.error ? xhr.responseJSON.error.message : xhr.responseJSON.message);
        $('#CloneOrderDetailModal').modal('hide');
    }
}

function AddIsValidClassCloneOrderDetail() {
    if (!$('span[aria-labelledby="select2-product_id_color_id_c-container"]').hasClass('is-invalid')) {
        $('span[aria-labelledby="select2-product_id_color_id_c-container"]').addClass('is-valid');
    }
}

function RemoveIsValidClassCloneOrderDetail() {
    $(`span[aria-labelledby="select2-product_id_color_id_c-container"]`).removeClass('is-valid');
}

function AddIsInvalidClassCloneOrderDetail(input) {
    if (!$(`span[aria-labelledby="select2-${input}_e-container`).hasClass('is-valid')) {
        $(`span[aria-labelledby="select2-${input}_e-container"]`).addClass('is-invalid');
    }
}

function RemoveIsInvalidClassCloneOrderDetail() {
    $(`span[aria-labelledby="select2-product_id_color_id_c-container"]`).removeClass('is-invalid');
}
