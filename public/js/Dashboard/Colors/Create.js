function CreateColorModal() {
    $.ajax({
        url: `/Dashboard/Colors/Create`,
        type: 'POST',
        data: {
            '_token': $('meta[name="csrf-token"]').attr('content')
        },
        success: function (response) {
            CreateColorModalCleaned();
            CreateColorAjaxSuccess(response);
            $('#CreateColorModal').modal('show');
        },
        error: function (xhr, textStatus, errorThrown) {
            CreateColorAjaxError(xhr);
        }
    });
}

function CreateColorModalCleaned() {
    RemoveIsValidClassCreateColor();
    RemoveIsInvalidClassCreateColor();

    $('#name_c').val('');
    $('#code_c').val('');
}

function CreateColor() {
    Swal.fire({
        title: '¿Desea guardar el color?',
        text: 'El color será creado.',
        icon: 'warning',
        showCancelButton: true,
        cancelButtonColor: '#DD6B55',
        confirmButtonColor: '#3085d6',
        confirmButtonText: 'Si, guardar!',
        cancelButtonText: 'No, cancelar!',
    }).then((result) => {
        if (result.value) {
            $.ajax({
                url: `/Dashboard/Colors/Store`,
                type: 'POST',
                data: {
                    '_token': $('meta[name="csrf-token"]').attr('content'),
                    'name': $('#name_c').val(),
                    'code': $('#code_c').val(),
                },
                success: function (response) {
                    tableColors.ajax.reload();
                    CreateColorAjaxSuccess(response);
                },
                error: function (xhr, textStatus, errorThrown) {
                    CreateColorAjaxError(xhr);
                }
            });
        } else {
            toastr.info('El color no fue creado.')
        }
    });
}

function CreateColorAjaxSuccess(response) {
    if (response.status === 204) {
        toastr.info(response.message);
        $('#CreateColorModal').modal('hide');
    }

    if (response.status === 201) {
        toastr.success(response.message);
        $('#CreateColorModal').modal('hide');
    }
}

function CreateColorAjaxError(xhr) {
    if (xhr.status === 403) {
        toastr.error(xhr.responseJSON.error ? xhr.responseJSON.error.message : xhr.responseJSON.message);
        $('#CreateColorModal').modal('hide');
    }

    if (xhr.status === 404) {
        toastr.error(xhr.responseJSON.error ? xhr.responseJSON.error.message : xhr.responseJSON.message);
        $('#CreateColorModal').modal('hide');
    }

    if (xhr.status === 419) {
        toastr.error(xhr.responseJSON.error ? xhr.responseJSON.error.message : xhr.responseJSON.message);
        $('#CreateColorModal').modal('hide');
    }

    if (xhr.status === 422) {
        RemoveIsValidClassCreateColor();
        RemoveIsInvalidClassCreateColor();
        $.each(xhr.responseJSON.errors, function (field, messages) {
            AddIsInvalidClassCreateColor(field);
            $.each(messages, function (index, message) {
                toastr.error(message);
            });
        });
        AddIsValidClassCreateColor();
    }

    if (xhr.status === 500) {
        toastr.error(xhr.responseJSON.error ? xhr.responseJSON.error.message : xhr.responseJSON.message);
        $('#CreateColorModal').modal('hide');
    }
}

function AddIsValidClassCreateColor() {
    if (!$('#name_c').hasClass('is-invalid')) {
        $('#name_c').addClass('is-valid');
    }
    if (!$('#code_c').hasClass('is-invalid')) {
        $('#code_c').addClass('is-valid');
    }
}

function RemoveIsValidClassCreateColor() {
    $('#name_c').removeClass('is-valid');
    $('#code_c').removeClass('is-valid');
}

function AddIsInvalidClassCreateColor(input) {
    if (!$(`#${input}_c`).hasClass('is-valid')) {
        $(`#${input}_c`).addClass('is-invalid');
    }
}

function RemoveIsInvalidClassCreateColor() {
    $('#name_c').removeClass('is-invalid');
    $('#code_c').removeClass('is-invalid');
}
