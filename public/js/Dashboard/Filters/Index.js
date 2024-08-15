let references = [];
let currentPosition = 1;
let totalPosition = 0;
let sizes = [];

$('#SideBarButton').trigger('click');
$('#tableReferences').DataTable();

IndexFilterReferences();

function IndexFilterReferences() {
    $.ajax({
        url: `/Dashboard/Filters/Index/Query`,
        type: 'POST',
        data: {
            '_token': $('meta[name="csrf-token"]').attr('content')
        },
        success: function(response) {
            references = response.data.products;
            totalPosition = references.length;
            
            IndexFilterOrganizeReferences(response.data.products, response.data.filtered);

            if(references.length == 0) {
                $(document).Toasts('create', {
                    class: 'bg-warning',
                    title: 'SIN REFERENCIAS PARA FILTRAR.',
                    body: 'No hay referencias disponibles para filtrar, espere a que se acepten pedidos para volver a filtrar.'
                });

                $('#currentPositionReference').text('x');
                $('#totalPositionReference').text('x');

                $('#reference').text('-');
                $('#referenceDescription').text('-');
                $('#color').text('-');
                $('#colorDescription').text('-');
                
                $('#siesa').attr('class', `alert alert-info`);
                $('#tns').attr('class', `alert alert-info`);
                $('#bmi').attr('class', `alert alert-info`);

                $('#loading').show();
                $('#table').hide();

                $('#graficReference').addClass('disabled');
                $('#filterReference').addClass('disabled');
                $('#prioritizeReference').addClass('disabled');
                $('#beforeReference').addClass('disabled');
                $('#afterReference').addClass('disabled');
                $('#listReference').addClass('disabled');
                $('#cuttedReference').addClass('disabled');
            } else {
                IndexFilterAjaxSuccess(response);
                IndexFilterTotalPositionReference();
            }
        },
        error: function(xhr, textStatus, errorThrown) {
            IndexFilterAjaxError(xhr);
        }
    });
}

function IndexFilterTotalPositionReference() {
    $('#siesa').attr('class', `alert alert-info`);
    $('#tns').attr('class', `alert alert-info`);
    $('#bmi').attr('class', `alert alert-info`);

    $('#graficReference').addClass('disabled');
    $('#filterReference').addClass('disabled');
    $('#prioritizeReference').addClass('disabled');
    $('#beforeReference').addClass('disabled');
    $('#afterReference').addClass('disabled');
    $('#listReference').addClass('disabled');
    $('#cuttedReference').addClass('disabled');

    currentPosition = currentPosition > totalPosition ? 1 : currentPosition;

    $('#currentPositionReference').text(currentPosition);
    $('#totalPositionReference').text(totalPosition);

    let object = references[currentPosition - 1];

    let reference = object.product.code;
    let referenceDescription = `${object.product.description} | ${object.product.category} | ${object.product.trademark}`;
    let color = object.color.code;
    let colorDescription = object.color.name;

    $('#reference').text(reference).attr('data-id', object.product.id);
    $('#referenceDescription').text(referenceDescription);
    $('#color').text(color).attr('data-id', object.color.id);
    $('#colorDescription').text(colorDescription);
    
    IndexFilterQueryDataReference(object.product.id, object.color.id);
}

function IndexFilterQueryDataReference(product_id, color_id) {
    $.ajax({
        url: `/Dashboard/Filters/Query`,
        type: 'POST',
        data: {
            '_token': $('meta[name="csrf-token"]').attr('content'),
            'product_id': product_id,
            'color_id': color_id
        },
        success: function(response) {
            sizes = response.data.sizes;
            IndexFilterOrganizeData(response.data);
            IndexFilterFiltered(response.data.filtered);
            IndexFilterAjaxSuccess(response);
        },
        error: function(xhr, textStatus, errorThrown) {
            IndexFilterAjaxError(xhr);
        }
    });
}

