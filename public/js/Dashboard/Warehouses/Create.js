function CreateWarehouseModal() {
    $.ajax({
        url: `/Dashboard/Warehouses/Create`,
        type: 'POST',
        data: {
            '_token': $('meta[name="csrf-token"]').attr('content')
        },
        success: function (response) {
            CreateWarehouseModalCleaned();
            CreateWarehouseAjaxSuccess(response);
            $('#CreateWarehouseModal').modal('show');
        },
        error: function (xhr, textStatus, errorThrown) {
            CreateWarehouseAjaxError(xhr);
        }
    });
}

function CreateWarehouseModalCleaned() {
    RemoveIsValidClassCreateWarehouse();
    RemoveIsInvalidClassCreateWarehouse();

    $('#name_c').val('');
    $('#code_c').val('');
    $('#to_cut_c').prop('checked', false);
    $('#to_transit_c').prop('checked', false);
    $('#to_discount_c').prop('checked', false);
    $('#to_exclusive_c').prop('checked', false);
}

function CreateWarehouse() {
    Swal.fire({
        title: '¿Desea guardar la bodega?',
        text: 'La bodega será creada.',
        icon: 'warning',
        showCancelButton: true,
        cancelButtonColor: '#DD6B55',
        confirmButtonColor: '#3085d6',
        confirmButtonText: 'Si, guardar!',
        cancelButtonText: 'No, cancelar!'
    }).then((result) => {
        if (result.value) {
            $.ajax({
                url: `/Dashboard/Warehouses/Store`,
                type: 'POST',
                data: {
                    '_token': $('meta[name="csrf-token"]').attr('content'),
                    'name': $('#name_c').val(),
                    'code': $('#code_c').val(),
                    'to_cut': $('#to_cut_c').is(':checked'),
                    'to_transit': $('#to_transit_c').is(':checked'),
                    'to_discount': $('#to_discount_c').is(':checked'),
                    'to_exclusive': $('#to_exclusive_c').is(':checked')
                },
                success: function (response) {
                    tableWarehouses.ajax.reload();
                    CreateWarehouseAjaxSuccess(response);
                },
                error: function (xhr, textStatus, errorThrown) {
                    CreateWarehouseAjaxError(xhr);
                }
            });
        } else {
            toastr.info('La bodega no fue creada.')
        }
    });
}

function CreateWarehouseAjaxSuccess(response) {
    if (response.status === 200) {
        toastr.info(response.message);
        $('#CreateWarehouseModal').modal('hide');
    }

    if (response.status === 201) {
        toastr.success(response.message);
        $('#CreateWarehouseModal').modal('hide');
    }
}

function CreateWarehouseAjaxError(xhr) {
    if (xhr.status === 403) {
        toastr.error(xhr.responseJSON.error ? xhr.responseJSON.error.message : xhr.responseJSON.message);
        $('#CreateWarehouseModal').modal('hide');
    }

    if (xhr.status === 404) {
        toastr.error(xhr.responseJSON.error ? xhr.responseJSON.error.message : xhr.responseJSON.message);
        $('#CreateWarehouseModal').modal('hide');
    }

    if (xhr.status === 419) {
        toastr.error(xhr.responseJSON.error ? xhr.responseJSON.error.message : xhr.responseJSON.message);
        $('#CreateWarehouseModal').modal('hide');
    }

    if (xhr.status === 422) {
        RemoveIsValidClassCreateWarehouse();
        RemoveIsInvalidClassCreateWarehouse();
        $.each(xhr.responseJSON.errors, function (field, messages) {
            AddIsInvalidClassCreateWarehouse(field);
            $.each(messages, function (index, message) {
                toastr.error(message);
            });
        });
        AddIsValidClassCreateWarehouse();
    }

    if (xhr.status === 500) {
        toastr.error(xhr.responseJSON.error ? xhr.responseJSON.error.message : xhr.responseJSON.message);
        $('#CreateWarehouseModal').modal('hide');
    }
}

function AddIsValidClassCreateWarehouse() {
    if (!$('#name_c').hasClass('is-invalid')) {
        $('#name_c').addClass('is-valid');
    }
    if (!$('#code_c').hasClass('is-invalid')) {
        $('#code_c').addClass('is-valid');
    }
}

function RemoveIsValidClassCreateWarehouse() {
    $('#name_c').removeClass('is-valid');
    $('#code_c').removeClass('is-valid');
}

function AddIsInvalidClassCreateWarehouse(input) {
    if (!$(`#${input}_c`).hasClass('is-valid')) {
        $(`#${input}_c`).addClass('is-invalid');
    }
}

function RemoveIsInvalidClassCreateWarehouse() {
    $('#name_c').removeClass('is-invalid');
    $('#code_c').removeClass('is-invalid');
}
