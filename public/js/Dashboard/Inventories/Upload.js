function UploadInventoryModal() {    
    $.ajax({
        url: `/Dashboard/Inventories/Upload/Query`,
        type: 'POST',
        data: {
            '_token': $('meta[name="csrf-token"]').attr('content')
        },
        success: function (response) {
            UploadInventoryModalCleaned();
            UploadInventoryAjaxSuccess(response);
            $('#UploadInventoryModal').modal('show');
        },
        error: function (xhr, textStatus, errorThrown) {
            UploadInventoryAjaxError(xhr);
        }
    });
}

function UploadInventoryModalCleaned() {
    $('#file_u').val('');
    $('#file_u').dropify().data('dropify').destroy();
    $('#file_u').dropify().data('dropify').init();
}

function UploadInventory() {
    let formData = new FormData();
    formData.append('_token', $('meta[name="csrf-token"]').attr('content'));
    formData.append('inventories', $('#file_u')[0].files[0]);

    Swal.fire({
        title: '¿Desea cargar el archivo de proyecciones?',
        text: 'El archivo de proyecciones se procesara y cargara.',
        icon: 'warning',
        showCancelButton: true,
        cancelButtonColor: '#DD6B55',
        confirmButtonColor: '#3085d6',
        confirmButtonText: 'Si, cargar!',
        cancelButtonText: 'No, cancelar!'
    }).then((result) => {
        if (result.value) {
            $('#content').append(
                `<div class="overlay d-flex justify-content-center align-items-center" id="loading_bmi">
                    <i class="fas fa-2x fa-sync fa-spin"></i>
                </div>`
            );
            toastr.info('Por favor espere un momento a que se cargue, valide y procese el archivo.');
            $.ajax({
                url: '/Dashboard/Inventories/Upload',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    tableInventories.ajax.reload();
                    UploadInventoryAjaxSuccess(response);
                    $('#UploadInventoryModal').modal('hide');
                    $('#loading_bmi').remove();
                },
                error: function(xhr, textStatus, errorThrown) {
                    UploadInventoryAjaxError(xhr);
                    $('#loading_bmi').remove();
                }
            });
        } else {
            toastr.info('El archivo de proyecciones no fue cargado.')
        }
    });
}

function UploadInventoryAjaxSuccess(response) {
    if(response.status === 201) {
        toastr.success(response.message);
    }

    if(response.status === 204) {
        toastr.info(response.message);
    }
}

function UploadInventoryAjaxError(xhr) {
    if(xhr.status === 403) {
        toastr.error(xhr.responseJSON.error ? xhr.responseJSON.error.message : xhr.responseJSON.message);
        $('#UploadInventoryModal').modal('hide');
    }

    if(xhr.status === 404) {
        toastr.error(xhr.responseJSON.error ? xhr.responseJSON.error.message : xhr.responseJSON.message);
        $('#UploadInventoryModal').modal('hide');
    }

    if(xhr.status === 419) {
        toastr.error(xhr.responseJSON.error ? xhr.responseJSON.error.message : xhr.responseJSON.message);
        $('#UploadInventoryModal').modal('hide');
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
        $('#UploadInventoryModal').modal('hide');
    }
}
