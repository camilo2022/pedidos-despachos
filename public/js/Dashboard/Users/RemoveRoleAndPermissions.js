function RemoveRoleAndPermissionUserModal(id, email) {
    $.ajax({
        url: `/Dashboard/Users/RemoveRoleAndPermissions/Query`,
        type: 'POST',
        data: {
            '_token': $('meta[name="csrf-token"]').attr('content'),
            'id': id
        },
        success: function(response) {
            if($('meta[name="user-id"]').attr('content') == id) {
                toastr.warning('Cuidado, vas a remover el rol y los permisos de tu usuario.');
            }

            if (response.data.length === 0) {
                $('#RemoveRoleAndPermissionUserModal').modal('hide');
                toastr.info('No tiene roles y permisos asignados para remover.');
                return false;
            }

            $('#email_r').val(email);
            $('#permissions-container-assign').empty();
            $('#permissions-container-remove').empty();
            $.each(response.data, function (index, item) {
                // Crear el div del card
                let card = $('<div>').addClass('card collapsed-card');

                // Crear el div del card-header
                let cardHeader = $('<div>').addClass('card-header border-0 ui-sortable-handle');
                let cardTitle = $('<h3>').addClass('card-title mt-1');
                let cardIcon = $('<i>').addClass('fas fa-shield-xmark fa-lg mr-1');

                cardTitle.append(cardIcon);
                cardTitle.append(item.role);

                // Crear el div de card-tools
                let cardTools = $('<div>').addClass('card-tools');

                let saveButton = $('<button>').attr({
                    'type': 'button',
                    'class': 'btn btn-primary btn-sm',
                    'title': 'Remover rol y permisos.'
                }).append($('<i>').addClass('fas fa-floppy-disk'));

                let collapseButton = $('<button>').attr({
                    'type': 'button',
                    'class': 'btn btn-info btn-sm ml-2',
                    'data-card-widget': 'collapse'
                }).append($('<i>').addClass('fas fa-plus'));

                let removeButton = $('<button>').attr({
                    'type': 'button',
                    'class': 'btn btn-danger btn-sm ml-2',
                    'data-card-widget': 'remove'
                }).append($('<i>').addClass('fas fa-times'));

                // Agregar elementos al cardHeader
                cardTools.append(saveButton);
                cardTools.append(collapseButton);
                cardTools.append(removeButton);
                cardHeader.append(cardTitle);
                cardHeader.append(cardTools);

                // Crear el div del card-body
                let cardBody = $('<div>').addClass('card-body').addClass('table-responsive').css('display', 'none');

                // Crear el div para checkboxes
                let checkboxesDiv = $('<div>').addClass('row icheck-primary');

                let selectAllCheckbox = $('<input>').attr({
                    'type': 'checkbox',
                    'id': `selectAllCheckbox${index}`
                });

                let selectAllLabel = $('<label>').text('Seleccionar todos los permisos').attr({
                    'for': `selectAllCheckbox${index}`
                });

                selectAllCheckbox.change(function() {
                    let checkboxes = cardBody.find('input[type="checkbox"]');
                    checkboxes.prop('checked', selectAllCheckbox.prop('checked'));
                });

                // Agregar elementos al cardBody

                checkboxesDiv.append(selectAllCheckbox);
                checkboxesDiv.append(selectAllLabel);
                checkboxesDiv.append('<br>');
                cardBody.append(checkboxesDiv);

                // Crear checkboxes para permisos
                $.each(item.permissions, function (i, permission) {
                    let permissionDiv = $('<div>').addClass('row pl-2 icheck-primary');

                    let permissionCheckbox = $(`<input>`).attr({
                        'type': 'checkbox',
                        'id': permission
                    });

                    let permissionLabel = $('<label>').text(permission).attr({
                        'for': permission,
                        'class': 'mt-3 ml-3'
                    });

                    // Agregar elementos al cardBody
                    permissionDiv.append(permissionCheckbox);
                    permissionDiv.append(permissionLabel);
                    cardBody.append(permissionDiv);
                });

                // Agregar evento click al botón de guardar
                saveButton.click(function() {
                    var selectedPermissions = [];
                    cardBody.find('input[type="checkbox"]:checked').each(function() {
                        if($(this).attr('id') !== `selectAllCheckbox${index}`) {
                            selectedPermissions.push($(this).attr('id'));
                        }
                    });

                    // Llamar a la función RemoveRoleAndPermission con el nombre del rol y los permisos
                    RemoveRoleAndPermissionUser(id, item.role, selectedPermissions, email);
                });

                // Agregar cardHeader, cardBody y cardFooter al card
                card.append(cardHeader);
                card.append(cardBody);

                // Agregar el card al contenedor
                $('#permissions-container-remove').append(card);

                // Mostrar el modal
                $('#RemoveRoleAndPermissionUserModal').modal('show');
            });
        },
        error: function(xhr, textStatus, errorThrown) {
            RemoveRoleAndPermissionUserAjaxError(xhr);
        }
    });
}

function RemoveRoleAndPermissionUser(id, role, permissions, email) {
    if($('meta[name="user-id"]').attr('content') === id) {
        toastr.warning('Cuidado, vas a remover el rol y los permisos de tu usuario.');
    }
    Swal.fire({
        title: '¿Desea remover el rol y los permisos al usuario?',
        text: 'Se removera al usuario el rol y los permisos especificados.',
        icon: 'warning',
        showCancelButton: true,
        cancelButtonColor: '#DD6B55',
        confirmButtonColor: '#3085d6',
        confirmButtonText: 'Si, remover!',
        cancelButtonText: 'No, cancelar!',
    }).then((result) => {
        if (result.value) {
            $.ajax({
                url: `/Dashboard/Users/RemoveRoleAndPermissions`,
                type: 'POST',
                data: {
                    '_token': $('meta[name="csrf-token"]').attr('content'),
                    'id': id,
                    'role': role,
                    'permissions': permissions
                },
                success: function(response) {
                    tableUsers.ajax.reload();
                    RemoveRoleAndPermissionUserAjaxSuccess(response);
                },
                error: function(xhr, textStatus, errorThrown) {
                    RemoveRoleAndPermissionUserAjaxError(xhr);
                }
            });
        } else {
            toastr.info('El rol y los permisos no fueron removidos al usuario.')
        }
    });
}


function RemoveRoleAndPermissionUserAjaxSuccess(response) {
    if(response.status === 200) {
        toastr.success(response.message);
        $('#RemoveRoleAndPermissionUserModal').modal('hide');
    }

    if(response.status === 204) {
        toastr.info(response.message);
        $('#PasswordUserModal').modal('hide');
    }
}

function RemoveRoleAndPermissionUserAjaxError(xhr) {
    if(xhr.status === 403) {
        toastr.error(xhr.responseJSON.error ? xhr.responseJSON.error.message : xhr.responseJSON.message);
        $('#RemoveRoleAndPermissionUserModal').modal('hide');
    }

    if(xhr.status === 404) {
        toastr.error(xhr.responseJSON.error ? xhr.responseJSON.error.message : xhr.responseJSON.message);
        $('#RemoveRoleAndPermissionUserModal').modal('hide');
    }

    if(xhr.status === 419) {
        toastr.error(xhr.responseJSON.error ? xhr.responseJSON.error.message : xhr.responseJSON.message);
        $('#RemoveRoleAndPermissionUserModal').modal('hide');
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
        $('#RemoveRoleAndPermissionUserModal').modal('hide');
    }
}
