<?php

use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\BusinessController;
use App\Http\Controllers\CashRegisterController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\CorreriaController;
use App\Http\Controllers\ColorController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\FilterController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\LibranzaController;
use App\Http\Controllers\ModulesAndSubmodulesController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\OrderDetailController;
use App\Http\Controllers\OrderDispatchController;
use App\Http\Controllers\OrderDispatchDetailController;
use App\Http\Controllers\OrderPackingController;
use App\Http\Controllers\OrderPackingDetailController;
use App\Http\Controllers\OrderPickingController;
use App\Http\Controllers\OrderPickingDetailController;
use App\Http\Controllers\PackageTypeController;
use App\Http\Controllers\PaymentMethodController;
use App\Http\Controllers\PersonController;
use App\Http\Controllers\POSController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\PromotionController;
use App\Http\Controllers\PublicController;
use App\Http\Controllers\ReportDispatchesController;
use App\Http\Controllers\ReportInvoicesController;
use App\Http\Controllers\ReportProductionsController;
use App\Http\Controllers\ReportSalesController;
use App\Http\Controllers\RolesAndPermissionsController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WarehouseController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {

    if (Auth::check()) {
        return redirect('/Dashboard');
    } else {
        return redirect('/login');
    }

});

Route::get('reset-password/{id}/{token}', [ResetPasswordController::class, 'showResetForm']);

Auth::routes(['register' => false]);

Route::prefix('/Public')->group(function () {
    Route::controller(PublicController::class)->group(function () {
        Route::get('/Order/{token}', 'order')->name('Public.Order.Index');
        Route::get('/Package/{token}', 'package')->name('Public.Package.Index');
        Route::get('/Catalogo', 'catalogo')->name('Public.Catalogo.Index');
        Route::get('/Catalogo/{referecia}/{business_id?}', 'referencia')->name('Public.Catalogo.Referencia');
        Route::get('/Catalogo/Download', 'download')->name('Public.Catalogo.Download');
    });
});

