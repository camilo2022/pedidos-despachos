function ShowProductModal(id) {
    $.ajax({
        url: `/Dashboard/Products/Show/${id}`,
        type: 'POST',
        data: {
            '_token': $('meta[name="csrf-token"]').attr('content')
        },
        success: function(response) {
            ShowProductModalCleaned(response.data);
            $('#ShowProductModal').modal('show');
        },
        error: function(xhr, textStatus, errorThrown) {
            ShowProductAjaxError(xhr);
        }
    });
}

function ShowProductModalCleaned(data) {
    $('#ShowProductChargeButton').attr('onclick', `ShowProductCharge(${data.product.id})`);
    
    $('#photo_s').val('');
    $('#photo_s').dropify().data('dropify').destroy();
    $('#photo_s').dropify().data('dropify').init();
    
    $('#photos_s').val('');
    $('#photos_s').dropify().data('dropify').destroy();
    $('#photos_s').dropify().data('dropify').init();
    
    $('#videos_s').val('');
    $('#videos_s').dropify().data('dropify').destroy();
    $('#videos_s').dropify().data('dropify').init();
    
    let table = `<div class="table-responsive">
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
    $.each(data.product.files, function(j, file) {
        table += `<tr>
                <td>${file.id}</td>
                <td>${file.name}</td>
                <td>${file.type}</td>
                <td>${file.extension}</td>
                <td>${file.user.name.toUpperCase()} ${file.user.last_name.toUpperCase()}</td>
                <td>
                    <div class="text-center" style="width: 100px;">
                        <a href="${file.path}" target="_blank"
                        class="btn btn-info btn-sm mr-2" title="Ver archivo de producto.">
                            <i class="fas fa-eye text-white"></i>
                        </a>
                        <a onclick="ShowProductDestroy(${file.id}, ${data.product.id})" type="button" 
                        class="btn btn-danger btn-sm mr-2" title="Eliminar archivo de producto.">
                            <i class="fas fa-trash text-white"></i>
                        </a>
                    </div>
                </td>
            </tr>`;
    })
    table += `</tbody>
        </table>
    </div>`;
    
    $('#tableFile_s').html(table);
    
    $('#tableFile').DataTable({
        "lengthMenu": [ [5, 7, 10, 20], [5, 7, 10, 20] ],
        "pageLength": 5
    }); 
}

function ShowProductCharge(product_id) {
    let formData = new FormData();
    formData.append('_token', $('meta[name="csrf-token"]').attr('content'));
    formData.append('product_id', product_id);

    let photo = $('#photo_s')[0].files[0];
    if(photo) {
        formData.append('photo', photo);
    }

    let photos = $('#photos_s')[0].files;
    $.each(photos, function(index, photo) {
        formData.append('photos[]', photo);
    });

    let videos = $('#videos_s')[0].files;
    $.each(videos, function(index, video) {
        formData.append('videos[]', video);
    });

    Swal.fire({
        title: '¿Desea cargar los archivos al producto?',
        text: 'Los archivos se cargaran al producto.',
        icon: 'warning',
        showCancelButton: true,
        cancelButtonColor: '#DD6B55',
        confirmButtonColor: '#3085d6',
        confirmButtonText: 'Si, guardar!',
        cancelButtonText: 'No, cancelar!',
    }).then((result) => {
        if (result.value) {
            $.ajax({
                url: `/Dashboard/Products/Charge`,
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function (response) {
                    tableProducts.ajax.reload();
                    ShowProductAjaxSuccess(response);
                    ShowProductModal(product_id);
                },
                error: function (xhr, textStatus, errorThrown) {
                    ShowProductAjaxError(xhr);
                }
            });
        } else {
            toastr.info('Los archivos no se cargaron al producto.')
        }
    });
}

function ShowProductDestroy(id, product_id) {
    Swal.fire({
        title: '¿Desea eliminar el archivo del producto?',
        text: 'El archivo del producto será eliminado.',
        icon: 'warning',
        showCancelButton: true,
        cancelButtonColor: '#DD6B55',
        confirmButtonColor: '#3085d6',
        confirmButtonText: 'Si, eliminar!',
        cancelButtonText: 'No, cancelar!',
    }).then((result) => {
        if (result.value) {
            $.ajax({
                url: `/Dashboard/Products/Destroy`,
                type: 'DELETE',
                data: {
                    '_token': $('meta[name="csrf-token"]').attr('content'),
                    'id': id
                },
                success: function(response) {
                    tableProducts.ajax.reload();
                    ShowProductAjaxSuccess(response);
                    ShowProductModal(product_id);
                },
                error: function(xhr, textStatus, errorThrown) {
                    ShowProductAjaxError(xhr);
                }
            });
        } else {
            toastr.info('El archivo del producto seleccionado no fue eliminado.');
        }
    });
}

function ShowProductSize(size, product, checkbox) {
    if ($(checkbox).prop('checked')) {
        ShowProductAssignSize(size, product);
    } else {
        ShowProductRemoveSize(size, product);
    }
}

function ShowProductAssignSize(size, product) {
    $.ajax({
        url: `/Dashboard/Products/AssignSize`,
        type: 'POST',
        data: {
            '_token': $('meta[name="csrf-token"]').attr('content'),
            'size_id': size,
            'product_id': product,
        },
        success: function (response) {
            tableProducts.ajax.reload();
            ShowProductAjaxSuccess(response);
            ShowProductModal(product);
        },
        error: function (xhr, textStatus, errorThrown) {
            ShowProductAjaxError(xhr);
        }
    });
}

function ShowProductRemoveSize(size, product) {
    $.ajax({
        url: `/Dashboard/Products/RemoveSize`,
        type: 'DELETE',
        data: {
            '_token': $('meta[name="csrf-token"]').attr('content'),
            'size_id': size,
            'product_id': product,
        },
        success: function (response) {
            tableProducts.ajax.reload();
            ShowProductAjaxSuccess(response);
            ShowProductModal(product);
        },
        error: function (xhr, textStatus, errorThrown) {
            ShowProductAjaxError(xhr);
        }
    });
}

function ShowProductColorTone(color, tone, product, checkbox) {
    if ($(checkbox).prop('checked')) {
        ShowProductAssignColorTone(color, tone, product);
    } else {
        ShowProductRemoveColorTone(color, tone, product);
    }
}

function ShowProductAssignColorTone(color, tone, product) {
    $.ajax({
        url: `/Dashboard/Products/AssignColorTone`,
        type: 'POST',
        data: {
            '_token': $('meta[name="csrf-token"]').attr('content'),
            'color_id': color,
            'tone_id': tone,
            'product_id': product,
        },
        success: function (response) {
            tableProducts.ajax.reload();
            ShowProductAjaxSuccess(response);
            ShowProductModal(product);
        },
        error: function (xhr, textStatus, errorThrown) {
            ShowProductAjaxError(xhr);
        }
    });
}

function ShowProductRemoveColorTone(color, tone, product) {
    $.ajax({
        url: `/Dashboard/Products/RemoveColorTone`,
        type: 'DELETE',
        data: {
            '_token': $('meta[name="csrf-token"]').attr('content'),
            'color_id': color,
            'tone_id': tone,
            'product_id': product,
        },
        success: function (response) {
            tableProducts.ajax.reload();
            ShowProductAjaxSuccess(response);
            ShowProductModal(product);
        },
        error: function (xhr, textStatus, errorThrown) {
            ShowProductAjaxError(xhr);
        }
    });
}

function ShowProductAjaxSuccess(response) {
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

    if(response.status === 200) {
        toastr.success(response.message);
    }
}

function ShowProductAjaxError(xhr) {
    if(xhr.status === 403) {
        toastr.error(xhr.responseJSON.error ? xhr.responseJSON.error.message : xhr.responseJSON.message);
        $('#ShowProductModal').modal('hide');
    }

    if(xhr.status === 404) {
        toastr.error(xhr.responseJSON.error ? xhr.responseJSON.error.message : xhr.responseJSON.message);
        $('#ShowProductModal').modal('hide');
    }

    if(xhr.status === 419) {
        toastr.error(xhr.responseJSON.error ? xhr.responseJSON.error.message : xhr.responseJSON.message);
        $('#ShowProductModal').modal('hide');
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
        $('#ShowProductModal').modal('hide');
    }
}
