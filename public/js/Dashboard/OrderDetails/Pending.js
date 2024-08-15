function PendingOrderDetail(id) {
    Swal.fire({
        title: '¿Desea pendiente el detalle del pedido?',
        text: 'El detalle del pedido será pendiente.',
        icon: 'warning',
        showCancelButton: true,
        cancelButtonColor: '#DD6B55',
        confirmButtonColor: '#3085d6',
        confirmButtonText: 'Si, pendiente!',
        cancelButtonText: 'No, cancelar!',
    }).then((result) => {
        if (result.value) {
            $.ajax({
                url: `/Dashboard/Orders/Details/Pending`,
                type: 'PUT',
                data: {
                    '_token': $('meta[name="csrf-token"]').attr('content'),
                    'id': id
                },
                success: function(response) {
                    $('#IndexOrderDetail').trigger('click');
                    PendingOrderDetailAjaxSuccess(response);
                },
                error: function(xhr, textStatus, errorThrown) {
                    $('#IndexOrderDetail').trigger('click');
                    PendingOrderDetailAjaxError(xhr);
                }
            });
        } else {
            toastr.info('El detalle del pedido seleccionada no fue pendiente.')
        }
    });
}

function PendingOrderDetailAjaxSuccess(response) {
    if(response.status === 200) {
        toastr.success(response.message);
    }

    if(response.status === 422) {
        toastr.warning(response.message);
    }
}

function PendingOrderDetailAjaxError(xhr) {
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
