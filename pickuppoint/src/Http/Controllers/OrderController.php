<?php

namespace Plugin\PickupPoint\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Plugin\PickupPoint\Http\Repositories\OrderRepotitory;
use Plugin\PickupPoint\Http\Repositories\PickupPointRepository;

class OrderController extends Controller
{

    protected $order_repository;
    protected $pickup_point_repositoty;
    public function __construct(OrderRepotitory $order_repository, PickupPointRepository $pickup_point_repositoty)
    {
        isActiveParentPlugin('tlecommercecore');

        $this->order_repository = $order_repository;
        $this->pickup_point_repositoty = $pickup_point_repositoty;
    }
    /**
     * Will return pickup point orders
     * 
     * @param \Illuminate\Http\Request $request
     * @return mixed
     */
    public function orderList(Request $request)
    {
        $pick_up_points = $this->pickup_point_repositoty->getActivePickupPoint($request);
        $orders = $this->order_repository->orderList($request, config('tlecommercecore.order_type.local_pickup'));
        $order_counter = $this->order_repository->orderCounter(config('tlecommercecore.order_type.local_pickup'));
        return view('plugin/pickuppoint::pages.orders.orders')->with(
            [
                'orders' => $orders,
                'pick_up_points' => $pick_up_points,
                'order_counter' => $order_counter
            ]
        );
    }
}
