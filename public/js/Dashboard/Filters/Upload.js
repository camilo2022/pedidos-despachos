function UploadFilter() {
    let formData = new FormData();
    formData.append('_token', $('meta[name="csrf-token"]').attr('content'));
    formData.append('cuts', $('#file_u')[0].files[0]);

    Swal.fire({
        title: '¿Desea cargar el archivo de cortes iniciales?',
        text: 'El archivo de cortes iniciales se procesara y cargara.',
        icon: 'warning',
        showCancelButton: true,
        cancelButtonColor: '#DD6B55',
        confirmButtonColor: '#3085d6',
        confirmButtonText: 'Si, cargar!',
        cancelButtonText: 'No, cancelar!'
    }).then((result) => {
        if (result.value) {
            $('#content').append(
                `<div class="overlay d-flex justify-content-center align-items-center" id="loading_modal">
                    <i class="fas fa-2x fa-sync fa-spin"></i>
                </div>`
            );
            toastr.info('Por favor espere un momento a que se cargue, valide y procese el archivo.');
            $.ajax({
                url: '/Dashboard/Filters/Upload',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    console.log(response);
                    $('#loading_modal').remove();
                    IndexFilterTotalPositionReference();
                    UploadFilterAjaxSuccess(response);
                    $('#UploadFilterModal').modal('hide');
                    $('#file_u').val('');
                    $('#file_u').dropify().data('dropify').destroy();
                    $('#file_u').dropify().data('dropify').init();
                },
                error: function(xhr, textStatus, errorThrown) {
                    UploadFilterAjaxError(xhr);
                    $('#loading_modal').remove();
                }
            });
        } else {
            toastr.info('El archivo de cortes iniciales no fue cargado.')
        }
    });
}

function UploadFilterAjaxSuccess(response) {
    if(response.status === 201) {
        toastr.success(response.message);
    }

    if(response.status === 204) {
        toastr.info(response.message);
    }
}

function UploadFilterAjaxError(xhr) {
    if(xhr.status === 403) {
        toastr.error(xhr.responseJSON.error ? xhr.responseJSON.error.message : xhr.responseJSON.message);
        $('#UploadFilterModal').modal('hide');
    }

    if(xhr.status === 404) {
        toastr.error(xhr.responseJSON.error ? xhr.responseJSON.error.message : xhr.responseJSON.message);
        $('#UploadFilterModal').modal('hide');
    }

    if(xhr.status === 419) {
        toastr.error(xhr.responseJSON.error ? xhr.responseJSON.error.message : xhr.responseJSON.message);
        $('#UploadFilterModal').modal('hide');
    }

    if(xhr.status === 422){
        $.each(xhr.responseJSON.errors ?? xhr.responseJSON.error.errors, function(field, messages) {
            $.each(messages, function(index, message) {
                toastr.error(message);
            });
        });
    }

    if(xhr.status === 500){
        toastr.error(xhr.responseJSON.error ? xhr.responseJSON.error.message : xhr.responseJSON.message);
        $('#UploadFilterModal').modal('hide');
    }
}
