function DeleteRoleAndPermissions(id, permission) {
    Swal.fire({
        title: '¿Desea eliminar el rol y los permisos?',
        text: 'El rol y sus permisos serán eliminados.',
        icon: 'warning',
        showCancelButton: true,
        cancelButtonColor: '#DD6B55',
        confirmButtonColor: '#3085d6',
        confirmButtonText: 'Si, eliminar!',
        cancelButtonText: 'No, cancelar!',
    }).then((result) => {
        if (result.value) {
            $.ajax({
                url: `/Dashboard/RolesAndPermissions/Delete`,
                type: 'DELETE',
                data: {
                    '_token': $('meta[name="csrf-token"]').attr('content'),
                    'role_id': [id],
                    'permission_id': permission
                },
                success: function(response) {
                    tableRolesAndPermissions.ajax.reload();
                    DeleteRoleAndPermissionsAjaxSuccess(response);
                },
                error: function(xhr, textStatus, errorThrown) {
                    DeleteRoleAndPermissionsAjaxError(xhr);
                }
            });
        } else {
            toastr.info('El rol y los permisos seleccionados no fueron eliminados.')
        }
    });
}

function DeleteRoleAndPermissionsAjaxSuccess(response) {
    if(response.status === 204) {
        toastr.success(response.message);
    }
}

function DeleteRoleAndPermissionsAjaxError(xhr) {
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
