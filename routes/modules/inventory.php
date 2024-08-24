<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\BrandController;
use App\Http\Controllers\UnitsController;
use App\Http\Controllers\CityController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\WarehouseController;
use App\Http\Controllers\StockController;
use App\Http\Controllers\TransferController;
use App\Http\Controllers\DeliveryOrderController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\DirectTransferController;
use App\Http\Controllers\TransferOrderController;
use App\Http\Controllers\ToolsController;

Route::get('/my/projects', [HomeController::class, 'myprojects'])->name('myprojects');
Route::post('/my/projects', [HomeController::class, 'switchProject'])->name('project.switch');

// new inventory routes
Route::get('/dashboard/data', [HomeController::class, 'dashboardData'])->name('dashboard.data');
// track records
Route::get('/track-records', [HomeController::class, 'track_records'])->name('track.home');
Route::get('/track-records/filter', [HomeController::class, 'filter_records'])->name('track.filter');

Route::get('/getCurrentUser', [HomeController::class, 'getCurrentUser'])->name('getCurrentUser');
Route::get('/getNotificationList', [HomeController::class, 'getNotifications'])->name('getNotifications');
Route::get('/sales', function () { return view('inventory.sale.list'); });
Route::get('/sales/create', function () { return view('inventory.sale.create'); });
Route::get('/customers', function () { return view('inventory.people.customers'); });
Route::get('/suppliers', function () { return view('inventory.people.suppliers'); });
Route::get('/getSubactivities', [ProductController::class, 'getSubactivities'])->name('getSubactivities');
// Direct Transfers
Route::get('/directTransfers', [DirectTransferController::class, 'index'])->name('directTransfers.list');
Route::post('/directTransfers/save', [DirectTransferController::class, 'save'])->name('directTransfers.save');
Route::get('/directTransfers/delete/{id}', [DirectTransferController::class, 'destroy'])->name('directTransfers.delete');
Route::post('fetchToolSerials', [StockController::class, 'fetchToolSerials'])->name('fetchToolSerials')->middleware('can:manage_warehouse');
// General Settings Routes
Route::prefix('settings')->group(function () {

    // Cities Routes
    Route::get('/cities', [CityController::class, 'index'])->name('cities.list');
    Route::post('/cities/store', [CityController::class, 'store'])->name('cities.store');
    Route::get('/cities/delete/{id}', [CityController::class, 'destroy'])->name('cities.destroy');
    Route::put('/cities/update/{id?}', [CityController::class, 'store'])->name('cities.update');
});

