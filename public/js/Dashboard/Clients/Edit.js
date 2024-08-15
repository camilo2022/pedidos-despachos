function EditClientModal(id) {
    $.ajax({
        url: `/Dashboard/Clients/Edit/${id}`,
        type: 'POST',
        data: {
            '_token': $('meta[name="csrf-token"]').attr('content')
        },
        success: function (response) {
            EditClientModalCleaned(response.data.client);
            EditClientModalCountry(response.data.countries);
            EditClientAjaxSuccess(response);
            $('#EditClientModal').modal('show');
        },
        error: function (xhr, textStatus, errorThrown) {
            EditClientAjaxError(xhr);
        }
    });
}

function EditClientModalCleaned(client) {
    EditClientModalResetSelect('country_e');
    RemoveIsValidClassEditClient();
    RemoveIsInvalidClassEditClient();

    $('#EditClientButton').attr('onclick', `EditClient(${client.id})`);
    $('#EditClientButton').attr('data-id', client.id);
    $('#EditClientButton').attr('data-country', client.country);
    $('#EditClientButton').attr('data-departament', client.departament);
    $('#EditClientButton').attr('data-city', client.city);
    
    $('#client_name_e').val(client.client_name);
    $('#client_address_e').val(client.client_address);
    $('#client_number_document_e').val(client.client_number_document);
    $('#client_number_phone_e').val(client.client_number_phone);
    $('#client_branch_code_e').val(client.client_branch_code);
    $('#client_branch_name_e').val(client.client_branch_name);
    $('#client_branch_address_e').val(client.client_branch_address);
    $('#client_branch_number_phone_e').val(client.client_branch_number_phone);
    $('#number_phone_e').val(client.number_phone);
    $('#email_e').val(client.email);
    $('#zone_e').val(client.zone);
}

function EditClientModalResetSelect(id) {
    $(`#${id}`).html('')
    $(`#${id}`).append(new Option('Seleccione', '', false, false));
    $(`#${id}`).trigger('change');
}

function EditClientModalCountry(countries) {
    countries.forEach(country => {
        $('#country_e').append(new Option(country.name, country.name, false, false));
    });

    let country = $('#EditClientButton').attr('data-country');
    if(country != '') {
        $("#country_e").val(country).trigger('change');
        $('#EditClientButton').attr('data-country', '');
    }
}

function EditClientModalCountryGetDepartament(select) {
    if($(select).val() == '') {
        EditClientModalResetSelect('departament_e');
    } else {
        let id = $('#EditClientButton').attr('data-id');
        $.ajax({
            url: `/Dashboard/Clients/Edit/${id}`,
            type: 'POST',
            data: {
                '_token': $('meta[name="csrf-token"]').attr('content'),
                'country':  $(select).val()
            },
            success: function(response) {
                EditClientModalResetSelect('departament_e');
                EditClientModalDepartament(response.data.departaments);
            },
            error: function(xhr, textStatus, errorThrown) {
                EditClientAjaxError(xhr);
            }
        });
    }
};

function EditClientModalDepartament(departaments) {
    departaments.forEach(departament => {
        $('#departament_e').append(new Option(departament.name, departament.name, false, false));
    });

    let departament = $('#EditClientButton').attr('data-departament');
    if(departament != '') {
        $("#departament_e").val(departament).trigger('change');
        $('#EditClientButton').attr('data-departament', '');
    }
}

function EditClientModalDepartamentGetCity(select) {
    if($(select).val() == '') {
        EditClientModalResetSelect('city_e');
    } else {
        let id = $('#EditClientButton').attr('data-id');
        $.ajax({
            url: `/Dashboard/Clients/Edit/${id}`,
            type: 'POST',
            data: {
                '_token': $('meta[name="csrf-token"]').attr('content'),
                'departament':  $(select).val()
            },
            success: function(response) {
                EditClientModalResetSelect('city_e');
                EditClientModalCity(response.data.cities);
            },
            error: function(xhr, textStatus, errorThrown) {
                EditClientAjaxError(xhr);
            }
        });
    }
};

