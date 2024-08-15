function SuggestionRole() {
    toastr.info('El nombre del rol debe ser representativo de un submódulo, por ejemplo, para el módulo de usuarios, el rol se llamaría "Users".');
    toastr.info('Es recomendable que el nombre del rol sea en inglés.');
}

function SuggestionPermissions() {
    toastr.info('Se recomienda que el permiso tenga el siguiente formato: "Dashboard.Role.Permission".');
    toastr.info('Es recomendable que el permiso vaya acorde a la URL. Por ejemplo, si la URL es "Dashboard/Users/Index", el permiso podría ser "Dashboard.Users.Index".');
    toastr.info('Se aconseja que el permiso sea el nombre de la función del controlador.');
}
