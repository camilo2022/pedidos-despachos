function CreateModuleAndSubmodulesModal() {
    $.ajax({
        url: `/Dashboard/ModulesAndSubmodules/Create`,
        type: 'POST',
        data: {
            '_token': $('meta[name="csrf-token"]').attr('content'),
        },
        success: function(response) {
            CreateModuleAndSubmodulesModalCleaned();
            CreateModuleAndSubmodulesQueryRoles(response.data.roles);
            CreateModuleAndSubmodulesAjaxSuccess(response);
            $('#CreateModuleAndSubmodulesModal').modal('show');
        },
        error: function(xhr, textStatus, errorThrown) {
            CreateModuleAndSubmodulesAjaxError(xhr);
        }
    });
}

function CreateModuleAndSubmodulesModalCleaned() {
    RemoveIsInvalidClassCreateModuleAndSubmodules();
    RemoveIsValidClassCreateModuleAndSubmodules();
    $('.submodules_c').empty();
    $('#roles_access_c').empty();
    $('#module_c').val('');
    $('#icon_c').val('');
    $('#CreateModuleAndSubmodulesAddSubmoduleButton').attr('data-count', 0);
    CreateModuleAndSubmodulesAddSubmodule();
}

function CreateModuleAndSubmodules() {
    Swal.fire({
        title: '¿Desea guardar el modulos y los submodulos?',
        text: 'El modulo y los submodulos serán creados.',
        icon: 'warning',
        showCancelButton: true,
        cancelButtonColor: '#DD6B55',
        confirmButtonColor: '#3085d6',
        confirmButtonText: 'Si, guardar!',
        cancelButtonText: 'No, cancelar!',
    }).then((result) => {
        if (result.value) {
            $.ajax({
                url: `/Dashboard/ModulesAndSubmodules/Store`,
                type: 'POST',
                data: {
                    '_token': $('meta[name="csrf-token"]').attr('content'),
                    'module': $('#module_c').val(),
                    'icon': $('#icon_c').val(),
                    'roles': $('#roles_access_c .icheck-primary input[type="checkbox"]:checked').map(function () {
                        return $(this).attr('data-id');
                    }).get(),
                    'submodules': $('.submodules_c').find('div.submodule_c').map(function(index) {
                        return {
                            'submodule': $(this).find('input.name_c').val(),
                            'url': $(this).find('input.url_c').val(),
                            'icon': $(this).find('input.subicon_c').val(),
                            'permission_id': $(this).find('select.permission_c').val()
                        };
                    }).get()
                },
                success: function(response) {
                    tableModulesAndSubmodules.ajax.reload();
                    CreateModuleAndSubmodulesAjaxSuccess(response);
                },
                error: function(xhr, textStatus, errorThrown) {
                    CreateModuleAndSubmodulesAjaxError(xhr);
                }
            });
        } else {
            toastr.info('El modulo y los submodulos no fueron creados.')
        }
    });
}

