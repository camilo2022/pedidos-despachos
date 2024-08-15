function EditPersonReferenceModal(id) {
    $.ajax({
        url: `/Dashboard/Clients/People/References/Edit/${id}`,
        type: 'POST',
        data: {
            '_token': $('meta[name="csrf-token"]').attr('content')
        },
        success: function (response) {
            tablePersonReferences.ajax.reload();
            EditPersonReferenceModalCleaned(response.data.personReference);
            EditPersonReferenceModalDocumentType(response.data.documentTypes);
            EditPersonReferenceModalCountry(response.data.countries);
            EditPersonReferenceAjaxSuccess(response);
            $('#EditPersonReferenceModal').modal('show');
        },
        error: function (xhr, textStatus, errorThrown) {
            tablePersonReferences.ajax.reload();
            EditPersonReferenceAjaxError(xhr);
        }
    });
}

function EditPersonReferenceModalCleaned(personReference) {
    EditPersonReferenceModalResetSelect('document_type_id_pr_e');
    EditPersonReferenceModalResetSelect('country_id_pr_e');
    RemoveIsValidClassEditPersonReference();
    RemoveIsInvalidClassEditPersonReference();

    $('#EditPersonReferenceButton').attr('onclick', `EditPersonReference(${personReference.id})`);
    $('#EditPersonReferenceButton').attr('data-id', personReference.id);
    $('#EditPersonReferenceButton').attr('data-document_type_id', personReference.document_type_id);
    $('#EditPersonReferenceButton').attr('data-country_id', personReference.country_id);
    $('#EditPersonReferenceButton').attr('data-departament_id', personReference.departament_id);
    $('#EditPersonReferenceButton').attr('data-city_id', personReference.city_id);

    $('#name_pr_e').val(personReference.name);
    $('#last_name_pr_e').val(personReference.last_name);
    $('#document_number_pr_e').val(personReference.document_number);
    $('#address_pr_e').val(personReference.address);
    $('#neighborhood_pr_e').val(personReference.neighborhood);
    $('#email_pr_e').val(personReference.email);
    $('#telephone_number_first_pr_e').val(personReference.telephone_number_first);
    $('#telephone_number_second_pr_e').val(personReference.telephone_number_second);
}

function EditPersonReferenceModalResetSelect(id) {
    $(`#${id}`).html('')
    $(`#${id}`).append(new Option('Seleccione', '', false, false));
    $(`#${id}`).trigger('change');
}

function EditPersonReferenceModalDocumentType(documentTypes) {
    documentTypes.forEach(documentType => {
        $('#document_type_id_pr_e').append(new Option(documentType.name, documentType.id, false, false));
    });

    let document_type_id = $('#EditPersonReferenceButton').attr('data-document_type_id');
    if(document_type_id != '') {
        $("#document_type_id_pr_e").val(document_type_id).trigger('change');
        $('#EditPersonReferenceButton').attr('data-document_type_id', '');
    }
}

function EditPersonReferenceModalCountry(countries) {
    countries.forEach(country => {
        $('#country_id_pr_e').append(new Option(country.name, country.id, false, false));
    });

    let country_id = $('#EditPersonReferenceButton').attr('data-country_id');
    if(country_id != '') {
        $("#country_id_pr_e").val(country_id).trigger('change');
        $('#EditPersonReferenceButton').attr('data-country_id', '');
    }
}

function EditPersonReferenceModalCountryGetDepartament(select) {
    if($(select).val() == '') {
        EditPersonReferenceModalResetSelect('departament_id_pr_e');
    } else {
        let id = $('#EditPersonReferenceButton').attr('data-id');
        $.ajax({
            url: `/Dashboard/Clients/People/References/Edit/${id}`,
            type: 'POST',
            data: {
                '_token': $('meta[name="csrf-token"]').attr('content'),
                'country_id':  $(select).val()
            },
            success: function(response) {
                EditPersonReferenceModalResetSelect('departament_id_pr_e');
                EditPersonReferenceModalDepartament(response.data);
            },
            error: function(xhr, textStatus, errorThrown) {
                EditPersonReferenceAjaxError(xhr);
            }
        });
    }
}

function EditPersonReferenceModalDepartament(departaments) {
    departaments.forEach(departament => {
        $('#departament_id_pr_e').append(new Option(departament.name, departament.id, false, false));
    });

    let departament_id = $('#EditPersonReferenceButton').attr('data-departament_id');
    if(departament_id != '') {
        $("#departament_id_pr_e").val(departament_id).trigger('change');
        $('#EditPersonReferenceButton').attr('data-departament_id', '');
    }
}