function IndexFilterOrganizeData(object) {
    IndexFilterShowColumnsTable(object.sizes);

    $('#siesa').attr('class', `alert ${object.siesaAlert}`);
    $('#tns').attr('class', `alert ${object.tnsAlert}`);
    $('#bmi').attr('class', `alert ${object.bmiAlert}`);

    $('#graficReference').removeClass('disabled');
    $('#filterReference').removeClass('disabled');
    $('#prioritizeReference').removeClass('disabled');
    $('#beforeReference').removeClass('disabled');
    $('#afterReference').removeClass('disabled');
    $('#listReference').removeClass('disabled');
    $('#cuttedReference').removeClass('disabled');

    let rowsWarehouses = [
        {
            'name': 'EXISTENCIA EN OTRAS BODEGA',
            'row': '#columnsTransit',
            'data': object.processed,
            'subject': 'transit_'
        },
        {
            'name': 'CORTE',
            'row': '#columnsCut',
            'data': object.cutted,
            'subject': 'cutted_'
        },
        {
            'name': 'EXISTENCIA PRODUCTO TERMINADO',
            'row': '#columnsDiscount',
            'data': object.finished,
            'subject': 'discount_'
        },
        {
            'name': 'COMPROMETIDO',
            'row': '#columnsCommitted',
            'data': object.committed,
            'subject': 'committed_'
        },
        {
            'name': 'EXISTENCIA DISPONIBLE PARA FILTRAR',
            'row': '#columnsAvailabled',
            'data': object.availabled,
            'subject': 'availabled_'
        },
        {
            'name': 'CURVA DISPONIBLE - FILTRADO',
            'row': '#columnsFiltered',
            'data': object.availabled,
            'subject': 'filtered_'
        },
    ];
    
    $.each(rowsWarehouses, function (index, rowWarehouse) {
        $(rowWarehouse).empty();
        IndexFilterOrganizeWarehouses(rowWarehouse.name, rowWarehouse.data, object.sizes, rowWarehouse.row, rowWarehouse.subject);
    });

    IndexFilterOrganizePercentage();

    $('#bodyClients').empty();
    IndexFilterOrganizeOrders(object.requested, object.sizes);
    
    IndexFilterHideColumnsTable(object.sizes, object);

    $('#loading').hide();
    $('#table').show();
}

function IndexFilterOrganizeReferences(references) {
    $('#tableReferences').DataTable().destroy().draw();
    $('#bodyReferences').empty();
    
    $.each(references, function (index, reference) {
        let tr =`<tr>
            <td>${index + 1}</td>
            <td>${reference.product.trademark}</td>
            <td>${reference.product.code.toUpperCase()}</td>
            <td>${reference.color.code.toUpperCase()} - ${reference.color.name}</td>
            <td>${reference.inventory}</td>
            <td>
                <a class="btn text-white" style="background-color: #343a40; color:white; font-weigth:bold;" data-dismiss="modal"
                type="button" onclick="IndexFilterPrioritizeReference(${index + 1})" title="Priorizar referencia.">
                    <i class="fas fa-magnifying-glass"></i>
                </a>
            </td>
        </tr>`;

        $('#bodyReferences').append(tr);
    });
    $('#tableReferences').DataTable();
}

function IndexFilterOrganizeWarehouses(warehouse, object, sizes, identify, subject) {
    let tr = `<td colspan="4"><label id="${subject}NAME">${warehouse}</label></td>`;
    
    $.each(sizes, function (index, size) {
        tr += `<td><label id="${subject}T${size.code}">${object['T'+size.code]}</label></td>`;
    });
    tr += `<td><label id="${subject}TTOTAL">${object['TOTAL']}</label></td>`;

    $(identify).html(tr);
}

