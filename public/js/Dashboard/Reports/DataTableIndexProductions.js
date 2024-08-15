let tableProductions = $('#productions').DataTable({
    ajax: {
        url: '/Dashboard/Reports/Productions/Index/Query',
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
            data: 'order_dispatch_detail',
            render: function(data, type, row) {
                return data == null ? 'PEDIDO' : 'ORDEN DESPACHO';
            }
        },
        {
            data: 'order',
            render: function(data, type, row) {
                return row.id;
            }
        },
        { data: 'id' },
        {
            data: 'order_dispatch_detail',
            render: function(data, type, row) {
                return data == null ? '-' : row.order_dispatch_detail.order_dispatch.consecutive;
            }
        },
        {
            data: 'order_dispatch_detail',
            render: function(data, type, row) {
                return data == null ? '-' : row.order_dispatch_detail.id;
            }
        },
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
        {
            data: 'order_dispatch_detail',
            render: function(data, type, row) {
                return data == null ? row.T04 : row.order_dispatch_detail.T04;
            }
        },
        {
            data: 'order_dispatch_detail',
            render: function(data, type, row) {
                return data == null ? row.T06 : row.order_dispatch_detail.T06;
            }
        },
        {
            data: 'order_dispatch_detail',
            render: function(data, type, row) {
                return data == null ? row.T08 : row.order_dispatch_detail.T08;
            }
        },
        {
            data: 'order_dispatch_detail',
            render: function(data, type, row) {
                return data == null ? row.T10 : row.order_dispatch_detail.T10;
            }
        },
        {
            data: 'order_dispatch_detail',
            render: function(data, type, row) {
                return data == null ? row.T12 : row.order_dispatch_detail.T12;
            }
        },
        {
            data: 'order_dispatch_detail',
            render: function(data, type, row) {
                return data == null ? row.T14 : row.order_dispatch_detail.T14;
            }
        },
        {
            data: 'order_dispatch_detail',
            render: function(data, type, row) {
                return data == null ? row.T16 : row.order_dispatch_detail.T16;
            }
        },
        {
            data: 'order_dispatch_detail',
            render: function(data, type, row) {
                return data == null ? row.T18 : row.order_dispatch_detail.T18;
            }
        },
        {
            data: 'order_dispatch_detail',
            render: function(data, type, row) {
                return data == null ? row.T20 : row.order_dispatch_detail.T20;
            }
        },
        {
            data: 'order_dispatch_detail',
            render: function(data, type, row) {
                return data == null ? row.T22 : row.order_dispatch_detail.T22;
            }
        },
        {
            data: 'order_dispatch_detail',
            render: function(data, type, row) {
                return data == null ? row.T24 : row.order_dispatch_detail.T24;
            }
        },
        {
            data: 'order_dispatch_detail',
            render: function(data, type, row) {
                return data == null ? row.T26 : row.order_dispatch_detail.T26;
            }
        },
        {
            data: 'order_dispatch_detail',
            render: function(data, type, row) {
                return data == null ? row.T28 : row.order_dispatch_detail.T28;
            }
        },
        {
            data: 'order_dispatch_detail',
            render: function(data, type, row) {
                return data == null ? row.T30 : row.order_dispatch_detail.T30;
            }
        },
        {
            data: 'order_dispatch_detail',
            render: function(data, type, row) {
                return data == null ? row.T32 : row.order_dispatch_detail.T32;
            }
        },
        {
            data: 'order_dispatch_detail',
            render: function(data, type, row) {
                return data == null ? row.T34 : row.order_dispatch_detail.T34;
            }
        },
        {
            data: 'order_dispatch_detail',
            render: function(data, type, row) {
                return data == null ? row.T36 : row.order_dispatch_detail.T36;
            }
        },
        {
            data: 'order_dispatch_detail',
            render: function(data, type, row) {
                return data == null ? row.T38 : row.order_dispatch_detail.T38;
            }
        },
        {
            data: 'order_dispatch_detail',
            render: function(data, type, row) {
                return data == null ? row.TXXS : row.order_dispatch_detail.TXXS;
            }
        },
        {
            data: 'order_dispatch_detail',
            render: function(data, type, row) {
                return data == null ? row.TXS : row.order_dispatch_detail.TXS;
            }
        },
        {
            data: 'order_dispatch_detail',
            render: function(data, type, row) {
                return data == null ? row.TS : row.order_dispatch_detail.TS;
            }
        },
        {
            data: 'order_dispatch_detail',
            render: function(data, type, row) {
                return data == null ? row.TM : row.order_dispatch_detail.TM;
            }
        },
        {
            data: 'order_dispatch_detail',
            render: function(data, type, row) {
                return data == null ? row.TL : row.order_dispatch_detail.TL;
            }
        },
        {
            data: 'order_dispatch_detail',
            render: function(data, type, row) {
                return data == null ? row.TXL : row.order_dispatch_detail.TXL;
            }
        },
        {
            data: 'order_dispatch_detail',
            render: function(data, type, row) {
                return data == null ? row.TXXL : row.order_dispatch_detail.TXXL;
            }
        },
        {
            data: 'order_dispatch_detail',
            render: function(data, type, row) {
                return data == null ? row.TOTAL : row.order_dispatch_detail.TOTAL;
            }
        },
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
            data: 'order_dispatch_detail',
            render: function(data, type, row) {
                return data == null ? '-' : `${data.order_dispatch.dispatch_user.name} ${data.order_dispatch.dispatch_user.last_name}`;
            }
        },
        {
            data: 'order_dispatch_detail',
            render: function(data, type, row) {
                return data == null ? '-' : data.order_dispatch.dispatch_status;
            }
        },
        {
            data: 'order_dispatch_detail',
            render: function(data, type, row) {
                return data == null ? '-' : data.order_dispatch.created_at.replace('T', ' ').split('.')[0];;
            }
        },
        {
            data: 'order_dispatch_detail',
            render: function(data, type, row) {
                return data == null ? '-' : ( data.order_dispatch.invoice_user == null ? '-' : `${data.order_dispatch.invoice_user.name} ${data.order_dispatch.invoice_user.last_name}`);
            }
        },
        {
            data: 'order_dispatch_detail',
            render: function(data, type, row) {
                return data == null ? '-' : ( data.order_dispatch.invoice_date == null ? '-' : data.order_dispatch.invoice_date);
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
                return data.dispatched_date ?? '-';
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

        api.columns([18, 19, 20, 21, 22, 23, 24, 25, 26, 27, 28, 29, 30, 31, 32, 33, 34, 35, 36, 37, 38, 39, 40, 41, 42, 43]).every(function () {
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
    }
});

tableProductions.on('draw.dt', function () {
    let api = $('#productions').DataTable();
    let numFormat = $.fn.dataTable.render.number('.', ',', 0, '').display;

    api.columns([18, 19, 20, 21, 22, 23, 24, 25, 26, 27, 28, 29, 30, 31, 32, 33, 34, 35, 36, 37, 38, 39, 40, 41, 42, 43], { search: 'applied' }).every(function () {
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