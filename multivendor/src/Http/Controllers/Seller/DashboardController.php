<?php

namespace Plugin\Multivendor\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use Plugin\Multivendor\Models\SellerEarnings;
use Plugin\TlcommerceCore\Models\OrderHasProducts;

class DashboardController extends Controller
{
    /**
     * Will return seller dashboard details
     */
    public function dashboard()
    {
        $pending_earnings = SellerEarnings::where('seller_id', auth()->user()->id)
            ->where('status', config('tlecommercecore.seller_earning_status.pending'))
            ->select('id', 'seller_id', 'order_package_id')
            ->get();
        if ($pending_earnings != null) {
            foreach ($pending_earnings as $earning) {
                $order_product_details = OrderHasProducts::where('id', $earning->order_package_id)
                    ->select('id', 'return_status', 'payment_status', 'delivery_status', 'delivery_time')
                    ->first();
                if ($order_product_details != null) {
                    if ($order_product_details->canReturn() != config('settings.general_status.active') && $order_product_details->return_status != config('tlecommercecore.product_return_status.returned') && $order_product_details->return_status != config('tlecommercecore.product_return_status.processing')) {
                        $updatable_earning = SellerEarnings::where('id', $earning->id)->first();
                        $updatable_earning->status = config('tlecommercecore.seller_earning_status.approve');
                        $updatable_earning->save();
                    }
                }
            }
        }

        return view('plugin/multivendor::seller.dashboard.pages.dashboard');
    }
}
