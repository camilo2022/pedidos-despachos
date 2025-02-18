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
    let item = `${product.product.code}${product.size ? '-'+product.size.code : ''}${product.color ? '-'+product.color.code : ''}`;
    let exists = $('#invoice_details tr').filter(function() {
        return $(this).find('#reference').text().trim() == item;
    }).length > 0;

    if (exists) {
        toastr.warning(`El item ${item} ya está agregado.`);
    } else {
        let tr = `<tr>
            <th id="reference" data-warehouse_id="${product.warehouse.id}" data-product_id="${product.product.id}" data-size_id="${product.size?.id}" data-color_id="${product.color?.id}" data-category="${product.product.category}" data-trademark="${product.product.trademark}">
                ${item}
            </th>
            <th id="price" data-price="${product.product.price}">${new Intl.NumberFormat('es-CO', { style: 'currency', currency: 'COP' }).format(product.product.price)}</th>
            <th><input type="number" id="quantity" class="form-control" value="1" max="${product.quantity}" onkeyup="IndexPOSCalculateItem(this)" onblur="IndexPOSCalculateItem(this, true);"></th>
            <th id="discount" data-discount="0">$ 0.00</th>
            <th id="promotion" data-promotion_id="">-</th>
            <th id="subtotal" data-subtotal="${product.product.price}">${new Intl.NumberFormat('es-CO', { style: 'currency', currency: 'COP' }).format(product.product.price)}</th>
            <th id="total" data-total="${product.product.price}">${new Intl.NumberFormat('es-CO', { style: 'currency', currency: 'COP' }).format(product.product.price)}</th>
            <th>
                <a type="button" class="btn btn-outline-dark btn-sm" title="Promocion item." id="PromotionItemButton" onclick="IndexPOSPromotions(this)">
                    <i class="fas fa-tag text-dark"></i>
                </a>
                <a type="button" class="btn btn-outline-danger btn-sm" title="Eliminar item." onclick="IndexPOSRemoveProduct(this, '${item}')">
                    <i class="fas fa-trash text-red"></i>
                </a>
            </th>
        </tr>`;

        $('#invoice_details').append(tr);
        IndexPOSCalculateInvoice();
        IndexPOSCalculateCashChange();
        toastr.success(`Item ${item} agregado exitosamente.`);
    }
}

function IndexPOSRemoveProduct(button, item){
    $(button).closest('tr').remove();
    IndexPOSCalculateInvoice();
    IndexPOSCalculateCashChange();
    toastr.danger(`Item ${item} removido exitosamente.`);
}

function IndexPOSPromotions(button) {
    $('#PromotionPOSModal').modal('show');

    $('.PromotionsButton').off('click').on('click', function() {
        let id = $(this).data('id');
        IndexPOSApplyPromotion(id, button);
    });
}


function IndexPOSApplyPromotion(id, button){
    let tr = $(button).closest('tr');
    let name = '-';
    let quantity = parseInt(tr.find('#quantity').val());
    let reference = tr.find('#reference');

    let apply = promotions.some(promotion => {
        if (promotion.id !== id) return false;

        let { trademarks, categories, products, quantity: minQuantity } = promotion.settings;
        name = promotion.name;
        return (
            (promotion.apply_trademark && trademarks.includes(reference.attr('data-trademark')) && quantity >= minQuantity) ||
            (promotion.apply_category && categories.includes(reference.attr('data-category')) && quantity >= minQuantity) ||
            (promotion.apply_product && products.includes(reference.attr('data-product_id')) && quantity >= minQuantity) ||
            (promotion.apply_quantity && promotion.apply_percentage && quantity >= minQuantity)
        );
    });

    if(apply){
        toastr.success('El item cumple con los requisitos para aplicar la promocion.');
        $('#PromotionPOSModal').modal('hide');
        tr.find('#quantity').prop('disabled', true);
        tr.find('#promotion').attr('data-promotion_id', id);
        tr.find('#promotion').text(name);
        IndexPOSCalculateDiscount();
    } else {
        toastr.error('El item no cumple con los requisitos para aplicar la promocion.');
    }
}

function IndexPOSCalculateItem(input, boolean = false){
    let tr = $(input).closest('tr');
    let price = parseInt(tr.find('#price').attr('data-price'));
    let quantity = parseInt($(input).val());
    let max = parseInt($(input).attr('max'));
    if ((boolean && isNaN(quantity)) || quantity > max) {
        if (quantity > max) {
            toastr.error(`La cantidad en inventario es de ${max}.`);
        }
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

function IndexPOSCalculateDiscount(){
    let invoice_details = $('#invoice_details tr');
    $.each(invoice_details, function(index, invoice_detail) {
        let promotion_id = $(this).find('#promotion').attr('data-promotion_id');
        let promotion = promotions.find(obj => obj.id == promotion_id);
        if(promotion){
            let quantity = parseInt($(this).find('#quantity').val());
            let price_quantity = parseInt($(this).find('#price').attr('data-price'));
            let price_promotion = promotion.settings.value;
            let quantity_promotion = promotion.settings.quantity;
            let percentage = promotion.settings.percentage;
            let subtotal = parseInt($(this).find('#subtotal').attr('data-subtotal'));
            let discount = 0;

            if(promotion.apply_quantity && !promotion.apply_percentage) {
                let group_promotion = Math.floor(quantity / quantity_promotion);
                let remaining = quantity % quantity_promotion;

                discount = (group_promotion * price_promotion) + (remaining * price_quantity);

            } else {
                discount = (percentage * subtotal) / 100;
            }

            $(this).find('#discount').attr('data-discount', subtotal - discount);
            $(this).find('#discount').text(new Intl.NumberFormat('es-CO', { style: 'currency', currency: 'COP' }).format(subtotal - discount));

            $(this).find('#total').attr('data-total', discount);
            $(this).find('#total').text(new Intl.NumberFormat('es-CO', { style: 'currency', currency: 'COP' }).format(discount));
        }
    });
    IndexPOSCalculateInvoice();
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
    IndexPOSCalculateCashChange();
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
    $('#change').attr('data-change', cash - (total - other));
}

function IndexPOSReset() {
    $.each(payment_methods, function(index, payment_method) {
        $(`#${payment_method.settings.name.toLowerCase().replace(/\s+/g, '_')}`).val('');
        $(`#${payment_method.settings.name.toLowerCase().replace(/\s+/g, '_')}_check`).prop('checked', payment_method.settings.default).trigger('change').prop('disabled', payment_method.is_libranza);
    });


    $('#person_number_document').attr('data-person_id', '');
    $('#person_number_document').val('');
    $('#person_name').val('');
    $('#person_last_name').val('');
    $('#person_phone_number').val('');
    $('#person_email').val('');
    $('#person_address').val('');
    $('#employee_quota').val('');
    $('#employee_debt').val('');
    $('#employee_available').val('');

    $('#green, #red').hide();
    $(`#black`).show();

    $('#invoice_details').empty();
    IndexPOSCalculateCashChange();
}

function IndexPOSAjaxSuccess(response) {
    /*if(response.status === 200) {
        toastr.success(response.message);
    }*/

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
