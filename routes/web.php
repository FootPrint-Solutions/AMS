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

// SETTINGS
use App\Http\Controllers\Settings\Promo;
use App\Http\Controllers\Settings\Tax;
use App\Http\Controllers\Settings\MessageTemplate;
use App\Http\Controllers\Settings\PaymentMethod;
use App\Http\Controllers\Settings\PrintTemplate;

// ADMIN
use App\Http\Controllers\Admin\User;

// DEVELOPER
use App\Http\Controllers\Developer\Menu;
use App\Http\Controllers\Developer\MenuParent;

// AUTH
use App\Http\Controllers\Auth\Authentication;

// PROFILE
use App\Http\Controllers\Profile;

// Midtrans
use App\Http\Controllers\Server\Midtrans;

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
    Route::get('/customer', [Customer::class, 'index']);
    Route::post('/customer/show', [Customer::class, 'show'])->name('customer.show');
    Route::get('/customer/create', [Customer::class, 'create']);
    Route::get('/customer/edit/{id}', [Customer::class, 'edit'])->name('customer.edit');
    Route::post('/customer/store', [Customer::class, 'store'])->name('customer.store');
    Route::post('/customer/update', [Customer::class, 'update'])->name('customer.update');
    Route::post('/customer/toggle', [Customer::class, 'updateStatus'])->name('customer.toggle');

    // Vehicle
    Route::get('/vehicle', [Vehicle::class, 'index'])->name('vehicle.index');
    Route::post('/vehicle/show', [Vehicle::class, 'show'])->name('vehicle.show');
    Route::get('/vehicle/create', [Vehicle::class, 'create']);
    Route::get('/vehicle/edit/{id}', [Vehicle::class, 'edit'])->name('vehicle.edit');
    Route::post('/vehicle/store', [Vehicle::class, 'store'])->name('vehicle.store');
    Route::post('/vehicle/update', [Vehicle::class, 'update'])->name('vehicle.update');
    Route::post('/vehicle/toggle', [Vehicle::class, 'updateStatus'])->name('vehicle.toggle');
    Route::post('/vehicle/import', [Vehicle::class, 'import'])->name('vehicle.import');

    // Vehicle Brand
    Route::get('/vehicle/brand', [VehicleBrand::class, 'index'])->name('vehicle.brand.index');
    Route::post('/vehicle/brand/show', [VehicleBrand::class, 'show'])->name('vehicle.brand.show');
    Route::get('/vehicle/brand/create', [VehicleBrand::class, 'create']);
    Route::get('/vehicle/brand/edit/{id}', [VehicleBrand::class, 'edit'])->name('vehicle.brand.edit');
    Route::post('/vehicle/brand/store', [VehicleBrand::class, 'store'])->name('vehicle.brand.store');
    Route::post('/vehicle/brand/update', [VehicleBrand::class, 'update'])->name('vehicle.brand.update');
    Route::post('/vehicle/brand/toggle', [VehicleBrand::class, 'updateStatus'])->name('vehicle.brand.toggle');

    // Battery
    Route::get('/battery', [Battery::class, 'index']);
    Route::post('/battery/show', [Battery::class, 'show'])->name('battery.show');
    Route::get('/battery/create', [Battery::class, 'create']);
    Route::get('/battery/edit/{id}', [Battery::class, 'edit'])->name('battery.edit');
    Route::post('/battery/store', [Battery::class, 'store'])->name('battery.store');
    Route::post('/battery/update', [Battery::class, 'update'])->name('battery.update');
    Route::post('/battery/toggle', [Battery::class, 'updateStatus'])->name('battery.toggle');
    Route::post('/battery/import', [Battery::class, 'import'])->name('battery.import');
    Route::post('/battery/get/size', [Battery::class, 'getBatteriesBySizeCategory']);
    Route::get('/battery/get/{keyword}', [Battery::class, 'getBatteriesByKeyword']);

    // Battery Brand
    Route::get('/battery/brand', [BatteryBrand::class, 'index'])->name('battery.brand.index');
    Route::post('/battery/brand/show', [BatteryBrand::class, 'show'])->name('battery.brand.show');
    Route::get('/battery/brand/create', [BatteryBrand::class, 'create']);
    Route::get('/battery/brand/edit/{id}', [BatteryBrand::class, 'edit'])->name('battery.brand.edit');
    Route::post('/battery/brand/store', [BatteryBrand::class, 'store'])->name('battery.brand.store');
    Route::post('/battery/brand/update', [BatteryBrand::class, 'update'])->name('battery.brand.update');
    Route::post('/battery/brand/destroy', [BatteryBrand::class, 'destroy'])->name('battery.brand.destroy');

    // Battery Subbrand Category
    Route::get('/battery/subbrand', [BatterySubbrand::class, 'index'])->name('battery.subbrand.index');
    Route::post('/battery/subbrand/show', [BatterySubbrand::class, 'show'])->name('battery.subbrand.show');
    Route::get('/battery/subbrand/create', [BatterySubbrand::class, 'create']);
    Route::get('/battery/subbrand/edit/{id}', [BatterySubbrand::class, 'edit'])->name('battery.subbrand.edit');
    Route::post('/battery/subbrand/store', [BatterySubbrand::class, 'store'])->name('battery.subbrand.store');
    Route::post('/battery/subbrand/update', [BatterySubbrand::class, 'update'])->name('battery.subbrand.update');
    Route::post('/battery/subbrand/destroy', [BatterySubbrand::class, 'destroy'])->name('battery.subbrand.destroy');

    // Battery Usage Type
    Route::get('/battery/usage', [BatteryUsage::class, 'index'])->name("battery.usage.index");
    Route::post('/battery/usage/show', [BatteryUsage::class, 'show'])->name('battery.usage.show');
    Route::get('/battery/usage/create', [BatteryUsage::class, 'create']);
    Route::get('/battery/usage/edit/{id}', [BatteryUsage::class, 'edit'])->name('battery.usage.edit');
    Route::post('/battery/usage/store', [BatteryUsage::class, 'store'])->name('battery.usage.store');
    Route::post('/battery/usage/update', [BatteryUsage::class, 'update'])->name('battery.usage.update');
    Route::post('/battery/usage/destroy', [BatteryUsage::class, 'destroy'])->name('battery.usage.destroy');

    // Battery Technology
    Route::get('/battery/technology', [BatteryTechnology::class, 'index'])->name("battery.Technology.index");
    Route::post('/battery/technology/show', [BatteryTechnology::class, 'show'])->name('battery.technology.show');
    Route::get('/battery/technology/create', [BatteryTechnology::class, 'create']);
    Route::get('/battery/technology/edit/{id}', [BatteryTechnology::class, 'edit'])->name('battery.technology.edit');
    Route::post('/battery/technology/store', [BatteryTechnology::class, 'store'])->name('battery.technology.store');
    Route::post('/battery/technology/update', [BatteryTechnology::class, 'update'])->name('battery.technology.update');
    Route::post('/battery/technology/destroy', [BatteryTechnology::class, 'destroy'])->name('battery.technology.destroy');

    // Battery Size Category
    Route::get('/battery/size', [BatterySize::class, 'index'])->name("battery.size.index");
    Route::post('/battery/size/show', [BatterySize::class, 'show'])->name('battery.size.show');
    Route::get('/battery/size/create', [BatterySize::class, 'create']);
    Route::get('/battery/size/edit/{id}', [BatterySize::class, 'edit'])->name('battery.size.edit');
    Route::post('/battery/size/store', [BatterySize::class, 'store'])->name('battery.size.store');
    Route::post('/battery/size/update', [BatterySize::class, 'update'])->name('battery.size.update');
    Route::post('/battery/size/destroy', [BatterySize::class, 'destroy'])->name('battery.size.destroy');

    // Distributor
    Route::get('/distributor', [Distributor::class, 'index'])->name("distributor.index");
    Route::post('/distributor/show', [Distributor::class, 'show']);
    Route::get('/distributor/create', [Distributor::class, 'create']);
    Route::get('/distributor/edit/{id}', [Distributor::class, 'edit']);
    Route::post('/distributor/store', [Distributor::class, 'store']);
    Route::post('/distributor/update', [Distributor::class, 'update']);
    Route::post('/distributor/toggle', [Distributor::class, 'updateStatus']);

    // Distributor Shop
    Route::get('/distributor/shop', [DistributorShop::class, 'index']);
    Route::post('/distributor/shop/show', [DistributorShop::class, 'show']);
    Route::get('/distributor/shop/create', [DistributorShop::class, 'create']);
    Route::get('/distributor/shop/edit/{id}', [DistributorShop::class, 'edit']);
    Route::post('/distributor/shop/store', [DistributorShop::class, 'store']);
    Route::post('/distributor/shop/update', [DistributorShop::class, 'update']);
    Route::post('/distributor/shop/toggle', [DistributorShop::class, 'updateStatus']);
    Route::post('/distributor/shop/battery/show', [DistributorShopBattery::class, 'show']);
    Route::get('/distributor/shop/battery/create/{shopId}/{distributorId}', [DistributorShopBattery::class, 'create']);
    Route::get('/distributor/shop/battery/edit/{id}', [DistributorShopBattery::class, 'edit']);
    Route::post('/distributor/shop/battery/store', [DistributorShopBattery::class, 'store']);
    Route::post('/distributor/shop/battery/store/batch/{shopId}', [DistributorShopBattery::class, 'storeBatch']);
    Route::post('/distributor/shop/battery/update', [DistributorShopBattery::class, 'update']);
    Route::post('/distributor/shop/battery/destroy', [DistributorShopBattery::class, 'destroy']);

    // Shop Technician
    Route::get('/distributor/technician', [DistributorShopTechnician::class, 'index'])->name("distributor.technician.index");
    Route::post('/distributor/technician/show', [DistributorShopTechnician::class, 'show']);
    Route::get('/distributor/technician/create', [DistributorShopTechnician::class, 'create']);
    Route::get('/distributor/technician/edit/{id}', [DistributorShopTechnician::class, 'edit']);
    Route::post('/distributor/technician/store', [DistributorShopTechnician::class, 'store']);
    Route::post('/distributor/technician/update', [DistributorShopTechnician::class, 'update']);
    Route::post('/distributor/technician/destroy', [DistributorShopTechnician::class, 'destroy']);

    // Orders
    // Quick Quotation
    Route::get('/quotation/quick', [QuickQuotation::class, 'index']);
    Route::get('/quotation/customer/find', [QuickQuotation::class, 'findCustomer'])->name('quotation.findCustomer');
    Route::post('/quotation/customer/share', [QuickQuotation::class, 'shareFormPersonalDetails'])->name('quotation.shareFormPersonalDetails');
    Route::get('/quotation/customer/vehicle/find', [QuickQuotation::class, 'findVehicleByIdCustomer'])->name('quotation.findVehicleByIdCustomer');
    Route::get('/quotation/vehicle/find', [QuickQuotation::class, 'findVehicleByIdVehicle'])->name('quotation.findVehicleByIdVehicle');
    Route::get('/quotation/customer/maps/near', [QuickQuotation::class, 'getMapsNearAddressCustomer'])->name('quotation.getMapsNearAddressCustomer');
    Route::post('/quotation/battery/share', [QuickQuotation::class, 'shareBattery'])->name('quotation.shareBattery');
    Route::get('/quotation/checkout', [QuickQuotation::class, 'getCheckoutPreview'])->name('quotation.getCheckoutPreview');
    Route::get('/quotation/payment', [QuickQuotation::class, 'getPaymentPreview'])->name('quotation.getPaymentPreview');
    Route::post('/quotation/battery/copy', [QuickQuotation::class, 'getBatteryCopyDetail'])->name('quotation.getBatteryCopyDetail');
    Route::post('/quotation/share-invoice', [QuickQuotation::class, 'shareInvoice'])->name('quotation.shareInvoice');
    Route::post('/quotation/share-payment-details', [QuickQuotation::class, 'sharePaymentDetails'])->name('quotation.sharePaymentDetails');
    Route::post('/quotation/save-data', [QuickQuotation::class, 'saveData'])->name('quotation.saveData');
    Route::post('/quotation/customer/copy', [QuickQuotation::class, 'getCustomerCopyDetail'])->name('quotation.getCustomerCopyDetail');
    Route::post('/quotation/checkout/copy', [QuickQuotation::class, 'getCheckoutCopyDetail'])->name('quotation.getCheckoutCopyDetail');
    Route::post('/quotation/payment-details/copy', [QuickQuotation::class, 'getPaymentDetailsCopyDetail'])->name('quotation.getPaymentDetailsCopyDetail');
    Route::get('/quotation/customer/findbycontact', [QuickQuotation::class, 'findCustomerByContact'])->name('quotation.findCustomerByContact');
    Route::get('/quotation/battery/find', [QuickQuotation::class, 'findBattery'])->name('quotation.findBattery');
    Route::get('/quotation/get-link-battery', [QuickQuotation::class, 'getLinkBattery'])->name('quotation.getLinkBattery');
    Route::get('/quotation/distributor/find', [QuickQuotation::class, 'findDistributor'])->name('quotation.findDistributor');
    Route::get('/quotation/battery/autoComplete', [QuickQuotation::class, 'autoCompleteBattery'])->name('quotation.autoCompleteBattery');
    Route::get('/quotation/work-order', [WorkOrder::class, 'index']);
    Route::post('/quotation/battery/screenshot', [QuickQuotation::class, 'screenshotBattery'])->name('quotation.screenshotBattery');
    Route::post('/quotation/battery/save-screenshoot', [QuickQuotation::class, 'saveScreenshoot'])->name('quotation.saveScreenshoot');
    // Quick Quotation Mobile
    Route::post('/quotation/mobile/checkout', [QuickQuotation::class, 'mobileCheckout']);
    Route::post('/quotation/mobile/detail/battery', [QuickQuotation::class, 'getBatteryDetail']);
    Route::post('/quotation/mobile/payment', [QuickQuotation::class, 'mobilePayment']);
    Route::post('/quotation/mobile/save-data', [QuickQuotation::class, 'saveDataMobile']);

    // Sales Order
    Route::get('/sales-order', [SalesOrder::class, 'index']);
    Route::post('/sales-order/show', [SalesOrder::class, 'show']);
    Route::get('/sales-order/invoice/{id}', [SalesOrder::class, 'invoice']);
    Route::get('/sales-order/create', [SalesOrder::class, 'create']);
    Route::get('/sales-order/edit/{id}', [SalesOrder::class, 'edit']);
    Route::post('/sales-order/store', [SalesOrder::class, 'store']);
    Route::post('/sales-order/update', [SalesOrder::class, 'update']);
    Route::post('/sales-order/delete', [SalesOrder::class, 'destroy']);
    Route::post('/sales-order/post', [SalesOrder::class, 'post']);
    Route::post('/sales-order/battery/show', [SalesOrderBattery::class, 'show']);
    Route::post('/sales-order/battery/update/production-code', [SalesOrderBattery::class, 'updateProductionCode']);
    Route::get('/sales-order/technician/get/{shopId}', [SalesOrder::class, 'getTechnicianByShop']);
    Route::get('/sales-order/work-order/{id}', [SalesOrder::class, 'workOrderCreate']);
    Route::get('/sales-order/recreate-payment-link/{id}', [SalesOrder::class, 'recreatePaymentLink']);
    Route::get('/sales-order/copy-link-payment/{id}', [SalesOrder::class, 'copyPaymentLink']);
    // Mobile
    Route::get('/sales-order/show/mobile/{status?}/{filter?}', [SalesOrder::class, 'getSalesOrders']);
    Route::get('/sales-order/show/detail/mobile/{id}', [SalesOrder::class, 'getSalesOrderDetail']);
    Route::get('/sales-order/status', [SalesOrder::class, 'getSalesOrderStatus']);

    // Work Order
    Route::get('/work-order', [WorkOrder::class, 'index']);
    Route::post('/work-order/show', [WorkOrder::class, 'show']);
    Route::post('/work-order/print/', [WorkOrder::class, 'print']);
    Route::post('/work-order/upload-image', [WorkOrder::class, 'uploadImage']);
    Route::get('/work-order/print-technician-report/{id}', [WorkOrder::class, 'printTechnicianReport']);
    Route::post('/work-order/delete', [WorkOrder::class, 'destroy']);
    Route::post('/work-order/detail', [WorkOrder::class, 'detail']);
    Route::post('/work-order/production-code', [WorkOrder::class, 'getProductionCode']);
    // work order mobile
    Route::get('/work-order/mobile/lazy-load/list', [WorkOrder::class, 'lazyLoadList']);
    Route::get('/work-order/mobile/detail', [WorkOrder::class, 'getWorkOrderDetail']);
    Route::post('/work-order/mobile/delete', [WorkOrder::class, 'destroy']);
    Route::get('/work-order/mobile/print-technician-report/{id}', [WorkOrder::class, 'printTechnicianReportMobile']);
    Route::post('/work-order/mobile/print/', [WorkOrder::class, 'printMobile']);

    // Settings
    // Message Template
    Route::get('/template/message', [MessageTemplate::class, 'index']);
    Route::post('/template/message/update', [MessageTemplate::class, 'update']);
    Route::get('/template/print', [PrintTemplate::class, 'index']);
    Route::post('/template/print/update', [PrintTemplate::class, 'update']);
    Route::post('/template/show', [PrintTemplate::class, 'show']);
    Route::get('/template/create', [PrintTemplate::class, 'create']);
    Route::post('/template/store', [PrintTemplate::class, 'store']);
    Route::post('/template/destroy', [PrintTemplate::class, 'destroy']);
    Route::get('/template/edit/{id}', [PrintTemplate::class, 'edit']);
    Route::post('/template/update', [PrintTemplate::class, 'update']);
    Route::get('/template/details/{id}', [PrintTemplate::class, 'details']);
    Route::post('/template/print/update/details', [PrintTemplate::class, 'updateDetails']);
    Route::post('/template/print/get/sub-task', [PrintTemplate::class, 'getSubTask']);
    Route::post('/template/print/update/sub-task', [PrintTemplate::class, 'updateSubTask']);
    Route::post('/template/print/delete/sub-task', [PrintTemplate::class, 'deleteSubTask']);

    // Tax
    Route::get('/tax', [Tax::class, 'index']);
    Route::post('/tax/show', [Tax::class, 'show']);
    Route::get('/tax/create', [Tax::class, 'create']);
    Route::get('/tax/edit/{id}', [Tax::class, 'edit']);
    Route::post('/tax/store', [Tax::class, 'store']);
    Route::post('/tax/update', [Tax::class, 'update']);
    Route::post('/tax/toggle', [Tax::class, 'updateStatus']);
    Route::post('/tax/destroy', [Tax::class, 'destroy']);

    // Payment Method
    Route::get('/payment', [PaymentMethod::class, 'index']);
    Route::post('/payment/show', [PaymentMethod::class, 'show']);
    Route::get('/payment/create', [PaymentMethod::class, 'create']);
    Route::get('/payment/edit/{id}', [PaymentMethod::class, 'edit']);
    Route::post('/payment/store', [PaymentMethod::class, 'store']);
    Route::post('/payment/update', [PaymentMethod::class, 'update']);
    Route::post('/payment/toggle', [PaymentMethod::class, 'updateStatus']);
    Route::post('/payment/destroy', [PaymentMethod::class, 'destroy']);

    // Promo
    Route::get('/promo', [Promo::class, 'index']);
    Route::post('/promo/show', [Promo::class, 'show']);
    Route::post('/promo/show/dashboard', [Promo::class, 'showDashboard']);
    Route::get('/promo/create', [Promo::class, 'create']);
    Route::get('/promo/edit/{id}', [Promo::class, 'edit']);
    Route::post('/promo/store', [Promo::class, 'store']);
    Route::post('/promo/update', [Promo::class, 'update']);
    Route::post('/promo/toggle', [Promo::class, 'updateStatus']);

    //profile
    Route::get('/profile',  [Profile::class, 'index']);
    Route::post('/profile/update', [Profile::class, 'update']);
    Route::post('/profile/picture/update', [Profile::class, 'updateProfilePicture']);
    Route::post('/profile/password/update', [Profile::class, 'updatePassword']);
    Route::get('/delete-session-whatsapp', [Profile::class, 'deleteSessionWhatsapp']);
    Route::post('/profile/api-key/update', [Profile::class, 'updateApiKey']);

    // Logout
    Route::get('/logout', [Authentication::class, 'logout']);


    // Reusable Component
    Route::get('/datatables/toolbar', function () {
        $editUrl = request()->input('editUrl');
        $deleteUrl = request()->input('deleteUrl');
        $toggleUrl = request()->input('toggleUrl');
        $idIdx = request()->input('idIdx');
        return view('components.dt-toolbar', array('idIdx' => $idIdx, 'editUrl' => $editUrl, 'deleteUrl' => $deleteUrl, 'toggleUrl' => $toggleUrl))->render();
    });
});

