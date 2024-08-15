function EditCorreriaModal(id) {
    $.ajax({
        url: `/Dashboard/Correrias/Edit/${id}`,
        type: 'POST',
        data: {
            '_token': $('meta[name="csrf-token"]').attr('content')
        },
        success: function(response) {
            EditCorreriaModalCleaned(response.data);
            EditCorreriaAjaxSuccess(response);
            $('#EditCorreriaModal').modal('show');
        },
        error: function(xhr, textStatus, errorThrown) {
            EditCorreriaAjaxError(xhr);
        }
    });
}

function EditCorreriaModalCleaned(correria) {
    RemoveIsValidClassEditCorreria();
    RemoveIsInvalidClassEditCorreria();

    $('#EditCorreriaButton').attr('onclick', `EditCorreria(${correria.id})`);

    $("#name_e").val(correria.name);
    $("#code_e").val(correria.code);
    $("#start_date_e").val(moment(correria.start_date).format('YYYY-MM-DD'));
    $("#end_date_e").val(moment(correria.end_date).format('YYYY-MM-DD'));
}

function EditCorreria(id) {
    Swal.fire({
        title: '¿Desea actualizar la correria?',
        text: 'La correria se actualizara.',
        icon: 'warning',
        showCancelButton: true,
        cancelButtonColor: '#DD6B55',
        confirmButtonColor: '#3085d6',
        confirmButtonText: 'Si, actualizar!',
        cancelButtonText: 'No, cancelar!',
    }).then((result) => {
        if (result.value) {
            $.ajax({
                url: `/Dashboard/Correrias/Update/${id}`,
                type: 'PUT',
                data: {
                    '_token': $('meta[name="csrf-token"]').attr('content'),
                    'id': id,
                    'name': $("#name_e").val(),
                    'code': $("#code_e").val(),
                    'start_date': $("#start_date_e").val(),
                    'end_date': $("#end_date_e").val()
                },
                success: function(response) {
                    tableCorrerias.ajax.reload();
                    EditCorreriaAjaxSuccess(response);
                },
                error: function(xhr, textStatus, errorThrown) {
                    EditCorreriaAjaxError(xhr);
                }
            });
        } else {
            toastr.info('La correria no fue actualizada.')
        }
    });
}

function EditCorreriaAjaxSuccess(response) {
    if(response.status === 200) {
        toastr.success(response.message);
        $('#EditCorreriaModal').modal('hide');
    }

    if(response.status === 204) {
        toastr.info(response.message);
        $('#PasswordCorreriaModal').modal('hide');
    }
}

function EditCorreriaAjaxError(xhr) {
    if(xhr.status === 403) {
        toastr.error(xhr.responseJSON.error ? xhr.responseJSON.error.message : xhr.responseJSON.message);
        $('#EditCorreriaModal').modal('hide');
    }

    if(xhr.status === 404) {
        toastr.error(xhr.responseJSON.error ? xhr.responseJSON.error.message : xhr.responseJSON.message);
        $('#EditCorreriaModal').modal('hide');
    }

    if(xhr.status === 419) {
        toastr.error(xhr.responseJSON.error ? xhr.responseJSON.error.message : xhr.responseJSON.message);
        $('#EditCorreriaModal').modal('hide');
    }

    if(xhr.status === 422){
        RemoveIsValidClassEditCorreria();
        RemoveIsInvalidClassEditCorreria();
        $.each(xhr.responseJSON.errors, function(field, messages) {
            AddIsInvalidClassEditCorreria(field);
            $.each(messages, function(index, message) {
                toastr.error(message);
            });
        });
        AddIsValidClassEditCorreria();
    }

    if(xhr.status === 500){
        toastr.error(xhr.responseJSON.error ? xhr.responseJSON.error.message : xhr.responseJSON.message);
        $('#EditCorreriaModal').modal('hide');
    }
}

function AddIsValidClassEditCorreria() {
    if (!$('#name_e').hasClass('is-invalid')) {
      $('#name_e').addClass('is-valid');
    }
    if (!$('#code_e').hasClass('is-invalid')) {
      $('#code_e').addClass('is-valid');
    }
    if (!$('#start_date_e').hasClass('is-invalid')) {
      $('#start_date_e').addClass('is-valid');
    }
    if (!$('#end_date_e').hasClass('is-invalid')) {
      $('#end_date_e').addClass('is-valid');
    }
}

function RemoveIsValidClassEditCorreria() {
    $('#name_e').removeClass('is-valid');
    $('#code_e').removeClass('is-valid');
    $('#start_date_e').removeClass('is-valid');
    $('#end_date_e').removeClass('is-valid');
}

function AddIsInvalidClassEditCorreria(input) {
    if (!$(`#${input}_e`).hasClass('is-valid')) {
        $(`#${input}_e`).addClass('is-invalid');
    }
}

function RemoveIsInvalidClassEditCorreria() {
    $('#name_e').removeClass('is-invalid');
    $('#code_e').removeClass('is-invalid');
    $('#start_date_e').removeClass('is-invalid');
    $('#end_date_e').removeClass('is-invalid');
}
