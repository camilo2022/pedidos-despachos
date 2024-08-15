let tableInventories = $('#inventories').DataTable({
    processing: true,
    serverSide: true,
    ajax: {
        url: `/Dashboard/Inventories/Index/Query`,
        type: 'POST',
        data: function (request) {
            var columnMappings = {
                0: 'warehouse_id',
                1: 'trademark',
                2: 'products.code',
                3: 'color_id',
                29: 'system'
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
            return response.data.inventories;
        },
        error: function (xhr, error, thrown) {
            toastr.error(xhr.responseJSON.error ? xhr.responseJSON.error.message : xhr.responseJSON.message);
        }
    },
    columns: [
        {
            data: null,
            render: function (data, type, row) {
                return `${row.BODEGA} - ${row.CODBOD}`;
            }
        },
        { data: 'MARCA' },
        { data: 'REFERENCIA' },
        {
            data: null,
            render: function (data, type, row) {
                return `${row.COLOR} - ${row.CODCOL}`;
            }
        },
        { data: 'T04' },
        { data: 'T06' },
        { data: 'T08' },
        { data: 'T10' },
        { data: 'T12' },
        { data: 'T14' },
        { data: 'T16' },
        { data: 'T18' },
        { data: 'T20' },
        { data: 'T22' },
        { data: 'T24' },
        { data: 'T26' },
        { data: 'T28' },
        { data: 'T30' },
        { data: 'T32' },
        { data: 'T34' },
        { data: 'T36' },
        { data: 'T38' },
        { data: 'TXXS' },
        { data: 'TXS' },
        { data: 'TS' },
        { data: 'TM' },
        { data: 'TL' },
        { data: 'TXL' },
        { data: 'TXXL' },
        { data: 'SISTEMA' }
    ],
    columnDefs: [
        {
            targets: 0,
            orderable: true,
            targets: [0, 1, 2, 3, 29]
        },
        {
            targets: 0,
            orderable: false,
            targets: [4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26, 27, 28]
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
