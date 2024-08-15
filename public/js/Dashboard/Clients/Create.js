function CreateClientModal() {
    $.ajax({
        url: `/Dashboard/Clients/Create`,
        type: 'POST',
        data: {
            '_token': $('meta[name="csrf-token"]').attr('content')
        },
        success: function (response) {
            CreateClientModalCleaned();
            CreateClientModalCountry(response.data.countries);
            CreateClientAjaxSuccess(response);
            $('#CreateClientModal').modal('show');
        },
        error: function (xhr, textStatus, errorThrown) {
            CreateClientAjaxError(xhr);
        }
    });
}

function CreateClientModalCleaned() {
    CreateClientModalResetSelect('country_c');
    RemoveIsValidClassCreateClient();
    RemoveIsInvalidClassCreateClient();

    $('#client_name_c').val('');
    $('#client_address_c').val('');
    $('#client_number_document_c').val('');
    $('#client_number_phone_c').val('');
    $('#client_branch_code_c').val('');
    $('#client_branch_name_c').val('');
    $('#client_branch_address_c').val('');
    $('#client_branch_number_phone_c').val('');
    $('#number_phone_c').val('');
    $('#email_c').val('');
    $('#zone_c').val('');
}

function CreateClientModalResetSelect(id) {
    $(`#${id}`).html('')
    $(`#${id}`).append(new Option('Seleccione', '', false, false));
    $(`#${id}`).trigger('change');
}

function CreateClientModalCountry(countries) {
    countries.forEach(country => {
        $('#country_c').append(new Option(country.name, country.name, false, false));
    });
}

function CreateClientModalCountryGetDepartament(select) {
    if($(select).val() == '') {
        CreateClientModalResetSelect('departament_c');
    } else {
        $.ajax({
            url: `/Dashboard/Clients/Create`,
            type: 'POST',
            data: {
                '_token': $('meta[name="csrf-token"]').attr('content'),
                'country':  $(select).val()
            },
            success: function(response) {
                CreateClientModalResetSelect('departament_c');
                CreateClientModalDepartament(response.data.departaments);
            },
            error: function(xhr, textStatus, errorThrown) {
                CreateClientAjaxError(xhr);
            }
        });
    }
};

function CreateClientModalDepartament(departaments) {
    departaments.forEach(departament => {
        $('#departament_c').append(new Option(departament.name, departament.name, false, false));
    });
}

function CreateClientModalDepartamentGetCity(select) {
    if($(select).val() == '') {
        CreateClientModalResetSelect('city_c');
    } else {
        $.ajax({
            url: `/Dashboard/Clients/Create`,
            type: 'POST',
            data: {
                '_token': $('meta[name="csrf-token"]').attr('content'),
                'departament':  $(select).val()
            },
            success: function(response) {
                CreateClientModalResetSelect('city_c');
                CreateClientModalCity(response.data.cities);
            },
            error: function(xhr, textStatus, errorThrown) {
                CreateClientAjaxError(xhr);
            }
        });
    }
};

function CreateClientModalCity(cities) {
    cities.forEach(city => {
        $('#city_c').append(new Option(city.name, city.name, false, false));
    });
}

function CreateClient() {
    Swal.fire({
        title: '¿Desea guardar el cliente?',
        text: 'El cliente será creado.',
        icon: 'warning',
        showCancelButton: true,
        cancelButtonColor: '#DD6B55',
        confirmButtonColor: '#3085d6',
        confirmButtonText: 'Si, guardar!',
        cancelButtonText: 'No, cancelar!',
    }).then((result) => {
        if (result.value) {
            $.ajax({
                url: `/Dashboard/Clients/Store`,
                type: 'POST',
                data: {
                    '_token': $('meta[name="csrf-token"]').attr('content'),
                    'client_name': $('#client_name_c').val(),
                    'client_address': $('#client_address_c').val(),
                    'client_number_document': $('#client_number_document_c').val(),
                    'client_number_phone': $('#client_number_phone_c').val(),
                    'client_branch_code': $('#client_branch_code_c').val(),
                    'client_branch_name': $('#client_branch_name_c').val(),
                    'client_branch_address': $('#client_branch_address_c').val(),
                    'client_branch_number_phone': $('#client_branch_number_phone_c').val(),
                    'country': $('#country_c').val(),
                    'departament': $('#departament_c').val(),
                    'city': $('#city_c').val(),
                    'number_phone': $('#number_phone_c').val(),
                    'email': $('#email_c').val(),
                    'zone': $('#zone_c').val(),
                },
                success: function (response) {
                    tableClients.ajax.reload();
                    CreateClientAjaxSuccess(response);
                },
                error: function (xhr, textStatus, errorThrown) {
                    CreateClientAjaxError(xhr);
                }
            });
        } else {
            toastr.info('El cliente no fue creado.')
        }
    });
}

function CreateClientAjaxSuccess(response) {
    if (response.status === 204) {
        toastr.info(response.message);
        $('#CreateClientModal').modal('hide');
    }

    if (response.status === 201) {
        toastr.success(response.message);
        $('#CreateClientModal').modal('hide');
    }
}

