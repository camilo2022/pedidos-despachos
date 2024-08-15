function DataClientModal(id) {
    $.ajax({
        url: `/Dashboard/Clients/Show/${id}`,
        type: 'POST',
        data: {
            '_token': $('meta[name="csrf-token"]').attr('content')
        },
        success: function (response) {
            DataClientModalCleaned(response.data.client);
            DataClientAjaxSuccess(response);
            $('#DataClientModal').modal('show');
        },
        error: function (xhr, textStatus, errorThrown) {
            DataClientAjaxError(xhr);
        }
    });
}

function DataClientModalCleaned(client, status = true) {
    RemoveIsValidClassDataClient();
    RemoveIsInvalidClassDataClient();

    status ? '' : $('.close_modal').attr('onclick', "$('#CreateOrderModal, #EditOrderModal').modal('hide')") ;

    $('#DataClientButton').attr('onclick', `DataClient(${client.id}, ${status})`);
    $('#DataClientButton').attr('data-id', client.id);
    $('#div_tableFiles_d').empty();
    
    $('#compra_name_d').val(client.compra ? client.compra.name : '');
    $('#compra_last_name_d').val(client.compra ? client.compra.last_name : '');
    $('#compra_phone_number_d').val(client.compra ? client.compra.phone_number : '');
    $('#compra_email_d').val(client.compra ? client.compra.email : '');

    $('#cartera_name_d').val(client.cartera ? client.cartera.name : '');
    $('#cartera_last_name_d').val(client.cartera ? client.cartera.last_name : '');
    $('#cartera_phone_number_d').val(client.cartera ? client.cartera.phone_number : '');
    $('#cartera_email_d').val(client.cartera ? client.cartera.email : '');
    
    $('#bodega_name_d').val(client.bodega ? client.bodega.name : '');
    $('#bodega_last_name_d').val(client.bodega ? client.bodega.last_name : '');
    $('#bodega_phone_number_d').val(client.bodega ? client.bodega.phone_number : '');
    $('#bodega_email_d').val(client.bodega ? client.bodega.email : '');
    
    $('#administrador_name_d').val(client.administrador ? client.administrador.name : '');
    $('#administrador_last_name_d').val(client.administrador ? client.administrador.last_name : '');
    $('#administrador_phone_number_d').val(client.administrador ? client.administrador.phone_number : '');
    $('#administrador_email_d').val(client.administrador ? client.administrador.email : '');

    client.compra ? '' : toastr.warning('Debe registrarle al cliente una referencia personal/comercial de tipo compra.') ;
    client.cartera ? '' : toastr.warning('Debe registrarle al cliente una referencia personal/comercial de tipo cartera.') ;
    client.bodega ? '' : toastr.warning('Debe registrarle al cliente una referencia personal/comercial de tipo bodega.') ;
    client.administrador ? '' : toastr.warning('Debe registrarle al cliente una referencia personal/comercial de tipo administrador.') ;

    client.chamber_of_commerce ? '' : toastr.warning('Debe registrarle al cliente el documento de camara de comercio.') ;
    client.rut ? '' : toastr.warning('Debe registrarle al cliente el documento rut.') ;
    client.identity_card ? '' : toastr.warning('Debe registrarle al cliente el documento de identificacion.') ;
    client.signature_warranty ? '' : toastr.warning('Debe registrarle al cliente el documento firma de garantia.') ;

    if(status) {
        let tableReference = `<div class="table-responsive form-group c_form_group">
            <label for="tableReference">REFERENCIAS DEL CLIENTE</label>
            <table id="tableReference" class="table table-bordered table-hover dataTable dtr-inline nowrap w-100">
                <thead class="thead-dark">
                    <tr>
                        <th>#</th>
                        <th>TIPO</th>
                        <th>NOMBRE</th>
                        <th>APELLIDO</th>
                        <th>TELEFONO</th>
                        <th>CORREO</th>
                        <th>ACCIONES</th>
                    </tr>
                </thead>
                <tbody>`;
        let references = ['compra', 'cartera', 'bodega', 'administrador'];
        $.each(references, function(j, reference) {
            if(client[reference]) {
                tableReference += `<tr>
                        <td>${client[reference].id}</td>
                        <td>${client[reference].type}</td>
                        <td>${client[reference].name}</td>
                        <td>${client[reference].last_name}</td>
                        <td>${client[reference].phone_number}</td>
                        <td>${client[reference].email}</td>
                        <td>
                            <div class="text-center" style="width: 100px;">
                                <a onclick="DataClientRemove(${client[reference].id}, ${client.id})" type="button" 
                                class="btn btn-danger btn-sm mr-2" title="Eliminar referencia de cliente.">
                                    <i class="fas fa-trash text-white"></i>
                                </a>
                            </div>
                        </td>
                    </tr>`;
            }
        })
        tableReference += `</tbody>
            </table>
        </div>`;
        
        $('#div_tableReferences_d').html(tableReference);
        
        $('#tableReference').DataTable({
            "lengthMenu": [ [5, 7, 10, 20], [5, 7, 10, 20] ],
            "pageLength": 5
        }); 

        let tableFile = `<div class="table-responsive form-group c_form_group">
            <label for="tableFile">ARCHIVOS DEL CLIENTE</label>
            <table id="tableFile" class="table table-bordered table-hover dataTable dtr-inline nowrap w-100">
                <thead class="thead-dark">
                    <tr>
                        <th>#</th>
                        <th>NOMBRE</th>
                        <th>TIPO</th>
                        <th>ESTENSION</th>
                        <th>USUARIO</th>
                        <th>ACCIONES</th>
                    </tr>
                </thead>
                <tbody>`;
        let documents = ['chamber_of_commerce', 'rut', 'identity_card', 'signature_warranty'];
        $.each(documents, function(j, document) {
            if(client[document]) {
                tableFile += `<tr>
                        <td>${client[document].id}</td>
                        <td>${client[document].name}</td>
                        <td>${client[document].type}</td>
                        <td>${client[document].extension}</td>
                        <td>${client[document].user.name.toUpperCase()} ${client[document].user.last_name.toUpperCase()}</td>
                        <td>
                            <div class="text-center" style="width: 100px;">
                                <a href="${client[document].path}" target="_blank"
                                class="btn btn-info btn-sm mr-2" title="Ver archivo de cliente.">
                                    <i class="fas fa-eye text-white"></i>
                                </a>
                                <a onclick="DataClientDestroy(${client[document].id}, ${client.id})" type="button" 
                                class="btn btn-danger btn-sm mr-2" title="Eliminar archivo de cliente.">
                                    <i class="fas fa-trash text-white"></i>
                                </a>
                            </div>
                        </td>
                    </tr>`;
            }
        })
        tableFile += `</tbody>
            </table>
        </div>`;
        
        $('#div_tableFiles_d').html(tableFile);
        
        $('#tableFile').DataTable({
            "lengthMenu": [ [5, 7, 10, 20], [5, 7, 10, 20] ],
            "pageLength": 5
        }); 
    } else {
        !client.chamber_of_commerce && (client.type == 'CREDITO' || !client.type) ? $('#div_chamber_of_commerce_d').show() : $('#div_chamber_of_commerce_d').hide() ;
        !client.rut && (client.type == 'CREDITO' || !client.type) ? $('#div_rut_d').show() : $('#div_rut_d').hide() ;
        !client.identity_card ? $('#div_identity_card_d').show() : $('#div_identity_card_d').hide();
        !client.signature_warranty && (client.type == 'CREDITO' || !client.type) ? $('#div_signature_warranty_d').show() : $('#div_signature_warranty_d').hide() ;
    }

    $('#chamber_of_commerce_d').val('');
    $('#chamber_of_commerce_d').dropify().data('dropify').destroy();
    $('#chamber_of_commerce_d').dropify().data('dropify').init();
    
    $('#rut_d').val('');
    $('#rut_d').dropify().data('dropify').destroy();
    $('#rut_d').dropify().data('dropify').init();
    
    $('#identity_card_d').val('');
    $('#identity_card_d').dropify().data('dropify').destroy();
    $('#identity_card_d').dropify().data('dropify').init();
    
    $('#signature_warranty_d').val('');
    $('#signature_warranty_d').dropify().data('dropify').destroy();
    $('#signature_warranty_d').dropify().data('dropify').init();
}

