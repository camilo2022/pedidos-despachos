function CreateCategoryAndSubcategoriesModal() {
    $.ajax({
        url: `/Dashboard/CategoriesAndSubcategories/Create`,
        type: 'POST',
        data: {
            '_token': $('meta[name="csrf-token"]').attr('content'),
        },
        success: function(response) {
            CreateCategoryAndSubcategoriesModalCleaned();
            CreateCategoryAndSubcategoriesModalClothingLines(response.data);
            CreateCategoryAndSubcategoriesAjaxSuccess(response);
            $('#CreateCategoryAndSubcategoriesModal').modal('show');
        },
        error: function(xhr, textStatus, errorThrown) {
            CreateCategoryAndSubcategoriesAjaxError(xhr);
        }
    });
}

function CreateCategoryAndSubcategoriesModalCleaned() {
    RemoveIsInvalidClassCreateCategoryAndSubcategories();
    RemoveIsValidClassCreateCategoryAndSubcategories();
    $('.subcategories_c').empty();
    $('#name_c').val('');
    $('#code_c').val('');
    $('#description_c').val('');
    $('#CreateCategoryAndSubcategoriesAddSubcategoryButton').attr('data-count', 0);
    CreateCategoryAndSubcategoriesAddSubcategory();
    CreateCategoryAndSubcategoriesModalResetSelect();
}

function CreateCategoryAndSubcategoriesModalResetSelect() {
    $(`#clothing_line_id_c`).html('')
    $(`#clothing_line_id_c`).append(new Option('Seleccione', '', false, false));
    $(`#clothing_line_id_c`).trigger('change');
}

function CreateCategoryAndSubcategoriesModalClothingLines(clothingLines) {
    clothingLines.forEach(clothingLine => {
        $('#clothing_line_id_c').append(new Option(clothingLine.name, clothingLine.id, false, false));
    });
}

function CreateCategoryAndSubcategories() {
    Swal.fire({
        title: '¿Desea guardar la categoria y subcategorias?',
        text: 'La categoria y subcategorias serán creados.',
        icon: 'warning',
        showCancelButton: true,
        cancelButtonColor: '#DD6B55',
        confirmButtonColor: '#3085d6',
        confirmButtonText: 'Si, guardar!',
        cancelButtonText: 'No, cancelar!',
    }).then((result) => {
        if (result.value) {
            $.ajax({
                url: `/Dashboard/CategoriesAndSubcategories/Store`,
                type: 'POST',
                data: {
                    '_token': $('meta[name="csrf-token"]').attr('content'),
                    'clothing_line_id': $('#clothing_line_id_c').val(),
                    'name': $('#name_c').val(),
                    'code': $('#code_c').val(),
                    'description': $('#description_c').val(),
                    'subcategories': $('.subcategories_c').find('div.subcategory_c').map(function(index) {
                        return {
                            'name': $(this).find('input.name_c').val(),
                            'code': $(this).find('input.code_c').val(),
                            'description': $(this).find('textarea.description_c').val()
                        };
                    }).get()
                },
                success: function(response) {
                    tableCategoriesAndSubcategories.ajax.reload();
                    CreateCategoryAndSubcategoriesAjaxSuccess(response);
                },
                error: function(xhr, textStatus, errorThrown) {
                    CreateCategoryAndSubcategoriesAjaxError(xhr);
                }
            });
        } else {
            toastr.info('La categoria y subcategorias no fueron creados.')
        }
    });
}

function CreateCategoryAndSubcategoriesAddSubcategory() {
    // Crear el nuevo elemento HTML con jQuery
    let id = $('#CreateCategoryAndSubcategoriesAddSubcategoryButton').attr('data-count');
    let newSubcategory = $('<div>').attr({
        'id': `group-subcategory${id}`,
        'class': 'form-group subcategory_c'
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
        'class': 'btn btn-danger btn-sm ml-2 mt-2',
        'data-card-widget': 'remove',
        'onclick': `CreateCategoryAndSubcategoriesRemoveSubcategory(${id})`
    });
    let timesIcon = $('<i>').addClass('fas fa-times');

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
        'id': `code${id}_c`,
        'class': 'form-control code_c',
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
        'id': `description${id}_c`,
        'class': 'form-control description_c',
        'cols': '30',
        'rows': '3'
    });
    let descriptionInputAppend = $('<div>').addClass('input-group-append');
    let descriptionIcon = $('<span>').addClass('input-group-text').append($('<i>').addClass('fas fa-text-size'));
    descriptionInputAppend.append(descriptionIcon);
    descriptionInputGroup.append(descriptionInput, descriptionInputAppend);
    descriptionForm.append(descriptionLabel, descriptionInputGroup);


    cardBody.append(codeForm, descriptionForm);
    card.append(cardBody);

    newSubcategory.append(card);

    // Agregar el nuevo elemento al elemento con clase "subcategories_c"
    $('.subcategories_c').append(newSubcategory);
    id++;
    $('#CreateCategoryAndSubcategoriesAddSubcategoryButton').attr('data-count', id)
}

function CreateCategoryAndSubcategoriesRemoveSubcategory(index) {
    $(`#group-subcategory${index}`).remove();
}