//tools
Route::prefix('tools')->group(function(){
    Route::get('/', [ToolsController::class, 'index'])->name('tools.list');
    Route::get('/getList', [ToolsController::class, 'getlist'])->name('tools.getlist');

    Route::get('/transfer/history/{id}', [StockController::class, 'transfer_history'])->name('tools.transfer.history');

    Route::get('/create', [ToolsController::class, 'create'])->name('tools.create');
    Route::post('/save/{id?}', [ToolsController::class, 'save'])->name('tools.save');
    Route::get('/stocks', [ToolsController::class, 'getStock'])->name('tools.stocklist');
    Route::get('/getProducts', [ToolsController::class, '_getProducts'])->name('tools.getProducts');
    Route::get('/delete/{id}', [ToolsController::class, 'delete'])->name('tools.delete');
})->middleware('can:manage_warehouse');
// Store Routes
Route::prefix('stores')->group(function(){
    // site stores
    Route::get('/', [ProjectController::class, 'index'])->name('stores.list')->middleware('can:manage_store');
    Route::get('/create', [ProjectController::class, 'create'])->name('stores.create')->middleware('can:store_create');
    Route::post('/save/{id?}', [ProjectController::class, 'store'])->name('stores.store')->middleware('can:store_create');
    Route::post('/subStore/save/{id?}', [ProjectController::class, 'saveSubStore'])->name('stores.saveSubStore')->middleware('can:store_create');
    Route::get('/edit/{id}', [ProjectController::class, 'edit'])->name('stores.edit')->middleware('can:store_edit');
    Route::get('/delete/{id}', [ProjectController::class, 'destroy'])->name('stores.destroy')->middleware('can:store_delete');
    Route::put('/update/{id?}', [ProjectController::class, 'store'])->name('stores.update');
    Route::put('/subStore/update/{id?}', [ProjectController::class, 'saveSubStore'])->name('stores.updateSubStore');
    Route::post('/getStoreIncharges', [ProjectController::class, 'getStoreIncharges'])->name('stores.getStoreIncharges');

    Route::prefix('material/issued')->group(function(){
        
        Route::get('/', [ProjectController::class, 'materialIssued'])->name('stores.materialIssued');
        Route::get('/getList', [ProjectController::class, 'getMaterialIssuedList'])->name('stores.getMaterialIssuedList');
        Route::get('/employee_record', [ProjectController::class, 'track_material_issued_records'])->name('stores.getMaterialIssuedEmployeeRecord');
        Route::get('/filter', [ProjectController::class, 'filter_material_issued'])->name('material_issued.filter');

        Route::get('/create', [ProjectController::class, 'createMaterialIssued'])->name('stores.createMaterialIssued');
        Route::post('/save/{id?}', [ProjectController::class, 'saveMaterialIssued'])->name('stores.saveMaterialIssued');
        Route::get('/stocks', [ProjectController::class, 'getStockRecord'])->name('stores.stocklist');
        Route::get('/getProducts', [ProjectController::class, '_getProducts'])->name('stores.getProducts');
        Route::get('/delete/{id}', [ProjectController::class, 'deleteMaterialIssued'])->name('stores.deleteMaterialIssued');
        Route::get('/print/{id}', [ProjectController::class, 'printMaterialIssued'])->name('project.material_issued.print');
        Route::get('/edit/{id}', [ProjectController::class, 'editMaterialIssued'])->name('project.material_issued.edit');
        
        Route::post('/update/{id}', [ProjectController::class, 'updateMaterialIssued'])->name('project.material_issued.update');

    });

    // Stock Record Routes
    Route::get('/stocks/{id}', [ProjectController::class, 'stock'])->name('stores.stock');
    Route::get('/exportStock', [ProjectController::class, 'exportStock'])->name('stores.stock.export');
    Route::get('/stocks/getList/{id}', [StockController::class, 'storeStocksList'])->name('stores.stocks.getList');
    Route::get('/stocks/{projectId}/{productId}', [StockController::class, 'getProjectProductStock'])->name('project.stock.history');
    // Update product stock
    Route::get('/products', [ProjectController::class, 'storeProducts'])->name('stores.storeProducts');
    Route::post('/products/showUpdateForm/{id}', [ProjectController::class, 'showUpdateForm'])->name('stores.showUpdateForm');
    Route::post('/updateProductStock/{id}', [ProjectController::class, 'updateProductStock'])->name('stores.updateProductStock');
    Route::get('/get-list', [ProjectController::class, 'getProductsList'])->name('store.getProductsList');

    // Item Limit List
    Route::get('/items/list/{id}', [ProjectController::class, 'storeItemsList'])->name('stores.items.list');
    Route::get('/items/list/create/{id}', [ProjectController::class, 'createItemsList'])->name('stores.items.create');
    Route::post('/items/save', [ProjectController::class, 'saveStoreItems'])->name('stores.items.save');
    Route::get('/items/delete/{id}', [ProjectController::class, 'deleteItem'])->name('stores.items.delete')->middleware('can:store_delete');
    Route::get('/getSubStores/{store_id}', [ProjectController::class, 'getSubStore'])->name('stores.get_sub_store');

    // material returns
    Route::get('/transferReturns', [TransferController::class, 'transferReturns'])->name('transferReturns.list');
    Route::get('/createReturnRequest', [TransferController::class, 'createReturnRequest'])->name('transferReturns.create');
    Route::post('/transferReturns/save', [TransferController::class, 'transferReturnsSave'])->name('transferReturns.save');
    Route::get('/transferReturns/delete/{id}', [TransferController::class, 'transferReturnsDelete'])->name('transferReturns.delete');
    Route::get('transferReturns/getProducts', [TransferController::class, '_getReturnProducts'])->name('transferReturns.getProducts');

    // returnable items
    Route::get('/returnables/{id}', [ProjectController::class, 'returnables'])->name('returnables.list');
    Route::get('/transferReturns/show/{id}', [TransferController::class, 'showTransferReturns'])->name('showTransferReturns');
    Route::post('/transferReturns/approve', [TransferController::class, 'approveTransferReturnRequest'])->name('transferReturns.approve');


});

