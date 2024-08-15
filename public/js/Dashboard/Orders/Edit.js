function EditOrderModal(id) {
    $.ajax({
        url: `/Dashboard/Orders/Edit/${id}`,
        type: 'POST',
        data: {
            '_token': $('meta[name="csrf-token"]').attr('content')
        },
        success: function (response) {
            EditOrderModalCleaned(response.data.order);
            EditOrderModalClient(response.data.clients);
            EditOrderAjaxSuccess(response);
            $('#EditOrderModal').modal('show');
        },
        error: function (xhr, textStatus, errorThrown) {
            EditOrderAjaxError(xhr);
        }
    });
}

function EditOrderModalCleaned(order) {
    EditOrderModalResetSelect('client_id_e');
    RemoveIsValidClassEditOrder();
    RemoveIsInvalidClassEditOrder();

    $('#EditOrderButton').attr('onclick', `EditOrder(${order.id})`);
    $('#EditOrderButton').attr('data-id', order.id);
    $('#EditOrderButton').attr('data-client_id', order.client_id);

    $('#dispatch_type_e').val(order.dispatch_type).trigger('change');
    $('#seller_observation_e').val(order.seller_observation);
    $('#dispatch_date_e').val(order.dispatch_date);
    $('#seller_dispatch_official_e').val(order.seller_dispatch_official);
    $('#seller_dispatch_document_e').val(order.seller_dispatch_document);
}

function EditOrderModalResetSelect(id) {
    $(`#${id}`).html('')
    $(`#${id}`).append(new Option('Seleccione', '', false, false));
    $(`#${id}`).trigger('change');
}

function EditOrderModalClient(clients) {
    $.each(clients, function(index, client) {
        $('#client_id_e').append(new Option(`${client.client_name} | ${client.client_number_document}-${client.client_branch_code} | ${client.client_branch_address} | ${client.departament}-${client.city}`, client.id, false, false));
    });

    let client_id = $('#EditOrderButton').attr('data-client_id');
    if(client_id != '') {
        $("#client_id_e").val(client_id).trigger('change');
        $('#EditOrderButton').attr('data-client_id', '');
    }
}

function EditOrderGetClient(select) {
    if($(select).val() != '') {
        let id = $('#EditOrderButton').attr('data-id');
        $.ajax({
            url: `/Dashboard/Orders/Edit/${id}`,
            type: 'POST',
            data: {
                '_token': $('meta[name="csrf-token"]').attr('content'),
                'client_id':  $(select).val()
            },
            success: function(response) {
                let fields = [];
                if(response.data.client.type == 'CREDITO' || !response.data.client.type) {
                    fields = ['compra', 'cartera', 'bodega', 'administrador', 'chamber_of_commerce', 'rut', 'identity_card', 'signature_warranty'];
                } else if(response.data.client.type == 'DEBITO') {
                    fields = ['compra', 'cartera', 'bodega', 'administrador', 'identity_card'];
                }

                $.each(fields, function(index, field) {
                    if(!response.data.client[field]) {
                        DataClientModalCleaned(response.data.client, false);
                        $('#DataClientModal').modal('show');
                        return false;
                    }
                });

                EditOrderAjaxSuccess(response, false);
            },
            error: function(xhr, textStatus, errorThrown) {
                EditOrderDetailAjaxError(xhr);
            }
        });
    }
}

function EditOrderModalDispatchGetDispatchDate(select) {
    if(['', 'De inmediato', 'Total', 'Semanal'].includes($(select).val())) {
        $('#div_dispatch_date_e').hide();
        $('#dispatch_date_e').val(new Date().toISOString().split('T')[0]);
    } else {
        $('#div_dispatch_date_e').show();
    }
}

function EditOrder(id) {
    Swal.fire({
        title: '¿Desea actualizar el pedido?',
        text: 'El pedido se actualizara.',
        icon: 'warning',
        showCancelButton: true,
        cancelButtonColor: '#DD6B55',
        confirmButtonColor: '#3085d6',
        confirmButtonText: 'Si, actualizar!',
        cancelButtonText: 'No, cancelar!',
    }).then((result) => {
        if (result.value) {
            $.ajax({
                url: `/Dashboard/Orders/Update/${id}`,
                type: 'PUT',
                data: {
                    '_token': $('meta[name="csrf-token"]').attr('content'),
                    'client_id': $('#client_id_e').val(),
                    'dispatch_type': $('#dispatch_type_e').val(),
                    'dispatch_date': ['', 'De inmediato', 'Total', 'Semanal'].includes($('#dispatch_type_e').val()) ? new Date().toISOString().split('T')[0] : $('#dispatch_date_e').val(),
                    'seller_observation': $('#seller_observation_e').val(),
                    'seller_dispatch_official': $('#seller_dispatch_official_e').val(),
                    'seller_dispatch_document': $('#seller_dispatch_document_e').val()
                },
                success: function (response) {
                    tableOrders.ajax.reload();
                    EditOrderAjaxSuccess(response);
                },
                error: function (xhr, textStatus, errorThrown) {
                    EditOrderAjaxError(xhr);
                }
            });
        } else {
            toastr.info('El pedido no fue actualizada.')
        }
    });
}

