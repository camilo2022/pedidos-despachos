function IndexSearchPerson() {
    if($('#search_person').val().length > 3) {
        $.ajax({
            url: `/Dashboard/POS/Person`,
            type: 'POST',
            data: {
                '_token': $('meta[name="csrf-token"]').attr('content'),
                'search': $('#search_client_number_document').prop("checked"),
                'value': $('#search_person').val()
            },
            success: function(response) {
                $("#people").empty();

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
                            IndexSelectPerson(item.record);
                        }
                    }
                });

                IndexPOSAjaxSuccess(response);
            },
            error: function(xhr, textStatus, errorThrown) {
                IndexPOSAjaxError(xhr);
            }
        });
    }
}

function IndexSelectPerson(person) {
    $('#person_number_document').val(person.number_document);
    $('#person_name').val(person.name.toUpperCase());
    $('#person_last_name').val(person.last_name.toUpperCase());
    $('#person_phone_number').val(person.phone_number.toUpperCase());
    $('#person_email').val(person.email.toUpperCase());
    $('#person_address').val(person.address.toUpperCase());
    if(person.employee != null){
        $('#employee_quota').val(person.employee.quota);
        let debt = 0;

        if(person.invoices.length > 0) {

        }

        $('#employee_debt').val(debt);
        $('#employee_available').val((person.employee.quota ?? 0) - debt);
        if((person.employee.quota ?? 0) - debt > 0) {
            $('#green').show();
            $('#red').hide();
            $('#black').hide();
            $('#text').text('Puede solicitar libranza.');
        } else {
            $('#green').hide();
            $('#red').show();
            $('#black').hide();
            $('#text').text('No puede solicitar libranza.');
        }
    } else {
        $('#green').hide();
        $('#red').hide();
        $('#black').show();
        $('#text').text('No es empleado.');
    }
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
