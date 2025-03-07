function AuditLibranzaModal(id) {
    $.ajax({
        url: `/Dashboard/Libranzas/Audit/${id}`,
        type: 'POST',
        data: {
            '_token': $('meta[name="csrf-token"]').attr('content')
        },
        success: function (response) {
            AuditLibranzaModalCleaned(response.data.audits);
            AuditLibranzaAjaxSuccess(response);
            $('#AuditLibranzaModal').modal('show');
        },
        error: function (xhr, textStatus, errorThrown) {
            AuditLibranzaAjaxError(xhr);
        }
    });
}

function AuditLibranzaModalCleaned(audits) {
    let auditHtml = '';

    $.each(audits, function (index, audit) {
        let user = audit.user ? `${audit.user.name} ${audit.user.last_name}` : 'Sistema';
        let date = new Date(audit.created_at).toLocaleString('es-ES');
        let eventIcon = '';
        let eventText = '';
        let changesHtml = '';
        let urlHtml = audit.url ? `<br><small class="text-muted">📍 Origen: <a href="${audit.url}" target="_blank">${audit.url}</a></small>` : '';

        switch (audit.event) {
            case 'created':
                eventIcon = '🟢';
                eventText = `<strong>Creó</strong> un ${audit.auditable_type} con ID ${audit.auditable_id}.`;
                changesHtml = '<strong>Valores iniciales:</strong><br>';
                Object.entries(audit.new_values).forEach(([key, value]) => {
                    if (typeof value === 'object') {
                        changesHtml += `<strong>- ${key}</strong>: <br>`;
                        Object.entries(value).forEach(([subKey, subValue]) => {
                            changesHtml += `<strong class="ml-4">- ${subKey}</strong>: "${subValue}"<br>`;
                        });
                    } else {
                        changesHtml += `<strong>- ${key}</strong>: "${value}"<br>`;
                    }
                });
                break;
            case 'updated':
                eventIcon = '🟡';
                eventText = `<strong>Actualizó</strong> un ${audit.auditable_type} con ID ${audit.auditable_id}.`;
                changesHtml = '<strong>Cambios realizados:</strong><br>';
                Object.entries(audit.old_values).forEach(([key, oldValue]) => {
                    let newValue = audit.new_values[key] || 'Sin cambios';
                    if (typeof oldValue === 'object' && typeof newValue === 'object') {
                        changesHtml += `<strong>- ${key}</strong>: <br>`;
                        Object.entries(oldValue).forEach(([subKey, subValue]) => {
                            changesHtml += `<strong class="ml-4">- ${subKey}</strong>: "${subValue}" → "${newValue[subKey]}"<br>`;
                        });
                    } else {
                        changesHtml += `<strong>- ${key}</strong>: "${oldValue}" → "${newValue}"<br>`;
                    }
                });
                break;
            case 'deleted':
                eventIcon = '🔴';
                eventText = `<strong>Eliminó</strong> un ${audit.auditable_type} con ID ${audit.auditable_id}.`;
                break;
            case 'restored':
                eventIcon = '🟣';
                eventText = `<strong>Restauró</strong> un ${audit.auditable_type} con ID ${audit.auditable_id}.`;
                break;
        }

        auditHtml += `
            <div class="list-group-item">
                <small class="text-muted">${date} - <strong>${user}</strong></small>
                <p class="mb-1">
                    ${eventIcon} ${eventText}
                    <br>${changesHtml}
                    ${urlHtml}
                </p>
            </div>
        `;
    });

    $('#AuditLibranzaBody').html(auditHtml);

}

function AuditLibranzaAjaxSuccess(response) {
    if(response.status === 200) {
        toastr.info(response.message);
        $('#AuditLibranzaModal').modal('hide');
    }
}

function AuditLibranzaAjaxError(xhr) {
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
        $.each(xhr.responseJSON.errors, function (field, messages) {
            $.each(messages, function (index, message) {
                toastr.error(message);
            });
        });
    }

    if(xhr.status === 500){
        toastr.error(xhr.responseJSON.error ? xhr.responseJSON.error.message : xhr.responseJSON.message);
    }
}
