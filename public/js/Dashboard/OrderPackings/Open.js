function OpenOrderPackage(order_package_id) {
    Swal.fire({
        title: '¿Desea abrir el empaque de la orden de empacado?',
        text: 'El empaque de la orden de empacado será abierto.',
        icon: 'warning',
        showCancelButton: true,
        cancelButtonColor: '#DD6B55',
        confirmButtonColor: '#3085d6',
        confirmButtonText: 'Si, abrir!',
        cancelButtonText: 'No, cancelar!',
    }).then((result) => {
        if (result.value) {
            $.ajax({
                url: `/Dashboard/Packings/Open`,
                type: 'PUT',
                data: {
                    '_token': $('meta[name="csrf-token"]').attr('content'),
                    'order_package_id': order_package_id
                },
                success: function(response) {
                    $('#IndexOrderPacking').trigger('click');
                    OpenOrderPackageAjaxSuccess(response);
                },
                error: function(xhr, textStatus, errorThrown) {
                    OpenOrderPackageAjaxError(xhr);
                }
            });
        } else {
            toastr.info('El empaque de la orden de despacho no fue abierto.')
        }
    });
}

function OpenOrderPackageAjaxSuccess(response) {
    if(response.status === 200) {
        toastr.success(response.message);
    }
}

function OpenOrderPackageAjaxError(xhr) {
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