function IndexFilterOrganizePercentage(boolean = true) {
    if(boolean){
        $('#columnsPercentage').empty();
    }

    let tr = `<td colspan="4"><label id="pctg_NAME">PORCENTAJE</label></td>`;
        
    let corteTotal = parseInt($(`#cutted_TTOTAL`).text());
    let disponibleTotal = parseInt($(`#filtered_TTOTAL`).text());
    
    let porcentajeTotal = Math.round((disponibleTotal / corteTotal) * 100)
    
    $.each(sizes, function (index, size) {
        let corte = parseInt($(`#cutted_T${size.code}`).text());
        let disponible = parseInt($(`#filtered_T${size.code}`).text());

        let porcentaje = Math.round((disponible / corte) * 100)

        let background = 'bg-success';

        if (porcentaje < 0) {
            background = 'bg-danger';
        } else if (porcentaje > 0 && porcentajeTotal == 0) {
            background = 'bg-danger';
        } else if (porcentaje == porcentajeTotal && corte > 0) {
            background = 'bg-success';
        } else if (disponible > 0 && corte > 0) {
            if ((porcentajeTotal + 12) >= porcentaje && (porcentajeTotal - 12) <= porcentaje && porcentajeTotal > 95) {
                background = 'bg-warning';
            } else if ((porcentajeTotal + 11) >= porcentaje && (porcentajeTotal - 11) <= porcentaje && porcentajeTotal > 90) {
                background = 'bg-warning';
            } else if ((porcentajeTotal + 10) >= porcentaje && (porcentajeTotal - 10) <= porcentaje && porcentajeTotal > 80) {
                background = 'bg-warning';
            } else if ((porcentajeTotal + 9) >= porcentaje && (porcentajeTotal - 9) <= porcentaje && porcentajeTotal > 70) {
                background = 'bg-warning';
            } else if ((porcentajeTotal + 8) >= porcentaje && (porcentajeTotal - 8) <= porcentaje && porcentajeTotal > 60) {
                background = 'bg-warning';
            } else if ((porcentajeTotal + 7) >= porcentaje && (porcentajeTotal - 7) <= porcentaje && porcentajeTotal > 50) {
                background = 'bg-warning';
            } else if ((porcentajeTotal + 6) >= porcentaje && (porcentajeTotal - 6) <= porcentaje && porcentajeTotal > 40) {
                background = 'bg-warning';
            } else if ((porcentajeTotal + 5) >= porcentaje && (porcentajeTotal - 5) <= porcentaje && porcentajeTotal > 30) {
                background = 'bg-warning';
            } else if ((porcentajeTotal + 4) >= porcentaje && (porcentajeTotal - 4) <= porcentaje && porcentajeTotal > 20) {
                background = 'bg-warning';
            } else if ((porcentajeTotal + 3) >= porcentaje && (porcentajeTotal - 3) <= porcentaje && porcentajeTotal > 10) {
                background = 'bg-warning';
            } else if ((porcentajeTotal + 2) >= porcentaje && (porcentajeTotal - 2) <= porcentaje && porcentajeTotal > 5) {
                background = 'bg-warning';
            } else if ((porcentajeTotal + 1) >= porcentaje && (porcentajeTotal - 1) <= porcentaje && porcentajeTotal > 0) {
                background = 'bg-warning';
            } else {
                background = 'bg-danger';
            }
        } else {
            background = 'bg-danger';
        }

        tr += `<td class="${background}"><label id="pctg_T${size.code}" style="color: white !important;">${corte == 0 ? '0' : Math.round((disponible / corte) * 100)} %</label></td>`;

        if(!boolean) {
            let pctg = corte == 0 ? 0 : Math.round((disponible / corte) * 100);

            let element = $(`#pctg_T${size.code}`);

            element.parent().attr('class', background);
            element.text(`${pctg} %`);
        }
    });

    let background = 'bg-success';

    if(disponibleTotal < 0 || corteTotal == 0) {
        background = 'bg-danger';
    }else{
        background = 'bg-success';
    }
    tr += `<td class="${background}"><label id="pctg_TTOTAL">${corteTotal == 0 ? '0' : Math.round((disponibleTotal / corteTotal) * 100)} %</label></td>`;

    if(!boolean) {
        let pctg_total = corteTotal == 0 ? '0' : Math.round((disponibleTotal / corteTotal) * 100);

        let element_total = $(`#pctg_TTOTAL`);

        element_total.parent().attr('class', background);
        element_total.text(`${pctg_total} %`);
    }

    if(boolean) {
        $('#columnsPercentage').html(tr);
    }
}

function IndexFilterFiltered(filtered) {
    $('#tableFiltered').DataTable().destroy().draw();
    $('#bodyFiltered').empty();

    $.each(filtered, function (index, filter) {
        let tr =`<tr>
            <td>${index + 1}</td>
            <td>${filter.product.trademark}</td>
            <td>${filter.product.code.toUpperCase()}</td>
            <td>${filter.color.code.toUpperCase()} - ${filter.color.name}</td>
        </tr>`;

        $('#bodyFiltered').append(tr);
    });
    $('#tableFiltered').DataTable();
}

