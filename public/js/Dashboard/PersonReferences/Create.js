function CreatePersonReferenceModal() {
    $.ajax({
        url: `/Dashboard/Clients/People/References/Create`,
        type: 'POST',
        data: {
            '_token': $('meta[name="csrf-token"]').attr('content')
        },
        success: function (response) {
            CreatePersonReferenceModalCleaned();
            CreatePersonReferenceModalDocumentType(response.data.documentTypes);
            CreatePersonReferenceModalCountry(response.data.countries);
            CreatePersonReferenceAjaxSuccess(response);
            $('#CreatePersonReferenceModal').modal('show');
        },
        error: function (xhr, textStatus, errorThrown) {
            CreatePersonReferenceAjaxError(xhr);
        }
    });
}

function CreatePersonReferenceModalCleaned() {
    CreatePersonReferenceModalResetSelect('document_type_id_pr_c');
    CreatePersonReferenceModalResetSelect('country_id_pr_c');
    RemoveIsValidClassCreatePersonReference();
    RemoveIsInvalidClassCreatePersonReference();

    $('#name_pr_c').val('');
    $('#last_name_pr_c').val('');
    $('#document_number_pr_c').val('');
    $('#address_pr_c').val('');
    $('#neighborhood_pr_c').val('');
    $('#email_pr_c').val('');
    $('#telephone_number_first_pr_c').val('');
    $('#telephone_number_second_pr_c').val('');
}

function CreatePersonReferenceModalResetSelect(id) {
    $(`#${id}`).html('')
    $(`#${id}`).append(new Option('Seleccione', '', false, false));
    $(`#${id}`).trigger('change');
}

function CreatePersonReferenceModalDocumentType(documentTypes) {
    documentTypes.forEach(documentType => {
        $('#document_type_id_pr_c').append(new Option(documentType.name, documentType.id, false, false));
    });
}

function CreatePersonReferenceModalCountry(countries) {
    countries.forEach(country => {
        $('#country_id_pr_c').append(new Option(country.name, country.id, false, false));
    });
}

function CreatePersonReferenceModalCountryGetDepartament(select) {
    if($(select).val() == '') {
        CreatePersonReferenceModalResetSelect('departament_id_pr_c');
    } else {
        $.ajax({
            url: `/Dashboard/Clients/People/References/Create`,
            type: 'POST',
            data: {
                '_token': $('meta[name="csrf-token"]').attr('content'),
                'country_id':  $(select).val()
            },
            success: function(response) {
                CreatePersonReferenceModalResetSelect('departament_id_pr_c');
                CreatePersonReferenceModalDepartament(response.data);
            },
            error: function(xhr, textStatus, errorThrown) {
                CreatePersonReferenceAjaxError(xhr);
            }
        });
    }
}

function CreatePersonReferenceModalDepartament(departaments) {
    departaments.forEach(departament => {
        $('#departament_id_pr_c').append(new Option(departament.name, departament.id, false, false));
    });
}

function CreatePersonReferenceModalDepartamentGetCity(select) {
    if($(select).val() == '') {
        CreatePersonReferenceModalResetSelect('city_id_pr_c');
    } else {
        $.ajax({
            url: `/Dashboard/Clients/People/References/Create`,
            type: 'POST',
            data: {
                '_token': $('meta[name="csrf-token"]').attr('content'),
                'departament_id':  $(select).val()
            },
            success: function(response) {
                CreatePersonReferenceModalResetSelect('city_id_pr_c');
                CreatePersonReferenceModalCity(response.data);
            },
            error: function(xhr, textStatus, errorThrown) {
                CreatePersonReferenceAjaxError(xhr);
            }
        });
    }
};

function CreatePersonReferenceModalCity(cities) {
    cities.forEach(city => {
        $('#city_id_pr_c').append(new Option(city.name, city.id, false, false));
    });
}

