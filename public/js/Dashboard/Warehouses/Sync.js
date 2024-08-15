function SyncWarehouse() {
    toastr.info('Inicia la sincronizacion de las bodegas en el sistema de Siesa. Por favor espere...');
    SyncSiesaWarehouse();
    toastr.info('Inicia la sincronizacion de las bodegas en el sistema de Tns. Por favor espere...');
    SyncTnsWarehouse();
}

function SyncSiesaWarehouse() {
    $.ajax({
        url: `/Dashboard/Warehouses/SyncSiesa`,
        type: 'POST',
        data: {
            '_token': $('meta[name="csrf-token"]').attr('content')
        },
        success: function(response) {
            tableWarehouses.ajax.reload();
            SyncWarehouseAjaxSuccess(response);
        },
        error: function(xhr, textStatus, errorThrown) {
            SyncWarehouseAjaxError(xhr);
        }
    });
}

function SyncTnsWarehouse() {
    $.ajax({
        url: `/Dashboard/Warehouses/SyncTns`,
        type: 'POST',
        data: {
            '_token': $('meta[name="csrf-token"]').attr('content')
        },
        success: function(response) {
            tableWarehouses.ajax.reload();
            SyncWarehouseAjaxSuccess(response);
        },
        error: function(xhr, textStatus, errorThrown) {
            SyncWarehouseAjaxError(xhr);
        }
    });
}

function SyncWarehouseAjaxSuccess(response) {
    if(response.status === 204) {
        toastr.success(response.message);
    }
}

function SyncWarehouseAjaxError(xhr) {
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
