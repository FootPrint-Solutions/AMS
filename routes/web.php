<?php

use App\Http\Controllers\Admin\Role;
use Illuminate\Support\Facades\Route;

// DASHBOARD
use App\Http\Controllers\Dashboard\Dashboard;

// MASTER DATA
use App\Http\Controllers\MasterData\Company\Company;
use App\Http\Controllers\MasterData\Customer\Customer;
use App\Http\Controllers\MasterData\Vehicle\Vehicle;
use App\Http\Controllers\MasterData\Vehicle\VehicleBrand;
use App\Http\Controllers\MasterData\Battery\Battery;
use App\Http\Controllers\MasterData\Battery\BatteryBrand;
use App\Http\Controllers\MasterData\Battery\BatterySubbrand;
use App\Http\Controllers\MasterData\Battery\BatteryTechnology;
use App\Http\Controllers\MasterData\Battery\BatteryUsage;
use App\Http\Controllers\MasterData\Battery\BatterySize;
use App\Http\Controllers\MasterData\Distributor\Distributor;
use App\Http\Controllers\MasterData\Distributor\DistributorShop;
use App\Http\Controllers\MasterData\Distributor\DistributorShopTechnician;
use App\Http\Controllers\MasterData\Distributor\DistributorShopBattery;

// ORDERS
use App\Http\Controllers\Orders\QuickQuotation;
use App\Http\Controllers\Orders\SalesOrder;
use App\Http\Controllers\Orders\SalesOrderBattery;

// WORK ORDER
use App\Http\Controllers\Orders\WorkOrder;

// TRACKING TECHNICIAN
use App\Http\Controllers\Orders\TrackingTechnician;

// SETTINGS
use App\Http\Controllers\Settings\Promo;
use App\Http\Controllers\Settings\Tax;
use App\Http\Controllers\Settings\MessageTemplate;
use App\Http\Controllers\Settings\PaymentMethod;
use App\Http\Controllers\Settings\PrintTemplate;
use App\Http\Controllers\Settings\ImportTemplate;
use App\Http\Controllers\Settings\UserManager;


// ADMIN
use App\Http\Controllers\Admin\User;

// DEVELOPER
use App\Http\Controllers\Developer\Menu;
use App\Http\Controllers\Developer\MenuParent;

// AUTH
use App\Http\Controllers\Auth\Authentication;
use App\Http\Controllers\Inventory\Inventory;
// PROFILE
use App\Http\Controllers\Profile;

// Midtrans
use App\Http\Controllers\Server\Midtrans;

// Data Battery
use App\Http\Controllers\Publish\DataBattery;

// Sales Online
use App\Http\Controllers\Publish\SalesOnline;

// Data Category
use App\Http\Controllers\Publish\DataCategory;

// Work Order Technician
use App\Http\Controllers\Orders\WorkOrderInstruction;

// Audit
use App\Http\Controllers\Developer\Audit;

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


