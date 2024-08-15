function StoreOrderPacking(id) {
    let options = ``;

    $.each(packageTypes, function(index, package_type) {
        options += `<option value="${package_type.id}">${package_type.name}</option>`;
    });

    Swal.fire({
        title: '¿Desea añadir un nuevo empaque a la orden de empacado?',
        text: 'Se añadira un empaque nuevo a la orden de empacado.',
        icon: 'warning',
        showCancelButton: true,
        cancelButtonColor: '#DD6B55',
        confirmButtonColor: '#3085d6',
        confirmButtonText: 'Si, añadir!',
        cancelButtonText: 'No, cancelar!',
        html:`<div class="input-group">
            <select class="form-control" id="package_type_id_c" name="package_type_id_c">
                <option value="">SELECCIONE</option>
                ${options}
            </select>
        </div>`,
        footer: '<div class="text-center">Selecciona el tipo de empaque.</div>'
    }).then((result) => {
        if (result.value) {
            $.ajax({
                url: `/Dashboard/Packings/Store`,
                type: 'POST',
                data: {
                    '_token': $('meta[name="csrf-token"]').attr('content'),
                    'id': id,
                    'package_type_id': $('#package_type_id_c').val()
                },
                success: function(response) {
                    $('#IndexOrderPacking').trigger('click');
                    StoreOrderPackingAjaxSuccess(response);
                },
                error: function(xhr, textStatus, errorThrown) {
                    StoreOrderPackingAjaxError(xhr);
                }
            });
        } else {
            toastr.info('El empaque no fue añadido a la orden de empacado.')
        }
    });
}

function StoreOrderPackingAjaxSuccess(response) {
    if(response.status === 200) {
        toastr.success(response.message);
    }
}

function StoreOrderPackingAjaxError(xhr) {
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
