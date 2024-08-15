function EditCategoryAndSubcategoriesModal(id) {
    $.ajax({
        url: `/Dashboard/CategoriesAndSubcategories/Edit/${id}`,
        type: 'POST',
        data: {
            '_token': $('meta[name="csrf-token"]').attr('content'),
        },
        success: function(response) {
            EditCategoryAndSubcategoriesModalCleaned(response.data.category);
            EditCategoryAndSubcategoriesModalClothingLines(response.data.clothingLines);
            EditCategoryAndSubcategoriesAjaxSuccess(response);
            $('#EditCategoryAndSubcategoriesModal').modal('show');
        },
        error: function(xhr, textStatus, errorThrown) {
            EditCategoryAndSubcategoriesAjaxError(xhr);
        }
    });
}

function EditCategoryAndSubcategoriesModalCleaned(categoryAndSubcategories) {
    RemoveIsInvalidClassEditCategoryAndSubcategories();
    RemoveIsValidClassEditCategoryAndSubcategories();
    EditCategoryAndSubcategoriesModalResetSelect();
    $('.subcategories_e').empty();
    $('#name_e').val(categoryAndSubcategories.name);
    $('#code_e').val(categoryAndSubcategories.code);
    $('#description_e').val(categoryAndSubcategories.description);
    $('#EditCategoryAndSubcategoriesAddSubcategoryButton').attr('data-count', 0);
    $('#EditCategoryAndSubcategoriesButton').attr('data-clothing_line_id', categoryAndSubcategories.clothing_line_id);
    $('#EditCategoryAndSubcategoriesButton').attr('onclick', `EditCategoryAndSubcategories(${categoryAndSubcategories.id})`);
    $.each(categoryAndSubcategories.subcategories, function (i, subcategory) {
        EditCategoryAndSubcategoriesAddSubcategory(subcategory.id, subcategory.name, subcategory.code, subcategory.description, subcategory.deleted_at);
    })
}

function EditCategoryAndSubcategoriesModalResetSelect() {
    $(`#clothing_line_id_e`).html('')
    $(`#clothing_line_id_e`).append(new Option('Seleccione', '', false, false));
    $(`#clothing_line_id_e`).trigger('change');
}

function EditCategoryAndSubcategoriesModalClothingLines(clothingLines) {
    clothingLines.forEach(clothingLine => {
        $('#clothing_line_id_e').append(new Option(clothingLine.name, clothingLine.id, false, false));
    });
    let clothing_line_id = $('#EditCategoryAndSubcategoriesButton').attr('data-clothing_line_id');
    if(clothing_line_id != '') {
        $("#clothing_line_id_e").val(clothing_line_id).trigger('change');
        $('#EditCategoryAndSubcategoriesButton').attr('data-clothing_line_id', '');
    }
}

function EditCategoryAndSubcategories(id) {
    Swal.fire({
        title: '¿Desea actualizar la categoria y subcategorias?',
        text: 'La categoria y subcategorias serán actualizados.',
        icon: 'warning',
        showCancelButton: true,
        cancelButtonColor: '#DD6B55',
        confirmButtonColor: '#3085d6',
        confirmButtonText: 'Si, actualizar!',
        cancelButtonText: 'No, cancelar!',
    }).then((result) => {
        if (result.value) {
            $.ajax({
                url: `/Dashboard/CategoriesAndSubcategories/Update/${id}`,
                type: 'PUT',
                data: {
                    '_token': $('meta[name="csrf-token"]').attr('content'),
                    'clothing_line_id': $('#clothing_line_id_e').val(),
                    'name': $('#name_e').val(),
                    'code': $('#code_e').val(),
                    'description': $('#description_e').val(),
                    'subcategories': $('.subcategories_e').find('div.subcategory_e').map(function(index) {
                        const id = $(this).find('input.name_e').attr('data-id');
                        const subcategory = {
                            'name': $(this).find('input.name_e').val(),
                            'code': $(this).find('input.code_e').val(),
                            'description': $(this).find('textarea.description_e').val(),
                            'status': $(this).find('button.status').attr('data-status')
                        };
                        if (id != undefined) {
                            subcategory['id'] = id;
                        };
                        return subcategory;
                    }).get()
                },
                success: function(response) {
                    tableCategoriesAndSubcategories.ajax.reload();
                    EditCategoryAndSubcategoriesAjaxSuccess(response);
                },
                error: function(xhr, textStatus, errorThrown) {
                    EditCategoryAndSubcategoriesAjaxError(xhr);
                }
            });
        } else {
            toastr.info('La categoria y subcategorias no fueron actualizados.')
        }
    });
}

