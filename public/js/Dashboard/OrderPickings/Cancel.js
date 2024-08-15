function CancelOrderPicking(id) {
    Swal.fire({
        title: '¿Desea cancelar el alistamiento de la orden de despacho?',
        text: 'El alistamiento de la orden de despacho será cancelado.',
        icon: 'warning',
        showCancelButton: true,
        cancelButtonColor: '#DD6B55',
        confirmButtonColor: '#3085d6',
        confirmButtonText: 'Si, cancelar!',
        cancelButtonText: 'No, cancelar!',
    }).then((result) => {
        if (result.value) {
            $.ajax({
                url: `/Dashboard/Pickings/Cancel`,
                type: 'PUT',
                data: {
                    '_token': $('meta[name="csrf-token"]').attr('content'),
                    'id': id
                },
                success: function(response) {
                    window.location.href = response.data.urlOrderDispatches;
                    CancelOrderPickingAjaxSuccess(response);
                },
                error: function(xhr, textStatus, errorThrown) {
                    CancelOrderPickingAjaxError(xhr);
                }
            });
        } else {
            toastr.info('El alistamiento de la orden de despacho no fue cancelado.')
        }
    });
}

function CancelOrderPickingAjaxSuccess(response) {
    if(response.status === 200) {
        toastr.success(response.message);
    }
}

function CancelOrderPickingAjaxError(xhr) {
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