Route::middleware(['auth'])->group(function () {
    // DASHBOARD
    Route::get('/', [Dashboard::class, 'index']);
    Route::get('/dashboard', [Dashboard::class, 'index']);
    Route::get('/dashboard/chart/revenue', [Dashboard::class, 'getRevenueChart']);

    // MASTER DATA
    // Company
    Route::get('/company', [Company::class, 'index'])->name('company.index');
    Route::post('/company/update', [Company::class, 'update'])->name('company.update');

    // Customer
    Route::get('/customer', [Customer::class, 'index'])->name('customer.index')->middleware('permission:view_customer');
    Route::post('/customer/show', [Customer::class, 'show'])->name('customer.show')->middleware('permission:view_customer');
    Route::get('/customer/create', [Customer::class, 'create'])->middleware('permission:add_customer');
    Route::get('/customer/edit/{id}', [Customer::class, 'edit'])->name('customer.edit')->middleware('permission:edit_customer');
    Route::post('/customer/store', [Customer::class, 'store'])->name('customer.store')->middleware('permission:add_customer');
    Route::post('/customer/update', [Customer::class, 'update'])->name('customer.update')->middleware('permission:edit_customer');
    Route::post('/customer/toggle', [Customer::class, 'updateStatus'])->name('customer.toggle')->middleware('permission:edit_customer');

    // Vehicle
    Route::get('/vehicle', [Vehicle::class, 'index'])->name('vehicle.index')->middleware('permission:view_vehicle');
    Route::post('/vehicle/show', [Vehicle::class, 'show'])->name('vehicle.show')->middleware('permission:view_vehicle');
    Route::get('/vehicle/create', [Vehicle::class, 'create'])->middleware('permission:add_vehicle');
    Route::get('/vehicle/edit/{id}', [Vehicle::class, 'edit'])->name('vehicle.edit')->middleware('permission:edit_vehicle');
    Route::post('/vehicle/store', [Vehicle::class, 'store'])->name('vehicle.store')->middleware('permission:add_vehicle');
    Route::post('/vehicle/update', [Vehicle::class, 'update'])->name('vehicle.update')->middleware('permission:edit_vehicle');
    Route::post('/vehicle/toggle', [Vehicle::class, 'updateStatus'])->name('vehicle.toggle')->middleware('permission:edit_vehicle');
    Route::post('/vehicle/import', [Vehicle::class, 'import'])->name('vehicle.import')->middleware('permission:add_vehicle');

    // Vehicle Brand
    Route::get('/vehicle/brand', [VehicleBrand::class, 'index'])->name('vehicle.brand.index')->middleware('permission:view_vehicle');
    Route::post('/vehicle/brand/show', [VehicleBrand::class, 'show'])->name('vehicle.brand.show')->middleware('permission:view_vehicle');
    Route::get('/vehicle/brand/create', [VehicleBrand::class, 'create'])->middleware('permission:add_vehicle');
    Route::get('/vehicle/brand/edit/{id}', [VehicleBrand::class, 'edit'])->name('vehicle.brand.edit')->middleware('permission:edit_vehicle');
    Route::post('/vehicle/brand/store', [VehicleBrand::class, 'store'])->name('vehicle.brand.store')->middleware('permission:add_vehicle');
    Route::post('/vehicle/brand/update', [VehicleBrand::class, 'update'])->name('vehicle.brand.update')->middleware('permission:edit_vehicle');
    Route::post('/vehicle/brand/toggle', [VehicleBrand::class, 'updateStatus'])->name('vehicle.brand.toggle')->middleware('permission:edit_vehicle');

    // Battery
    Route::get('/battery', [Battery::class, 'index'])->middleware('permission:view_battery')->name('battery.index');
    Route::post('/battery/show', [Battery::class, 'show'])->name('battery.show')->middleware('permission:view_battery');
    Route::get('/battery/create', [Battery::class, 'create'])->middleware('permission:add_battery');
    Route::get('/battery/edit/{id}', [Battery::class, 'edit'])->name('battery.edit')->middleware('permission:edit_battery');
    Route::post('/battery/store', [Battery::class, 'store'])->name('battery.store')->middleware('permission:add_battery');
    Route::post('/battery/update', [Battery::class, 'update'])->name('battery.update')->middleware('permission:edit_battery');
    Route::post('/battery/toggle', [Battery::class, 'updateStatus'])->name('battery.toggle')->middleware('permission:edit_battery');
    Route::post('/battery/import', [Battery::class, 'import'])->name('battery.import')->middleware('permission:add_battery');
    Route::post('/battery/import/price', [Battery::class, 'importPrice'])->name('battery.import.price')->middleware('permission:add_battery');
    Route::post('/battery/export', [Battery::class, 'export'])->name('battery.export')->middleware('permission:view_battery');
    Route::post('/battery/get/size', [Battery::class, 'getBatteriesBySizeCategory'])->name('battery.size')->middleware('permission:view_battery');
    Route::get('/battery/get/{keyword}', [Battery::class, 'getBatteriesByKeyword'])->name('battery.get')->middleware('permission:view_battery');
    Route::post('/battery/compress', [Battery::class, 'compress'])->name('battery.compress')->middleware('permission:view_battery');

    // Battery Brand
    Route::get('/battery/brand', [BatteryBrand::class, 'index'])->name('battery.brand.index')->middleware('permission:view_battery');
    Route::post('/battery/brand/show', [BatteryBrand::class, 'show'])->name('battery.brand.show')->middleware('permission:view_battery');
    Route::get('/battery/brand/create', [BatteryBrand::class, 'create'])->middleware('permission:add_battery');
    Route::get('/battery/brand/edit/{id}', [BatteryBrand::class, 'edit'])->name('battery.brand.edit')->middleware('permission:edit_battery');
    Route::post('/battery/brand/store', [BatteryBrand::class, 'store'])->name('battery.brand.store')->middleware('permission:add_battery');
    Route::post('/battery/brand/update', [BatteryBrand::class, 'update'])->name('battery.brand.update')->middleware('permission:edit_battery');
    Route::post('/battery/brand/destroy', [BatteryBrand::class, 'destroy'])->name('battery.brand.destroy')->middleware('permission:delete_battery');

    // Battery Subbrand Category
    Route::get('/battery/subbrand', [BatterySubbrand::class, 'index'])->name('battery.subbrand.index')->middleware('permission:view_battery');
    Route::post('/battery/subbrand/show', [BatterySubbrand::class, 'show'])->name('battery.subbrand.show')->middleware('permission:view_battery');
    Route::get('/battery/subbrand/create', [BatterySubbrand::class, 'create'])->middleware('permission:add_battery');
    Route::get('/battery/subbrand/edit/{id}', [BatterySubbrand::class, 'edit'])->name('battery.subbrand.edit')->middleware('permission:edit_battery');
    Route::post('/battery/subbrand/store', [BatterySubbrand::class, 'store'])->name('battery.subbrand.store')->middleware('permission:add_battery');
    Route::post('/battery/subbrand/update', [BatterySubbrand::class, 'update'])->name('battery.subbrand.update')->middleware('permission:edit_battery');
    Route::post('/battery/subbrand/destroy', [BatterySubbrand::class, 'destroy'])->name('battery.subbrand.destroy')->middleware('permission:delete_battery');

    // Battery Usage Type
    Route::get('/battery/usage', [BatteryUsage::class, 'index'])->name('battery.usage.index')->middleware('permission:view_battery');
    Route::post('/battery/usage/show', [BatteryUsage::class, 'show'])->name('battery.usage.show')->middleware('permission:view_battery');
    Route::get('/battery/usage/create', [BatteryUsage::class, 'create'])->middleware('permission:add_battery');
    Route::get('/battery/usage/edit/{id}', [BatteryUsage::class, 'edit'])->name('battery.usage.edit')->middleware('permission:edit_battery');
    Route::post('/battery/usage/store', [BatteryUsage::class, 'store'])->name('battery.usage.store')->middleware('permission:add_battery');
    Route::post('/battery/usage/update', [BatteryUsage::class, 'update'])->name('battery.usage.update')->middleware('permission:edit_battery');
    Route::post('/battery/usage/destroy', [BatteryUsage::class, 'destroy'])->name('battery.usage.destroy')->middleware('permission:delete_battery');

    // Battery Technology
    Route::get('/battery/technology', [BatteryTechnology::class, 'index'])->name('battery.Technology.index')->middleware('permission:view_battery');
    Route::post('/battery/technology/show', [BatteryTechnology::class, 'show'])->name('battery.technology.show')->middleware('permission:view_battery');
    Route::get('/battery/technology/create', [BatteryTechnology::class, 'create'])->middleware('permission:add_battery');
    Route::get('/battery/technology/edit/{id}', [BatteryTechnology::class, 'edit'])->name('battery.technology.edit')->middleware('permission:edit_battery');
    Route::post('/battery/technology/store', [BatteryTechnology::class, 'store'])->name('battery.technology.store')->middleware('permission:add_battery');
    Route::post('/battery/technology/update', [BatteryTechnology::class, 'update'])->name('battery.technology.update')->middleware('permission:edit_battery');
    Route::post('/battery/technology/destroy', [BatteryTechnology::class, 'destroy'])->name('battery.technology.destroy')->middleware('permission:delete_battery');

    // Battery Size Category
    Route::get('/battery/size', [BatterySize::class, 'index'])->name('battery.size.index')->middleware('permission:view_battery');
    Route::post('/battery/size/show', [BatterySize::class, 'show'])->name('battery.size.show')->middleware('permission:view_battery');
    Route::get('/battery/size/create', [BatterySize::class, 'create'])->middleware('permission:add_battery');
    Route::get('/battery/size/edit/{id}', [BatterySize::class, 'edit'])->name('battery.size.edit')->middleware('permission:edit_battery');
    Route::post('/battery/size/store', [BatterySize::class, 'store'])->name('battery.size.store')->middleware('permission:add_battery');
    Route::post('/battery/size/update', [BatterySize::class, 'update'])->name('battery.size.update')->middleware('permission:edit_battery');
    Route::post('/battery/size/destroy', [BatterySize::class, 'destroy'])->name('battery.size.destroy')->middleware('permission:delete_battery');

    // Distributor
    Route::get('/distributor', [Distributor::class, 'index'])->name('distributor.index')->middleware('permission:view_distributor');
    Route::post('/distributor/show', [Distributor::class, 'show'])->name('distributor.show')->middleware('permission:view_distributor');
    Route::get('/distributor/create', [Distributor::class, 'create'])->middleware('permission:add_distributor');
    Route::get('/distributor/edit/{id}', [Distributor::class, 'edit'])->name('distributor.edit')->middleware('permission:edit_distributor');
    Route::post('/distributor/store', [Distributor::class, 'store'])->name('distributor.store')->middleware('permission:add_distributor');
    Route::post('/distributor/update', [Distributor::class, 'update'])->name('distributor.update')->middleware('permission:edit_distributor');
    Route::post('/distributor/toggle', [Distributor::class, 'updateStatus'])->name('distributor.toggle')->middleware('permission:edit_distributor');

    // Distributor Shop
    Route::get('/distributor/shop', [DistributorShop::class, 'index'])->name('distributor.shop.index')->middleware('permission:view_distributor');
    Route::post('/distributor/shop/show', [DistributorShop::class, 'show'])->name('distributor.shop.show')->middleware('permission:view_distributor');
    Route::get('/distributor/shop/create', [DistributorShop::class, 'create'])->name('distributor.shop.create')->middleware('permission:add_distributor');
    Route::get('/distributor/shop/edit/{id}', [DistributorShop::class, 'edit'])->name('distributor.shop.edit')->middleware('permission:edit_distributor');
    Route::post('/distributor/shop/store', [DistributorShop::class, 'store'])->name('distributor.shop.store')->middleware('permission:add_distributor');
    Route::post('/distributor/shop/update', [DistributorShop::class, 'update'])->name('distributor.shop.update')->middleware('permission:edit_distributor');
    Route::post('/distributor/shop/toggle', [DistributorShop::class, 'updateStatus'])->name('distributor.shop.toggle')->middleware('permission:edit_distributor');
    Route::post('/distributor/shop/battery/show', [DistributorShopBattery::class, 'show'])->name('distributor.shop.battery.show')->middleware('permission:view_distributor');
    Route::get('/distributor/shop/battery/create/{shopId}/{distributorId}', [DistributorShopBattery::class, 'create'])->name('distributor.shop.battery.create')->middleware('permission:add_distributor');
    Route::get('/distributor/shop/battery/edit/{id}', [DistributorShopBattery::class, 'edit'])->name('distributor.shop.battery.edit')->middleware('permission:edit_distributor');
    Route::post('/distributor/shop/battery/store', [DistributorShopBattery::class, 'store'])->name('distributor.shop.battery.store')->middleware('permission:add_distributor');
    Route::post('/distributor/shop/battery/store/batch/{shopId}', [DistributorShopBattery::class, 'storeBatch'])->name('distributor.shop.battery.store.batch')->middleware('permission:add_distributor');
    Route::post('/distributor/shop/battery/update', [DistributorShopBattery::class, 'update'])->name('distributor.shop.battery.update')->middleware('permission:edit_distributor');
    Route::post('/distributor/shop/battery/destroy', [DistributorShopBattery::class, 'destroy'])->name('distributor.shop.battery.destroy')->middleware('permission:delete_distributor');

    // Shop Technician
    Route::get('/distributor/technician', [DistributorShopTechnician::class, 'index'])->name('distributor.technician.index')->middleware('permission:view_distributor');
    Route::post('/distributor/technician/show', [DistributorShopTechnician::class, 'show'])->name('distributor.technician.show')->middleware('permission:view_distributor');
    Route::get('/distributor/technician/create', [DistributorShopTechnician::class, 'create'])->name('distributor.technician.create')->middleware('permission:add_distributor');
    Route::get('/distributor/technician/edit/{id}', [DistributorShopTechnician::class, 'edit'])->name('distributor.technician.edit')->middleware('permission:edit_distributor');
    Route::post('/distributor/technician/store', [DistributorShopTechnician::class, 'store'])->name('distributor.technician.store')->middleware('permission:add_distributor');
    Route::post('/distributor/technician/update', [DistributorShopTechnician::class, 'update'])->name('distributor.technician.update')->middleware('permission:edit_distributor');
    Route::post('/distributor/technician/destroy', [DistributorShopTechnician::class, 'destroy'])->name('distributor.technician.destroy')->middleware('permission:delete_distributor');

    // Inventory
    // Inventory
    Route::get('/inventory', [Inventory::class, 'index'])->name('inventory.index')->middleware('permission:view_inventory');
    Route::get('/inventory/get/{name}', [Inventory::class, 'getStock'])->name('inventory.get')->middleware('permission:view_inventory');

    // Orders
    // Quick Quotation
    Route::get('/quotation/quick', [QuickQuotation::class, 'index'])->name('quotation.index')->middleware('permission:view_quick_quotation');
    Route::get('/quotation/customer/find', [QuickQuotation::class, 'findCustomer'])->name('quotation.findCustomer')->middleware('permission:view_quick_quotation');
    Route::post('/quotation/customer/share', [QuickQuotation::class, 'shareFormPersonalDetails'])->name('quotation.shareFormPersonalDetails')->middleware('permission:view_quick_quotation');
    Route::get('/quotation/customer/vehicle/find', [QuickQuotation::class, 'findVehicleByIdCustomer'])->name('quotation.findVehicleByIdCustomer')->middleware('permission:view_quick_quotation');
    Route::get('/quotation/vehicle/find', [QuickQuotation::class, 'findVehicleByIdVehicle'])->name('quotation.findVehicleByIdVehicle')->middleware('permission:view_quick_quotation');
    Route::get('/quotation/customer/maps/near', [QuickQuotation::class, 'getMapsNearAddressCustomer'])->name('quotation.getMapsNearAddressCustomer')->middleware('permission:view_quick_quotation');
    Route::post('/quotation/battery/share', [QuickQuotation::class, 'shareBattery'])->name('quotation.shareBattery')->middleware('permission:view_quick_quotation');
    Route::get('/quotation/checkout', [QuickQuotation::class, 'getCheckoutPreview'])->name('quotation.getCheckoutPreview')->middleware('permission:view_quick_quotation');
    Route::get('/quotation/payment', [QuickQuotation::class, 'getPaymentPreview'])->name('quotation.getPaymentPreview')->middleware('permission:view_quick_quotation');
    Route::post('/quotation/battery/copy', [QuickQuotation::class, 'getBatteryCopyDetail'])->name('quotation.getBatteryCopyDetail')->middleware('permission:view_quick_quotation');
    Route::post('/quotation/share-invoice', [QuickQuotation::class, 'shareInvoice'])->name('quotation.shareInvoice')->middleware('permission:view_quick_quotation')->middleware('permission:view_quick_quotation');
    Route::post('/quotation/share-payment-details', [QuickQuotation::class, 'sharePaymentDetails'])->name('quotation.sharePaymentDetails')->middleware('permission:view_quick_quotation');
    Route::post('/quotation/save-data', [QuickQuotation::class, 'saveData'])->name('quotation.saveData')->middleware('permission:view_quick_quotation');
    Route::post('/quotation/customer/copy', [QuickQuotation::class, 'getCustomerCopyDetail'])->name('quotation.getCustomerCopyDetail')->middleware('permission:view_quick_quotation');
    Route::post('/quotation/checkout/copy', [QuickQuotation::class, 'getCheckoutCopyDetail'])->name('quotation.getCheckoutCopyDetail')->middleware('permission:view_quick_quotation');
    Route::post('/quotation/payment-details/copy', [QuickQuotation::class, 'getPaymentDetailsCopyDetail'])->name('quotation.getPaymentDetailsCopyDetail')->middleware('permission:view_quick_quotation');
    Route::get('/quotation/customer/findbycontact', [QuickQuotation::class, 'findCustomerByContact'])->name('quotation.findCustomerByContact')->middleware('permission:view_quick_quotation');
    Route::get('/quotation/battery/find', [QuickQuotation::class, 'findBattery'])->name('quotation.findBattery')->middleware('permission:view_quick_quotation');
    Route::get('/quotation/get-link-battery', [QuickQuotation::class, 'getLinkBattery'])->name('quotation.getLinkBattery')->middleware('permission:view_quick_quotation');
    Route::get('/quotation/distributor/find', [QuickQuotation::class, 'findDistributor'])->name('quotation.findDistributor')->middleware('permission:view_quick_quotation');
    Route::get('/quotation/battery/autoComplete', [QuickQuotation::class, 'autoCompleteBattery'])->name('quotation.autoCompleteBattery')->middleware('permission:view_quick_quotation');
    Route::get('/quotation/work-order', [WorkOrder::class, 'index'])->name('quotation.workOrder')->middleware('permission:view_work_order');
    Route::post('/quotation/battery/screenshot', [QuickQuotation::class, 'screenshotBattery'])->name('quotation.screenshotBattery')->middleware('permission:view_quick_quotation');
    Route::post('/quotation/battery/save-screenshoot', [QuickQuotation::class, 'saveScreenshoot'])->name('quotation.saveScreenshoot')->middleware('permission:view_quick_quotation');
    // Quick Quotation Mobile
    Route::post('/quotation/mobile/checkout', [QuickQuotation::class, 'mobileCheckout'])->name('quotation.mobileCheckout')->middleware('permission:view_quick_quotation');
    Route::post('/quotation/mobile/detail/battery', [QuickQuotation::class, 'getBatteryDetail'])->name('quotation.getBatteryDetail')->middleware('permission:view_quick_quotation');
    Route::post('/quotation/mobile/payment', [QuickQuotation::class, 'mobilePayment'])->name('quotation.mobilePayment')->middleware('permission:view_quick_quotation');
    Route::post('/quotation/mobile/save-data', [QuickQuotation::class, 'saveDataMobile'])->name('quotation.saveDataMobile')->middleware('permission:view_quick_quotation');

    // Sales Order
    Route::get('/sales-order', [SalesOrder::class, 'index'])->name('sales-order.index')->middleware('permission:view_sales_order_(so)');
    Route::post('/sales-order/show', [SalesOrder::class, 'show'])->name('sales-order.show')->middleware('permission:view_sales_order_(so)');
    Route::get('/sales-order/invoice/{id}', [SalesOrder::class, 'invoice'])->name('sales-order.invoice')->middleware('permission:view_sales_order_(so)');
    Route::get('/sales-order/create', [SalesOrder::class, 'create'])->middleware('permission:add_sales_order_(so)');
    Route::get('/sales-order/edit/{id}', [SalesOrder::class, 'edit'])->name('sales-order.edit')->middleware('permission:edit_sales_order_(so)');
    Route::post('/sales-order/store', [SalesOrder::class, 'store'])->name('sales-order.store')->middleware('permission:add_sales_order_(so)');
    Route::post('/sales-order/update', [SalesOrder::class, 'update'])->name('sales-order.update')->middleware('permission:edit_sales_order_(so)');
    Route::post('/sales-order/delete', [SalesOrder::class, 'destroy'])->name('sales-order.delete')->middleware('permission:delete_sales_order_(so)');
    Route::post('/sales-order/post', [SalesOrder::class, 'post'])->name('sales-order.post')->middleware('permission:edit_sales_order_(so)');
    Route::post('/sales-order/battery/show', [SalesOrderBattery::class, 'show'])->name('sales-order.battery.show')->middleware('permission:view_sales_order_(so)');
    Route::post('/sales-order/battery/update/production-code', [SalesOrderBattery::class, 'updateProductionCode'])->name('sales-order.battery.update.production-code')->middleware('permission:edit_sales_order_(so)');
    Route::get('/sales-order/technician/get/{shopId}', [SalesOrder::class, 'getTechnicianByShop'])->name('sales-order.getTechnicianByShop')->middleware('permission:view_sales_order_(so)');
    Route::get('/sales-order/work-order/{id}', [SalesOrder::class, 'workOrderCreate'])->name('sales-order.workOrderCreate')->middleware('permission:view_sales_order_(so)');
    Route::get('/sales-order/recreate-payment-link/{id}', [SalesOrder::class, 'recreatePaymentLink'])->name('sales-order.recreatePaymentLink')->middleware('permission:view_sales_order_(so)');
    Route::get('/sales-order/copy-link-payment/{id}', [SalesOrder::class, 'copyPaymentLink'])->name('sales-order.copyPaymentLink')->middleware('permission:view_sales_order_(so)');
    // Mobile
    Route::get('/sales-order/show/mobile/{status?}/{filter?}', [SalesOrder::class, 'getSalesOrders'])->name('sales-order.getSalesOrders')->middleware('permission:view_sales_order_(so)');
    Route::get('/sales-order/show/detail/mobile/{id}', [SalesOrder::class, 'getSalesOrderDetail'])->name('sales-order.getSalesOrderDetail')->middleware('permission:view_sales_order_(so)');
    Route::get('/sales-order/status', [SalesOrder::class, 'getSalesOrderStatus'])->name('sales-order.getSalesOrderStatus')->middleware('permission:view_sales_order_(so)');

    // Work Order
    Route::get('/work-order', [WorkOrder::class, 'index'])->name('work-order.index')->middleware('permission:view_work_order_(wo)');
    Route::post('/work-order/show', [WorkOrder::class, 'show'])->name('work-order.show')->middleware('permission:view_work_order_(wo)');
    Route::post('/work-order/print/', [WorkOrder::class, 'print'])->name('work-order.print')->middleware('permission:view_work_order_(wo)');
    Route::post('/work-order/upload-image', [WorkOrder::class, 'uploadImage'])->name('work-order.uploadImage')->middleware('permission:view_work_order_(wo)');
    Route::get('/work-order/print-technician-report/{id}', [WorkOrder::class, 'printTechnicianReport'])->name('work-order.printTechnicianReport')->middleware('permission:view_work_order_(wo)');
    Route::post('/work-order/delete', [WorkOrder::class, 'destroy'])->name('work-order.delete')->middleware('permission:delete_work_order_(wo)');
    Route::post('/work-order/detail', [WorkOrder::class, 'detail'])->name('work-order.detail')->middleware('permission:view_work_order_(wo)');
    Route::post('/work-order/production-code', [WorkOrder::class, 'getProductionCode'])->name('work-order.getProductionCode')->middleware('permission:view_work_order_(wo)');
    Route::get('/work-order/print-technician-report/{id}/{selectionPrintTechnicianReport}', [WorkOrder::class, 'printTechnicianReportTemplate'])->name('work-order.printTechnicianReportTemplate')->middleware('permission:view_work_order_(wo)');
    Route::post('/work-order/copy-instruction', [WorkOrder::class, 'copyInstruction'])->name('work-order.copyInstruction')->middleware('permission:view_work_order_(wo)');
    // work order mobile
    Route::get('/work-order/mobile/lazy-load/list', [WorkOrder::class, 'lazyLoadList'])->name('work-order.lazyLoadList')->middleware('permission:view_work_order_(wo)');
    Route::get('/work-order/mobile/detail', [WorkOrder::class, 'getWorkOrderDetail'])->name('work-order.getWorkOrderDetail')->middleware('permission:view_work_order_(wo)');
    Route::post('/work-order/mobile/delete', [WorkOrder::class, 'destroy'])->middleware('permission:delete_work_order_(wo)');
    Route::get('/work-order/mobile/print-technician-report/{id}', [WorkOrder::class, 'printTechnicianReportMobile'])->name('work-order.printTechnicianReportMobile')->middleware('permission:view_work_order_(wo)');
    Route::post('/work-order/mobile/print/', [WorkOrder::class, 'printMobile'])->name('work-order.printMobile')->middleware('permission:view_work_order_(wo)');
    // tracking
    Route::post('/work-order/mobile/track/start', [WorkOrder::class, 'startTracking'])->name('work-order.startTracking')->middleware('permission:view_work_order_(wo)');
    Route::post('/work-order/mobile/track/end', [WorkOrder::class, 'endTracking'])->name('work-order.endTracking')->middleware('permission:view_work_order_(wo)');
    Route::post('/work-order/mobile/track/update', [WorkOrder::class, 'updateTracking'])->name('work-order.updateTracking')->middleware('permission:view_work_order_(wo)');

    // tracking technician
    Route::get('/tracking-technician', [TrackingTechnician::class, 'index'])->name('tracking-technician.index')->middleware('permission:view_tracking_technician');
    Route::post('/tracking-technician/show', [TrackingTechnician::class, 'show'])->name('tracking-technician.show')->middleware('permission:view_tracking_technician');
    Route::post('/tracking-technician/share', [TrackingTechnician::class, 'share'])->name('tracking-technician.share')->middleware('permission:view_tracking_technician');
    Route::post('/tracking-technician/delete', [TrackingTechnician::class, 'delete'])->name('tracking-technician.delete')->middleware('permission:delete_tracking_technician');

    // Settings
    // Message Template
    Route::get('/template/message', [MessageTemplate::class, 'index'])->name('message-template.index')->middleware('permission:view_message_templates');
    Route::post('/template/message/update', [MessageTemplate::class, 'update'])->name('message-template.update')->middleware('permission:edit_message_templates');
    Route::get('/template/print', [PrintTemplate::class, 'index'])->name('print-template.index')->middleware('permission:view_print_templates');
    Route::post('/template/print/update', [PrintTemplate::class, 'update'])->middleware('permission:edit_print_templates');
    Route::post('/template/show', [PrintTemplate::class, 'show'])->name('print-template.show')->middleware('permission:view_print_templates');
    Route::get('/template/create', [PrintTemplate::class, 'create'])->middleware('permission:add_print_templates');
    Route::post('/template/store', [PrintTemplate::class, 'store'])->name('print-template.store')->middleware('permission:add_print_templates');
    Route::post('/template/destroy', [PrintTemplate::class, 'destroy'])->name('print-template.destroy')->middleware('permission:delete_print_templates');
    Route::get('/template/edit/{id}', [PrintTemplate::class, 'edit'])->name('print-template.edit')->middleware('permission:edit_print_templates');
    Route::post('/template/update', [PrintTemplate::class, 'update'])->middleware('permission:edit_print_templates');
    Route::get('/template/details/{id}', [PrintTemplate::class, 'details'])->name('print-template.details')->middleware('permission:view_print_templates');
    Route::post('/template/print/update/details', [PrintTemplate::class, 'updateDetails'])->name('print-template.update.details')->middleware('permission:edit_print_templates');
    Route::post('/template/print/get/sub-task', [PrintTemplate::class, 'getSubTask'])->name('print-template.getSubTask')->middleware('permission:view_print_templates');
    Route::post('/template/print/update/sub-task', [PrintTemplate::class, 'updateSubTask'])->name('print-template.update.subTask')->middleware('permission:edit_print_templates');
    Route::post('/template/print/delete/sub-task', [PrintTemplate::class, 'deleteSubTask'])->name('print-template.delete.subTask')->middleware('permission:delete_print_templates');

    // Import Template
    Route::post('/template/import/update', [ImportTemplate::class, 'update'])->name('import-template.update')->middleware('permission:edit_import_templates');
    Route::post('/template/import/delete', [ImportTemplate::class, 'delete'])->name('import-template.delete')->middleware('permission:delete_import_templates');

    // Tax
    Route::get('/tax', [Tax::class, 'index'])->name('tax.index')->middleware('permission:view_tax');
    Route::post('/tax/show', [Tax::class, 'show'])->name('tax.show')->middleware('permission:view_tax');
    Route::get('/tax/create', [Tax::class, 'create'])->middleware('permission:add_tax');
    Route::get('/tax/edit/{id}', [Tax::class, 'edit'])->name('tax.edit')->middleware('permission:edit_tax');
    Route::post('/tax/store', [Tax::class, 'store'])->name('tax.store')->middleware('permission:add_tax');
    Route::post('/tax/update', [Tax::class, 'update'])->name('tax.update')->middleware('permission:edit_tax');
    Route::post('/tax/toggle', [Tax::class, 'updateStatus'])->name('tax.toggle')->middleware('permission:edit_tax');
    Route::post('/tax/destroy', [Tax::class, 'destroy'])->name('tax.destroy')->middleware('permission:delete_tax');

    // Payment Method
    Route::get('/payment', [PaymentMethod::class, 'index'])->name('payment.index')->middleware('permission:view_payment_method');
    Route::post('/payment/show', [PaymentMethod::class, 'show'])->name('payment.show')->middleware('permission:view_payment_method');
    Route::get('/payment/create', [PaymentMethod::class, 'create'])->middleware('permission:add_payment_method');
    Route::get('/payment/edit/{id}', [PaymentMethod::class, 'edit'])->name('payment.edit')->middleware('permission:edit_payment_method');
    Route::post('/payment/store', [PaymentMethod::class, 'store'])->name('payment.store')->middleware('permission:add_payment_method');
    Route::post('/payment/update', [PaymentMethod::class, 'update'])->name('payment.update')->middleware('permission:edit_payment_method');
    Route::post('/payment/toggle', [PaymentMethod::class, 'updateStatus'])->name('payment.toggle')->middleware('permission:edit_payment_method');
    Route::post('/payment/destroy', [PaymentMethod::class, 'destroy'])->name('payment.destroy')->middleware('permission:delete_payment_method');

    // Promo
    Route::get('/promo', [Promo::class, 'index'])->name('promo.index')->middleware('permission:view_promo');
    Route::post('/promo/show', [Promo::class, 'show'])->name('promo.show')->middleware('permission:view_promo');
    Route::post('/promo/show/dashboard', [Promo::class, 'showDashboard'])->name('promo.showDashboard')->middleware('permission:view_promo');
    Route::get('/promo/create', [Promo::class, 'create'])->middleware('permission:add_promo');
    Route::get('/promo/edit/{id}', [Promo::class, 'edit'])->name('promo.edit')->middleware('permission:edit_promo');
    Route::post('/promo/store', [Promo::class, 'store'])->name('promo.store')->middleware('permission:add_promo');
    Route::post('/promo/update', [Promo::class, 'update'])->name('promo.update')->middleware('permission:edit_promo');
    Route::post('/promo/toggle', [Promo::class, 'updateStatus'])->name('promo.toggle')->middleware('permission:edit_promo');

    //profile
    Route::get('/profile', [Profile::class, 'index'])->name('profile.index');
    Route::post('/profile/update', [Profile::class, 'update'])->name('profile.update');
    Route::post('/profile/picture/update', [Profile::class, 'updateProfilePicture'])->name('profile.updateProfilePicture');
    Route::post('/profile/password/update', [Profile::class, 'updatePassword'])->name('profile.updatePassword');
    Route::get('/delete-session-whatsapp', [Profile::class, 'deleteSessionWhatsapp'])->name('profile.deleteSessionWhatsapp');
    Route::post('/profile/api-key/update', [Profile::class, 'updateApiKey'])->name('profile.updateApiKey');
    // Data Battery
    Route::get('/data-battery', [DataBattery::class, 'index'])->name('data-battery.index')->middleware('permission:view_battery_(_online_)');
    Route::post('/data-battery/sync-woocommerce', [DataBattery::class, 'syncWooCommerce'])->name('data-battery.syncWooCommerce')->middleware('permission:view_battery_(_online_)');
    Route::post('/data-battery/sync-category', [DataBattery::class, 'syncCategory'])->name('data-battery.syncCategory')->middleware('permission:view_battery_(_online_)');
    Route::post('/data-battery/sync-product', [DataBattery::class, 'syncProduct'])->name('data-battery.syncProduct')->middleware('permission:view_battery_(_online_)');
    Route::post('/data-battery/view-details', [DataBattery::class, 'viewDetails'])->name('data-battery.viewDetails')->middleware('permission:view_battery_(_online_)');
    Route::post('/data-battery/send-product', [DataBattery::class, 'sendProduct'])->name('data-battery.sendProduct')->middleware('permission:view_battery_(_online_)');
    Route::post('/data-battery/count-category', [DataBattery::class, 'countCategory'])->name('data-battery.countCategory')->middleware('permission:view_battery_(_online_)');
    Route::post('/data-battery/send-category-partially', [DataBattery::class, 'sendCategoryPartially'])->name('data-battery.sendCategoryPartially')->middleware('permission:view_battery_(_online_)');
    Route::post('/data-battery/count-product', [DataBattery::class, 'countProduct'])->name('data-battery.countProduct')->middleware('permission:view_battery_(_online_)');
    Route::post('/data-battery/send-product-partially', [DataBattery::class, 'sendProductPartially'])->name('data-battery.sendProductPartially')->middleware('permission:view_battery_(_online_)');
    Route::get('/data-battery/export/csv', [DataBattery::class, 'exportCsv'])->name('data-battery.exportCsv')->middleware('permission:view_battery_(_online_)');

    // Sales Online
    Route::get('/sales-online', [SalesOnline::class, 'index'])->name('sales-online.index')->middleware('permission:view_sales_(_online_)');
    Route::post('/sales-online/view-details', [SalesOnline::class, 'viewDetails'])->name('sales-online.viewDetails')->middleware('permission:view_sales_(_online_)');
    Route::post('/sales-online/sync-sales-online', [SalesOnline::class, 'syncSalesOnline'])->name('sales-online.syncSalesOnline')->middleware('permission:view_sales_(_online_)');
    Route::post('/sales-online/save-to-sales-orders', [SalesOnline::class, 'saveToSalesOrders'])->name('sales-online.saveToSalesOrders')->middleware('permission:view_sales_(_online_)');
    Route::post('/sales-online/get-form-sales-order', [SalesOnline::class, 'getFormSalesOrder'])->name('sales-online.getFormSalesOrder')->middleware('permission:view_sales_(_online_)');
    Route::post('/sales-online/get-technician', [SalesOnline::class, 'getTechnicianByShop'])->name('sales-online.getTechnicianByShop')->middleware('permission:view_sales_(_online_)');

    // Data Category
    Route::get('/data-category', [DataCategory::class, 'index'])->name('data-category.index')->middleware('permission:view_category_(_online_)');
    Route::post('/data-category/sync-category', [DataCategory::class, 'syncCategory'])->name('data-category.syncCategory')->middleware('permission:view_category_(_online_)');
    Route::post('/data-category/count-parent-category', [DataCategory::class, 'countParentCategory'])->name('data-category.countParentCategory')->middleware('permission:view_category_(_online_)');
    Route::post('/data-category/send-parent-category-partially', [DataCategory::class, 'sendParentCategoryPartially'])->name('data-category.sendParentCategoryPartially')->middleware('permission:view_category_(_online_)');
    Route::post('/data-category/count-category', [DataCategory::class, 'countCategory'])->name('data-category.countCategory')->middleware('permission:view_category_(_online_)');
    Route::post('/data-category/send-category-partially', [DataCategory::class, 'sendCategoryPartially'])->name('data-category.sendCategoryPartially')->middleware('permission:view_category_(_online_)');

    // Work Order Instruction
    Route::get('/work-order-instruction', [WorkOrderInstruction::class, 'index'])->name('work-order-instruction.index')->middleware('permission:view_wo_instruction');
    Route::post('/work-order-instruction/show', [WorkOrderInstruction::class, 'show'])->name('work-order-instruction.show')->middleware('permission:view_wo_instruction');
    Route::get('/wo/{work_order_instruction_number}', [WorkOrderInstruction::class, 'InstructionDetail'])->name('work-order-instruction.instructionDetail')->middleware('permission:view_wo_instruction');
    Route::post('/work-order-instruction/delete', [WorkOrderInstruction::class, 'destroy'])->name('work-order-instruction.delete')->middleware('permission:delete_wo_instruction');
    Route::post('/work-order-instruction/update', [WorkOrderInstruction::class, 'update'])->name('work-order-instruction.update')->middleware('permission:edit_wo_instruction');
    Route::post('/work-order-instruction/detail', [WorkOrderInstruction::class, 'detail'])->name('work-order-instruction.detail')->middleware('permission:view_wo_instruction');
    Route::get('/work-order-instruction/mobile/lazy-load/list', [WorkOrderInstruction::class, 'lazyLoadList'])->name('work-order-instruction.lazyLoadList')->middleware('permission:view_wo_instruction');
    Route::post('/work-order-instruction/mobile/delete', [WorkOrderInstruction::class, 'destroy'])->middleware('permission:delete_wo_instruction');
    Route::post('/work-order-instruction/upload-image', [WorkOrderInstruction::class, 'uploadImage'])->name('work-order-instruction.uploadImage')->middleware('permission:view_wo_instruction');

    // user manager 
    Route::get('/user-manager', [UserManager::class, 'index'])->name('user-manager.index')->middleware('permission:view_user_manager');
    Route::post('/user-manager/show', [UserManager::class, 'show'])->name('user-manager.show')->middleware('permission:view_user_manager');
    Route::get('/user-manager/edit/{id}', [UserManager::class, 'edit'])->name('user-manager.edit')->middleware('permission:edit_user_manager');
    Route::post('/user-manager/destroy', [UserManager::class, 'destroy'])->name('user-manager.destroy')->middleware('permission:delete_user_manager');
    Route::post('/user-manager/update', [UserManager::class, 'update'])->name('user-manager.update')->middleware('permission:edit_user_manager');
    Route::get('/user-manager/create', [UserManager::class, 'create'])->middleware('permission:add_user_manager');
    Route::post('/user-manager/store', [UserManager::class, 'store'])->name('user-manager.store')->middleware('permission:add_user_manager');

    // Logout
    Route::get('/logout', [Authentication::class, 'logout']);

    // Reusable Component
    Route::get('/datatables/toolbar', function () {
        $editUrl = request()->input('editUrl');
        $deleteUrl = request()->input('deleteUrl');
        $toggleUrl = request()->input('toggleUrl');
        $idIdx = request()->input('idIdx');
        return view('components.dt-toolbar', ['idIdx' => $idIdx, 'editUrl' => $editUrl, 'deleteUrl' => $deleteUrl, 'toggleUrl' => $toggleUrl])->render();
    });
});

