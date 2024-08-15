function DownloadProduct() {
    Swal.fire({
        title: '¿Desea descargar el archivo de productos?',
        text: 'El archivo de productos se procesara y descargara.',
        icon: 'warning',
        showCancelButton: true,
        cancelButtonColor: '#DD6B55',
        confirmButtonColor: '#3085d6',
        confirmButtonText: 'Si, descargar!',
        cancelButtonText: 'No, cancelar!'
    }).then((result) => {
        if (result.value) {
            toastr.info('Por favor espere un momento a que se procese, genere y descargue el archivo porfavor.');
            document.DownloadProducts.submit();
        } else {
            toastr.info('El archivo de productos no fue descargado.')
        }
    });
}
