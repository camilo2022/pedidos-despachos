function IndexPersonReferenceModal(person_id) {
    $.ajax({
        url: `/Dashboard/Clients/People/References/Index`,
        type: 'POST',
        data: {
            '_token': $('meta[name="csrf-token"]').attr('content'),
            'person_id': person_id
        },
        success: function (response) {
            IndexPersonReferenceModalCleaned(response.data);
            tablePersonReferences.ajax.reload();
            IndexPersonReferenceAjaxSuccess(response);
            $('#IndexPersonReferenceModal').modal('show');
        },
        error: function (xhr, textStatus, errorThrown) {
            tablePersonReferences.ajax.reload();
            IndexPersonReferenceAjaxError(xhr);
        }
    });
}

function IndexPersonReferenceModalCleaned(person) {
    $('#IndexPersonReferenceButton').attr('data-person_id', person.id);
}

function IndexPersonReferenceAjaxSuccess(response) {
    if(response.status === 200) {
        toastr.info(response.message);
        $('#IndexPersonReferenceModal').modal('hide');
    }
}

function IndexPersonReferenceAjaxError(xhr) {
    if (xhr.status === 403) {
        toastr.error(xhr.responseJSON.error ? xhr.responseJSON.error.message : xhr.responseJSON.message);
        $('#IndexPersonReferenceModal').modal('hide');
    }

    if (xhr.status === 404) {
        toastr.error(xhr.responseJSON.error ? xhr.responseJSON.error.message : xhr.responseJSON.message);
        $('#IndexPersonReferenceModal').modal('hide');
    }

    if (xhr.status === 419) {
        toastr.error(xhr.responseJSON.error ? xhr.responseJSON.error.message : xhr.responseJSON.message);
        $('#IndexPersonReferenceModal').modal('hide');
    }

    if (xhr.status === 422) {
        $.each(xhr.responseJSON.errors, function (field, messages) {
            $.each(messages, function (index, message) {
                toastr.error(message);
            });
        });
    }

    if (xhr.status === 500) {
        toastr.error(xhr.responseJSON.error ? xhr.responseJSON.error.message : xhr.responseJSON.message);
        $('#IndexPersonReferenceModal').modal('hide');
    }
}
