function CreateUserModal() {
    $.ajax({
        url: `/Dashboard/Users/Create`,
        type: 'POST',
        data: {
            '_token': $('meta[name="csrf-token"]').attr('content')
        },
        success: function(response) {
            CreateUserModalCleaned(response.data.titles, response.data.zones);
            CreateUserAjaxSuccess(response);
            $('#CreateUserModal').modal('show');
        },
        error: function(xhr, textStatus, errorThrown) {
            CreateUserAjaxError(xhr);
        }
    });
}

function CreateUserModalCleaned(titles, zones) {
    RemoveIsValidClassCreateUser();
    RemoveIsInvalidClassCreateUser();

    $('#name_c').val('');
    $('#last_name_c').val('');
    $('#document_number_c').val('');
    $('#phone_number_c').val('');
    $('#address_c').val('');
    $('#email_c').val('');
    $('#password_c').val('');
    $('#title_c').empty().append(`<option value="">Seleccione</option>`);
    $.each(titles, function(index, title) {
        $('#title_c').append(`<option value="${title}">${title}</option>`);
    });
    $.each(zones, function(index, zone) {
        $('#zone_c').append(`<option value="${zone}">${zone}</option>`);
    });
}

function CreateUser() {
    Swal.fire({
        title: '¿Desea guardar el usuario?',
        text: 'El usuario será creado.',
        icon: 'warning',
        showCancelButton: true,
        cancelButtonColor: '#DD6B55',
        confirmButtonColor: '#3085d6',
        confirmButtonText: 'Si, guardar!',
        cancelButtonText: 'No, cancelar!',
    }).then((result) => {
        if (result.value) {
            $.ajax({
                url: `/Dashboard/Users/Store`,
                type: 'POST',
                data: {
                    '_token': $('meta[name="csrf-token"]').attr('content'),
                    'name': $('#name_c').val(),
                    'last_name': $('#last_name_c').val(),
                    'document_number': $('#document_number_c').val(),
                    'phone_number': $('#phone_number_c').val(),
                    'address': $('#address_c').val(),
                    'email': $('#email_c').val(),
                    'title': $('#title_c').val(),
                    'zone': $('#zone_c').val(),
                    'password': $('#password_c').val(),
                    'password_confirmation': $('#password_confirmation_c').val()
                },
                success: function(response) {
                    tableUsers.ajax.reload();
                    CreateUserAjaxSuccess(response);
                },
                error: function(xhr, textStatus, errorThrown) {
                    CreateUserAjaxError(xhr);
                }
            });
        } else {
            toastr.info('El usuario no fue creado.')
        }
    });
}

function CreateUserAjaxSuccess(response) {
    if(response.status === 204) {
        toastr.info(response.message);
        $('#CreateUserModal').modal('hide');
    }

    if(response.status === 201) {
        toastr.success(response.message);
        $('#CreateUserModal').modal('hide');
    }
}

function CreateUserAjaxError(xhr) {
    if(xhr.status === 403) {
        toastr.error(xhr.responseJSON.error ? xhr.responseJSON.error.message : xhr.responseJSON.message);
        $('#CreateUserModal').modal('hide');
    }

    if(xhr.status === 404) {
        toastr.error(xhr.responseJSON.error ? xhr.responseJSON.error.message : xhr.responseJSON.message);
        $('#CreateUserModal').modal('hide');
    }

    if(xhr.status === 419) {
        toastr.error(xhr.responseJSON.error ? xhr.responseJSON.error.message : xhr.responseJSON.message);
        $('#CreateUserModal').modal('hide');
    }

    if(xhr.status === 422){
        RemoveIsValidClassCreateUser();
        RemoveIsInvalidClassCreateUser();
        $.each(xhr.responseJSON.errors, function(field, messages) {
            AddIsInvalidClassCreateUser(field);
            $.each(messages, function(index, message) {
                toastr.error(message);
            });
        });
        AddIsValidClassCreateUser();
    }

    if(xhr.status === 500){
        toastr.error(xhr.responseJSON.error ? xhr.responseJSON.error.message : xhr.responseJSON.message);
        $('#CreateUserModal').modal('hide');
    }
}

function AddIsValidClassCreateUser() {
    if (!$('#name_c').hasClass('is-invalid')) {
        $('#name_c').addClass('is-valid');
    }
    if (!$('#last_name_c').hasClass('is-invalid')) {
        $('#last_name_c').addClass('is-valid');
    }
    if (!$('#document_number_c').hasClass('is-invalid')) {
        $('#document_number_c').addClass('is-valid');
    }
    if (!$('#phone_number_c').hasClass('is-invalid')) {
        $('#phone_number_c').addClass('is-valid');
    }
    if (!$('#address_c').hasClass('is-invalid')) {
        $('#address_c').addClass('is-valid');
    }
    if (!$('#email_c').hasClass('is-invalid')) {
        $('#email_c').addClass('is-valid');
    }
    if (!$('#password_c').hasClass('is-invalid')) {
        $('#password_c').addClass('is-valid');
    }
    if (!$('#password_confirmation_c').hasClass('is-invalid')) {
        $('#password_confirmation_c').addClass('is-valid');
    }
    if (!$('#title_c').hasClass('is-invalid')) {
        $('#title_c').addClass('is-valid');
    }
    if (!$('#zone_c').hasClass('is-invalid')) {
        $('#zone_c').addClass('is-valid');
    }
}

function RemoveIsValidClassCreateUser() {
    $('#name_c').removeClass('is-valid');
    $('#last_name_c').removeClass('is-valid');
    $('#document_number_c').removeClass('is-valid');
    $('#phone_number_c').removeClass('is-valid');
    $('#address_c').removeClass('is-valid');
    $('#email_c').removeClass('is-valid');
    $('#password_c').removeClass('is-valid');
    $('#password_confirmation_c').removeClass('is-valid');
    $('#title_c').removeClass('is-valid');
    $('#zone_c').removeClass('is-valid');

}

function AddIsInvalidClassCreateUser(input) {
    if (!$(`#${input}_c`).hasClass('is-valid')) {
        $(`#${input}_c`).addClass('is-invalid');
    }
}

function RemoveIsInvalidClassCreateUser() {
    $('#name_c').removeClass('is-invalid');
    $('#last_name_c').removeClass('is-invalid');
    $('#document_number_c').removeClass('is-invalid');
    $('#phone_number_c').removeClass('is-invalid');
    $('#address_c').removeClass('is-invalid');
    $('#email_c').removeClass('is-invalid');
    $('#password_c').removeClass('is-invalid');
    $('#password_confirmation_c').removeClass('is-invalid');
    $('#title_c').removeClass('is-invalid');
    $('#zone_c').removeClass('is-invalid');
}
