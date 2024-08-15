let tableWarehouses = $('#warehouses').DataTable({
    processing: true,
    serverSide: true,
    ajax: {
        url: `/Dashboard/Warehouses/Index/Query`,
        type: 'POST',
        data: function (request) {
            var columnMappings = {
                0: 'id',
                1: 'name',
                2: 'code',
                3: 'to_cut',
                4: 'to_transit',
                5: 'to_discount',
                6: 'to_exclusive',
                7: 'deleted_at',
                8: 'id'
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
            return response.data.warehouses;
        },
        error: function (xhr, error, thrown) {
            toastr.error(xhr.responseJSON.error ? xhr.responseJSON.error.message : xhr.responseJSON.message);
        }
    },
    columns: [
        { data: 'id' },
        { data: 'name' },
        { data: 'code' },
        {
            data: 'to_cut',
            render: function (data, type, row) {
                if (data == 1) {
                    return `<h5><span class="badge badge-pill badge-success"><i class="fas fa-circle-check mr-2"></i>Activo</span></h5>`;
                } else {
                    return `<h5><span class="badge badge-pill badge-danger"><i class="fas fa-circle-xmark mr-2"></i>Inactivo</span></h5>`;
                }
            }
        },
        {
            data: 'to_transit',
            render: function (data, type, row) {
                if (data == 1) {
                    return `<h5><span class="badge badge-pill badge-success"><i class="fas fa-circle-check mr-2"></i>Activo</span></h5>`;
                } else {
                    return `<h5><span class="badge badge-pill badge-danger"><i class="fas fa-circle-xmark mr-2"></i>Inactivo</span></h5>`;
                }
            }
        },
        {
            data: 'to_discount',
            render: function (data, type, row) {
                if (data == 1) {
                    return `<h5><span class="badge badge-pill badge-success"><i class="fas fa-circle-check mr-2"></i>Activo</span></h5>`;
                } else {
                    return `<h5><span class="badge badge-pill badge-danger"><i class="fas fa-circle-xmark mr-2"></i>Inactivo</span></h5>`;
                }
            }
        },
        {
            data: 'to_exclusive',
            render: function (data, type, row) {
                if (data == 1) {
                    return `<h5><span class="badge badge-pill badge-success"><i class="fas fa-circle-check mr-2"></i>Activo</span></h5>`;
                } else {
                    return `<h5><span class="badge badge-pill badge-danger"><i class="fas fa-circle-xmark mr-2"></i>Inactivo</span></h5>`;
                }
            }
        },
        {
            data: 'deleted_at',
            render: function (data, type, row) {
                if (data == null) {
                    return `<h5><span class="badge badge-pill badge-success"><i class="fas fa-check mr-2"></i>Activo</span></h5>`;
                } else {
                    return `<h5><span class="badge badge-pill badge-danger"><i class="fas fa-xmark mr-2"></i>Inactivo</span></h5>`;
                }
            }
        },
        {
            data: 'deleted_at',
            render: function (data, type, row) {
                let btn = `<div class="text-center" style="width: 100%;">`;
                if (isAdministrador()) {
                    if (data == null) {
                        btn += `<a onclick="EditWarehouseModal(${row.id})" type="button"
                        class="btn btn-primary btn-sm mr-2" title="Editar bodega.">
                            <i class="fas fa-pen text-white"></i>
                        </a>`;

                        btn += `<a onclick="DeleteWarehouse(${row.id})" type="button"
                        class="btn btn-danger btn-sm mr-2" title="Eliminar bodega.">
                            <i class="fas fa-trash text-white"></i>
                        </a>`;
                    } else {
                        btn += `<a onclick="RestoreWarehouse(${row.id})" type="button"
                        class="btn btn-info btn-sm mr-2" title="Restaurar bodega.">
                            <i class="fas fa-arrow-rotate-left text-white"></i>
                        </a>`;
                    }
                }
                btn += `</div>`;
                return btn;
            }
        }
    ],
    columnDefs: [
        {
            orderable: true,
            targets: [0, 1, 2, 3, 4]
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
