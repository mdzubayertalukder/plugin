<?php

namespace Plugin\PickupPoint\Http\Repositories;

use Illuminate\Support\Facades\DB;

class OrderRepotitory
{
    /**
     * Will return order counter
     * 
     * @param String $shipping_type
     * @return Array
     * 
     */
    public function orderCounter($shipping_type)
    {
        try {

            $data = [
                DB::raw('GROUP_CONCAT(DISTINCT(tl_com_ordered_products.order_id)) as order_id'),
            ];
            $temp = [];
            $temp['all'] = DB::table('tl_com_ordered_products')
                ->leftJoin('tl_com_orders', 'tl_com_orders.id', '=', 'tl_com_ordered_products.order_id')
                ->groupBy('tl_com_ordered_products.order_id')
                ->where('tl_com_orders.shipping_type', $shipping_type)
                ->select($data)->get()->count();

            $temp['pending'] = DB::table('tl_com_ordered_products')
                ->leftJoin('tl_com_orders', 'tl_com_orders.id', '=', 'tl_com_ordered_products.order_id')
                ->groupBy('tl_com_ordered_products.order_id')
                ->select($data)
                ->where('tl_com_orders.shipping_type', $shipping_type)->where('tl_com_ordered_products.delivery_status', config('tlecommercecore.order_delivery_status.pending'))->get()->count();

            $temp['delivered'] = DB::table('tl_com_ordered_products')
                ->leftJoin('tl_com_orders', 'tl_com_orders.id', '=', 'tl_com_ordered_products.order_id')
                ->groupBy('tl_com_ordered_products.order_id')
                ->select($data)
                ->where('tl_com_orders.shipping_type', $shipping_type)->where('tl_com_ordered_products.delivery_status', config('tlecommercecore.order_delivery_status.delivered'))->get()->count();

            $temp['processing'] = DB::table('tl_com_ordered_products')
                ->leftJoin('tl_com_orders', 'tl_com_orders.id', '=', 'tl_com_ordered_products.order_id')
                ->groupBy('tl_com_ordered_products.order_id')
                ->select($data)
                ->where('tl_com_orders.shipping_type', $shipping_type)->where('tl_com_ordered_products.delivery_status', config('tlecommercecore.order_delivery_status.processing'))->get()->count();

            $temp['shipped'] = DB::table('tl_com_ordered_products')
                ->leftJoin('tl_com_orders', 'tl_com_orders.id', '=', 'tl_com_ordered_products.order_id')
                ->groupBy('tl_com_ordered_products.order_id')
                ->select($data)
                ->where('tl_com_orders.shipping_type', $shipping_type)->where('tl_com_ordered_products.delivery_status', config('tlecommercecore.order_delivery_status.shipped'))->get()->count();

            $temp['cancelled'] = DB::table('tl_com_ordered_products')
                ->leftJoin('tl_com_orders', 'tl_com_orders.id', '=', 'tl_com_ordered_products.order_id')
                ->groupBy('tl_com_ordered_products.order_id')
                ->select($data)
                ->where('tl_com_orders.shipping_type', $shipping_type)->where('tl_com_ordered_products.delivery_status', config('tlecommercecore.order_delivery_status.cancelled'))->get()->count();

            $temp['paid'] = DB::table('tl_com_ordered_products')
                ->leftJoin('tl_com_orders', 'tl_com_orders.id', '=', 'tl_com_ordered_products.order_id')
                ->groupBy('tl_com_ordered_products.order_id')
                ->select($data)
                ->where('tl_com_orders.shipping_type', $shipping_type)->where('tl_com_ordered_products.payment_status', config('tlecommercecore.order_payment_status.paid'))->get()->count();

            $temp['unpaid'] = DB::table('tl_com_ordered_products')
                ->leftJoin('tl_com_orders', 'tl_com_orders.id', '=', 'tl_com_ordered_products.order_id')
                ->groupBy('tl_com_ordered_products.order_id')
                ->select($data)
                ->where('tl_com_orders.shipping_type', $shipping_type)->where('tl_com_ordered_products.payment_status', config('tlecommercecore.order_payment_status.unpaid'))->get()->count();
            return $temp;
        } catch (\Exception $e) {
            return null;
        } catch (\Error $e) {
            return null;
        }
    }
    /**
     * Will return order list
     * 
     * @param $shipping_type
     * @param Object $request
     * @return Collections
     */
    public function orderList($request, $shipping_type = null)
    {
        try {
            $data = [
                'tl_com_orders.id',
                DB::raw('GROUP_CONCAT(DISTINCT(tl_com_orders.total_payable_amount)) as total_payable_amount'),
                DB::raw('GROUP_CONCAT(DISTINCT(tl_com_orders.order_code)) as order_code'),
                DB::raw('GROUP_CONCAT(DISTINCT(tl_com_orders.created_at)) as created_at'),
                DB::raw('GROUP_CONCAT(DISTINCT(tl_com_orders.read_at)) as read_at'),
                DB::raw('GROUP_CONCAT(DISTINCT(tl_com_orders.delivery_status)) as delivery_status'),
                DB::raw('GROUP_CONCAT(DISTINCT(tl_com_orders.payment_status)) as payment_status'),
                DB::raw('GROUP_CONCAT(DISTINCT(tl_com_customers.name)) as customer_name'),
                DB::raw('GROUP_CONCAT(DISTINCT(tl_com_guest_customer.name)) as guest_customer'),
                DB::raw('GROUP_CONCAT(DISTINCT(tl_com_customers.id)) as customer_id'),
                DB::raw('GROUP_CONCAT(DISTINCT(tl_pick_up_points.name)) as pickup_point_name'),
                DB::raw('sum(tl_com_ordered_products.quantity) as total_product'),
            ];



            $query = DB::table('tl_com_orders')
                ->leftJoin('tl_com_guest_customer', 'tl_com_guest_customer.order_id', '=', 'tl_com_orders.id')
                ->leftjoin('tl_com_ordered_products', 'tl_com_ordered_products.order_id', '=', 'tl_com_orders.id')
                ->leftjoin('tl_com_customers', 'tl_com_customers.id', '=', 'tl_com_orders.customer_id')
                ->leftjoin('tl_pick_up_points', 'tl_pick_up_points.id', '=', 'tl_com_orders.pickup_point_id')
                ->groupBy('tl_com_orders.id')
                ->select($data);

            if ($shipping_type != null) {

                $query = $query->where('shipping_type', $shipping_type);
            }
            if ($request->has('payment_status') && $request['payment_status'] != null) {
                $query = $query->where('tl_com_orders.payment_status', $request['payment_status']);
            }

            if ($request->has('delivery_status') && $request['delivery_status'] != null) {
                $query = $query->where('tl_com_ordered_products.delivery_status', $request['delivery_status']);
            }

            if ($request->has('order_code') && $request['order_code'] != null) {
                $query = $query->where('tl_com_orders.order_code', "like", "%" . $request['order_code'] . "%");
            }

            if ($request->has('order_date') && $request['order_date'] != null) {
                $date_range = explode(' to ', $request['order_date']);
                $query = $query->whereBetween('tl_com_orders.created_at', $date_range);
            }

            if ($request->has('pick_up_point') && $request['pick_up_point'] != null) {
                $query = $query->where('tl_com_orders.pickup_point_id', $request['pick_up_point']);
            }

            $per_page = $request->has('per_page') && $request['per_page'] != null ? $request['per_page'] : 10;

            if ($per_page != null && $per_page == 'all') {
                $products = $query->orderBy('tl_com_orders.id', 'DESC')
                    ->paginate($query->get()->count())
                    ->withQueryString();
            } else {
                $products = $query->orderBy('tl_com_orders.id', 'DESC')
                    ->paginate($per_page)
                    ->withQueryString();
            }

            return $products;
        } catch (\Exception $e) {

            return [];
        }
    }
}
