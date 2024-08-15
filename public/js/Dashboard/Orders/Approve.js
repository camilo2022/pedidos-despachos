function ApproveOrder(id, status = true) {
    Swal.fire({
        title: '¿Desea aprobar el pedido?',
        text: 'El pedido será aprobado.',
        icon: 'warning',
        showCancelButton: true,
        cancelButtonColor: '#DD6B55',
        confirmButtonColor: '#3085d6',
        confirmButtonText: 'Si, aprobar!',
        cancelButtonText: 'No, cancelar!'
    }).then((result) => {
        if (result.value) {
            $.ajax({
                url: `/Dashboard/Orders/Approve`,
                type: 'PUT',
                data: {
                    '_token': $('meta[name="csrf-token"]').attr('content'),
                    'id': id
                },
                success: function(response) {
                    status ? tableOrders.ajax.reload() : location.reload() ;
                    ApproveOrderAjaxSuccess(response);
                },
                error: function(xhr, textStatus, errorThrown) {
                    ApproveOrderAjaxError(xhr);
                }
            });
        } else {
            toastr.info('El pedido seleccionada no fue aprobado.')
        }
    });
}

function ApproveOrderAjaxSuccess(response) {
    if(response.status === 200) {
        toastr.success(response.message);
    }

    if(response.status === 422) {
        toastr.warning(response.message);
    }
}

function ApproveOrderAjaxError(xhr) {
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
                if(field == 'quota') {
                    $(document).Toasts('create', {
                        class: 'bg-danger',
                        title: 'CARTERA VENCIDA PENDIENTE POR PAGO',
                        body: message.replace(/\n/g, '<br>')
                    });
                } else {
                    toastr.error(message);
                }
            });
        });
    }

    if(xhr.status === 500){
        toastr.error(xhr.responseJSON.error ? xhr.responseJSON.error.message : xhr.responseJSON.message);
    }
}
