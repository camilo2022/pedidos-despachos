function ConfirmLibranza(id) {
    Swal.fire({
        title: '¿Desea confirmar la libranza?',
        text: 'La libranza será confirmada.',
        icon: 'warning',
        showCancelButton: true,
        cancelButtonColor: '#DD6B55',
        confirmButtonColor: '#3085d6',
        confirmButtonText: 'Si, confirmar!',
        cancelButtonText: 'No, cancelar!'
    }).then((result) => {
        if (result.value) {
            $.ajax({
                url: `/Dashboard/Libranzas/Confirm/${id}`,
                type: 'PUT',
                data: {
                    '_token': $('meta[name="csrf-token"]').attr('content'),
                    'id': id,
                    'share': $('#share_l').val(),
                    'code': $('#code_l').val()
                },
                success: function(response) {
                    if(response.data.url != null){
                        window.open(response.data.url, '_blank');
                    }
                    ConfirmLibranzaAjaxSuccess(response);
                },
                error: function(xhr, textStatus, errorThrown) {
                    ConfirmLibranzaAjaxError(xhr);
                }
            });
        } else {
            toastr.info('La libranza no fue confirmada.')
        }
    });
}

function ConfirmLibranzaAjaxSuccess(response) {
    if(response.status === 200) {
        toastr.success(response.message);
        $('#ConfirmLibranzaModal').modal('hide');
    }
}

function ConfirmLibranzaAjaxError(xhr) {
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
        RemoveIsValidClassConfirmLibranza();
        RemoveIsInvalidClassConfirmLibranza();
        $.each(xhr.responseJSON.errors, function (field, messages) {
            AddIsInvalidClassConfirmLibranza(field);
            $.each(messages, function (index, message) {
                toastr.error(message);
            });
        });
        AddIsValidClassConfirmLibranza();
    }

    if(xhr.status === 500){
        toastr.error(xhr.responseJSON.error ? xhr.responseJSON.error.message : xhr.responseJSON.message);
    }
}

function AddIsValidClassConfirmLibranza() {
    if (!$('#share_l').hasClass('is-invalid')) {
        $('#share_l').addClass('is-valid');
    }
    if (!$('#code_l').hasClass('is-invalid')) {
        $('#code_l').addClass('is-valid');
    }
}

function RemoveIsValidClassConfirmLibranza() {
    $('#share_l').removeClass('is-valid');
    $('#code_l').removeClass('is-valid');
}

function AddIsInvalidClassConfirmLibranza(input) {
    if (!$(`#${input}_l`).hasClass('is-valid')) {
        $(`#${input}_l`).addClass('is-invalid');
    }
}

function RemoveIsInvalidClassConfirmLibranza() {
    $('#share_l').removeClass('is-invalid');
    $('#code_l').removeClass('is-invalid');
}
