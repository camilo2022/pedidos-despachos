let tableOrders = $('#orders').DataTable({
    processing: true,
    serverSide: true,
    ajax: {
        url: `/Dashboard/Orders/Index/Query`,
        type: 'POST',
        data: function (request) {
            var columnMappings = {
                0: 'id',
                1: 'id',
                2: 'client_id',
                3: 'client_id',
                4: 'client_id',
                5: 'client_id',
                6: 'created_at',
                7: 'seller_date',
                8: 'seller_user_id',
                9: 'seller_status',
                10: 'wallet_status',
                11: 'dispatched_status',
                12: 'correria_id',
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
            response.recordsTotal = response.data.meta.pagination.count;
            response.recordsFiltered = response.data.meta.pagination.total;
            return response.data.orders;
        },
        error: function (xhr, error, thrown) {
            toastr.error(xhr.responseJSON.error ? xhr.responseJSON.error.message : xhr.responseJSON.message);
        }
    },
    columns: [
        {
            data: 'order_details',
            render: function (data, type, row) {
                let btn = '';
                if(data.length > 0) {
                    btn += '<button class="btn btn-sm btn-success dt-expand rounded-circle"><i class="fas fa-plus"></i</button>';
                }
                return btn;
            },
        },
        { data: 'id' },
        {
            data: 'client_id',
            render: function (data, type, row) {
                return `${row.client.client_number_document}-${row.client.client_branch_code}`;
            }
        },
        {
            data: 'client_id',
            render: function (data, type, row) {
                return row.client.client_name;
            }
        },
        {
            data: 'client_id',
            render: function (data, type, row) {
                return `${row.client.departament} - ${row.client.city}`;
            }
        },
        {
            data: 'client_id',
            render: function (data, type, row) {
                return row.client.client_branch_address;
            }
        },
        { data: 'created_at' },
        { data: 'seller_date' },
        {
            data: 'seller_user_id' ,
            render: function (data, type, row) {
                return `${row.seller_user.name} ${row.seller_user.last_name}`;
            }
        },
        {
            data: 'seller_status',
            render: function (data, type, row) {
                switch (data) {
                    case 'Pendiente':
                        return `<h5><span class="badge badge-pill badge-info"><i class="fas fa-arrows-rotate mr-2"></i>Pendiente</span></h5>`;
                        break;
                    case 'Cancelado':
                        return `<h5><span class="badge badge-pill badge-danger text-white"><i class="fas fa-xmark mr-2 text-white"></i>Cancelado</span></h5>`;
                        break;
                    case 'Aprobado':
                        return `<h5><span class="badge badge-pill badge-success"><i class="fas fa-check mr-2"></i>Aprobado</span></h5>`;
                        break;
                    default:
                        return `<h5><span class="badge badge-pill badge-info"><i class="fas fa-arrows-rotate mr-2"></i>Pendiente</span></h5>`;
                        break;
                }
            }
        },
        {
            data: 'wallet_status',
            render: function (data, type, row) {
                switch (data) {
                    case 'Pendiente':
                        return `<h5><span class="badge badge-pill badge-info"><i class="fas fa-arrows-rotate mr-2"></i>Pendiente</span></h5>`;
                        break;
                    case 'Cancelado':
                        return `<h5><span class="badge badge-pill badge-danger text-white"><i class="fas fa-xmark mr-2 text-white"></i>Cancelado</span></h5>`;
                        break;
                    case 'Suspendido':
                        return `<h5><span class="badge badge-pill badge-secondary text-white"><i class="fas fa-solid fa-clock-rotate-left mr-2 text-white"></i>Suspendido</span></h5>`;
                        break;
                    case 'En mora':
                        return `<h5><span class="badge badge-pill bg-orange text-white" style="color: white !important;"><i class="fas fa-dollar-sign mr-2 text-white"></i>En mora</span></h5>`;
                        break;
                    case 'Parcialmente Aprobado':
                        return `<h5><span class="badge badge-pill badge-warning text-white"><i class="fas fa-check mr-2"></i>Parcialmente Aprobado</span></h5>`;
                        break;
                    case 'Aprobado':
                        return `<h5><span class="badge badge-pill badge-success"><i class="fas fa-check-double mr-2"></i>Aprobado</span></h5>`;
                        break;
                    case 'Autorizado':
                        return `<h5><span class="badge badge-pill badge-success"><i class="fas fa-check-double mr-2"></i>Autorizado</span></h5>`;
                        break;
                    default:
                        return `<h5><span class="badge badge-pill badge-info"><i class="fas fa-arrows-rotate mr-2"></i>Pendiente</span></h5>`;
                        break;
                }
            }
        },
        {
            data: 'dispatch_status',
            render: function (data, type, row) {
                switch (data) {
                    case 'Pendiente':
                        return `<h5><span class="badge badge-pill badge-info"><i class="fas fa-arrows-rotate mr-2"></i>Pendiente</span></h5>`;
                        break;
                    case 'Cancelado':
                        return `<h5><span class="badge badge-pill badge-danger text-white"><i class="fas fa-xmark mr-2 text-white"></i>Cancelado</span></h5>`;
                        break;
                    case 'Parcialmente Aprobado':
                        return `<h5><span class="badge badge-pill badge-warning text-white"><i class="fas fa-check mr-2 text-white"></i>Parcialmente Aprobado</span></h5>`;
                        break;
                    case 'Aprobado':
                        return `<h5><span class="badge badge-pill badge-success"><i class="fas fa-check-double mr-2"></i>Aprobado</span></h5>`;
                        break;
                    case 'Parcialmente Despachado':
                        return `<h5><span class="badge badge-pill badge-secondary text-white"><i class="fas fa-share mr-2 text-white"></i>Parcialmente Despachado</span></h5>`;
                        break;
                    case 'Despachado':
                        return `<h5><span class="badge badge-pill badge-primary"><i class="fas fa-share-all mr-2"></i>Despachado</span></h5>`;
                        break;
                    default:
                        return `<h5><span class="badge badge-pill badge-info"><i class="fas fa-arrows-rotate mr-2"></i>Pendiente</span></h5>`;
                        break;
                }
            }
        },
        {
            data: 'correria_id',
            render: function (data, type, row) {
                return row.correria.code;
            }
        },
        {
            data: null,
            render: function (data, type, row) {
                let btn = `<div class="text-center" style="width: 100%;">`;

                btn += `<a href="/Dashboard/Orders/Download/${row.id}" type="button"
                class="btn bg-purple btn-sm mr-2" title="Descargar pdf del pedido." target="_blank">
                    <i class="fas fa-file-pdf text-white"></i>
                </a>`;

                btn += `<a href="/Dashboard/Orders/Details/Index/${row.id}" type="button"
                class="btn btn-info btn-sm mr-2" title="Visualizar detalles del pedido.">
                    <i class="fas fa-eye text-white"></i>
                </a>`;

                if (row.seller_status == 'Pendiente' || isCartera()) {
                    btn += `<a onclick="EditOrderModal(${row.id})" type="button"
                    class="btn btn-primary btn-sm mr-2" title="Editar pedido.">
                        <i class="fas fa-pen text-white"></i>
                    </a>`;
                }

                if (row.seller_status == 'Pendiente' && (isVendedor() || isVendedorEspecial() || row.seller_user_id == $('meta[name="user-id"]').attr('content'))) {
                    btn += `<a onclick="AssentOrder(${row.id})" type="button"
                    class="btn btn-success btn-sm mr-2" title="Asentar pedido.">
                        <i class="fas fa-check text-white"></i>
                    </a>`;

                    btn += `<a onclick="CancelOrder(${row.id})" type="button"
                    class="btn btn-danger btn-sm mr-2" title="Cancelar pedido.">
                        <i class="fas fa-xmark text-white"></i>
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

tableOrders.on('click', 'button.dt-expand', function (e) {
    let tr = e.target.closest('tr');
    let row = tableOrders.row(tr);

    let iconButton = $(this);

    if (row.child.isShown()) {
        row.child.hide();
        iconButton.html('<i class="fas fa-plus"></i>').removeClass('btn-danger').addClass('btn-success');
    } else {
        row.child(tableOrderDetails(row.data())).show();
        iconButton.html('<i class="fas fa-minus"></i>').removeClass('btn-success').addClass('btn-danger');
        $(`#orderDetails${row.data().id}`).DataTable({});
    }
});

function tableOrderDetails(row) {
    let columns  = '';
    let totalSum = 0;
    let quantitySum = 0;

    $.each(row.sizes, function(index, size) {
        columns += `<th>T${size.code}</th>`;
    });

    let table = `<table class="table table-bordered table-hover dataTable dtr-inline nowrap w-100" id="orderDetails${row.id}">
                    <thead>
                        <tr>
                            <th>PRECIO TOTAL</th>
                            <th>REFERENCIA</th>
                            <th>COLOR</th>
                            ${columns}
                            <th>TOTAL</th>
                            <th>OBSERVACION</th>
                            <th>ESTADO</th>
                        </tr>
                    </thead>
                    <tbody>`;

    $.each(row.order_details, function(index, order_detail) {
        table += `<tr>`;
        let quantities = 0;

        $.each(row.sizes, function(index, size) {
            quantities += order_detail[`T${size.code}`];
        });

        totalSum += quantities * order_detail.negotiated_price;
        quantitySum += quantities;

        table += `<td>${(quantities * order_detail.negotiated_price).toLocaleString('es-CO', { style: 'currency', currency: 'COP', minimumFractionDigits: 0, maximumFractionDigits: 0 })}</td>
                <td>${order_detail.product.code}</td>
                <td>${order_detail.color.name + ' - ' + order_detail.color.code}</td>`;

        $.each(row.sizes, function(index, size) {
            table += `<td>${order_detail[`T${size.code}`]}</td>`;
        });

        table += `<td>${quantities}</td>
            <td>${order_detail.seller_observation == null ? '' : order_detail.seller_observation}</td>`;

        switch (order_detail.status) {
            case 'Pendiente':
                table += `<td><span class="badge badge-pill badge-info"><i class="fas fa-arrows-rotate mr-2"></i>Pendiente</span></td>`;
                break;
            case 'Cancelado':
                table += `<td><span class="badge badge-pill badge-danger text-white"><i class="fas fa-xmark mr-2 text-white"></i>Cancelado</span></td>`;
                break;
            case 'Aprobado':
                table += `<td><span class="badge badge-pill badge-success"><i class="fas fa-check mr-2"></i>Aprobado</span></td>`;
                break;
            case 'Autorizado':
                table += `<td><span class="badge badge-pill badge-success"><i class="fas fa-check mr-2"></i>Autorizado</span></td>`;
                break;
            case 'Agotado':
                table += `<td><span class="badge badge-pill badge-warning" style="color:white !important;"><i class="fas fa-triangle-exclamation mr-2 text-white"></i>Agotado</span></td>`;
                break;
            case 'Suspendido':
                table += `<td><span class="badge badge-pill badge-secondary text-white"><i class="fas fa-solid fa-clock-rotate-left mr-2 text-white"></i>Suspendido</span></td>`;
                break;
            case 'Comprometido':
                table += `<td><span class="badge badge-pill bg-purple" style="color:white !important;"><i class="fas fa-filter mr-2 text-white"></i>Comprometido</span></td>`;
                break;
            case 'Despachado':
                table += `<td><span class="badge badge-pill badge-primary"><i class="fas fa-share mr-2 text-white"></i>Despachado</span></td>`;
                break;
            default:
                table += `<td><span class="badge badge-pill badge-info"><i class="fas fa-arrows-rotate mr-2"></i>Pendiente</span></td>`;
                break;
        };

        table += `</tr>`;
    })

    let partials = '';

    $.each(row.sizes, function(index, size) {
        let sizeSum = 0;
        $.each(row.order_details, function(i, order_detail) {
            sizeSum += order_detail[`T${size.code}`];
        });
        partials += `<th>${sizeSum}</th>`;
    });

    table += `</tbody>
                <tfoot>
                    <tr>
                        <th>${totalSum.toLocaleString('es-CO', { style: 'currency', currency: 'COP', minimumFractionDigits: 0, maximumFractionDigits: 0 })}</th>
                        <th>-</th>
                        <th>-</th>
                        ${partials}
                        <th>${quantitySum}</th>
                        <th>-</th>
                        <th>-</th>
                    </tr>
                </tfoot>
            </table>`;


    return table;
}

