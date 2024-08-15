function CreateBusinessModal() {
    $.ajax({
        url: `/Dashboard/Businesses/Create`,
        type: 'POST',
        data: {
            '_token': $('meta[name="csrf-token"]').attr('content')
        },
        success: function(response) {
            CreateBusinessModalCleaned();
            CreateBusinessModalCountry(response.data.countries);
            CreateBusinessAjaxSuccess(response);
            $('#CreateBusinessModal').modal('show');
        },
        error: function(xhr, textStatus, errorThrown) {
            CreateBusinessAjaxError(xhr);
        }
    });
}

function CreateBusinessModalCleaned() {
    CreateBusinessModalResetSelect('country_c');

    RemoveIsValidClassCreateBusiness();
    RemoveIsInvalidClassCreateBusiness();

    $('#name_c').val('ORGANIZACIÓN BLESS S.A.S');
    $('#branch_c').val('');
    $('#number_document_c').val('900835084-7');
    $('#address_c').val('');
    $('#order_footer_c').val('');
    $('#dispatch_footer_c').val('');
    $('#packing_footer_c').val('');
    $('#letterhead_c').val('');
    $('#letterhead_c').dropify().data('dropify').destroy();
    $('#letterhead_c').dropify().data('dropify').init();
}

function CreateBusinessModalResetSelect(id) {
    $(`#${id}`).html('<option value="">Seleccione</option>').trigger('change');
}

function CreateBusinessModalCountry(countries) {
    $.each(countries, function(index, country) {
        $('#country_c').append(`<option value="${country.name}">${country.name}</option>`);
    });
}

function CreateBusinessModalCountryGetDepartament(select) {
    if($(select).val() == '') {
        CreateBusinessModalResetSelect('departament_c');
    } else {
        $.ajax({
            url: `/Dashboard/Businesses/Create`,
            type: 'POST',
            data: {
                '_token': $('meta[name="csrf-token"]').attr('content'),
                'country':  $(select).val()
            },
            success: function(response) {
                CreateBusinessModalResetSelect('departament_c');
                CreateBusinessModalDepartament(response.data.departaments);
            },
            error: function(xhr, textStatus, errorThrown) {
                CreateBusinessAjaxError(xhr);
            }
        });
    }
};

function CreateBusinessModalDepartament(departaments) {
    $.each(departaments, function(index, departament) {
        $('#departament_c').append(`<option value="${departament.name}">${departament.name}</option>`);
    });
}

function CreateBusinessModalDepartamentGetCity(select) {
    if($(select).val() == '') {
        CreateBusinessModalResetSelect('city_c');
    } else {
        $.ajax({
            url: `/Dashboard/Businesses/Create`,
            type: 'POST',
            data: {
                '_token': $('meta[name="csrf-token"]').attr('content'),
                'departament':  $(select).val()
            },
            success: function(response) {
                CreateBusinessModalResetSelect('city_c');
                CreateBusinessModalCity(response.data.cities);
            },
            error: function(xhr, textStatus, errorThrown) {
                CreateBusinessAjaxError(xhr);
            }
        });
    }
};

function CreateBusinessModalCity(cities) {
    $.each(cities, function(index, city) {
        $('#city_c').append(`<option value="${city.name}">${city.name}</option>`);
    });
}

function CreateBusiness() {
    Swal.fire({
        title: '¿Desea guardar la sucursal de la empresa?',
        text: 'La sucursal de la empresa será creada.',
        icon: 'warning',
        showCancelButton: true,
        cancelButtonColor: '#DD6B55',
        confirmButtonColor: '#3085d6',
        confirmButtonText: 'Si, guardar!',
        cancelButtonText: 'No, cancelar!',
    }).then((result) => {
        if (result.value) {
            $.ajax({
                url: `/Dashboard/Businesses/Store`,
                type: 'POST',
                data: {
                    '_token': $('meta[name="csrf-token"]').attr('content'),
                    'name': $('#name_c').val(),
                    'branch': $('#branch_c').val(),
                    'number_document': $('#number_document_c').val(),
                    'country': $('#country_c').val(),
                    'departament': $('#departament_c').val(),
                    'city': $('#city_c').val(),
                    'address': $('#address_c').val(),
                    'order_footer': $('#order_footer_c').val(),
                    'dispatch_footer': $('#dispatch_footer_c').val(),
                    'packing_footer': $('#packing_footer_c').val(),
                },
                success: function(response) {
                    tableBusinesses.ajax.reload();
                    CreateBusinessAjaxSuccess(response);
                },
                error: function(xhr, textStatus, errorThrown) {
                    CreateBusinessAjaxError(xhr);
                }
            });
        } else {
            toastr.info('La sucursal de la empresa no fue creada.')
        }
    });
}

