let tableOrderDispatches = $('#orderDispatches').DataTable({
    processing: true,
    serverSide: true,
    ajax: {
        url: `/Dashboard/Dispatches/Index/Query`,
        type: 'POST',
        data: function (request) {
            var columnMappings = {
                0: 'id',
                1: 'id',
                2: 'consecutive',
                3: 'client_id',
                4: 'client_id',
                5: 'client_id',
                6: 'client_id',
                7: 'created_at',
                8: 'dispatch_date',
                9: 'dispatch_user_id',
                10: 'dispatch_status',
                11: 'correria_id',
                12: 'id',
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
            return response.data.orderDispatches;
        },
        error: function (xhr, error, thrown) {
            toastr.error(xhr.responseJSON.error ? xhr.responseJSON.error.message : xhr.responseJSON.message);
        }
    },
    columns: [
        {
            data: 'order_dispatch_details',
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
            data: 'consecutive',
            render: function (data, type, row) {
                return `<h5><span class="badge bg-info text-white"><i class="fas fa-paperclip mr-2 text-white"></i>${data}</span></h5>`
            }
        },
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
        { data: 'dispatch_date' },
        {
            data: 'dispatch_user_id' ,
            render: function (data, type, row) {
                return `${row.dispatch_user.name} ${row.dispatch_user.last_name}`;
            }
        },
        {
            data: 'dispatch_status',
            render: function (data, type, row) {
                switch (data) {
                    case 'Pendiente':
                        return `<h5><span class="badge badge-info"><i class="fas fa-arrows-rotate mr-2"></i>Pendiente</span></h5>`;
                        break;
                    case 'Cancelado':
                        return `<h5><span class="badge badge-danger"><i class="fas fa-xmark mr-2 text-white"></i>Cancelado</span></h5>`;
                        break;
                    case 'Alistamiento':
                        return `<h5><span class="badge badge-primary text-white"><i class="fas fa-barcode-read mr-2"></i>Alistamiento</span></h5>`;
                        break;
                    case 'Revision':
                        return `<h5><span class="badge badge-warning" style="color:white !important;"><i class="fas fa-gear mr-2 text-white"></i>Revision</span></h5>`;
                        break;
                    case 'Empacado':
                        return `<h5><span class="badge badge-secondary"><i class="fas fa-box-open-full mr-2"></i>Empacado</span></h5>`;
                        break;
                    case 'Facturacion':
                        return `<h5><span class="badge bg-orange text-white" style="color: white !important;"><i class="fas fa-money-bill mr-2 text-white"></i>Facturacion</span></h5>`;
                        break;
                    case 'Despachado':
                        return `<h5><span class="badge badge-success"><i class="fas fa-share-all mr-2"></i>Despachado</span></h5>`;
                        break;
                    default:
                        return `<h5><span class="badge badge-info"><i class="fas fa-arrows-rotate mr-2"></i>Pendiente</span></h5>`;
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

                btn += `<a href="/Dashboard/Dispatches/Print/${row.id}" type="button"
                class="btn bg-purple btn-sm mr-2" title="Descargar pdf de la orden de despacho." target="_blank">
                    <i class="fas fa-print text-white"></i>
                </a>`;

                btn += `<a href="/Dashboard/Dispatches/Details/Index/${row.id}" type="button" style="background: saddlebrown;"
                class="btn btn-sm mr-2" title="Visualizar detalles de la orden de despacho.">
                    <i class="fas fa-eye text-white"></i>
                </a>`;

                switch (row.dispatch_status) {
                    case 'Pendiente':
                        if((isFiltrador() || isCoordinadorBodega() || isAdministrador())) {
                            btn += `<a onclick="ApproveOrderDispatch(${row.id})" type="button"
                            class="btn btn-success btn-sm mr-2" title="Aprobar orden de despacho.">
                                <i class="fas fa-check text-white"></i>
                            </a>`;

                            btn += `<a onclick="CancelOrderDispatch(${row.id})" type="button"
                            class="btn btn-danger btn-sm mr-2" title="Cancelar orden de despacho.">
                                <i class="fas fa-xmark-large text-white"></i>
                            </a>`;
                        }
                        break;
                    case 'Alistamiento':
                        if((isFiltrador() || isCoordinadorBodega() || isAdministrador()) && row.order_picking == null) {
                            btn += `<a onclick="PendingOrderDispatch(${row.id})" type="button"
                            class="btn btn-info btn-sm mr-2" title="Regresar orden de despacho.">
                                <i class="fas fa-arrows-rotate text-white"></i>
                            </a>`;
                        }

                        if((isBodega() || isAdministrador()) && row.order_picking == null) {
                            btn += `<a onclick="PickingOrderDispatch(${row.id})" type="button"
                            class="btn btn-primary btn-sm mr-2" title="Alistar orden de despacho.">
                                <i class="fas fa-barcode-read text-white"></i>
                            </a>`;
                        }
                        break;
                    case 'Revision':
                        if(isCoordinadorBodega() || isAdministrador()) {
                            btn += `<a href="/Dashboard/Dispatches/Review/${row.id}" type="button"
                            class="btn btn-warning text-white btn-sm mr-2" title="Revisar alistamiento orden de despacho.">
                                <i class="fas fa-gear text-white"></i>
                            </a>`;
                        }
                        break;
                    case 'Empacado':
                        if((isBodega() || isAdministrador()) && row.order_packing == null) {
                            btn += `<a onclick="PackingOrderDispatch(${row.id})" type="button"
                            class="btn btn-secondary btn-sm mr-2" title="Empacar orden de despacho.">
                                <i class="fas fa-box-open-full text-white"></i>
                            </a>`;
                        }
                        break;
                    case 'Facturacion':
                        if(isFacturador() || isAdministrador()) {
                            btn += `<a onclick="InvoiceOrderDispatchModal(${row.id})" type="button"
                            class="btn bg-orange btn-sm mr-2" title="Facturar orden de despacho.">
                                <i class="fas fa-money-bill text-white"></i>
                            </a>`;
                        }
                        break;
                    case 'Despachado':
                        if((isCoordinadorBodega() || isFiltrador() || isFacturador() || isAdministrador()) && row.order_packing != null) {
                            btn += `<a href="/Dashboard/Dispatches/Download/${row.id}" type="button"
                            class="btn btn-sm mr-2" style="background: #343a40;" title="Descargar pdf de la orden de despacho." target="_blank">
                                <i class="fas fa-file-pdf text-white"></i>
                            </a>`;
                        }
                        break;
                    default:
                        btn += '';
                        break;
                }

                btn += `</div>`;
                return btn;
            }
        }
    ],
    columnDefs: [
        {
            orderable: true,
            targets: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12]
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

tableOrderDispatches.on('click', 'button.dt-expand', function (e) {
    let tr = e.target.closest('tr');
    let row = tableOrderDispatches.row(tr);

    let iconButton = $(this);

    if (row.child.isShown()) {
        row.child.hide();
        iconButton.html('<i class="fas fa-plus"></i>').removeClass('btn-danger').addClass('btn-success');
    } else {
        row.child(tableOrderDispatchDetails(row.data())).show();
        iconButton.html('<i class="fas fa-minus"></i>').removeClass('btn-success').addClass('btn-danger');
        $(`#orderDispatchDetails${row.data().id}`).DataTable({});
    }
});

function tableOrderDispatchDetails(row) {
    let columns  = '';
    let totalSum = 0;
    let quantitySum = 0;

    $.each(row.sizes, function(index, size) {
        columns += `<th>T${size.code}</th>`;
    });

    let table = `<table class="table table-bordered table-hover dataTable dtr-inline nowrap w-100" id="orderDispatchDetails${row.id}">
                    <thead>
                        <tr>
                            <th>ESTADO</th>
                            <th>PEDIDO</th>
                            <th>AMARRADOR</th>
                            <th>REFERENCIA</th>
                            <th>COLOR</th>
                            ${columns}
                            <th>TOTAL</th>
                            <th>OBSERVACION</th>
                            <th>DESPACHO</th>
                            <th>ESTADO PEDIDO</th>
                        </tr>
                    </thead>
                    <tbody>`;

    $.each(row.order_dispatch_details, function(index, order_dispatch_detail) {
        table += `<tr>`;
        switch (order_dispatch_detail.status) {
            case 'Pendiente':
                table += `<td><span class="badge badge-info"><i class="fas fa-arrows-rotate mr-2"></i>Pendiente</span></td>`;
                break;
            case 'Cancelado':
                table += `<td><span class="badge badge-danger"><i class="fas fa-xmark mr-2 text-white"></i>Cancelado</span></td>`;
                break;
            case 'Aprobado':
                table += `<td><span class="badge badge-success"><i class="fas fa-check mr-2"></i>Aprobado</span></td>`;
                break;
            case 'Alistamiento':
                table += `<td><span class="badge badge-warning text-white"><i class="fas fa-box-open-full mr-2"></i>Alistamiento</span></td>`;
                break;
            case 'Revision':
                table += `<td><span class="badge bg-dark text-white"><i class="fas fa-box-taped mr-2 text-black"></i>Revision</span></td>`;
                break;
            case 'Empacado':
                table += `<td><span class="badge badge-secondary"><i class="fas fa-reply mr-2"></i>Empacado</span></td>`;
                break;
            case 'Facturacion':
                table += `<td><span class="badge bg-orange text-white" style="color: #fff !important;"><i class="fas fa-money-bill mr-2 text-white"></i>Facturacion</span></td>`;
                break;
            case 'Despachado':
                table += `<td><span class="badge badge-primary"><i class="fas fa-share-all mr-2"></i>Despachado</span></td>`;
                break;
            default:
                table += `<td><span class="badge badge-info"><i class="fas fa-arrows-rotate mr-2"></i>Pendiente</span></td>`;
                break;
        };

        table += `<td>${order_dispatch_detail.order_id}</td>
            <td>${order_dispatch_detail.order_detail_id}</td>
            <td>${order_dispatch_detail.order_detail.product.code}</td>
            <td>${order_dispatch_detail.order_detail.color.name} - ${order_dispatch_detail.order_detail.color.code}</td>`;

            let quantities = 0;

            $.each(row.sizes, function(index, size) {
                table += `<td>${order_dispatch_detail[`T${size.code}`]}</td>`;
                quantities += order_dispatch_detail[`T${size.code}`];
            });

            table += `<td>${quantities}</td>
                <td>${order_dispatch_detail.order_detail.seller_observation == null ? '' : order_dispatch_detail.order_detail.seller_observation}</td>
                <td><span class="badge bg-dark">${order_dispatch_detail.order.dispatch_type}</span></td>`;

            switch (order_dispatch_detail.order_detail.status) {
                case 'Pendiente':
                    table += `<td><span class="badge badge-info"><i class="fas fa-arrows-rotate mr-2"></i>Pendiente</span></td>`;
                    break;
                case 'Cancelado':
                    table += `<td><span class="badge badge-danger text-white"><i class="fas fa-xmark mr-2 text-white"></i>Cancelado</span></td>`;
                    break;
                case 'Aprobado':
                    table += `<td><span class="badge badge-success"><i class="fas fa-check mr-2"></i>Aprobado</span></td>`;
                    break;
                case 'Autorizado':
                    table += `<td><span class="badge badge-success"><i class="fas fa-check mr-2"></i>Autorizado</span></td>`;
                    break;
                case 'Agotado':
                    table += `<td><span class="badge badge-warning" style="color:white !important;"><i class="fas fa-triangle-exclamation mr-2 text-white"></i>Agotado</span></td>`;
                    break;
                case 'Suspendido':
                    table += `<td><span class="badge badge-secondary text-white"><i class="fas fa-clock-rotate-left mr-2 text-white"></i>Suspendido</span></td>`;
                    break;
                case 'Comprometido':
                    table += `<td><span class="badge bg-purple" style="color:white !important;"><i class="fas fa-filter mr-2 text-white"></i>Comprometido</span></td>`;
                    break;
                case 'Despachado':
                    table += `<td><span class="badge badge-primary"><i class="fas fa-share mr-2 text-white"></i>Despachado</span></td>`;
                    break;
                default:
                    table += `<td><span class="badge badge-info"><i class="fas fa-arrows-rotate mr-2"></i>Pendiente</span></td>`;
                    break;
            };

        table += `</td>
            </tr>`;
    });

    table += `</tbody></table>`;


    return table;
}