// Suppliers
Route::prefix('supplier')->group(function (){
    Route::get('/', [SupplierController::class, 'index'])->name('suppliers.list')->middleware('can:manage_supplier');
    Route::get('/create', [SupplierController::class, 'create'])->name('suppliers.create')->middleware('can:manage_supplier');
    Route::post('/save/{id?}', [SupplierController::class, 'store'])->name('suppliers.store')->middleware('can:manage_supplier');
    Route::get('/edit/{id}', [SupplierController::class, 'edit'])->name('suppliers.edit')->middleware('can:manage_supplier');
    Route::get('/delete/{id}', [SupplierController::class, 'destroy'])->name('suppliers.destroy')->middleware('can:manage_supplier');
    Route::put('/update/{id?}', [SupplierController::class, 'store'])->name('suppliers.update');
});

// Purchases Routes
Route::prefix('purchases')->group(function(){
    Route::get('/list/{projectId?}', [PurchaseController::class, 'index'])->name('purchases.list');
    Route::get('/create', [PurchaseController::class, 'create'])->name('purchases.create');
    Route::post('/save/{id?}', [PurchaseController::class, 'store'])->name('purchases.store');
    Route::get('/edit/{id}', [PurchaseController::class, 'edit'])->name('purchases.edit');
    Route::get('/delete/{id}', [PurchaseController::class, 'destroy'])->name('purchases.destroy');
    Route::post('/update/{id?}', [PurchaseController::class, 'update'])->name('purchases.update');
    Route::get('/get-list/{id?}', [PurchaseController::class, 'getList'])->name('purchases.getList');
    Route::post('/getDeliveryOrder/{id}', [PurchaseController::class, 'getDeliveryOrder'])->name('purchases.getDeliveryOrder');
    Route::get('/detail/{id}', [PurchaseController::class, 'details'])->name('purchases.detail');
    Route::post('purchase/upload/{id}', [PurchaseController::class, 'upload'])->name('purchases.upload');
    Route::get('/download', [PurchaseController::class, 'download'])->name('purchases.download');
    Route::post('getAvailableProducts', [PurchaseController::class, 'getAvailableProducts'])->name('purchases.getAvailableProducts');
    Route::post('getRequestedQuantity', [PurchaseController::class, 'getRequestedQuantity'])->name('purchases.getRequestedQuantity');

});

// Warehouse Routes
Route::prefix('warehouses')->group(function(){
    Route::get('/', [WarehouseController::class, 'index'])->name('warehouses.list')->middleware('can:manage_warehouse');
    Route::get('/create', [WarehouseController::class, 'create'])->name('warehouses.create')->middleware('can:manage_warehouse');
    Route::post('/save/{id?}', [WarehouseController::class, 'store'])->name('warehouses.store')->middleware('can:manage_warehouse');
    Route::get('/edit/{id}', [WarehouseController::class, 'edit'])->name('warehouses.edit')->middleware('can:manage_warehouse');
    Route::get('/delete/{id}', [WarehouseController::class, 'destroy'])->name('warehouses.destroy')->middleware('can:manage_warehouse');
    Route::put('/update/{id?}', [WarehouseController::class, 'store'])->name('warehouses.update');

    // Stock Record Routes
    Route::get('/stocks/{id}/{type}', [WarehouseController::class, 'stock'])->name('warehouses.stock')->middleware('can:manage_warehouse');
    Route::get('/stock/details/{id}', [StockController::class, 'stockDetail'])->name('stock.detail')->middleware('can:manage_warehouse');
    Route::post('/stock/details/save/{id}', [StockController::class, 'saveDetail'])->name('stock.saveDetail')->middleware('can:manage_warehouse');

    Route::get('/exportStock', [WarehouseController::class, 'exportStock'])->name('warehouses.stock.export');
    Route::get('/stocks/get-list/{id}/{type?}', [StockController::class, 'getList'])->name('stocks.getList');

    Route::prefix('material/issued')->group(function(){
        Route::get('/', [WarehouseController::class, 'materialIssued'])->name('warehouses.materialIssued');
        Route::get('/getList', [WarehouseController::class, 'getMaterialIssuedList'])->name('warehouses.getMaterialIssuedList');
        Route::get('/create', [WarehouseController::class, 'createMaterialIssued'])->name('warehouses.createMaterialIssued');
        Route::post('/save/{id?}', [WarehouseController::class, 'saveMaterialIssued'])->name('warehouses.saveMaterialIssued');
        Route::get('/stocks', [WarehouseController::class, 'getStockRecord'])->name('warehouses.stocklist');
        Route::get('/getProducts', [WarehouseController::class, '_getProducts'])->name('warehouses.getProducts');
        Route::get('/delete/{id}', [WarehouseController::class, 'deleteMaterialIssued'])->name('warehouses.deleteMaterialIssued');
    })->middleware('can:manage_warehouse');

});

