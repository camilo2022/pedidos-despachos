<?php

namespace Database\Seeders;

use App\Models\Module;
use App\Models\Submodule;
use Illuminate\Database\Seeder;

class ModulesAndSubmodulesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $Configuracion = Module::create(['name' => 'Configuración', 'icon' => 'fas fa-cog']);

        $Configuracion->roles()->sync([2, 3, 4, 5, 6]);

        Submodule::create([
            'name' => 'Usuarios',
            'url' => '/Dashboard/Users/Index',
            'icon' => 'fas fa-users',
            'module_id' => $Configuracion->id,
            'permission_id' => 3
        ]);

        Submodule::create([
            'name' => 'Accesos',
            'url' => '/Dashboard/RolesAndPermissions/Index',
            'icon' => 'fas fa-key-skeleton-left-right',
            'module_id' => $Configuracion->id,
            'permission_id' => 20
        ]);

        Submodule::create([
            'name' => 'Enrutamientos',
            'url' => '/Dashboard/ModulesAndSubmodules/Index',
            'icon' => 'fas fa-shield-keyhole',
            'module_id' => $Configuracion->id,
            'permission_id' => 27
        ]);

        Submodule::create([
            'name' => 'Empresas',
            'url' => '/Dashboard/Businesses/Index',
            'icon' => 'fas fa-briefcase',
            'module_id' => $Configuracion->id,
            'permission_id' => 34
        ]);

        Submodule::create([
            'name' => 'Correrias',
            'url' => '/Dashboard/Correrias/Index',
            'icon' => 'fas fa-rectangle-vertical-history',
            'module_id' => $Configuracion->id,
            'permission_id' => 45
        ]);

        $Administracion = Module::create(['name' => 'Administración', 'icon' => 'fas fa-folder']);

        $Administracion->roles()->sync([7, 8, 9, 10]);

        Submodule::create([
            'name' => 'Bodegas',
            'url' => '/Dashboard/Warehouses/Index',
            'icon' => 'fas fa-warehouse',
            'module_id' => $Administracion->id,
            'permission_id' => 52
        ]);

        Submodule::create([
            'name' => 'Colores',
            'url' => '/Dashboard/Colors/Index',
            'icon' => 'fas fa-palette',
            'module_id' => $Administracion->id,
            'permission_id' => 63
        ]);

        Submodule::create([
            'name' => 'Productos',
            'url' => '/Dashboard/Products/Index',
            'icon' => 'fas fa-bookmark',
            'module_id' => $Administracion->id,
            'permission_id' => 73
        ]);

        Submodule::create([
            'name' => 'Inventarios',
            'url' => '/Dashboard/Inventories/Index',
            'icon' => 'fas fa-shelves',
            'module_id' => $Administracion->id,
            'permission_id' => 81
        ]);

        $Comercial = Module::create(['name' => 'Comercial', 'icon' => 'fas fa-money-bill']);

        $Comercial->roles()->sync([11, 12, 13, 14, 15, 16]);

        Submodule::create([
            'name' => 'Clientes',
            'url' => '/Dashboard/Clients/Index',
            'icon' => 'fas fa-user-tie',
            'module_id' => $Comercial->id,
            'permission_id' => 90
        ]);

        Submodule::create([
            'name' => 'Pedidos',
            'url' => '/Dashboard/Orders/Index',
            'icon' => 'fas fa-receipt',
            'module_id' => $Comercial->id,
            'permission_id' => 108
        ]);

        Submodule::create([
            'name' => 'Filtro',
            'url' => '/Dashboard/Filters/Index',
            'icon' => 'fas fa-filter',
            'module_id' => $Comercial->id,
            'permission_id' => 142
        ]);

        Submodule::create([
            'name' => 'Ordenes',
            'url' => '/Dashboard/Dispatches/Index',
            'icon' => 'fas fa-truck-fast',
            'module_id' => $Comercial->id,
            'permission_id' => 148
        ]);

        $Reportes = Module::create(['name' => 'Reportes', 'icon' => 'fas fa-chart-mixed-up-circle-currency']);

        $Reportes->roles()->sync([17]);

        Submodule::create([
            'name' => 'Ventas',
            'url' => '/Dashboard/Reports/Sales/Index',
            'icon' => 'fas fa-hand-holding-dollar',
            'module_id' => $Reportes->id,
            'permission_id' => 176
        ]);

        Submodule::create([
            'name' => 'Despacho',
            'url' => '/Dashboard/Reports/Dispatches/Index',
            'icon' => 'fas fa-hand-holding-box',
            'module_id' => $Reportes->id,
            'permission_id' => 178
        ]);

        Submodule::create([
            'name' => 'Produccion',
            'url' => '/Dashboard/Reports/Productions/Index',
            'icon' => 'fas fa-hand-holding-seedling',
            'module_id' => $Reportes->id,
            'permission_id' => 180
        ]);

        Submodule::create([
            'name' => 'Marcas',
            'url' => '/Dashboard/Reports/Trademarks/Index',
            'icon' => 'fas fa-hand-holding-magic',
            'module_id' => $Reportes->id,
            'permission_id' => 182
        ]);

        Submodule::create([
            'name' => 'Referencias',
            'url' => '/Dashboard/Reports/Products/Index',
            'icon' => 'fas fa-hand-holding-medical',
            'module_id' => $Reportes->id,
            'permission_id' => 185
        ]);
    }
}
