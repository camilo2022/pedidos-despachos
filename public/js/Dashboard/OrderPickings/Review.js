function ReviewOrderPicking(id) {
    Swal.fire({
        title: '¿Desea revisar el alistamiento de la orden de despacho?',
        text: 'El alistamiento de la orden de despacho será revisado.',
        icon: 'warning',
        showCancelButton: true,
        cancelButtonColor: '#DD6B55',
        confirmButtonColor: '#3085d6',
        confirmButtonText: 'Si, revisar!',
        cancelButtonText: 'No, cancelar!',
    }).then((result) => {
        if (result.value) {
            $.ajax({
                url: `/Dashboard/Pickings/Review`,
                type: 'PUT',
                data: {
                    '_token': $('meta[name="csrf-token"]').attr('content'),
                    'id': id
                },
                success: function(response) {
                    window.location.href = response.data.urlOrderDispatches;
                    ReviewOrderPickingAjaxSuccess(response);
                },
                error: function(xhr, textStatus, errorThrown) {
                    ReviewOrderPickingAjaxError(xhr);
                }
            });
        } else {
            toastr.info('El alistamiento de la orden de despacho no fue revisado.')
        }
    });
}

function ReviewOrderPickingAjaxSuccess(response) {
    if(response.status === 200) {
        toastr.success(response.message);
    }
}

function ReviewOrderPickingAjaxError(xhr) {
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