// Transfers Routes
Route::prefix('transfers')->group(function(){

    Route::post('/status/{id}', [TransferController::class, 'changeStatus'])->name('transfer.changeStatus');
    Route::get('/data/{id}', [TransferController::class, 'detail'])->name('transfer.data');
    Route::get('/detail/{id}', [TransferController::class, 'details'])->name('transfer.detail');
    Route::get('/edit/{id}', [TransferController::class, 'edit'])->name('transfer.edit');
    Route::post('/update/{id}', [TransferController::class, 'update'])->name('transfer.update');
    Route::post('/update-quantity', [TransferController::class, 'updateQuantity'])->name('update.quantity');

    Route::post('/getCurrentStock', [StockController::class, 'getCurrentStock'])->name('products.current_stock');
    Route::post('/getCurrentStockWarehouse', [StockController::class, 'getCurrentStockWarehouse'])->name('products.current_stock_warehouse');

    Route::get('/print/{id}', [TransferController::class, 'print'])->name('transfer.print');
    Route::get('/delivery-order/{id}', [TransferController::class, 'printDeliveryOrder'])->name('transfer.delivery-order');
    Route::post('/getProducts', [TransferController::class, '_getProducts'])->name('transfer.getProducts');
    Route::post('/getTransferProducts', [TransferController::class, '_getTransferProducts'])->name('transfer.getTransferProducts');

    // Store transfer routes
    Route::get('/list/{track?}', [TransferController::class, 'getStoreTransfers'])->name('transfer.list');
    Route::get('/create', [TransferController::class, 'createStoreTransfers'])->name('transfer.create');
    Route::get('/import', [TransferController::class, 'importStoreTransfers'])->name('transfer.import.create');
    Route::post('/upload', [TransferController::class, 'uploadFile'])->name('transfer.import.upload');
    Route::post('/import', [TransferController::class, 'importTransfers'])->name('transfer.import');
    Route::get('/downloadImportedFile', [TransferController::class, 'downloadImportedFile'])->name('transfer.downloadImportedFile');
    
    Route::get('/show', [TransferController::class, 'showRequest'])->name('transfer.show');
    Route::post('/save/{id?}', [TransferController::class, 'saveStoreTransfers'])->name('transfer.save');
    Route::get('/delete/{id}', [TransferController::class, 'deleteStoreTransfers'])->name('transfer.delete');
    Route::post('/getList', [TransferController::class, '_dataTable'])->name('transfer.getList');
    Route::get('/getTransferStatus/{id?}', [TransferController::class, 'getTransferStatus'])->name('transfer.getStatus');
    Route::post('/getStatusList/{id?}', [TransferController::class, 'getStatusList'])->name('transfer.getStatusList');
    Route::post('/store{id?}', [TransferController::class, 'store'])->name('transfer.store');

    Route::get('/deliveryOrder/create/{id}', [TransferController::class, 'createTransferVoucher'])->name('transfer.voucher.create');
    Route::get('/purchase/detail/{id}', [PurchaseController::class, 'detail'])->name('transfer.lpo.details');
    Route::post('/deliveryOrder/save/{id}', [TransferController::class, 'storeTransferVoucher'])->name('transfer.voucher.store');

    Route::post('/getProductsForTransfer', [TransferController::class, 'getProductsForTransfer'])->name('transfer.getProductsForTransfer');
    Route::post('/getPurchasesForTransfer', [TransferController::class, 'getPurchasesForTransfer'])->name('transfer.getPurchasesForTransfer');
    Route::post('/getProductsForPurchase', [TransferController::class, 'getProductsForPurchase'])->name('transfer.getProductsForPurchase');
    Route::post('/getTransferForPurchase', [TransferController::class, 'getTransferForPurchase'])->name('transfer.getTransferForPurchase');
    Route::post('/getProductForTransfer', [TransferController::class, 'getProductForTransfer'])->name('transfer.getProductForTransfer');
    Route::post('/getProductsForTransferRequest', [TransferController::class, 'getProductsForTransferRequest'])->name('transfer.getProductsForTransferRequest');
    Route::get('/deliveryOrder/create', [DeliveryOrderController::class, 'create'])->name('deliveryOrder.create');
    Route::post('/saveDeliveryOrders', [DeliveryOrderController::class, 'saveDeliveryOrder'])->name('deliveryOrder.save');
    Route::post('/saveDeliveryOrderWithoutLPO', [DeliveryOrderController::class, 'saveDeliveryOrderWithoutLPO'])->name('deliveryOrderWithoutLPO.save');
    Route::post('delivery-order/upload/{id}', [DeliveryOrderController::class, 'upload'])->name('deliveryOrder.upload');
    Route::get('/deliveryOrder/detail/{id}', [DeliveryOrderController::class, 'detail'])->name('deliveryOrder.detail');
    Route::get('/download', [DeliveryOrderController::class, 'download'])->name('deliveryOrder.download');

    Route::post('/getProductTable', [MaterialRequestController::class, 'getProductTable'])->name('transfer.getProductTable');
    Route::get('/deliveryOrders/{id?}/{type?}', [DeliveryOrderController::class, 'index'])->name('deliveryOrder.list');
    Route::get('/print-delivery-order/{id}', [DeliveryOrderController::class, 'print'])->name('deliveryOrder.print');
    Route::get('/deliveryOrder/delete/{id}', [DeliveryOrderController::class, 'delete'])->name('deliveryOrder.delete');
    Route::post('/deliveryOrders/getList/{id?}', [DeliveryOrderController::class, '_dataTable'])->name('deliveryOrder.getList');

    Route::post('/deliveryOrders/update/{id?}', [DeliveryOrderController::class, 'update'])->name('deliveryOrder.update');
    Route::post('/deliveryOrders/getAvailableProducts', [DeliveryOrderController::class, 'getAvailableProducts'])->name('deliveryOrder.getAvailableProducts');
    Route::post('/deliveryOrders/getRequestedQuantity', [DeliveryOrderController::class, 'getRequestedQuantity'])->name('deliveryOrder.getRequestedQuantity');
});