function DataClientModalResetReference(actually, other) {
    $(`#${actually}_name_d`).val($(`#${other}_name_d`).val())
    $(`#${actually}_last_name_d`).val($(`#${other}_last_name_d`).val())
    $(`#${actually}_phone_number_d`).val($(`#${other}_phone_number_d`).val())
    $(`#${actually}_email_d`).val($(`#${other}_email_d`).val())
}

function DataClient(client_id, status = true) {
    let formData = new FormData();
    formData.append('_token', $('meta[name="csrf-token"]').attr('content'));
    formData.append('client_id', client_id);

    let compra = {
        name: $('#compra_name_d').val(),
        last_name: $('#compra_last_name_d').val(),
        phone_number: $('#compra_phone_number_d').val(),
        email: $('#compra_email_d').val()
    };
    formData.append('compra', JSON.stringify(compra));
    
    let cartera = {
        name: $('#cartera_name_d').val(),
        last_name: $('#cartera_last_name_d').val(),
        phone_number: $('#cartera_phone_number_d').val(),
        email: $('#cartera_email_d').val()
    };
    formData.append('cartera', JSON.stringify(cartera));

    let bodega = {
        name: $('#bodega_name_d').val(),
        last_name: $('#bodega_last_name_d').val(),
        phone_number: $('#bodega_phone_number_d').val(),
        email: $('#bodega_email_d').val()
    };
    formData.append('bodega', JSON.stringify(bodega));

    let administrador = {
        name: $('#administrador_name_d').val(),
        last_name: $('#administrador_last_name_d').val(),
        phone_number: $('#administrador_phone_number_d').val(),
        email: $('#administrador_email_d').val()
    };
    formData.append('administrador', JSON.stringify(administrador));

    let chamber_of_commerce = $('#chamber_of_commerce_d')[0].files[0];
    if(chamber_of_commerce) {
        formData.append('chamber_of_commerce', chamber_of_commerce);
    }

    let rut = $('#rut_d')[0].files[0];
    if(rut) {
        formData.append('rut', rut);
    }

    let identity_card = $('#identity_card_d')[0].files[0];
    if(identity_card) {
        formData.append('identity_card', identity_card);
    }

    let signature_warranty = $('#signature_warranty_d')[0].files[0];
    if(signature_warranty) {
        formData.append('signature_warranty', signature_warranty);
    }

    Swal.fire({
        title: '¿Desea guardar las referencias peronsales/comerciales y los archivos del cliente?',
        text: 'Las referencias peronsales/comerciales y los archivos del cliente se guardaran.',
        icon: 'warning',
        showCancelButton: true,
        cancelButtonColor: '#DD6B55',
        confirmButtonColor: '#3085d6',
        confirmButtonText: 'Si, guardar!',
        cancelButtonText: 'No, cancelar!',
    }).then((result) => {
        if (result.value) {
            $.ajax({
                url: `/Dashboard/Clients/Data`,
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function (response) {
                    status ? tableClients.ajax.reload() : '' ;
                    DataClientAjaxSuccess(response);
                },
                error: function (xhr, textStatus, errorThrown) {
                    DataClientAjaxError(xhr);
                }
            });
        } else {
            toastr.info('Las referencias peronsales/comerciales y los archivos del cliente no fueron guardados.')
        }
    });
}

