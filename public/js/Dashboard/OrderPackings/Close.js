function CloseOrderPackage(order_package_id) {
    Swal.fire({
        title: '¿Desea cerrar el empaque de la orden de empacado?',
        text: 'El empaque de la orden de empacado será cerrado.',
        icon: 'warning',
        showCancelButton: true,
        cancelButtonColor: '#DD6B55',
        confirmButtonColor: '#3085d6',
        confirmButtonText: 'Si, cerrar!',
        cancelButtonText: 'No, cancelar!',
        cancelButtonText: 'No, cancelar!',
        html:`<div class="input-group">
            <input class="form-control" id="weight_value_c" name="weight_value_c">
            <select class="form-control" id="weight_option_c" name="weight_option_c">
                <option value="KG">KG</option>
            </select>
        </div>`,
        footer: '<div class="text-center">El empaque de la orden de empacado será cerrado. Ingresa el peso del empaque.</div>'
    }).then((result) => {
        if (result.value) {
            $.ajax({
                url: `/Dashboard/Packings/Close`,
                type: 'PUT',
                data: {
                    '_token': $('meta[name="csrf-token"]').attr('content'),
                    'order_package_id': order_package_id,
                    'weight': `${$('#weight_value_c').val()} ${$('#weight_option_c').val()}`
                },
                success: function(response) {
                    $('#IndexOrderPacking').trigger('click');
                    if(response.data.urlOrderDispatches != null) {
                        window.location.href = response.data.urlOrderDispatches;
                    }
                    CloseOrderPackageAjaxSuccess(response);
                },
                error: function(xhr, textStatus, errorThrown) {
                    CloseOrderPackageAjaxError(xhr);
                }
            });
        } else {
            toastr.info('El empaque de la orden de despacho no fue cerrado.')
        }
    });
}

function CloseOrderPackageAjaxSuccess(response) {
    if(response.status === 200) {
        toastr.success(response.message);
    }
}

function CloseOrderPackageAjaxError(xhr) {
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