// Product Routes
Route::prefix('products')->group(function () {

    Route::get('/', [ProductController::class, 'index'])->name('products.list');
    Route::get('/create', [ProductController::class, 'create'])->name('products.create');
    Route::post('/store/{id?}', [ProductController::class, 'store'])->name('products.store');
    Route::get('/edit/{id}', [ProductController::class, 'edit'])->name('products.edit');
    Route::get('/destroy/{id}', [ProductController::class, 'destroy'])->name('products.destroy');
    Route::get('/get-list', [ProductController::class, 'getList'])->name('products.getList');
    Route::post('/get-list', [ProductController::class, 'getProducts'])->name('products.getList');
    Route::post('/showUpdateForm/{id}', [ProductController::class, 'showUpdateForm'])->name('products.showUpdateForm');
    Route::post('/updateProduct', [ProductController::class, 'updateProduct'])->name('products.updateProduct');
    Route::post('/checkProductType', [ProductController::class, 'checkProductType'])->name('checkProductType');

    // Categories Routes
    Route::get('/categories', [CategoryController::class, 'index'])->name('categories.list');
    Route::post('/categories/store', [CategoryController::class, 'store'])->name('categories.store');
    Route::get('/categories/delete/{id}', [CategoryController::class, 'destroy'])->name('categories.destroy');
    Route::put('/categories/update/{id?}', [CategoryController::class, 'store'])->name('categories.update');
    Route::get('/categories/get-list', [CategoryController::class, 'getList'])->name('categories.getList');

    // Brands Routes
    Route::get('/brands', [BrandController::class, 'index'])->name('brands.list');
    Route::post('/brands/store', [BrandController::class, 'store'])->name('brands.store');
    Route::get('/brands/delete/{id}', [BrandController::class, 'destroy'])->name('brands.destroy');
    Route::put('/brands/update/{id?}', [BrandController::class, 'store'])->name('brands.update');
    Route::get('/brands/get-list', [BrandController::class, 'getList'])->name('brands.getList');

    // Units Routes
    Route::get('/units', [UnitsController::class, 'index'])->name('units.list');
    Route::post('/units/store', [UnitsController::class, 'store'])->name('units.store');
    Route::get('/units/delete/{id}', [UnitsController::class, 'destroy'])->name('units.destroy');
    Route::put('/units/update/{id?}', [UnitsController::class, 'store'])->name('units.update');
    Route::get('/units/get-list', [UnitsController::class, 'getList'])->name('units.getList');

    // Import products Routes
    Route::post('/import', [ProductController::class, 'import'])->name('products.import');

    // Search products Routes
    Route::get('/search', [ProductController::class,'search'])->name('products.search');
    Route::get('/searchByStock', [ProductController::class,'searchByStock'])->name('products.searchByStock');
    Route::get('/searchByCategorySubcategory/{category_id}/{subcategory_id}', [ProductController::class,'searchByCategorySubcategory'])->name('products.searchByCategorySubcategory');

});
