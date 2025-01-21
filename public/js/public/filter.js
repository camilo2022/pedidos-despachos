const Toast = Swal.mixin({
    toast: true,
    position: 'top-end',
    showConfirmButton: false,
    timer: 5000
});

const grid = new Muuri('.grid', {
	layout: {
		rounding: false
	}
});

function change(div){
    let element = $(div).find('img');
    $('#element').html(`<img src="${element.attr('src')}" class="product-image" style="height: 500px; width: auto;">`);
    if(element.length == 0) {
        element = $(div).find('source');
        $('#element').html(`<video controls width="100%">
            <source src="${element.attr('src')}" type="video/mp4"/>
            Tu navegador no soporta el elemento de video.
        </video>`);
    }
    $('.product-image-thumb.active').removeClass('active')
    $(div).addClass('active')
}


grid.refreshItems().layout();

function inicializarGridConBusqueda() {
    grid.refreshItems().layout();
    document.getElementById('grid').classList.add('imagenes-cargadas');

    document.querySelector('#barra-busqueda').addEventListener('input', manejarBusqueda);
}

function manejarBusqueda(evento) {
    const busqueda = evento.target.value.toUpperCase();

    grid.filter((item) => item.getElement().dataset.etiquetas.toUpperCase().includes(busqueda));
    grid.refreshItems().layout();

    const visibles = $('#grid div.muuri-item:visible').length;

    if (visibles == 0) {
        Toast.fire({
            icon: 'question',
            title: 'No hay coincidencias a la referencia que está buscando.'
        })
    }
}

window.addEventListener('load', inicializarGridConBusqueda);
