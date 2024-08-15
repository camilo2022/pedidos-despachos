function WarehouseUserModal(id) {
    $.ajax({
        url: `/Dashboard/Users/Warehouses/${id}`,
        type: 'POST',
        data: {
            '_token': $('meta[name="csrf-token"]').attr('content')
        },
        success: function (response) {
            WarehouseUserModalCleaned(response.data);
            WarehouseUserAjaxSuccess(response);
            $('#WarehouseUserModal').modal('show');
        },
        error: function (xhr, textStatus, errorThrown) {
            WarehouseUserAjaxError(xhr);
        }
    });
}

function WarehouseUserModalCleaned(data) {
    $("#name_and_last_name_w").val(`${data.user.name} ${data.user.last_name}`);
    $("#email_w").val(data.user.email);
    $('#warehouses_w').empty();
    $.each(data.warehouses, function (index, warehouse) {
        let warehouseDiv = $('<div>').addClass('row pl-2 icheck-primary');
        let warehouseCheckbox = $(`<input>`).attr({
            'type': 'checkbox',
            'id': warehouse.id,
            'checked': warehouse.admin,
            'onchange': `WarehouseUser(${data.user.id}, ${warehouse.id}, this)`
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

function WarehouseUser(user, warehouse, checkbox) {
    if ($(checkbox).prop('checked')) {
        UserAssignWarehouse(user, warehouse);
    } else {
        UserRemoveWarehouse(user, warehouse);
    }
}

function UserAssignWarehouse(user, warehouse) {
    $.ajax({
        url: `/Dashboard/Users/AssignWarehouses`,
        type: 'POST',
        data: {
            '_token': $('meta[name="csrf-token"]').attr('content'),
            'user_id': user,
            'warehouse_id': warehouse,
        },
        success: function (response) {
            tableUsers.ajax.reload();
            WarehouseUserAjaxSuccess(response);
        },
        error: function (xhr, textStatus, errorThrown) {
            WarehouseUserAjaxError(xhr);
        }
    });
}

function UserRemoveWarehouse(user, warehouse) {
    $.ajax({
        url: `/Dashboard/Users/RemoveWarehouses`,
        type: 'DELETE',
        data: {
            '_token': $('meta[name="csrf-token"]').attr('content'),
            'user_id': user,
            'warehouse_id': warehouse,
        },
        success: function (response) {
            tableUsers.ajax.reload();
            WarehouseUserAjaxSuccess(response);
        },
        error: function (xhr, textStatus, errorThrown) {
            WarehouseUserAjaxError(xhr);
        }
    });
}

function WarehouseUserAjaxSuccess(response) {
    if(response.status === 200) {
        toastr.success(response.message);
    }

    if(response.status === 204) {
        toastr.info(response.message);
    }
}

function WarehouseUserAjaxError(xhr) {
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
