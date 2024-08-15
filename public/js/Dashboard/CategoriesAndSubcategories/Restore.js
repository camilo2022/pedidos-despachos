function RestoreCategoryAndSubcategories(id) {
    Swal.fire({
        title: '¿Desea restaurar la categoria y subcategorias?',
        text: 'La categoria y subcategorias serán restaurada.',
        icon: 'warning',
        showCancelButton: true,
        cancelButtonColor: '#DD6B55',
        confirmButtonColor: '#3085d6',
        confirmButtonText: 'Si, restaurar!',
        cancelButtonText: 'No, cancelar!'
    }).then((result) => {
        if (result.value) {
            $.ajax({
                url: '/Dashboard/CategoriesAndSubcategories/Restore',
                type: 'PUT',
                data: {
                    '_token': $('meta[name="csrf-token"]').attr('content'),
                    'id': id
                },
                success: function(response) {
                    tableCategoriesAndSubcategories.ajax.reload();
                    RestoreCategoryAndSubcategoriesAjaxSuccess(response);
                },
                error: function(xhr, textStatus, errorThrown) {
                    RestoreCategoryAndSubcategoriesAjaxError(xhr);
                }
            });
        } else {
            toastr.info('La categoria y las subcategorias seleccionadas no fueron restauradas.')
        }
    });
}

function RestoreCategoryAndSubcategoriesAjaxSuccess(response) {
    if(response.status === 204) {
        toastr.success(response.message);
    }
}

function RestoreCategoryAndSubcategoriesAjaxError(xhr) {
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
