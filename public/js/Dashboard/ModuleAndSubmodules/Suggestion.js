function SuggestionModule() {
    toastr.info('El nombre del módulo debe representar claramente su funcionalidad en el aplicativo.');
    toastr.info('Asegúrate de que el nombre del módulo refleje los roles que tendrán acceso a él, por ejemplo, "Configuración" para los roles de "Administrador".');
}

function SuggestionModuleIcon() {
    toastr.info('Selecciona un icono que sea representativo de la funcionalidad específica del módulo. Por ejemplo, "fas fa-cog" para el módulo "Configuración".');
    toastr.info('Puedes apoyarte con la pagina "https://fontawesome.com", puedes usar iconos "free" y "pro".');
}

function SuggestionModuleRoles() {
    toastr.info('Asigna roles que tendrán acceso a este módulo para controlar quién puede utilizarlo');
}

function SuggestionSubmodule() {
    toastr.info('El nombre del submódulo debe describir su funcionalidad de manera concisa y clara.');
    toastr.info('Elige un nombre que refleje el permiso de acceso relacionado. Por ejemplo, para "Users", usa "Usuarios".');
}

function SuggestionSumodulePermission() {
    toastr.info('Selecciona el permiso que corresponde al submódulo, como "Dashboard.Permission.Index".');
    toastr.info('Este permiso controla el acceso a la vista del submódulo.');
}

function SuggestionSumoduleRoute() {
    toastr.info('La ruta del submódulo se generará automáticamente a partir del nombre del permiso asignado. Reemplazará los puntos (.) por barras (/)');
    toastr.info('Por ejemplo, el permiso "Dashboard.Permission.Index" generará la ruta "/Dashboard/Permission/Index".');
}

function SuggestionSubmoduleIcon() {
    toastr.info('Selecciona un icono que sea representativo de la funcionalidad específica del submódulo. Por ejemplo, "fas fa-user" para el submódulo "Usuarios".');
    toastr.info('Puedes apoyarte con la pagina "https://fontawesome.com", puedes usar iconos "free" y "pro".');
}
