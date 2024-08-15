function WalletClientModal(id) {
    $.ajax({
        url: `/Dashboard/Clients/Show/${id}`,
        type: 'POST',
        data: {
            '_token': $('meta[name="csrf-token"]').attr('content')
        },
        success: function (response) {
            WalletClientModalCleaned(response.data.client, response.data.wallet);
            WalletClientAjaxSuccess(response);
            $('#WalletClientModal').modal('show');
        },
        error: function (xhr, textStatus, errorThrown) {
            WalletClientAjaxError(xhr);
        }
    });
}

function WalletClientModalCleaned(client, wallet) {
    RemoveIsValidClassWalletClient();
    RemoveIsInvalidClassWalletClient();

    $('#WalletClientButton').attr('onclick', `WalletClient(${client.id})`);
    $('#WalletClientButton').attr('data-id', client.id);
    
    $('#client_w').val(client.client_name);
    $('#document_w').val(client.client_number_document);

    $('#zero_to_thirty_w').val(Number(wallet.zero_to_thirty).toLocaleString());
    $('#one_to_thirty_w').val(Number(wallet.one_to_thirty).toLocaleString());
    $('#thirty_one_to_sixty_w').val(Number(wallet.thirty_one_to_sixty).toLocaleString());
    $('#sixty_one_to_ninety_w').val(Number(wallet.sixty_one_to_ninety).toLocaleString());
    $('#ninety_one_to_one_hundred_twenty_w').val(Number(wallet.ninety_one_to_one_hundred_twenty).toLocaleString());
    $('#one_hundred_twenty_one_to_one_hundred_fifty_w').val(Number(wallet.one_hundred_twenty_one_to_one_hundred_fifty).toLocaleString());
    $('#one_hundred_fifty_one_to_one_hundred_eighty_one_w').val(Number(wallet.one_hundred_fifty_one_to_one_hundred_eighty_one).toLocaleString());
    $('#eldest_to_one_hundred_eighty_one_w').val(Number(wallet.eldest_to_one_hundred_eighty_one).toLocaleString());
}

function WalletClientModalResetInput(input) {
    if ($(input).val().trim() == '') {
        $(input).val(0);
    }
}

function WalletClientModalFormatInput(input) {
    let value = $(input).val();
    value = value.replace(/\D/g, '');
    value = Number(value).toLocaleString();
    $(input).val(value);
}

function WalletClient(client_id) {
    Swal.fire({
        title: '¿Desea actualizar la cartera del cliente?',
        text: 'La cartera del cliente se actualizara.',
        icon: 'warning',
        showCancelButton: true,
        cancelButtonColor: '#DD6B55',
        confirmButtonColor: '#3085d6',
        confirmButtonText: 'Si, actualizar!',
        cancelButtonText: 'No, cancelar!',
    }).then((result) => {
        if (result.value) {
            $.ajax({
                url: `/Dashboard/Clients/Wallet`,
                type: 'POST',
                data: {
                    '_token': $('meta[name="csrf-token"]').attr('content'),
                    'client_id': client_id,
                    'zero_to_thirty': $('#zero_to_thirty_w').val().replace(/\D/g, ''),
                    'one_to_thirty': $('#one_to_thirty_w').val().replace(/\D/g, ''),
                    'thirty_one_to_sixty': $('#thirty_one_to_sixty_w').val().replace(/\D/g, ''),
                    'sixty_one_to_ninety': $('#sixty_one_to_ninety_w').val().replace(/\D/g, ''),
                    'ninety_one_to_one_hundred_twenty': $('#ninety_one_to_one_hundred_twenty_w').val().replace(/\D/g, ''),
                    'one_hundred_twenty_one_to_one_hundred_fifty': $('#one_hundred_twenty_one_to_one_hundred_fifty_w').val().replace(/\D/g, ''),
                    'one_hundred_fifty_one_to_one_hundred_eighty_one': $('#one_hundred_fifty_one_to_one_hundred_eighty_one_w').val().replace(/\D/g, ''),
                    'eldest_to_one_hundred_eighty_one': $('#eldest_to_one_hundred_eighty_one_w').val().replace(/\D/g, '')
                },
                success: function (response) {
                    tableClients.ajax.reload();
                    WalletClientAjaxSuccess(response);
                },
                error: function (xhr, textStatus, errorThrown) {
                    WalletClientAjaxError(xhr);
                }
            });
        } else {
            toastr.info('La cartera del cliente no fue actualizada.')
        }
    });
}

