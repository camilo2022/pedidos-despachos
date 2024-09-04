$('#IndexOrderDispatchDetail').trigger('click');

function IndexOrderDispatchDetail(order_dispatch_id) {
    $.ajax({
        url: `/Dashboard/Dispatches/Details/Index/Query`,
        type: 'POST',
        data: {
            '_token': $('meta[name="csrf-token"]').attr('content'),
            'order_dispatch_id': order_dispatch_id
        },
        success: function(response) {
            IndexOrderDispatchDetailModalCleaned(response.data.orderDispatch, response.data.sizes);
            IndexOrderDispatchDetailAjaxSuccess(response);
        },
        error: function(xhr, textStatus, errorThrown) {
            IndexOrderDispatchDetailAjaxError(xhr);
        }
    });
}

function IndexOrderDispatchDetailModalCleaned(orderDispatch, sizes) {
    if ($.fn.DataTable.isDataTable('#details')) {
        $('#details').DataTable().destroy();
        $('#details thead').empty();
        $('#details tbody').empty();
        $('#details tfoot').empty();
    }

    $('#OrderDispatchDetailHead').html('');
    $('#OrderDispatchDetailBody').html('');

    let columns = '';

    $.each(sizes, function(index, size) {
        columns += `<th>T${size.code}</th>`;
    });

    let head = `<tr>
                    <th>#</th>
                    <th>ACCION</th>
                    <th>ESTADO</th>
                    <th>FECHA FILTRO</th>
                    <th>PEDIDO</th>
                    <th>AMARRADOR</th>
                    <th>REFERENCIA</th>
                    <th>COLOR</th>
                    ${columns}
                    <th>TOTAL</th>
                    <th>OBSERVACION</th>
                    <th>ESTADO CARTERA</th>
                    <th>TIPO DESPACHO</th>
                    <th>OFC - DCO</th>
                    <th>VENDEDOR</th>
                </tr>`;

    let foot = `<tr>
        <th>#</th>
        <th>ACCION</th>
        <th>ESTADO</th>
        <th>FECHA FILTRO</th>
        <th>PEDIDO</th>
        <th>AMARRADOR</th>
        <th>REFERENCIA</th>
        <th>COLOR</th>`;

    let quantitySum = 0;

    let body = '';

    $.each(orderDispatch.order_dispatch_details, function(index, order_dispatch_detail) {
        body += `<tr>
            <td>
                <div class="icheck-primary"><input type="checkbox" id="${order_dispatch_detail.id}" name="${order_dispatch_detail.id}" class="details"><label for="${order_dispatch_detail.id}"></label></div>
            </td>`;
        let btn = '';

        switch (order_dispatch_detail.status) {
            case 'Pendiente':
                if(['Pendiente'].includes(orderDispatch.dispatch_status)) {
                    btn += `<a onclick="CancelOrderDispatchDetail(${order_dispatch_detail.id})" type="button"
                    class="btn btn-danger btn-sm mr-2" title="Cancelar detalle de la orden de despacho.">
                        <i class="fas fa-xmark text-white"></i>
                    </a>`;
                }
                break;
            case 'Cancelado':
                if(['Pendiente'].includes(orderDispatch.dispatch_status) && ['Aprobado'].includes(order_dispatch_detail.order_detail.status)) {
                    btn += `<a onclick="PendingOrderDispatchDetail(${order_dispatch_detail.id})" type="button"
                    class="btn btn-info btn-sm mr-2" title="Habilitar detalle de la orden de despacho.">
                        <i class="fas fa-arrows-rotate text-white"></i>
                    </a>`;
                }
                break;
            default:
                btn += ``;
                break;
        };

        body += `<td><div class="text-center">${btn}</div></td>`;

        switch (order_dispatch_detail.status) {
            case 'Pendiente':
                body += `<td><span class="badge badge-info"><i class="fas fa-arrows-rotate mr-2"></i>Pendiente</span></td>`;
                break;
            case 'Cancelado':
                body += `<td><span class="badge badge-danger"><i class="fas fa-xmark mr-2 text-white"></i>Cancelado</span></td>`;
                break;
            case 'Alistamiento':
                body += `<td><span class="badge badge-primary text-white"><i class="fas fa-barcode-read mr-2"></i>Alistamiento</span></td>`;
                break;
            case 'Revision':
                body += `<td><span class="badge badge-warning" style="color:white !important;"><i class="fas fa-gear mr-2 text-white"></i>Revision</span></td>`;
                break;
            case 'Empacado':
                body += `<td><span class="badge badge-secondary"><i class="fas fa-box-open-full mr-2"></i>Empacado</span></td>`;
                break;
            case 'Facturacion':
                body += `<td><span class="badge bg-orange text-white" style="color: white !important;"><i class="fas fa-money-bill mr-2 text-white"></i>Facturacion</span></td>`;
                break;
            case 'Despachado':
                body += `<td><span class="badge badge-success"><i class="fas fa-share-all mr-2"></i>Despachado</span></td>`;
                break;
            default:
                body += `<td><span class="badge badge-info"><i class="fas fa-arrows-rotate mr-2"></i>Pendiente</span></td>`;
                break;
        };

        body += `<td><span class="badge badge-dark">${order_dispatch_detail.date}</span></td>
            <td>${order_dispatch_detail.order_id}</td>
            <td>${order_dispatch_detail.order_detail_id}</td>
            <td>${order_dispatch_detail.order_detail.product.code}</td>
            <td>${order_dispatch_detail.order_detail.color.name + ' - ' + order_dispatch_detail.order_detail.color.code}</td>`;

        let quantities = 0;

        $.each(sizes, function(index, size) {
            body += `<td>${order_dispatch_detail[`T${size.code}`]}</td>`;
            quantities += order_dispatch_detail[`T${size.code}`];
        });

        quantitySum += quantities;

        body += `<td>${quantities}</td>
            <td>${order_dispatch_detail.order_detail.seller_observation ?? ''}</td>`;

        switch (order_dispatch_detail.order_detail.status) {
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
                body += `<td><span class="badge badge-secondary text-white"><i class="fas fa-clock-rotate-left mr-2 text-white"></i>Suspendido</span></td>`;
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

        body += `<td><span class="badge badge-dark">${order_dispatch_detail.order.dispatch_type}</span></td>
            <td>${order_dispatch_detail.order.wallet_dispatch_official ?? order_dispatch_detail.order.seller_dispatch_official} % - ${order_dispatch_detail.order.wallet_dispatch_document ?? order_dispatch_detail.order.seller_dispatch_document} %</td>
            <td>${order_dispatch_detail.order_detail.seller_user.name + ' ' + order_dispatch_detail.order_detail.seller_user.last_name}</td>
            </tr>`;
    });

    foot += ``;

    $.each(sizes, function(index, size) {
        let sizeSum = 0;
        $.each(orderDispatch.order_dispatch_details, function(i, order_dispatch_detail) {
            sizeSum += order_dispatch_detail[`T${size.code}`];
        });
        foot += `<th>${sizeSum}</th>`;
    });

    foot += `<th>${quantitySum}</th>
        <th>OBSERVACION</th>
        <th>ESTADO CARTERA</th>
        <th>TIPO DESPACHO</th>
        <th>OFC - DCO</th>
        <th>VENDEDOR</th>
        </tr>`;

    $('#OrderDispatchDetailHead').html(head);
    $('#OrderDispatchDetailBody').html(body);
    $('#OrderDispatchDetailFoot').html(foot);
    $('#details').DataTable({
        "paging": false,
        "info": false,
        "lengthChange": false,
        "searching": true,
        "pageLength": -1
    });
}

function IndexOrderDispatchDetailAjaxSuccess(response) {
    if(response.status === 204) {
        toastr.info(response.message);
    }
}

function IndexOrderDispatchDetailAjaxError(xhr) {
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
