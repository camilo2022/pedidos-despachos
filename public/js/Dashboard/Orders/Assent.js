function AssentOrder(id, status = true) {
    Swal.fire({
        title: '¿Desea aprobar el pedido?',
        text: 'El pedido será aprobado.',
        icon: 'warning',
        showCancelButton: true,
        cancelButtonColor: '#DD6B55',
        confirmButtonColor: '#3085d6',
        confirmButtonText: 'Si, aprobar!',
        cancelButtonText: 'No, cancelar!',
        html: `<div class="icheck-primary"><input type="checkbox" id="email_a" name="email_a"><label for="email_a">¿Enviar correo electronico?</label></div>
        <div class="icheck-primary"><input type="checkbox" id="download_a" name="download_a"><label for="download_a">¿Descargar pdf del pedido?</label></div>`,
        footer: '<div class="text-center">Puedes notificar via correo electronico al correo registrado del cliente y de la surcursal la confirmacion del pedido. Ademas puedes descargarlo en formato pdf para enviarselo por WhatsApp.</div>'
    }).then((result) => {
        if (result.value) {
            $.ajax({
                url: `/Dashboard/Orders/Assent`,
                type: 'PUT',
                data: {
                    '_token': $('meta[name="csrf-token"]').attr('content'),
                    'id': id,
                    'email': $('#email_a').is(':checked'),
                    'download': $('#download_a').is(':checked')
                },
                success: function(response) {
                    status ? tableOrders.ajax.reload() : (response.data.urlEmail == null ? location.reload() : '') ;
                    if(response.data.urlEmail != null) {
                        $.ajax({
                            url: `/Dashboard/Orders/Email/${id}`,
                            type: 'POST',
                            data: {
                                '_token': $('meta[name="csrf-token"]').attr('content')
                            },
                            success: function(response) {
                                AssentOrderAjaxSuccess(response);
                                setTimeout(() => { status ? tableOrders.ajax.reload() : location.reload(); }, 5000);                                
                            },
                            error: function(xhr, textStatus, errorThrown) {
                                AssentOrderAjaxError(xhr);
                            }
                        });
                    }
                    if(response.data.urlDownload != null) {
                        window.open(response.data.urlDownload, '_blank');
                    }
                    AssentOrderAjaxSuccess(response);
                },
                error: function(xhr, textStatus, errorThrown) {
                    AssentOrderAjaxError(xhr, id);
                }
            });
        } else {
            toastr.info('El pedido seleccionada no fue aprobado.')
        }
    });
}

function AssentOrderAjaxSuccess(response) {
    if(response.status === 200) {
        toastr.success(response.message);
    }

    if(response.status === 204) {
        $(document).Toasts('create', {
            class: 'bg-success',
            title: 'NOTIFICACION DE CORREO ENVIADA EXITOSAMENTE',
            body: response.message.toUpperCase()
        });
    }
}

function AssentOrderAjaxWarning(response) {
    if(response.status === 204) {
        $(document).Toasts('create', {
            class: 'bg-warning',
            title: 'NOTIFICACION DE CORREO ENVIADA EXITOSAMENTE',
            body: response.message.toUpperCase()
        });
    }
}

function AssentOrderAjaxError(xhr, id = null) {
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
                if(field == 'quota') {
                    $(document).Toasts('create', {
                        class: 'bg-danger',
                        title: 'CARTERA VENCIDA PENDIENTE POR PAGO',
                        body: message.toUpperCase().replace(/\n/g, '<br>')
                    });
                    $.ajax({
                        url: `/Dashboard/Orders/Wallet/${id}`,
                        type: 'POST',
                        data: {
                            '_token': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function(response) {
                            AssentOrderAjaxWarning(response);
                        },
                        error: function(xhr, textStatus, errorThrown) {
                            AssentOrderAjaxError(xhr);
                        }
                    });
                } else {
                    toastr.error(message);
                }
            });
        });
    }

    if(xhr.status === 500){
        toastr.error(xhr.responseJSON.error ? xhr.responseJSON.error.message : xhr.responseJSON.message);
    }
}
