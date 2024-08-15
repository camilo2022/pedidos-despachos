function SyncInventory() {
    toastr.info('Inicia la sincronizacion del inventario en el sistema de Siesa. Por favor espere...');
    SyncSiesaInventory();
    toastr.info('Inicia la sincronizacion del inventario en el sistema de Tns. Por favor espere...');
    SyncTnsInventory();
}

function SyncSiesaInventory() {
    $.ajax({
        url: `/Dashboard/Inventories/SyncSiesa`,
        type: 'POST',
        data: {
            '_token': $('meta[name="csrf-token"]').attr('content')
        },
        success: function(response) {
            tableInventories.ajax.reload();
            SyncInventoryAjaxSuccess(response);
        },
        error: function(xhr, textStatus, errorThrown) {
            SyncInventoryAjaxError(xhr);
        }
    });
}

function SyncTnsInventory() {
    $.ajax({
        url: `/Dashboard/Inventories/SyncTns`,
        type: 'POST',
        data: {
            '_token': $('meta[name="csrf-token"]').attr('content')
        },
        success: function(response) {
            tableInventories.ajax.reload();
            SyncInventoryAjaxSuccess(response);
        },
        error: function(xhr, textStatus, errorThrown) {
            SyncInventoryAjaxError(xhr);
        }
    });
}

function SyncInventoryAjaxSuccess(response) {
    if(response.status === 204) {
        toastr.success(response.message);
    }
}

function SyncInventoryAjaxError(xhr) {
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