function CreateCategoryAndSubcategoriesAjaxSuccess(response) {
    if(response.status === 204) {
        toastr.info(response.message);
        $('#CreateCategoryAndSubcategoriesModal').modal('hide');
    }

    if(response.status === 201) {
        toastr.success(response.message);
        $('#CreateCategoryAndSubcategoriesModal').modal('hide');
    }
}

function CreateCategoryAndSubcategoriesAjaxError(xhr) {
    if(xhr.status === 403) {
        toastr.error(xhr.responseJSON.error ? xhr.responseJSON.error.message : xhr.responseJSON.message);
        $('#CreateCategoryAndSubcategoriesModal').modal('hide');
    }

    if(xhr.status === 404) {
        toastr.error(xhr.responseJSON.error ? xhr.responseJSON.error.message : xhr.responseJSON.message);
        $('#CreateCategoryAndSubcategoriesModal').modal('hide');
    }

    if(xhr.status === 419) {
        toastr.error(xhr.responseJSON.error ? xhr.responseJSON.error.message : xhr.responseJSON.message);
        $('#CreateCategoryAndSubcategoriesModal').modal('hide');
    }

    if(xhr.status === 422){
        RemoveIsValidClassCreateCategoryAndSubcategories();
        RemoveIsInvalidClassCreateCategoryAndSubcategories();
        $.each(xhr.responseJSON.errors, function(field, messages) {
            AddIsInvalidClassCreateCategoryAndSubcategories(field);
            $.each(messages, function(index, message) {
                toastr.error(message);
            });
        });
        AddIsValidClassCreateCategoryAndSubcategories();
    }

    if(xhr.status === 500){
        toastr.error(xhr.responseJSON.error ? xhr.responseJSON.error.message : xhr.responseJSON.message);
        $('#CreateCategoryAndSubcategoriesModal').modal('hide');
    }
}

function AddIsValidClassCreateCategoryAndSubcategories() {
    if (!$('#name_c').hasClass('is-invalid')) {
        $('#name_c').addClass('is-valid');
    }
    if (!$('#code_c').hasClass('is-invalid')) {
        $('#code_c').addClass('is-valid');
    }
    if (!$('#description_c').hasClass('is-invalid')) {
        $('#description_c').addClass('is-valid');
    }
    if (!$('span[aria-labelledby="select2-clothing_line_id_c-container"]').hasClass('is-invalid')) {
        $('span[aria-labelledby="select2-clothing_line_id_c-container"]').addClass('is-valid');
    }

    $('.subcategories_c').find('div.subcategory_c').each(function(index) {
        if (!$(this).find('input.name_c').hasClass('is-invalid')) {
            $(this).find('input.name_c').addClass('is-valid');
        }
        if (!$(this).find('input.code_c').hasClass('is-invalid')) {
            $(this).find('input.code_c').addClass('is-valid');
        }
        if (!$(this).find('textarea.description_c').hasClass('is-invalid')) {
            $(this).find('textarea.description_c').addClass('is-valid');
        }
    });
}

function RemoveIsValidClassCreateCategoryAndSubcategories() {
    $('#name_c').removeClass('is-valid');
    $('#code_c').removeClass('is-valid');
    $('#description_c').removeClass('is-valid');
    $('span[aria-labelledby="select2-clothing_line_id_c-container"]').removeClass('is-valid');

    $('.subcategories_c').find('div.subcategory_c').each(function(index) {
        $(this).find('input.name_c').removeClass('is-valid');
        $(this).find('input.code_c').removeClass('is-valid');
        $(this).find('textarea.description_c').removeClass('is-valid');
    });
}

function AddIsInvalidClassCreateCategoryAndSubcategories(input) {
    if (!$(`#${input}_c`).hasClass('is-valid')) {
        $(`#${input}_c`).addClass('is-invalid');
    }

    if (!$(`#span[aria-labelledby="select2-${input}_c-container`).hasClass('is-valid')) {
        $(`span[aria-labelledby="select2-${input}_c-container"]`).addClass('is-invalid');
    }

    $('.subcategories_c').find('div.subcategory_c').each(function(index) {
        // Agrega la clase 'is-invalid'
        if(input === `subcategories.${index}.name`) {
            if (!$(this).find('input.name_c').hasClass('is-valid')) {
                $(this).find('input.name_c').addClass('is-invalid');
            }
        }
        if(input === `subcategories.${index}.code`) {
            if (!$(this).find('input.code_c').hasClass('is-valid')) {
                $(this).find('input.code_c').addClass('is-invalid');
            }
        }
        if(input === `subcategories.${index}.description`) {
            if (!$(this).find('textarea.description_c').hasClass('is-valid')) {
                $(this).find('textarea.description_c').addClass('is-invalid');
            }
        }
    });
}

function RemoveIsInvalidClassCreateCategoryAndSubcategories() {
    $('#name_c').removeClass('is-invalid');
    $('#code_c').removeClass('is-invalid');
    $('#description_c').removeClass('is-invalid');
    $('span[aria-labelledby="select2-clothing_line_id_c-container"]').removeClass('is-invalid');

    $('.subcategories_c').find('div.subcategory_c').each(function(index) {
        $(this).find('input.name_c').removeClass('is-invalid');
        $(this).find('input.code_c').removeClass('is-invalid');
        $(this).find('textarea.description_c').removeClass('is-invalid');
    });
}
