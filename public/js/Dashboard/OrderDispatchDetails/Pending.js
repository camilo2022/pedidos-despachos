function PendingOrderDispatchDetails() {
    let orderDispatchDetails = $('input[type="checkbox"].details:checked');

    if (orderDispatchDetails.length > 0) {
        Swal.fire({
            title: '¿Desea devolver los detalles seleccionados de la orden de despacho?',
            text: 'Los detalles seleccionados de la orden de despacho serán devueltos.',
            icon: 'warning',
            showCancelButton: true,
            cancelButtonColor: '#DD6B55',
            confirmButtonColor: '#3085d6',
            confirmButtonText: 'Si, devolver!',
            cancelButtonText: 'No, cancelar!',
        }).then((result) => {
            if (result.value) {
                orderDispatchDetails.each(function() {
                    $.ajax({
                        url: `/Dashboard/Dispatches/Details/Pending`,
                        type: 'PUT',
                        data: {
                            '_token': $('meta[name="csrf-token"]').attr('content'),
                            'id': $(this).attr('id')
                        },
                        success: function(response) {
                            $('#IndexOrderDispatchDetail').trigger('click');
                            PendingOrderDispatchDetailAjaxSuccess(response);
                        },
                        error: function(xhr, textStatus, errorThrown) {
                            $('#IndexOrderDispatchDetail').trigger('click');
                            PendingOrderDispatchDetailAjaxError(xhr);
                        }
                    });
                });
            } else {
                toastr.info('Los detalles seleccionados de la orden de despacho no fueron devueltos.');
            }
        });
    } else {
        toastr.error('No se ha seleccionado ningún detalle de la orden de despacho para devolver.');
    }
}

function PendingOrderDispatchDetail(id) {
    Swal.fire({
        title: '¿Desea devolver el detalle de la orden de despacho del pedido?',
        text: 'El detalle de la orden de despacho del pedido será devuelto.',
        icon: 'warning',
        showCancelButton: true,
        cancelButtonColor: '#DD6B55',
        confirmButtonColor: '#3085d6',
        confirmButtonText: 'Si, devolver!',
        cancelButtonText: 'No, cancelar!',
    }).then((result) => {
        if (result.value) {
            $.ajax({
                url: `/Dashboard/Dispatches/Details/Pending`,
                type: 'PUT',
                data: {
                    '_token': $('meta[name="csrf-token"]').attr('content'),
                    'id': id
                },
                success: function(response) {
                    $('#IndexOrderDispatchDetail').trigger('click');
                    PendingOrderDispatchDetailAjaxSuccess(response);
                },
                error: function(xhr, textStatus, errorThrown) {
                    $('#IndexOrderDispatchDetail').trigger('click');
                    PendingOrderDispatchDetailAjaxError(xhr);
                }
            });
        } else {
            toastr.info('El detalle de la orden de despacho del pedido seleccionada no fue devuelto.')
        }
    });
}

function PendingOrderDispatchDetailAjaxSuccess(response) {
    if(response.status === 200) {
        toastr.success(response.message);
    }

    if(response.status === 422) {
        toastr.warning(response.message);
    }
}

function PendingOrderDispatchDetailAjaxError(xhr) {
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
