function Numbers(event) {
    if(event.keyCode >= 48 && event.keyCode <= 57){
        return true;
    }
    return false; 
}

function CleanText(data) {
    return data.replace(/(\r\n|\n|\r)/gm, ' ').replace(/['"`]/g, '');
}

function UpperCase(input) {
    input.value = input.value.toUpperCase();
}

function Trim(input) {
    input.value = input.value.trim();
}

function isAdministrador() {
    return ['SUPER ADMINISTRADOR', 'ADMINISTRADOR'].includes($('meta[name="title"]').attr('content'));
}

function isVendedor() {
    return ['VENDEDOR'].includes($('meta[name="title"]').attr('content'));
}

function isVendedorEspecial() {
    return ['VENDEDOR ESPECIAL'].includes($('meta[name="title"]').attr('content'));
}

function isCartera() {
    return ['CARTERA'].includes($('meta[name="title"]').attr('content'));
}

function isFiltrador() {
    return ['FILTRADOR'].includes($('meta[name="title"]').attr('content'));
}

function isBodega() {
    return ['BODEGA'].includes($('meta[name="title"]').attr('content'));
}

function isCoordinadorBodega() {
    return ['BODEGA'].includes($('meta[name="title"]').attr('content'));
}

function isFacturador() {
    return ['FACTURADOR'].includes($('meta[name="title"]').attr('content'));
}

function isPromotora() {
    return ['PROMOTORA'].includes($('meta[name="title"]').attr('content'));
}

function isCoordinadorPromotora() {
    return ['COORDINADOR'].includes($('meta[name="title"]').attr('content'));
}

function isUsuario() {
    return ['USUARIO'].includes($('meta[name="title"]').attr('content'));
}
