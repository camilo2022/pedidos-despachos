function IndexPOSSearchPerson() {
    if($('#search_person').val().length > 3) {
        $('.person .typeahead__result').not(':last').remove();
        $.ajax({
            url: `/Dashboard/POS/Person`,
            type: 'POST',
            data: {
                '_token': $('meta[name="csrf-token"]').attr('content'),
                'search': $('#search_client_number_document').prop("checked"),
                'value': $('#search_person').val()
            },
            success: function(response) {
                $('#search_person').typeahead({
                    source: {
                        data: response.data.map(record => ({
                            id: record.number_document,
                            display: `${record.name} ${record.last_name} - ${record.number_document} - ${record.phone_number}`,
                            record: record
                        }))
                    },
                    callback: {
                        onClickAfter: function(node, a, item, event) {
                            $('.typeahead__result').remove();
                            $('#search_person').val('');
                            IndexPOSSelectPerson(item.record);
                        }
                    }
                });
                $('#search_person').typeahead('open');

                IndexPOSAjaxSuccess(response);
            },
            error: function(xhr, textStatus, errorThrown) {
                IndexPOSAjaxError(xhr);
            }
        });
    } else {
        $('.person .typeahead__result').remove();
    }
}

function IndexPOSSelectPerson(person) {
    $('#person_number_document').attr('data-person_id', person.id);
    $('#person_number_document').val(person.number_document);
    $('#person_name').val(person.name.toUpperCase());
    $('#person_last_name').val(person.last_name.toUpperCase());
    $('#person_phone_number').val(person.phone_number.toUpperCase());
    $('#person_email').val(person.email.toUpperCase());
    $('#person_address').val(person.address.toUpperCase());

    if (person.employee) {
        $('#employee_quota').val(person.employee.quota);
        let debt = 0;

        if(person.invoices.length > 0) {

        }

        let available = (person.employee.quota ?? 0) - debt;
        $('#employee_debt').val(debt);
        $('#employee_available').val(available);

        // Determinar estado de libranza y actualizar interfaz
        let status = available > 0 ? 'green' : 'red';
        let message = available > 0 ? 'Puede solicitar libranza.' : 'No puede solicitar libranza.';

        IndexPOSUpdateCheck('Libranza', status, message, available > 0);
    } else {
        IndexPOSUpdateCheck('Libranza', 'black', 'No es empleado.', false);
    }
}

function IndexPOSUpdateCheck(name, status, message, boolean) {
    if(name == 'Libranza'){
        $('#green, #red, #black').hide();
        $(`#${status}`).show();
        $('#text').text(message);

        $.each(payment_methods, function(index, payment_method) {
            if (payment_method.is_libranza && payment_method.settings?.name) {
                $(`#${payment_method.settings.name.toLowerCase().replace(/\s+/g, '_')}_check`).prop('checked', false).trigger('change').prop('disabled', !boolean);
            }
        });
    }
}

function IndexPOSChangePaymentMethod(id, type = ''){
    let checked = $(`#${id}_check`).prop('checked');
    let div = $(`#${id}_div`);
    checked ? div.show() : div.hide() ;
    $(`#${id}`).val('');
    if(type == 'cash' && !checked){
        $('.cash').hide();
    } else if(type == 'cash' && checked){
        $('.cash').show();
    }
}

function IndexPOSSearchProduct() {
    if($('#search_product').val().length > 3) {
        $('.product .typeahead__result').not(':last').remove();
        $.ajax({
            url: `/Dashboard/POS/Product`,
            type: 'POST',
            data: {
                '_token': $('meta[name="csrf-token"]').attr('content'),
                'search': $('#search_product_code_bar').prop("checked"),
                'value': $('#search_product').val()
            },
            success: function(response) {
                $('#search_product').typeahead({
                    source: {
                        data: response.data.map(record => ({
                            id: `${record.product.code}-${record.size?.code}-${record.color?.code}`,
                            display: `${record.product.code}-${record.size?.code}-${record.color?.code}`,
                            record: record
                        }))
                    },
                    callback: {
                        onClickAfter: function(node, a, item, event) {
                            $('.typeahead__result').remove();
                            $('#search_product').val('');
                            IndexPOSSelectProduct(item.record);
                        }
                    }
                });
                $('#search_product').typeahead('open');

                IndexPOSAjaxSuccess(response);
            },
            error: function(xhr, textStatus, errorThrown) {
                IndexPOSAjaxError(xhr);
            }
        });
    } else {
        $('.product .typeahead__result').remove();
    }
}

