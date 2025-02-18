function CreateInvoice(){
    let boolean = true;
    $.each(payment_methods, function(field, payment_method) {
        let checked = $(`#${payment_method.settings.name.toLowerCase().replace(/\s+/g, '_')}_check`).prop('checked');
        if(checked){
            if(payment_method.is_cash) {
                if(parseInt($('#change').attr('data-change')) < 0){
                    $(document).Toasts('create', {
                        class: 'bg-danger',
                        title: 'CAMBIO DE EFECTIVO',
                        body: 'El cambio en efectivo no puede ser negativo, verificar el efectivo recibido.'
                    });
                    boolean = false;
                }
            }
            if(payment_method.is_libranza){
                let libranza = parseInt($(`#${payment_method.settings.name.toLowerCase().replace(/\s+/g, '_')}`).val());
                if(isNaN(libranza) || libranza == 0){
                    $(document).Toasts('create', {
                        class: 'bg-warning',
                        title: 'VALOR REQUERIDO',
                        body: 'El valor de libranza es requerido.'
                    });
                    boolean = false;
                } else if(libranza > parseInt($('#employee_available').val())){
                    $(document).Toasts('create', {
                        class: 'bg-danger',
                        title: 'CUPO NO DISPONIBLE',
                        body: 'El valor en libranza no puede ser mayor al cupo disponible, verificar el valor ingresado.'
                    });
                    boolean = false;
                }
            }
        }
    })

    if(boolean){
        Swal.fire({
            title: '¿Desea guardar la factura?',
            text: 'Se registrará la factura y se descontarán las unidades del inventario.',
            icon: 'warning',
            showCancelButton: true,
            cancelButtonColor: '#DD6B55',
            confirmButtonColor: '#3085d6',
            confirmButtonText: 'Si, guardar!',
            cancelButtonText: 'No, cancelar!',
        }).then((result) => {
            if (result.value) {
                $.ajax({
                    url: `/Dashboard/Invoices/Store`,
                    type: 'POST',
                    data: {
                        '_token': $('meta[name="csrf-token"]').attr('content'),
                        'person_id': $('#person_number_document').attr('data-person_id'),
                        'invoice_details': $('#invoice_details tr').map(function() {
                            return {
                                'warehouse_id': $(this).find('#reference').attr('data-warehouse_id'),
                                'product_id': $(this).find('#reference').attr('data-product_id'),
                                'size_id': $(this).find('#reference').attr('data-size_id'),
                                'color_id': $(this).find('#reference').attr('data-color_id'),
                                'quantity': $(this).find('#quantity').val(),
                                'promotion_id': $(this).find('#promotion').attr('data-promotion_id'),
                                'price': $(this).find('#price').attr('data-price'),
                                'discount': $(this).find('#discount').attr('data-discount'),
                                'subtotal': $(this).find('#subtotal').attr('data-subtotal'),
                                'total': $(this).find('#total').attr('data-total')
                            };
                        }).get(),
                        'payments': payment_methods.map(function(payment_method) {
                            let checked = $(`#${payment_method.settings.name.toLowerCase().replace(/\s+/g, '_')}_check`).prop('checked');
                            if (checked) {
                                let payment = parseInt($(`#${payment_method.settings.name.toLowerCase().replace(/\s+/g, '_')}`).val());
                                return {
                                    'payment_method_id': payment_method.id,
                                    'payment': (isNaN(payment) ? 0 : payment) - (payment_method.is_cash ? parseInt($('#change').attr('data-change')) : 0)
                                };
                            }
                        }).filter(Boolean)
                    },
                    success: function(response) {
                        if(response.data.url != null && response.data.libranza == null){
                            window.open(response.data.url, '_blank');
                        } else {
                            toastr.info('Para cerrar la factura y poder imprimirla debe firmar la libranza.');
                            $('#ConfirmLibranzaButton').attr('onclick', `ConfirmLibranza(${response.data.libranza.id})`);
                            $('#ConfirmLibranzaModal').modal('show');
                        }
                        IndexPOSReset();
                        CreateInvoiceAjaxSuccess(response);
                    },
                    error: function(xhr, textStatus, errorThrown) {
                        CreateInvoiceAjaxError(xhr);
                    }
                });
            } else {
                toastr.info('La factura no fue creada.')
            }
        });
    }
}

function CreateInvoiceAjaxSuccess(response) {
    if(response.status === 200) {
        toastr.success(response.message);
    }

    if(response.status === 201) {
        toastr.success(response.message);
    }

    if(response.status === 204) {
        toastr.warning(response.message);
    }
}

function CreateInvoiceAjaxError(xhr) {
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