function EditCategoryAndSubcategoriesAddSubcategory(id, name, code, description, deleted) {
    // Crear el nuevo elemento HTML con jQuery
    let data = $('#EditCategoryAndSubcategoriesAddSubcategoryButton').attr('data-count');
    let newSubcategory = $('<div>').attr({
        'id': `group-subcategory${data}`,
        'class': 'form-group subcategory_e'
    });
    let card = $('<div>').addClass('card collapsed-card');
    let cardHeader = $('<div>').addClass('card-header border-0 ui-sortable-handle');
    let cardTitle = $('<h3>').addClass('card-title mt-1').css({'width':'70%'});
    let inputGroup = $('<div>').addClass('input-group');
    let input = $('<input>').attr({
        'type': 'text',
        'class': 'form-control name_e',
        'id': `name${data}_e`,
        'value': name,
        'data-id': id
    });
    let inputGroupAppend = $('<div>').addClass('input-group-append');
    let inputGroupText = $('<span>').addClass('input-group-text');
    let signatureIcon = $('<i>').addClass('fas fa-signature');
    let cardTools = $('<div>').addClass('card-tools');
    let collapseButton = $('<button>').attr({
        'type': 'button',
        'class': 'btn btn-info btn-sm ml-2 mt-2',
        'data-card-widget': 'collapse'
    });
    let plusIcon = $('<i>').addClass('fas fa-plus');
    let removeButton = $('<button>').attr({
        'type': 'button',
        'class': `btn btn-${deleted == null ? 'danger' : 'success'} btn-sm ml-2 mt-2 status`,
        'data-status': deleted == null ? 'true' : 'false',
        'onclick': `EditCategoryAndSubcategories${deleted == null ? 'Inactive' : 'Active'}Subcategory(this)`
    });
    let timesIcon = $('<i>').addClass(`${deleted == null ? 'fas fa-times' : 'fas fa-check'}`);

    // Anidar elementos
    inputGroupText.append(signatureIcon);
    inputGroupAppend.append(inputGroupText);
    inputGroup.append(input, inputGroupAppend);
    cardTitle.append(inputGroup);
    collapseButton.append(plusIcon);
    removeButton.append(timesIcon);
    cardTools.append(collapseButton, removeButton);
    cardHeader.append(cardTitle, cardTools);
    card.append(cardHeader);

    let cardBody = $('<div>').addClass('card-body').addClass('table-responsive').css('display', 'none');


    let codeForm = $('<div>').addClass('form-group');
    let codeLabel = $('<label>').attr('for', '').text('Codigo');
    let codeInputGroup = $('<div>').addClass('input-group');
    let codeInput = $('<input>').attr({
        'type': 'text',
        'id': `code${data}_e`,
        'class': 'form-control code_e',
        'value': code
    });
    let codeInputAppend = $('<div>').addClass('input-group-append');
    let codeIcon = $('<span>').addClass('input-group-text').append($('<i>').addClass('fas fa-code'));
    codeInputAppend.append(codeIcon);
    codeInputGroup.append(codeInput, codeInputAppend);
    codeForm.append(codeLabel, codeInputGroup);

    let descriptionForm = $('<div>').addClass('form-group');
    let descriptionLabel = $('<label>').attr('for', '').text('Descripcion');
    let descriptionInputGroup = $('<div>').addClass('input-group');
    let descriptionInput = $('<textarea>').attr({
        'type': 'text',
        'id': `description${data}_e`,
        'class': 'form-control description_e',
        'cols': '30',
        'rows': '3'
    }).text(description);
    let descriptionInputAppend = $('<div>').addClass('input-group-append');
    let descriptionIcon = $('<span>').addClass('input-group-text').append($('<i>').addClass('fas fa-text-size'));
    descriptionInputAppend.append(descriptionIcon);
    descriptionInputGroup.append(descriptionInput, descriptionInputAppend);
    descriptionForm.append(descriptionLabel, descriptionInputGroup);


    cardBody.append(codeForm, descriptionForm);
    card.append(cardBody);

    newSubcategory.append(card);

    // Agregar el nuevo elemento al elemento con clase "subcategories_e"
    $('.subcategories_e').append(newSubcategory);
    data++;
    $('#EditCategoryAndSubcategoriesAddSubcategoryButton').attr('data-count', data)
}

function EditCategoryAndSubcategoriesActiveSubcategory(button) {
    $(button).removeClass('btn-success').addClass('btn-danger')
    $(button).attr('onclick', 'EditCategoryAndSubcategoriesInactiveSubcategory(this)');
    $(button).attr('data-status', 'true');
    $(button).find('i').removeClass().addClass('fas fa-times');
}

function EditCategoryAndSubcategoriesInactiveSubcategory(button) {
    $(button).removeClass('btn-danger').addClass('btn-success')
    $(button).attr('onclick', 'EditCategoryAndSubcategoriesActiveSubcategory(this)');
    $(button).attr('data-status', 'false');
    $(button).find('i').removeClass().addClass('fas fa-check');
}

function EditCategoryAndSubcategoriesAjaxSuccess(response) {
    if(response.status === 200) {
        toastr.success(response.message);
        $('#EditCategoryAndSubcategoriesModal').modal('hide');
    }

    if(response.status === 204) {
        toastr.info(response.message);
        $('#EditCategoryAndSubcategoriesModal').modal('hide');
    }
}

