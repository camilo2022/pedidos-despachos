let tableSales = $('#sales').DataTable({
    ajax: {
        url: '/Dashboard/Reports/Sales/Index/Query',
        type: 'POST',
        data: function(request) {
            request._token = $('meta[name="csrf-token"]').attr('content');
        },
        dataSrc: function (response) {
            return response.data;
        },
    },
    columns: [
        {
            data: null,
            render: function(data, type, row) {
                return 'PEDIDO';
            }
        },
        {
            data: 'order',
            render: function(data, type, row) {
                return data.id;
            }
        },
        { data: 'id' },
        {
            data: 'order',
            render: function(data, type, row) {
                return data.client.client_name;
            }
        },
        {
            data: 'order',
            render: function(data, type, row) {
                return `${data.client.client_number_document}-${data.client.client_branch_code}`;
            }
        },
        {
            data: 'order',
            render: function(data, type, row) {
                return data.client.client_branch_address;
            }
        },
        {
            data: 'order',
            render: function(data, type, row) {
                return data.client.departament;
            }
        },
        {
            data: 'order',
            render: function(data, type, row) {
                return data.client.city;
            }
        },
        {
            data: 'order',
            render: function(data, type, row) {
                return data.client.zone;
            }
        },
        {
            data: 'order',
            render: function(data, type, row) {
                return data.dispatch_type;
            }
        },
        {
            data: 'order',
            render: function(data, type, row) {
                return data.dispatch_date;
            }
        },
        {
            data: 'product',
            render: function(data, type, row) {
                return data.trademark;
            }
        },
        {
            data: 'product',
            render: function(data, type, row) {
                return data.category;
            }
        },
        {
            data: 'product',
            render: function(data, type, row) {
                return data.description;
            }
        },
        {
            data: 'product',
            render: function(data, type, row) {
                return data.code;
            }
        },
        {
            data: 'color',
            render: function(data, type, row) {
                return `${data.name} - ${data.code}`;
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
        { data: 'TOTAL' },
        { data: 'seller_date' },
        { data: 'seller_observation' },
        {
            data: 'wallet_user',
            render: function(data, type, row) {
                return data == null ? '-' : `${data.name} ${data.last_name}`;
            }
        },
        { data: 'wallet_date' },
        {
            data: 'dispatch_user',
            render: function(data, type, row) {
                return data == null ? '-' : `${data.name} ${data.last_name}`;
            }
        },
        { data: 'dispatch_date' },
        { data: 'status' },
        {
            data: 'order',
            render: function(data, type, row) {
                return data.seller_user == null ? '-' : `${data.seller_user.name} ${data.seller_user.last_name}`;
            }
        },
        {
            data: 'order',
            render: function(data, type, row) {
                return data.seller_status;
            }
        },
        {
            data: 'order',
            render: function(data, type, row) {
                return data.seller_date;
            }
        },
        {
            data: 'order',
            render: function(data, type, row) {
                return data.seller_observation ?? '';
            }
        },
        {
            data: 'order',
            render: function(data, type, row) {
                return `${data.seller_dispatch_official} %`;
            }
        },
        {
            data: 'order',
            render: function(data, type, row) {
                return `${data.seller_dispatch_document} %`;
            }
        },
        {
            data: 'order',
            render: function(data, type, row) {
                return data.wallet_user == null ? '-' : `${data.wallet_user.name} ${data.wallet_user.last_name}`;
            }
        },
        {
            data: 'order',
            render: function(data, type, row) {
                return data.wallet_status;
            }
        },
        {
            data: 'order',
            render: function(data, type, row) {
                return data.wallet_date ?? '';
            }
        },
        {
            data: 'order',
            render: function(data, type, row) {
                return data.wallet_observation ?? '';
            }
        },
        {
            data: 'order',
            render: function(data, type, row) {
                return `${data.wallet_dispatch_official} %`;
            }
        },
        {
            data: 'order',
            render: function(data, type, row) {
                return `${data.wallet_dispatch_document} %`;
            }
        },
        {
            data: 'order',
            render: function(data, type, row) {
                return data.dispatch_status;
            }
        },
        {
            data: 'order',
            render: function(data, type, row) {
                return data.dispatched_date ?? '';
            }
        },
        {
            data: 'order',
            render: function(data, type, row) {
                return `${data.correria.name} - ${data.correria.code}`;
            }
        }
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

        api.columns([16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26, 27, 28, 29, 30, 31, 32, 33, 34, 35, 36, 37, 38, 39, 40, 41]).every(function () {
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

tableSales.on('draw.dt', function () {
    let api = $('#sales').DataTable();
    let numFormat = $.fn.dataTable.render.number('.', ',', 0, '').display;

    api.columns([16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26, 27, 28, 29, 30, 31, 32, 33, 34, 35, 36, 37, 38, 39, 40, 41], { search: 'applied' }).every(function () {
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