function DataClientRemove(id, client_id) {
    Swal.fire({
        title: '¿Desea remover la referencia personal/comercial del cliente?',
        text: 'La referencia personal/comercial del cliente será removida.',
        icon: 'warning',
        showCancelButton: true,
        cancelButtonColor: '#DD6B55',
        confirmButtonColor: '#3085d6',
        confirmButtonText: 'Si, remover!',
        cancelButtonText: 'No, cancelar!',
    }).then((result) => {
        if (result.value) {
            $.ajax({
                url: `/Dashboard/Clients/Remove`,
                type: 'DELETE',
                data: {
                    '_token': $('meta[name="csrf-token"]').attr('content'),
                    'id': id
                },
                success: function(response) {
                    tableClients.ajax.reload();
                    DataClientAjaxSuccess(response);
                    DataClientModal(client_id);
                },
                error: function(xhr, textStatus, errorThrown) {
                    DataClientAjaxError(xhr);
                }
            });
        } else {
            toastr.info('La referencia personal/comercial del cliente seleccionado no fue removida.');
        }
    });
}

function DataClientDestroy(id, client_id) {
    Swal.fire({
        title: '¿Desea eliminar el archivo del cliente?',
        text: 'El archivo del cliente será eliminado.',
        icon: 'warning',
        showCancelButton: true,
        cancelButtonColor: '#DD6B55',
        confirmButtonColor: '#3085d6',
        confirmButtonText: 'Si, eliminar!',
        cancelButtonText: 'No, cancelar!',
    }).then((result) => {
        if (result.value) {
            $.ajax({
                url: `/Dashboard/Clients/Destroy`,
                type: 'DELETE',
                data: {
                    '_token': $('meta[name="csrf-token"]').attr('content'),
                    'id': id
                },
                success: function(response) {
                    tableClients.ajax.reload();
                    DataClientAjaxSuccess(response);
                    DataClientModal(client_id);
                },
                error: function(xhr, textStatus, errorThrown) {
                    DataClientAjaxError(xhr);
                }
            });
        } else {
            toastr.info('El archivo del cliente seleccionado no fue eliminado.');
        }
    });
}