Route::middleware(['auth'])->group(function () {

    Route::prefix('/Dashboard')->group(function () {

        Route::controller(HomeController::class)->group(function () {
            Route::get('/', 'index')->middleware('can:Dashboard,Dashboard')->name('Dashboard');
        });

        Route::prefix('/Users')->group(function () {
            Route::controller(UserController::class)->group(function () {
                Route::get('/Index', 'index')->middleware('can:Users,Dashboard.Users.Index')->name('Dashboard.Users.Index');
                Route::post('/Index/Query', 'indexQuery')->middleware('can:Users,Dashboard.Users.Index.Query')->name('Dashboard.Users.Index.Query');
                Route::post('/Create', 'create')->middleware('can:Users,Dashboard.Users.Create')->name('Dashboard.Users.Create');
                Route::post('/Store', 'store')->middleware('can:Users,Dashboard.Users.Store')->name('Dashboard.Users.Store');
                Route::post('/Edit/{id}', 'edit')->middleware('can:Users,Dashboard.Users.Edit')->name('Dashboard.Users.Edit');
                Route::put('/Update/{id}', 'update')->middleware('can:Users,Dashboard.Users.Update')->name('Dashboard.Users.Update');
                Route::post('/Show/{id}', 'show')->middleware('can:Users,Dashboard.Users.Show')->name('Dashboard.Users.Show');
                Route::put('/Password/{id}', 'password')->middleware('can:Users,Dashboard.Users.Password')->name('Dashboard.Users.Password');
                Route::delete('/Delete', 'delete')->middleware('can:Users,Dashboard.Users.Delete')->name('Dashboard.Users.Delete');
                Route::put('/Restore', 'restore')->middleware('can:Users,Dashboard.Users.Restore')->name('Dashboard.Users.Restore');
                Route::post('/AssignRoleAndPermissions', 'assignRoleAndPermissions')->middleware('can:Users,Dashboard.Users.AssignRoleAndPermissions')->name('Dashboard.Users.AssignRoleAndPermissions');
                Route::post('/AssignRoleAndPermissions/Query', 'assignRoleAndPermissionsQuery')->middleware('can:Users,Dashboard.Users.AssignRoleAndPermissions.Query')->name('Dashboard.Users.AssignRoleAndPermissions.Query');
                Route::post('/RemoveRoleAndPermissions', 'removeRoleAndPermissions')->middleware('can:Users,Dashboard.Users.RemoveRoleAndPermissions')->name('Dashboard.Users.RemoveRoleAndPermissions');
                Route::post('/RemoveRoleAndPermissions/Query', 'removeRoleAndPermissionsQuery')->middleware('can:Users,Dashboard.Users.RemoveRoleAndPermissions.Query')->name('Dashboard.Users.RemoveRoleAndPermissions.Query');
                Route::post('/Warehouses/{id}', 'warehouses')->middleware('can:Users,Dashboard.Users.Warehouses')->name('Dashboard.Users.Warehouses');
                Route::post('/AssignWarehouses', 'assignWarehouses')->middleware('can:Users,Dashboard.Users.AssignWarehouses')->name('Dashboard.Users.AssignWarehouses');
                Route::delete('/RemoveWarehouses', 'removeWarehouses')->middleware('can:Users,Dashboard.Users.RemoveWarehouses')->name('Dashboard.Users.RemoveWarehouses');
            });
        });

        Route::prefix('/RolesAndPermissions')->group(function () {
            Route::controller(RolesAndPermissionsController::class)->group(function () {
                Route::get('/Index', 'index')->middleware('can:RolesAndPermissions,Dashboard.RolesAndPermissions.Index')->name('Dashboard.RolesAndPermissions.Index');
                Route::post('/Index/Query', 'indexQuery')->middleware('can:RolesAndPermissions,Dashboard.RolesAndPermissions.Index.Query')->name('Dashboard.RolesAndPermissions.Index.Query');
                Route::post('/Create', 'create')->middleware('can:RolesAndPermissions,Dashboard.RolesAndPermissions.Create')->name('Dashboard.RolesAndPermissions.Create');
                Route::post('/Store', 'store')->middleware('can:RolesAndPermissions,Dashboard.RolesAndPermissions.Store')->name('Dashboard.RolesAndPermissions.Store');
                Route::post('/Edit/{id}', 'edit')->middleware('can:RolesAndPermissions,Dashboard.RolesAndPermissions.Edit')->name('Dashboard.RolesAndPermissions.Edit');
                Route::put('/Update/{id}', 'update')->middleware('can:RolesAndPermissions,Dashboard.RolesAndPermissions.Update')->name('Dashboard.RolesAndPermissions.Update');
                Route::delete('/Delete', 'delete')->middleware('can:RolesAndPermissions,Dashboard.RolesAndPermissions.Delete')->name('Dashboard.RolesAndPermissions.Delete');
            });
        });

        Route::prefix('/ModulesAndSubmodules')->group(function () {
            Route::controller(ModulesAndSubmodulesController::class)->group(function () {
                Route::get('/Index', 'index')->middleware('can:ModulesAndSubmodules,Dashboard.ModulesAndSubmodules.Index')->name('Dashboard.ModulesAndSubmodules.Index');
                Route::post('/Index/Query', 'indexQuery')->middleware('can:ModulesAndSubmodules,Dashboard.ModulesAndSubmodules.Index.Query')->name('Dashboard.ModulesAndSubmodules.Index.Query');
                Route::post('/Create', 'create')->middleware('can:ModulesAndSubmodules,Dashboard.ModulesAndSubmodules.Create')->name('Dashboard.ModulesAndSubmodules.Create');
                Route::post('/Store', 'store')->middleware('can:ModulesAndSubmodules,Dashboard.ModulesAndSubmodules.Store')->name('Dashboard.ModulesAndSubmodules.Store');
                Route::post('/Edit/{id}', 'edit')->middleware('can:ModulesAndSubmodules,Dashboard.ModulesAndSubmodules.Edit')->name('Dashboard.ModulesAndSubmodules.Edit');
                Route::put('/Update/{id}', 'update')->middleware('can:ModulesAndSubmodules,Dashboard.ModulesAndSubmodules.Update')->name('Dashboard.ModulesAndSubmodules.Update');
                Route::delete('/Delete', 'delete')->middleware('can:ModulesAndSubmodules,Dashboard.ModulesAndSubmodules.Delete')->name('Dashboard.ModulesAndSubmodules.Delete');
            });
        });

        Route::prefix('/Businesses')->group(function () {
            Route::controller(BusinessController::class)->group(function () {
                Route::get('/Index', 'index')->middleware('can:Businesses,Dashboard.Businesses.Index')->name('Dashboard.Businesses.Index');
                Route::post('/Index/Query', 'indexQuery')->middleware('can:Businesses,Dashboard.Businesses.Index.Query')->name('Dashboard.Businesses.Index.Query');
                Route::post('/Create', 'create')->middleware('can:Businesses,Dashboard.Businesses.Create')->name('Dashboard.Businesses.Create');
                Route::post('/Store', 'store')->middleware('can:Businesses,Dashboard.Businesses.Store')->name('Dashboard.Businesses.Store');
                Route::post('/Edit/{id}', 'edit')->middleware('can:Businesses,Dashboard.Businesses.Edit')->name('Dashboard.Businesses.Edit');
                Route::put('/Update/{id}', 'update')->middleware('can:Businesses,Dashboard.Businesses.Update')->name('Dashboard.Businesses.Update');
                Route::delete('/Delete', 'delete')->middleware('can:Businesses,Dashboard.Businesses.Delete')->name('Dashboard.Businesses.Delete');
                Route::put('/Restore', 'restore')->middleware('can:Businesses,Dashboard.Businesses.Restore')->name('Dashboard.Businesses.Restore');
                Route::post('/Warehouses/{id}', 'warehouses')->middleware('can:Businesses,Dashboard.Businesses.Warehouses')->name('Dashboard.Businesses.Warehouses');
                Route::post('/AssignWarehouses', 'assignWarehouses')->middleware('can:Businesses,Dashboard.Businesses.AssignWarehouses')->name('Dashboard.Businesses.AssignWarehouses');
                Route::delete('/RemoveWarehouses', 'removeWarehouses')->middleware('can:Businesses,Dashboard.Businesses.RemoveWarehouses')->name('Dashboard.Businesses.RemoveWarehouses');
            });
        });

        Route::prefix('/Correrias')->group(function () {
            Route::controller(CorreriaController::class)->group(function () {
                Route::get('/Index', 'index')->middleware('can:Correrias,Dashboard.Correrias.Index')->name('Dashboard.Correrias.Index');
                Route::post('/Index/Query', 'indexQuery')->middleware('can:Correrias,Dashboard.Correrias.Index.Query')->name('Dashboard.Correrias.Index.Query');
                Route::post('/Create', 'create')->middleware('can:Correrias,Dashboard.Correrias.Create')->name('Dashboard.Correrias.Create');
                Route::post('/Store', 'store')->middleware('can:Correrias,Dashboard.Correrias.Store')->name('Dashboard.Correrias.Store');
                Route::post('/Edit/{id}', 'edit')->middleware('can:Correrias,Dashboard.Correrias.Edit')->name('Dashboard.Correrias.Edit');
                Route::put('/Update/{id}', 'update')->middleware('can:Correrias,Dashboard.Correrias.Update')->name('Dashboard.Correrias.Update');
                Route::delete('/Delete', 'delete')->middleware('can:Correrias,Dashboard.Correrias.Delete')->name('Dashboard.Correrias.Delete');
            });
        });

        Route::prefix('/PackageTypes')->group(function () {
            Route::controller(PackageTypeController::class)->group(function () {
                Route::get('/Index', 'index')->middleware('can:PackageTypes,Dashboard.PackageTypes.Index')->name('Dashboard.PackageTypes.Index');
                Route::post('/Index/Query', 'indexQuery')->middleware('can:PackageTypes,Dashboard.PackageTypes.Index.Query')->name('Dashboard.PackageTypes.Index.Query');
                Route::post('/Create', 'create')->middleware('can:PackageTypes,Dashboard.PackageTypes.Create')->name('Dashboard.PackageTypes.Create');
                Route::post('/Store', 'store')->middleware('can:PackageTypes,Dashboard.PackageTypes.Store')->name('Dashboard.PackageTypes.Store');
                Route::post('/Edit/{id}', 'edit')->middleware('can:PackageTypes,Dashboard.PackageTypes.Edit')->name('Dashboard.PackageTypes.Edit');
                Route::put('/Update/{id}', 'update')->middleware('can:PackageTypes,Dashboard.PackageTypes.Update')->name('Dashboard.PackageTypes.Update');
                Route::delete('/Delete', 'delete')->middleware('can:PackageTypes,Dashboard.PackageTypes.Delete')->name('Dashboard.PackageTypes.Delete');
            });
        });

        Route::prefix('/PaymentMethods')->group(function () {
            Route::controller(PaymentMethodController::class)->group(function () {
                Route::get('/Index', 'index')->middleware('can:PaymentMethods,Dashboard.PaymentMethods.Index')->name('Dashboard.PaymentMethods.Index');
                Route::post('/Index/Query', 'indexQuery')->middleware('can:PaymentMethods,Dashboard.PaymentMethods.Index.Query')->name('Dashboard.PaymentMethods.Index.Query');
                Route::post('/Create', 'create')->middleware('can:PaymentMethods,Dashboard.PaymentMethods.Create')->name('Dashboard.PaymentMethods.Create');
                Route::post('/Store', 'store')->middleware('can:PaymentMethods,Dashboard.PaymentMethods.Store')->name('Dashboard.PaymentMethods.Store');
                Route::post('/Edit/{id}', 'edit')->middleware('can:PaymentMethods,Dashboard.PaymentMethods.Edit')->name('Dashboard.PaymentMethods.Edit');
                Route::put('/Update/{id}', 'update')->middleware('can:PaymentMethods,Dashboard.PaymentMethods.Update')->name('Dashboard.PaymentMethods.Update');
                Route::delete('/Delete', 'delete')->middleware('can:PaymentMethods,Dashboard.PaymentMethods.Delete')->name('Dashboard.PaymentMethods.Delete');
            });
        });

        Route::prefix('/Promotions')->group(function () {
            Route::controller(PromotionController::class)->group(function () {
                Route::get('/Index', 'index')->middleware('can:Promotions,Dashboard.Promotions.Index')->name('Dashboard.Promotions.Index');
                Route::post('/Index/Query', 'indexQuery')->middleware('can:Promotions,Dashboard.Promotions.Index.Query')->name('Dashboard.Promotions.Index.Query');
                Route::post('/Create', 'create')->middleware('can:Promotions,Dashboard.Promotions.Create')->name('Dashboard.Promotions.Create');
                Route::post('/Store', 'store')->middleware('can:Promotions,Dashboard.Promotions.Store')->name('Dashboard.Promotions.Store');
                Route::post('/Edit/{id}', 'edit')->middleware('can:Promotions,Dashboard.Promotions.Edit')->name('Dashboard.Promotions.Edit');
                Route::put('/Update/{id}', 'update')->middleware('can:Promotions,Dashboard.Promotions.Update')->name('Dashboard.Promotions.Update');
                Route::delete('/Delete', 'delete')->middleware('can:Promotions,Dashboard.Promotions.Delete')->name('Dashboard.Promotions.Delete');
            });
        });

        Route::prefix('/Warehouses')->group(function () {
            Route::controller(WarehouseController::class)->group(function () {
                Route::get('/Index', 'index')->middleware('can:Warehouses,Dashboard.Warehouses.Index')->name('Dashboard.Warehouses.Index');
                Route::post('/Index/Query', 'indexQuery')->middleware('can:Warehouses,Dashboard.Warehouses.Index.Query')->name('Dashboard.Warehouses.Index.Query');
                Route::post('/Create', 'create')->middleware('can:Warehouses,Dashboard.Warehouses.Create')->name('Dashboard.Warehouses.Create');
                Route::post('/Store', 'store')->middleware('can:Warehouses,Dashboard.Warehouses.Store')->name('Dashboard.Warehouses.Store');
                Route::post('/Edit/{id}', 'edit')->middleware('can:Warehouses,Dashboard.Warehouses.Edit')->name('Dashboard.Warehouses.Edit');
                Route::put('/Update/{id}', 'update')->middleware('can:Warehouses,Dashboard.Warehouses.Update')->name('Dashboard.Warehouses.Update');
                Route::post('/Show/{id}', 'show')->middleware('can:Warehouses,Dashboard.Warehouses.Show')->name('Dashboard.Warehouses.Show');
                Route::delete('/Delete', 'delete')->middleware('can:Warehouses,Dashboard.Warehouses.Delete')->name('Dashboard.Warehouses.Delete');
                Route::put('/Restore', 'restore')->middleware('can:Warehouses,Dashboard.Warehouses.Restore')->name('Dashboard.Warehouses.Restore');
                Route::post('/SyncSiesa', 'syncSiesa')->middleware('can:Warehouses,Dashboard.Warehouses.SyncSiesa')->name('Dashboard.Warehouses.SyncSiesa');
                Route::post('/SyncTns', 'syncTns')->middleware('can:Warehouses,Dashboard.Warehouses.SyncTns')->name('Dashboard.Warehouses.SyncTns');
            });
        });

        Route::prefix('/Colors')->group(function () {
            Route::controller(ColorController::class)->group(function () {
                Route::get('/Index', 'index')->middleware('can:Colors,Dashboard.Colors.Index')->name('Dashboard.Colors.Index');
                Route::post('/Index/Query', 'indexQuery')->middleware('can:Colors,Dashboard.Colors.Index.Query')->name('Dashboard.Colors.Index.Query');
                Route::post('/Create', 'create')->middleware('can:Colors,Dashboard.Colors.Create')->name('Dashboard.Colors.Create');
                Route::post('/Store', 'store')->middleware('can:Colors,Dashboard.Colors.Store')->name('Dashboard.Colors.Store');
                Route::post('/Edit/{id}', 'edit')->middleware('can:Colors,Dashboard.Colors.Edit')->name('Dashboard.Colors.Edit');
                Route::put('/Update/{id}', 'update')->middleware('can:Colors,Dashboard.Colors.Update')->name('Dashboard.Colors.Update');
                Route::delete('/Delete', 'delete')->middleware('can:Colors,Dashboard.Colors.Delete')->name('Dashboard.Colors.Delete');
                Route::put('/Restore', 'restore')->middleware('can:Colors,Dashboard.Colors.Restore')->name('Dashboard.Colors.Restore');
                Route::post('/SyncSiesa', 'syncSiesa')->middleware('can:Colors,Dashboard.Colors.SyncSiesa')->name('Dashboard.Colors.SyncSiesa');
                Route::post('/SyncTns', 'syncTns')->middleware('can:Colors,Dashboard.Colors.SyncTns')->name('Dashboard.Colors.SyncTns');
            });
        });

        Route::prefix('/Products')->group(function () {
            Route::controller(ProductController::class)->group(function () {
                Route::get('/Index', 'index')->middleware('can:Products,Dashboard.Products.Index')->name('Dashboard.Products.Index');
                Route::post('/Index/Query', 'indexQuery')->middleware('can:Products,Dashboard.Products.Index.Query')->name('Dashboard.Products.Index.Query');
                Route::post('/Show/{id}', 'show')->middleware('can:Products,Dashboard.Products.Show')->name('Dashboard.Products.Show');
                Route::post('/Charge', 'charge')->middleware('can:Products,Dashboard.Products.Charge')->name('Dashboard.Products.Charge');
                Route::delete('/Destroy', 'destroy')->middleware('can:Products,Dashboard.Products.Destroy')->name('Dashboard.Products.Destroy');
                Route::post('/Download', 'download')->middleware('can:Products,Dashboard.Products.Download')->name('Dashboard.Products.Download');
                Route::post('/SyncSiesa', 'syncSiesa')->middleware('can:Products,Dashboard.Products.SyncSiesa')->name('Dashboard.Products.SyncSiesa');
                Route::post('/SyncTns', 'syncTns')->middleware('can:Products,Dashboard.Products.SyncTns')->name('Dashboard.Products.SyncTns');
                Route::post('/SyncPortal', 'syncPortal')->middleware('can:Products,Dashboard.Products.SyncPortal')->name('Dashboard.Products.SyncPortal');
                Route::get('/Sync', 'sync')->middleware('can:Products,Dashboard.Products.Sync')->name('Dashboard.Products.Sync');
            });
        });

        Route::prefix('/Inventories')->group(function () {
            Route::controller(InventoryController::class)->group(function () {
                Route::get('/Index', 'index')->middleware('can:Inventories,Dashboard.Inventories.Index')->name('Dashboard.Inventories.Index');
                Route::post('/Index/Query', 'indexQuery')->middleware('can:Inventories,Dashboard.Inventories.Index.Query')->name('Dashboard.Inventories.Index.Query');
                Route::post('/Upload/Query', 'uploadQuery')->middleware('can:Inventories,Dashboard.Inventories.Upload.Query')->name('Dashboard.Inventories.Upload.Query');
                Route::post('/Upload', 'upload')->middleware('can:Inventories,Dashboard.Inventories.Upload')->name('Dashboard.Inventories.Upload');
                Route::post('/Download', 'download')->middleware('can:Inventories,Dashboard.Inventories.Download')->name('Dashboard.Inventories.Download');
                Route::post('/SyncSiesa', 'syncSiesa')->middleware('can:Inventories,Dashboard.Inventories.SyncSiesa')->name('Dashboard.Inventories.SyncSiesa');
                Route::post('/SyncTns', 'syncTns')->middleware('can:Inventories,Dashboard.Inventories.SyncTns')->name('Dashboard.Inventories.SyncTns');
            });
        });

        Route::prefix('/Clients')->group(function () {
            Route::controller(ClientController::class)->group(function () {
                Route::get('/Index', 'index')->middleware('can:Clients,Dashboard.Clients.Index')->name('Dashboard.Clients.Index');
                Route::post('/Index/Query', 'indexQuery')->middleware('can:Clients,Dashboard.Clients.Index.Query')->name('Dashboard.Clients.Index.Query');
                Route::post('/Create', 'create')->middleware('can:Clients,Dashboard.Clients.Create')->name('Dashboard.Clients.Create');
                Route::post('/Store', 'store')->middleware('can:Clients,Dashboard.Clients.Store')->name('Dashboard.Clients.Store');
                Route::post('/Edit/{id}', 'edit')->middleware('can:Clients,Dashboard.Clients.Edit')->name('Dashboard.Clients.Edit');
                Route::put('/Update/{id}', 'update')->middleware('can:Clients,Dashboard.Clients.Update')->name('Dashboard.Clients.Update');
                Route::post('/Show/{id}', 'show')->middleware('can:Clients,Dashboard.Clients.Show')->name('Dashboard.Clients.Show');
                Route::post('/Wallet', 'wallet')->middleware('can:Clients,Dashboard.Clients.Wallet')->name('Dashboard.Clients.Wallet');
                Route::post('/Data', 'data')->middleware('can:Clients,Dashboard.Clients.Data')->name('Dashboard.Clients.Data');
                Route::delete('/Remove', 'remove')->middleware('can:Clients,Dashboard.Clients.Remove')->name('Dashboard.Clients.Remove');
                Route::delete('/Destroy', 'destroy')->middleware('can:Clients,Dashboard.Clients.Destroy')->name('Dashboard.Clients.Destroy');
                Route::delete('/Delete', 'delete')->middleware('can:Clients,Dashboard.Clients.Delete')->name('Dashboard.Clients.Delete');
                Route::put('/Restore', 'restore')->middleware('can:Clients,Dashboard.Clients.Restore')->name('Dashboard.Clients.Restore');
                Route::post('/Upload/Query', 'uploadQuery')->middleware('can:Clients,Dashboard.Clients.Upload.Query')->name('Dashboard.Clients.Upload.Query');
                Route::post('/Upload', 'upload')->middleware('can:Clients,Dashboard.Clients.Upload')->name('Dashboard.Clients.Upload');
                Route::post('/Audit/{id}', 'audit')->middleware('can:Clients,Dashboard.Clients.Audit')->name('Dashboard.Clients.Audit');
                Route::get('/Download/{id}', 'download')->middleware('can:Clients,Dashboard.Clients.Download')->name('Dashboard.Clients.Download');
                Route::post('/SyncSiesa', 'syncSiesa')->middleware('can:Clients,Dashboard.Clients.SyncSiesa')->name('Dashboard.Clients.SyncSiesa');
                Route::post('/SyncTns', 'syncTns')->middleware('can:Clients,Dashboard.Clients.SyncTns')->name('Dashboard.Clients.SyncTns');
            });
        });

        Route::prefix('/People')->group(function () {
            Route::controller(PersonController::class)->group(function () {
                Route::get('/Index', 'index')->middleware('can:People,Dashboard.People.Index')->name('Dashboard.People.Index');
                Route::post('/Index/Query', 'indexQuery')->middleware('can:People,Dashboard.People.Index.Query')->name('Dashboard.People.Index.Query');
                Route::post('/Create', 'create')->middleware('can:People,Dashboard.People.Create')->name('Dashboard.People.Create');
                Route::post('/Store', 'store')->middleware('can:People,Dashboard.People.Store')->name('Dashboard.People.Store');
                Route::post('/Edit/{id}', 'edit')->middleware('can:People,Dashboard.People.Edit')->name('Dashboard.People.Edit');
                Route::put('/Update/{id}', 'update')->middleware('can:People,Dashboard.People.Update')->name('Dashboard.People.Update');
                Route::post('/Invoice/{id}', 'invoice')->middleware('can:People,Dashboard.People.Invoice')->name('Dashboard.People.Invoice');
            });
        });

        Route::prefix('/Employees')->group(function () {
            Route::controller(EmployeeController::class)->group(function () {
                Route::get('/Index', 'index')->middleware('can:Employees,Dashboard.Employees.Index')->name('Dashboard.Employees.Index');
                Route::post('/Index/Query', 'indexQuery')->middleware('can:Employees,Dashboard.Employees.Index.Query')->name('Dashboard.Employees.Index.Query');
                Route::post('/SyncSiesa', 'syncSiesa')->middleware('can:Employees,Dashboard.Employees.SyncSiesa')->name('Dashboard.Employees.SyncSiesa');
            });
        });

        Route::prefix('/Stores')->group(function () {
            Route::controller(UserController::class)->group(function () {
                Route::get('/Index', 'index')->middleware('can:Stores,Dashboard.Stores.Index')->name('Dashboard.Stores.Index');
                Route::post('/Index/Query', 'indexQuery')->middleware('can:Stores,Dashboard.Stores.Index.Query')->name('Dashboard.Stores.Index.Query');
                Route::post('/Create', 'create')->middleware('can:Stores,Dashboard.Stores.Create')->name('Dashboard.Stores.Create');
                Route::post('/Store', 'store')->middleware('can:Stores,Dashboard.Stores.Store')->name('Dashboard.Stores.Store');
                Route::post('/Edit/{id}', 'edit')->middleware('can:Stores,Dashboard.Stores.Edit')->name('Dashboard.Stores.Edit');
                Route::put('/Update/{id}', 'update')->middleware('can:Stores,Dashboard.Stores.Update')->name('Dashboard.Stores.Update');
                Route::delete('/Delete', 'delete')->middleware('can:Stores,Dashboard.Stores.Delete')->name('Dashboard.Stores.Delete');
                Route::put('/Restore', 'restore')->middleware('can:Stores,Dashboard.Stores.Restore')->name('Dashboard.Stores.Restore');
                Route::post('/Warehouses/{id}', 'warehouses')->middleware('can:Stores,Dashboard.Stores.Warehouses')->name('Dashboard.Stores.Warehouses');
                Route::post('/AssignWarehouses', 'assignWarehouses')->middleware('can:Stores,Dashboard.Stores.AssignWarehouses')->name('Dashboard.Stores.AssignWarehouses');
                Route::delete('/RemoveWarehouses', 'removeWarehouses')->middleware('can:Stores,Dashboard.Stores.RemoveWarehouses')->name('Dashboard.Stores.RemoveWarehouses');
                Route::post('/Users/{id}', 'users')->middleware('can:Stores,Dashboard.Stores.Users')->name('Dashboard.Stores.Users');
                Route::post('/AssignUsers', 'assignUsers')->middleware('can:Stores,Dashboard.Stores.AssignUsers')->name('Dashboard.Stores.AssignUsers');
                Route::delete('/RemoveUsers', 'removeUsers')->middleware('can:Stores,Dashboard.Stores.RemoveUsers')->name('Dashboard.Stores.RemoveUsers');
                Route::post('/PaymentMethods/{id}', 'paymentMethods')->middleware('can:Stores,Dashboard.Stores.PaymentMethods')->name('Dashboard.Stores.PaymentMethods');
                Route::post('/AssignPaymentMethods', 'assignPaymentMethods')->middleware('can:Stores,Dashboard.Stores.AssignPaymentMethods')->name('Dashboard.Stores.AssignPaymentMethods');
                Route::delete('/RemovePaymentMethods', 'removePaymentMethods')->middleware('can:Stores,Dashboard.Stores.RemovePaymentMethods')->name('Dashboard.Stores.RemovePaymentMethods');
                Route::post('/Promotions/{id}', 'promotions')->middleware('can:Stores,Dashboard.Stores.Promotions')->name('Dashboard.Stores.Promotions');
                Route::post('/AssignPromotions', 'assignPromotions')->middleware('can:Stores,Dashboard.Stores.AssignPromotions')->name('Dashboard.Stores.AssignPromotions');
                Route::delete('/RemovePromotions', 'removePromotions')->middleware('can:Stores,Dashboard.Stores.RemovePromotions')->name('Dashboard.Stores.RemovePromotions');
                Route::prefix('/CashRegisters')->group(function () {
                    Route::controller(CashRegisterController::class)->group(function () {
                        Route::get('/Index/{id}', 'index')->middleware('can:Stores,Dashboard.Stores.CashRegisters.Index')->name('Dashboard.Stores.CashRegisters.Index');
                        Route::post('/Index/Query', 'indexQuery')->middleware('can:Stores,Dashboard.Stores.CashRegisters.Index.Query')->name('Dashboard.Stores.CashRegisters.Index.Query');
                        Route::post('/Create', 'create')->middleware('can:Stores,Dashboard.Stores.CashRegisters.Create')->name('Dashboard.Stores.CashRegisters.Create');
                        Route::post('/Store', 'store')->middleware('can:Stores,Dashboard.Stores.CashRegisters.Store')->name('Dashboard.Stores.CashRegisters.Store');
                        Route::post('/Edit/{id}', 'edit')->middleware('can:Stores,Dashboard.Stores.CashRegisters.Edit')->name('Dashboard.Stores.CashRegisters.Edit');
                        Route::put('/Update/{id}', 'update')->middleware('can:Stores,Dashboard.Stores.CashRegisters.Update')->name('Dashboard.Stores.CashRegisters.Update');
                        Route::post('/Show/{id}', 'show')->middleware('can:Stores,Dashboard.Stores.CashRegisters.Show')->name('Dashboard.Stores.CashRegisters.Show');
                        Route::post('/Audit/{id}', 'audit')->middleware('can:Stores,Dashboard.Stores.CashRegisters.Audit')->name('Dashboard.Stores.CashRegisters.Audit');
                        Route::delete('/Delete', 'delete')->middleware('can:Stores,Dashboard.Stores.CashRegisters.Delete')->name('Dashboard.Stores.CashRegisters.Delete');
                        Route::put('/Restore', 'restore')->middleware('can:Stores,Dashboard.Stores.CashRegisters.Restore')->name('Dashboard.Stores.CashRegisters.Restore');
                    });
                });
            });
        });

        Route::prefix('/POS')->group(function () {
            Route::controller(POSController::class)->group(function () {
                Route::prefix('/Stores')->group(function () {
                    Route::get('/Report', 'report')->middleware('can:POS,Dashboard.POS.Stores.Report')->name('Dashboard.POS.Stores.Report');
                    Route::get('/Seller', 'seller')->middleware('can:POS,Dashboard.POS.Stores.Seller')->name('Dashboard.POS.Stores.Seller');
                });
                Route::prefix('/CashRegisters')->group(function () {
                    Route::get('/Check', 'check')->middleware('can:POS,Dashboard.POS.CashRegisters.Check')->name('Dashboard.POS.CashRegisters.Check');
                    Route::post('/Open', 'open')->middleware('can:POS,Dashboard.POS.CashRegisters.Open')->name('Dashboard.POS.CashRegisters.Open');
                    Route::put('/Close', 'close')->middleware('can:POS,Dashboard.POS.CashRegisters.Close')->name('Dashboard.POS.CashRegisters.Close');
                    Route::get('/Consolidated', 'consolidated')->middleware('can:POS,Dashboard.POS.CashRegisters.Consolidated')->name('Dashboard.POS.CashRegisters.Consolidated');
                });
                Route::get('/Index', 'index')->middleware('can:POS,Dashboard.POS.Index')->name('Dashboard.POS.Index');
                Route::post('/Person', 'person')->middleware('can:POS,Dashboard.POS.Person')->name('Dashboard.POS.Person');
                Route::post('/Product', 'product')->middleware('can:POS,Dashboard.POS.Product')->name('Dashboard.POS.Product');
            });
        });

        Route::prefix('/Invoices')->group(function () {
            Route::controller(InvoiceController::class)->group(function () {
                Route::get('/Index', 'index')->middleware('can:Invoices,Dashboard.Invoices.Index')->name('Dashboard.Invoices.Index');
                Route::post('/Index/Query', 'indexQuery')->middleware('can:Invoices,Dashboard.Invoices.Index.Query')->name('Dashboard.Invoices.Index.Query');
                Route::post('/Store', 'store')->middleware('can:Invoices,Dashboard.Invoices.Store')->name('Dashboard.Invoices.Store');
                Route::get('/Ticket/{id}', 'ticket')->middleware('can:Invoices,Dashboard.Invoices.Ticket')->name('Dashboard.Invoices.Ticket');
            });
        });

        Route::prefix('/Libranzas')->group(function () {
            Route::controller(LibranzaController::class)->group(function () {
                Route::get('/Index', 'index')->middleware('can:Libranzas,Dashboard.Libranzas.Index')->name('Dashboard.Libranzas.Index');
                Route::post('/Index/Query', 'indexQuery')->middleware('can:Libranzas,Dashboard.Libranzas.Index.Query')->name('Dashboard.Libranzas.Index.Query');
                Route::post('/Create', 'create')->middleware('can:Libranzas,Dashboard.Libranzas.Create')->name('Dashboard.Libranzas.Create');
                Route::post('/Store', 'store')->middleware('can:Libranzas,Dashboard.Libranzas.Store')->name('Dashboard.Libranzas.Store');
                Route::post('/Show/{id}', 'show')->middleware('can:Libranzas,Dashboard.Libranzas.Show')->name('Dashboard.Libranzas.Show');
                Route::put('/Approve', 'approve')->middleware('can:Libranzas,Dashboard.Libranzas.Approve')->name('Dashboard.Libranzas.Approve');
                Route::put('/Cancel', 'cancel')->middleware('can:Libranzas,Dashboard.Libranzas.Cancel')->name('Dashboard.Libranzas.Cancel');
                Route::put('/Discount', 'discount')->middleware('can:Libranzas,Dashboard.Libranzas.Discount')->name('Dashboard.Libranzas.Discount');
                Route::post('/Audit/{id}', 'audit')->middleware('can:Libranzas,Dashboard.Libranzas.Audit')->name('Dashboard.Libranzas.Audit');
                Route::get('/Download/{id}', 'download')->middleware('can:Libranzas,Dashboard.Libranzas.Download')->name('Dashboard.Libranzas.Download');
            });
        });

        Route::prefix('/Orders')->group(function () {
            Route::controller(OrderController::class)->group(function () {
                Route::get('/Index', 'index')->middleware('can:Orders,Dashboard.Orders.Index')->name('Dashboard.Orders.Index');
                Route::post('/Index/Query', 'indexQuery')->middleware('can:Orders,Dashboard.Orders.Index.Query')->name('Dashboard.Orders.Index.Query');
                Route::post('/Create', 'create')->middleware('can:Orders,Dashboard.Orders.Create')->name('Dashboard.Orders.Create');
                Route::post('/Store', 'store')->middleware('can:Orders,Dashboard.Orders.Store')->name('Dashboard.Orders.Store');
                Route::post('/Edit/{id}', 'edit')->middleware('can:Orders,Dashboard.Orders.Edit')->name('Dashboard.Orders.Edit');
                Route::put('/Update/{id}', 'update')->middleware('can:Orders,Dashboard.Orders.Update')->name('Dashboard.Orders.Update');
                Route::put('/Observation', 'observation')->middleware('can:Orders,Dashboard.Orders.Observation')->name('Dashboard.Orders.Observation');
                Route::put('/Cancel', 'cancel')->middleware('can:Orders,Dashboard.Orders.Cancel')->name('Dashboard.Orders.Cancel');
                Route::put('/Assent', 'assent')->middleware('can:Orders,Dashboard.Orders.Assent')->name('Dashboard.Orders.Assent');
                Route::put('/Pending', 'pending')->middleware('can:Orders,Dashboard.Orders.Pending')->name('Dashboard.Orders.Pending');
                Route::put('/Suspend', 'suspend')->middleware('can:Orders,Dashboard.Orders.Suspend')->name('Dashboard.Orders.Suspend');
                Route::put('/Delay', 'delay')->middleware('can:Orders,Dashboard.Orders.Delay')->name('Dashboard.Orders.Delay');
                Route::put('/Decline', 'decline')->middleware('can:Orders,Dashboard.Orders.Decline')->name('Dashboard.Orders.Decline');
                Route::put('/Authorize', 'authorized')->middleware('can:Orders,Dashboard.Orders.Authorize')->name('Dashboard.Orders.Authorize');
                Route::put('/Approve', 'approve')->middleware('can:Orders,Dashboard.Orders.Approve')->name('Dashboard.Orders.Approve');
                Route::put('/PartiallyApprove', 'partiallyApprove')->middleware('can:Orders,Dashboard.Orders.PartiallyApprove')->name('Dashboard.Orders.PartiallyApprove');
                Route::put('/Dispatch', 'despatch')->middleware('can:Orders,Dashboard.Orders.Dispatch')->name('Dashboard.Orders.Dispatch');
                Route::post('/Audit/{id}', 'audit')->middleware('can:Orders,Dashboard.Orders.Audit')->name('Dashboard.Orders.Audit');
                Route::post('/Wallet/{id}', 'wallet')->middleware('can:Orders,Dashboard.Orders.Wallet')->name('Dashboard.Orders.Wallet');
                Route::post('/Email/{id}', 'email')->middleware('can:Orders,Dashboard.Orders.Email')->name('Dashboard.Orders.Email');
                Route::get('/Download/{id}', 'download')->middleware('can:Orders,Dashboard.Orders.Download')->name('Dashboard.Orders.Download');
            });
            Route::prefix('/Details')->group(function () {
                Route::controller(OrderDetailController::class)->group(function () {
                    Route::get('/Index/{id}', 'index')->middleware('can:Orders,Dashboard.Orders.Details.Index')->name('Dashboard.Orders.Details.Index');
                    Route::post('/Index/Query', 'indexQuery')->middleware('can:Orders,Dashboard.Orders.Details.Index.Query')->name('Dashboard.Orders.Details.Index.Query');
                    Route::post('/Create', 'create')->middleware('can:Orders,Dashboard.Orders.Details.Create')->name('Dashboard.Orders.Details.Create');
                    Route::post('/Store', 'store')->middleware('can:Orders,Dashboard.Orders.Details.Store')->name('Dashboard.Orders.Details.Store');
                    Route::post('/Edit/{id}', 'edit')->middleware('can:Orders,Dashboard.Orders.Details.Edit')->name('Dashboard.Orders.Details.Edit');
                    Route::put('/Update/{id}', 'update')->middleware('can:Orders,Dashboard.Orders.Details.Update')->name('Dashboard.Orders.Details.Update');
                    Route::post('/Show/{id}', 'show')->middleware('can:Orders,Dashboard.Orders.Details.Show')->name('Dashboard.Orders.Details.Show');
                    Route::post('/Clone', 'clone')->middleware('can:Orders,Dashboard.Orders.Details.Clone')->name('Dashboard.Orders.Details.Clone');
                    Route::put('/Pending', 'pending')->middleware('can:Orders,Dashboard.Orders.Details.Pending')->name('Dashboard.Orders.Details.Pending');
                    Route::put('/Authorize', 'authorized')->middleware('can:Orders,Dashboard.Orders.Details.Authorize')->name('Dashboard.Orders.Details.Authorize');
                    Route::put('/Approve', 'approve')->middleware('can:Orders,Dashboard.Orders.Details.Approve')->name('Dashboard.Orders.Details.Approve');
                    Route::put('/Allow', 'allow')->middleware('can:Orders,Dashboard.Orders.Details.Allow')->name('Dashboard.Orders.Details.Allow');
                    Route::put('/Cancel', 'cancel')->middleware('can:Orders,Dashboard.Orders.Details.Cancel')->name('Dashboard.Orders.Details.Cancel');
                    Route::put('/Suspend', 'suspend')->middleware('can:Orders,Dashboard.Orders.Details.Suspend')->name('Dashboard.Orders.Details.Suspend');
                    Route::post('/Audit/{id}', 'audit')->middleware('can:Orders,Dashboard.Orders.Details.Audit')->name('Dashboard.Orders.Details.Audit');
                });
            });
        });

        Route::prefix('/Filters')->group(function () {
            Route::controller(FilterController::class)->group(function () {
                Route::get('/Index', 'index')->middleware('can:Filters,Dashboard.Filters.Index')->name('Dashboard.Filters.Index');
                Route::post('/Index/Query', 'indexQuery')->middleware('can:Filters,Dashboard.Filters.Index.Query')->name('Dashboard.Filters.Index.Query');
                Route::post('/Query', 'query')->middleware('can:Filters,Dashboard.Filters.Query')->name('Dashboard.Filters.Query');
                Route::post('/Grafic', 'grafic')->middleware('can:Filters,Dashboard.Filters.Grafic')->name('Dashboard.Filters.Grafic');
                Route::post('/Upload', 'upload')->middleware('can:Filters,Dashboard.Filters.Upload')->name('Dashboard.Filters.Upload');
                Route::post('/Save', 'save')->middleware('can:Filters,Dashboard.Filters.Save')->name('Dashboard.Filters.Save');
            });
        });

        Route::prefix('/Dispatches')->group(function () {
            Route::controller(OrderDispatchController::class)->group(function () {
                Route::get('/Index', 'index')->middleware('can:Dispatches,Dashboard.Dispatches.Index')->name('Dashboard.Dispatches.Index');
                Route::post('/Index/Query', 'indexQuery')->middleware('can:Dispatches,Dashboard.Dispatches.Index.Query')->name('Dashboard.Dispatches.Index.Query');
                Route::put('/Pending', 'pending')->middleware('can:Dispatches,Dashboard.Dispatches.Pending')->name('Dashboard.Dispatches.Pending');
                Route::put('/Approve', 'approve')->middleware('can:Dispatches,Dashboard.Dispatches.Approve')->name('Dashboard.Dispatches.Approve');
                Route::put('/Cancel', 'cancel')->middleware('can:Dispatches,Dashboard.Dispatches.Cancel')->name('Dashboard.Dispatches.Cancel');
                Route::put('/Picking', 'picking')->middleware('can:Dispatches,Dashboard.Dispatches.Picking')->name('Dashboard.Dispatches.Picking');
                Route::get('/Review/{id}', 'review')->middleware('can:Dispatches,Dashboard.Dispatches.Review')->name('Dashboard.Dispatches.Review');
                Route::put('/Packing', 'packing')->middleware('can:Dispatches,Dashboard.Dispatches.Packing')->name('Dashboard.Dispatches.Packing');
                Route::post('/Show/{id}', 'show')->middleware('can:Dispatches,Dashboard.Dispatches.Show')->name('Dashboard.Dispatches.Show');
                Route::post('/Invoice', 'invoice')->middleware('can:Dispatches,Dashboard.Dispatches.Invoice')->name('Dashboard.Dispatches.Invoice');
                Route::post('/Audit/{id}', 'audit')->middleware('can:Dispatches,Dashboard.Dispatches.Audit')->name('Dashboard.Dispatches.Audit');
                Route::get('/Print/{id}', 'print')->middleware('can:Dispatches,Dashboard.Dispatches.Print')->name('Dashboard.Dispatches.Print');
                Route::get('/Download/{id}', 'download')->middleware('can:Dispatches,Dashboard.Dispatches.Download')->name('Dashboard.Dispatches.Download');
            });
            Route::prefix('/Details')->group(function () {
                Route::controller(OrderDispatchDetailController::class)->group(function () {
                    Route::get('/Index/{id}', 'index')->middleware('can:Dispatches,Dashboard.Dispatches.Details.Index')->name('Dashboard.Dispatches.Details.Index');
                    Route::post('/Index/Query', 'indexQuery')->middleware('can:Dispatches,Dashboard.Dispatches.Details.Index.Query')->name('Dashboard.Dispatches.Details.Index.Query');
                    Route::put('/Pending', 'pending')->middleware('can:Dispatches,Dashboard.Dispatches.Details.Pending')->name('Dashboard.Dispatches.Details.Pending');
                    Route::put('/Cancel', 'cancel')->middleware('can:Dispatches,Dashboard.Dispatches.Details.Cancel')->name('Dashboard.Dispatches.Details.Cancel');
                    Route::post('/Audit/{id}', 'audit')->middleware('can:Dispatches,Dashboard.Dispatches.Details.Audit')->name('Dashboard.Dispatches.Details.Audit');
                });
            });
        });

        Route::prefix('/Pickings')->group(function () {
            Route::controller(OrderPickingController::class)->group(function () {
                Route::get('/Index/{id}', 'index')->middleware('can:Pickings,Dashboard.Pickings.Index')->name('Dashboard.Pickings.Index');
                Route::post('/Index/Query', 'indexQuery')->middleware('can:Pickings,Dashboard.Pickings.Index.Query')->name('Dashboard.Pickings.Index.Query');
                Route::put('/Approve', 'approve')->middleware('can:Pickings,Dashboard.Pickings.Approve')->name('Dashboard.Pickings.Approve');
                Route::put('/Review', 'review')->middleware('can:Pickings,Dashboard.Pickings.Review')->name('Dashboard.Pickings.Review');
                Route::put('/Cancel', 'cancel')->middleware('can:Pickings,Dashboard.Pickings.Cancel')->name('Dashboard.Pickings.Cancel');
                Route::post('/Audit/{id}', 'audit')->middleware('can:Pickings,Dashboard.Pickings.Audit')->name('Dashboard.Pickings.Audit');
            });
            Route::prefix('/Details')->group(function () {
                Route::controller(OrderPickingDetailController::class)->group(function () {
                    Route::put('/Add', 'add')->middleware('can:Pickings,Dashboard.Pickings.Details.Add')->name('Dashboard.Pickings.Details.Add');
                    Route::post('/Audit/{id}', 'audit')->middleware('can:Pickings,Dashboard.Pickings.Details.Audit')->name('Dashboard.Pickings.Details.Audit');
                });
            });
        });

        Route::prefix('/Packings')->group(function () {
            Route::controller(OrderPackingController::class)->group(function () {
                Route::get('/Index/{id}', 'index')->middleware('can:Packings,Dashboard.Packings.Index')->name('Dashboard.Packings.Index');
                Route::post('/Index/Query', 'indexQuery')->middleware('can:Packings,Dashboard.Packings.Index.Query')->name('Dashboard.Packings.Index.Query');
                Route::post('/Store', 'store')->middleware('can:Packings,Dashboard.Packings.Store')->name('Dashboard.Packings.Store');
                Route::put('/Open', 'open')->middleware('can:Packings,Dashboard.Packings.Open')->name('Dashboard.Packings.Open');
                Route::put('/Close', 'close')->middleware('can:Packings,Dashboard.Packings.Close')->name('Dashboard.Packings.Close');
                Route::post('/Audit/{id}', 'audit')->middleware('can:Packings,Dashboard.Packings.Audit')->name('Dashboard.Packings.Audit');
            });
            Route::prefix('/Details')->group(function () {
                Route::controller(OrderPackingDetailController::class)->group(function () {
                    Route::put('/Add', 'add')->middleware('can:Packings,Dashboard.Packings.Details.Add')->name('Dashboard.Packings.Details.Add');
                    Route::post('/Audit/{id}', 'audit')->middleware('can:Packings,Dashboard.Packings.Details.Audit')->name('Dashboard.Packings.Details.Audit');
                });
            });
        });

        Route::prefix('/Reports')->group(function () {
            Route::controller(ReportSalesController::class)->group(function () {
                Route::prefix('/Sales')->group(function () {
                    Route::get('/Index', 'index')->middleware('can:ReportsSales,Dashboard.Reports.Sales.Index')->name('Dashboard.Reports.Sales.Index');
                    Route::post('/Index/Query', 'indexQuery')->middleware('can:ReportsSales,Dashboard.Reports.Sales.Index.Query')->name('Dashboard.Reports.Sales.Index.Query');
                });
            });
            Route::controller(ReportDispatchesController::class)->group(function () {
                Route::prefix('/Dispatches')->group(function () {
                    Route::get('/Index', 'index')->middleware('can:ReportsDispatches,Dashboard.Reports.Dispatches.Index')->name('Dashboard.Reports.Dispatches.Index');
                    Route::post('/Index/Query', 'indexQuery')->middleware('can:ReportsDispatches,Dashboard.Reports.Dispatches.Index.Query')->name('Dashboard.Reports.Dispatches.Index.Query');
                });
            });
            Route::controller(ReportProductionsController::class)->group(function () {
                Route::prefix('/Productions')->group(function () {
                    Route::get('/Index', 'index')->middleware('can:ReportsProductions,Dashboard.Reports.Productions.Index')->name('Dashboard.Reports.Productions.Index');
                    Route::post('/Index/Query', 'indexQuery')->middleware('can:ReportsProductions,Dashboard.Reports.Productions.Index.Query')->name('Dashboard.Reports.Productions.Index.Query');
                });
            });
            Route::controller(ReportInvoicesController::class)->group(function () {
                Route::prefix('/Invoices')->group(function () {
                    Route::get('/Index', 'index')->middleware('can:ReportsInvoices,Dashboard.Reports.Invoices.Index')->name('Dashboard.Reports.Invoices.Index');
                    Route::post('/Index/Query', 'indexQuery')->middleware('can:ReportsInvoices,Dashboard.Reports.Invoices.Index.Query')->name('Dashboard.Reports.Invoices.Index.Query');
                });
            });
        });

    });

});
