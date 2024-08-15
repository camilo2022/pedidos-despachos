let tablePersonReferences = $('#personReferences').DataTable({
    processing: true,
    serverSide: true,
    ajax: {
        url: `/Dashboard/Clients/People/References/Index/Query`,
        type: 'POST',
        data: function (request) {
            var columnMappings = {
                0: 'id',
                1: 'name',
                2: 'last_name',
                3: 'document_type_id',
                4: 'document_number',
                5: 'country_id',
                6: 'departament_id',
                7: 'city_id',
                8: 'address',
                9: 'neighborhood',
                10: 'email',
                11: 'telephone_number_first',
                12: 'telephone_number_second',
            };
            request._token = $('meta[name="csrf-token"]').attr('content');
            request.perPage = request.length;
            request.page = (request.start / request.length) + 1;
            request.search = request.search.value;
            request.column = columnMappings[request.order[0].column];
            request.dir = request.order[0].dir;
            request.person_id = $('#IndexPersonReferenceButton').attr('data-person_id');
        },
        dataSrc: function (response) {
            response.recordsTotal = response.data.meta.pagination.count;
            response.recordsFiltered = response.data.meta.pagination.total;
            return response.data.personReferences;
        },
        error: function (xhr, error, thrown) {
            toastr.error(xhr.responseJSON.error ? xhr.responseJSON.error.message : xhr.responseJSON.message);
        }
    },
    columns: [
        { data: 'id' },
        { data: 'name' },
        { data: 'last_name' },
        {
            data: 'document_type_id',
            render: function (data, type, row) {
                return row.document_type.name
            }
        },
        { data: 'document_number' },
        {
            data: 'country_id',
            render: function (data, type, row) {
                return row.country.name
            }
        },
        {
            data: 'departament_id',
            render: function (data, type, row) {
                return row.departament.name
            }
        },
        {
            data: 'city_id',
            render: function (data, type, row) {
                return row.city.name
            }
        },
        { data: 'address' },
        { data: 'neighborhood' },
        { data: 'email' },
        { data: 'telephone_number_first' },
        { data: 'telephone_number_second' },
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
                let btn = `<div class="text-center" style="width: 100%;">`;
                if (data === null) {
                    btn += `<a onclick="EditPersonReferenceModal(${row.id})" type="button"
                    class="btn btn-primary btn-sm mr-2" title="Editar persona.">
                        <i class="fas fa-pen text-white"></i>
                    </a>`;

                    btn += `<a onclick="DeletePersonReference(${row.id})" type="button"
                    class="btn btn-danger btn-sm mr-2" title="Eliminar persona.">
                        <i class="fas fa-trash text-white"></i>
                    </a>`;
                } else {
                    btn += `<a onclick="RestorePersonReference(${row.id})" type="button"
                    class="btn btn-info btn-sm mr-2"title="Restaurar persona.">
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
            orderable: true,
            targets: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12]
        },
        {
            orderable: false,
            targets: [13, 14]
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