function CreateModuleAndSubmodulesAddSubmodule() {
    // Crear el nuevo elemento HTML con jQuery
    let id = $('#CreateModuleAndSubmodulesAddSubmoduleButton').attr('data-count');
    let newSubmodule = $('<div>').attr({
        'id': `group-submodule${id}`,
        'class': 'form-group submodule_c'
    });
    let card = $('<div>').addClass('card collapsed-card');
    let cardHeader = $('<div>').addClass('card-header border-0 ui-sortable-handle');
    let cardTitle = $('<h3>').addClass('card-title mt-1').css({'width':'70%'});
    let inputGroup = $('<div>').addClass('input-group');
    let input = $('<input>').attr({
        'type': 'text',
        'class': 'form-control name_c',
        'id': `name${id}_c`,
        'name': ''
    });
    let inputGroupAppend = $('<div>').addClass('input-group-append');
    let inputGroupText = $('<span>').addClass('input-group-text');
    let sliderIcon = $('<i>').addClass('fas fa-slider');
    let cardTools = $('<div>').addClass('card-tools');
    let collapseButton = $('<button>').attr({
        'type': 'button',
        'class': 'btn btn-info btn-sm ml-2 mt-2',
        'data-card-widget': 'collapse'
    });
    let plusIcon = $('<i>').addClass('fas fa-plus');
    let removeButton = $('<button>').attr({
        'type': 'button',
        'class': 'btn btn-danger btn-sm ml-2 mt-2',
        'data-card-widget': 'remove',
        'onclick': `CreateModuleAndSubmodulesRemoveSubmodule(${id})`
    });
    let timesIcon = $('<i>').addClass('fas fa-times');

    // Anidar elementos
    inputGroupText.append(sliderIcon);
    inputGroupAppend.append(inputGroupText);
    inputGroup.append(input, inputGroupAppend);
    cardTitle.append(inputGroup);
    collapseButton.append(plusIcon);
    removeButton.append(timesIcon);
    cardTools.append(collapseButton, removeButton);
    cardHeader.append(cardTitle, cardTools);
    card.append(cardHeader);

    let cardBody = $('<div>').addClass('card-body').addClass('table-responsive').css('display', 'none');
    let roleForm = $('<div>').addClass('form-group');
    let roleLabel = $('<label>').attr('for', '').text('ROL');
    let roleIcon = $('<i>').attr({
        'class': 'ml-2 far fa-circle-question',
        'onclick': 'SuggestionModuleRoles()'
    });
    let roleSelect = $('<select>').attr({
        'id': `role${id}_c`,
        'class': 'form-control role_c',
        'onchange': `CreateModuleAndSubmodulesQueryPermissions(this, 'permission${id}_c')`
    });
    let roleOption = $('<option>').attr('value', '').text('Seleccione');
    roleSelect.append(roleOption);
    $('#roles_access_c .icheck-primary input[type="checkbox"]:checked').map(function() {
        roleSelect.append(
            $('<option>')
            .attr('value', $(this).attr('id'))
            .text($(this).attr('id'))
        );
    });
    roleForm.append(roleLabel, roleIcon, roleSelect);
    let permissionForm = $('<div>').addClass('form-group');
    let permissionLabel = $('<label>').attr('for', '').text('PERMISO');
    let permissionIcon = $('<i>').attr({
        'class': 'ml-2 far fa-circle-question',
        'onclick': 'SuggestionSumodulePermission()'
    });
    let permissionSelect = $('<select>').attr({
        'id': `permission${id}_c`,
        'class': 'form-control permission_c',
        'onchange': `CreateModuleAndSubmodulesWriteUrl(this, 'url${id}_c')`
    });
    let permissionOption = $('<option>').attr('value', '').text('Seleccione');
    permissionSelect.append(permissionOption);
    permissionForm.append(permissionLabel, permissionIcon, permissionSelect);
    let urlForm = $('<div>').addClass('form-group');
    let urlLabel = $('<label>').attr('for', '').text('RUTA');
    let urlSuggestion = $('<i>').attr({
        'class': 'ml-2 far fa-circle-question',
        'onclick': 'SuggestionSumoduleRoute()'
    });
    let urlInputGroup = $('<div>').addClass('input-group');
    let urlInput = $('<input>').attr({
        'type': 'text',
        'id': `url${id}_c`,
        'class': 'form-control url_c',
        'readonly': 'readonly'
    });
    let urlInputAppend = $('<div>').addClass('input-group-append');
    let urlIcon = $('<span>').addClass('input-group-text').append($('<i>').addClass('fas fa-route-highway'));
    urlInputAppend.append(urlIcon);
    urlInputGroup.append(urlInput, urlInputAppend);
    urlForm.append(urlLabel, urlSuggestion, urlInputGroup);
    let subIconForm = $('<div>').addClass('form-group');
    let subIconLabel = $('<label>').attr('for', '').text('ICONO SUBMODULO');
    let subIconSuggestion = $('<i>').attr({
        'class': 'ml-2 far fa-circle-question',
        'onclick': 'SuggestionSubmoduleIcon()'
    });
    let subIconInputGroup = $('<div>').addClass('input-group');
    let subIconInput = $('<input>').attr({
        'type': 'text',
        'id': `subicon${id}_c`,
        'class': 'form-control subicon_c',
        'onkeyup': `CreateModuleAndSubmodulesChangeClassIcon(this, 'icono${id}_c')`
    });
    let subIconInputAppend = $('<div>').addClass('input-group-append');
    let subIconInputIcon = $('<span>').addClass('input-group-text').append($('<i>').attr('id', `icono${id}_c`));
    subIconInputAppend.append(subIconInputIcon);
    subIconInputGroup.append(subIconInput, subIconInputAppend);
    subIconForm.append(subIconLabel, subIconSuggestion, subIconInputGroup);

    cardBody.append(roleForm, permissionForm, urlForm, subIconForm);
    card.append(cardBody);

    newSubmodule.append(card);

    // Agregar el nuevo elemento al elemento con clase "submodules_c"
    $('.submodules_c').append(newSubmodule);
    id++;
    $('#CreateModuleAndSubmodulesAddSubmoduleButton').attr('data-count', id)
}