Route::middleware(['developer'])->group(function () {
    // Menu Manager
    Route::get('/menu',  [Menu::class, 'index']);
    Route::post('/menu/show', [Menu::class, 'show']);
    Route::get('/menu/create', [Menu::class, 'create']);
    Route::get('/menu/edit/{id}', [Menu::class, 'edit']);
    Route::post('/menu/store', [Menu::class, 'store']);
    Route::post('/menu/update', [Menu::class, 'update']);
    Route::post('/menu/destroy', [Menu::class, 'destroy']);
    Route::get('/menu/refresh', [Menu::class, 'refresh']);
    Route::get('/menu/get/parent/{id}', [Menu::class, 'getMenu']);
    Route::get('/menu/parent/create', [MenuParent::class, 'create']);
    Route::post('/menu/parent/store', [MenuParent::class, 'store']);
    Route::get('/logs', [\Rap2hpoutre\LaravelLogViewer\LogViewerController::class, 'index']);
});

// Auth
Route::middleware(['guest'])->group(function () {
    Route::get('/login', [Authentication::class, 'index'])->name('login');
    Route::post('/auth', [Authentication::class, 'authenticate'])->name('auth.authenticate');

    // Midtrans Core API
    Route::post('/midtrans/snap/token', [Midtrans::class, 'createSnapToken']);
    Route::post('/midtrans/notification', [Midtrans::class, 'notificationHandler']);
});
