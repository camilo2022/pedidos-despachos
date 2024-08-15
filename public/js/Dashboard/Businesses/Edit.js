function EditBusinessModal(id) {
    $.ajax({
        url: `/Dashboard/Businesses/Edit/${id}`,
        type: 'POST',
        data: {
            '_token': $('meta[name="csrf-token"]').attr('content')
        },
        success: function(response) {
            EditBusinessModalCleaned(response.data.business);
            EditBusinessAjaxSuccess(response);
            $('#EditBusinessModal').modal('show');
        },
        error: function(xhr, textStatus, errorThrown) {
            EditBusinessAjaxError(xhr);
        }
    });
}

function EditBusinessModalCleaned(business) {
    RemoveIsValidClassEditBusiness();
    RemoveIsInvalidClassEditBusiness();

    $('#EditBusinessButton').attr('onclick', `EditBusiness(${business.id})`);
    $('#EditBusinessButton').attr('data-id', business.id);

    $('#name_e').val(business.name);
}

function EditBusiness(id) {
    Swal.fire({
        title: '¿Desea actualizar la sucursal de la empresa?',
        text: 'La sucursal de la empresa se actualizara.',
        icon: 'warning',
        showCancelButton: true,
        cancelButtonColor: '#DD6B55',
        confirmButtonColor: '#3085d6',
        confirmButtonText: 'Si, actualizar!',
        cancelButtonText: 'No, cancelar!',
    }).then((result) => {
        if (result.value) {
            $.ajax({
                url: `/Dashboard/Businesses/Update/${id}`,
                type: 'PUT',
                data: {
                    '_token': $('meta[name="csrf-token"]').attr('content'),
                    'name': $('#name_e').val()
                },
                success: function(response) {
                    tableBusinesses.ajax.reload();
                    EditBusinessAjaxSuccess(response);
                },
                error: function(xhr, textStatus, errorThrown) {
                    EditBusinessAjaxError(xhr);
                }
            });
        } else {
            toastr.info('La sucursal de la empresa no fue actualizado.')
        }
    });
}

function EditBusinessAjaxSuccess(response) {
    if(response.status === 204) {
        toastr.info(response.message);
        $('#EditBusinessModal').modal('hide');
    }

    if(response.status === 200) {
        toastr.success(response.message);
        $('#EditBusinessModal').modal('hide');
    }
}

function EditBusinessAjaxError(xhr) {
    if(xhr.status === 403) {
        toastr.error(xhr.responseJSON.error ? xhr.responseJSON.error.message : xhr.responseJSON.message);
        $('#EditBusinessModal').modal('hide');
    }

    if(xhr.status === 404) {
        toastr.error(xhr.responseJSON.error ? xhr.responseJSON.error.message : xhr.responseJSON.message);
        $('#EditBusinessModal').modal('hide');
    }

    if(xhr.status === 419) {
        toastr.error(xhr.responseJSON.error ? xhr.responseJSON.error.message : xhr.responseJSON.message);
        $('#EditBusinessModal').modal('hide');
    }

    if(xhr.status === 422){
        RemoveIsValidClassEditBusiness();
        RemoveIsInvalidClassEditBusiness();
        $.each(xhr.responseJSON.errors, function(field, messages) {
            AddIsInvalidClassEditBusiness(field);
            $.each(messages, function(index, message) {
                toastr.error(message);
            });
        });
        AddIsValidClassEditBusiness();
    }

    if(xhr.status === 500){
        toastr.error(xhr.responseJSON.error ? xhr.responseJSON.error.message : xhr.responseJSON.message);
        $('#EditBusinessModal').modal('hide');
    }
}

function AddIsValidClassEditBusiness() {
    if (!$('#name_e').hasClass('is-invalid')) {
        $('#name_e').addClass('is-valid');
    }
}

function RemoveIsValidClassEditBusiness() {
    $('#name_e').removeClass('is-valid');
}

function AddIsInvalidClassEditBusiness(input) {
    if (!$(`#${input}_e`).hasClass('is-valid')) {
        $(`#${input}_e`).addClass('is-invalid');
    }
}

function RemoveIsInvalidClassEditBusiness() {
    $('#name_e').removeClass('is-invalid');
}
