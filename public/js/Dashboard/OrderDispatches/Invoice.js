function InvoiceOrderDispatchModal(id) {
    $.ajax({
        url: `/Dashboard/Dispatches/Show/${id}`,
        type: 'POST',
        data: {
            '_token': $('meta[name="csrf-token"]').attr('content'),
        },
        success: function(response) {
            InvoiceOrderDispatchModalCleaned(id);
            InvoiceOrderDispatchAjaxSuccess(response);
            $('#InvoiceOrderDispatchModal').modal('show');
        },
        error: function(xhr, textStatus, errorThrown) {
            InvoiceOrderDispatchAjaxError(xhr);
        }
    });
}

function InvoiceOrderDispatchModalCleaned(id) {
    RemoveIsInvalidClassInvoiceOrderDispatch();
    RemoveIsValidClassInvoiceOrderDispatch();
    $('.invoices_i').empty();
    $('#InvoiceOrderDispatchButton').attr('onclick', `InvoiceOrderDispatch(${id}, false)`);
    $('#InvoiceOrderDispatchButton').attr('data-count', 0);
    InvoiceOrderDispatchAdd();
}

function InvoiceOrderDispatchAdd() {
    
    let id = $('#InvoiceOrderDispatchButton').attr('data-count');

    let invoice = `<div id="group-invoice${id}" class="form-group invoice_i">
        <div class="card collapsed-card">
            <div class="card-header border-0 ui-sortable-handle">
                <h3 class="card-title mt-1" style="width: 70%;">
                    <div class="input-group">
                        <input type="text" class="form-control reference_i" id="reference${id}_i" name="reference${id}_i">
                        <div class="input-group-append">
                            <span class="input-group-text">
                                <i class="fas fa-paperclip"></i>
                            </span>
                        </div>
                    </div>
                </h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-info btn-sm ml-2 mt-2" data-card-widget="collapse">
                        <i class="fas fa-plus"></i>
                    </button>
                    <button type="button" class="btn btn-danger btn-sm ml-2 mt-2" data-card-widget="remove" onclick="InvoiceOrderDispatchRemove(${id})">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
            <div class="card-body table-responsive" style="display: none;">
                <div class="col-lg-12">
                    <div class="form-group">
                        <label for="">Soportes</label>
                        <input type="file" id="supports${id}_i" name="supports${id}_i" class="form-control dropify supports_i" accept=".jpeg, .jpg, .png, .gif, .pdf, .txt, .docx, .xlsx, .xlsm, .xlsb, .xltx" multiple>
                    </div>
                </div>
            </div>
        </div>
    </div>`;

    // Agregar el nuevo elemento al elemento con clase "subcategories_i"
    $('.invoices_i').append(invoice);

    $(`#supports${id}_i`).dropify();

    $('#InvoiceOrderDispatchButton').attr('data-count', ++id);
}

function InvoiceOrderDispatchRemove(index) {
    $(`#group-invoice${index}`).remove();
}

function InvoiceOrderDispatch(id, status = true) {

    let formData = new FormData();
    formData.append('_token', $('meta[name="csrf-token"]').attr('content'));
    formData.append('id', id);
    $('.invoices_i').find('div.invoice_i').each(function(i) {
        formData.append(`invoices[${i}][reference]`, $(this).find('input.reference_i').val());
        formData.append(`invoices[${i}][supports]`, []);
        $.each($(this).find('input.supports_i')[0].files, function(j, file) {
            formData.append(`invoices[${i}][supports][${j}]`, file);
        });
    });
    
    Swal.fire({
        title: '¿Desea guardar las facturas de la orden de despacho?',
        text: 'Las facturas de la orden de despacho serán guardadas.',
        icon: 'warning',
        showCancelButton: true,
        cancelButtonColor: '#DD6B55',
        confirmButtonColor: '#3085d6',
        confirmButtonText: 'Si, guardar!',
        cancelButtonText: 'No, cancelar!',
    }).then((result) => {
        if (result.value) {
            $.ajax({
                url: `/Dashboard/Dispatches/Invoice`,
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    window.open(response.data.urlDispatchDownload, '_blank');
                    InvoiceOrderDispatchAjaxSuccess(response);
                    status ? tableOrderDispatches.ajax.reload() : location.reload()
                },
                error: function(xhr, textStatus, errorThrown) {
                    InvoiceOrderDispatchAjaxError(xhr);
                    console.log(xhr);
                }
            });
        } else {
            toastr.info('Las facturas de la orden de despacho no fueron guardadas.')
        }
    });
}

function InvoiceOrderDispatchAjaxSuccess(response) {
    if(response.status === 201) {
        toastr.success(response.message);
        $('#InvoiceOrderDispatchModal').modal('hide');
    }
}

function InvoiceOrderDispatchAjaxError(xhr) {
    if(xhr.status === 403) {
        toastr.error(xhr.responseJSON.error ? xhr.responseJSON.error.message : xhr.responseJSON.message);
        $('#InvoiceOrderDispatchModal').modal('hide');
    }

    if(xhr.status === 404) {
        toastr.error(xhr.responseJSON.error ? xhr.responseJSON.error.message : xhr.responseJSON.message);
        $('#InvoiceOrderDispatchModal').modal('hide');
    }

    if(xhr.status === 419) {
        toastr.error(xhr.responseJSON.error ? xhr.responseJSON.error.message : xhr.responseJSON.message);
        $('#InvoiceOrderDispatchModal').modal('hide');
    }

    if(xhr.status === 422){
        RemoveIsValidClassInvoiceOrderDispatch();
        RemoveIsInvalidClassInvoiceOrderDispatch();
        $.each(xhr.responseJSON.errors, function(field, messages) {
            AddIsInvalidClassInvoiceOrderDispatch(field);
            $.each(messages, function(index, message) {
                toastr.error(message);
            });
        });
        AddIsValidClassInvoiceOrderDispatch();
    }

    if(xhr.status === 500){
        toastr.error(xhr.responseJSON.error ? xhr.responseJSON.error.message : xhr.responseJSON.message);
        $('#InvoiceOrderDispatchModal').modal('hide');
    }
}

function AddIsValidClassInvoiceOrderDispatch() {
    $('.invoices_i').find('div.invoice_i').each(function(index) {
        if (!$(this).find('input.reference_i').hasClass('is-invalid')) {
            $(this).find('input.reference_i').addClass('is-valid');
        }
    });
}

function RemoveIsValidClassInvoiceOrderDispatch() {
    $('.invoices_i').find('div.invoice_i').each(function(index) {
        $(this).find('input.reference_i').removeClass('is-valid');
    });
}

function AddIsInvalidClassInvoiceOrderDispatch(input) {
    $('.invoices_i').find('div.invoice_i').each(function(index) {
        // Agrega la clase 'is-invalid'
        if(input === `invoices.${index}.reference`) {
            if (!$(this).find('input.reference_i').hasClass('is-valid')) {
                $(this).find('input.reference_i').addClass('is-invalid');
            }
        }
    });
}

function RemoveIsInvalidClassInvoiceOrderDispatch() {
    $('.invoices_i').find('div.invoice_i').each(function(index) {
        $(this).find('input.reference_i').removeClass('is-invalid');
        $(this).find('input.value_i').removeClass('is-invalid');
        $(this).find('input.code_i').removeClass('is-invalid');
    });
}