Route::middleware(['developer'])->group(function () {
    // Menu Manager
    Route::get('/menu', [Menu::class, 'index'])->name('menu.index')->middleware('permission:view_menu_manager');
    Route::post('/menu/show', [Menu::class, 'show'])->name('menu.show')->middleware('permission:view_menu_manager');
    Route::get('/menu/create', [Menu::class, 'create'])->middleware('permission:add_menu_manager');
    Route::get('/menu/edit/{id}', [Menu::class, 'edit'])->name('menu.edit')->middleware('permission:edit_menu_manager');
    Route::post('/menu/store', [Menu::class, 'store'])->name('menu.store')->middleware('permission:add_menu_manager');
    Route::post('/menu/update', [Menu::class, 'update'])->name('menu.update')->middleware('permission:edit_menu_manager');
    Route::post('/menu/destroy', [Menu::class, 'destroy'])->name('menu.destroy')->middleware('permission:delete_menu_manager');
    Route::get('/menu/refresh', [Menu::class, 'refresh'])->name('menu.refresh')->middleware('permission:view_menu_manager');
    Route::get('/menu/get/parent/{id}', [Menu::class, 'getMenu'])->name('menu.getMenu')->middleware('permission:view_menu_manager');
    Route::get('/menu/parent/create', [MenuParent::class, 'create'])->middleware('permission:add_menu_manager');
    Route::post('/menu/parent/store', [MenuParent::class, 'store'])->name('menu.parent.store')->middleware('permission:add_menu_manager');
    Route::get('/logs', [\Rap2hpoutre\LaravelLogViewer\LogViewerController::class, 'index']);

    // Audit
    Route::get('/audit', [Audit::class, 'index'])->name('audit.index')->middleware('permission:view_audit_log');
    Route::post('/audit/show', [Audit::class, 'show'])->name('audit.show')->middleware('permission:view_audit_log');
});

// Auth
Route::middleware(['guest'])->group(function () {
    Route::get('/login', [Authentication::class, 'index'])->name('login');
    Route::post('/auth', [Authentication::class, 'authenticate'])->name('auth.authenticate');

    // Midtrans Core API
    Route::post('/midtrans/snap/token', [Midtrans::class, 'createSnapToken']);
    Route::post('/midtrans/notification', [Midtrans::class, 'notificationHandler']);
});

// route for all user to tracking order
Route::get('/tracking/{order_id}', [WorkOrder::class, 'trackingOrder']);
Route::get('/tracking/live/{order_id}', [WorkOrder::class, 'trackingOrderLive']);
