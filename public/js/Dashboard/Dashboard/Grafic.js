GraficDashboardCorreria();

function GraficDashboardCorreria(correria_id = null) {
    $.ajax({
        url: `/Dashboard/Chart/Correria`,
        type: 'POST',
        data: {
            '_token': $('meta[name="csrf-token"]').attr('content'),
            'correria_id': correria_id
        },
        success: function(response) {
            let timestamp = new Date().getTime();
            $('#ChartCorreria').html(`<img src="${response.data.chart}?t=${timestamp}" alt="DISTRIBUCION DE UNIDADES EN ESTADOS POR CORRERIAS" height="300px" style="width: auto;">`);
            GraficDashboardAjaxSuccess(response);
        },
        error: function(xhr, textStatus, errorThrown) {
            GraficDashboardAjaxError(xhr);
        }
    });
}

function GraficDashboardAjaxSuccess(response) {
    if(response.status === 200) {
        toastr.success(response.message);
    }
}

function GraficDashboardAjaxError(xhr) {
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
