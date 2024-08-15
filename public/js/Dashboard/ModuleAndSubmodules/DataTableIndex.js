let tableModulesAndSubmodules = $('#modulesAndSubmodules').DataTable({
    processing: true,
    serverSide: true,
    ajax: {
        url: `/Dashboard/ModulesAndSubmodules/Index/Query`,
        type: 'POST',
        data: function (request) {
            var columnMappings = {
                0: 'id',
                1: 'name',
                2: 'icon',
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
            return response.data.modules;
        },
        error: function (xhr, error, thrown) {
            toastr.error(xhr.responseJSON.error ? xhr.responseJSON.error.message : xhr.responseJSON.message);
        }
    },
    columns: [
        { data: 'id' },
        { data: 'module' },
        {
            data: null,
            render: function(data, type, row) {
                return `<i class="${data.icon}"></i>`;
            }
        },
        {
            data: null,
            render: function(data, type, row) {
                let div = `<div>`;
                $.each(data.roles, function(index, role) {
                    div += `<span>${role.name}</span><br>`;
                });
                div += `</div>`;

                return div;
            }
        },
        {
            data: null,
            render: function(data, type, row) {
                let div = `<div">`;
                $.each(data.submodules, function(index, submodule) {
                    div += `<span>${submodule.name}</span><br>`;
                });
                div += `</div>`;

                return div;
            }
        },
        {
            data: null,
            render: function(data, type, row) {
                let div = `<div>`;
                $.each(data.submodules, function(index, submodule) {
                    div += `<span>${submodule.url}</span><br>`;
                });
                div += `</div>`;

                return div;
            }
        },
        {
            data: null,
            render: function(data, type, row) {
                let div = `<div>`;
                $.each(data.submodules, function(index, submodule) {
                    div += `<i class="${submodule.icon}"></i><br>`;
                });
                div += `</div>`;

                return div;
            }
        },
        {
            data: null,
            render: function(data, type, row) {
                let div = `<div>`;
                $.each(data.submodules, function(index, submodule) {
                    div += `<span>${submodule.permission.name}</span><br>`;
                });
                div += `</div>`;

                return div;
            }
        },
        {
            data: null,
            render: function (data, type, row) {
                if (isAdministrador()) {
                    return `<div class="text-center" style="width: 100%;">
                    <a type="button" onclick="EditModuleAndSubmodulesModal(${data.id})"
                    class="btn btn-primary btn-sm" title="Editar modulo y submomdulos.">
                        <i class="fas fa-shield-keyhole text-white"></i>
                    </a></div>`;
                } else {
                    return '';
                }
            }
        },
        {
            data: null,
            render: function (data, type, row) {
                if (isAdministrador()) {
                    return `<div class="text-center" style="width: 100%;">
                    <a type="button" onclick="DeleteModuleAndSubmodules(${data.id})"
                    class="btn btn-danger btn-sm" title="Eliminar modulo y submodulos.">
                        <i class="fas fa-shield-minus text-white"></i>
                    </a></div>`;
                } else {
                    return '';
                }
            }
        },
    ],
    columnDefs: [
        {
            orderable: true,
            targets: [0, 1, 2]
        },
        {
            orderable: false,
            targets: [2, 3, 4, 5, 6, 7, 8, 9]
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