function IndexPOSSelectProduct(product) {
    let tr = `<tr>
        <th id="reference" data-warehouse_id="${product.warehouse.id}" data-product_id="${product.product.id}" data-size_id="${product.size?.id}" data-color_id="${product.color?.id}">
            ${product.product.code}${product.size ? '-'+product.size.code : ''}${product.color ? '-'+product.color.code : ''}
        </th>
        <th id="price" data-price="${product.product.price}">${new Intl.NumberFormat('es-CO', { style: 'currency', currency: 'COP' }).format(product.product.price)}</th>
        <th><input type="number" id="quantity" class="form-control" value="1" onkeyup="IndexPOSCalculateItem(this)" onblur="IndexPOSCalculateItem(this, true);"></th>
        <th id="discount" data-discount="0">$ 0.00</th>
        <th id="promotion" data-promotion_id="">-</th>
        <th id="subtotal" data-subtotal="${product.product.price}">${new Intl.NumberFormat('es-CO', { style: 'currency', currency: 'COP' }).format(product.product.price)}</th>
        <th id="total" data-total="${product.product.price}">${new Intl.NumberFormat('es-CO', { style: 'currency', currency: 'COP' }).format(product.product.price)}</th>
        <th>
            <a type="button" class="btn btn-outline-dark btn-sm" title="Promocion item." id="PromotionItemButton">
                <i class="fas fa-tag text-dark"></i>
            </a>
            <a type="button" class="btn btn-outline-danger btn-sm" title="Eliminar item." onclick="IndexPOSRemoveProduct(this)">
                <i class="fas fa-trash text-red"></i>
            </a>
        </th>
    </tr>`;

    $('#invoice_details').append(tr);
    IndexPOSCalculateInvoice();
    IndexPOSCalculateCashChange()
}

function IndexPOSRemoveProduct(button){
    $(button).closest('tr').remove();
    IndexPOSCalculateInvoice();
    IndexPOSCalculateCashChange()
}

function IndexPOSCalculateItem(input, boolean = false){
    let tr = $(input).closest('tr');
    let price = parseInt(tr.find('#price').attr('data-price'));
    let quantity = parseInt($(input).val());
    if(boolean && isNaN(quantity)){
        quantity = 1;
        $(input).val(quantity);
    }
    let value = price * (isNaN(quantity) ? 0 : quantity);

    tr.find('#subtotal').attr('data-subtotal', value);
    tr.find('#subtotal').text(new Intl.NumberFormat('es-CO', { style: 'currency', currency: 'COP' }).format(value));
    tr.find('#total').attr('data-total', value);
    tr.find('#total').text(new Intl.NumberFormat('es-CO', { style: 'currency', currency: 'COP' }).format(value));

    IndexPOSCalculateInvoice();
    IndexPOSCalculateCashChange();
}

function IndexPOSCalculateInvoice(boolean = false){
    let invoice_details = $('#invoice_details tr');
    let subtotal = 0;
    let discount = 0;
    let total = 0;

    $.each(invoice_details, function(index, invoice_detail) {
        subtotal += parseInt($(invoice_detail).find('#subtotal').attr('data-subtotal'));
        discount += parseInt($(invoice_detail).find('#discount').attr('data-discount'));
        total += parseInt($(invoice_detail).find('#total').attr('data-total'));
    });

    $('#invoice_subtotal').text(new Intl.NumberFormat('es-CO', { style: 'currency', currency: 'COP' }).format(subtotal));
    $('#invoice_discount').text(new Intl.NumberFormat('es-CO', { style: 'currency', currency: 'COP' }).format(discount));
    $('#invoice_total').text(new Intl.NumberFormat('es-CO', { style: 'currency', currency: 'COP' }).format(total));
}

function IndexPOSCalculateCashChange(){
    let invoice_details = $('#invoice_details tr');
    let cash = 0;
    let other = 0;
    let total = 0;

    $.each(invoice_details, function(index, invoice_detail) {
        total += parseInt($(invoice_detail).find('#total').attr('data-total'));
    });

    $.each(payment_methods, function(index, payment_method) {
        let value = parseInt($(`#${payment_method.settings.name.toLowerCase().replace(/\s+/g, '_')}`).val());
        if (payment_method.is_cash && payment_method.settings?.name) {
            cash += isNaN(value) ? 0 : value;
        } else {
            other += isNaN(value) ? 0 : value;
        }
    });

    $('#change').text(new Intl.NumberFormat('es-CO', { style: 'currency', currency: 'COP' }).format(cash - (total - other)));
}

function IndexPOSAjaxSuccess(response) {
    if(response.status === 200) {
        toastr.success(response.message);
    }

    if(response.status === 204) {
        toastr.warning(response.message);
    }
}

function IndexPOSAjaxError(xhr) {
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
