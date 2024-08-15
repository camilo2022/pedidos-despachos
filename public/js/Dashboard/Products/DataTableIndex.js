let tableProducts = $('#products').DataTable({
    processing: true,
    serverSide: true,
    ajax: {
        url: `/Dashboard/Products/Index/Query`,
        type: 'POST',
        data: function (request) {
            var columnMappings = {
                0: 'id',
                1: 'id',
                2: 'code',
                3: 'category',
                4: 'trademark',
                5: 'price',
                6: 'description',
                7: 'id',
                8: 'id',
                9: 'id',
                10: 'deleted_at',
                11: 'id'
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
            return response.data.products;
        },
        error: function (xhr, error, thrown) {
            toastr.error(xhr.responseJSON.error ? xhr.responseJSON.error.message : xhr.responseJSON.message);
        }
    },
    columns: [
        {
            data: 'inventories',
            render: function (data, type, row) {
                let btn = '';
                if(data.length > 0) {
                    btn += '<button class="btn btn-sm btn-success dt-expand rounded-circle"><i class="fas fa-plus"></i</button>';
                }
                return btn;
            },
        },
        { data: 'id' },
        { data: 'code' },
        { data: 'category' },
        { data: 'trademark' },
        {
            data: 'price',
            render: function (data, type, row) {
                return data.toLocaleString('es-CO', { style: 'currency', currency: 'COP' });
            },
        },
        { data: 'description' },
        {
            data: 'warehouses',
            render: function(data, type, row) {
                let div = `<div>`;
                $.each(data, function(index, warehouse) {
                    div += `<span class="badge badge-info mr-1">${warehouse.code}</span>`;
                });
                div += `</div>`;

                return div;
            }
        },
        {
            data: 'sizes',
            render: function(data, type, row) {
                let div = `<div>`;
                $.each(data, function(index, size) {
                    div += `<span class="badge badge-info mr-1">${size.code}</span>`;
                });
                div += `</div>`;

                return div;
            }
        },
        {
            data: 'colors',
            render: function(data, type, row) {
                let div = `<div>`;
                $.each(data, function(index, color) {
                    div += `<span class="badge badge-info mr-1">${color.name} - ${color.code}</span>`;
                });
                div += `</div>`;

                return div;
            }
        },
        {
            data: 'deleted_at',
            render: function (data, type, row) {
                if (data === null) {
                    return `<h5><span class="badge badge-pill badge-success"><i class="fas fa-check mr-2"></i>Activa</span></h5>`;
                } else {
                    return `<h5><span class="badge badge-pill badge-danger"><i class="fas fa-xmark mr-2"></i>Inactiva</span></h5>`;
                }
            }
        },
        {
            data: 'deleted_at',
            render: function (data, type, row) {
                let btn = `<div class="text-center" style="width: 200px;">`;
                
                btn += `<a onclick="SyncProduct('${row.code}')" type="button"
                class="btn btn-primary btn-sm mr-2" title="Sincronizar inventario de Siesa y Visual Tns.">
                    <i class="fas fa-rotate text-white"></i>
                </a>`;

                if (data === null) {
                    btn += `<a onclick="ShowProductModal(${row.id})" type="button"
                    class="btn btn-info btn-sm mr-2" title="Visualizar producto">
                        <i class="fas fa-eye text-white"></i>
                    </a>`;

                    btn += `<a onclick="DeleteProduct(${row.id})" type="button"
                    class="btn btn-danger btn-sm mr-2" title="Eliminar producto">
                        <i class="fas fa-trash text-white"></i>
                    </a>`;
                } else {
                    btn += `<a onclick="RestoreProduct(${row.id})" type="button"
                    class="btn btn-info btn-sm mr-2"title="Restaurar producto">
                        <i class="fas fa-arrow-rotate-left text-white"></i>
                    </a>`;
                }
                
                btn += `</div>`;
                return btn;
            }
        }
    ],
    columnDefs: [
        {
            targets: 0,
            orderable: true,
            targets: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11]
        },
        {
            orderable: false,
            targets: []
        },
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

tableProducts.on('click', 'button.dt-expand', function (e) {
    let tr = e.target.closest('tr');
    let row = tableProducts.row(tr);

    let iconButton = $(this);

    if (row.child.isShown()) {
        row.child.hide();
        iconButton.html('<i class="fas fa-plus"></i>').removeClass('btn-danger').addClass('btn-success');
    } else {
        row.child(tableProductsInventories(row.data())).show();
        iconButton.html('<i class="fas fa-minus"></i>').removeClass('btn-success').addClass('btn-danger');
        $(`#tableProducts${row.data().id}`).DataTable({});
    }
});

function tableProductsInventories(row) {
    let table = `<table class="table table-bordered table-hover dataTable dtr-inline nowrap w-100" id="tableProducts${row.id}">
                    <thead>
                        <tr>
                            <th>Bodega</th>
                            <th>Talla</th>
                            <th>Color</th>
                            <th>Cantidad</th>
                            <th>Sistema</th>
                        </tr>
                    </thead>
                    <tbody>`;

    $.each(row.inventories, function(index, inventory) {
        table += `<tr>
            <td> ${inventory.warehouse.name} - ${inventory.warehouse.code}</td>
            <td> ${inventory.size.code} </td>
            <td> ${inventory.color.name} - ${inventory.color.code} </td>
            <td> ${inventory.quantity} </td>
            <td> ${inventory.system ?? '-'} </td>
        </tr>`;
    });

    table += `</tbody></table>`;


    return table;
}

