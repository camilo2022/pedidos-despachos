<?php

use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\BusinessController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\CorreriaController;
use App\Http\Controllers\ColorController;
use App\Http\Controllers\FilterController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\ModulesAndSubmodulesController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\OrderDetailController;
use App\Http\Controllers\OrderDispatchController;
use App\Http\Controllers\OrderDispatchDetailController;
use App\Http\Controllers\OrderPackingController;
use App\Http\Controllers\OrderPackingDetailController;
use App\Http\Controllers\OrderPickingController;
use App\Http\Controllers\OrderPickingDetailController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\PublicController;
use App\Http\Controllers\ReportDispatchesController;
use App\Http\Controllers\ReportProductionsController;
use App\Http\Controllers\ReportProductsController;
use App\Http\Controllers\ReportSalesController;
use App\Http\Controllers\ReportTrademarksController;
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

Route::get('/phpinfo', function () { 
    echo phpinfo();
} );

Route::get('/', function () {

    if (Auth::check()) {
        return redirect('/Dashboard');
    } else {
        return redirect('/Catalogo');
    }

});

Route::get('reset-password/{id}/{token}', [ResetPasswordController::class, 'showResetForm']);

Auth::routes(['register' => false]);

Route::controller(PublicController::class)->group(function () {
    Route::get('/Packages/Detail/{token}', 'packageDetail')->name('Public.Packages.Detail');
    Route::get('/Catalogo', 'catalogo')->name('Public.Catalogo.Index');
    Route::get('/Catalogo/{referecia}', 'referencia')->name('Public.Catalogo.Referencia');
});

