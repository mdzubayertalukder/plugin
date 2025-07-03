<?php

namespace Plugin\Multivendor\Http\Controllers\Seller;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Plugin\TlcommerceCore\Models\Orders;
use Plugin\TlcommerceCore\Repositories\OrderRepository;
use Plugin\TlcommerceCore\Repositories\EcommerceNotification;
use Plugin\TlcommerceCore\Http\Requests\DeliveryStatusUpdateRequest;
use Plugin\TlcommerceCore\Models\OrderHasProducts;

class OrderController extends Controller
{

    public function __construct(public OrderRepository $orderRepository) {}
    /**
     * Will return order list
     * 
     * @param \Illuminate\Http\Request $request
     * @return mixed
     */
    public function ordersList(Request $request)
    {
        $orders = $this->orderRepository->orderList($request, null, 'seller', auth()->user()->id);
        $order_counter = $this->orderRepository->singleSellerOrderCounter(auth()->user()->id);
        return view('plugin/multivendor::seller.dashboard.pages.orders.list')->with(
            [
                'orders' => $orders,
                'order_counter' => $order_counter
            ]
        );
    }
    /**
     * Will return Order status details
     * 
     * @param \Illuminate\Http\Request $request
     * @return mixed
     */
    public function orderStatusDetails(Request $request)
    {
        $order_details = $this->orderRepository->sellerOrderDetails($request['id'], auth()->user()->id);;
        return view('plugin/multivendor::seller.dashboard.pages.orders.status_update_form')->with(
            [
                'order_details' => $order_details
            ]
        );
    }
    /**
     * Will return seller order details
     * 
     * @param Int $order_id
     */
    public function orderDetails($id)
    {
        $order_details = $this->orderRepository->sellerOrderDetails($id, auth()->user()->id);
        return view('plugin/multivendor::seller.dashboard.pages.orders.details')->with(
            [
                'order_details' => $order_details
            ]
        );
    }
    /**
     * Will update order status
     * 
     * @param DeliveryStatusUpdateRequest $request
     * @return mixed
     */
    public function updateOrderStatus(DeliveryStatusUpdateRequest $request)
    {
        $res = $this->orderRepository->updateOrderStatus($request);
        if ($res) {
            //Send notification to admin
            EcommerceNotification::sendSellerOrderStatusNotificationToAdmin($request['order_id']);
            return response()->json(
                [
                    'success' => true,
                ]
            );
        } else {
            return response()->json(
                [
                    'success' => false,
                ]
            );
        }
    }

    /**
     * Will accept order 
     * 
     * @param \Illuminate\Http\Request $request
     * @return void
     */
    public function acceptOrder(Request $request)
    {
        $res = $this->orderRepository->acceptOrder($request['order_id'], auth()->user()->id);
        if ($res) {
            //Send notification to admin
            EcommerceNotification::sendSellerOrderStatusNotificationToAdmin($request['order_id'], translate('Seller accepted an order'));
            toastNotification('success', translate('Order accept successfully'));
        } else {
            toastNotification('error', translate('Order accept failed'));
        }
        return redirect()->back();
    }
    /**
     * Will cancel an order
     * 
     * @param \Illuminate\Http\Request $request
     * @return mixed
     */
    public function cancelOrder(Request $request)
    {

        $res = $this->orderRepository->cancelOrder($request['order_id'], auth()->user()->id);
        if ($res) {
            //Send notification to admin
            EcommerceNotification::sendSellerOrderStatusNotificationToAdmin($request['order_id'], translate('Seller cancel an order'));
            toastNotification('success', translate('Order cancelled successfully'));
        } else {
            toastNotification('error', translate('Order cancel failed'));
        }
        return redirect()->back();
    }
    /**
     * Will cancel an item
     * 
     * @param \Illuminate\Http\Request $request
     * @return mixed 
     */
    public function cancelOrderItem(Request $request)
    {
        $res = $this->orderRepository->changeOrderItemStatus($request['item_id'], $request['order_id'], config('tlecommercecore.order_delivery_status.cancelled'));
        if ($res) {
            //Send notification to admin
            EcommerceNotification::sendSellerOrderStatusNotificationToAdmin($request['order_id'], translate('Seller cancel item of an order'));
            toastNotification('success', translate('Item has been cancelled'));
        } else {
            toastNotification('error', translate('Action failed'));
        }

        return redirect()->back();
    }
    /**
     * Will return seller sales chart report data
     * 
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse 
     */
    public function salesChartReport(Request $request)
    {
        $current_locale = getLocale();
        if ($current_locale == 'sa') {
            $current_locale = 'ar';
        }

        if ($current_locale == 'bd') {
            $current_locale = 'bn';
        }
        Carbon::setLocale($current_locale);


        if ($request['type'] == 'monthly') {
            $times = array();
            $sales = array();
            for ($i = 11; $i >= 0; $i--) {
                $first_day_of_month = Carbon::today()->startOfMonth()->subMonth($i);
                $last_day_of_month = Carbon::today()->endOfMonth()->subMonth($i);

                $total_sales = OrderHasProducts::whereBetween(
                    'created_at',
                    [$first_day_of_month, $last_day_of_month]
                )->select('unit_price', 'quantity', 'seller_id')
                    ->where('seller_id', auth()->user()->id)
                    ->get()
                    ->sum(function ($sale) {
                        return $sale->unit_price * $sale->quantity;
                    });
                array_push($times, $first_day_of_month->shortMonthName);
                array_push($sales, $total_sales);
            }
            return response()->json(
                [
                    'success' => true,
                    'times' => $times,
                    'sales' => $sales,
                ]
            );
        }

        if ($request['type'] == 'daily') {
            $times = array();
            $sales = array();
            for ($i = 29; $i >= 0; $i--) {

                $day = Carbon::today()->endOfDay()->subDay($i);
                $total_sales = OrderHasProducts::whereDate('created_at', $day)
                    ->where('seller_id', auth()->user()->id)
                    ->select('unit_price', 'quantity', 'seller_id')
                    ->get()
                    ->sum(function ($sale) {
                        return $sale->unit_price * $sale->quantity;
                    });
                array_push($sales, $total_sales);

                array_push($times, $day->translatedFormat('d M'));
            }

            return response()->json(
                [
                    'success' => true,
                    'times' => $times,
                    'sales' => $sales,
                ]
            );
        }
    }
}