function EditClientModalCity(cities) {
    cities.forEach(city => {
        $('#city_e').append(new Option(city.name, city.name, false, false));
    });

    let city = $('#EditClientButton').attr('data-city');
    if(city != '') {
        $("#city_e").val(city).trigger('change');
        $('#EditClientButton').attr('data-city', '');
    }
}

function EditClient(id) {
    Swal.fire({
        title: '¿Desea actualizar el cliente?',
        text: 'El cliente se actualizara.',
        icon: 'warning',
        showCancelButton: true,
        cancelButtonColor: '#DD6B55',
        confirmButtonColor: '#3085d6',
        confirmButtonText: 'Si, actualizar!',
        cancelButtonText: 'No, cancelar!',
    }).then((result) => {
        if (result.value) {
            $.ajax({
                url: `/Dashboard/Clients/Update/${id}`,
                type: 'PUT',
                data: {
                    '_token': $('meta[name="csrf-token"]').attr('content'),
                    'client_name': $('#client_name_e').val(),
                    'client_address': $('#client_address_e').val(),
                    'client_number_document': $('#client_number_document_e').val(),
                    'client_number_phone': $('#client_number_phone_e').val(),
                    'client_branch_code': $('#client_branch_code_e').val(),
                    'client_branch_name': $('#client_branch_name_e').val(),
                    'client_branch_address': $('#client_branch_address_e').val(),
                    'client_branch_number_phone': $('#client_branch_number_phone_e').val(),
                    'country': $('#country_e').val(),
                    'departament': $('#departament_e').val(),
                    'city': $('#city_e').val(),
                    'number_phone': $('#number_phone_e').val(),
                    'email': $('#email_e').val(),
                    'zone': $('#zone_e').val(),
                },
                success: function (response) {
                    tableClients.ajax.reload();
                    EditClientAjaxSuccess(response);
                },
                error: function (xhr, textStatus, errorThrown) {
                    EditClientAjaxError(xhr);
                }
            });
        } else {
            toastr.info('El cliente no fue actualizada.')
        }
    });
}

function EditClientAjaxSuccess(response) {
    if (response.status === 204) {
        toastr.info(response.message);
        $('#EditClientModal').modal('hide');
    }

    if (response.status === 200) {
        toastr.success(response.message);
        $('#EditClientModal').modal('hide');
    }
}

function EditClientAjaxError(xhr) {
    if (xhr.status === 403) {
        toastr.error(xhr.responseJSON.error ? xhr.responseJSON.error.message : xhr.responseJSON.message);
        $('#EditClientModal').modal('hide');
    }

    if (xhr.status === 404) {
        toastr.error(xhr.responseJSON.error ? xhr.responseJSON.error.message : xhr.responseJSON.message);
        $('#EditClientModal').modal('hide');
    }

    if (xhr.status === 419) {
        toastr.error(xhr.responseJSON.error ? xhr.responseJSON.error.message : xhr.responseJSON.message);
        $('#EditClientModal').modal('hide');
    }

    if (xhr.status === 422) {
        RemoveIsValidClassEditClient();
        RemoveIsInvalidClassEditClient();
        $.each(xhr.responseJSON.errors, function (field, messages) {
            AddIsInvalidClassEditClient(field);
            $.each(messages, function (index, message) {
                toastr.error(message);
            });
        });
        AddIsValidClassEditClient();
    }

    if (xhr.status === 500) {
        toastr.error(xhr.responseJSON.error ? xhr.responseJSON.error.message : xhr.responseJSON.message);
        $('#EditClientModal').modal('hide');
    }
}