function IndexFilterOrganizeOrders(requested, sizes) {
    $.each(requested, function (index, request) {
        let tr = `<tr class="pedidos">
            <td><input type="checkbox" name="check" id="${request.id}" onclick="IndexFilterAvailabledVsFiltered()"></td>
            <td id="order_id">${request.order_id}</td>
            <td>${request.order.client.client_name} | ${request.order.client.client_number_document}-${request.order.client.client_branch_code} | ${request.order.client.client_branch_address} | ${request.order.client.departament} | ${request.order.client.city}</td>
            <td>CORRERIA: ${request.order.correria.code} | OBS PED: ${request.order.seller_observation ?? ''} | OBS CAR: ${request.order.wallet_observation ?? ''} | DESPACHO: OFC ${request.order.wallet_dispatch_official ?? request.order.seller_dispatch_official}% - DCO ${request.order.wallet_dispatch_document ?? request.order.seller_dispatch_document}%</td>`;
            $.each(sizes, function (index, size) {
                tr += `<td><input type="number" class="t${size.code}" id="t${size.code}" value="-${request['T'+size.code]}" onblur="IndexFilterResetValue(this, -${request['T'+size.code]})"></td>`;
            });
            tr += `<td><input type="number" class="tTOTAL" id="tTOTAL" value="-${request.TOTAL}"></td>
        </tr>`;

        $('#bodyClients').append(tr);
    });

    $("#footClients").empty();
    let tr = `<tr>
        <td><input type="checkbox" onclick="IndexFilterCheckAll(this)" name="check_total" id=""></td>
        <th>PED</th>
        <th>CLIENTE</th>
        <th>OBSERVACIONES</th>`;
        $.each(sizes, function (index, size) {
            tr += `<td><label id="sum_t${size.code}">-0</label></td>`;
        });
    tr += `<td><label id="sum_tTOTAL">-0</label></td>
    </tr>`;
    $("#footClients").append(tr);
}

function IndexFilterShowColumnsTable(sizes) {
    $.each(sizes, function (index, size) {
        $(`.tableExistencia thead>tr>th:nth-child(${index + 2})`).show();
        $(`.tableExistencia tbody>tr>td:nth-child(${index + 2})`).show();
        $(`.tableExistencia tfoot>tr>td:nth-child(${index + 2})`).show();
        $(`.tableClientes thead>tr>th:nth-child(${index + 5})`).show();
        $(`.tableClientes tbody>tr>td:nth-child(${index + 5})`).show();
        $(`.tableClientes tfoot>tr>td:nth-child(${index + 5})`).show();
    });
}

function IndexFilterHideColumnsTable(sizes, object) {
    $.each(sizes, function (index, size) {
        let quantity = object.availabled[`T${size.code}`] + object.committed[`T${size.code}`] + object.finished[`T${size.code}`] + object.processed[`T${size.code}`];

        $.each(object.requested, function (column, request) {
            quantity += request[`T${size.code}`];
        });
        
        if(quantity == 0) {
            $(`.tableExistencia thead>tr>th:nth-child(${index + 2})`).hide();
            $(`.tableExistencia tbody>tr>td:nth-child(${index + 2})`).hide();
            $(`.tableExistencia tfoot>tr>td:nth-child(${index + 2})`).hide();
            $(`.tableClientes thead>tr>th:nth-child(${index + 5})`).hide();
            $(`.tableClientes tbody>tr>td:nth-child(${index + 5})`).hide();
            $(`.tableClientes tfoot>tr>td:nth-child(${index + 5})`).hide();
        }
    });
}

function IndexFilterPrioritizeReference(position) {
    currentPosition = position;

    $('#loading').show();
    $('#table').hide();

    IndexFilterTotalPositionReference();
}

function IndexFilterGraficReference() {
    $('#ReferenceGrafic').html('');
    let chosenSizes = [];
    let cutted = [];
    let filtered = [];
    $.each(sizes, function (index, size) {
        let cut = parseInt($(`#cutted_T${size.code}`).text());
        let filter = parseInt($(`#filtered_T${size.code}`).text());
        if(cut != 0 || filter != 0) {
            chosenSizes.push(`T${size.code}`);
            cutted.push(cut);
            filtered.push(filter);
        }
    });

    $.ajax({
        url: `/Dashboard/Filters/Grafic`,
        type: 'POST',
        data: {
            '_token': $('meta[name="csrf-token"]').attr('content'),
            'chosenSizes': chosenSizes,
            'cutted': cutted,
            'filtered': filtered
        },
        success: function(response) {
            let timestamp = new Date().getTime();
            $('#ReferenceGrafic').html(`<img src="${response.data.chart}?t=${timestamp}" alt="GRAFICA CURVA CORTE VS FILTRADA">`);
            IndexFilterAjaxSuccess(response);
        },
        error: function(xhr, textStatus, errorThrown) {
            IndexFilterAjaxError(xhr);
        }
    });
}

