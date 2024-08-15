function PartiallyApproveOrder(id, status = true) {
    Swal.fire({
        title: '¿Desea aprobar parcialmente el pedido?',
        text: 'El pedido será aprobado parcialmente.',
        icon: 'warning',
        showCancelButton: true,
        cancelButtonColor: '#DD6B55',
        confirmButtonColor: '#3085d6',
        confirmButtonText: 'Si, aprobar parcialmente!',
        cancelButtonText: 'No, cancelar!',
    }).then((result) => {
        if (result.value) {
            $.ajax({
                url: `/Dashboard/Orders/PartiallyApprove`,
                type: 'PUT',
                data: {
                    '_token': $('meta[name="csrf-token"]').attr('content'),
                    'id': id
                },
                success: function(response) {
                    status ? tableOrders.ajax.reload() : location.reload() ;
                    PartiallyApproveOrderAjaxSuccess(response);
                },
                error: function(xhr, textStatus, errorThrown) {
                    PartiallyApproveOrderAjaxError(xhr);
                }
            });
        } else {
            toastr.info('El pedido seleccionada no fue aprobado parcialmente.')
        }
    });
}

function PartiallyApproveOrderAjaxSuccess(response) {
    if(response.status === 200) {
        toastr.success(response.message);
    }

    if(response.status === 422) {
        toastr.warning(response.message);
    }
}

function PartiallyApproveOrderAjaxError(xhr) {
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
