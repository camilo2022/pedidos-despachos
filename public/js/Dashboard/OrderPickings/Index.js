$('#IndexOrderPicking').trigger('click');

function IndexOrderPicking(id) {
    $.ajax({
        url: `/Dashboard/Pickings/Index/Query`,
        type: 'POST',
        data: {
            '_token': $('meta[name="csrf-token"]').attr('content'),
            'id': id
        },
        success: function(response) {
            sizes = response.data.sizes;
            IndexOrderPickingModalCleaned(response.data.orderPicking, response.data.sizes);
            IndexOrderPickingAjaxSuccess(response);
        },
        error: function(xhr, textStatus, errorThrown) {
            IndexOrderPickingAjaxError(xhr);
        }
    });
}

function IndexOrderPickingModalCleaned(orderPicking, sizes) {
    $('#OrderPickingDetails').empty();

    $.each(orderPicking.order_picking_details, function(index, order_picking_detail) {
        let missing = 0;
        let total = 0;
                
        let identify = `${order_picking_detail.id}-${order_picking_detail.order_dispatch_detail.order_detail.product.code}-${order_picking_detail.order_dispatch_detail.order_detail.color.code}`.toUpperCase();
        
        let table = `<table width="100%" class="table table-striped table-bordered" cellpadding="0" cellspacing="0">
            <thead>
                <tr>
                    <th>TALLA</th>
                    <th>CA</th>
                    <th>CT</th>
                </tr>
            </thead>
            <tbody>`;
            $.each(sizes, function(j, size) {
                missing += order_picking_detail['T'+size.code];
                total += order_picking_detail.order_dispatch_detail['T'+size.code];

                if(order_picking_detail.order_dispatch_detail['T'+size.code] > 0) {
                    table += `<tr>
                        <td id="${identify}-T${size.code}">T${size.code}</td>
                        <td id="${identify}-${size.code}-CA">${order_picking_detail['T'+size.code]}</td>
                        <td id="${identify}-${size.code}-CT">${order_picking_detail.order_dispatch_detail['T'+size.code]}</td>
                    </tr>`;
                }
            });
            table += `</tbody>
                </table>`;

        let item = `<div class="col-md-4 col-sm-12 col-12">
            <div>
                <button type="button" class="mb-2 btn w-100 collapsed btn-dark" data-toggle="collapse" data-target="#collapsePackage${index}" aria-expanded="false" aria-controls="#collapsePackage${index}">
                    <b>
                        <div class="table-responsive">
                            <span class="badge badge-info">${order_picking_detail.order_dispatch_detail.order_detail.product.code.toUpperCase()} | ${order_picking_detail.order_dispatch_detail.order_detail.color.name.toUpperCase()} - ${order_picking_detail.order_dispatch_detail.order_detail.color.code.toUpperCase()}</span> | 
                            <span class="badge badge-light" id="${identify}-quantity-missing">${missing}</span> de <span class="badge badge-warning" id="${identify}-quantity-total">${total}</span> | 
                            <span class="badge badge-${missing == total ? 'success' : 'danger'}" id="${identify}-badge">${missing == total ? 'Completado' : 'Hace falta'}</span>
                        </div>
                    </b>
                </button>
                <div class="table-responsive collapse" id="collapsePackage${index}">                
                    <input type="text" class="form-control" id="${identify}" data-product="${order_picking_detail.order_dispatch_detail.order_detail.product.code.toUpperCase()}" data-color="${order_picking_detail.order_dispatch_detail.order_detail.color.code}" data-id="${order_picking_detail.id}" onkeyup="AddOrderPickingDetail(this, event)">
                    <div class="col-12 pt-2">
                        <div class="table-responsive">
                            ${table}
                        </div>
                    </div>
                </div>
            </div>
        </div>`;
        $('#OrderPickingDetails').append(item);
    });
}

function IndexOrderPickingAjaxSuccess(response) {
    if(response.status === 200) {
        toastr.success(response.message);
    }
}

function IndexOrderPickingAjaxError(xhr) {
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
