function DeleteCategoryAndSubcategories(id) {
    Swal.fire({
        title: '¿Desea eliminar la categoria y subcategorias?',
        text: 'La categoria y subcategorias serán eliminados.',
        icon: 'warning',
        showCancelButton: true,
        cancelButtonColor: '#DD6B55',
        confirmButtonColor: '#3085d6',
        confirmButtonText: 'Si, eliminar!',
        cancelButtonText: 'No, cancelar!',
    }).then((result) => {
        if (result.value) {
            $.ajax({
                url: `/Dashboard/CategoriesAndSubcategories/Delete`,
                type: 'DELETE',
                data: {
                    '_token': $('meta[name="csrf-token"]').attr('content'),
                    'id': id
                },
                success: function(response) {
                    tableCategoriesAndSubcategories.ajax.reload();
                    DeleteCategoryAndSubcategoriesAjaxSuccess(response);
                },
                error: function(xhr, textStatus, errorThrown) {
                    DeleteCategoryAndSubcategoriesAjaxError(xhr);
                }
            });
        } else {
            toastr.info('La categoria y las subcategorias seleccionadas no fueron eliminadas.')
        }
    });
}

function DeleteCategoryAndSubcategoriesAjaxSuccess(response) {
    if(response.status === 204) {
        toastr.success(response.message);
    }
}

function DeleteCategoryAndSubcategoriesAjaxError(xhr) {
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
