function CreatePackageTypeModal() {
    $.ajax({
        url: `/Dashboard/PackageTypes/Create`,
        type: 'POST',
        data: {
            '_token': $('meta[name="csrf-token"]').attr('content')
        },
        success: function(response) {
            CreatePackageTypeModalCleaned();
            CreatePackageTypeAjaxSuccess(response);
            $('#CreatePackageTypeModal').modal('show');
        },
        error: function(xhr, textStatus, errorThrown) {
            CreatePackageTypeAjaxError(xhr);
        }
    });
}

function CreatePackageTypeModalCleaned() {
    RemoveIsValidClassCreatePackageType();
    RemoveIsInvalidClassCreatePackageType();

    $('#name_c').val('');
    $('#code_c').val('');
}

function CreatePackageType() {
    Swal.fire({
        title: '¿Desea guardar el tipo de empaque?',
        text: 'El tipo de empaque será creado.',
        icon: 'warning',
        showCancelButton: true,
        cancelButtonColor: '#DD6B55',
        confirmButtonColor: '#3085d6',
        confirmButtonText: 'Si, guardar!',
        cancelButtonText: 'No, cancelar!',
    }).then((result) => {
        if (result.value) {
            $.ajax({
                url: `/Dashboard/PackageTypes/Store`,
                type: 'POST',
                data: {
                    '_token': $('meta[name="csrf-token"]').attr('content'),
                    'name': $('#name_c').val(),
                    'code': $('#code_c').val(),
                },
                success: function(response) {
                    tablePackageTypes.ajax.reload();
                    CreatePackageTypeAjaxSuccess(response);
                },
                error: function(xhr, textStatus, errorThrown) {
                    CreatePackageTypeAjaxError(xhr);
                }
            });
        } else {
            toastr.info('El tipo de empaque no fue creado.')
        }
    });
}

function CreatePackageTypeAjaxSuccess(response) {
    if(response.status === 204) {
        toastr.info(response.message);
        $('#CreatePackageTypeModal').modal('hide');
    }

    if(response.status === 201) {
        toastr.success(response.message);
        $('#CreatePackageTypeModal').modal('hide');
    }
}

function CreatePackageTypeAjaxError(xhr) {
    if(xhr.status === 403) {
        toastr.error(xhr.responseJSON.error ? xhr.responseJSON.error.message : xhr.responseJSON.message);
        $('#CreatePackageTypeModal').modal('hide');
    }

    if(xhr.status === 404) {
        toastr.error(xhr.responseJSON.error ? xhr.responseJSON.error.message : xhr.responseJSON.message);
        $('#CreatePackageTypeModal').modal('hide');
    }

    if(xhr.status === 419) {
        toastr.error(xhr.responseJSON.error ? xhr.responseJSON.error.message : xhr.responseJSON.message);
        $('#CreatePackageTypeModal').modal('hide');
    }

    if(xhr.status === 422){
        RemoveIsValidClassCreatePackageType();
        RemoveIsInvalidClassCreatePackageType();
        $.each(xhr.responseJSON.errors, function(field, messages) {
            AddIsInvalidClassCreatePackageType(field);
            $.each(messages, function(index, message) {
                toastr.error(message);
            });
        });
        AddIsValidClassCreatePackageType();
    }

    if(xhr.status === 500){
        toastr.error(xhr.responseJSON.error ? xhr.responseJSON.error.message : xhr.responseJSON.message);
        $('#CreatePackageTypeModal').modal('hide');
    }
}

function AddIsValidClassCreatePackageType() {
    if (!$('#name_c').hasClass('is-invalid')) {
      $('#name_c').addClass('is-valid');
    }
    if (!$('#code_c').hasClass('is-invalid')) {
      $('#code_c').addClass('is-valid');
    }
}

function RemoveIsValidClassCreatePackageType() {
    $('#name_c').removeClass('is-valid');
    $('#code_c').removeClass('is-valid');
}

function AddIsInvalidClassCreatePackageType(input) {
    if (!$(`#${input}_c`).hasClass('is-valid')) {
        $(`#${input}_c`).addClass('is-invalid');
    }
}

function RemoveIsInvalidClassCreatePackageType() {
    $('#name_c').removeClass('is-invalid');
    $('#code_c').removeClass('is-invalid');
}
