$('#IndexOrderPacking').trigger('click');
let packageTypes = [];

function IndexOrderPacking(id) {
    $.ajax({
        url: `/Dashboard/Packings/Index/Query`,
        type: 'POST',
        data: {
            '_token': $('meta[name="csrf-token"]').attr('content'),
            'id': id
        },
        success: function(response) {
            sizes = response.data.sizes;
            packageTypes = response.data.packageTypes;
            if(response.data.status) {
                IndexOrderPackingModalCleaned(response.data.orderPacking, response.data.sizes, response.data.status, {});
            } else {
                IndexOrderPackingModalCleaned({}, response.data.sizes, response.data.status, response.data.orderPackage);
            }
            IndexOrderPackingAjaxSuccess(response);
        },
        error: function(xhr, textStatus, errorThrown) {
            IndexOrderPackingAjaxError(xhr);
        }
    });
}

function IndexOrderPackingModalCleaned(orderPacking = {}, sizes, status, orderPackage = {}) {
    $('#TitleOrderPacking').text('');
    $('#ButtonsOrderPacking').empty();
    $('#OrderPackages').empty();
    $('#OrderPackingDetails').empty();

    if(status) {
        $('#TitleOrderPacking').text('DETALLES DE LA ORDEN DE EMPACADO');

        let li = `<li class="nav-item ml-auto">
            <a class="nav-link active" type="button" onclick="StoreOrderPacking(${orderPacking.id})" title="Agregar empaque a la orden de empacado.">
                <i class="fas fa-plus mr-2"></i> <b>AGREGAR EMPAQUE</b>
            </a>
        </li>`;
        
        $('#ButtonsOrderPacking').html(li);

        $.each(orderPacking.order_packages, function(i, order_package) {

            let quantitiesTotal = 0;
            let items = ``;

            $.each(order_package.order_packing_details, function(j, order_packing_detail) {

                let table = `<table width="100%" class="table table-striped table-bordered" cellpadding="0" cellspacing="0">
                <thead>
                    <tr>
                        <th>TALLA</th>
                        <th>CE</th>
                        <th>CT</th>
                    </tr>
                </thead>
                <tbody>`;

                let quantities = 0;

                $.each(sizes, function(j, size) {
                    quantities += order_packing_detail[`T${size.code}`];

                    if(order_packing_detail['T'+size.code] > 0) {
                        table += `<tr>
                            <td>T${size.code}</td>
                            <td>${order_packing_detail['T'+size.code]}</td>
                            <td>${order_packing_detail.order_dispatch_detail['T'+size.code]}</td>
                        </tr>`;
                    }
                });

                quantitiesTotal += quantities;

                table += `</tbody>
                    </table>`;

                items = `<div class="col-lg-12">
                    <div>
                        <button type="button" class="mb-2 btn w-100 collapsed btn-info" data-toggle="collapse" data-target="#collapseOrderPackage${order_package.id}Detail${order_packing_detail.id}" aria-expanded="false" aria-controls="#collapseOrderPackage${order_package.id}Detail${order_packing_detail.id}">
                            <b>
                                # ${j + 1} | <span class="badge badge-light">${order_packing_detail.order_dispatch_detail.order_detail.product.code.toUpperCase()}</span> | 
                                <span class="badge badge-light">${order_packing_detail.order_dispatch_detail.order_detail.color.name.toUpperCase()} - ${order_packing_detail.order_dispatch_detail.order_detail.color.code.toUpperCase()}</span> | 
                                <span class="badge badge-warning">${quantities} UNDS</span>
                            </b>
                        </button>
                        <div class="table-responsive collapse" id="collapseOrderPackage${order_package.id}Detail${order_packing_detail.id}">                
                            <div class="col-12 pt-2">
                                <div class="table-responsive">
                                    ${table}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>`;
            });            

            let item = `<div class="col-md-4 col-sm-12 col-12">
                <div>
                    <button type="button" class="mb-2 btn w-100 collapsed btn-dark" data-toggle="collapse" data-target="#collapseOrderPackage${order_package.id}" aria-expanded="false" aria-controls="#collapseOrderPackage${order_package.id}">
                        <b># ${i + 1} | <span class="badge badge-info">${order_package.package_type.name}</span> | <span class="badge badge-warning">${quantitiesTotal} UNDS</span> | <span class="badge badge-light">${order_package.weight}</span></b>
                    </button>
                    <div class="table-responsive collapse" id="collapseOrderPackage${order_package.id}">                
                        <div class="col-12 pt-2">
                            <div class="row">
                                <div class="col-lg-6">
                                    <a class="btn btn-success text-white w-100" onclick="OpenOrderPackage(${order_package.id})" title="Abrir empaque.">
                                        <i class="fas fa-box-open mr-2"></i> <b>ABRIR</b>
                                    </a>
                                </div>
                                <div class="col-lg-6">
                                    <a class="btn btn-danger text-white w-100" onclick="CloseOrderPackage(${order_package.id})" title="Cerrar empaque.">
                                        <i class="fas fa-box-taped mr-2"></i> <b>CERRAR</b>
                                    </a>
                                </div>
                            </div>
                            <div class="table-responsive pt-2">
                                ${items}
                            </div>
                        </div>
                    </div>
                </div>
            </div> `;
            
            $('#OrderPackages').append(item);
        });
    } else {
        $('#TitleOrderPacking').text(`DETALLES DEL EMPAQUE | ${orderPackage.id} - ${orderPackage.package_type.name}`);

        let li = `<li class="nav-item ml-auto">
            <a class="btn btn-danger text-white w-100" onclick="CloseOrderPackage(${orderPackage.id})" title="Cerrar paquete.">
                <i class="fas fa-box-taped mr-2"></i> <b>CERRAR</b>
            </a>
        </li>`;
        
        $('#ButtonsOrderPacking').html(li);

        $.each(orderPackage.order_packing_details, function(i, order_packing_detail) {
                    
            let identify = `${order_packing_detail.order_dispatch_detail.order_detail.product.code}-${order_packing_detail.order_dispatch_detail.order_detail.color.code}`.toUpperCase();
            
            let table = `<table width="100%" class="table table-striped table-bordered" cellpadding="0" cellspacing="0">
                <thead>
                    <tr>
                        <th>TALLA</th>
                        <th>CA</th>
                        <th>CT</th>
                        <th>ACCION</th>
                    </tr>
                </thead>
                <tbody>`;
                $.each(sizes, function(j, size) {

                    if(order_packing_detail.order_dispatch_detail[`T${size.code}`] > 0) {
                        table += `<tr>
                            <td id="${order_packing_detail.id}-${identify}-T${size.code}">T${size.code}</td>
                            <td id="${order_packing_detail.id}-${identify}-${size.code}-CE">${order_packing_detail['T'+size.code]}</td>
                            <td id="${order_packing_detail.id}-${identify}-${size.code}-CT">${order_packing_detail.order_dispatch_detail['T'+size.code] - order_packing_detail.order_dispatch_detail.order_packings_details.reduce((acc, current) => current.id !== order_packing_detail.id ? acc + current['T'+size.code] : acc, 0)}</td>
                            <td class="text-center">
                                <a class="btn btn-info text-white btn-xs" onclick="AddOrderPackingDetailModal(${order_packing_detail.id}, '${order_packing_detail.order_dispatch_detail.order_detail.product.code.toUpperCase()}', '${size.code}', '${order_packing_detail.order_dispatch_detail.order_detail.color.code.toUpperCase()}', '${order_packing_detail.order_dispatch_detail.order_detail.color.name.toUpperCase()}')" title="Cerrar paquete.">
                                    <i class="fas fa-pen"></i>
                                </a>
                            </td>
                        </tr>`;
                    }
                });
                table += `</tbody>
                    </table>`;

            let item = `<div class="col-md-6 col-sm-12 col-12">
                <div>
                    <button type="button" class="mb-2 btn w-100 collapsed btn-primary" data-toggle="collapse" data-target="#collapsePackingDetail${i}" aria-expanded="false" aria-controls="#collapsePackingDetail${i}">
                        <b>
                            <div class="table-responsive">
                                <span class="badge badge-light">${order_packing_detail.order_dispatch_detail.order_detail.product.code.toUpperCase()} </span> | 
                                <span class="badge badge-light"> ${order_packing_detail.order_dispatch_detail.order_detail.color.name.toUpperCase()} - ${order_packing_detail.order_dispatch_detail.order_detail.color.code.toUpperCase()}</span>
                            </div>
                        </b>
                    </button>
                    <div class="table-responsive collapse" id="collapsePackingDetail${i}">
                        <div class="col-12 pt-2">
                            <div class="table-responsive">
                                ${table}
                            </div>
                        </div>
                    </div>
                </div>
            </div>`;

            $('#OrderPackingDetails').append(item);
        });
    }
}

function IndexOrderPackingAjaxSuccess(response) {
    if(response.status === 200) {
        toastr.success(response.message);
    }
}

function IndexOrderPackingAjaxError(xhr) {
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