function CreateModuleAndSubmodulesRemoveSubmodule(index) {
    $(`#group-submodule${index}`).remove();
}

function CreateModuleAndSubmodulesChangeClassIcon(input, icon) {
    $(`#${icon}`).attr('class', input.value);
}

function CreateModuleAndSubmodulesWriteUrl(selectPermission, inputUrl) {
    if($(selectPermission).val() === '') {
        $(`#${inputUrl}`).val('');
    } else {
        $(`#${inputUrl}`).val(`/${$(selectPermission).find('option:selected').text().replace(/\./g, '/')}`);
    }
}

function CreateModuleAndSubmodulesQueryRoles(roles) {
    let rolesDiv = $('#roles_access_c');

    $.each(roles, function (i, role) {
        let roleDiv = $('<div>').addClass('row pl-2 icheck-primary');

        let roleCheckbox = $('<input>').attr({
            'id': role.name,
            'type': 'checkbox',
            'onclick': 'CreateModuleAndSubmodulesRoles(this)',
            'data-id': role.id
        });

        let roleLabel = $('<label>').text(role.name).attr({
            'for': role.name,
            'class': 'mt-3 ml-3'
        });

        roleDiv.append(roleCheckbox);
        roleDiv.append(roleLabel);
        rolesDiv.append(roleDiv);
    });
}

function CreateModuleAndSubmodulesRoles(checkbox) {
    // Obtener IDs de los checkboxes marcados en #roles_access_c
    let role = $(checkbox).attr('id');

    // Recorrer #submodules_c
    $('.submodules_c').each(function () {
        let submoduleElement = $(this);
        let selectRole = submoduleElement.find('select.role_c');

        // Verificar si el checkbox está marcado
        if ($(checkbox).is(':checked')) {
            // Agregar el role al select
            selectRole.append($('<option>', {
                'value': role,
                'text': role
            }));
        } else {
            let optionToRemove = selectRole.find(`option[value="${role}"]`);

            // Verificar si la opción estaba seleccionada antes de la eliminación
            let isSelected = optionToRemove.is(':selected');

            if (isSelected) {
                let selectPermission = submoduleElement.find('select.permission_c');
                submoduleElement.find('input.url_c').val('')
                selectPermission.empty().append(
                    $('<option>', {
                        'value': '',
                        'text': 'Seleccione'
                    })
                );
            }
            // Quitar el role del select
            optionToRemove.remove();
        }
    });
}

function CreateModuleAndSubmodulesQueryPermissions(selectRoles, selectPermissions) {
    $.ajax({
        url: `/Dashboard/ModulesAndSubmodules/Create`,
        type: 'POST',
        data: {
            '_token': $('meta[name="csrf-token"]').attr('content'),
            'role': $(selectRoles).val()
        },
        success: function(response) {
            if($(selectRoles).val() !== '') {
                CreateModuleAndSubmodulesPermissions(selectPermissions, response.data.permissions);
            }
        },
        error: function(xhr, textStatus, errorThrown) {
            CreateModuleAndSubmodulesAjaxError(xhr);
            $(`#${selectPermissions}`).empty().append(
                $('<option>', {
                    'value': '',
                    'text': 'Seleccione'
                })
            );
        }
    });
}

function CreateModuleAndSubmodulesPermissions(selectPermissions, permissions) {
    console.log(permissions);
    let select = $(`#${selectPermissions}`).empty().append(
        $('<option>',{
            'value': '',
            'text': 'Seleccione'
        })
    );
    // Agregar opciones con los roles seleccionados
    $.each(permissions, function(index, permission) {
        select.append($('<option>',
            {
                'value': permission.id,
                'text': permission.name
            }
        ));
    });
}

function CreateModuleAndSubmodulesAjaxSuccess(response) {
    if(response.status === 204) {
        toastr.info(response.message);
        $('#CreateModuleAndSubmodulesModal').modal('hide');
    }

    if(response.status === 201) {
        toastr.success(response.message);
        $('#CreateModuleAndSubmodulesModal').modal('hide');
    }
}

