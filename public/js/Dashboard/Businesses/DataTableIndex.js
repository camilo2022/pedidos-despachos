let tableBusinesses = $('#businesses').DataTable({
    processing: true,
    serverSide: true,
    ajax: {
        url: `/Dashboard/Businesses/Index/Query`,
        type: 'POST',
        data: function (request) {
            var columnMappings = {
                0: 'id',
                1: 'name',
                2: 'deleted_at',
                3: 'id'
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
            return response.data.businesses;
        },
        error: function (xhr, error, thrown) {
            toastr.error(xhr.responseJSON.error ? xhr.responseJSON.error.message : xhr.responseJSON.message);
        }
    },
    columns: [
        { data: 'id' },
        { data: 'name' },
        { data: 'branch' },
        { data: 'number_document' },
        { data: 'country' },
        { data: 'departament' },
        { data: 'city' },
        { data: 'address' },
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
                let btn = `<div class="text-center" style="width: 100%;">`;
                if (data == null) {
                    btn += `<a onclick="WarehouseBusinessModal(${row.id})" type="button"
                    class="btn btn-secondary btn-sm mr-2" title="Asignacion y remocion de bodegas.">
                        <i class="fas fa-warehouse text-white"></i>
                    </a>`;

                    btn += `<a onclick="EditBusinessModal(${row.id})" type="button"
                    class="btn btn-primary btn-sm mr-2" title="Editar sucursal de la empresa">
                        <i class="fas fa-pen text-white"></i>
                    </a>`;

                    btn += `<a onclick="DeleteBusiness(${row.id})" type="button"
                    class="btn btn-danger btn-sm mr-2" title="Eliminar sucursal de la empresa">
                        <i class="fas fa-trash text-white"></i>
                    </a>`;
                } else {
                    btn += `<a onclick="RestoreBusiness(${row.id})" type="button"
                    class="btn btn-info btn-sm mr-2"title="Restaurar sucursal de la empresa">
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
            targets: [0, 1, 2, 3]
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
