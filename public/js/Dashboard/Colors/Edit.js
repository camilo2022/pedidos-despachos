function EditColorModal(id) {
    $.ajax({
        url: `/Dashboard/Colors/Edit/${id}`,
        type: 'POST',
        data: {
            '_token': $('meta[name="csrf-token"]').attr('content')
        },
        success: function (response) {
            EditColorModalCleaned(response.data);
            EditColorAjaxSuccess(response);
            $('#EditColorModal').modal('show');
        },
        error: function (xhr, textStatus, errorThrown) {
            EditColorAjaxError(xhr);
        }
    });
}

function EditColorModalCleaned(color) {
    RemoveIsValidClassEditColor();
    RemoveIsInvalidClassEditColor();

    $('#EditColorButton').attr('onclick', `EditColor(${color.id})`);

    $("#name_e").val(color.name);
    $("#code_e").val(color.code);
}

function EditColor(id) {
    Swal.fire({
        title: '¿Desea actualizar el color?',
        text: 'El color se actualizara.',
        icon: 'warning',
        showCancelButton: true,
        cancelButtonColor: '#DD6B55',
        confirmButtonColor: '#3085d6',
        confirmButtonText: 'Si, actualizar!',
        cancelButtonText: 'No, cancelar!',
    }).then((result) => {
        if (result.value) {
            $.ajax({
                url: `/Dashboard/Colors/Update/${id}`,
                type: 'PUT',
                data: {
                    '_token': $('meta[name="csrf-token"]').attr('content'),
                    'name': $('#name_e').val(),
                    'code': $('#code_e').val(),
                },
                success: function (response) {
                    tableColors.ajax.reload();
                    EditColorAjaxSuccess(response);
                },
                error: function (xhr, textStatus, errorThrown) {
                    EditColorAjaxError(xhr);
                }
            });
        } else {
            toastr.info('El color no fue actualizado.')
        }
    });
}

function EditColorAjaxSuccess(response) {
    if (response.status === 204) {
        toastr.info(response.message);
        $('#EditColorModal').modal('hide');
    }
    
    if (response.status === 200) {
        toastr.success(response.message);
        $('#EditColorModal').modal('hide');
    }
}

function EditColorAjaxError(xhr) {
    if (xhr.status === 403) {
        toastr.error(xhr.responseJSON.error ? xhr.responseJSON.error.message : xhr.responseJSON.message);
        $('#EditColorModal').modal('hide');
    }

    if (xhr.status === 404) {
        toastr.error(xhr.responseJSON.error ? xhr.responseJSON.error.message : xhr.responseJSON.message);
        $('#EditColorModal').modal('hide');
    }

    if (xhr.status === 419) {
        toastr.error(xhr.responseJSON.error ? xhr.responseJSON.error.message : xhr.responseJSON.message);
        $('#EditColorModal').modal('hide');
    }

    if (xhr.status === 422) {
        RemoveIsValidClassEditColor();
        RemoveIsInvalidClassEditColor();
        $.each(xhr.responseJSON.errors, function (field, messages) {
            AddIsInvalidClassEditColor(field);
            $.each(messages, function (index, message) {
                toastr.error(message);
            });
        });
        AddIsValidClassEditColor();
    }

    if (xhr.status === 500) {
        toastr.error(xhr.responseJSON.error ? xhr.responseJSON.error.message : xhr.responseJSON.message);
        $('#EditColorModal').modal('hide');
    }
}

function AddIsValidClassEditColor() {
    if (!$('#name_e').hasClass('is-invalid')) {
        $('#name_e').addClass('is-valid');
    }
    if (!$('#code_e').hasClass('is-invalid')) {
        $('#code_e').addClass('is-valid');
    }
}

function RemoveIsValidClassEditColor() {
    $('#name_e').removeClass('is-valid');
    $('#code_e').removeClass('is-valid');
}

function AddIsInvalidClassEditColor(input) {
    if (!$(`#${input}_e`).hasClass('is-valid')) {
        $(`#${input}_e`).addClass('is-invalid');
    }
}

function RemoveIsInvalidClassEditColor() {
    $('#name_e').removeClass('is-invalid');
    $('#code_e').removeClass('is-invalid');
}