function DataClientAjaxSuccess(response) {
    if(response.data.messages) {
        $.each(response.data.messages.success, function(index, success) {
            toastr.success(success);
        });
        $.each(response.data.messages.warning, function(index, warning) {
            toastr.warning(warning);
        });
        $.each(response.data.messages.error, function(index, error) {
            toastr.error(error);
        });
    } 
    if (response.status === 204) {
        toastr.info(response.message);
        $('#DataClientModal').modal('hide');
    }

    if (response.status === 200) {
        toastr.success(response.message);
        $('#DataClientModal').modal('hide');
    }
}

function DataClientAjaxError(xhr) {
    if (xhr.status === 403) {
        toastr.error(xhr.responseJSON.error ? xhr.responseJSON.error.message : xhr.responseJSON.message);
        $('#DataClientModal').modal('hide');
    }

    if (xhr.status === 404) {
        toastr.error(xhr.responseJSON.error ? xhr.responseJSON.error.message : xhr.responseJSON.message);
        $('#DataClientModal').modal('hide');
    }

    if (xhr.status === 419) {
        toastr.error(xhr.responseJSON.error ? xhr.responseJSON.error.message : xhr.responseJSON.message);
        $('#DataClientModal').modal('hide');
    }

    if (xhr.status === 422) {
        RemoveIsValidClassDataClient();
        RemoveIsInvalidClassDataClient();
        $.each(xhr.responseJSON.errors, function (field, messages) {
            AddIsInvalidClassDataClient(field);
            $.each(messages, function (index, message) {
                toastr.error(message);
            });
        });
        AddIsValidClassDataClient();
    }

    if (xhr.status === 500) {
        toastr.error(xhr.responseJSON.error ? xhr.responseJSON.error.message : xhr.responseJSON.message);
        $('#DataClientModal').modal('hide');
    }
}

