function DiscountLibranzaModal(id) {
    $.ajax({
        url: `/Dashboard/Libranzas/Show/${id}`,
        type: 'POST',
        data: {
            '_token': $('meta[name="csrf-token"]').attr('content')
        },
        success: function (response) {
            DiscountLibranzaModalCleaned(response.data);
            DiscountLibranzaAjaxSuccess(response);
            $('#DiscountLibranzaModal').modal('show');
        },
        error: function (xhr, textStatus, errorThrown) {
            DiscountLibranzaAjaxError(xhr);
        }
    });
}

function DiscountLibranzaModalCleaned(libranza) {
    RemoveIsValidClassDiscountLibranza();
    RemoveIsInvalidClassDiscountLibranza();

    $('#DiscountLibranzaButton').attr('onclick', `DiscountLibranza(${libranza.id})`);
    $('#DiscountLibranzaButton').attr('data-id', libranza.id);

    $('#value_d').val('');
}

function DiscountLibranza(id, status = true) {
    Swal.fire({
        title: '¿Desea descontar la libranza?',
        text: 'La libranza será descontada.',
        icon: 'warning',
        showCancelButton: true,
        cancelButtonColor: '#DD6B55',
        confirmButtonColor: '#3085d6',
        confirmButtonText: 'Si, descontar!',
        cancelButtonText: 'No, cancelar!'
    }).then((result) => {
        if (result.value) {
            $.ajax({
                url: `/Dashboard/Libranzas/Discount`,
                type: 'PUT',
                data: {
                    '_token': $('meta[name="csrf-token"]').attr('content'),
                    'id': id,
                    'value': $('#value_d').val()
                },
                success: function(response) {
                    tableLibranzas.ajax.reload();
                    DiscountLibranzaAjaxSuccess(response);
                },
                error: function(xhr, textStatus, errorThrown) {
                    DiscountLibranzaAjaxError(xhr);
                }
            });
        } else {
            toastr.info('La libranza no fue descontada.')
        }
    });
}

function DiscountLibranzaAjaxSuccess(response) {
    if(response.status === 200) {
        toastr.success(response.message);
        $('#DiscountLibranzaModal').modal('hide');
    }
}

function DiscountLibranzaAjaxError(xhr) {
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
        RemoveIsValidClassDiscountLibranza();
        RemoveIsInvalidClassDiscountLibranza();
        $.each(xhr.responseJSON.errors, function (field, messages) {
            AddIsInvalidClassDiscountLibranza(field);
            $.each(messages, function (index, message) {
                toastr.error(message);
            });
        });
        AddIsValidClassDiscountLibranza();
    }

    if(xhr.status === 500){
        toastr.error(xhr.responseJSON.error ? xhr.responseJSON.error.message : xhr.responseJSON.message);
    }
}

function AddIsValidClassDiscountLibranza() {
    if (!$('#value_d').hasClass('is-invalid')) {
        $('#value_d').addClass('is-valid');
    }
}

function RemoveIsValidClassDiscountLibranza() {
    $('#value_d').removeClass('is-valid');
}

function AddIsInvalidClassDiscountLibranza(input) {
    if (!$(`#${input}_d`).hasClass('is-valid')) {
        $(`#${input}_d`).addClass('is-invalid');
    }
}

function RemoveIsInvalidClassDiscountLibranza() {
    $('#value_d').removeClass('is-invalid');
}
