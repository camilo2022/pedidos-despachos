function CancelOrderDetails() {
    let orderDetails = $('input[type="checkbox"].details:checked');

    if (orderDetails.length > 0) {
        Swal.fire({
            title: '¿Desea cancelar los detalles seleccionados del pedido?',
            text: 'Los detalles seleccionados del pedido serán cancelados.',
            icon: 'warning',
            showCancelButton: true,
            cancelButtonColor: '#DD6B55',
            confirmButtonColor: '#3085d6',
            confirmButtonText: 'Si, cancelar!',
            cancelButtonText: 'No, cancelar!',
        }).then((result) => {
            if (result.value) {
                orderDetails.each(function() {
                    $.ajax({
                        url: `/Dashboard/Orders/Details/Cancel`,
                        type: 'PUT',
                        data: {
                            '_token': $('meta[name="csrf-token"]').attr('content'),
                            'id': $(this).attr('id')
                        },
                        success: function(response) {
                            $('#IndexOrderDetail').trigger('click');
                            CancelOrderDetailAjaxSuccess(response);
                        },
                        error: function(xhr, textStatus, errorThrown) {
                            $('#IndexOrderDetail').trigger('click');
                            CancelOrderDetailAjaxError(xhr);
                        }
                    });
                });
            } else {
                toastr.info('Los detalles seleccionados del pedido no fueron cancelados.');
            }
        });
    } else {
        toastr.error('No se ha seleccionado ningún detalle del pedido para cancelar.');
    }
}

function CancelOrderDetail(id) {
    Swal.fire({
        title: '¿Desea cancelar el detalle del pedido?',
        text: 'El detalle del pedido será cancelado.',
        icon: 'warning',
        showCancelButton: true,
        cancelButtonColor: '#DD6B55',
        confirmButtonColor: '#3085d6',
        confirmButtonText: 'Si, cancelar!',
        cancelButtonText: 'No, cancelar!',
    }).then((result) => {
        if (result.value) {
            $.ajax({
                url: `/Dashboard/Orders/Details/Cancel`,
                type: 'PUT',
                data: {
                    '_token': $('meta[name="csrf-token"]').attr('content'),
                    'id': id
                },
                success: function(response) {
                    $('#IndexOrderDetail').trigger('click');
                    CancelOrderDetailAjaxSuccess(response);
                },
                error: function(xhr, textStatus, errorThrown) {
                    $('#IndexOrderDetail').trigger('click');
                    CancelOrderDetailAjaxError(xhr);
                }
            });
        } else {
            toastr.info('El detalle del pedido seleccionada no fue cancelado.')
        }
    });
}

function CancelOrderDetailAjaxSuccess(response) {
    if(response.status === 200) {
        toastr.success(response.message);
    }

    if(response.status === 422) {
        toastr.warning(response.message);
    }
}

function CancelOrderDetailAjaxError(xhr) {
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
