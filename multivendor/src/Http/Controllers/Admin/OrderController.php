<?php

namespace Plugin\Multivendor\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Plugin\TlcommerceCore\Repositories\OrderRepository;

class OrderController extends Controller
{

    public function __construct(public OrderRepository $orderRepository)
    {
    }
    /**
     * Will return seller orders
     * 
     * @param \Illuminate\Http\Request $request
     */
    public function sellerOrders(Request $request)
    {
        $orders = $this->orderRepository->orderList($request, config('tlecommercecore.order_type.home_delivery'), 'seller', null);
        $order_counter = $this->orderRepository->sellerOrderCounter(config('tlecommercecore.order_type.home_delivery'));
        return view('plugin/multivendor::admin.orders.index')->with(
            [
                'orders' => $orders,
                'order_counter' => $order_counter
            ]
        );
    }
}
