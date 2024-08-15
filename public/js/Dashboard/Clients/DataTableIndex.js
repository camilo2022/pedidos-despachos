let tableClients = $('#clients').DataTable({
    processing: true,
    serverSide: true,
    ajax: {
        url: `/Dashboard/Clients/Index/Query`,
        type: 'POST',
        data: function (request) {
            var columnMappings = {
                0: 'id',
                1: 'client_name',
                2: 'client_address',
                3: 'client_number_document',
                4: 'client_number_phone',
                5: 'client_branch_code',
                6: 'client_branch_name',
                7: 'client_branch_address',
                8: 'client_branch_number_phone',
                9: 'departament',
                10: 'city',
                11: 'number_phone',
                12: 'email',
                13: 'zone',
                14: 'deleted_at',
                15: 'id'
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
            return response.data.clients;
        },
        error: function (xhr, error, thrown) {
            toastr.error(xhr.responseJSON.error ? xhr.responseJSON.error.message : xhr.responseJSON.message);
        }
    },
    columns: [
        { data: 'id' },
        { data: 'client_name' },
        { data: 'client_address' },
        { data: 'client_number_document' },
        { data: 'client_number_phone' },
        { data: 'client_branch_code' },
        { data: 'client_branch_name' },
        { data: 'client_branch_address' },
        { data: 'client_branch_number_phone' },
        { data: 'departament' },
        { data: 'city' },
        { data: 'number_phone' },
        { data: 'email' },
        { data: 'zone' },
        {
            data: 'deleted_at',
            render: function (data, type, row) {
                if (data === null) {
                    return `<h5><span class="badge badge-pill badge-success"><i class="fas fa-check mr-2"></i>Activa</span></h5>`;
                } else {
                    return `<h5><span class="badge badge-pill badge-danger"><i class="fas fa-xmark mr-2"></i>Inactiva</span></h5>`;
                }
            }
        },
        {
            data: 'deleted_at',
            render: function (data, type, row) {
                let btn = `<div class="text-center" style="width: 100%;">`;
                if (data === null) {
                    btn += `<a onclick="DataClientModal(${row.id})" type="button"
                    class="btn btn-info btn-sm mr-2" title="Datos cliente.">
                        <i class="fas fa-eye text-white"></i>
                    </a>`;

                    btn += `<a onclick="WalletClientModal(${row.id})" type="button"
                    class="btn btn-secondary btn-sm mr-2" title="Cartera cliente.">
                        <i class="fas fa-wallet text-white"></i>
                    </a>`;
                    
                    btn += `<a onclick="EditClientModal(${row.id})" type="button"
                    class="btn btn-primary btn-sm mr-2" title="Editar cliente.">
                        <i class="fas fa-pen text-white"></i>
                    </a>`;

                    btn += `<a onclick="DeleteClient(${row.id})" type="button"
                    class="btn btn-danger btn-sm mr-2" title="Eliminar cliente.">
                        <i class="fas fa-trash text-white"></i>
                    </a>`;
                } else {
                    btn += `<a onclick="RestoreClient(${row.id})" type="button"
                    class="btn btn-info btn-sm mr-2" title="Restaurar cliente.">
                        <i class="fas fa-arrow-rotate-left text-white"></i>
                    </a>`;
                }

                btn += `</div>`;
                return btn;
            }
        }
    ],
    columnDefs: [
        {
            orderable: true,
            targets: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15]
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
