function AuditOrderModal(id) {
    $.ajax({
        url: `/Dashboard/Orders/Audit/${id}`,
        type: 'POST',
        data: {
            '_token': $('meta[name="csrf-token"]').attr('content')
        },
        success: function (response) {
            AuditOrderModalCleaned(response.data.audits);
            AuditOrderAjaxSuccess(response);
            $('#AuditOrderModal').modal('show');
        },
        error: function (xhr, textStatus, errorThrown) {
            AuditOrderAjaxError(xhr);
        }
    });
}

function AuditOrderModalCleaned(audits) {
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
                    changesHtml += `- <strong>${key}</strong>: "${value}"<br>`;
                });
                break;
            case 'updated':
                eventIcon = '🟡';
                eventText = `<strong>Actualizó</strong> un ${audit.auditable_type} con ID ${audit.auditable_id}.`;
                changesHtml = '<strong>Cambios realizados:</strong><br>';
                Object.entries(audit.old_values).forEach(([key, oldValue]) => {
                    let newValue = audit.new_values[key] || 'Sin cambios';
                    changesHtml += `- <strong>${key}</strong>: "${oldValue}" → "${newValue}"<br>`;
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

    $('#AuditOrderBody').html(auditHtml);
}

function AuditOrderAjaxSuccess(response) {
    if (response.status === 200) {
        toastr.info(response.message);
        $('#AuditOrderModal').modal('hide');
    }
}

function AuditOrderAjaxError(xhr) {
    if (xhr.status === 403) {
        toastr.error(xhr.responseJSON.error ? xhr.responseJSON.error.message : xhr.responseJSON.message);
        $('#AuditOrderModal').modal('hide');
    }

    if (xhr.status === 404) {
        toastr.error(xhr.responseJSON.error ? xhr.responseJSON.error.message : xhr.responseJSON.message);
        $('#AuditOrderModal').modal('hide');
    }

    if (xhr.status === 419) {
        toastr.error(xhr.responseJSON.error ? xhr.responseJSON.error.message : xhr.responseJSON.message);
        $('#AuditOrderModal').modal('hide');
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
        $('#AuditOrderModal').modal('hide');
    }
}
