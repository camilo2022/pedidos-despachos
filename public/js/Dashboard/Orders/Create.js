function CreateOrderModal() {
    $.ajax({
        url: `/Dashboard/Orders/Create`,
        type: 'POST',
        data: {
            '_token': $('meta[name="csrf-token"]').attr('content')
        },
        success: function (response) {
            CreateOrderModalCleaned();
            CreateOrderModalClient(response.data.clients);
            CreateOrderAjaxSuccess(response);
            $('#CreateOrderModal').modal('show');
        },
        error: function (xhr, textStatus, errorThrown) {
            CreateOrderAjaxError(xhr);
        }
    });
}

function CreateOrderModalCleaned() {
    CreateOrderModalResetSelect('client_id_c');
    RemoveIsValidClassCreateOrder();
    RemoveIsInvalidClassCreateOrder();

    $('#dispatch_type_c').val('').trigger('change');
    $('#seller_observation_c').val('');
    $('#dispatch_date_c').val('');
    $('#seller_dispatch_official_c').val('');
    $('#seller_dispatch_document_c').val('');
}

function CreateOrderModalResetSelect(id) {
    $(`#${id}`).html('')
    $(`#${id}`).append(new Option('Seleccione', '', false, false));
    $(`#${id}`).trigger('change');
}

function CreateOrderModalClient(clients) {
    $.each(clients, function(index, client) {
        $('#client_id_c').append(new Option(`${client.client_name} | ${client.client_number_document}-${client.client_branch_code} | ${client.client_branch_address} | ${client.departament}-${client.city}`, client.id, false, false));
    });
}

function CreateOrderGetClient(select) {
    if($(select).val() != '') {
        $.ajax({
            url: `/Dashboard/Orders/Create`,
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

                CreateOrderAjaxSuccess(response, false);
            },
            error: function(xhr, textStatus, errorThrown) {
                CreateOrderDetailAjaxError(xhr);
            }
        });
    }
}

function CreateOrderModalDispatchGetDispatchDate(select) {
    if(['', 'De inmediato', 'Total', 'Semanal'].includes($(select).val())) {
        $('#div_dispatch_date_c').hide();
        $('#dispatch_date_c').val(new Date().toISOString().split('T')[0]);
    } else {
        $('#div_dispatch_date_c').show();
    }
}

function CreateOrder() {
    Swal.fire({
        title: '¿Desea guardar el pedido?',
        text: 'El pedido será creado.',
        icon: 'warning',
        showCancelButton: true,
        cancelButtonColor: '#DD6B55',
        confirmButtonColor: '#3085d6',
        confirmButtonText: 'Si, guardar!',
        cancelButtonText: 'No, cancelar!',
        html:`<div class="form-group c_form_group">
            <label for="order_id_c">N° PEDIDO</label>
            <input type="number" class="form-control" name="order_id_c" id="order_id_c" onclick="$('#order_id_c').focus()">
        </div>`,
        footer: '<div class="text-center">Si desea clonar las referencias ingresadas de otro pedido, ingrese el numero del pedido en el campo "N° PEDIDO".</div>'
    }).then((result) => {
        if (result.value) {
            $.ajax({
                url: `/Dashboard/Orders/Store`,
                type: 'POST',
                data: {
                    '_token': $('meta[name="csrf-token"]').attr('content'),
                    'order_id': $('#order_id_c').val() ?? null,
                    'client_id': $('#client_id_c').val(),
                    'dispatch_type': $('#dispatch_type_c').val(),
                    'dispatch_date': ['', 'De inmediato', 'Total', 'Semanal'].includes($('#dispatch_type_c').val()) ? new Date().toISOString().split('T')[0] : $('#dispatch_date_c').val(),
                    'seller_observation': $('#seller_observation_c').val(),
                    'seller_dispatch_official': $('#seller_dispatch_official_c').val(),
                    'seller_dispatch_document': $('#seller_dispatch_document_c').val()
                },
                success: function (response) {
                    window.location.href = response.data.url;
                    tableOrders.ajax.reload();
                    CreateOrderAjaxSuccess(response);
                },
                error: function (xhr, textStatus, errorThrown) {
                    CreateOrderAjaxError(xhr);
                }
            });
        } else {
            toastr.info('El pedido no fue creado.')
        }
    });
}

