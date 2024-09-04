$('#IndexOrderDetail').trigger('click');

function IndexOrderDetail(order_id) {

    $.ajax({
        url: `/Dashboard/Orders/Details/Index/Query`,
        type: 'POST',
        data: {
            '_token': $('meta[name="csrf-token"]').attr('content'),
            'order_id': order_id
        },
        success: function(response) {
            IndexOrderDetailModalCleaned(response.data.order, response.data.sizes);
            IndexOrderDetailAjaxSuccess(response);
        },
        error: function(xhr, textStatus, errorThrown) {
            IndexOrderDetailAjaxError(xhr);
        }
    });
}

function IndexOrderDetailModalCleaned(order, sizes) {
    if ($.fn.DataTable.isDataTable('#details')) {
        $('#details').DataTable().destroy();
        $('#details thead').empty();
        $('#details tbody').empty();
        $('#details tfoot').empty();
    }

    $('#OrderDetailHead').html('');
    $('#OrderDetailBody').html('');

    let columns = '';

    $.each(sizes, function(index, size) {
        columns += `<th>T${size.code}</th>`;
    });

    let head = `<tr>
                    <th>#</th>
                    <th>ACCION</th>
                    <th>PRIORIDAD</th>
                    <th>PRECIO TOTAL</th>
                    <th>REFERENCIA</th>
                    <th>COLOR</th>
                    ${columns}
                    <th>TOTAL</th>
                    <th>OBSERVACION</th>
                    <th>ESTADO</th>
                    <th>FECHA</th>
                </tr>`;

    let foot = `<tr>
                    <th>#</th>
                    <th>ACCION</th>
                    <th>PRIORIDAD</th>`;

    let totalSum = 0;
    let quantitySum = 0;

    let body = '';

    $.each(order.order_details, function(index, order_detail) {
        body += `<tr>
            <td>
                <div class="icheck-primary"><input type="checkbox" id="${order_detail.id}" name="${order_detail.id}" class="details"><label for="${order_detail.id}"></label></div>
            </td>`;
        let btn = '';

        if ((isAdministrador() || isVendedor() || isVendedorEspecial() || isCartera() || isFiltrador()) && order.seller_user_id == $('meta[name="user-id"]').attr('content') && order.seller_status == 'Pendiente') {
            switch (order_detail.status) {
                case 'Pendiente':
                    btn += `<a onclick="CancelOrderDetail(${order_detail.id})" type="button"
                    class="btn btn-danger btn-sm mr-2 btn-order" title="Cancelar detalle de pedido.">
                        <i class="fas fa-xmark text-white"></i>
                    </a>`;

                    btn += `<a onclick="EditOrderDetailModal(${order_detail.id})" type="button"
                    class="btn btn-primary btn-sm mr-2 btn-order" title="Editar detalle de pedido.">
                        <i class="fas fa-pen text-white"></i>
                    </a>`;

                    btn += `<a onclick="CloneOrderDetailModal(${order_detail.id})" type="button"
                    class="btn bg-orange btn-sm mr-2 btn-order" title="Clonar detalle de pedido.">
                        <i class="fas fa-arrow-right-arrow-left text-white"></i>
                    </a>`;
                    break;
                case 'Cancelado':
                    btn += `<a onclick="PendingOrderDetail(${order_detail.id})" type="button"
                    class="btn btn-info btn-sm mr-2 btn-order" title="Pendiente detalle de pedido.">
                        <i class="fas fa-arrows-rotate text-white"></i>
                    </a>`;
                    break;
                default:
                    btn += ``;
                    break;
            };
        } else if ((isAdministrador() || isCartera()) && ['Pendiente', 'Parcialmente Aprobado', 'Aprobado', 'Autorizado', 'Suspendido', 'En mora'].includes(order.wallet_status) && order.dispatch_status != 'Despachado') {
            switch (order_detail.status) {
                case 'Agotado':
                    if(['Aprobado', 'Autorizado', 'Parcialmente Aprobado'].includes(order.wallet_status)) {
                        btn += `<a onclick="AllowOrderDetail(${order_detail.id})" type="button"
                        class="btn btn-warning btn-sm mr-2 btn-order" title="Permitir detalle de pedido.">
                            <i class="fas fa-key-skeleton text-white"></i>
                        </a>`;
                    }

                    btn += `<a onclick="EditOrderDetailModal(${order_detail.id})" type="button"
                    class="btn btn-primary btn-sm mr-2 btn-order" title="Editar detalle de pedido.">
                        <i class="fas fa-pen text-white"></i>
                    </a>`;
                    break;
                case 'Pendiente':
                    btn += `<a onclick="CancelOrderDetail(${order_detail.id})" type="button"
                    class="btn btn-danger btn-sm mr-2 btn-order" title="Rechazar detalle de pedido.">
                        <i class="fas fa-xmark text-white"></i>
                    </a>`;

                    if(['Parcialmente Aprobado', 'Aprobado', 'Autorizado'].includes(order.wallet_status)) {
                        if(['Autorizado'].includes(order.wallet_status)) {
                            btn += `<a onclick="AuthorizeOrderDetail(${order_detail.id})" type="button"
                            class="btn btn-success btn-sm mr-2 btn-order" title="Autorizar detalle de pedido.">
                                <i class="fas fa-check text-white"></i>
                            </a>`;
                        } else if(['Aprobado', 'Parcialmente Aprobado'].includes(order.wallet_status)) {
                            btn += `<a onclick="ApproveOrderDetail(${order_detail.id})" type="button"
                            class="btn btn-success btn-sm mr-2 btn-order" title="Aprobar detalle de pedido.">
                                <i class="fas fa-check text-white"></i>
                            </a>`;
                        }
                    }

                    btn += `<a onclick="EditOrderDetailModal(${order_detail.id})" type="button"
                    class="btn btn-primary btn-sm mr-2 btn-order" title="Editar detalle de pedido.">
                        <i class="fas fa-pen text-white"></i>
                    </a>`;

                    btn += `<a onclick="SuspendOrderDetail(${order_detail.id})" type="button"
                    class="btn btn-secondary btn-sm mr-2 btn-order" title="Suspender detalle de pedido.">
                        <i class="fas fa-solid fa-clock-rotate-left text-white"></i>
                    </a>`;
                    break;
                case 'Cancelado':
                    if(['Parcialmente Aprobado', 'Aprobado', 'Autorizado'].includes(order.wallet_status)) {
                        if(['Autorizado'].includes(order.wallet_status)) {
                            btn += `<a onclick="AuthorizeOrderDetail(${order_detail.id})" type="button"
                            class="btn btn-success btn-sm mr-2 btn-order" title="Autorizar detalle de pedido.">
                                <i class="fas fa-check text-white"></i>
                            </a>`;
                        } else if(['Aprobado', 'Parcialmente Aprobado'].includes(order.wallet_status)) {
                            btn += `<a onclick="ApproveOrderDetail(${order_detail.id})" type="button"
                            class="btn btn-success btn-sm mr-2 btn-order" title="Aprobar detalle de pedido.">
                                <i class="fas fa-check text-white"></i>
                            </a>`;
                        }
                    }
                    break;
                case 'Suspendido':
                    btn += `<a onclick="CancelOrderDetail(${order_detail.id})" type="button"
                    class="btn btn-danger btn-sm mr-2 btn-order" title="Rechazar detalle de pedido.">
                        <i class="fas fa-xmark text-white"></i>
                    </a>`;

                    if(['Aprobado', 'Parcialmente Aprobado'].includes(order.wallet_status)) {
                        btn += `<a onclick="ApproveOrderDetail(${order_detail.id})" type="button"
                        class="btn btn-success btn-sm mr-2 btn-order" title="Aprobar detalle de pedido.">
                            <i class="fas fa-check text-white"></i>
                        </a>`;
                    }

                    btn += `<a onclick="EditOrderDetailModal(${order_detail.id})" type="button"
                    class="btn btn-primary btn-sm mr-2 btn-order" title="Editar detalle de pedido.">
                        <i class="fas fa-pen text-white"></i>
                    </a>`;
                    break;
                case 'Aprobado':
                    btn += `<a onclick="CancelOrderDetail(${order_detail.id})" type="button"
                    class="btn btn-danger btn-sm mr-2 btn-order" title="Rechazar detalle de pedido.">
                        <i class="fas fa-xmark text-white"></i>
                    </a>`;

                    btn += `<a onclick="SuspendOrderDetail(${order_detail.id})" type="button"
                    class="btn btn-secondary btn-sm mr-2 btn-order" title="Suspender detalle de pedido.">
                        <i class="fas fa-solid fa-clock-rotate-left text-white"></i>
                    </a>`;
                    break;
                case 'Autorizado':
                    btn += `<a onclick="CancelOrderDetail(${order_detail.id})" type="button"
                    class="btn btn-danger btn-sm mr-2 btn-order" title="Rechazar detalle de pedido.">
                        <i class="fas fa-xmark text-white"></i>
                    </a>`;

                    btn += `<a onclick="SuspendOrderDetail(${order_detail.id})" type="button"
                    class="btn btn-secondary btn-sm mr-2 btn-order" title="Suspender detalle de pedido.">
                        <i class="fas fa-solid fa-clock-rotate-left text-white"></i>
                    </a>`;
                    break;
                default:
                    btn += ``;
                    break;
            };
        }

        body += `<td><div class="text-center">${btn}</div></td>`;

        switch (order_detail.priority) {
            case '1':
                body += `<th><span class="badge bg-danger">⚠️ Crítica</span></th>`;
                break;
            case '2':
                body += `<th><span class="badge bg-warning">🔥 Alta</span></th>`;
                break;
            case '3':
                body += `<th><span class="badge bg-primary">⏳ Media</span></th>`;
                break;
            case '4':
                body += `<th><span class="badge bg-info">🛒 Baja</span></th>`;
                break;
            case '5':
                body += `<th><span class="badge bg-secondary">💤 Mínima</span></th>`;
                break;
            default:
                body += `<th></th>`;
                break;
        };

        let quantities = 0;

        $.each(sizes, function(index, size) {
            quantities += order_detail[`T${size.code}`];
        });

        totalSum += quantities * order_detail.negotiated_price;
        quantitySum += quantities;

        body += `<td style="background: #FF5733; color: #fff; font-weight: bold;">${(quantities * order_detail.negotiated_price).toLocaleString('es-CO', { style: 'currency', currency: 'COP', minimumFractionDigits: 0, maximumFractionDigits: 0 })} COP</td>
                <td>${order_detail.product.code}</td>
                <td>${order_detail.color.name + ' - ' + order_detail.color.code}</td>`;

        $.each(sizes, function(index, size) {
            body += `<td>${order_detail[`T${size.code}`]}</td>`;
        });

        body += `<td>${quantities}</td>
            <td>${order_detail.seller_observation ?? ''}</td>`;

        switch (order_detail.status) {
            case 'Pendiente':
                body += `<td><span class="badge badge-info"><i class="fas fa-arrows-rotate mr-2"></i>Pendiente</span></td>`;
                break;
            case 'Cancelado':
                body += `<td><span class="badge badge-danger text-white"><i class="fas fa-xmark mr-2 text-white"></i>Cancelado</span></td>`;
                break;
            case 'Aprobado':
                body += `<td><span class="badge badge-success"><i class="fas fa-check mr-2"></i>Aprobado</span></td>`;
                break;
            case 'Autorizado':
                body += `<td><span class="badge badge-success"><i class="fas fa-check mr-2"></i>Autorizado</span></td>`;
                break;
            case 'Agotado':
                body += `<td><span class="badge badge-warning" style="color:white !important;"><i class="fas fa-triangle-exclamation mr-2 text-white"></i>Agotado</span></td>`;
                break;
            case 'Suspendido':
                body += `<td><span class="badge badge-secondary text-white"><i class="fas fa-solid fa-clock-rotate-left mr-2 text-white"></i>Suspendido</span></td>`;
                break;
            case 'Comprometido':
                body += `<td><span class="badge bg-purple" style="color:white !important;"><i class="fas fa-filter mr-2 text-white"></i>Comprometido</span></td>`;
                break;
            case 'Despachado':
                body += `<td><span class="badge badge-primary"><i class="fas fa-share mr-2 text-white"></i>Despachado</span></td>`;
                break;
            default:
                body += `<td><span class="badge badge-info"><i class="fas fa-arrows-rotate mr-2"></i>Pendiente</span></td>`;
                break;
        };

        body += `<td><span class="badge badge-dark">${(order_detail.dispatch_date ?? order_detail.wallet_date) ?? order_detail.seller_date}</span></td>
        </tr>`;
    });

    foot += `<th>${totalSum.toLocaleString('es-CO', { style: 'currency', currency: 'COP', minimumFractionDigits: 0, maximumFractionDigits: 0 })} COP</th>
        <th>REFERENCIA</th>
        <th>COLOR</th>`;

    $.each(sizes, function(index, size) {
        let sizeSum = 0;
        $.each(order.order_details, function(i, order_detail) {
            sizeSum += order_detail[`T${size.code}`];
        });
        foot += `<th>${sizeSum}</th>`;
    });

    foot += `<th>${quantitySum}</th>
        <th>OBSERVACION</th>
        <th>ESTADO</th>
        <th>FECHA</th>
        </tr>`;

    $('#OrderDetailHead').html(head);
    $('#OrderDetailBody').html(body);
    $('#OrderDetailFoot').html(foot);
    $('#details').DataTable({
        "paging": false,
        "info": false,
        "lengthChange": false,
        "searching": true,
        "pageLength": -1
    });
}

function IndexOrderDetailAjaxSuccess(response) {
    if(response.status === 204) {
        toastr.info(response.message);
    }
}

function IndexOrderDetailAjaxError(xhr) {
    if(xhr.status === 403) {
        toastr.error(xhr.responseJSON.error ? xhr.responseJSON.error.message : xhr.responseJSON.message);
    }

    if(xhr.status === 404) {
        toastr.error(xhr.responseJSON.error ? xhr.responseJSON.error.message : xhr.responseJSON.message);
    }

    if(xhr.status === 419) {
        toastr.error(xhr.responseJSON.error ? xhr.responseJSON.error.message : xhr.responseJSON.message);
    }

    if(xhr.status === 422){
        $.each(xhr.responseJSON.errors, function(field, messages) {
            $.each(messages, function(index, message) {
                toastr.error(message);
            });
        });
    }

    if(xhr.status === 500){
        toastr.error(xhr.responseJSON.error ? xhr.responseJSON.error.message : xhr.responseJSON.message);
    }
}
