function RestoreClient(id) {
    Swal.fire({
        title: '¿Desea restaurar el cliente?',
        text: 'El cliente será restaurada.',
        icon: 'warning',
        showCancelButton: true,
        cancelButtonColor: '#DD6B55',
        confirmButtonColor: '#3085d6',
        confirmButtonText: 'Si, restaurar!',
        cancelButtonText: 'No, cancelar!'
    }).then((result) => {
        if (result.value) {
            $.ajax({
                url: '/Dashboard/Clients/Restore',
                type: 'PUT',
                data: {
                    '_token': $('meta[name="csrf-token"]').attr('content'),
                    'id': id
                },
                success: function(response) {
                    tableClients.ajax.reload();
                    RestoreClientAjaxSuccess(response);
                },
                error: function(xhr, textStatus, errorThrown) {
                    RestoreClientAjaxError(xhr);
                }
            });
        } else {
            toastr.info('El cliente seleccionada no fue restaurado.')
        }
    });
}

function RestoreClientAjaxSuccess(response) {
    if(response.status === 204) {
        toastr.success(response.message);
    }
}

function RestoreClientAjaxError(xhr) {
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
