let tableUsers = $('#users').DataTable({
    processing: true,
    serverSide: true,
    ajax: {
        url: `/Dashboard/Users/Index/Query`,
        type: 'POST',
        data: function (request) {
            var columnMappings = {
                0: 'id',
                1: 'name',
                2: 'last_name',
                3: 'document_number',
                4: 'phone_number',
                5: 'address',
                6: 'email',
                7: 'title',
                8: 'zona',
                9: 'business_id',
                10: 'deleted_at',
                11: 'id',
                12: 'id',
                13: 'id',
                14: 'id',
                15: 'id',
                16: 'id',
                17: 'id'
            };
            request._token = $('meta[name="csrf-token"]').attr('content');
            request.perPage = request.length;
            request.page = (request.start / request.length) + 1;
            request.search = request.search.value;
            request.column = columnMappings[request.order[0].column];
            request.dir = request.order[0].dir;
        },
        dataSrc: function (response) {
            response.recordsTotal = response.data.meta.pagination.count;
            response.recordsFiltered = response.data.meta.pagination.total;
            return response.data.users;
        },
        error: function (xhr, error, thrown) {
            toastr.error(xhr.responseJSON.error ? xhr.responseJSON.error.message : xhr.responseJSON.message);
        }
    },
    columns: [
        { data: 'id' },
        { data: 'name' },
        { data: 'last_name' },
        { data: 'document_number' },
        { data: 'phone_number' },
        { data: 'address' },
        { data: 'email' },
        { data: 'title' },
        { data: 'zone' },
        {
            data: 'business',
            render: function (data, type, row) {
                return data.name;
            }
        },
        {
            data: 'deleted_at',
            render: function (data, type, row) {
                if (data == null) {
                    return `<h5><span class="badge badge-pill badge-success text-center"><i class="fas fa-check mr-2"></i>Activo</span></h5>`;
                } else {
                    return `<h5><span class="badge badge-pill badge-danger"><i class="fas fa-xmark mr-2"></i>Inactivo</span></h5>`;
                }
            }
        },
        {
            data: 'deleted_at',
            render: function (data, type, row) {
                if (data == null && isAdministrador()) {
                    return `<div class="text-center" style="width: 100%;">
                    <a class="btn btn-secondary btn-sm" onclick="WarehouseUserModal(${row.id})"
                    type="button" title="Asignacion y remocion de bodegas.">
                        <i class="fas fa-user-tag text-white"></i>
                    </a></div>`;
                } else {
                    return '';
                }
            }
        },
        {
            data: 'deleted_at',
            render: function (data, type, row) {
                if (data == null && isAdministrador()) {
                    return `<div class="text-center" style="width: 100%;">
                    <a class="btn bg-dark btn-sm" onclick="PasswordUserModal(${row.id})"
                    type="button" title="Recuperar contraseña.">
                        <i class="fas fa-user-gear text-white"></i>
                    </a></div>`;
                } else {
                    return '';
                }
            }
        },
        {
            data: 'deleted_at',
            render: function (data, type, row) {
                if (data == null && isAdministrador()) {
                    return `<div class="text-center" style="width: 100%;">
                    <a class="btn btn-primary btn-sm" onclick="EditUserModal(${row.id})" 
                    type="button" title="Editar usuario.">
                        <i class="fas fa-user-pen text-white"></i>
                    </a></div>`;
                } else {
                    return '';
                }
            }
        },
        {
            data: 'deleted_at',
            render: function (data, type, row) {
                if (data == null && isAdministrador()) {
                    return `<div class="text-center" style="width: 100%;">
                    <a class="btn btn-danger btn-sm" onclick="DeleteUser(${row.id})"
                    type="button" title="Eliminar usuario.">
                        <i class="fas fa-user-minus text-white"></i>
                    </a></div>`;
                } else {
                    return '';
                }
            }
        },
        {
            data: 'deleted_at',
            render: function (data, type, row) {
                if (data != null && isAdministrador()) {
                    return `<div class="text-center" style="width: 100%;">
                    <a class="btn btn-info btn-sm" onclick="RestoreUser(${row.id})"
                    type="button" title="Restaurar usuario.">
                        <i class="fas fa-user-plus text-white"></i>
                    </a></div>`;
                } else {
                    return '';
                }
            }
        },
        {
            data: 'deleted_at',
            render: function (data, type, row) {
                if (data == null && isAdministrador()) {
                    return `<div class="text-center" style="width: 100%;">
                    <a onclick="AssignRoleAndPermissionUserModal(${row.id}, '${CleanText(row.email)}')"
                    class="btn btn-success btn-sm" type="button" title="Asignar rol y permisos al usuario.">
                        <i class="fas fa-user-unlock text-white"></i>
                    </a></div>`;
                } else {
                    return '';
                }
            }
        },
        {
            data: 'deleted_at',
            render: function (data, type, row) {
                if (data == null && isAdministrador()) {
                    return `<div class="text-center" style="width: 100%;">
                    <a onclick="RemoveRoleAndPermissionUserModal(${row.id}, '${CleanText(row.email)}')"
                    class="btn bg-orange btn-sm" type="button" title="Remover rol y permisos al usuario.">
                        <i class="fas fa-user-lock text-white"></i>
                    </a></div>`;
                } else {
                    return '';
                }
            }
        },
    ],
    columnDefs: [
        {
            orderable: true,
            targets: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16]
        },
        {
            orderable: false,
            targets: []
        }
    ],
    pagingType: 'full_numbers',
    language: {
        oPaginate: {
            sFirst: 'Primero',
            sLast: 'Último',
            sNext: 'Siguiente',
            sPrevious: 'Anterior',
        },
        info: 'Mostrando _START_ a _END_ de _TOTAL_ registros',
        infoEmpty: 'No hay registros para mostrar',
        infoFiltered: '(filtrados de _MAX_ registros en total)',
        emptyTable: 'No hay datos disponibles.',
        lengthMenu: 'Mostrar _MENU_ registros por página.',
        search: 'Buscar:',
        zeroRecords: 'No se encontraron registros coincidentes.',
        decimal: ',',
        thousands: '.',
        sEmptyTable: 'No se ha llamado información o no está disponible.',
        sZeroRecords: 'No se encuentran resultados.',
        sProcessing: 'Procesando...'
    },
    pageLength: 10,
    lengthMenu: [10, 25, 50, 100],
    paging: true,
    info: true,
    searching: true,
    autoWidth: true
});