function IndexFilterBeforeReference() {
    if((currentPosition - 1) == 0 ) {
        currentPosition = totalPosition;
    } else {
        currentPosition--;
    }

    $('#loading').show();
    $('#table').hide();

    IndexFilterTotalPositionReference();
}

function IndexFilterAfterReference() {
    if((currentPosition + 1) > totalPosition ) {
        currentPosition = 1;
    } else {
        currentPosition++;
    }

    $('#loading').show();
    $('#table').hide();

    IndexFilterTotalPositionReference();
}

function IndexFilterResetValue(input, quantity) {
    let element = parseInt($(input).val());
    if(element === '' || element == undefined || isNaN(element)) {
        $(input).val(quantity);
    } else if(element > 0) {
        $(input).val(parseInt($(input).val()) * -1);
    }
    IndexFilterAvailabledVsFiltered();
}

function IndexFilterCheckAll(source) {
    $('input[type="checkbox"]').not($(source)).prop('checked', $(source).prop('checked'));
    IndexFilterAvailabledVsFiltered();
}

function IndexFilterAvailabledVsFiltered(){
    let tableClientesRows = $('#tableClientes tr.pedidos');

    let sumSizes = {};

    $.each(sizes, function(j, size) {
        sumSizes[`t${size.code}`] = 0;
    });

    sumSizes[`tTOTAL`] = 0;

    $.each(tableClientesRows, function(index, tableClientesRow) {
        if($(tableClientesRow).find('input[name="check"]').prop('checked')){
            
            let total = 0;

            $.each(sizes, function(j, size) {
                
                $(tableClientesRow).find(`#t${size.code}`).attr('onkeyup', 'IndexFilterAvailabledVsFiltered()');
                
                let quantity = parseInt($(tableClientesRow).find(`#t${size.code}`).val())

                sumSizes[`t${size.code}`] += quantity ?? -0;
                sumSizes['tTOTAL'] += quantity ?? -0;
                total += quantity ?? -0;

            })

            $(tableClientesRow).find(`#tTOTAL`).val(total);

        } else {
            $.each(sizes, function(j, size) {
                $(tableClientesRow).find(`#t${size.code}`).removeAttr('onkeyup');
            })
        }
    });

    $.each(sizes, function(j, size) {
        let availabled = parseInt($(`#availabled_T${size.code}`).text());
        let sum = availabled + sumSizes[`t${size.code}`];

        $(`#sum_t${size.code}`).text(sumSizes[`t${size.code}`]);
        
        let filteredElement = $(`#filtered_T${size.code}`);
        filteredElement.text(sum);
    
        if (sum < 0) {
            filteredElement.parent().addClass('bg-danger');
        } else {
            filteredElement.parent().removeClass('bg-danger');
        }
    });

    let availabled_total = parseInt($(`#availabled_TTOTAL`).text());

    $(`#sum_tTOTAL`).text(sumSizes[`tTOTAL`]);

    $(`#filtered_TTOTAL`).text(availabled_total + sumSizes[`tTOTAL`]);
    
    IndexFilterOrganizePercentage(false);
}