function CreateOrderAjaxSuccess(response, status = true) {
    if (response.status === 204) {
        toastr.info(response.message);
        status ? $('#CreateOrderModal').modal('hide') : '' ;
    }

    if (response.status === 201) {
        toastr.success(response.message);
        status ? $('#CreateOrderModal').modal('hide') : '' ;
    }
}

function CreateOrderAjaxError(xhr) {
    if (xhr.status === 403) {
        toastr.error(xhr.responseJSON.error ? xhr.responseJSON.error.message : xhr.responseJSON.message);
        $('#CreateOrderModal').modal('hide');
    }

    if (xhr.status === 404) {
        toastr.error(xhr.responseJSON.error ? xhr.responseJSON.error.message : xhr.responseJSON.message);
        $('#CreateOrderModal').modal('hide');
    }

    if (xhr.status === 419) {
        toastr.error(xhr.responseJSON.error ? xhr.responseJSON.error.message : xhr.responseJSON.message);
        $('#CreateOrderModal').modal('hide');
    }

    if (xhr.status === 422) {
        RemoveIsValidClassCreateOrder();
        RemoveIsInvalidClassCreateOrder();
        $.each(xhr.responseJSON.errors, function (field, messages) {
            AddIsInvalidClassCreateOrder(field);
            $.each(messages, function (index, message) {
                toastr.error(message);
            });
        });
        AddIsValidClassCreateOrder();
    }

    if (xhr.status === 500) {
        toastr.error(xhr.responseJSON.error ? xhr.responseJSON.error.message : xhr.responseJSON.message);
        $('#CreateOrderModal').modal('hide');
    }
}

function AddIsValidClassCreateOrder() {
    if (!$('#seller_observation_c').hasClass('is-invalid')) {
        $('#seller_observation_c').addClass('is-valid');
    }
    if (!$('#dispatch_type_c').hasClass('is-invalid')) {
        $('#dispatch_type_c').addClass('is-valid');
    }
    if (!$('#dispatch_date_c').hasClass('is-invalid')) {
        $('#dispatch_date_c').addClass('is-valid');
    }
    if (!$('#seller_dispatch_official_c').hasClass('is-invalid')) {
        $('#seller_dispatch_official_c').addClass('is-valid');
    }
    if (!$('#seller_dispatch_document_c').hasClass('is-invalid')) {
        $('#seller_dispatch_document_c').addClass('is-valid');
    }
    if (!$('span[aria-labelledby="select2-client_id_c-container"]').hasClass('is-invalid')) {
        $('span[aria-labelledby="select2-client_id_c-container"]').addClass('is-valid');
    }
}

function RemoveIsValidClassCreateOrder() {
    $('#seller_observation_c').removeClass('is-valid');
    $('#dispatch_type_c').removeClass('is-valid');
    $('#dispatch_date_c').removeClass('is-valid');
    $('#seller_dispatch_official_c').removeClass('is-valid');
    $('#seller_dispatch_document_c').removeClass('is-valid');
    $('span[aria-labelledby="select2-client_id_c-container"]').removeClass('is-valid');
}

function AddIsInvalidClassCreateOrder(input) {
    if (!$(`#${input}_c`).hasClass('is-valid')) {
        $(`#${input}_c`).addClass('is-invalid');
    }
    if (!$(`span[aria-labelledby="select2-${input}_c-container"]`).hasClass('is-valid')) {
        $(`span[aria-labelledby="select2-${input}_c-container"]`).addClass('is-invalid');
    }
    if (input == 'seller_dispatch_percentage') {
        $('#seller_dispatch_official_c').addClass('is-invalid');
        $('#seller_dispatch_document_c').addClass('is-invalid');
    }
}

function RemoveIsInvalidClassCreateOrder() {
    $('#seller_observation_c').removeClass('is-invalid');
    $('#dispatch_type_c').removeClass('is-invalid');
    $('#dispatch_date_c').removeClass('is-invalid');
    $('#seller_dispatch_official_c').removeClass('is-invalid');
    $('#seller_dispatch_document_c').removeClass('is-invalid');
    $('span[aria-labelledby="select2-client_id_c-container"]').removeClass('is-invalid');
}