function AddIsValidClassEditClient() {
    if (!$('#client_name_e').hasClass('is-invalid')) {
        $('#client_name_e').addClass('is-valid');
    }
    if (!$('#client_address_e').hasClass('is-invalid')) {
        $('#client_address_e').addClass('is-valid');
    }
    if (!$('#client_number_document_e').hasClass('is-invalid')) {
        $('#client_number_document_e').addClass('is-valid');
    }
    if (!$('#client_number_phone_e').hasClass('is-invalid')) {
        $('#client_number_phone_e').addClass('is-valid');
    }
    if (!$('#client_branch_code_e').hasClass('is-invalid')) {
        $('#client_branch_code_e').addClass('is-valid');
    }
    if (!$('#client_branch_name_e').hasClass('is-invalid')) {
        $('#client_branch_name_e').addClass('is-valid');
    }
    if (!$('#client_branch_address_e').hasClass('is-invalid')) {
        $('#client_branch_address_e').addClass('is-valid');
    }
    if (!$('#client_branch_number_phone_e').hasClass('is-invalid')) {
        $('#client_branch_number_phone_e').addClass('is-valid');
    }
    if (!$('span[aria-labelledby="select2-country_e-container"]').hasClass('is-invalid')) {
        $('span[aria-labelledby="select2-country_e-container"]').addClass('is-valid');
    }
    if (!$('span[aria-labelledby="select2-departament_e-container"]').hasClass('is-invalid')) {
        $('span[aria-labelledby="select2-departament_e-container"]').addClass('is-valid');
    }
    if (!$('span[aria-labelledby="select2-city_e-container"]').hasClass('is-invalid')) {
        $('span[aria-labelledby="select2-city_e-container"]').addClass('is-valid');
    }
    if (!$('#number_phone_e').hasClass('is-invalid')) {
        $('#number_phone_e').addClass('is-valid');
    }
    if (!$('#email_e').hasClass('is-invalid')) {
        $('#email_e').addClass('is-valid');
    }
    if (!$('#zone_e').hasClass('is-invalid')) {
        $('#zone_e').addClass('is-valid');
    }
}

function RemoveIsValidClassEditClient() {
    $('#client_name_e').removeClass('is-valid');
    $('#client_address_e').removeClass('is-valid');
    $('#client_number_document_e').removeClass('is-valid');
    $('#client_number_phone_e').removeClass('is-valid');
    $('#client_branch_code_e').removeClass('is-valid');
    $('#client_branch_name_e').removeClass('is-valid');
    $('#client_branch_address_e').removeClass('is-valid');
    $('#client_branch_number_phone_e').removeClass('is-valid');
    $('span[aria-labelledby="select2-country_e-container"]').removeClass('is-valid');
    $('span[aria-labelledby="select2-departament_e-container"]').removeClass('is-valid');
    $('span[aria-labelledby="select2-city_e-container"]').removeClass('is-valid');
    $('#number_phone_e').removeClass('is-valid');
    $('#email_e').removeClass('is-valid');
    $('#zone_e').removeClass('is-valid');
}

function AddIsInvalidClassEditClient(input) {
    if (!$(`#${input}_e`).hasClass('is-valid')) {
        $(`#${input}_e`).addClass('is-invalid');
    }
    if (!$(`span[aria-labelledby="select2-${input}_e-container`).hasClass('is-valid')) {
        $(`span[aria-labelledby="select2-${input}_e-container"]`).addClass('is-invalid');
    }
}

function RemoveIsInvalidClassEditClient() {
    $('#client_name_e').removeClass('is-invalid');
    $('#client_address_e').removeClass('is-invalid');
    $('#client_number_document_e').removeClass('is-invalid');
    $('#client_number_phone_e').removeClass('is-invalid');
    $('#client_branch_code_e').removeClass('is-invalid');
    $('#client_branch_name_e').removeClass('is-invalid');
    $('#client_branch_address_e').removeClass('is-invalid');
    $('#client_branch_number_phone_e').removeClass('is-invalid');
    $('span[aria-labelledby="select2-country_e-container"]').removeClass('is-invalid');
    $('span[aria-labelledby="select2-departament_e-container"]').removeClass('is-invalid');
    $('span[aria-labelledby="select2-city_e-container"]').removeClass('is-invalid');
    $('#number_phone_e').removeClass('is-invalid');
    $('#email_e').removeClass('is-invalid');
    $('#zone_e').removeClass('is-invalid');
}
