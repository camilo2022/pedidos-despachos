function SyncColor() {
    toastr.info('Inicia la sincronizacion de los colores en el sistema de Siesa. Por favor espere...');
    SyncSiesaColor();
    toastr.info('Inicia la sincronizacion de los colores en el sistema de Tns. Por favor espere...');
    SyncTnsColor();
}

function SyncSiesaColor() {
    $.ajax({
        url: `/Dashboard/Colors/SyncSiesa`,
        type: 'POST',
        data: {
            '_token': $('meta[name="csrf-token"]').attr('content')
        },
        success: function(response) {
            tableColors.ajax.reload();
            SyncColorAjaxSuccess(response);
        },
        error: function(xhr, textStatus, errorThrown) {
            SyncColorAjaxError(xhr);
        }
    });
}

function SyncTnsColor() {
    $.ajax({
        url: `/Dashboard/Colors/SyncTns`,
        type: 'POST',
        data: {
            '_token': $('meta[name="csrf-token"]').attr('content')
        },
        success: function(response) {
            tableColors.ajax.reload();
            SyncColorAjaxSuccess(response);
        },
        error: function(xhr, textStatus, errorThrown) {
            SyncColorAjaxError(xhr);
        }
    });
}

function SyncColorAjaxSuccess(response) {
    if(response.status === 204) {
        toastr.success(response.message);
    }
}

function SyncColorAjaxError(xhr) {
    if(xhr.status === 403) {
        toastr.error(xhr.responseJSON.error ? xhr.responseJSON.error.message : xhr.responseJSON.message);
    }

    if(xhr.status === 404) {
        toastr.error(xhr.responseJSON.error ? xhr.responseJSON.error.message : xhr.responseJSON.message);
    }

    if(xhr.status === 419) {
        toastr.error(xhr.responseJSON.error ? xhr.responseJSON.error.message : xhr.responseJSON.message);
    }

    if(xhr.status === 422){
        $.each(xhr.responseJSON.errors, function(field, messages) {
            $.each(messages, function(index, message) {
                toastr.error(message);
            });
        });
    }

    if(xhr.status === 500){
        toastr.error(xhr.responseJSON.error ? xhr.responseJSON.error.message : xhr.responseJSON.message);
    }
}