function CreateModuleAndSubmodulesAjaxError(xhr) {
    if(xhr.status === 403) {
        toastr.error(xhr.responseJSON.error ? xhr.responseJSON.error.message : xhr.responseJSON.message);
        $('#CreateModuleAndSubmodulesModal').modal('hide');
    }

    if(xhr.status === 404) {
        toastr.error(xhr.responseJSON.error ? xhr.responseJSON.error.message : xhr.responseJSON.message);
        $('#CreateModuleAndSubmodulesModal').modal('hide');
    }

    if(xhr.status === 419) {
        toastr.error(xhr.responseJSON.error ? xhr.responseJSON.error.message : xhr.responseJSON.message);
        $('#CreateModuleAndSubmodulesModal').modal('hide');
    }

    if(xhr.status === 422){
        RemoveIsValidClassCreateModuleAndSubmodules();
        RemoveIsInvalidClassCreateModuleAndSubmodules();
        $.each(xhr.responseJSON.errors, function(field, messages) {
            AddIsInvalidClassCreateModuleAndSubmodules(field);
            $.each(messages, function(index, message) {
                toastr.error(message);
            });
        });
        AddIsValidClassCreateModuleAndSubmodules();
    }

    if(xhr.status === 500){
        toastr.error(xhr.responseJSON.error ? xhr.responseJSON.error.message : xhr.responseJSON.message);
        $('#CreateModuleAndSubmodulesModal').modal('hide');
    }
}

function AddIsValidClassCreateModuleAndSubmodules() {
    if (!$('#module_c').hasClass('is-invalid')) {
        $('#module_c').addClass('is-valid');
    }

    if (!$('#icon_c').hasClass('is-invalid')) {
        $('#icon_c').addClass('is-valid');
    }

    $('.submodules_c').find('div.submodule_c').each(function(index) {
        if (!$(this).find('select.role_c').hasClass('is-invalid')) {
            $(this).find('select.role_c').addClass('is-valid');
        }
        if (!$(this).find('select.permission_c').hasClass('is-invalid')) {
            $(this).find('select.permission_c').addClass('is-valid');
        }
        if (!$(this).find('input.subicon_c').hasClass('is-invalid')) {
            $(this).find('input.subicon_c').addClass('is-valid');
        }
        if (!$(this).find('input.name_c').hasClass('is-invalid')) {
            $(this).find('input.name_c').addClass('is-valid');
        }
        if (!$(this).find('input.url_c').hasClass('is-invalid')) {
            $(this).find('input.url_c').addClass('is-valid');
        }
    });
}

function RemoveIsValidClassCreateModuleAndSubmodules() {
    $('#module_c').removeClass('is-valid');
    $('#icon_c').removeClass('is-valid');

    $('.submodules_c').find('div.submodule_c').each(function(index) {
        $(this).find('select.role_c').removeClass('is-valid');
        $(this).find('select.permission_c').removeClass('is-valid');
        $(this).find('input.subicon_c').removeClass('is-valid');
        $(this).find('input.name_c').removeClass('is-valid');
        $(this).find('input.url_c').removeClass('is-valid');
    });
}

function AddIsInvalidClassCreateModuleAndSubmodules(input) {
    if (!$(`#${input}_c`).hasClass('is-valid')) {
        $(`#${input}_c`).addClass('is-invalid');
    }

    $('.submodules_c').find('div.submodule_c').each(function(index) {
        // Agrega la clase 'is-invalid'
        if(input === `submodules.${index}.permission_id`) {
            if (!$(this).find('select.permission_c').hasClass('is-valid')) {
                $(this).find('select.role_c').addClass('is-invalid');
                $(this).find('select.permission_c').addClass('is-invalid');
            }
        }
        if(input === `submodules.${index}.icon`) {
            if (!$(this).find('input.subicon_c').hasClass('is-valid')) {
                $(this).find('input.subicon_c').addClass('is-invalid');
            }
        }
        if(input === `submodules.${index}.submodule`) {
            if (!$(this).find('input.name_c').hasClass('is-valid')) {
                $(this).find('input.name_c').addClass('is-invalid');
            }
        }
        if(input === `submodules.${index}.url`) {
            if (!$(this).find('input.url_c').hasClass('is-valid')) {
                $(this).find('input.url_c').addClass('is-invalid');
            }
        }
    });
}

function RemoveIsInvalidClassCreateModuleAndSubmodules() {
    $('#module_c').removeClass('is-invalid');
    $('#icon_c').removeClass('is-invalid');

    $('.submodules_c').find('div.submodule_c').each(function(index) {
        $(this).find('select.role_c').removeClass('is-invalid');
        $(this).find('select.permission_c').removeClass('is-invalid');
        $(this).find('input.subicon_c').removeClass('is-invalid');
        $(this).find('input.name_c').removeClass('is-invalid');
        $(this).find('input.url_c').removeClass('is-invalid');
    });
}
