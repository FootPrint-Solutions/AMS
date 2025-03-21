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
use App\Http\Controllers\MasterData\Vehicle\VehicleYear;
use App\Http\Controllers\MasterData\Vehicle\VehicleFuel;
use App\Http\Controllers\MasterData\Vehicle\VehicleTransmission;
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

// Work Order Instruction Template
use App\Http\Controllers\Settings\WorkOrderInstructionTemplate;

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
    Route::get('/', [Dashboard::class, 'index'])->name('dashboard');
    Route::get('/auth', [Authentication::class, 'index'])->name('auth.index');
    Route::get('/dashboard', [Dashboard::class, 'index']);
    Route::get('/dashboard/chart/revenue', [Dashboard::class, 'getRevenueChart']);

    // MASTER DATA
    // Company
    require __DIR__ . '/web/master-data/company.php';

    // Customer
    require __DIR__ . '/web/master-data/customer.php';

    // Vehicle
    require __DIR__ . '/web/master-data/vehicle.php';

    // Vehicle Brand
    require __DIR__ . '/web/master-data/vehicle-brand.php';

    // Vehicle Year
    require __DIR__ . '/web/master-data/vehicle-year.php';

    // Vehicle Fuel
    require __DIR__ . '/web/master-data/vehicle-fuel.php';

    // Vehicle Transmission
    require __DIR__ . '/web/master-data/vehicle-transmission.php';

    // Battery
    require __DIR__ . '/web/master-data/battery.php';

    // Battery Brand
    require __DIR__ . '/web/master-data/battery-brand.php';

    // Battery Subbrand Category
    require __DIR__ . '/web/master-data/battery-subrand.php';

    // Battery Usage Type
    require __DIR__ . '/web/master-data/battery-usage-type.php';

    // Battery Technology
    require __DIR__ . '/web/master-data/battery-technology.php';

    // Battery Size Category
    require __DIR__ . '/web/master-data/battery-size-category.php';

    // Distributor
    require __DIR__ . '/web/master-data/distributor.php';

    // Distributor Shop
    require __DIR__ . '/web/master-data/distributor-shop.php';

    // Shop Technician
    require __DIR__ . '/web/master-data/distributor-shop-technician.php';

    // Inventory
    // Inventory
    require __DIR__ . '/web/inventory/inventory.php';

    // Orders
    // Quick Quotation
    require __DIR__ . '/web/orders/quick-quotation.php';

    // Quick Quotation Mobile
    require __DIR__ . '/web/orders/quick-quotation-mobile.php';

    // Sales Order
    require __DIR__ . '/web/orders/sales-order.php';

    // Mobile
    require __DIR__ . '/web/orders/sales-order-mobile.php';

    // Work Order
    require __DIR__ . '/web/orders/work-order.php';

    // work order mobile
    require __DIR__ . '/web/orders/work-order-mobile.php';

    // tracking technician
    require __DIR__ . '/web/orders/tracking-technician.php';


    // Settings
    // Message Template
    require __DIR__ . '/web/settings/message-template.php';

    // Print Template
    require __DIR__ . '/web/settings/print-template.php';


    // Import Template
    require __DIR__ . '/web/settings/import-template.php';

    // Tax
    require __DIR__ . '/web/settings/tax.php';

    // Payment Method
    require __DIR__ . '/web/settings/payment-method.php';

    // Promo
    require __DIR__ . '/web/settings/promo.php';

    //profile
    require __DIR__ . '/web/settings/profile.php';

    // Data Battery
    require __DIR__ . '/web/publish/data-battery.php';

    // Sales Online
    require __DIR__ . '/web/publish/sales-online.php';

    // Data Category
    require __DIR__ . '/web/publish/data-category.php';

    // Work Order Instruction
    require __DIR__ . '/web/orders/work-order-instruction.php';


    // WO Instruction Template
    require __DIR__ . '/web/settings/work-order-instruction-template.php';

    // user manager 
    require __DIR__ . '/web/settings/user-manager.php';

    // Review
    require __DIR__ . '/web/publish/review.php';

    // Faq
    require __DIR__ . '/web/publish/faq.php';

    // Gallery
    require __DIR__ . '/web/publish/gallery.php';

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
    require __DIR__ . '/web/developer/menu.php';

    // Menu Parent Manager
    require __DIR__ . '/web/developer/menu-parent.php';

    // Audit
    require __DIR__ . '/web/developer/audit.php';
});

// Auth
Route::middleware(['guest'])->group(function () {
    // Midtrans Core API
    Route::post('/midtrans/snap/token', [Midtrans::class, 'createSnapToken']);
    Route::post('/midtrans/notification', [Midtrans::class, 'notificationHandler']);
});

Route::get('/login', [Authentication::class, 'index'])->name('login');
Route::post('/auth', [Authentication::class, 'authenticate'])->name('auth.authenticate');

// route for all user to tracking order
Route::get('/tracking/{order_id}', [WorkOrder::class, 'trackingOrder']);
Route::get('/tracking/live/{order_id}', [WorkOrder::class, 'trackingOrderLive']);

Route::get('sandbox/battery_template', [Battery::class, 'battery_template']);
