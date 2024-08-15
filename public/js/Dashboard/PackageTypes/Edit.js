function EditPackageTypeModal(id) {
    $.ajax({
        url: `/Dashboard/PackageTypes/Edit/${id}`,
        type: 'POST',
        data: {
            '_token': $('meta[name="csrf-token"]').attr('content')
        },
        success: function(response) {
            EditPackageTypeModalCleaned(response.data);
            EditPackageTypeAjaxSuccess(response);
            $('#EditPackageTypeModal').modal('show');
        },
        error: function(xhr, textStatus, errorThrown) {
            EditPackageTypeAjaxError(xhr);
        }
    });
}

function EditPackageTypeModalCleaned(packageType) {
    RemoveIsValidClassEditPackageType();
    RemoveIsInvalidClassEditPackageType();

    $('#EditPackageTypeButton').attr('onclick', `EditPackageType(${packageType.id})`);

    $("#name_e").val(packageType.name);
    $("#code_e").val(packageType.code);
}

function EditPackageType(id) {
    Swal.fire({
        title: '¿Desea actualizar el tipo de empaque?',
        text: 'El tipo de empaque se actualizara.',
        icon: 'warning',
        showCancelButton: true,
        cancelButtonColor: '#DD6B55',
        confirmButtonColor: '#3085d6',
        confirmButtonText: 'Si, actualizar!',
        cancelButtonText: 'No, cancelar!',
    }).then((result) => {
        if (result.value) {
            $.ajax({
                url: `/Dashboard/PackageTypes/Update/${id}`,
                type: 'PUT',
                data: {
                    '_token': $('meta[name="csrf-token"]').attr('content'),
                    'id': id,
                    'name': $("#name_e").val(),
                    'code': $("#code_e").val()
                },
                success: function(response) {
                    tablePackageTypes.ajax.reload();
                    EditPackageTypeAjaxSuccess(response);
                },
                error: function(xhr, textStatus, errorThrown) {
                    EditPackageTypeAjaxError(xhr);
                }
            });
        } else {
            toastr.info('El tipo de empaque no fue actualizado.')
        }
    });
}

function EditPackageTypeAjaxSuccess(response) {
    if(response.status === 204) {
        toastr.info(response.message);
        $('#EditPackageTypeModal').modal('hide');
    }

    if(response.status === 200) {
        toastr.success(response.message);
        $('#EditPackageTypeModal').modal('hide');
    }
}

function EditPackageTypeAjaxError(xhr) {
    if(xhr.status === 403) {
        toastr.error(xhr.responseJSON.error ? xhr.responseJSON.error.message : xhr.responseJSON.message);
        $('#EditPackageTypeModal').modal('hide');
    }

    if(xhr.status === 404) {
        toastr.error(xhr.responseJSON.error ? xhr.responseJSON.error.message : xhr.responseJSON.message);
        $('#EditPackageTypeModal').modal('hide');
    }

    if(xhr.status === 419) {
        toastr.error(xhr.responseJSON.error ? xhr.responseJSON.error.message : xhr.responseJSON.message);
        $('#EditPackageTypeModal').modal('hide');
    }

    if(xhr.status === 422){
        RemoveIsValidClassEditPackageType();
        RemoveIsInvalidClassEditPackageType();
        $.each(xhr.responseJSON.errors, function(field, messages) {
            AddIsInvalidClassEditPackageType(field);
            $.each(messages, function(index, message) {
                toastr.error(message);
            });
        });
        AddIsValidClassEditPackageType();
    }

    if(xhr.status === 500){
        toastr.error(xhr.responseJSON.error ? xhr.responseJSON.error.message : xhr.responseJSON.message);
        $('#EditPackageTypeModal').modal('hide');
    }
}

function AddIsValidClassEditPackageType() {
    if (!$('#name_e').hasClass('is-invalid')) {
      $('#name_e').addClass('is-valid');
    }
    if (!$('#code_e').hasClass('is-invalid')) {
      $('#code_e').addClass('is-valid');
    }
}

function RemoveIsValidClassEditPackageType() {
    $('#name_e').removeClass('is-valid');
    $('#code_e').removeClass('is-valid');
}

function AddIsInvalidClassEditPackageType(input) {
    if (!$(`#${input}_e`).hasClass('is-valid')) {
        $(`#${input}_e`).addClass('is-invalid');
    }
}

function RemoveIsInvalidClassEditPackageType() {
    $('#name_e').removeClass('is-invalid');
    $('#code_e').removeClass('is-invalid');
}