function EditOrderAjaxSuccess(response, status = true) {
    if (response.status === 204) {
        toastr.info(response.message);
        status ? $('#EditOrderModal').modal('hide') : '' ;
    }

    if (response.status === 200) {
        toastr.success(response.message);
        status ? $('#EditOrderModal').modal('hide') : '' ;
    }
}

function EditOrderAjaxError(xhr) {
    if (xhr.status === 403) {
        toastr.error(xhr.responseJSON.error ? xhr.responseJSON.error.message : xhr.responseJSON.message);
        $('#EditOrderModal').modal('hide');
    }

    if (xhr.status === 404) {
        toastr.error(xhr.responseJSON.error ? xhr.responseJSON.error.message : xhr.responseJSON.message);
        $('#EditOrderModal').modal('hide');
    }

    if (xhr.status === 419) {
        toastr.error(xhr.responseJSON.error ? xhr.responseJSON.error.message : xhr.responseJSON.message);
        $('#EditOrderModal').modal('hide');
    }

    if (xhr.status === 422) {
        RemoveIsValidClassEditOrder();
        RemoveIsInvalidClassEditOrder();
        $.each(xhr.responseJSON.errors, function (field, messages) {
            AddIsInvalidClassEditOrder(field);
            $.each(messages, function (index, message) {
                toastr.error(message);
            });
        });
        AddIsValidClassEditOrder();
    }

    if (xhr.status === 500) {
        toastr.error(xhr.responseJSON.error ? xhr.responseJSON.error.message : xhr.responseJSON.message);
        $('#EditOrderModal').modal('hide');
    }
}

function AddIsValidClassEditOrder() {
    if (!$('#seller_observation_e').hasClass('is-invalid')) {
        $('#seller_observation_e').addClass('is-valid');
    }
    if (!$('#dispatch_type_e').hasClass('is-invalid')) {
        $('#dispatch_type_e').addClass('is-valid');
    }
    if (!$('#dispatch_date_e').hasClass('is-invalid')) {
        $('#dispatch_date_e').addClass('is-valid');
    }
    if (!$('#seller_dispatch_official_e').hasClass('is-invalid')) {
        $('#seller_dispatch_official_e').addClass('is-valid');
    }
    if (!$('#seller_dispatch_document_e').hasClass('is-invalid')) {
        $('#seller_dispatch_document_e').addClass('is-valid');
    }
    if (!$('span[aria-labelledby="select2-client_id_e-container"]').hasClass('is-invalid')) {
        $('span[aria-labelledby="select2-client_id_e-container"]').addClass('is-valid');
    }
}

function RemoveIsValidClassEditOrder() {
    $('#seller_observation_e').removeClass('is-valid');
    $('#dispatch_type_e').removeClass('is-valid');
    $('#dispatch_date_e').removeClass('is-valid');
    $('#seller_dispatch_official_e').removeClass('is-valid');
    $('#seller_dispatch_document_e').removeClass('is-valid');
    $('span[aria-labelledby="select2-client_id_e-container"]').removeClass('is-valid');
}

function AddIsInvalidClassEditOrder(input) {
    if (!$(`#${input}_e`).hasClass('is-valid')) {
        $(`#${input}_e`).addClass('is-invalid');
    }
    if (!$(`span[aria-labelledby="select2-${input}_e-container"]`).hasClass('is-valid')) {
        $(`span[aria-labelledby="select2-${input}_e-container"]`).addClass('is-invalid');
    }
    if (input == 'seller_dispatch_percentage') {
        $('#seller_dispatch_official_e').addClass('is-invalid');
        $('#seller_dispatch_document_e').addClass('is-invalid');
    }
}

function RemoveIsInvalidClassEditOrder() {
    $('#seller_observation_e').removeClass('is-invalid');
    $('#dispatch_type_e').removeClass('is-invalid');
    $('#dispatch_date_e').removeClass('is-invalid');
    $('#seller_dispatch_official_e').removeClass('is-invalid');
    $('#seller_dispatch_document_e').removeClass('is-invalid');
    $('span[aria-labelledby="select2-client_id_e-container"]').removeClass('is-invalid');
}