const grid = new Muuri('.grid', {
	layout: {
		rounding: false
	}
});


grid.refreshItems().layout();

window.addEventListener('load', () => {
	grid.refreshItems().layout();
	document.getElementById('grid').classList.add('imagenes-cargadas');

	// Agregamos el listener para la barra de busqueda
	document.querySelector('#barra-busqueda').addEventListener('input', (evento) => {
		const busqueda = evento.target.value;
		grid.filter( (item) => item.getElement().dataset.etiquetas.includes(busqueda) );
	});

	// Agregamos listener para las imagenes
	const overlay = document.getElementById('overlay');
	document.querySelectorAll('.grid .item img').forEach((elemento) => {
		elemento.addEventListener('click', () => {
			
			const ruta = elemento.dataset.img2src;
			const descripcion = elemento.parentNode.parentNode.parentNode.dataset.descripcion;
			/* overlay.classList.add('activo'); */

			/* document.querySelector('#overlay img').src = ruta;
			document.querySelector('#overlay .descripcion').innerHTML = descripcion; */
		});
	});

	// Eventlistener del boton de cerrar
	/* document.querySelector('#btn-cerrar-popup').addEventListener('click', () => {
		overlay.classList.remove('activo');
	}); */

	// Eventlistener del overlay
	/* overlay.addEventListener('click', (evento) => {
		evento.target.id === 'overlay' ? overlay.classList.remove('activo') : '';
	}); */
});;