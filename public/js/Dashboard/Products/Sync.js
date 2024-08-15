function SyncProduct(referencia) {
    toastr.info('Inicia la sincronizacion de la referencia en el sistema de Siesa. Por favor espere...');
    SyncSiesaProduct(referencia);
    toastr.info('Inicia la sincronizacion de la referencia en el sistema de Tns. Por favor espere...');
    SyncTnsProduct(referencia);
}

function SyncSiesaProduct(referencia) {
    $.ajax({
        url: `/Dashboard/Products/SyncSiesa`,
        type: 'POST',
        data: {
            '_token': $('meta[name="csrf-token"]').attr('content'),
            'referencia': referencia
        },
        success: function(response) {
            tableProducts.ajax.reload();
            SyncProductAjaxSuccess(response);
        },
        error: function(xhr, textStatus, errorThrown) {
            SyncProductAjaxError(xhr);
        }
    });
}

function SyncTnsProduct(referencia) {
    $.ajax({
        url: `/Dashboard/Products/SyncTns`,
        type: 'POST',
        data: {
            '_token': $('meta[name="csrf-token"]').attr('content'),
            'referencia': referencia
        },
        success: function(response) {
            tableProducts.ajax.reload();
            SyncProductAjaxSuccess(response);
        },
        error: function(xhr, textStatus, errorThrown) {
            SyncProductAjaxError(xhr);
        }
    });
}

function SyncProductAjaxSuccess(response) {
    if(response.status === 200) {
        toastr.success(response.message);
    }
}

function SyncProductAjaxError(xhr) {
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