function CreatePersonReference() {
    Swal.fire({
        title: '¿Desea guardar el representante legal?',
        text: 'El representante legal será creado.',
        icon: 'warning',
        showCancelButton: true,
        cancelButtonColor: '#DD6B55',
        confirmButtonColor: '#3085d6',
        confirmButtonText: 'Si, guardar!',
        cancelButtonText: 'No, cancelar!',
    }).then((result) => {
        if (result.value) {
            $.ajax({
                url: `/Dashboard/Clients/People/References/Store`,
                type: 'POST',
                data: {
                    '_token': $('meta[name="csrf-token"]').attr('content'),
                    'person_id': $('#IndexPersonReferenceButton').attr('data-person_id'),
                    'name': $('#name_pr_c').val(),
                    'last_name': $('#last_name_pr_c').val(),
                    'document_type_id': $('#document_type_id_pr_c').val(),
                    'document_number': $('#document_number_pr_c').val(),
                    'country_id': $('#country_id_pr_c').val(),
                    'departament_id': $('#departament_id_pr_c').val(),
                    'city_id': $('#city_id_pr_c').val(),
                    'address': $('#address_pr_c').val(),
                    'neighborhood': $('#neighborhood_pr_c').val(),
                    'email': $('#email_pr_c').val(),
                    'telephone_number_first': $('#telephone_number_first_pr_c').val(),
                    'telephone_number_second': $('#telephone_number_second_pr_c').val(),
                },
                success: function (response) {
                    tablePersonReferences.ajax.reload();
                    CreatePersonReferenceAjaxSuccess(response);
                },
                error: function (xhr, textStatus, errorThrown) {
                    CreatePersonReferenceAjaxError(xhr);
                }
            });
        } else {
            toastr.info('El representante legal no fue creado.')
        }
    });
}

function CreatePersonReferenceAjaxSuccess(response) {
    if (response.status === 204) {
        toastr.info(response.message);
        $('#CreatePersonReferenceModal').modal('hide');
    }

    if (response.status === 201) {
        toastr.success(response.message);
        $('#CreatePersonReferenceModal').modal('hide');
    }
}

function CreatePersonReferenceAjaxError(xhr) {
    if (xhr.status === 403) {
        toastr.error(xhr.responseJSON.error ? xhr.responseJSON.error.message : xhr.responseJSON.message);
        $('#CreatePersonReferenceModal').modal('hide');
    }

    if (xhr.status === 404) {
        toastr.error(xhr.responseJSON.error ? xhr.responseJSON.error.message : xhr.responseJSON.message);
        $('#CreatePersonReferenceModal').modal('hide');
    }

    if (xhr.status === 419) {
        toastr.error(xhr.responseJSON.error ? xhr.responseJSON.error.message : xhr.responseJSON.message);
        $('#CreatePersonReferenceModal').modal('hide');
    }

    if (xhr.status === 422) {
        RemoveIsValidClassCreatePersonReference();
        RemoveIsInvalidClassCreatePersonReference();
        $.each(xhr.responseJSON.errors, function (field, messages) {
            AddIsInvalidClassCreatePersonReference(field);
            $.each(messages, function (index, message) {
                toastr.error(message);
            });
        });
        AddIsValidClassCreatePersonReference();
    }

    if (xhr.status === 500) {
        toastr.error(xhr.responseJSON.error ? xhr.responseJSON.error.message : xhr.responseJSON.message);
        $('#CreatePersonReferenceModal').modal('hide');
    }
}