function AddIsValidClassDataClient() {
    if (!$('#compra_name_d').hasClass('is-invalid')) {
        $('#compra_name_d').addClass('is-valid');
    }
    if (!$('#compra_last_name_d').hasClass('is-invalid')) {
        $('#compra_last_name_d').addClass('is-valid');
    }
    if (!$('#compra_phone_number_d').hasClass('is-invalid')) {
        $('#compra_phone_number_d').addClass('is-valid');
    }
    if (!$('#compra_email_d').hasClass('is-invalid')) {
        $('#compra_email_d').addClass('is-valid');
    }    
    if (!$('#cartera_name_d').hasClass('is-invalid')) {
        $('#cartera_name_d').addClass('is-valid');
    }
    if (!$('#cartera_last_name_d').hasClass('is-invalid')) {
        $('#cartera_last_name_d').addClass('is-valid');
    }
    if (!$('#cartera_phone_number_d').hasClass('is-invalid')) {
        $('#cartera_phone_number_d').addClass('is-valid');
    }
    if (!$('#cartera_email_d').hasClass('is-invalid')) {
        $('#cartera_email_d').addClass('is-valid');
    }    
    if (!$('#bodega_name_d').hasClass('is-invalid')) {
        $('#bodega_name_d').addClass('is-valid');
    }
    if (!$('#bodega_last_name_d').hasClass('is-invalid')) {
        $('#bodega_last_name_d').addClass('is-valid');
    }
    if (!$('#bodega_phone_number_d').hasClass('is-invalid')) {
        $('#bodega_phone_number_d').addClass('is-valid');
    }
    if (!$('#bodega_email_d').hasClass('is-invalid')) {
        $('#bodega_email_d').addClass('is-valid');
    }   
    if (!$('#administrador_name_d').hasClass('is-invalid')) {
        $('#administrador_name_d').addClass('is-valid');
    }
    if (!$('#administrador_last_name_d').hasClass('is-invalid')) {
        $('#administrador_last_name_d').addClass('is-valid');
    }
    if (!$('#administrador_phone_number_d').hasClass('is-invalid')) {
        $('#administrador_phone_number_d').addClass('is-valid');
    }
    if (!$('#administrador_email_d').hasClass('is-invalid')) {
        $('#administrador_email_d').addClass('is-valid');
    }
}

function RemoveIsValidClassDataClient() {
    $('#compra_name_d').removeClass('is-valid');
    $('#compra_last_name_d').removeClass('is-valid');
    $('#compra_phone_number_d').removeClass('is-valid');
    $('#compra_email_d').removeClass('is-valid');
    $('#cartera_name_d').removeClass('is-valid');
    $('#cartera_last_name_d').removeClass('is-valid');
    $('#cartera_phone_number_d').removeClass('is-valid');
    $('#cartera_email_d').removeClass('is-valid');
    $('#bodega_name_d').removeClass('is-valid');
    $('#bodega_last_name_d').removeClass('is-valid');
    $('#bodega_phone_number_d').removeClass('is-valid');
    $('#bodega_email_d').removeClass('is-valid');
    $('#administrador_name_d').removeClass('is-valid');
    $('#administrador_last_name_d').removeClass('is-valid');
    $('#administrador_phone_number_d').removeClass('is-valid');
    $('#administrador_email_d').removeClass('is-valid');
}

function AddIsInvalidClassDataClient(input) {
    if (!$(`#${input.replace(/\./g, '_')}_d`).hasClass('is-valid')) {
        $(`#${input.replace(/\./g, '_')}_d`).addClass('is-invalid');
    }
}

function RemoveIsInvalidClassDataClient() {
    $('#compra_name_d').removeClass('is-invalid');
    $('#compra_last_name_d').removeClass('is-invalid');
    $('#compra_phone_number_d').removeClass('is-invalid');
    $('#compra_email_d').removeClass('is-invalid');
    $('#cartera_name_d').removeClass('is-invalid');
    $('#cartera_last_name_d').removeClass('is-invalid');
    $('#cartera_phone_number_d').removeClass('is-invalid');
    $('#cartera_email_d').removeClass('is-invalid');
    $('#bodega_name_d').removeClass('is-invalid');
    $('#bodega_last_name_d').removeClass('is-invalid');
    $('#bodega_phone_number_d').removeClass('is-invalid');
    $('#bodega_email_d').removeClass('is-invalid');
    $('#administrador_name_d').removeClass('is-invalid');
    $('#administrador_last_name_d').removeClass('is-invalid');
    $('#administrador_phone_number_d').removeClass('is-invalid');
    $('#administrador_email_d').removeClass('is-invalid');
}
