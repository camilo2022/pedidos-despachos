let tableProducts = $('#products').DataTable({
    ajax: {
        url: '/Dashboard/Reports/Products/Index/Query',
        type: 'POST',
        data: function(request) {
            request._token = $('meta[name="csrf-token"]').attr('content');
        },
        dataSrc: function (response) {
            return response.data;
        },
    },
    columns: [
        { data: 'type' },
        { data: 'trademark' },
        { data: 'product' },
        { data: 'color' },
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
        { data: 'TOTAL' }
    ],
    dom: 'lBfrtip',
    buttons: [
        {
            extend: 'copy',
            text: '<i class="fas fa-copy"></i>',
            className: 'btn bg-dark',
            titleAttr: 'Copiar'
        },
        {
            extend: 'csv',
            text: '<i class="fas fa-file-csv"></i>',
            className: 'btn bg-dark',
            titleAttr: 'Exportar CSV'
        },
        {
            extend: 'excel',
            text: '<i class="fas fa-file-excel"></i>',
            className: 'btn bg-dark',
            titleAttr: 'Exportar Excel'
        },
        {
            extend: 'pdf',
            text: '<i class="fas fa-file-pdf"></i>',
            className: 'btn bg-dark',
            titleAttr: 'Exportar PDF'
        },
        {
            extend: 'print',
            text: '<i class="fas fa-print"></i>',
            className: 'btn bg-dark',
            titleAttr: 'Imprimir'
        }
    ],
    initComplete: function() {
        $('.dt-button').removeClass('dt-button');
    },
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
    autoWidth: true,
    footerCallback: function (row, data, start, end, display) {
        let api = this.api();
        let numFormat = $.fn.dataTable.render.number('.', ',', 0, '').display;

        api.columns([4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26, 27, 28, 29]).every(function () {
            let sum = this
                .data()
                .reduce(function (a, b) {
                    let x = parseFloat(a) || 0;
                    let y = parseFloat(b) || 0;
                    return x + y;
                }, 0);

            $(this.footer()).html(numFormat(sum));
        });
    }
});

tableProducts.on('draw.dt', function () {
    let api = $('#products').DataTable();
    let numFormat = $.fn.dataTable.render.number('.', ',', 0, '').display;

    api.columns([4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26, 27, 28, 29], { search: 'applied' }).every(function () {
        let sum = this
            .nodes()
            .to$()
            .map(function (index, td) {
                return $(td).text();
            })
            .toArray()
            .reduce(function (a, b) {
                let x = parseFloat(a) || 0;
                let y = parseFloat(b) || 0;
                return x + y;
            }, 0);

        $(this.footer()).html(numFormat(sum));
    });
});