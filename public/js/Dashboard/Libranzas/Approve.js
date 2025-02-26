function ApproveLibranza(id) {
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
                url: `/Dashboard/Libranzas/Approve`,
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
                    ApproveLibranzaAjaxSuccess(response);
                },
                error: function(xhr, textStatus, errorThrown) {
                    ApproveLibranzaAjaxError(xhr);
                }
            });
        } else {
            toastr.info('La libranza no fue confirmada.')
        }
    });
}

function ApproveLibranzaAjaxSuccess(response) {
    if(response.status === 200) {
        toastr.success(response.message);
        $('#ApproveLibranzaModal').modal('hide');
    }
}

function ApproveLibranzaAjaxError(xhr) {
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
        RemoveIsValidClassApproveLibranza();
        RemoveIsInvalidClassApproveLibranza();
        $.each(xhr.responseJSON.errors, function (field, messages) {
            AddIsInvalidClassApproveLibranza(field);
            $.each(messages, function (index, message) {
                toastr.error(message);
            });
        });
        AddIsValidClassApproveLibranza();
    }

    if(xhr.status === 500){
        toastr.error(xhr.responseJSON.error ? xhr.responseJSON.error.message : xhr.responseJSON.message);
    }
}

function AddIsValidClassApproveLibranza() {
    if (!$('#share_l').hasClass('is-invalid')) {
        $('#share_l').addClass('is-valid');
    }
    if (!$('#code_l').hasClass('is-invalid')) {
        $('#code_l').addClass('is-valid');
    }
}

function RemoveIsValidClassApproveLibranza() {
    $('#share_l').removeClass('is-valid');
    $('#code_l').removeClass('is-valid');
}

function AddIsInvalidClassApproveLibranza(input) {
    if (!$(`#${input}_l`).hasClass('is-valid')) {
        $(`#${input}_l`).addClass('is-invalid');
    }
}

function RemoveIsInvalidClassApproveLibranza() {
    $('#share_l').removeClass('is-invalid');
    $('#code_l').removeClass('is-invalid');
}
