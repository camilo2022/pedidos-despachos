function DeleteModuleAndSubmodules(id) {
    Swal.fire({
        title: '¿Desea eliminar el modulos y los submodulos?',
        text: 'El modulo y sus submodulos serán eliminados.',
        icon: 'warning',
        showCancelButton: true,
        cancelButtonColor: '#DD6B55',
        confirmButtonColor: '#3085d6',
        confirmButtonText: 'Si, eliminar!',
        cancelButtonText: 'No, cancelar!',
    }).then((result) => {
        if (result.value) {
            $.ajax({
                url: `/Dashboard/ModulesAndSubmodules/Delete`,
                type: 'DELETE',
                data: {
                    '_token': $('meta[name="csrf-token"]').attr('content'),
                    'id': id
                },
                success: function(response) {
                    tableModulesAndSubmodules.ajax.reload();
                    DeleteModuleAndSubmodulesAjaxSuccess(response);
                },
                error: function(xhr, textStatus, errorThrown) {
                    DeleteModuleAndSubmodulesAjaxError(xhr);
                }
            });
        } else {
            toastr.info('El modulo y los submodulos seleccionados no fueron eliminados.')
        }
    });
}

function DeleteModuleAndSubmodulesAjaxSuccess(response) {
    if(response.status === 204) {
        toastr.success(response.message);
    }
}

function DeleteModuleAndSubmodulesAjaxError(xhr) {
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
