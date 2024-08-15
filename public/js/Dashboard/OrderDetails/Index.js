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
    $('#OrderDetailHead').html('');
    $('#OrderDetailBody').html('');

    let columns = '';

    $.each(sizes, function(index, size) {
        columns += `<th>T${size.code}</th>`;
    });

    let head = `<tr>
                    <th>ACCION</th>
                    <th>PRECIO TOTAL</th>
                    <th>REFERENCIA</th>
                    <th>COLOR</th>
                    ${columns}
                    <th>TOTAL</th>
                    <th>OBSERVACION</th>
                    <th>ESTADO</th>
                </tr>`;

    let foot = `<tr>
                    <th>ACCION</th>`;

    let totalSum = 0;
    let quantitySum = 0;

    let body = '';

    $.each(order.order_details, function(index, order_detail) {
        body += `<tr>`;
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
        } else if ((isAdministrador() || isCartera()) && ['Pendiente', 'Parcialmente Aprobado', 'Aprobado', 'Autorizado'].includes(order.wallet_status) && order.dispatch_status != 'Despachado') {
            switch (order_detail.status) {
                case 'Agotado':
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

                    if(order.seller_user.title == 'VENDEDOR ESPECIAL') {
                        btn += `<a onclick="AuthorizeOrderDetail(${order_detail.id})" type="button"
                        class="btn btn-success btn-sm mr-2 btn-order" title="Autorizar detalle de pedido.">
                            <i class="fas fa-check text-white"></i>
                        </a>`;
                    } else {
                        btn += `<a onclick="ApproveOrderDetail(${order_detail.id})" type="button"
                        class="btn btn-success btn-sm mr-2 btn-order" title="Aprobar detalle de pedido.">
                            <i class="fas fa-check text-white"></i>
                        </a>`;
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
                    btn += `<a onclick="ApproveOrderDetail(${order_detail.id})" type="button"
                    class="btn btn-success btn-sm mr-2 btn-order" title="Aprobar detalle de pedido.">
                        <i class="fas fa-check text-white"></i>
                    </a>`;
                    break;
                case 'Suspendido':
                    btn += `<a onclick="CancelOrderDetail(${order_detail.id})" type="button"
                    class="btn btn-danger btn-sm mr-2 btn-order" title="Rechazar detalle de pedido.">
                        <i class="fas fa-xmark text-white"></i>
                    </a>`;
                    
                    btn += `<a onclick="ApproveOrderDetail(${order_detail.id})" type="button"
                    class="btn btn-success btn-sm mr-2 btn-order" title="Aprobar detalle de pedido.">
                        <i class="fas fa-check text-white"></i>
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

        let quantities = 0;

        $.each(sizes, function(index, size) {
            quantities += order_detail[`T${size.code}`];
        });

        totalSum += quantities * order_detail.negotiated_price;
        quantitySum += quantities;

        body += `<td>${(quantities * order_detail.negotiated_price).toLocaleString('es-CO', { style: 'currency', currency: 'COP', minimumFractionDigits: 0, maximumFractionDigits: 0 })} COP</td>
                <td>${order_detail.product.code}</td>
                <td>${order_detail.color.name + ' - ' + order_detail.color.code}</td>`;

        $.each(sizes, function(index, size) {
            body += `<td>${order_detail[`T${size.code}`]}</td>`;
        });

        body += `<td>${quantities}</td>
            <td>${order_detail.seller_observation ?? ''}</td>`;

        switch (order_detail.status) {
            case 'Pendiente':
                body += `<td><span class="badge badge-pill badge-info"><i class="fas fa-arrows-rotate mr-2"></i>Pendiente</span></td>`;
                break;
            case 'Cancelado':
                body += `<td><span class="badge badge-pill badge-danger text-white"><i class="fas fa-xmark mr-2 text-white"></i>Cancelado</span></td>`;
                break;
            case 'Aprobado':
                body += `<td><span class="badge badge-pill badge-success"><i class="fas fa-check mr-2"></i>Aprobado</span></td>`;
                break;
            case 'Autorizado':
                body += `<td><span class="badge badge-pill badge-success"><i class="fas fa-check mr-2"></i>Autorizado</span></td>`;
                break;
            case 'Agotado':
                body += `<td><span class="badge badge-pill badge-warning" style="color:white !important;"><i class="fas fa-triangle-exclamation mr-2 text-white"></i>Agotado</span></td>`;
                break;
            case 'Suspendido':
                body += `<td><span class="badge badge-pill badge-secondary text-white"><i class="fas fa-solid fa-clock-rotate-left mr-2 text-white"></i>Suspendido</span></td>`;
                break;
            case 'Comprometido':
                body += `<td><span class="badge badge-pill bg-purple" style="color:white !important;"><i class="fas fa-filter mr-2 text-white"></i>Comprometido</span></td>`;
                break;
            case 'Despachado':
                body += `<td><span class="badge badge-pill badge-primary"><i class="fas fa-share mr-2 text-white"></i>Despachado</span></td>`;
                break;
            default:
                body += `<td><span class="badge badge-pill badge-info"><i class="fas fa-arrows-rotate mr-2"></i>Pendiente</span></td>`;
                break;
        };

        body += `</tr>`;
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
        </tr>`;

    $('#OrderDetailHead').html(head);
    $('#OrderDetailBody').html(body);
    $('#OrderDetailFoot').html(foot);
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
