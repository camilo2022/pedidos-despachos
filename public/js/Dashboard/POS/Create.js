function CreatePOSInvoice(){
    if(parseInt($('#change').attr('data-change')) < 0){
        $(document).Toasts('create', {
            class: 'bg-danger',
            title: 'CAMBIO DE EFECTIVO',
            body: 'El cambio en efectivo no puede ser negativo, verificar el efectivo recibido.'
        });
    }
    let items = $('#invoice_details tr');
    let person_id = $('#person_number_document').attr('data-person_id');
    let invoice_details = [];
    let payments = [];

    $.each(items, function(index, item) {
        invoice_details.push({
            'warehouse_id': $(item).find('#reference').attr('data-warehouse_id'),
            'product_id': $(item).find('#reference').attr('data-product_id'),
            'size_id': $(item).find('#reference').attr('data-size_id'),
            'color_id': $(item).find('#reference').attr('data-color_id'),
            'quantity': $(item).find('#quantity').val(),
            'promotion_id': $(item).find('#promotion').attr('data-promotion_id'),
            'price': $(item).find('#price').attr('data-price'),
            'discount': $(item).find('#discount').attr('data-discount'),
            'subtotal': $(item).find('#subtotal').attr('data-subtotal'),
            'total': $(item).find('#total').attr('data-total')
        });
    });

    $.each(payment_methods, function(index, payment_method) {
        let checked = $(`#${payment_method.settings.name.toLowerCase().replace(/\s+/g, '_')}_check`).prop('checked');
        if(checked) {
            let payment = parseInt($(`#${payment_method.settings.name.toLowerCase().replace(/\s+/g, '_')}`).val());
            payments.push({
                'id': payment_method.id,
                'payment': (isNaN(payment) ? 0 : payment) - ( payment_method.is_cash ? parseInt($('#change').attr('data-change')) : 0 )
            });
        }
    });
    console.log(person_id, invoice_details, payments);
}
