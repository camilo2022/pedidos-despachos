<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolesAndPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $Dashboard = Role::create(['name' => 'Dashboard']);

        $Users = Role::create(['name' => 'Users']);

        $RolesAndPermissions = Role::create(['name' => 'RolesAndPermissions']);

        $ModulesAndSubmodules = Role::create(['name' => 'ModulesAndSubmodules']);

        $Bussinesses = Role::create(['name' => 'Bussinesses']);

        $Correrias = Role::create(['name' => 'Correrias']);

        $Warehouses = Role::create(['name' => 'Warehouses']);

        $Colors = Role::create(['name' => 'Colors']);

        $Products = Role::create(['name' => 'Products']);

        $Inventories = Role::create(['name' => 'Inventories']);

        $Clients = Role::create(['name' => 'Clients']);

        $Orders = Role::create(['name' => 'Orders']);

        $Filters = Role::create(['name' => 'Filters']);

        $Dispatches = Role::create(['name' => 'Dispatches']);

        $Pickings = Role::create(['name' => 'Pickings']);

        $Packings = Role::create(['name' => 'Packings']);

        $Reports = Role::create(['name' => 'Reports']);

        Permission::create(['name' => 'Dashboard'])->syncRoles([$Dashboard]);
        Permission::create(['name' => 'Dashboard.Chart.Correria'])->syncRoles([$Dashboard]);

        Permission::create(['name' => 'Dashboard.Users.Index'])->syncRoles([$Users]);
        Permission::create(['name' => 'Dashboard.Users.Index.Query'])->syncRoles([$Users]);
        Permission::create(['name' => 'Dashboard.Users.Create'])->syncRoles([$Users]);
        Permission::create(['name' => 'Dashboard.Users.Store'])->syncRoles([$Users]);
        Permission::create(['name' => 'Dashboard.Users.Edit'])->syncRoles([$Users]);
        Permission::create(['name' => 'Dashboard.Users.Update'])->syncRoles([$Users]);
        Permission::create(['name' => 'Dashboard.Users.Show'])->syncRoles([$Users]);
        Permission::create(['name' => 'Dashboard.Users.Password'])->syncRoles([$Users]);
        Permission::create(['name' => 'Dashboard.Users.Delete'])->syncRoles([$Users]);
        Permission::create(['name' => 'Dashboard.Users.Restore'])->syncRoles([$Users]);
        Permission::create(['name' => 'Dashboard.Users.AssignRoleAndPermissions'])->syncRoles([$Users]);
        Permission::create(['name' => 'Dashboard.Users.AssignRoleAndPermissions.Query'])->syncRoles([$Users]);
        Permission::create(['name' => 'Dashboard.Users.RemoveRoleAndPermissions'])->syncRoles([$Users]);
        Permission::create(['name' => 'Dashboard.Users.RemoveRoleAndPermissions.Query'])->syncRoles([$Users]);
        Permission::create(['name' => 'Dashboard.Users.Warehouses'])->syncRoles([$Users]);
        Permission::create(['name' => 'Dashboard.Users.AssignWarehouses'])->syncRoles([$Users]);
        Permission::create(['name' => 'Dashboard.Users.RemoveWarehouses'])->syncRoles([$Users]);

        Permission::create(['name' => 'Dashboard.RolesAndPermissions.Index'])->syncRoles([$RolesAndPermissions]);
        Permission::create(['name' => 'Dashboard.RolesAndPermissions.Index.Query'])->syncRoles([$RolesAndPermissions]);
        Permission::create(['name' => 'Dashboard.RolesAndPermissions.Create'])->syncRoles([$RolesAndPermissions]);
        Permission::create(['name' => 'Dashboard.RolesAndPermissions.Store'])->syncRoles([$RolesAndPermissions]);
        Permission::create(['name' => 'Dashboard.RolesAndPermissions.Edit'])->syncRoles([$RolesAndPermissions]);
        Permission::create(['name' => 'Dashboard.RolesAndPermissions.Update'])->syncRoles([$RolesAndPermissions]);
        Permission::create(['name' => 'Dashboard.RolesAndPermissions.Delete'])->syncRoles([$RolesAndPermissions]);

        Permission::create(['name' => 'Dashboard.ModulesAndSubmodules.Index'])->syncRoles([$ModulesAndSubmodules]);
        Permission::create(['name' => 'Dashboard.ModulesAndSubmodules.Index.Query'])->syncRoles([$ModulesAndSubmodules]);
        Permission::create(['name' => 'Dashboard.ModulesAndSubmodules.Create'])->syncRoles([$ModulesAndSubmodules]);
        Permission::create(['name' => 'Dashboard.ModulesAndSubmodules.Store'])->syncRoles([$ModulesAndSubmodules]);
        Permission::create(['name' => 'Dashboard.ModulesAndSubmodules.Edit'])->syncRoles([$ModulesAndSubmodules]);
        Permission::create(['name' => 'Dashboard.ModulesAndSubmodules.Update'])->syncRoles([$ModulesAndSubmodules]);
        Permission::create(['name' => 'Dashboard.ModulesAndSubmodules.Delete'])->syncRoles([$ModulesAndSubmodules]);

        Permission::create(['name' => 'Dashboard.Businesses.Index'])->syncRoles([$Bussinesses]);
        Permission::create(['name' => 'Dashboard.Businesses.Index.Query'])->syncRoles([$Bussinesses]);
        Permission::create(['name' => 'Dashboard.Businesses.Create'])->syncRoles([$Bussinesses]);
        Permission::create(['name' => 'Dashboard.Businesses.Store'])->syncRoles([$Bussinesses]);
        Permission::create(['name' => 'Dashboard.Businesses.Edit'])->syncRoles([$Bussinesses]);
        Permission::create(['name' => 'Dashboard.Businesses.Update'])->syncRoles([$Bussinesses]);
        Permission::create(['name' => 'Dashboard.Businesses.Delete'])->syncRoles([$Bussinesses]);
        Permission::create(['name' => 'Dashboard.Businesses.Restore'])->syncRoles([$Bussinesses]);
        Permission::create(['name' => 'Dashboard.Businesses.Warehouses'])->syncRoles([$Bussinesses]);
        Permission::create(['name' => 'Dashboard.Businesses.AssignWarehouses'])->syncRoles([$Bussinesses]);
        Permission::create(['name' => 'Dashboard.Businesses.RemoveWarehouses'])->syncRoles([$Bussinesses]);

        Permission::create(['name' => 'Dashboard.Correrias.Index'])->syncRoles([$Correrias]);
        Permission::create(['name' => 'Dashboard.Correrias.Index.Query'])->syncRoles([$Correrias]);
        Permission::create(['name' => 'Dashboard.Correrias.Create'])->syncRoles([$Correrias]);
        Permission::create(['name' => 'Dashboard.Correrias.Store'])->syncRoles([$Correrias]);
        Permission::create(['name' => 'Dashboard.Correrias.Edit'])->syncRoles([$Correrias]);
        Permission::create(['name' => 'Dashboard.Correrias.Update'])->syncRoles([$Correrias]);
        Permission::create(['name' => 'Dashboard.Correrias.Delete'])->syncRoles([$Correrias]);

        Permission::create(['name' => 'Dashboard.Warehouses.Index'])->syncRoles([$Warehouses]);
        Permission::create(['name' => 'Dashboard.Warehouses.Index.Query'])->syncRoles([$Warehouses]);
        Permission::create(['name' => 'Dashboard.Warehouses.Create'])->syncRoles([$Warehouses]);
        Permission::create(['name' => 'Dashboard.Warehouses.Store'])->syncRoles([$Warehouses]);
        Permission::create(['name' => 'Dashboard.Warehouses.Edit'])->syncRoles([$Warehouses]);
        Permission::create(['name' => 'Dashboard.Warehouses.Update'])->syncRoles([$Warehouses]);
        Permission::create(['name' => 'Dashboard.Warehouses.Show'])->syncRoles([$Warehouses]);
        Permission::create(['name' => 'Dashboard.Warehouses.Delete'])->syncRoles([$Warehouses]);
        Permission::create(['name' => 'Dashboard.Warehouses.Restore'])->syncRoles([$Warehouses]);
        Permission::create(['name' => 'Dashboard.Warehouses.SyncSiesa'])->syncRoles([$Warehouses]);
        Permission::create(['name' => 'Dashboard.Warehouses.SyncTns'])->syncRoles([$Warehouses]);

        Permission::create(['name' => 'Dashboard.Colors.Index'])->syncRoles([$Colors]);
        Permission::create(['name' => 'Dashboard.Colors.Index.Query'])->syncRoles([$Colors]);
        Permission::create(['name' => 'Dashboard.Colors.Create'])->syncRoles([$Colors]);
        Permission::create(['name' => 'Dashboard.Colors.Store'])->syncRoles([$Colors]);
        Permission::create(['name' => 'Dashboard.Colors.Edit'])->syncRoles([$Colors]);
        Permission::create(['name' => 'Dashboard.Colors.Update'])->syncRoles([$Colors]);
        Permission::create(['name' => 'Dashboard.Colors.Delete'])->syncRoles([$Colors]);
        Permission::create(['name' => 'Dashboard.Colors.Restore'])->syncRoles([$Colors]);
        Permission::create(['name' => 'Dashboard.Colors.SyncSiesa'])->syncRoles([$Colors]);
        Permission::create(['name' => 'Dashboard.Colors.SyncTns'])->syncRoles([$Colors]);

        Permission::create(['name' => 'Dashboard.Products.Index'])->syncRoles([$Products]);
        Permission::create(['name' => 'Dashboard.Products.Index.Query'])->syncRoles([$Products]);
        Permission::create(['name' => 'Dashboard.Products.Show'])->syncRoles([$Products]);
        Permission::create(['name' => 'Dashboard.Products.Charge'])->syncRoles([$Products]);
        Permission::create(['name' => 'Dashboard.Products.Destroy'])->syncRoles([$Products]);
        Permission::create(['name' => 'Dashboard.Products.Download'])->syncRoles([$Products]);
        Permission::create(['name' => 'Dashboard.Products.SyncSiesa'])->syncRoles([$Products]);
        Permission::create(['name' => 'Dashboard.Products.SyncTns'])->syncRoles([$Products]);

        Permission::create(['name' => 'Dashboard.Inventories.Index'])->syncRoles([$Inventories]);
        Permission::create(['name' => 'Dashboard.Inventories.Index.Query'])->syncRoles([$Inventories]);
        Permission::create(['name' => 'Dashboard.Inventories.Upload.Query'])->syncRoles([$Inventories]);
        Permission::create(['name' => 'Dashboard.Inventories.Upload'])->syncRoles([$Inventories]);
        Permission::create(['name' => 'Dashboard.Inventories.Download'])->syncRoles([$Inventories]);
        Permission::create(['name' => 'Dashboard.Inventories.SyncSiesa'])->syncRoles([$Inventories]);
        Permission::create(['name' => 'Dashboard.Inventories.SyncTns'])->syncRoles([$Inventories]);
        Permission::create(['name' => 'Dashboard.Inventories.SyncBmi.Query'])->syncRoles([$Inventories]);
        Permission::create(['name' => 'Dashboard.Inventories.SyncBmi'])->syncRoles([$Inventories]);

        Permission::create(['name' => 'Dashboard.Clients.Index'])->syncRoles([$Clients]);
        Permission::create(['name' => 'Dashboard.Clients.Index.Query'])->syncRoles([$Clients]);
        Permission::create(['name' => 'Dashboard.Clients.Create'])->syncRoles([$Clients]);
        Permission::create(['name' => 'Dashboard.Clients.Store'])->syncRoles([$Clients]);
        Permission::create(['name' => 'Dashboard.Clients.Edit'])->syncRoles([$Clients]);
        Permission::create(['name' => 'Dashboard.Clients.Update'])->syncRoles([$Clients]);
        Permission::create(['name' => 'Dashboard.Clients.Show'])->syncRoles([$Clients]);
        Permission::create(['name' => 'Dashboard.Clients.Wallet'])->syncRoles([$Clients]);
        Permission::create(['name' => 'Dashboard.Clients.Data'])->syncRoles([$Clients]);
        Permission::create(['name' => 'Dashboard.Clients.Remove'])->syncRoles([$Clients]);
        Permission::create(['name' => 'Dashboard.Clients.Destroy'])->syncRoles([$Clients]);
        Permission::create(['name' => 'Dashboard.Clients.Delete'])->syncRoles([$Clients]);
        Permission::create(['name' => 'Dashboard.Clients.Restore'])->syncRoles([$Clients]);
        Permission::create(['name' => 'Dashboard.Clients.Upload.Query'])->syncRoles([$Clients]);
        Permission::create(['name' => 'Dashboard.Clients.Upload'])->syncRoles([$Clients]);
        Permission::create(['name' => 'Dashboard.Clients.Download'])->syncRoles([$Clients]);
        Permission::create(['name' => 'Dashboard.Clients.SyncSiesa'])->syncRoles([$Clients]);
        Permission::create(['name' => 'Dashboard.Clients.SyncTns'])->syncRoles([$Clients]);

        Permission::create(['name' => 'Dashboard.Orders.Index'])->syncRoles([$Orders]);
        Permission::create(['name' => 'Dashboard.Orders.Index.Query'])->syncRoles([$Orders]);
        Permission::create(['name' => 'Dashboard.Orders.Create'])->syncRoles([$Orders]);
        Permission::create(['name' => 'Dashboard.Orders.Store'])->syncRoles([$Orders]);
        Permission::create(['name' => 'Dashboard.Orders.Edit'])->syncRoles([$Orders]);
        Permission::create(['name' => 'Dashboard.Orders.Update'])->syncRoles([$Orders]);
        Permission::create(['name' => 'Dashboard.Orders.Observation'])->syncRoles([$Orders]);
        Permission::create(['name' => 'Dashboard.Orders.Cancel'])->syncRoles([$Orders]);
        Permission::create(['name' => 'Dashboard.Orders.Assent'])->syncRoles([$Orders]);
        Permission::create(['name' => 'Dashboard.Orders.Pending'])->syncRoles([$Orders]);
        Permission::create(['name' => 'Dashboard.Orders.Suspend'])->syncRoles([$Orders]);
        Permission::create(['name' => 'Dashboard.Orders.Delay'])->syncRoles([$Orders]);
        Permission::create(['name' => 'Dashboard.Orders.Decline'])->syncRoles([$Orders]);
        Permission::create(['name' => 'Dashboard.Orders.Authorize'])->syncRoles([$Orders]);
        Permission::create(['name' => 'Dashboard.Orders.Approve'])->syncRoles([$Orders]);
        Permission::create(['name' => 'Dashboard.Orders.PartiallyApprove'])->syncRoles([$Orders]);
        Permission::create(['name' => 'Dashboard.Orders.Dispatch'])->syncRoles([$Orders]);
        Permission::create(['name' => 'Dashboard.Orders.Wallet'])->syncRoles([$Orders]);
        Permission::create(['name' => 'Dashboard.Orders.Email'])->syncRoles([$Orders]);
        Permission::create(['name' => 'Dashboard.Orders.Download'])->syncRoles([$Orders]);
        Permission::create(['name' => 'Dashboard.Orders.Details.Index'])->syncRoles([$Orders]);
        Permission::create(['name' => 'Dashboard.Orders.Details.Index.Query'])->syncRoles([$Orders]);
        Permission::create(['name' => 'Dashboard.Orders.Details.Create'])->syncRoles([$Orders]);
        Permission::create(['name' => 'Dashboard.Orders.Details.Store'])->syncRoles([$Orders]);
        Permission::create(['name' => 'Dashboard.Orders.Details.Edit'])->syncRoles([$Orders]);
        Permission::create(['name' => 'Dashboard.Orders.Details.Update'])->syncRoles([$Orders]);
        Permission::create(['name' => 'Dashboard.Orders.Details.Pending'])->syncRoles([$Orders]);
        Permission::create(['name' => 'Dashboard.Orders.Details.Authorize'])->syncRoles([$Orders]);
        Permission::create(['name' => 'Dashboard.Orders.Details.Approve'])->syncRoles([$Orders]);
        Permission::create(['name' => 'Dashboard.Orders.Details.Cancel'])->syncRoles([$Orders]);
        Permission::create(['name' => 'Dashboard.Orders.Details.Suspend'])->syncRoles([$Orders]);

        Permission::create(['name' => 'Dashboard.Filters.Index'])->syncRoles([$Filters]);
        Permission::create(['name' => 'Dashboard.Filters.Index.Query'])->syncRoles([$Filters]);
        Permission::create(['name' => 'Dashboard.Filters.Query'])->syncRoles([$Filters]);
        Permission::create(['name' => 'Dashboard.Filters.Grafic'])->syncRoles([$Filters]);
        Permission::create(['name' => 'Dashboard.Filters.Upload'])->syncRoles([$Filters]);
        Permission::create(['name' => 'Dashboard.Filters.Save'])->syncRoles([$Filters]);

        Permission::create(['name' => 'Dashboard.Dispatches.Index'])->syncRoles([$Dispatches]);
        Permission::create(['name' => 'Dashboard.Dispatches.Index.Query'])->syncRoles([$Dispatches]);
        Permission::create(['name' => 'Dashboard.Dispatches.Pending'])->syncRoles([$Dispatches]);
        Permission::create(['name' => 'Dashboard.Dispatches.Approve'])->syncRoles([$Dispatches]);
        Permission::create(['name' => 'Dashboard.Dispatches.Cancel'])->syncRoles([$Dispatches]);
        Permission::create(['name' => 'Dashboard.Dispatches.Picking'])->syncRoles([$Dispatches]);
        Permission::create(['name' => 'Dashboard.Dispatches.Review'])->syncRoles([$Dispatches]);
        Permission::create(['name' => 'Dashboard.Dispatches.Packing'])->syncRoles([$Dispatches]);
        Permission::create(['name' => 'Dashboard.Dispatches.Show'])->syncRoles([$Dispatches]);
        Permission::create(['name' => 'Dashboard.Dispatches.Invoice'])->syncRoles([$Dispatches]);
        Permission::create(['name' => 'Dashboard.Dispatches.Print'])->syncRoles([$Dispatches]);
        Permission::create(['name' => 'Dashboard.Dispatches.Download'])->syncRoles([$Dispatches]);
        Permission::create(['name' => 'Dashboard.Dispatches.Details.Index'])->syncRoles([$Dispatches]);
        Permission::create(['name' => 'Dashboard.Dispatches.Details.Index.Query'])->syncRoles([$Dispatches]);
        Permission::create(['name' => 'Dashboard.Dispatches.Details.Pending'])->syncRoles([$Dispatches]);
        Permission::create(['name' => 'Dashboard.Dispatches.Details.Cancel'])->syncRoles([$Dispatches]);

        Permission::create(['name' => 'Dashboard.Pickings.Index'])->syncRoles([$Pickings]);
        Permission::create(['name' => 'Dashboard.Pickings.Index.Query'])->syncRoles([$Pickings]);
        Permission::create(['name' => 'Dashboard.Pickings.Approve'])->syncRoles([$Pickings]);
        Permission::create(['name' => 'Dashboard.Pickings.Review'])->syncRoles([$Pickings]);
        Permission::create(['name' => 'Dashboard.Pickings.Cancel'])->syncRoles([$Pickings]);
        Permission::create(['name' => 'Dashboard.Pickings.Details.Add'])->syncRoles([$Pickings]);
        
        Permission::create(['name' => 'Dashboard.Packings.Index'])->syncRoles([$Packings]);
        Permission::create(['name' => 'Dashboard.Packings.Index.Query'])->syncRoles([$Packings]);
        Permission::create(['name' => 'Dashboard.Packings.Store'])->syncRoles([$Packings]);
        Permission::create(['name' => 'Dashboard.Packings.Open'])->syncRoles([$Packings]);
        Permission::create(['name' => 'Dashboard.Packings.Close'])->syncRoles([$Packings]);
        Permission::create(['name' => 'Dashboard.Packings.Details.Add'])->syncRoles([$Packings]);
        
        Permission::create(['name' => 'Dashboard.Reports.Sales.Index'])->syncRoles([$Reports]);
        Permission::create(['name' => 'Dashboard.Reports.Sales.Index.Query'])->syncRoles([$Reports]);
        Permission::create(['name' => 'Dashboard.Reports.Dispatches.Index'])->syncRoles([$Reports]);
        Permission::create(['name' => 'Dashboard.Reports.Dispatches.Index.Query'])->syncRoles([$Reports]);
        Permission::create(['name' => 'Dashboard.Reports.Productions.Index'])->syncRoles([$Reports]);
        Permission::create(['name' => 'Dashboard.Reports.Productions.Index.Query'])->syncRoles([$Reports]);
        Permission::create(['name' => 'Dashboard.Reports.Trademarks.Index'])->syncRoles([$Reports]);
        Permission::create(['name' => 'Dashboard.Reports.Trademarks.Index.Query'])->syncRoles([$Reports]);
        Permission::create(['name' => 'Dashboard.Reports.Trademarks.Download'])->syncRoles([$Reports]);
        Permission::create(['name' => 'Dashboard.Reports.Products.Index'])->syncRoles([$Reports]);
        Permission::create(['name' => 'Dashboard.Reports.Products.Index.Query'])->syncRoles([$Reports]);
        Permission::create(['name' => 'Dashboard.Reports.Products.Download'])->syncRoles([$Reports]);
    }
}