function EditCategoryAndSubcategoriesAjaxError(xhr) {
    if(xhr.status === 403) {
        toastr.error(xhr.responseJSON.error ? xhr.responseJSON.error.message : xhr.responseJSON.message);
        $('#EditCategoryAndSubcategoriesModal').modal('hide');
    }

    if(xhr.status === 404) {
        toastr.error(xhr.responseJSON.error ? xhr.responseJSON.error.message : xhr.responseJSON.message);
        $('#EditCategoryAndSubcategoriesModal').modal('hide');
    }

    if(xhr.status === 419) {
        toastr.error(xhr.responseJSON.error ? xhr.responseJSON.error.message : xhr.responseJSON.message);
        $('#EditCategoryAndSubcategoriesModal').modal('hide');
    }

    if(xhr.status === 422){
        RemoveIsValidClassEditCategoryAndSubcategories();
        RemoveIsInvalidClassEditCategoryAndSubcategories();
        $.each(xhr.responseJSON.errors, function(field, messages) {
            AddIsInvalidClassEditCategoryAndSubcategories(field);
            $.each(messages, function(index, message) {
                toastr.error(message);
            });
        });
        AddIsValidClassEditCategoryAndSubcategories();
    }

    if(xhr.status === 500){
        toastr.error(xhr.responseJSON.error ? xhr.responseJSON.error.message : xhr.responseJSON.message);
        $('#EditCategoryAndSubcategoriesModal').modal('hide');
    }
}

function AddIsValidClassEditCategoryAndSubcategories() {
    if (!$('#name_e').hasClass('is-invalid')) {
        $('#name_e').addClass('is-valid');
    }
    if (!$('#code_e').hasClass('is-invalid')) {
        $('#code_e').addClass('is-valid');
    }
    if (!$('#description_e').hasClass('is-invalid')) {
        $('#description_e').addClass('is-valid');
    }
    if (!$('span[aria-labelledby="select2-clothing_line_id_e-container"]').hasClass('is-invalid')) {
        $('span[aria-labelledby="select2-clothing_line_id_e-container"]').addClass('is-valid');
    }

    $('.subcategories_e').find('div.subcategory_e').each(function(index) {
        if (!$(this).find('input.name_e').hasClass('is-invalid')) {
            $(this).find('input.name_e').addClass('is-valid');
        }
        if (!$(this).find('input.code_e').hasClass('is-invalid')) {
            $(this).find('input.code_e').addClass('is-valid');
        }
        if (!$(this).find('textarea.description_e').hasClass('is-invalid')) {
            $(this).find('textarea.description_e').addClass('is-valid');
        }
    });
}

function RemoveIsValidClassEditCategoryAndSubcategories() {
    $('#name_e').removeClass('is-valid');
    $('#code_e').removeClass('is-valid');
    $('#description_e').removeClass('is-valid');
    $('span[aria-labelledby="select2-clothing_line_id_e-container"]').removeClass('is-valid');

    $('.subcategories_e').find('div.subcategory_e').each(function(index) {
        $(this).find('input.name_e').removeClass('is-valid');
        $(this).find('input.code_e').removeClass('is-valid');
        $(this).find('textarea.description_e').removeClass('is-valid');
    });
}

function AddIsInvalidClassEditCategoryAndSubcategories(input) {
    if (!$(`#${input}_e`).hasClass('is-valid')) {
        $(`#${input}_e`).addClass('is-invalid');
    }

    if (!$(`span[aria-labelledby="select2-${input}_e-container`).hasClass('is-valid')) {
        $(`span[aria-labelledby="select2-${input}_e-container"]`).addClass('is-invalid');
    }

    $('.subcategories_e').find('div.subcategory_e').each(function(index) {
        // Agrega la clase 'is-invalid'
        if(input === `subcategories.${index}.name`) {
            if (!$(this).find('input.name_e').hasClass('is-valid')) {
                $(this).find('input.name_e').addClass('is-invalid');
            }
        }
        if(input === `subcategories.${index}.code`) {
            if (!$(this).find('input.code_e').hasClass('is-valid')) {
                $(this).find('input.code_e').addClass('is-invalid');
            }
        }
        if(input === `subcategories.${index}.description`) {
            if (!$(this).find('textarea.description_e').hasClass('is-valid')) {
                $(this).find('textarea.description_e').addClass('is-invalid');
            }
        }
    });
}

function RemoveIsInvalidClassEditCategoryAndSubcategories() {
    $('#name_e').removeClass('is-invalid');
    $('#code_e').removeClass('is-invalid');
    $('#description_e').removeClass('is-invalid');
    $('span[aria-labelledby="select2-clothing_line_id_e-container"]').removeClass('is-invalid');

    $('.subcategories_e').find('div.subcategory_e').each(function(index) {
        $(this).find('input.name_e').removeClass('is-invalid');
        $(this).find('input.code_e').removeClass('is-invalid');
        $(this).find('textarea.description_e').removeClass('is-invalid');
    });
}
