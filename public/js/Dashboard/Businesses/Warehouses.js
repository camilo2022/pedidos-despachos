function WarehouseBusinessModal(id) {
    $.ajax({
        url: `/Dashboard/Businesses/Warehouses/${id}`,
        type: 'POST',
        data: {
            '_token': $('meta[name="csrf-token"]').attr('content')
        },
        success: function (response) {
            WarehouseBusinessModalCleaned(response.data);
            WarehouseBusinessAjaxSuccess(response);
            $('#WarehouseBusinessModal').modal('show');
        },
        error: function (xhr, textStatus, errorThrown) {
            WarehouseBusinessAjaxError(xhr);
        }
    });
}

function WarehouseBusinessModalCleaned(data) {
    $("#name_w").val(data.business.branch);
    $('#warehouses_w').empty();
    $.each(data.warehouses, function (index, warehouse) {
        let warehouseDiv = $('<div>').addClass('row pl-2 icheck-primary');
        let warehouseCheckbox = $(`<input>`).attr({
            'type': 'checkbox',
            'id': warehouse.id,
            'checked': warehouse.admin,
            'onchange': `WarehouseBusiness(${data.business.id}, ${warehouse.id}, this)`
        });
        let warehouseLabel = $('<label>').text(`${warehouse.name} - ${warehouse.code}`).attr({
            'for': warehouse.id,
            'class': 'mt-3 ml-3'
        });
        // Agregar elementos al cardBody
        warehouseDiv.append(warehouseCheckbox, warehouseLabel);
        $('#warehouses_w').append(warehouseDiv);
    });
}

function WarehouseBusiness(business, warehouse, checkbox) {
    if ($(checkbox).prop('checked')) {
        BusinessAssignWarehouse(business, warehouse);
    } else {
        BusinessRemoveWarehouse(business, warehouse);
    }
}

function BusinessAssignWarehouse(business, warehouse) {
    $.ajax({
        url: `/Dashboard/Businesses/AssignWarehouses`,
        type: 'POST',
        data: {
            '_token': $('meta[name="csrf-token"]').attr('content'),
            'business_id': business,
            'warehouse_id': warehouse,
        },
        success: function (response) {
            tableBusinesses.ajax.reload();
            WarehouseBusinessAjaxSuccess(response);
        },
        error: function (xhr, textStatus, errorThrown) {
            WarehouseBusinessAjaxError(xhr);
        }
    });
}

function BusinessRemoveWarehouse(business, warehouse) {
    $.ajax({
        url: `/Dashboard/Businesses/RemoveWarehouses`,
        type: 'DELETE',
        data: {
            '_token': $('meta[name="csrf-token"]').attr('content'),
            'business_id': business,
            'warehouse_id': warehouse,
        },
        success: function (response) {
            tableBusinesses.ajax.reload();
            WarehouseBusinessAjaxSuccess(response);
        },
        error: function (xhr, textStatus, errorThrown) {
            WarehouseBusinessAjaxError(xhr);
        }
    });
}

function WarehouseBusinessAjaxSuccess(response) {
    if(response.status === 200) {
        toastr.success(response.message);
    }

    if(response.status === 204) {
        toastr.info(response.message);
    }
}

function WarehouseBusinessAjaxError(xhr) {
    if (xhr.status === 403) {
        toastr.error(xhr.responseJSON.error ? xhr.responseJSON.error.message : xhr.responseJSON.message);
    }

    if (xhr.status === 404) {
        toastr.error(xhr.responseJSON.error ? xhr.responseJSON.error.message : xhr.responseJSON.message);
    }

    if (xhr.status === 419) {
        toastr.error(xhr.responseJSON.error ? xhr.responseJSON.error.message : xhr.responseJSON.message);
    }

    if (xhr.status === 422) {
        $.each(xhr.responseJSON.errors, function (field, messages) {
            $.each(messages, function (index, message) {
                toastr.error(message);
            });
        });
    }

    if (xhr.status === 500) {
        toastr.error(xhr.responseJSON.error ? xhr.responseJSON.error.message : xhr.responseJSON.message);
    }
}
