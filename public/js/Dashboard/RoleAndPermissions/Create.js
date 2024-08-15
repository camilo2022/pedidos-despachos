function CreateRoleAndPermissionsModal() {
    $.ajax({
        url: `/Dashboard/RolesAndPermissions/Create`,
        type: 'POST',
        data: {
            '_token': $('meta[name="csrf-token"]').attr('content')
        },
        success: function(response) {
            CreateRoleAndPermissionsModalCleaned();
            CreateRoleAndPermissionsAjaxSuccess(response);
            $('#CreateRoleAndPermissionsModal').modal('show');
        },
        error: function(xhr, textStatus, errorThrown) {
            CreateRoleAndPermissionsAjaxError(xhr);
        }
    });
}

function CreateRoleAndPermissionsModalCleaned() {
    RemoveIsValidClassCreateRoleAndPermissions();
    RemoveIsInvalidClassCreateRoleAndPermissions();

    $('#role_c').val('');
    $('.permissions_c').empty();

    let permissionGroup = $('<div>').addClass('form-group');
    let inputGroup = $('<div>').addClass('input-group');
    let input = $('<input>').attr({
        'type': 'text',
        'class': 'form-control',
        'id': 'permission_c0',
        'name': 'permissions_c[]',
        'placeholder': 'Permiso'
    });
    inputGroup.append(input);
    permissionGroup.append(inputGroup);

    $('.permissions_c').append(permissionGroup);
}

function CreateRoleAndPermissions() {
    Swal.fire({
        title: '¿Desea guardar el rol y los permisos?',
        text: 'El rol y los permisos serán creados.',
        icon: 'warning',
        showCancelButton: true,
        cancelButtonColor: '#DD6B55',
        confirmButtonColor: '#3085d6',
        confirmButtonText: 'Si, guardar!',
        cancelButtonText: 'No, cancelar!',
    }).then((result) => {
        if (result.value) {
            $.ajax({
                url: `/Dashboard/RolesAndPermissions/Store`,
                type: 'POST',
                data: {
                    '_token': $('meta[name="csrf-token"]').attr('content'),
                    'role': $('#role_c').val(),
                    'permissions': $('input[name="permissions_c[]"]').map(function() {
                        return $(this).val();
                    }).get()
                },
                success: function(response) {
                    tableRolesAndPermissions.ajax.reload();
                    CreateRoleAndPermissionsAjaxSuccess(response);
                },
                error: function(xhr, textStatus, errorThrown) {
                    CreateRoleAndPermissionsAjaxError(xhr);
                }
            });
        } else {
            toastr.info('El rol y los permisos no fueron creados.')
        }
    });
}

function CreateRoleAndPermissionsAddPermission(permission) {
    let permissionCount = $(permission).data('count');

    let newPermissionGroup = $('<div>').addClass('form-group permission-group');
    let inputGroup = $('<div>').addClass('input-group');

    let input = $('<input>').attr({
        'type': 'text',
        'class': 'form-control',
        'id': `permission_c${permissionCount}`,
        'name': 'permissions_c[]',
        'placeholder': 'Permiso'
    });

    let inputGroupAppend = $('<div>').addClass('input-group-append');

    let span = $('<span>').attr({
        'class': 'input-group-text bg-red permission-toggle',
        'data-id': permissionCount,
        'onclick': 'CreateRoleAndPermissionsRemovePermission(this)'
    });

    let icon = $('<i>').addClass('fas fa-minus');

    // Construir la estructura de elementos
    span.append(icon);
    inputGroupAppend.append(span);
    inputGroup.append(input, inputGroupAppend);
    newPermissionGroup.append(inputGroup);

    // Agregar al contenedor
    $('.permissions_c').append(newPermissionGroup);
    permissionCount++;
    $(permission).data('count', permissionCount);
}

function CreateRoleAndPermissionsRemovePermission(permission) {
    let permissionId = $(permission).data('id');
    let permissionGroup = $(`#permission_c${permissionId}`).closest('.permission-group');
    permissionGroup.remove();
}

function CreateRoleAndPermissionsAjaxSuccess(response) {
    if(response.status === 204) {
        toastr.info(response.message);
        $('#CreateRoleAndPermissionsModal').modal('hide');
    }

    if(response.status === 201) {
        toastr.success(response.message);
        $('#CreateRoleAndPermissionsModal').modal('hide');
    }
}

function CreateRoleAndPermissionsAjaxError(xhr) {
    if(xhr.status === 403) {
        toastr.error(xhr.responseJSON.error ? xhr.responseJSON.error.message : xhr.responseJSON.message);
        $('#CreateRoleAndPermissionsModal').modal('hide');
    }

    if(xhr.status === 404) {
        toastr.error(xhr.responseJSON.error ? xhr.responseJSON.error.message : xhr.responseJSON.message);
        $('#CreateRoleAndPermissionsModal').modal('hide');
    }

    if(xhr.status === 419) {
        toastr.error(xhr.responseJSON.error ? xhr.responseJSON.error.message : xhr.responseJSON.message);
        $('#CreateRoleAndPermissionsModal').modal('hide');
    }

    if(xhr.status === 422){
        RemoveIsValidClassCreateRoleAndPermissions();
        RemoveIsInvalidClassCreateRoleAndPermissions();
        $.each(xhr.responseJSON.errors, function(field, messages) {
            AddIsInvalidClassCreateRoleAndPermissions(field);
            $.each(messages, function(index, message) {
                toastr.error(message);
            });
        });
        AddIsValidClassCreateRoleAndPermissions();
    }

    if(xhr.status === 500){
        toastr.error(xhr.responseJSON.error ? xhr.responseJSON.error.message : xhr.responseJSON.message);
        $('#CreateRoleAndPermissionsModal').modal('hide');
    }
}

function AddIsValidClassCreateRoleAndPermissions() {
    if (!$('#role_c').hasClass('is-invalid')) {
      $('#role_c').addClass('is-valid');
    }

    // Itera sobre los inputs dentro del div
    $('.permissions_c').find('input').each(function() {

        // Verifica si el input no tiene la clase 'is-invalid'
        if (!$(this).hasClass('is-invalid')) {
            // Agrega la clase 'is-valid'
            $(this).addClass('is-valid');
        }
    });
}

function RemoveIsValidClassCreateRoleAndPermissions() {
    $('#role_c').removeClass('is-valid');

    // Itera sobre los inputs dentro del div
    $('.permissions_c').find('input').each(function() {
        // Agrega la clase 'is-valid'
        $(this).removeClass('is-valid');
    });
}

function AddIsInvalidClassCreateRoleAndPermissions(input) {
    if (!$(`#${input}_c`).hasClass('is-valid')) {
        $(`#${input}_c`).addClass('is-invalid');
    }
    $('.permissions_c').find('input').each(function(index) {
        // Agrega la clase 'is-invalid'
        if(input === `permissions.${index}`) {
            $(this).addClass('is-invalid');
        }
    });
}

function RemoveIsInvalidClassCreateRoleAndPermissions() {
    $('#role_c').removeClass('is-invalid');

    // Itera sobre los inputs dentro del div
    $('.permissions_c').find('input').each(function() {
        // Remover la clase 'is-invalid'
        $(this).removeClass('is-invalid');
    });
}
