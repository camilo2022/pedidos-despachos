let tableOrders = $('#orders').DataTable({
    processing: true,
    serverSide: true,
    ajax: {
        url: `/Dashboard/Libranzas/Index/Query`,
        type: 'POST',
        data: function (request) {
            var columnMappings = {
                0: 'id',
                1: 'invoice_id',
                2: 'invoice_id',
                3: 'invoice_id',
                4: 'created_at',
                5: 'user_id',
                6: 'value',
                7: 'value',
                8: 'value',
                9: 'share',
                10: 'share',
                11: 'share',
                12: 'status',
                13: 'id'
            };
            request._token = $('meta[name="csrf-token"]').attr('content');
            request.perPage = request.length;
            request.page = (request.start / request.length) + 1;
            request.search = request.search.value;
            request.column = columnMappings[request.order[0].column];
            request.dir = request.order[0].dir;
        },
        dataSrc: function (response) {
            response.recordsTotal = response.data.libranzas.meta.pagination.count;
            response.recordsFiltered = response.data.libranzas.meta.pagination.total;
            console.log(response.data.values)
            $('#total').text(new Intl.NumberFormat('es-CO', { maximumFractionDigits: 0 }).format(response.data.values.total));
            $('#pay').text(new Intl.NumberFormat('es-CO', { maximumFractionDigits: 0 }).format(response.data.values.pay));
            $('#debt').text(new Intl.NumberFormat('es-CO', { maximumFractionDigits: 0 }).format(response.data.values.debt));
            console.log(response.data.libranzas.libranzas);
            return response.data.libranzas.libranzas;
        },
        error: function (xhr, error, thrown) {
            toastr.error(xhr.responseJSON.error ? xhr.responseJSON.error.message : xhr.responseJSON.message);
        }
    },
    columns: [
        { data: 'id' },
        {
            data: 'invoice_id',
            render: function (data, type, row) {
                return row.invoice.reference;
            }
        },
        {
            data: 'invoice_id',
            render: function (data, type, row) {
                return row.invoice.model.number_document;
            }
        },
        {
            data: 'invoice_id',
            render: function (data, type, row) {
                return `${row.invoice.model.name} ${row.invoice.model.last_name}`;
            }
        },
        { data: 'created_at' },
        {
            data: 'invoice_id',
            render: function (data, type, row) {
                return row.invoice.cash_register ? row.invoice.cash_register.store.name : row.user.name ;
            }
        },
        {
            data: 'value',
            render: function (data, type, row) {
                return `<h5><span class="badge badge-primary">${new Intl.NumberFormat('es-CO', { style: 'currency', currency: 'COP', maximumFractionDigits: 0 }).format(row.value)}</span></h5>`;
            }
        },
        {
            data: 'value',
            render: function (data, type, row) {
                return `<h5><span class="badge badge-success">${new Intl.NumberFormat('es-CO', { style: 'currency', currency: 'COP', maximumFractionDigits: 0 }).format(row.libranza_discounts.reduce((total, item) => total + item.value, 0))}</span></h5>`;
            }
        },
        {
            data: 'value',
            render: function (data, type, row) {
                return `<h5><span class="badge badge-danger">${new Intl.NumberFormat('es-CO', { style: 'currency', currency: 'COP', maximumFractionDigits: 0 }).format(row.value - row.libranza_discounts.reduce((total, item) => total + item.value, 0))}</span></h5>`;
            }
        },
        { data: 'share' },
        {
            data: 'invoice_id',
            render: function (data, type, row) {
                return `${row.libranza_discounts.length} de ${row.share}` ;
            }
        },
        {
            data: 'value',
            render: function (data, type, row) {
                return `<h5><span class="badge badge-info">${new Intl.NumberFormat('es-CO', { style: 'currency', currency: 'COP', maximumFractionDigits: 0 }).format(row.value / row.share)}</span></h5>`;
            }
        },
        {
            data: 'status',
            render: function (data, type, row) {
                switch (data) {
                    case 'Pendiente':
                        return `<h5><span class="badge badge-info"><i class="fas fa-arrows-rotate mr-2"></i>Pendiente</span></h5>`;
                        break;
                    case 'Cancelado':
                        return `<h5><span class="badge badge-danger text-white"><i class="fas fa-xmark mr-2 text-white"></i>Cancelado</span></h5>`;
                        break;
                    case 'Aprobado':
                        return `<h5><span class="badge badge-success"><i class="fas fa-check mr-2"></i>Aprobado</span></h5>`;
                        break;
                    case 'Proceso':
                        return `<h5><span class="badge bg-orange text-white" style="color: white !important;"><i class="fas fa-dollar-sign mr-2 text-white"></i>Proceso</span></h5>`;
                        break;
                    case 'Pagado':
                        return `<h5><span class="badge badge-primary"><i class="fas fa-money-bill mr-2"></i>Pagado</span></h5>`;
                        break;
                    default:
                        return `<h5><span class="badge badge-info"><i class="fas fa-arrows-rotate mr-2"></i>Pendiente</span></h5>`;
                        break;
                }
            }
        },
        {
            data: 'id',
            render: function (data, type, row) {
                let btn = `<div class="text-center" style="width: 100%;">`;

                btn += `<a href="/Dashboard/Orders/Download/${row.id}" type="button"
                class="btn bg-purple btn-sm mr-2" title="Descargar pdf del pedido." target="_blank">
                    <i class="fas fa-file-pdf text-white"></i>
                </a>`;

                btn += `<a onclick="AssentOrder(${row.id})" type="button"
                class="btn btn-info btn-sm mr-2" title="Visualizar detalles del pedido.">
                    <i class="fas fa-eye text-white"></i>
                </a>`;

                btn += `<a onclick="AssentOrder(${row.id})" type="button"
                class="btn btn-success btn-sm mr-2" title="Asentar pedido.">
                    <i class="fas fa-check text-white"></i>
                </a>`;

                btn += `<a onclick="CancelOrder(${row.id})" type="button"
                class="btn btn-danger btn-sm mr-2" title="Cancelar pedido.">
                    <i class="fas fa-xmark text-white"></i>
                </a>`;

                btn += `<a onclick="CancelOrder(${row.id})" type="button"
                class="btn btn-primary btn-sm mr-2" title="Cancelar pedido.">
                    <i class="fas fa-money-bill text-white"></i>
                </a>`;

                btn += `</div>`;
                return btn;
            }
        },
    ],
    columnDefs: [
        {
            orderable: true,
            targets: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13]
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