function EditPersonReferenceModalDepartamentGetCity(select) {
    if($(select).val() == '') {
        EditPersonReferenceModalResetSelect('city_id_pr_e');
    } else {
        let id = $('#EditPersonReferenceButton').attr('data-id');
        $.ajax({
            url: `/Dashboard/Clients/People/References/Edit/${id}`,
            type: 'POST',
            data: {
                '_token': $('meta[name="csrf-token"]').attr('content'),
                'departament_id':  $(select).val()
            },
            success: function(response) {
                EditPersonReferenceModalResetSelect('city_id_pr_e');
                EditPersonReferenceModalCity(response.data);
            },
            error: function(xhr, textStatus, errorThrown) {
                EditPersonReferenceAjaxError(xhr);
            }
        });
    }
};

function EditPersonReferenceModalCity(cities) {
    cities.forEach(city => {
        $('#city_id_pr_e').append(new Option(city.name, city.id, false, false));
    });

    let city_id = $('#EditPersonReferenceButton').attr('data-city_id');
    if(city_id != '') {
        $("#city_id_pr_e").val(city_id).trigger('change');
        $('#EditPersonReferenceButton').attr('data-city_id', '');
    }
}

function EditPersonReference(id) {
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
                url: `/Dashboard/Clients/People/References/Update/${id}`,
                type: 'PUT',
                data: {
                    '_token': $('meta[name="csrf-token"]').attr('content'),
                    'person_id': $('#IndexPersonReferenceButton').attr('data-person_id'),
                    'name': $('#name_pr_e').val(),
                    'last_name': $('#last_name_pr_e').val(),
                    'document_type_id': $('#document_type_id_pr_e').val(),
                    'document_number': $('#document_number_pr_e').val(),
                    'country_id': $('#country_id_pr_e').val(),
                    'departament_id': $('#departament_id_pr_e').val(),
                    'city_id': $('#city_id_pr_e').val(),
                    'address': $('#address_pr_e').val(),
                    'neighborhood': $('#neighborhood_pr_e').val(),
                    'email': $('#email_pr_e').val(),
                    'telephone_number_first': $('#telephone_number_first_pr_e').val(),
                    'telephone_number_second': $('#telephone_number_second_pr_e').val(),
                },
                success: function (response) {
                    tablePersonReferences.ajax.reload();
                    EditPersonReferenceAjaxSuccess(response);
                },
                error: function (xhr, textStatus, errorThrown) {
                    tablePersonReferences.ajax.reload();
                    EditPersonReferenceAjaxError(xhr);
                }
            });
        } else {
            toastr.info('El cliente no fue actualizada.')
        }
    });
}

function EditPersonReferenceAjaxSuccess(response) {
    if (response.status === 204) {
        toastr.info(response.message);
        $('#EditPersonReferenceModal').modal('hide');
    }

    if (response.status === 200) {
        toastr.success(response.message);
        $('#EditPersonReferenceModal').modal('hide');
    }
}

function EditPersonReferenceAjaxError(xhr) {
    if (xhr.status === 403) {
        toastr.error(xhr.responseJSON.error ? xhr.responseJSON.error.message : xhr.responseJSON.message);
        $('#EditPersonReferenceModal').modal('hide');
    }

    if (xhr.status === 404) {
        toastr.error(xhr.responseJSON.error ? xhr.responseJSON.error.message : xhr.responseJSON.message);
        $('#EditPersonReferenceModal').modal('hide');
    }

    if (xhr.status === 419) {
        toastr.error(xhr.responseJSON.error ? xhr.responseJSON.error.message : xhr.responseJSON.message);
        $('#EditPersonReferenceModal').modal('hide');
    }

    if (xhr.status === 422) {
        RemoveIsValidClassEditPersonReference();
        RemoveIsInvalidClassEditPersonReference();
        $.each(xhr.responseJSON.errors, function (field, messages) {
            AddIsInvalidClassEditPersonReference(field);
            $.each(messages, function (index, message) {
                toastr.error(message);
            });
        });
        AddIsValidClassEditPersonReference();
    }

    if (xhr.status === 500) {
        toastr.error(xhr.responseJSON.error ? xhr.responseJSON.error.message : xhr.responseJSON.message);
        $('#EditPersonReferenceModal').modal('hide');
    }
}