function CreateClientAjaxError(xhr) {
    if (xhr.status === 403) {
        toastr.error(xhr.responseJSON.error ? xhr.responseJSON.error.message : xhr.responseJSON.message);
        $('#CreateClientModal').modal('hide');
    }

    if (xhr.status === 404) {
        toastr.error(xhr.responseJSON.error ? xhr.responseJSON.error.message : xhr.responseJSON.message);
        $('#CreateClientModal').modal('hide');
    }

    if (xhr.status === 419) {
        toastr.error(xhr.responseJSON.error ? xhr.responseJSON.error.message : xhr.responseJSON.message);
        $('#CreateClientModal').modal('hide');
    }

    if (xhr.status === 422) {
        RemoveIsValidClassCreateClient();
        RemoveIsInvalidClassCreateClient();
        $.each(xhr.responseJSON.errors, function (field, messages) {
            AddIsInvalidClassCreateClient(field);
            $.each(messages, function (index, message) {
                toastr.error(message);
            });
        });
        AddIsValidClassCreateClient();
    }

    if (xhr.status === 500) {
        toastr.error(xhr.responseJSON.error ? xhr.responseJSON.error.message : xhr.responseJSON.message);
        $('#CreateClientModal').modal('hide');
    }
}

function AddIsValidClassCreateClient() {
    if (!$('#client_name_c').hasClass('is-invalid')) {
        $('#client_name_c').addClass('is-valid');
    }
    if (!$('#client_address_c').hasClass('is-invalid')) {
        $('#client_address_c').addClass('is-valid');
    }
    if (!$('#client_number_document_c').hasClass('is-invalid')) {
        $('#client_number_document_c').addClass('is-valid');
    }
    if (!$('#client_number_phone_c').hasClass('is-invalid')) {
        $('#client_number_phone_c').addClass('is-valid');
    }
    if (!$('#client_branch_code_c').hasClass('is-invalid')) {
        $('#client_branch_code_c').addClass('is-valid');
    }
    if (!$('#client_branch_name_c').hasClass('is-invalid')) {
        $('#client_branch_name_c').addClass('is-valid');
    }
    if (!$('#client_branch_address_c').hasClass('is-invalid')) {
        $('#client_branch_address_c').addClass('is-valid');
    }
    if (!$('#client_branch_number_phone_c').hasClass('is-invalid')) {
        $('#client_branch_number_phone_c').addClass('is-valid');
    }
    if (!$('span[aria-labelledby="select2-country_c-container"]').hasClass('is-invalid')) {
        $('span[aria-labelledby="select2-country_c-container"]').addClass('is-valid');
    }
    if (!$('span[aria-labelledby="select2-departament_c-container"]').hasClass('is-invalid')) {
        $('span[aria-labelledby="select2-departament_c-container"]').addClass('is-valid');
    }
    if (!$('span[aria-labelledby="select2-city_c-container"]').hasClass('is-invalid')) {
        $('span[aria-labelledby="select2-city_c-container"]').addClass('is-valid');
    }
    if (!$('#number_phone_c').hasClass('is-invalid')) {
        $('#number_phone_c').addClass('is-valid');
    }
    if (!$('#email_c').hasClass('is-invalid')) {
        $('#email_c').addClass('is-valid');
    }
    if (!$('#zone_c').hasClass('is-invalid')) {
        $('#zone_c').addClass('is-valid');
    }
}

function RemoveIsValidClassCreateClient() {
    $('#client_name_c').removeClass('is-valid');
    $('#client_address_c').removeClass('is-valid');
    $('#client_number_document_c').removeClass('is-valid');
    $('#client_number_phone_c').removeClass('is-valid');
    $('#client_branch_code_c').removeClass('is-valid');
    $('#client_branch_name_c').removeClass('is-valid');
    $('#client_branch_address_c').removeClass('is-valid');
    $('#client_branch_number_phone_c').removeClass('is-valid');
    $('span[aria-labelledby="select2-country_c-container"]').removeClass('is-valid');
    $('span[aria-labelledby="select2-departament_c-container"]').removeClass('is-valid');
    $('span[aria-labelledby="select2-city_c-container"]').removeClass('is-valid');
    $('#number_phone_c').removeClass('is-valid');
    $('#email_c').removeClass('is-valid');
    $('#zone_c').removeClass('is-valid');
}

function AddIsInvalidClassCreateClient(input) {
    if (!$(`#${input}_c`).hasClass('is-valid')) {
        $(`#${input}_c`).addClass('is-invalid');
    }
    if (!$(`span[aria-labelledby="select2-${input}_c-container`).hasClass('is-valid')) {
        $(`span[aria-labelledby="select2-${input}_c-container"]`).addClass('is-invalid');
    }
}

function RemoveIsInvalidClassCreateClient() {
    $('#client_name_c').removeClass('is-invalid');
    $('#client_address_c').removeClass('is-invalid');
    $('#client_number_document_c').removeClass('is-invalid');
    $('#client_number_phone_c').removeClass('is-invalid');
    $('#client_branch_code_c').removeClass('is-invalid');
    $('#client_branch_name_c').removeClass('is-invalid');
    $('#client_branch_address_c').removeClass('is-invalid');
    $('#client_branch_number_phone_c').removeClass('is-invalid');
    $('span[aria-labelledby="select2-country_c-container"]').removeClass('is-invalid');
    $('span[aria-labelledby="select2-departament_c-container"]').removeClass('is-invalid');
    $('span[aria-labelledby="select2-city_c-container"]').removeClass('is-invalid');
    $('#number_phone_c').removeClass('is-invalid');
    $('#email_c').removeClass('is-invalid');
    $('#zone_c').removeClass('is-invalid');
}