function AddIsValidClassCreatePersonReference() {
    if (!$('#name_pr_c').hasClass('is-invalid')) {
        $('#name_pr_c').addClass('is-valid');
    }
    if (!$('#last_name_pr_c').hasClass('is-invalid')) {
        $('#last_name_pr_c').addClass('is-valid');
    }
    if (!$('#document_number_pr_c').hasClass('is-invalid')) {
        $('#document_number_pr_c').addClass('is-valid');
    }
    if (!$('#address_pr_c').hasClass('is-invalid')) {
        $('#address_pr_c').addClass('is-valid');
    }
    if (!$('#neighborhood_pr_c').hasClass('is-invalid')) {
        $('#neighborhood_pr_c').addClass('is-valid');
    }
    if (!$('#email_pr_c').hasClass('is-invalid')) {
        $('#email_pr_c').addClass('is-valid');
    }
    if (!$('#telephone_number_first_pr_c').hasClass('is-invalid')) {
        $('#telephone_number_first_pr_c').addClass('is-valid');
    }
    if (!$('#telephone_number_second_pr_c').hasClass('is-invalid')) {
        $('#telephone_number_second_pr_c').addClass('is-valid');
    }
    if (!$('span[aria-labelledby="select2-document_type_id_pr_c-container"]').hasClass('is-invalid')) {
        $('span[aria-labelledby="select2-document_type_id_pr_c-container"]').addClass('is-valid');
    }
    if (!$('span[aria-labelledby="select2-country_id_pr_c-container"]').hasClass('is-invalid')) {
        $('span[aria-labelledby="select2-country_id_pr_c-container"]').addClass('is-valid');
    }
    if (!$('span[aria-labelledby="select2-departament_id_pr_c-container"]').hasClass('is-invalid')) {
        $('span[aria-labelledby="select2-departament_id_pr_c-container"]').addClass('is-valid');
    }
    if (!$('span[aria-labelledby="select2-city_id_pr_c-container"]').hasClass('is-invalid')) {
        $('span[aria-labelledby="select2-city_id_pr_c-container"]').addClass('is-valid');
    }
}

function RemoveIsValidClassCreatePersonReference() {
    $('#name_pr_c').removeClass('is-valid');
    $('#last_name_pr_c').removeClass('is-valid');
    $('#document_number_pr_c').removeClass('is-valid');
    $('#address_pr_c').removeClass('is-valid');
    $('#neighborhood_pr_c').removeClass('is-valid');
    $('#email_pr_c').removeClass('is-valid');
    $('#telephone_number_first_pr_c').removeClass('is-valid');
    $('#telephone_number_second_pr_c').removeClass('is-valid');
    $('span[aria-labelledby="select2-document_type_id_pr_c-container"]').removeClass('is-valid');
    $('span[aria-labelledby="select2-country_id_pr_c-container"]').removeClass('is-valid');
    $('span[aria-labelledby="select2-departament_id_pr_c-container"]').removeClass('is-valid');
    $('span[aria-labelledby="select2-city_id_pr_c-container"]').removeClass('is-valid');
}

function AddIsInvalidClassCreatePersonReference(input) {
    if (!$(`#${input}_pr_c`).hasClass('is-valid')) {
        $(`#${input}_pr_c`).addClass('is-invalid');
    }
    if (!$(`span[aria-labelledby="select2-${input}_pr_c-container`).hasClass('is-valid')) {
        $(`span[aria-labelledby="select2-${input}_pr_c-container"]`).addClass('is-invalid');
    }
}

function RemoveIsInvalidClassCreatePersonReference() {
    $('#name_pr_c').removeClass('is-invalid');
    $('#last_name_pr_c').removeClass('is-invalid');
    $('#document_number_pr_c').removeClass('is-invalid');
    $('#address_pr_c').removeClass('is-invalid');
    $('#neighborhood_pr_c').removeClass('is-invalid');
    $('#email_pr_c').removeClass('is-invalid');
    $('#telephone_number_first_pr_c').removeClass('is-invalid');
    $('#telephone_number_second_pr_c').removeClass('is-invalid');
    $('span[aria-labelledby="select2-document_type_id_pr_c-container"]').removeClass('is-invalid');
    $('span[aria-labelledby="select2-country_id_pr_c-container"]').removeClass('is-invalid');
    $('span[aria-labelledby="select2-departament_id_pr_c-container"]').removeClass('is-invalid');
    $('span[aria-labelledby="select2-city_id_pr_c-container"]').removeClass('is-invalid');
}
