function SyncClient() {
    toastr.info('Inicia la sincronizacion de los clientes en el sistema de Siesa. Por favor espere...');
    SyncSiesaClient();
    toastr.info('Inicia la sincronizacion de los clientes en el sistema de Tns. Por favor espere...');
    SyncTnsClient();
}

function SyncSiesaClient() {
    $.ajax({
        url: `/Dashboard/Clients/SyncSiesa`,
        type: 'POST',
        data: {
            '_token': $('meta[name="csrf-token"]').attr('content')
        },
        success: function(response) {
            tableClients.ajax.reload();
            SyncClientAjaxSuccess(response);
        },
        error: function(xhr, textStatus, errorThrown) {
            SyncClientAjaxError(xhr);
        }
    });
}

function SyncTnsClient() {
    $.ajax({
        url: `/Dashboard/Clients/SyncTns`,
        type: 'POST',
        data: {
            '_token': $('meta[name="csrf-token"]').attr('content')
        },
        success: function(response) {
            tableClients.ajax.reload();
            SyncClientAjaxSuccess(response);
        },
        error: function(xhr, textStatus, errorThrown) {
            SyncClientAjaxError(xhr);
        }
    });
}

function SyncClientAjaxSuccess(response) {
    if(response.status === 204) {
        toastr.success(response.message);
    }
}

function SyncClientAjaxError(xhr) {
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
