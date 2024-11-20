<?php

namespace App\Http\Controllers\Publish;

use Illuminate\Http\Request;
use Automattic\WooCommerce\Client;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;


// MODEL
use App\Models\Settings\PaymentMethodModel;
use App\Models\MasterData\Vehicle\VehicleModel;
use App\Models\MasterData\Customer\CustomerModel;
use App\Models\Orders\SalesOrder\SalesOrderModel;
use App\Models\MasterData\Distributor\DistributorShopModel;
use App\Models\MasterData\Distributor\DistributorShopTechnicianModel;
use App\Models\MasterData\Battery\BatteryModel;

class SalesOnline extends Controller
{
    private $title = 'Sales Online';
    private $woocommerce;

    /**
     * Initializes the WooCommerce Client in the constructor.
     *
     * @return void
     */
    public function __construct()
    {
        // Initialize WooCommerce Client in the constructor
        $this->woocommerce = new Client(
            'https://akikita.web.id/',
            'ck_7034e6f1e7a7d3b705df60c37bb003c6a1ca6f9b',
            'cs_b7973fc68cdd299d2ad6647989872e517d88cab0',
            [
                'version' => 'wc/v3',
            ]
        );
    }

    /**
     * Display the index page for the DataBattery controller.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function index()
    {
        try {
            // check if session has sales data already
            if (session()->has('salesData')) {
                $salesData = session('salesData');
            } else {
                $salesData = $this->getSalesAll();
                session(['salesData' => $salesData]);
            }

            $data = array(
                'Sales' => $salesData,
                'Vehicles' => VehicleModel::orderBy('name', 'asc')->get(),
                'Distributors' => DistributorShopModel::orderBy('name', 'asc')->get(),
                'Customers' => CustomerModel::orderBy('name', 'asc')->get(),
            );

            return view(
                'Publish.SalesOnline.index',
                getIndexData(
                    $this->title,
                    $data
                )
            );
        } catch (\Throwable $th) {
            Log::error($th);
            return redirect()->route('dashboard')->with('error', 'Failed to get data sales online');
        }
    }

    /**
     * Retrieves the sales data by the given sales online number.
     *
     * @param \Illuminate\Http\Request $request The HTTP request object.
     *
     * @return \Illuminate\Http\JsonResponse The JSON response containing the sales data.
     */
    public function viewDetails(Request $request)
    {
        try {
            $SalesData = $this->getSalesById($request->sales_online_number);
            return response()->json([
                'status' => 'success',
                'message' => 'Sales data retrieved successfully',
                'data' => $SalesData
            ]);
        } catch (\Throwable $th) {
            Log::error($th);
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to get sales data' . $th->getMessage(),
                'data' => null
            ]);
        }
    }

    public function syncSalesOnline()
    {
        try {
            $salesData = $this->getSalesAll();
            session(['salesData' => $salesData]);

            return response()->json([
                'status' => 'success',
                'message' => 'Sync data to WooCommerce Sales Online successful',
            ]);
        } catch (\Throwable $th) {
            Log::error($th);
            return response()->json([
                'status' => 'error',
                'message' => 'Sync data to WooCommerce Sales Online failed ' . $th->getMessage(),
            ]);
        }
    }

    /**
     * Save sales data from WooCommerce to Sales Orders.
     *
     * This method handles the process of saving sales data retrieved from WooCommerce
     * into the Sales Orders database. It includes the following steps:
     * - Begin a database transaction.
     * - Retrieve sales data from WooCommerce using the provided sales online number.
     * - Create or find a customer based on the billing email.
     * - Create a new sales order with the retrieved data.
     * - Find or create a payment method based on the payment method name.
     * - Save the sales order and its items to the database.
     * - Commit the transaction if all operations are successful.
     * - Rollback the transaction and log an error if any operation fails.
     *
     * @param \Illuminate\Http\Request $request The incoming request containing sales online number, vehicle ID, distributor ID, and technician ID.
     * @return \Illuminate\Http\JsonResponse JSON response indicating the success or failure of the operation.
     */
    public function saveToSalesOrders(Request $request)
    {
        try {
            DB::beginTransaction();

            $SalesOnlineID = $request->input('sales_online_number');

            // get from woocommerce
            $SalesOnline = $this->getSalesById($SalesOnlineID);

            // save to sales orders
            $SalesOrder = new SalesOrderModel();
            $SalesOrder->sales_order_number = SalesOrderModel::newCode();
            $SalesOrder->date = $SalesOnline['date_created'];


            // find customer by email or create new customer
            $customer = CustomerModel::where('email', $SalesOnline['billing']['email'])->first();
            if (!$customer) {
                $customer = new CustomerModel();
                $customer->name = $SalesOnline['billing']['first_name'] . ' ' . $SalesOnline['billing']['last_name'];
                $customer->email = $SalesOnline['billing']['email'];
                $customer->address = $SalesOnline['billing']['address_1'];
                $customer->contact = $SalesOnline['billing']['phone'];
                $customer->latitude = 0;
                $customer->longitude = 0;
                $customer->save();
            }
            $SalesOrder->customer_id = $customer->id;
            $SalesOrder->vehicle_id = $request->vehicle_id;
            $SalesOrder->distributor_shop_id = $request->distributor_id;
            $SalesOrder->distributor_shop_technician_id = $request->technician_id;

            // find payment method by name or create new payment method
            $paymentMethod = PaymentMethodModel::where('name', $SalesOnline['payment_method'])->first();
            if (!$paymentMethod) {
                $paymentMethod = new PaymentMethodModel();
                $paymentMethod->name = $SalesOnline['payment_method_title'];
                $paymentMethod->save();
            }

            $SalesOrder->payment_method_id = $paymentMethod->id;
            $SalesOrder->payment_status = $SalesOnline['status'];
            $SalesOrder->discount = 0;
            $SalesOrder->discount_price = 0;
            $SalesOrder->subtotal = 0;
            $SalesOrder->total = $SalesOnline['total'];
            $SalesOrder->address = $SalesOnline['billing']['address_1'];
            $SalesOrder->source_id = $SalesOnlineID;
            $SalesOrder->source_platform = 'woocommerce';
            $SalesOrder->save();

            // save sales order items
            $salesOrderItems = [];
            foreach ($SalesOnline['line_items'] as $item) {
                // find product by name or cancel the order
                $product = BatteryModel::where('name', $item['product']['name'])->first();
                if (!$product) {
                    DB::rollBack();
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Product ' . $item['product']['name'] . ' not found',
                    ]);
                }

                $salesOrderItems[] = [
                    'sales_order_id' => $SalesOrder->id,
                    'battery_id' => $product->id,
                    'battery_name' => $product->name,
                    'battery_price_retail' => $item['product']['price'],
                    'tax' => 0,
                    'tax_price' => 0,
                    'discout' => 0,
                    'discount_price' => 0,
                    'price_net' => $item['product']['price'],
                    'quantity' => $item['quantity']
                ];
            }

            $SalesOrder->batteries()->createMany($salesOrderItems);

            DB::commit();
            return response()->json([
                'status' => 'success',
                'message' => 'Sales data saved to Sales Orders successfully',
            ]);
        } catch (\Throwable $th) {
            Log::error($th);
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to save sales data to Sales Orders ' . $th->getMessage(),
            ]);
        }
    }


    /**
     * Retrieve technicians by shop.
     *
     * This method handles the request to get technicians associated with a specific shop.
     * It fetches the technicians from the `DistributorShopTechnicianModel` based on the provided
     * distributor shop ID and returns the data in a JSON response.
     *
     * @param \Illuminate\Http\Request $request The incoming request instance containing the distributor shop ID.
     * @return \Illuminate\Http\JsonResponse The JSON response containing the status, message, and technicians data.
     */
    public function getTechnicianByShop(Request $request)
    {
        $shopID = $request->distributor_id;
        $technicians = DistributorShopTechnicianModel::where('distributor_shop_id', $shopID)->get();
        return response()->json([
            'status' => 'success',
            'message' => 'Technicians data retrieved successfully',
            'data' => $technicians
        ]);
    }

    /**
     * Retrieves all sales from the WooCommerce API.
     *
     * @return array The sales data.
     */
    private function getSalesAll()
    {
        $sales = $this->woocommerce->get('orders');
        return $sales;
    }

    /**
     * Retrieves a specific sale from the WooCommerce API.
     *
     * @param int $id The ID of the sale.
     * @return array The sale data.
     */
    private function getSalesById($id)
    {
        $sales = $this->woocommerce->get('orders/' . $id);
        $sales = json_decode(json_encode($sales), true); // Convert stdClass to array

        // get detail product and add to sales
        foreach ($sales['line_items'] as $key => $item) {
            $product = $this->woocommerce->get('products/' . $item['product_id']);
            $sales['line_items'][$key]['product'] = json_decode(json_encode($product), true); // Convert stdClass to array
        }
        return $sales;
    }
}