Route::middleware(['auth'])->group(function () {

    Route::prefix('/Dashboard')->group(function () {

        Route::controller(HomeController::class)->group(function () {
            Route::get('/', 'index')->middleware('can:Dashboard')->name('Dashboard');
            Route::post('/Chart/Correria', 'chartCorreria')->middleware('can:Dashboard.Chart.Correria')->name('Dashboard.Chart.Correria');
        });

        Route::prefix('/Users')->group(function () {
            Route::controller(UserController::class)->group(function () {
                Route::get('/Index', 'index')->middleware('can:Dashboard.Users.Index')->name('Dashboard.Users.Index');
                Route::post('/Index/Query', 'indexQuery')->middleware('can:Dashboard.Users.Index.Query')->name('Dashboard.Users.Index.Query');
                Route::post('/Create', 'create')->middleware('can:Dashboard.Users.Create')->name('Dashboard.Users.Create');
                Route::post('/Store', 'store')->middleware('can:Dashboard.Users.Store')->name('Dashboard.Users.Store');
                Route::post('/Edit/{id}', 'edit')->middleware('can:Dashboard.Users.Edit')->name('Dashboard.Users.Edit');
                Route::put('/Update/{id}', 'update')->middleware('can:Dashboard.Users.Update')->name('Dashboard.Users.Update');
                Route::post('/Show/{id}', 'show')->middleware('can:Dashboard.Users.Show')->name('Dashboard.Users.Show');
                Route::put('/Password/{id}', 'password')->middleware('can:Dashboard.Users.Password')->name('Dashboard.Users.Password');
                Route::delete('/Delete', 'delete')->middleware('can:Dashboard.Users.Delete')->name('Dashboard.Users.Delete');
                Route::put('/Restore', 'restore')->middleware('can:Dashboard.Users.Restore')->name('Dashboard.Users.Restore');
                Route::post('/AssignRoleAndPermissions', 'assignRoleAndPermissions')->middleware('can:Dashboard.Users.AssignRoleAndPermissions')->name('Dashboard.Users.AssignRoleAndPermissions');
                Route::post('/AssignRoleAndPermissions/Query', 'assignRoleAndPermissionsQuery')->middleware('can:Dashboard.Users.AssignRoleAndPermissions.Query')->name('Dashboard.Users.AssignRoleAndPermissions.Query');
                Route::post('/RemoveRoleAndPermissions', 'removeRoleAndPermissions')->middleware('can:Dashboard.Users.RemoveRoleAndPermissions')->name('Dashboard.Users.RemoveRoleAndPermissions');
                Route::post('/RemoveRoleAndPermissions/Query', 'removeRoleAndPermissionsQuery')->middleware('can:Dashboard.Users.RemoveRoleAndPermissions.Query')->name('Dashboard.Users.RemoveRoleAndPermissions.Query');
                Route::post('/Warehouses/{id}', 'warehouses')->middleware('can:Dashboard.Users.Warehouses')->name('Dashboard.Users.Warehouses');
                Route::post('/AssignWarehouses', 'assignWarehouses')->middleware('can:Dashboard.Users.AssignWarehouses')->name('Dashboard.Users.AssignWarehouses');
                Route::delete('/RemoveWarehouses', 'removeWarehouses')->middleware('can:Dashboard.Users.RemoveWarehouses')->name('Dashboard.Users.RemoveWarehouses');
            });
        });

        Route::prefix('/RolesAndPermissions')->group(function () {
            Route::controller(RolesAndPermissionsController::class)->group(function () {
                Route::get('/Index', 'index')->middleware('can:Dashboard.RolesAndPermissions.Index')->name('Dashboard.RolesAndPermissions.Index');
                Route::post('/Index/Query', 'indexQuery')->middleware('can:Dashboard.RolesAndPermissions.Index.Query')->name('Dashboard.RolesAndPermissions.Index.Query');
                Route::post('/Create', 'create')->middleware('can:Dashboard.RolesAndPermissions.Create')->name('Dashboard.RolesAndPermissions.Create');
                Route::post('/Store', 'store')->middleware('can:Dashboard.RolesAndPermissions.Store')->name('Dashboard.RolesAndPermissions.Store');
                Route::post('/Edit/{id}', 'edit')->middleware('can:Dashboard.RolesAndPermissions.Edit')->name('Dashboard.RolesAndPermissions.Edit');
                Route::put('/Update/{id}', 'update')->middleware('can:Dashboard.RolesAndPermissions.Update')->name('Dashboard.RolesAndPermissions.Update');
                Route::delete('/Delete', 'delete')->middleware('can:Dashboard.RolesAndPermissions.Delete')->name('Dashboard.RolesAndPermissions.Delete');
            });
        });

        Route::prefix('/ModulesAndSubmodules')->group(function () {
            Route::controller(ModulesAndSubmodulesController::class)->group(function () {
                Route::get('/Index', 'index')->middleware('can:Dashboard.ModulesAndSubmodules.Index')->name('Dashboard.ModulesAndSubmodules.Index');
                Route::post('/Index/Query', 'indexQuery')->middleware('can:Dashboard.ModulesAndSubmodules.Index.Query')->name('Dashboard.ModulesAndSubmodules.Index.Query');
                Route::post('/Create', 'create')->middleware('can:Dashboard.ModulesAndSubmodules.Create')->name('Dashboard.ModulesAndSubmodules.Create');
                Route::post('/Store', 'store')->middleware('can:Dashboard.ModulesAndSubmodules.Store')->name('Dashboard.ModulesAndSubmodules.Store');
                Route::post('/Edit/{id}', 'edit')->middleware('can:Dashboard.ModulesAndSubmodules.Edit')->name('Dashboard.ModulesAndSubmodules.Edit');
                Route::put('/Update/{id}', 'update')->middleware('can:Dashboard.ModulesAndSubmodules.Update')->name('Dashboard.ModulesAndSubmodules.Update');
                Route::delete('/Delete', 'delete')->middleware('can:Dashboard.ModulesAndSubmodules.Delete')->name('Dashboard.ModulesAndSubmodules.Delete');
            });
        });

        Route::prefix('/Businesses')->group(function () {
            Route::controller(BusinessController::class)->group(function () {
                Route::get('/Index', 'index')->middleware('can:Dashboard.Businesses.Index')->name('Dashboard.Businesses.Index');
                Route::post('/Index/Query', 'indexQuery')->middleware('can:Dashboard.Businesses.Index.Query')->name('Dashboard.Businesses.Index.Query');
                Route::post('/Create', 'create')->middleware('can:Dashboard.Businesses.Create')->name('Dashboard.Businesses.Create');
                Route::post('/Store', 'store')->middleware('can:Dashboard.Businesses.Store')->name('Dashboard.Businesses.Store');
                Route::post('/Edit/{id}', 'edit')->middleware('can:Dashboard.Businesses.Edit')->name('Dashboard.Businesses.Edit');
                Route::put('/Update/{id}', 'update')->middleware('can:Dashboard.Businesses.Update')->name('Dashboard.Businesses.Update');
                Route::delete('/Delete', 'delete')->middleware('can:Dashboard.Businesses.Delete')->name('Dashboard.Businesses.Delete');
                Route::put('/Restore', 'restore')->middleware('can:Dashboard.Businesses.Restore')->name('Dashboard.Businesses.Restore');
                Route::post('/Warehouses/{id}', 'warehouses')->middleware('can:Dashboard.Businesses.Warehouses')->name('Dashboard.Businesses.Warehouses');
                Route::post('/AssignWarehouses', 'assignWarehouses')->middleware('can:Dashboard.Businesses.AssignWarehouses')->name('Dashboard.Businesses.AssignWarehouses');
                Route::delete('/RemoveWarehouses', 'removeWarehouses')->middleware('can:Dashboard.Businesses.RemoveWarehouses')->name('Dashboard.Businesses.RemoveWarehouses');
            });
        });

        Route::prefix('/Correrias')->group(function () {
            Route::controller(CorreriaController::class)->group(function () {
                Route::get('/Index', 'index')->middleware('can:Dashboard.Correrias.Index')->name('Dashboard.Correrias.Index');
                Route::post('/Index/Query', 'indexQuery')->middleware('can:Dashboard.Correrias.Index.Query')->name('Dashboard.Correrias.Index.Query');
                Route::post('/Create', 'create')->middleware('can:Dashboard.Correrias.Create')->name('Dashboard.Correrias.Create');
                Route::post('/Store', 'store')->middleware('can:Dashboard.Correrias.Store')->name('Dashboard.Correrias.Store');
                Route::post('/Edit/{id}', 'edit')->middleware('can:Dashboard.Correrias.Edit')->name('Dashboard.Correrias.Edit');
                Route::put('/Update/{id}', 'update')->middleware('can:Dashboard.Correrias.Update')->name('Dashboard.Correrias.Update');
                Route::delete('/Delete', 'delete')->middleware('can:Dashboard.Correrias.Delete')->name('Dashboard.Correrias.Delete');
            });
        });

        Route::prefix('/Warehouses')->group(function () {
            Route::controller(WarehouseController::class)->group(function () {
                Route::get('/Index', 'index')->middleware('can:Dashboard.Warehouses.Index')->name('Dashboard.Warehouses.Index');
                Route::post('/Index/Query', 'indexQuery')->middleware('can:Dashboard.Warehouses.Index.Query')->name('Dashboard.Warehouses.Index.Query');
                Route::post('/Create', 'create')->middleware('can:Dashboard.Warehouses.Create')->name('Dashboard.Warehouses.Create');
                Route::post('/Store', 'store')->middleware('can:Dashboard.Warehouses.Store')->name('Dashboard.Warehouses.Store');
                Route::post('/Edit/{id}', 'edit')->middleware('can:Dashboard.Warehouses.Edit')->name('Dashboard.Warehouses.Edit');
                Route::put('/Update/{id}', 'update')->middleware('can:Dashboard.Warehouses.Update')->name('Dashboard.Warehouses.Update');
                Route::post('/Show/{id}', 'show')->middleware('can:Dashboard.Warehouses.Show')->name('Dashboard.Warehouses.Show');
                Route::delete('/Delete', 'delete')->middleware('can:Dashboard.Warehouses.Delete')->name('Dashboard.Warehouses.Delete');
                Route::put('/Restore', 'restore')->middleware('can:Dashboard.Warehouses.Restore')->name('Dashboard.Warehouses.Restore');
                Route::post('/SyncSiesa', 'syncSiesa')->middleware('can:Dashboard.Warehouses.SyncSiesa')->name('Dashboard.Warehouses.SyncSiesa');
                Route::post('/SyncTns', 'syncTns')->middleware('can:Dashboard.Warehouses.SyncTns')->name('Dashboard.Warehouses.SyncTns');
            });
        });

        Route::prefix('/Colors')->group(function () {
            Route::controller(ColorController::class)->group(function () {
                Route::get('/Index', 'index')->middleware('can:Dashboard.Colors.Index')->name('Dashboard.Colors.Index');
                Route::post('/Index/Query', 'indexQuery')->middleware('can:Dashboard.Colors.Index.Query')->name('Dashboard.Colors.Index.Query');
                Route::post('/Create', 'create')->middleware('can:Dashboard.Colors.Create')->name('Dashboard.Colors.Create');
                Route::post('/Store', 'store')->middleware('can:Dashboard.Colors.Store')->name('Dashboard.Colors.Store');
                Route::post('/Edit/{id}', 'edit')->middleware('can:Dashboard.Colors.Edit')->name('Dashboard.Colors.Edit');
                Route::put('/Update/{id}', 'update')->middleware('can:Dashboard.Colors.Update')->name('Dashboard.Colors.Update');
                Route::delete('/Delete', 'delete')->middleware('can:Dashboard.Colors.Delete')->name('Dashboard.Colors.Delete');
                Route::put('/Restore', 'restore')->middleware('can:Dashboard.Colors.Restore')->name('Dashboard.Colors.Restore');
                Route::post('/SyncSiesa', 'syncSiesa')->middleware('can:Dashboard.Colors.SyncSiesa')->name('Dashboard.Colors.SyncSiesa');
                Route::post('/SyncTns', 'syncTns')->middleware('can:Dashboard.Colors.SyncTns')->name('Dashboard.Colors.SyncTns');
            });
        });

        Route::prefix('/Products')->group(function () {
            Route::controller(ProductController::class)->group(function () {
                Route::get('/Index', 'index')->middleware('can:Dashboard.Products.Index')->name('Dashboard.Products.Index');
                Route::post('/Index/Query', 'indexQuery')->middleware('can:Dashboard.Products.Index.Query')->name('Dashboard.Products.Index.Query');
                Route::post('/Show/{id}', 'show')->middleware('can:Dashboard.Products.Show')->name('Dashboard.Products.Show');
                Route::post('/Charge', 'charge')->middleware('can:Dashboard.Products.Charge')->name('Dashboard.Products.Charge');
                Route::delete('/Destroy', 'destroy')->middleware('can:Dashboard.Products.Destroy')->name('Dashboard.Products.Destroy');
                Route::post('/Download', 'download')->middleware('can:Dashboard.Products.Download')->name('Dashboard.Products.Download');
                Route::post('/SyncSiesa', 'syncSiesa')->middleware('can:Dashboard.Products.SyncSiesa')->name('Dashboard.Products.SyncSiesa');
                Route::post('/SyncTns', 'syncTns')->middleware('can:Dashboard.Products.SyncTns')->name('Dashboard.Products.SyncTns');
                Route::get('/Sync', 'sync')->name('Dashboard.Products.Sync');
            });
        });

        Route::prefix('/Inventories')->group(function () {
            Route::controller(InventoryController::class)->group(function () {
                Route::get('/Index', 'index')->middleware('can:Dashboard.Inventories.Index')->name('Dashboard.Inventories.Index');
                Route::post('/Index/Query', 'indexQuery')->middleware('can:Dashboard.Inventories.Index.Query')->name('Dashboard.Inventories.Index.Query');
                Route::post('/Upload/Query', 'uploadQuery')->middleware('can:Dashboard.Inventories.Upload.Query')->name('Dashboard.Inventories.Upload.Query');
                Route::post('/Upload', 'upload')->middleware('can:Dashboard.Inventories.Upload')->name('Dashboard.Inventories.Upload');
                Route::post('/Download', 'download')->middleware('can:Dashboard.Inventories.Download')->name('Dashboard.Inventories.Download');
                Route::post('/SyncSiesa', 'syncSiesa')->middleware('can:Dashboard.Inventories.SyncSiesa')->name('Dashboard.Inventories.SyncSiesa');
                Route::post('/SyncTns', 'syncTns')->middleware('can:Dashboard.Inventories.SyncTns')->name('Dashboard.Inventories.SyncTns');
                Route::post('/SyncBmi/Query', 'syncBmiQuery')->middleware('can:Dashboard.Inventories.SyncBmi.Query')->name('Dashboard.Inventories.SyncBmi.Query');
                Route::post('/SyncBmi', 'syncBmi')->middleware('can:Dashboard.Inventories.SyncBmi')->name('Dashboard.Inventories.SyncBmi');
            });
        });

        Route::prefix('/Clients')->group(function () {
            Route::controller(ClientController::class)->group(function () {
                Route::get('/Index', 'index')->middleware('can:Dashboard.Clients.Index')->name('Dashboard.Clients.Index');
                Route::post('/Index/Query', 'indexQuery')->middleware('can:Dashboard.Clients.Index.Query')->name('Dashboard.Clients.Index.Query');
                Route::post('/Create', 'create')->middleware('can:Dashboard.Clients.Create')->name('Dashboard.Clients.Create');
                Route::post('/Store', 'store')->middleware('can:Dashboard.Clients.Store')->name('Dashboard.Clients.Store');
                Route::post('/Edit/{id}', 'edit')->middleware('can:Dashboard.Clients.Edit')->name('Dashboard.Clients.Edit');
                Route::put('/Update/{id}', 'update')->middleware('can:Dashboard.Clients.Update')->name('Dashboard.Clients.Update');
                Route::post('/Show/{id}', 'show')->middleware('can:Dashboard.Clients.Show')->name('Dashboard.Clients.Show');
                Route::post('/Wallet', 'wallet')->middleware('can:Dashboard.Clients.Wallet')->name('Dashboard.Clients.Wallet');
                Route::post('/Data', 'data')->middleware('can:Dashboard.Clients.Data')->name('Dashboard.Clients.Data');
                Route::delete('/Remove', 'remove')->middleware('can:Dashboard.Clients.Remove')->name('Dashboard.Clients.Remove');
                Route::delete('/Destroy', 'destroy')->middleware('can:Dashboard.Clients.Destroy')->name('Dashboard.Clients.Destroy');
                Route::delete('/Delete', 'delete')->middleware('can:Dashboard.Clients.Delete')->name('Dashboard.Clients.Delete');
                Route::put('/Restore', 'restore')->middleware('can:Dashboard.Clients.Restore')->name('Dashboard.Clients.Restore');
                Route::post('/Upload/Query', 'uploadQuery')->middleware('can:Dashboard.Clients.Upload.Query')->name('Dashboard.Clients.Upload.Query');
                Route::post('/Upload', 'upload')->middleware('can:Dashboard.Clients.Upload')->name('Dashboard.Clients.Upload');
                Route::post('/Download', 'download')->middleware('can:Dashboard.Clients.Download')->name('Dashboard.Clients.Download');
                Route::post('/SyncSiesa', 'syncSiesa')->middleware('can:Dashboard.Clients.SyncSiesa')->name('Dashboard.Clients.SyncSiesa');
                Route::post('/SyncTns', 'syncTns')->middleware('can:Dashboard.Clients.SyncTns')->name('Dashboard.Clients.SyncTns');
            });
        });

        Route::prefix('/Orders')->group(function () {
            Route::controller(OrderController::class)->group(function () {
                Route::get('/Index', 'index')->middleware('can:Dashboard.Orders.Index')->name('Dashboard.Orders.Index');
                Route::post('/Index/Query', 'indexQuery')->middleware('can:Dashboard.Orders.Index.Query')->name('Dashboard.Orders.Index.Query');
                Route::post('/Create', 'create')->middleware('can:Dashboard.Orders.Create')->name('Dashboard.Orders.Create');
                Route::post('/Store', 'store')->middleware('can:Dashboard.Orders.Store')->name('Dashboard.Orders.Store');
                Route::post('/Edit/{id}', 'edit')->middleware('can:Dashboard.Orders.Edit')->name('Dashboard.Orders.Edit');
                Route::put('/Update/{id}', 'update')->middleware('can:Dashboard.Orders.Update')->name('Dashboard.Orders.Update');
                Route::put('/Observation', 'observation')->middleware('can:Dashboard.Orders.Observation')->name('Dashboard.Orders.Observation');
                Route::put('/Cancel', 'cancel')->middleware('can:Dashboard.Orders.Cancel')->name('Dashboard.Orders.Cancel');
                Route::put('/Assent', 'assent')->middleware('can:Dashboard.Orders.Assent')->name('Dashboard.Orders.Assent');
                Route::put('/Pending', 'pending')->middleware('can:Dashboard.Orders.Pending')->name('Dashboard.Orders.Pending');
                Route::put('/Suspend', 'suspend')->middleware('can:Dashboard.Orders.Suspend')->name('Dashboard.Orders.Suspend');
                Route::put('/Delay', 'delay')->middleware('can:Dashboard.Orders.Delay')->name('Dashboard.Orders.Delay');
                Route::put('/Decline', 'decline')->middleware('can:Dashboard.Orders.Decline')->name('Dashboard.Orders.Decline');
                Route::put('/Authorize', 'authorized')->middleware('can:Dashboard.Orders.Authorize')->name('Dashboard.Orders.Authorize');
                Route::put('/Approve', 'approve')->middleware('can:Dashboard.Orders.Approve')->name('Dashboard.Orders.Approve');
                Route::put('/PartiallyApprove', 'partiallyApprove')->middleware('can:Dashboard.Orders.PartiallyApprove')->name('Dashboard.Orders.PartiallyApprove');
                Route::put('/Dispatch', 'despatch')->middleware('can:Dashboard.Orders.Dispatch')->name('Dashboard.Orders.Dispatch');
                Route::post('/Wallet/{id}', 'wallet')->middleware('can:Dashboard.Orders.Wallet')->name('Dashboard.Orders.Wallet');
                Route::post('/Email/{id}', 'email')->middleware('can:Dashboard.Orders.Email')->name('Dashboard.Orders.Email');
                Route::get('/Download/{id}', 'download')->middleware('can:Dashboard.Orders.Download')->name('Dashboard.Orders.Download');
            });
            Route::prefix('/Details')->group(function () {
                Route::controller(OrderDetailController::class)->group(function () {
                    Route::get('/Index/{id}', 'index')->middleware('can:Dashboard.Orders.Details.Index')->name('Dashboard.Orders.Details.Index');
                    Route::post('/Index/Query', 'indexQuery')->middleware('can:Dashboard.Orders.Details.Index.Query')->name('Dashboard.Orders.Details.Index.Query');
                    Route::post('/Create', 'create')->middleware('can:Dashboard.Orders.Details.Create')->name('Dashboard.Orders.Details.Create');
                    Route::post('/Store', 'store')->middleware('can:Dashboard.Orders.Details.Store')->name('Dashboard.Orders.Details.Store');
                    Route::post('/Edit/{id}', 'edit')->middleware('can:Dashboard.Orders.Details.Edit')->name('Dashboard.Orders.Details.Edit');
                    Route::put('/Update/{id}', 'update')->middleware('can:Dashboard.Orders.Details.Update')->name('Dashboard.Orders.Details.Update');
                    Route::put('/Pending', 'pending')->middleware('can:Dashboard.Orders.Details.Pending')->name('Dashboard.Orders.Details.Pending');
                    Route::put('/Authorize', 'authorized')->middleware('can:Dashboard.Orders.Details.Authorize')->name('Dashboard.Orders.Details.Authorize');
                    Route::put('/Approve', 'approve')->middleware('can:Dashboard.Orders.Details.Approve')->name('Dashboard.Orders.Details.Approve');
                    Route::put('/Cancel', 'cancel')->middleware('can:Dashboard.Orders.Details.Cancel')->name('Dashboard.Orders.Details.Cancel');
                    Route::put('/Suspend', 'suspend')->middleware('can:Dashboard.Orders.Details.Suspend')->name('Dashboard.Orders.Details.Suspend');
                });
            });
        });

        Route::prefix('/Filters')->group(function () {
            Route::controller(FilterController::class)->group(function () {
                Route::get('/Index', 'index')->middleware('can:Dashboard.Filters.Index')->name('Dashboard.Filters.Index');
                Route::post('/Index/Query', 'indexQuery')->middleware('can:Dashboard.Filters.Index.Query')->name('Dashboard.Filters.Index.Query');
                Route::post('/Query', 'query')->middleware('can:Dashboard.Filters.Query')->name('Dashboard.Filters.Query');
                Route::post('/Grafic', 'grafic')->middleware('can:Dashboard.Filters.Grafic')->name('Dashboard.Filters.Grafic');
                Route::post('/Upload', 'upload')->middleware('can:Dashboard.Filters.Upload')->name('Dashboard.Filters.Upload');
                Route::post('/Save', 'save')->middleware('can:Dashboard.Filters.Save')->name('Dashboard.Filters.Save');
            });
        });

        Route::prefix('/Dispatches')->group(function () {
            Route::controller(OrderDispatchController::class)->group(function () {
                Route::get('/Index', 'index')->middleware('can:Dashboard.Dispatches.Index')->name('Dashboard.Dispatches.Index');
                Route::post('/Index/Query', 'indexQuery')->middleware('can:Dashboard.Dispatches.Index.Query')->name('Dashboard.Dispatches.Index.Query');
                Route::put('/Pending', 'pending')->middleware('can:Dashboard.Dispatches.Pending')->name('Dashboard.Dispatches.Pending');
                Route::put('/Approve', 'approve')->middleware('can:Dashboard.Dispatches.Approve')->name('Dashboard.Dispatches.Approve');
                Route::put('/Cancel', 'cancel')->middleware('can:Dashboard.Dispatches.Cancel')->name('Dashboard.Dispatches.Cancel');
                Route::put('/Picking', 'picking')->middleware('can:Dashboard.Dispatches.Picking')->name('Dashboard.Dispatches.Picking');
                Route::get('/Review/{id}', 'review')->middleware('can:Dashboard.Dispatches.Review')->name('Dashboard.Dispatches.Review');
                Route::put('/Packing', 'packing')->middleware('can:Dashboard.Dispatches.Packing')->name('Dashboard.Dispatches.Packing');
                Route::post('/Show/{id}', 'show')->middleware('can:Dashboard.Dispatches.Show')->name('Dashboard.Dispatches.Show');
                Route::post('/Invoice', 'invoice')->middleware('can:Dashboard.Dispatches.Invoice')->name('Dashboard.Dispatches.Invoice');
                Route::get('/Print/{id}', 'print')->middleware('can:Dashboard.Dispatches.Print')->name('Dashboard.Dispatches.Print');
                Route::get('/Download/{id}', 'download')->middleware('can:Dashboard.Dispatches.Download')->name('Dashboard.Dispatches.Download');
            });
            Route::prefix('/Details')->group(function () {
                Route::controller(OrderDispatchDetailController::class)->group(function () {
                    Route::get('/Index/{id}', 'index')->middleware('can:Dashboard.Dispatches.Details.Index')->name('Dashboard.Dispatches.Details.Index');
                    Route::post('/Index/Query', 'indexQuery')->middleware('can:Dashboard.Dispatches.Details.Index.Query')->name('Dashboard.Dispatches.Details.Index.Query');
                    Route::put('/Pending', 'pending')->middleware('can:Dashboard.Dispatches.Details.Pending')->name('Dashboard.Dispatches.Details.Pending');
                    Route::put('/Cancel', 'cancel')->middleware('can:Dashboard.Dispatches.Details.Cancel')->name('Dashboard.Dispatches.Details.Cancel');
                });
            });
        });

        Route::prefix('/Pickings')->group(function () {
            Route::controller(OrderPickingController::class)->group(function () {
                Route::get('/Index/{id}', 'index')->middleware('can:Dashboard.Pickings.Index')->name('Dashboard.Pickings.Index');
                Route::post('/Index/Query', 'indexQuery')->middleware('can:Dashboard.Pickings.Index.Query')->name('Dashboard.Pickings.Index.Query');
                Route::put('/Approve', 'approve')->middleware('can:Dashboard.Pickings.Approve')->name('Dashboard.Pickings.Approve');
                Route::put('/Review', 'review')->middleware('can:Dashboard.Pickings.Review')->name('Dashboard.Pickings.Review');
                Route::put('/Cancel', 'cancel')->middleware('can:Dashboard.Pickings.Cancel')->name('Dashboard.Pickings.Cancel');
            });
            Route::prefix('/Details')->group(function () {
                Route::controller(OrderPickingDetailController::class)->group(function () {
                    Route::put('/Add', 'add')->middleware('can:Dashboard.Pickings.Details.Add')->name('Dashboard.Pickings.Details.Add');
                });
            });
        });

        Route::prefix('/Packings')->group(function () {
            Route::controller(OrderPackingController::class)->group(function () {
                Route::get('/Index/{id}', 'index')->middleware('can:Dashboard.Packings.Index')->name('Dashboard.Packings.Index');
                Route::post('/Index/Query', 'indexQuery')->middleware('can:Dashboard.Packings.Index.Query')->name('Dashboard.Packings.Index.Query');
                Route::post('/Store', 'store')->middleware('can:Dashboard.Packings.Store')->name('Dashboard.Packings.Store');
                Route::put('/Open', 'open')->middleware('can:Dashboard.Packings.Open')->name('Dashboard.Packings.Open');
                Route::put('/Close', 'close')->middleware('can:Dashboard.Packings.Close')->name('Dashboard.Packings.Close');
            });
            Route::prefix('/Details')->group(function () {
                Route::controller(OrderPackingDetailController::class)->group(function () {
                    Route::put('/Add', 'add')->middleware('can:Dashboard.Packings.Details.Add')->name('Dashboard.Packings.Details.Add');
                });
            });
        });


        Route::prefix('/Reports')->group(function () {
            Route::controller(ReportSalesController::class)->group(function () {
                Route::prefix('/Sales')->group(function () {
                    Route::get('/Index', 'index')->middleware('can:Dashboard.Reports.Sales.Index')->name('Dashboard.Reports.Sales.Index');
                    Route::post('/Index/Query', 'indexQuery')->middleware('can:Dashboard.Reports.Sales.Index.Query')->name('Dashboard.Reports.Sales.Index.Query');
                });
            });
            Route::controller(ReportDispatchesController::class)->group(function () {
                Route::prefix('/Dispatches')->group(function () {
                    Route::get('/Index', 'index')->middleware('can:Dashboard.Reports.Dispatches.Index')->name('Dashboard.Reports.Dispatches.Index');
                    Route::post('/Index/Query', 'indexQuery')->middleware('can:Dashboard.Reports.Dispatches.Index.Query')->name('Dashboard.Reports.Dispatches.Index.Query');
                });
            });
            Route::controller(ReportProductionsController::class)->group(function () {
                Route::prefix('/Productions')->group(function () {
                    Route::get('/Index', 'index')->middleware('can:Dashboard.Reports.Productions.Index')->name('Dashboard.Reports.Productions.Index');
                    Route::post('/Index/Query', 'indexQuery')->middleware('can:Dashboard.Reports.Productions.Index.Query')->name('Dashboard.Reports.Productions.Index.Query');
                });
            });
            Route::controller(ReportTrademarksController::class)->group(function () {
                Route::prefix('/Trademarks')->group(function () {
                    Route::get('/Index', 'index')->middleware('can:Dashboard.Reports.Trademarks.Index')->name('Dashboard.Reports.Trademarks.Index');
                    Route::post('/Index/Query', 'indexQuery')->middleware('can:Dashboard.Reports.Trademarks.Index.Query')->name('Dashboard.Reports.Trademarks.Index.Query');
                    Route::get('/Download', 'download')->middleware('can:Dashboard.Reports.Trademarks.Download')->name('Dashboard.Reports.Trademarks.Download');
                });
            });
            Route::controller(ReportProductsController::class)->group(function () {
                Route::prefix('/Products')->group(function () {
                    Route::get('/Index', 'index')->middleware('can:Dashboard.Reports.Products.Index')->name('Dashboard.Reports.Products.Index');
                    Route::post('/Index/Query', 'indexQuery')->middleware('can:Dashboard.Reports.Products.Index.Query')->name('Dashboard.Reports.Products.Index.Query');
                    Route::get('/Download', 'download')->middleware('can:Dashboard.Reports.Products.Download')->name('Dashboard.Reports.Products.Download');
                });
            });
        });

    });

});