function AddIsValidClassEditPersonReference() {
    if (!$('#name_pr_e').hasClass('is-invalid')) {
        $('#name_pr_e').addClass('is-valid');
    }
    if (!$('#last_name_pr_e').hasClass('is-invalid')) {
        $('#last_name_pr_e').addClass('is-valid');
    }
    if (!$('#document_number_pr_e').hasClass('is-invalid')) {
        $('#document_number_pr_e').addClass('is-valid');
    }
    if (!$('#address_pr_e').hasClass('is-invalid')) {
        $('#address_pr_e').addClass('is-valid');
    }
    if (!$('#neighborhood_pr_e').hasClass('is-invalid')) {
        $('#neighborhood_pr_e').addClass('is-valid');
    }
    if (!$('#email_pr_e').hasClass('is-invalid')) {
        $('#email_pr_e').addClass('is-valid');
    }
    if (!$('#telephone_number_first_pr_e').hasClass('is-invalid')) {
        $('#telephone_number_first_pr_e').addClass('is-valid');
    }
    if (!$('#telephone_number_second_pr_e').hasClass('is-invalid')) {
        $('#telephone_number_second_pr_e').addClass('is-valid');
    }
    if (!$('span[aria-labelledby="select2-person_type_id_pr_e-container"]').hasClass('is-invalid')) {
        $('span[aria-labelledby="select2-person_type_id_pr_e-container"]').addClass('is-valid');
    }
    if (!$('span[aria-labelledby="select2-client_type_id_pr_e-container"]').hasClass('is-invalid')) {
        $('span[aria-labelledby="select2-client_type_id_pr_e-container"]').addClass('is-valid');
    }
    if (!$('span[aria-labelledby="select2-document_type_id_pr_e-container"]').hasClass('is-invalid')) {
        $('span[aria-labelledby="select2-document_type_id_pr_e-container"]').addClass('is-valid');
    }
    if (!$('span[aria-labelledby="select2-country_id_pr_e-container"]').hasClass('is-invalid')) {
        $('span[aria-labelledby="select2-country_id_pr_e-container"]').addClass('is-valid');
    }
    if (!$('span[aria-labelledby="select2-departament_id_pr_e-container"]').hasClass('is-invalid')) {
        $('span[aria-labelledby="select2-departament_id_pr_e-container"]').addClass('is-valid');
    }
    if (!$('span[aria-labelledby="select2-city_id_pr_e-container"]').hasClass('is-invalid')) {
        $('span[aria-labelledby="select2-city_id_pr_e-container"]').addClass('is-valid');
    }
}

function RemoveIsValidClassEditPersonReference() {
    $('#name_pr_e').removeClass('is-valid');
    $('#last_name_pr_e').removeClass('is-valid');
    $('#document_number_pr_e').removeClass('is-valid');
    $('#address_pr_e').removeClass('is-valid');
    $('#neighborhood_pr_e').removeClass('is-valid');
    $('#email_pr_e').removeClass('is-valid');
    $('#telephone_number_first_pr_e').removeClass('is-valid');
    $('#telephone_number_second_pr_e').removeClass('is-valid');
    $('span[aria-labelledby="select2-person_type_id_pr_e-container"]').removeClass('is-valid');
    $('span[aria-labelledby="select2-client_type_id_pr_e-container"]').removeClass('is-valid');
    $('span[aria-labelledby="select2-document_type_id_pr_e-container"]').removeClass('is-valid');
    $('span[aria-labelledby="select2-country_id_pr_e-container"]').removeClass('is-valid');
    $('span[aria-labelledby="select2-departament_id_pr_e-container"]').removeClass('is-valid');
    $('span[aria-labelledby="select2-city_id_pr_e-container"]').removeClass('is-valid');
}

function AddIsInvalidClassEditPersonReference(input) {
    if (!$(`#${input}_pr_e`).hasClass('is-valid')) {
        $(`#${input}_pr_e`).addClass('is-invalid');
    }
    if (!$(`span[aria-labelledby="select2-${input}_pr_e-container`).hasClass('is-valid')) {
        $(`span[aria-labelledby="select2-${input}_pr_e-container"]`).addClass('is-invalid');
    }
}

function RemoveIsInvalidClassEditPersonReference() {
    $('#name_pr_e').removeClass('is-invalid');
    $('#last_name_pr_e').removeClass('is-invalid');
    $('#document_number_pr_e').removeClass('is-invalid');
    $('#address_pr_e').removeClass('is-invalid');
    $('#neighborhood_pr_e').removeClass('is-invalid');
    $('#email_pr_e').removeClass('is-invalid');
    $('#telephone_number_first_pr_e').removeClass('is-invalid');
    $('#telephone_number_second_pr_e').removeClass('is-invalid');
    $('span[aria-labelledby="select2-person_type_id_pr_e-container"]').removeClass('is-invalid');
    $('span[aria-labelledby="select2-client_type_id_pr_e-container"]').removeClass('is-invalid');
    $('span[aria-labelledby="select2-document_type_id_pr_e-container"]').removeClass('is-invalid');
    $('span[aria-labelledby="select2-country_id_pr_e-container"]').removeClass('is-invalid');
    $('span[aria-labelledby="select2-departament_id_pr_e-container"]').removeClass('is-invalid');
    $('span[aria-labelledby="select2-city_id_pr_e-container"]').removeClass('is-invalid');
}