function IndexFilterValidated() {
    let reference = $('#reference').text();

    let boolean = true;

    let tableClientesRows = $('#tableClientes tr.pedidos').filter(function() {
        return $(this).find('input[name="check"]').prop('checked');
    });

    if(tableClientesRows.length == 0) {
        toastr.warning(`No se puede filtrar la referencia ${reference} porque no se ha seleccionado ningún pedido.`);
        return false;
    }

    
    $.each(tableClientesRows, function(index, tableClientesRow) {
        let total = $(this).find('#tTOTAL').val();
        let order_id = $(this).find('#order_id').text();

        if(total == 0 || total == -0) {
            toastr.error(`No se puede filtrar el pedido ${order_id} porque la cantidad total es invalida.`);
            boolean = false;
        }
    });

    let cutted_total = parseInt($('#cutted_TTOTAL').text());
    let filtered_total = parseInt($(`#filtered_TTOTAL`).text());

    if(cutted_total == 0) {
        toastr.warning(`No se puede filtrar la referencia ${reference} porque no se ha cargado el corte inicial.`);
        return false;
    }

    let ranges = [
        { limit: 95, range: 12 },
        { limit: 90, range: 11 },
        { limit: 80, range: 10 },
        { limit: 70, range: 9 },
        { limit: 60, range: 8 },
        { limit: 50, range: 7 },
        { limit: 40, range: 6 },
        { limit: 30, range: 5 },
        { limit: 20, range: 4 },
        { limit: 10, range: 3 },
        { limit: 5, range: 2 },
        { limit: 0, range: 1 }
    ];

    $.each(sizes, function(index, size) {
        let cutted = parseInt($(`#cutted_T${size.code}`).text());
        let filtered = parseInt($(`#filtered_T${size.code}`).text());
        
        let pctg = Math.round((filtered / cutted) * 100);
        let pctg_total = Math.round((filtered_total / cutted_total) * 100);

        if(filtered < 0) {
            toastr.error(`Las unidades restantes de la talla ${size.code} no puede ser un valor negativo.`);
            boolean = false;
        }

        if(cutted > 0) {
            if (pctg < 0) {
                toastr.error(`El porcentaje de la talla ${size.code} no puede ser un valor negativo.`);
                boolean = false;
            } else if (pctg > 0 && pctg_total == 0) {
                toastr.error(`Deben filtrarse todas las unidades de la talla ${size.code}.`);
                boolean = false;
            } else {
                $.each(ranges, function(j, range) {
                    if (pctg_total > range.limit) {
                        if ((pctg_total + range.range) < pctg) {
                            toastr.warning(`El porcentaje de la talla ${size.code} es superior al rango permitido para filtrar.`);
                        } else if ((pctg_total - range.range) > pctg) {
                            toastr.warning(`El porcentaje de la talla ${size.code} es inferior al rango permitido para filtrar.`);
                        }
                        return false;
                    }
                });
            }
        }
    });

    return boolean;
}

function IndexFilterReference() {
    let boolean = IndexFilterValidated();
    
    let reference = $('#reference').text();
    let color = $('#color').text();
    let colorName = $('#colorDescription').text();

    if(boolean) {
        Swal.fire({
            title: `¿Desea filtrar los pedido de la referencia ${reference} y color ${colorName} - ${color}?`,
            text: 'Los detalles del pedido seleccionados se filtrarán.',
            icon: 'warning',
            showCancelButton: true,
            cancelButtonColor: '#DD6B55',
            confirmButtonColor: '#3085d6',
            confirmButtonText: 'Si, filtrar!',
            cancelButtonText: 'No, cancelar!',
        }).then((result) => {
            if (result.value) {
                $.ajax({
                    url: `/Dashboard/Filters/Save`,
                    type: 'POST',
                    data: {
                        '_token': $('meta[name="csrf-token"]').attr('content'),
                        'product_id': $('#reference').attr('data-id'),
                        'color_id': $('#color').attr('data-id'),
                        'order_details': $('#tableClientes tr.pedidos').map(function() {
                            if ($(this).find('input[type="checkbox"]').prop('checked')) {
                
                                let row = $(this);
                
                                let object = {
                                    'order_detail_id': row.find('input[type="checkbox"]').attr('id')
                                }
                
                                $.each(sizes, function(j, size) {
                                    object[`T${size.code}`] = row.find(`#t${size.code}`).val() * -1;
                                });
                
                                return object;
                            }
                        }).get()
                    },
                    success: function(response) {
                        IndexFilterReferences();
                        IndexFilterAjaxSuccess(response);
                    },
                    error: function(xhr, textStatus, errorThrown) {
                        IndexFilterAjaxError(xhr);
                    }
                });
            } else {
                toastr.info('Los detalles del pedido no se filtraron.')
            }
        });
    }
}

function IndexFilterAjaxSuccess(response) {
    if(response.status === 200) {
        toastr.success(response.message);
    }
    
    if(response.status === 201) {
        $(document).Toasts('create', {
            class: 'bg-success',
            title: 'REFERENCIA FILTRADA.',
            body: response.message
        });
    }
}

function IndexFilterAjaxError(xhr) {
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