function CreateBusinessAjaxSuccess(response) {
    if(response.status === 204) {
        toastr.info(response.message);
        $('#CreateBusinessModal').modal('hide');
    }

    if(response.status === 201) {
        toastr.success(response.message);
        $('#CreateBusinessModal').modal('hide');
    }
}

function CreateBusinessAjaxError(xhr) {
    if(xhr.status === 403) {
        toastr.error(xhr.responseJSON.error ? xhr.responseJSON.error.message : xhr.responseJSON.message);
        $('#CreateBusinessModal').modal('hide');
    }

    if(xhr.status === 404) {
        toastr.error(xhr.responseJSON.error ? xhr.responseJSON.error.message : xhr.responseJSON.message);
        $('#CreateBusinessModal').modal('hide');
    }

    if(xhr.status === 419) {
        toastr.error(xhr.responseJSON.error ? xhr.responseJSON.error.message : xhr.responseJSON.message);
        $('#CreateBusinessModal').modal('hide');
    }

    if(xhr.status === 422){
        RemoveIsValidClassCreateBusiness();
        RemoveIsInvalidClassCreateBusiness();
        $.each(xhr.responseJSON.errors, function(field, messages) {
            AddIsInvalidClassCreateBusiness(field);
            $.each(messages, function(index, message) {
                toastr.error(message);
            });
        });
        AddIsValidClassCreateBusiness();
    }

    if(xhr.status === 500){
        toastr.error(xhr.responseJSON.error ? xhr.responseJSON.error.message : xhr.responseJSON.message);
        $('#CreateBusinessModal').modal('hide');
    }
}

function AddIsValidClassCreateBusiness() {
    if (!$('#name_c').hasClass('is-invalid')) {
      $('#name_c').addClass('is-valid');
    }
    if (!$('#branch_c').hasClass('is-invalid')) {
        $('#branch_c').addClass('is-valid');
    }
    if (!$('#number_document_c').hasClass('is-invalid')) {
        $('#number_document_c').addClass('is-valid');
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
    if (!$('#address_c').hasClass('is-invalid')) {
        $('#address_c').addClass('is-valid');
    }
    if (!$('#order_footer_c').hasClass('is-invalid')) {
        $('#order_footer_c').addClass('is-valid');
    }
    if (!$('#dispatch_footer_c').hasClass('is-invalid')) {
        $('#dispatch_footer_c').addClass('is-valid');
    }
    if (!$('#packing_footer_c').hasClass('is-invalid')) {
        $('#packing_footer_c').addClass('is-valid');
    }
}

function RemoveIsValidClassCreateBusiness() {
    $('#name_c').removeClass('is-valid');
    $('#branch_c').removeClass('is-valid');
    $('#number_document_c').removeClass('is-valid');
    $('span[aria-labelledby="select2-country_c-container"]').removeClass('is-valid');
    $('span[aria-labelledby="select2-departament_c-container"]').removeClass('is-valid');
    $('span[aria-labelledby="select2-city_c-container"]').removeClass('is-valid');
    $('#address_c').removeClass('is-valid');
    $('#order_footer_c').removeClass('is-valid');
    $('#dispatch_footer_c').removeClass('is-valid');
    $('#packing_footer_c').removeClass('is-valid');
}

function AddIsInvalidClassCreateBusiness(input) {
    if (!$(`#${input}_c`).hasClass('is-valid')) {
        $(`#${input}_c`).addClass('is-invalid');
    }
    if (!$(`span[aria-labelledby="select2-${input}_c-container"]`).hasClass('is-invalid')) {
        $(`span[aria-labelledby="select2-${input}_c-container"]`).addClass('is-valid');
    }
}

function RemoveIsInvalidClassCreateBusiness() {
    $('#name_c').removeClass('is-invalid');
    $('#branch_c').removeClass('is-invalid');
    $('#number_document_c').removeClass('is-invalid');
    $('span[aria-labelledby="select2-country_c-container"]').removeClass('is-invalid');
    $('span[aria-labelledby="select2-departament_c-container"]').removeClass('is-invalid');
    $('span[aria-labelledby="select2-city_c-container"]').removeClass('is-invalid');
    $('#address_c').removeClass('is-invalid');
    $('#order_footer_c').removeClass('is-invalid');
    $('#dispatch_footer_c').removeClass('is-invalid');
    $('#packing_footer_c').removeClass('is-invalid');
}