function WalletClientAjaxSuccess(response) {
    if (response.status === 204) {
        toastr.info(response.message);
        $('#WalletClientModal').modal('hide');
    }

    if (response.status === 200) {
        toastr.success(response.message);
        $('#WalletClientModal').modal('hide');
    }
}

function WalletClientAjaxError(xhr) {
    if (xhr.status === 403) {
        toastr.error(xhr.responseJSON.error ? xhr.responseJSON.error.message : xhr.responseJSON.message);
        $('#WalletClientModal').modal('hide');
    }

    if (xhr.status === 404) {
        toastr.error(xhr.responseJSON.error ? xhr.responseJSON.error.message : xhr.responseJSON.message);
        $('#WalletClientModal').modal('hide');
    }

    if (xhr.status === 419) {
        toastr.error(xhr.responseJSON.error ? xhr.responseJSON.error.message : xhr.responseJSON.message);
        $('#WalletClientModal').modal('hide');
    }

    if (xhr.status === 422) {
        RemoveIsValidClassWalletClient();
        RemoveIsInvalidClassWalletClient();
        $.each(xhr.responseJSON.errors, function (field, messages) {
            AddIsInvalidClassWalletClient(field);
            $.each(messages, function (index, message) {
                toastr.error(message);
            });
        });
        AddIsValidClassWalletClient();
    }

    if (xhr.status === 500) {
        toastr.error(xhr.responseJSON.error ? xhr.responseJSON.error.message : xhr.responseJSON.message);
        $('#WalletClientModal').modal('hide');
    }
}

function AddIsValidClassWalletClient() {
    if (!$('#zero_to_thirty_w').hasClass('is-invalid')) {
        $('#zero_to_thirty_w').addClass('is-valid');
    }
    if (!$('#one_to_thirty_w').hasClass('is-invalid')) {
        $('#one_to_thirty_w').addClass('is-valid');
    }
    if (!$('#thirty_one_to_sixty_w').hasClass('is-invalid')) {
        $('#thirty_one_to_sixty_w').addClass('is-valid');
    }
    if (!$('#sixty_one_to_ninety_w').hasClass('is-invalid')) {
        $('#sixty_one_to_ninety_w').addClass('is-valid');
    }
    if (!$('#ninety_one_to_one_hundred_twenty_w').hasClass('is-invalid')) {
        $('#ninety_one_to_one_hundred_twenty_w').addClass('is-valid');
    }
    if (!$('#one_hundred_twenty_one_to_one_hundred_fifty_w').hasClass('is-invalid')) {
        $('#one_hundred_twenty_one_to_one_hundred_fifty_w').addClass('is-valid');
    }
    if (!$('#one_hundred_fifty_one_to_one_hundred_eighty_one_w').hasClass('is-invalid')) {
        $('#one_hundred_fifty_one_to_one_hundred_eighty_one_w').addClass('is-valid');
    }
    if (!$('#eldest_to_one_hundred_eighty_one_w').hasClass('is-invalid')) {
        $('#eldest_to_one_hundred_eighty_one_w').addClass('is-valid');
    }
}

function RemoveIsValidClassWalletClient() {
    $('#zero_to_thirty_w').removeClass('is-valid');
    $('#one_to_thirty_w').removeClass('is-valid');
    $('#thirty_one_to_sixty_w').removeClass('is-valid');
    $('#sixty_one_to_ninety_w').removeClass('is-valid');
    $('#ninety_one_to_one_hundred_twenty_w').removeClass('is-valid');
    $('#one_hundred_twenty_one_to_one_hundred_fifty_w').removeClass('is-valid');
    $('#one_hundred_fifty_one_to_one_hundred_eighty_one_w').removeClass('is-valid');
    $('#eldest_to_one_hundred_eighty_one_w').removeClass('is-valid');
}

function AddIsInvalidClassWalletClient(input) {
    if (!$(`#${input}_e`).hasClass('is-valid')) {
        $(`#${input}_e`).addClass('is-invalid');
    }
}

function RemoveIsInvalidClassWalletClient() {
    $('#zero_to_thirty_w').removeClass('is-invalid');
    $('#one_to_thirty_w').removeClass('is-invalid');
    $('#thirty_one_to_sixty_w').removeClass('is-invalid');
    $('#sixty_one_to_ninety_w').removeClass('is-invalid');
    $('#ninety_one_to_one_hundred_twenty_w').removeClass('is-invalid');
    $('#one_hundred_twenty_one_to_one_hundred_fifty_w').removeClass('is-invalid');
    $('#one_hundred_fifty_one_to_one_hundred_eighty_one_w').removeClass('is-invalid');
    $('#eldest_to_one_hundred_eighty_one_w').removeClass('is-invalid');
}
