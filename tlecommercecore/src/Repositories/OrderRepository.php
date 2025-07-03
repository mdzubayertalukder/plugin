<?php

namespace Plugin\TlcommerceCore\Repositories;

use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use NunoMaduro\Collision\Adapters\Phpunit\State;
use Plugin\TlcommerceCore\Models\Cities;
use Plugin\TlcommerceCore\Models\Orders;
use Plugin\TlcommerceCore\Models\States;
use Plugin\TlcommerceCore\Models\Product;
use Plugin\TlcommerceCore\Models\TaxRate;
use Plugin\Carrier\Models\ShippingCarrier;
use Plugin\TlcommerceCore\Models\CartItem;
use Plugin\TlcommerceCore\Models\Customers;
use Plugin\TlcommerceCore\Models\BankPayment;
use Plugin\TlcommerceCore\Models\ShippingRate;
use Plugin\TlcommerceCore\Models\ShippingZone;
use Plugin\TlcommerceCore\Models\ProductReview;
use Plugin\TlcommerceCore\Models\GuestCustomers;
use Plugin\TlcommerceCore\Models\PaymentMethods;
use Plugin\TlcommerceCore\Models\CustomerAddress;
use Plugin\TlcommerceCore\Models\OrderHasProducts;
use Plugin\TlcommerceCore\Models\ShippingZoneCities;
use Plugin\TlcommerceCore\Models\SingleProductPrice;
use Plugin\TlcommerceCore\Models\ProductShippingInfo;
use Plugin\TlcommerceCore\Models\VariantProductPrice;
use Plugin\TlcommerceCore\Models\OrderPackageTracking;
use Plugin\TlcommerceCore\Models\ShippingZoneHasTaxes;
use Plugin\TlcommerceCore\Models\CollectionHasProducts;
use Plugin\TlcommerceCore\Models\ShippingProfileProducts;
use Plugin\TlcommerceCore\Repositories\SettingsRepository;
use Plugin\TlcommerceCore\Repositories\EcommerceNotification;
use Plugin\TlcommerceCore\Models\LocationWiseShippingRateCity;
use Plugin\TlcommerceCore\Http\Resources\ShippingRateCollection;
use Plugin\TlcommerceCore\Models\LocationWiseShippingRateStates;
use Plugin\TlcommerceCore\Models\LocationWiseShippingRateCountry;
use Plugin\TlcommerceCore\Http\Resources\SingleShippingRateCollection;
use Plugin\TlcommerceCore\Models\Country;
use Plugin\TlcommerceCore\Models\LocationWiseShippingRate;

class OrderRepository
{
    /**
     * Will return checkout configuration
     *
     */
    public function checkoutConfiguration()
    {
        try {
            $data = [
                'return_order_time_limit_unit' => SettingsRepository::getEcommerceSetting('return_order_time_limit_unit'),
                'return_order_time_limit' => SettingsRepository::getEcommerceSetting('return_order_time_limit'),
                'cancel_order_time_limit_unit' => SettingsRepository::getEcommerceSetting('cancel_order_time_limit_unit'),
                'cancel_order_time_limit' => SettingsRepository::getEcommerceSetting('cancel_order_time_limit'),
                'enable_guest_checkout'                   => SettingsRepository::getEcommerceSetting('enable_guest_checkout'),
                'enable_billing_address'                  => SettingsRepository::getEcommerceSetting('enable_billing_address'),
                'use_shipping_address_as_billing_address' => SettingsRepository::getEcommerceSetting('use_shipping_address_as_billing_address'),
                'create_account_in_guest_checkout'        => SettingsRepository::getEcommerceSetting('create_account_in_guest_checkout'),
                'enable_tax_in_checkout'                  => SettingsRepository::getEcommerceSetting('enable_tax_in_checkout'),
                'enable_coupon_in_checkout'               => SettingsRepository::getEcommerceSetting('enable_coupon_in_checkout'),
                'enable_multiple_coupon_in_checkout'      => SettingsRepository::getEcommerceSetting('enable_multiple_coupon_in_checkout'),
                'enable_minumun_order_amount'             => SettingsRepository::getEcommerceSetting('enable_minumun_order_amount'),
                'enable_wallet_in_checkout'               => SettingsRepository::getEcommerceSetting('enable_wallet_in_checkout'),
                'enable_order_note_in_checkout'           => SettingsRepository::getEcommerceSetting('enable_order_note_in_checkout'),
                'enable_document_in_checkout'             => SettingsRepository::getEcommerceSetting('enable_document_in_checkout'),
                'enable_carrier_in_checkout'              => SettingsRepository::getEcommerceSetting('enable_carrier_in_checkout'),
                'min_order_amount'                        => SettingsRepository::getEcommerceSetting('min_order_amount'),
                'enable_pickuppoint_in_checkout'          => SettingsRepository::getEcommerceSetting('enable_pickuppoint_in_checkout'),
                'is_active_wallet'                        => isActivePluging('wallet') ? config('settings.general_status.active') : config('settings.general_status.in_active'),
                'is_active_pickuppoint'                   => isActivePluging('pickuppoint') ? config('settings.general_status.active') : config('settings.general_status.in_active'),
                'is_active_coupon'                        => isActivePluging('coupon') ? config('settings.general_status.active') : config('settings.general_status.in_active'),
            ];

            return $data;
        } catch (\Exception $e) {
            return NULL;
        } catch (\Error $e) {
            return NULL;
        }
    }
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
                ->leftjoin('tl_users', 'tl_users.id', '=', 'tl_com_ordered_products.seller_id')
                ->leftJoin('tl_com_orders', 'tl_com_orders.id', '=', 'tl_com_ordered_products.order_id')
                ->groupBy('tl_com_ordered_products.order_id')
                ->where('tl_users.user_type', config('tlecommercecore.user_type.admin'))
                ->where('tl_com_orders.shipping_type', $shipping_type)
                ->select($data)->get()->count();

            $temp['pending'] = DB::table('tl_com_ordered_products')
                ->leftjoin('tl_users', 'tl_users.id', '=', 'tl_com_ordered_products.seller_id')
                ->leftJoin('tl_com_orders', 'tl_com_orders.id', '=', 'tl_com_ordered_products.order_id')
                ->groupBy('tl_com_ordered_products.order_id')
                ->select($data)
                ->where('tl_users.user_type', config('tlecommercecore.user_type.admin'))
                ->where('tl_com_orders.shipping_type', $shipping_type)->where('tl_com_ordered_products.delivery_status', config('tlecommercecore.order_delivery_status.pending'))->get()->count();

            $temp['delivered'] = DB::table('tl_com_ordered_products')
                ->leftjoin('tl_users', 'tl_users.id', '=', 'tl_com_ordered_products.seller_id')
                ->leftJoin('tl_com_orders', 'tl_com_orders.id', '=', 'tl_com_ordered_products.order_id')
                ->groupBy('tl_com_ordered_products.order_id')
                ->select($data)
                ->where('tl_users.user_type', config('tlecommercecore.user_type.admin'))
                ->where('tl_com_orders.shipping_type', $shipping_type)->where('tl_com_ordered_products.delivery_status', config('tlecommercecore.order_delivery_status.delivered'))->get()->count();

            $temp['processing'] = DB::table('tl_com_ordered_products')
                ->leftjoin('tl_users', 'tl_users.id', '=', 'tl_com_ordered_products.seller_id')
                ->leftJoin('tl_com_orders', 'tl_com_orders.id', '=', 'tl_com_ordered_products.order_id')
                ->groupBy('tl_com_ordered_products.order_id')
                ->select($data)
                ->where('tl_users.user_type', config('tlecommercecore.user_type.admin'))
                ->where('tl_com_orders.shipping_type', $shipping_type)->where('tl_com_ordered_products.delivery_status', config('tlecommercecore.order_delivery_status.processing'))->get()->count();

            $temp['ready_to_ship'] = DB::table('tl_com_ordered_products')
                ->leftjoin('tl_users', 'tl_users.id', '=', 'tl_com_ordered_products.seller_id')
                ->leftJoin('tl_com_orders', 'tl_com_orders.id', '=', 'tl_com_ordered_products.order_id')
                ->groupBy('tl_com_ordered_products.order_id')
                ->select($data)
                ->where('tl_users.user_type', config('tlecommercecore.user_type.admin'))
                ->where('tl_com_orders.shipping_type', $shipping_type)->where('tl_com_ordered_products.delivery_status', config('tlecommercecore.order_delivery_status.ready_to_ship'))->get()->count();

            $temp['shipped'] = DB::table('tl_com_ordered_products')
                ->leftjoin('tl_users', 'tl_users.id', '=', 'tl_com_ordered_products.seller_id')
                ->leftJoin('tl_com_orders', 'tl_com_orders.id', '=', 'tl_com_ordered_products.order_id')
                ->groupBy('tl_com_ordered_products.order_id')
                ->select($data)
                ->where('tl_users.user_type', config('tlecommercecore.user_type.admin'))
                ->where('tl_com_orders.shipping_type', $shipping_type)->where('tl_com_ordered_products.delivery_status', config('tlecommercecore.order_delivery_status.shipped'))->get()->count();

            $temp['cancelled'] = DB::table('tl_com_ordered_products')
                ->leftjoin('tl_users', 'tl_users.id', '=', 'tl_com_ordered_products.seller_id')
                ->leftJoin('tl_com_orders', 'tl_com_orders.id', '=', 'tl_com_ordered_products.order_id')
                ->groupBy('tl_com_ordered_products.order_id')
                ->select($data)
                ->where('tl_users.user_type', config('tlecommercecore.user_type.admin'))
                ->where('tl_com_orders.shipping_type', $shipping_type)->where('tl_com_ordered_products.delivery_status', config('tlecommercecore.order_delivery_status.cancelled'))->get()->count();

            $temp['paid'] = DB::table('tl_com_ordered_products')
                ->leftjoin('tl_users', 'tl_users.id', '=', 'tl_com_ordered_products.seller_id')
                ->leftJoin('tl_com_orders', 'tl_com_orders.id', '=', 'tl_com_ordered_products.order_id')
                ->groupBy('tl_com_ordered_products.order_id')
                ->select($data)
                ->where('tl_users.user_type', config('tlecommercecore.user_type.admin'))
                ->where('tl_com_orders.shipping_type', $shipping_type)->where('tl_com_ordered_products.payment_status', config('tlecommercecore.order_payment_status.paid'))->get()->count();

            $temp['unpaid'] = DB::table('tl_com_ordered_products')
                ->leftjoin('tl_users', 'tl_users.id', '=', 'tl_com_ordered_products.seller_id')
                ->leftJoin('tl_com_orders', 'tl_com_orders.id', '=', 'tl_com_ordered_products.order_id')
                ->groupBy('tl_com_ordered_products.order_id')
                ->select($data)
                ->where('tl_users.user_type', config('tlecommercecore.user_type.admin'))
                ->where('tl_com_orders.shipping_type', $shipping_type)->where('tl_com_ordered_products.payment_status', config('tlecommercecore.order_payment_status.unpaid'))->get()->count();
            return $temp;
        } catch (\Exception $e) {
            return null;
        } catch (\Error $e) {
            return null;
        }
    }
    /**
     * Will return seller order counter
     *
     * @param String $shipping_type
     * @return Array
     *
     */
    public function sellerOrderCounter($shipping_type)
    {
        try {

            $data = [
                DB::raw('GROUP_CONCAT(DISTINCT(tl_com_ordered_products.order_id)) as order_id'),
            ];
            $temp = [];
            $temp['all'] = DB::table('tl_com_ordered_products')
                ->leftJoin('tl_com_orders', 'tl_com_orders.id', '=', 'tl_com_ordered_products.order_id')
                ->leftjoin('tl_users', 'tl_users.id', '=', 'tl_com_ordered_products.seller_id')
                ->groupBy('tl_com_ordered_products.order_id')
                ->where('tl_users.user_type', config('tlecommercecore.user_type.seller'))
                ->where('tl_com_orders.shipping_type', $shipping_type)
                ->select($data)->get()->count();

            $temp['pending'] = DB::table('tl_com_ordered_products')
                ->leftJoin('tl_com_orders', 'tl_com_orders.id', '=', 'tl_com_ordered_products.order_id')
                ->leftjoin('tl_users', 'tl_users.id', '=', 'tl_com_ordered_products.seller_id')
                ->groupBy('tl_com_ordered_products.order_id')
                ->where('tl_users.user_type', config('tlecommercecore.user_type.seller'))
                ->select($data)
                ->where('tl_com_orders.shipping_type', $shipping_type)->where('tl_com_ordered_products.delivery_status', config('tlecommercecore.order_delivery_status.pending'))->get()->count();

            $temp['delivered'] = DB::table('tl_com_ordered_products')
                ->leftJoin('tl_com_orders', 'tl_com_orders.id', '=', 'tl_com_ordered_products.order_id')
                ->leftjoin('tl_users', 'tl_users.id', '=', 'tl_com_ordered_products.seller_id')
                ->groupBy('tl_com_ordered_products.order_id')
                ->select($data)
                ->where('tl_users.user_type', config('tlecommercecore.user_type.seller'))
                ->where('tl_com_orders.shipping_type', $shipping_type)->where('tl_com_ordered_products.delivery_status', config('tlecommercecore.order_delivery_status.delivered'))->get()->count();

            $temp['processing'] = DB::table('tl_com_ordered_products')
                ->leftJoin('tl_com_orders', 'tl_com_orders.id', '=', 'tl_com_ordered_products.order_id')
                ->leftjoin('tl_users', 'tl_users.id', '=', 'tl_com_ordered_products.seller_id')
                ->groupBy('tl_com_ordered_products.order_id')
                ->select($data)
                ->where('tl_users.user_type', config('tlecommercecore.user_type.seller'))
                ->where('tl_com_orders.shipping_type', $shipping_type)->where('tl_com_ordered_products.delivery_status', config('tlecommercecore.order_delivery_status.processing'))->get()->count();

            $temp['ready_to_ship'] = DB::table('tl_com_ordered_products')
                ->leftJoin('tl_com_orders', 'tl_com_orders.id', '=', 'tl_com_ordered_products.order_id')
                ->leftjoin('tl_users', 'tl_users.id', '=', 'tl_com_ordered_products.seller_id')
                ->groupBy('tl_com_ordered_products.order_id')
                ->select($data)
                ->where('tl_users.user_type', config('tlecommercecore.user_type.seller'))
                ->where('tl_com_orders.shipping_type', $shipping_type)->where('tl_com_ordered_products.delivery_status', config('tlecommercecore.order_delivery_status.ready_to_ship'))->get()->count();

            $temp['shipped'] = DB::table('tl_com_ordered_products')
                ->leftJoin('tl_com_orders', 'tl_com_orders.id', '=', 'tl_com_ordered_products.order_id')
                ->leftjoin('tl_users', 'tl_users.id', '=', 'tl_com_ordered_products.seller_id')
                ->groupBy('tl_com_ordered_products.order_id')
                ->select($data)
                ->where('tl_users.user_type', config('tlecommercecore.user_type.seller'))
                ->where('tl_com_orders.shipping_type', $shipping_type)->where('tl_com_ordered_products.delivery_status', config('tlecommercecore.order_delivery_status.shipped'))->get()->count();

            $temp['cancelled'] = DB::table('tl_com_ordered_products')
                ->leftJoin('tl_com_orders', 'tl_com_orders.id', '=', 'tl_com_ordered_products.order_id')
                ->leftjoin('tl_users', 'tl_users.id', '=', 'tl_com_ordered_products.seller_id')
                ->groupBy('tl_com_ordered_products.order_id')
                ->select($data)
                ->where('tl_users.user_type', config('tlecommercecore.user_type.seller'))
                ->where('tl_com_orders.shipping_type', $shipping_type)->where('tl_com_ordered_products.delivery_status', config('tlecommercecore.order_delivery_status.cancelled'))->get()->count();

            $temp['paid'] = DB::table('tl_com_ordered_products')
                ->leftJoin('tl_com_orders', 'tl_com_orders.id', '=', 'tl_com_ordered_products.order_id')
                ->leftjoin('tl_users', 'tl_users.id', '=', 'tl_com_ordered_products.seller_id')
                ->groupBy('tl_com_ordered_products.order_id')
                ->select($data)
                ->where('tl_users.user_type', config('tlecommercecore.user_type.seller'))
                ->where('tl_com_orders.shipping_type', $shipping_type)->where('tl_com_ordered_products.payment_status', config('tlecommercecore.order_payment_status.paid'))->get()->count();

            $temp['unpaid'] = DB::table('tl_com_ordered_products')
                ->leftJoin('tl_com_orders', 'tl_com_orders.id', '=', 'tl_com_ordered_products.order_id')
                ->leftjoin('tl_users', 'tl_users.id', '=', 'tl_com_ordered_products.seller_id')
                ->groupBy('tl_com_ordered_products.order_id')
                ->select($data)
                ->where('tl_users.user_type', config('tlecommercecore.user_type.seller'))
                ->where('tl_com_orders.shipping_type', $shipping_type)->where('tl_com_ordered_products.payment_status', config('tlecommercecore.order_payment_status.unpaid'))->get()->count();
            return $temp;
        } catch (\Exception $e) {
            return null;
        } catch (\Error $e) {
            return null;
        }
    }
    /**
     * Will return single seller order counter
     *
     * @param Int $seller_id
     * @return Array
     *
     */
    public function singleSellerOrderCounter($seller_id)
    {
        try {

            $data = [
                DB::raw('GROUP_CONCAT(DISTINCT(tl_com_ordered_products.order_id)) as order_id'),
            ];
            $temp = [];
            $temp['all'] = DB::table('tl_com_ordered_products')
                ->leftJoin('tl_com_orders', 'tl_com_orders.id', '=', 'tl_com_ordered_products.order_id')
                ->groupBy('tl_com_ordered_products.order_id')
                ->whereNotNull('tl_com_ordered_products.seller_id')
                ->where('tl_com_ordered_products.seller_id', $seller_id)
                ->select($data)->get()->count();

            $temp['pending'] = DB::table('tl_com_ordered_products')
                ->leftJoin('tl_com_orders', 'tl_com_orders.id', '=', 'tl_com_ordered_products.order_id')
                ->groupBy('tl_com_ordered_products.order_id')
                ->where('tl_com_ordered_products.seller_id', $seller_id)
                ->select($data)
                ->where('tl_com_ordered_products.delivery_status', config('tlecommercecore.order_delivery_status.pending'))
                ->get()
                ->count();

            $temp['delivered'] = DB::table('tl_com_ordered_products')
                ->leftJoin('tl_com_orders', 'tl_com_orders.id', '=', 'tl_com_ordered_products.order_id')
                ->groupBy('tl_com_ordered_products.order_id')
                ->where('tl_com_ordered_products.seller_id', $seller_id)
                ->select($data)
                ->where('tl_com_ordered_products.delivery_status', config('tlecommercecore.order_delivery_status.delivered'))
                ->get()
                ->count();

            $temp['processing'] = DB::table('tl_com_ordered_products')
                ->leftJoin('tl_com_orders', 'tl_com_orders.id', '=', 'tl_com_ordered_products.order_id')
                ->groupBy('tl_com_ordered_products.order_id')
                ->where('tl_com_ordered_products.seller_id', $seller_id)
                ->select($data)
                ->where('tl_com_ordered_products.delivery_status', config('tlecommercecore.order_delivery_status.processing'))
                ->get()
                ->count();

            $temp['ready_to_ship'] = DB::table('tl_com_ordered_products')
                ->leftJoin('tl_com_orders', 'tl_com_orders.id', '=', 'tl_com_ordered_products.order_id')
                ->groupBy('tl_com_ordered_products.order_id')
                ->where('tl_com_ordered_products.seller_id', $seller_id)
                ->select($data)
                ->where('tl_com_ordered_products.delivery_status', config('tlecommercecore.order_delivery_status.ready_to_ship'))
                ->get()
                ->count();

            $temp['shipped'] = DB::table('tl_com_ordered_products')
                ->leftJoin('tl_com_orders', 'tl_com_orders.id', '=', 'tl_com_ordered_products.order_id')
                ->groupBy('tl_com_ordered_products.order_id')
                ->where('tl_com_ordered_products.seller_id', $seller_id)
                ->select($data)
                ->where('tl_com_ordered_products.delivery_status', config('tlecommercecore.order_delivery_status.shipped'))
                ->get()
                ->count();

            $temp['cancelled'] = DB::table('tl_com_ordered_products')
                ->leftJoin('tl_com_orders', 'tl_com_orders.id', '=', 'tl_com_ordered_products.order_id')
                ->groupBy('tl_com_ordered_products.order_id')
                ->where('tl_com_ordered_products.seller_id', $seller_id)
                ->select($data)
                ->where('tl_com_ordered_products.delivery_status', config('tlecommercecore.order_delivery_status.cancelled'))
                ->get()
                ->count();

            $temp['paid'] = DB::table('tl_com_ordered_products')
                ->leftJoin('tl_com_orders', 'tl_com_orders.id', '=', 'tl_com_ordered_products.order_id')
                ->groupBy('tl_com_ordered_products.order_id')
                ->where('tl_com_ordered_products.seller_id', $seller_id)
                ->select($data)
                ->where('tl_com_ordered_products.payment_status', config('tlecommercecore.order_payment_status.paid'))
                ->get()
                ->count();

            $temp['unpaid'] = DB::table('tl_com_ordered_products')
                ->leftJoin('tl_com_orders', 'tl_com_orders.id', '=', 'tl_com_ordered_products.order_id')
                ->groupBy('tl_com_ordered_products.order_id')
                ->where('tl_com_ordered_products.seller_id', $seller_id)
                ->select($data)
                ->where('tl_com_ordered_products.payment_status', config('tlecommercecore.order_payment_status.unpaid'))
                ->get()
                ->count();
            return $temp;
        } catch (\Exception $e) {
            return null;
        } catch (\Error $e) {
            return null;
        }
    }
    /**
     * will return status wise order counter
     *
     * @param mixed $status
     *
     * @return Array
     */
    public function statusWiseOrderCounter($status = null, $seller_id = null)
    {
        try {
            $data = [
                DB::raw('GROUP_CONCAT(DISTINCT(tl_com_ordered_products.order_id)) as order_id'),
            ];
            $total_order = 0;
            if ($status != null) {
                $query = DB::table('tl_com_ordered_products')
                    ->leftJoin('tl_com_orders', 'tl_com_orders.id', '=', 'tl_com_ordered_products.order_id')
                    ->groupBy('tl_com_ordered_products.order_id')
                    ->select($data)->where('tl_com_ordered_products.delivery_status', $status);

                if ($seller_id != null) {
                    $query = $query->where('tl_com_ordered_products.seller_id', $seller_id);
                }
                $total_order = $query->get()->count();
            } else {
                $query = DB::table('tl_com_ordered_products')
                    ->leftJoin('tl_com_orders', 'tl_com_orders.id', '=', 'tl_com_ordered_products.order_id')
                    ->groupBy('tl_com_ordered_products.order_id')
                    ->select($data);
                if ($seller_id != null) {
                    $query = $query->where('tl_com_ordered_products.seller_id', $seller_id);
                }
                $total_order = $query->get()->count();
            }
            return $total_order;
        } catch (\Exception $e) {
            return 0;
        } catch (\Error $e) {
            return 0;
        }
    }
    /**
     * Upload order required document
     *
     * @param Object $request
     * @return Array
     */
    public function uploadAttachment($request)
    {
        try {
            if ($request->has('attachment_old')) {
                if ($request['attachment_old'] != null) {
                    removeMediaById($request['attachment_old']);
                }
            }
            if ($request->hasFile('attachment')) {
                $file_name_to_store = $request['attachment']->getClientOriginalName();
                $attachment = saveFileInStorage($request['attachment'], false);
            }
            return
                [
                    'file_name' => $file_name_to_store,
                    'file_id' => $attachment,
                ];
        } catch (\Exception $e) {
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
    public function orderList($request, $shipping_type = null, $order_type = "inhouse", $seller_id = null)
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
                DB::raw('GROUP_CONCAT(DISTINCT(tl_com_guest_customer.id)) as guest_customer_id'),
                DB::raw('sum(tl_com_ordered_products.quantity) as total_product'),
            ];

            $query = DB::table('tl_com_orders')
                ->leftJoin('tl_com_guest_customer', 'tl_com_guest_customer.order_id', '=', 'tl_com_orders.id')
                ->leftjoin('tl_com_ordered_products', 'tl_com_ordered_products.order_id', '=', 'tl_com_orders.id')
                ->leftjoin('tl_users', 'tl_users.id', '=', 'tl_com_ordered_products.seller_id')
                ->leftjoin('tl_com_customers', 'tl_com_customers.id', '=', 'tl_com_orders.customer_id')
                ->groupBy('tl_com_orders.id')
                ->select($data);



            if ($order_type != null && $order_type == "inhouse") {
                $query = $query->where('tl_users.user_type', config('tlecommercecore.user_type.admin'));
            }

            if ($order_type != null && $order_type == "seller") {
                $query = $query->where('tl_users.user_type', config('tlecommercecore.user_type.seller'));
            }

            if ($seller_id != null) {
                $query = $query->where('tl_com_ordered_products.seller_id', $seller_id);
            }

            if ($shipping_type != null) {
                $query = $query->where('shipping_type', $shipping_type);
            }
            if ($request->has('payment_status') && $request['payment_status'] != null) {
                $query = $query->where('tl_com_ordered_products.payment_status', $request['payment_status']);
            }

            if ($request->has('delivery_status') && $request['delivery_status'] != null) {
                $query = $query->where('tl_com_ordered_products.delivery_status', $request['delivery_status']);
            }

            if ($request->has('order_code') && $request['order_code'] != null) {
                $query = $query->where('tl_com_orders.order_code', "like", "%" . $request['order_code'] . "%");
            }

            if ($request->has('order_date') && $request['order_date'] != null) {
                $date_range = explode(' to ', $request['order_date']);
                if (sizeof($date_range) > 1) {
                    if ($date_range[0] == $date_range[1]) {
                        $query = $query->where('tl_com_orders.created_at', $date_range[0]);
                    } else {
                        $query = $query->whereBetween('tl_com_orders.created_at', $date_range);
                    }
                }
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
    /**
     * Will return order products
     *
     * @param Int $order_id
     * @return Collection
     */
    public function orderProducts($order_id)
    {
        return OrderHasProducts::where('order_id', $order_id)->get();
    }
    /**
     * Will return customer orders
     *
     * @param Object $request
     * @return Collections
     */
    public function customerOrders($request, $customer_id)
    {
        return Orders::where('customer_id', $customer_id)->orderBy('id', 'DESC')->paginate($request['perPage']);
    }
    /**
     * Will return customer orders
     *
     * @param Int $customer_id
     * @return Collections
     */
    public function customerLatestOrders($customer_id, $limit = 5)
    {
        return Orders::where('customer_id', $customer_id)->orderBy('id', 'DESC')->get()->take($limit);
    }
    /**
     * Will return customer order details
     *
     * @param Int $customer_id
     * @param Int $order_id
     * @return Collection
     */
    public function customerOrderDetails($customer_id, $order_id)
    {
        return  $order_details = Orders::where('id', $order_id)
            ->where('customer_id', $customer_id)
            ->first();

        if ($order_details != null) {
            return $order_details;
        } else {
            return null;
        }
    }
    /**
     * Will return order details
     *
     * @param Int $order_id
     * @return Collection
     */
    public function orderDetails($order_id)
    {
        $order = Orders::findOrFail($order_id);
        $order->read_at = Carbon::now()->format('y-m-d h:i:s');
        $order->save();
        return $order;
    }
    /**
     * Will return seller order details
     *
     * @param Int $order_id
     * @param Int $seller_id
     * @return Collection
     */
    public function sellerOrderDetails($order_id, $seller_id)
    {
        $query = Orders::with(['products' => function ($query) use ($seller_id) {
            $query->where('seller_id', $seller_id);
        }])->where('id', $order_id);

        return $query->first();
    }
    /**
     * Will return order details
     *
     * @param Int $order_code
     * @return Collection
     */
    public function OrderDetailsByOrderCode($order_code)
    {
        return  $order_details = Orders::where('order_code', $order_code)->first();

        if ($order_details != null) {
            return $order_details;
        } else {
            return null;
        }
    }
    /**
     * Will return order details
     *
     * @param Int $order_id
     * @return Collection
     */
    public function OrderDetailsByOrderId($order_id)
    {
        return  $order_details = Orders::where('id', $order_id)->first();

        if ($order_details != null) {
            return $order_details;
        } else {
            return null;
        }
    }
    /**
     * Will return shipping options
     *
     * @param Object $request
     * @return mixed
     */
    public function shippingOptions($request)
    {
        try {

            $allow_free_shipping = true;
            if ($request->coupons) {
                $coupons = json_decode($request['coupons'], true);
                foreach ($coupons as $coupon) {
                    if ($coupon['allow_free_shipping'] == config('settings.general_status.in_active')) {
                        $allow_free_shipping = false;
                        break;
                    }
                }
            }
            //Home Delivery
            if ($request->has('shipping_type')  && $request['shipping_type'] == 'home_delivery') {
                $shipping_option = getEcommerceSetting('shipping_option');


                //Flat rate  shipping cost
                if ($shipping_option == null || $shipping_option == config('tlecommercecore.shipping_cost_options.flat_rate')) {
                    return $this->flatRateShippingCostCalculation($request, $allow_free_shipping);
                }

                //Product wise  shipping cost
                if ($shipping_option != null && $shipping_option == config('tlecommercecore.shipping_cost_options.product_wise_rate')) {
                    return $this->productWiseShippingCostCalculation($request, $allow_free_shipping);
                }
                //Profile Based shipping cost
                if ($shipping_option != null && $shipping_option == config('tlecommercecore.shipping_cost_options.profile_wise_rate')) {
                    return $this->shippingProfileBasedShippingCostCalculation($request, $allow_free_shipping);
                }

                //Location wise shipping cost
                if ($shipping_option != null && $shipping_option == config('tlecommercecore.shipping_cost_options.location_wise_rate')) {
                    return $this->locationBasedShippingCostCalculation($request, $allow_free_shipping);
                }
            }
            //Pickup point order
            if ($request->has('shipping_type')  && $request['shipping_type'] == 'pickup_delivery') {
                return $this->pickupPointBasedShippingCostCalculation($request);
            }
        } catch (\Exception $e) {
            dd($e);
            return [
                'success' => false,
                'shipping_available' => false
            ];
        }
    }

    /**
     *
     * Calculated flat rate shipping cost
     */
    public function flatRateShippingCostCalculation($request, $allow_free_shipping)
    {
        $shipping_options = [];
        $product_list = json_decode($request['products'], true);
        $flat_rate_shipping_cost = getEcommerceSetting('flat_rate_shipping_cost') != null ? getEcommerceSetting('flat_rate_shipping_cost') : 0;
        $total_products = sizeof($product_list);
        foreach ($product_list as $product) {
            $shipping_cost = $flat_rate_shipping_cost / $total_products;
            $option = [
                'id' => null,
                'title' => 'Home Delivery',
                'shipping_cost' => $shipping_cost,
                'shipping_time' => null,
                'shipping_from' => null,
                'by' => null
            ];

            $temp = [
                'id' => $product['uid'],
                'product' => $product,
                'options' => $option,
                'default_option' => $option,
                'tax' => $this->calculateProductTax($product['id'], $product['unitPrice'], $product['quantity'], $request['location'], $request['post_code']),
            ];
            array_push($shipping_options, $temp);
        }
        return [
            'success' => true,
            'shipping_available' => true,
            'options' => $shipping_options,
        ];
    }
    /**
     * Calculated product wise shipping cost
     *
     */
    public function productWiseShippingCostCalculation($request, $allow_free_shipping)
    {
        $shipping_options = [];
        $product_list = json_decode($request['products'], true);
        foreach ($product_list as $product) {
            $product_info = Product::select(['is_apply_multiple_qty_shipping_cost', 'is_enable_tax', 'tax_profile', 'shipping_cost', 'id'])->find($product['id']);
            $shipping_cost = $product_info->shipping_cost;
            if ($product_info->is_apply_multiple_qty_shipping_cost == config('settings.general_status.active')) {
                $shipping_cost = $shipping_cost * $product['quantity'];
            }

            $option = [
                'id' => null,
                'title' => 'Home Delivery',
                'shipping_cost' => $shipping_cost,
                'shipping_time' => null,
                'shipping_from' => null,
                'by' => null
            ];

            $temp = [
                'id' => $product['uid'],
                'product' => $product,
                'options' => $option,
                'default_option' => $option,
                'tax' => $this->calculateProductTax($product['id'], $product['unitPrice'], $product['quantity'], $request['location'], $request['post_code']),
            ];
            array_push($shipping_options, $temp);
        }
        return [
            'success' => true,
            'shipping_available' => true,
            'options' => $shipping_options,
        ];
    }
    /**
     *Calculated shipping profile based shipping cost
     *
     */
    public function shippingProfileBasedShippingCostCalculation($request, $allow_free_shipping = true)
    {
        $shipping_zone_ids = ShippingZoneCities::where('city_id', $request['location'])->pluck('zone_id');
        if (count($shipping_zone_ids) > 0) {
            $profile_ids = ShippingZone::whereIn('id', $shipping_zone_ids)->pluck('profile_id');
            $product_list = json_decode($request['products'], true);
            $all_product_shipping_profiles = [];
            $not_available_products = [];
            foreach ($product_list as $product) {
                $product_shipping_profile = $this->productShippingAvailability($product['id'], $profile_ids);
                if ($product_shipping_profile != null) {
                    $temp_data = [
                        'profile' => $product_shipping_profile,
                        'product' => $product
                    ];
                    array_push($all_product_shipping_profiles, $temp_data);
                } else {
                    array_push($not_available_products, $product);
                }
            }
            //Not Available  product found
            if (sizeof($not_available_products) > 0) {
                return [
                    'success' => true,
                    'products' => $not_available_products,
                    'shipping_available' => false
                ];
            }

            if (sizeof($not_available_products) < 1) {

                if (sizeof($all_product_shipping_profiles) > 0) {
                    $shipping_options = [];
                    foreach ($all_product_shipping_profiles as $key => $profile) {
                        //When free shipping is allow
                        if ($allow_free_shipping) {
                            $shipping_rates = ShippingRate::whereIn('zone_id', ShippingZone::whereIn('id', $shipping_zone_ids)->where('profile_id', $profile['profile'])->pluck('id'))->orderBy('shipping_cost', 'ASC')->get();
                        }
                        //When free shipping not allow
                        if (!$allow_free_shipping) {
                            $shipping_rates = ShippingRate::whereIn('zone_id', ShippingZone::whereIn('id', $shipping_zone_ids)->where('profile_id', $profile['profile'])->pluck('id'))
                                ->where('shipping_cost', '>', 0)
                                ->orderBy('shipping_cost', 'ASC')
                                ->get();
                        }

                        $validate_shipping_rates = $this->getMatchingShippingRate($profile['product']['id'], $shipping_rates, $profile['product']['unitPrice'], $profile['product']['quantity']);

                        if (sizeof($validate_shipping_rates) > 0) {
                            $options = new ShippingRateCollection($validate_shipping_rates);
                            $default_option = new SingleShippingRateCollection($validate_shipping_rates[0]);
                            $tax = $this->calculateProductTax($profile['product']['id'], $profile['product']['unitPrice'], $profile['product']['quantity'], $request['location'], $request['post_code']);
                            $temp = [
                                'id' => $profile['product']['uid'],
                                'product' => $profile['product'],
                                'options' => $options,
                                'default_option' => $default_option,
                                'tax' => $tax,
                            ];
                            array_push($shipping_options, $temp);
                        } else {
                            return [
                                'success' => true,
                                'products' => [$profile['product']],
                                'shipping_available' => false
                            ];
                        }
                    }

                    return [
                        'success' => true,
                        'shipping_available' => true,
                        'options' => $shipping_options,
                    ];
                }
                //No Shipping Profile Found
                if (sizeof($all_product_shipping_profiles) < 1) {
                    return [
                        'success' => true,
                        'shipping_available' => false
                    ];
                }
            }
        }
        //No Shipping Zone Found
        if (count($shipping_zone_ids) < 1) {
            return [
                'success' => true,
                'shipping_available' => false,
            ];
        }
    }
    /**
     * Will calculate location wise shipping cost 
     */
    public function locationBasedShippingCostCalculation($request, $allow_free_shipping = true)
    {
        $shipping_rate = null;
        $shipping_rate_id = LocationWiseShippingRateCity::where('city_id', $request['location'])->pluck('rate_id');

        if (count($shipping_rate_id) < 1) {
            //Check States
            $city_info = Cities::where('id', $request['location'])->first();
            $cities = Cities::where('state_id', $city_info?->state_id)->pluck('id');

            if (!LocationWiseShippingRateCity::whereIn('city_id', $cities)->exists()) {
                $shipping_rate_id = LocationWiseShippingRateStates::where('state_id', $city_info?->state_id)->pluck('rate_id');
            }

            //Check Countries
            if (count($shipping_rate_id) < 1) {
                $state_info = States::where('id', $city_info?->state_id)->first();
                $states = States::where('country_id', $state_info?->country_id)->pluck('id');

                if (!LocationWiseShippingRateStates::whereIn('state_id', $states)->exists()) {
                    $shipping_rate_id = LocationWiseShippingRateCountry::where('country_id', $state_info?->country_id)->pluck('rate_id');
                }
            }
        }

        if (count($shipping_rate_id) < 1) {
            return [
                'success' => true,
                'shipping_available' => false,
            ];
        }

        $shipping_rate = LocationWiseShippingRate::whereIn('id', $shipping_rate_id)->orderBy('cost', 'DESC')->first();


        $location_based_shipping_cost = $shipping_rate != null ? $shipping_rate->cost : 0;

        $shipping_options = [];
        $product_list = json_decode($request['products'], true);
        $total_products = sizeof($product_list);
        foreach ($product_list as $product) {
            $shipping_cost = $location_based_shipping_cost / $total_products;
            $option = [
                'id' => null,
                'title' => 'Home Delivery',
                'shipping_cost' => $shipping_cost,
                'shipping_time' => null,
                'shipping_from' => null,
                'by' => null
            ];

            $temp = [
                'id' => $product['uid'],
                'product' => $product,
                'options' => $option,
                'default_option' => $option,
                'tax' => $this->calculateProductTax($product['id'], $product['unitPrice'], $product['quantity'], $request['location'], $request['post_code']),
            ];
            array_push($shipping_options, $temp);
        }
        return [
            'success' => true,
            'shipping_available' => true,
            'options' => $shipping_options,
            'shipping_cost' => $location_based_shipping_cost
        ];
    }
    /**
     *
     * Calculated pickup point based shipping cost
     */
    public function pickupPointBasedShippingCostCalculation($request)
    {
        $shipping_options = [];
        $product_list = json_decode($request['products'], true);
        foreach ($product_list as $product) {
            $tax = $this->calculateProductTax($product['id'], $product['unitPrice'], $product['quantity'], $request['zone_id'], $request['post_code']);
            $temp = [
                'id' => $product['uid'],
                'product' => $product,
                'options' => [],
                'default_option' => [],
                'tax' => $tax,
            ];
            array_push($shipping_options, $temp);
        }
        return [
            'success' => true,
            'shipping_available' => true,
            'options' => $shipping_options,
        ];
    }
    /**
     * Check product shipping available area
     *
     * @param Array $profiles
     * @param Int $location
     * @return mixed
     */
    public function productShippingAvailability($product_id, $profiles)
    {
        $product_shipping_profile = ShippingProfileProducts::whereIn('profile_id', $profiles)->where('product_id', $product_id)->first();
        if ($product_shipping_profile != null) {
            return $product_shipping_profile->profile_id;
        } else {
            return null;
        }
    }
    /**
     * Will return product tax
     *
     * @param Int $product_id
     * @param  Int $city_id
     * @param Int $profile_id
     *
     * @return mixed
     */
    public function calculateProductTax($product_id, $price = 0, $quantity = 1, $city_id = null, $postal_code = null)
    {

        $is_active_tax = getEcommerceSetting('enable_tax_in_checkout');
        if ($is_active_tax != config('settings.general_status.active')) {
            return 0;
        }

        $product_info = Product::select(['is_enable_tax', 'tax_profile', 'id'])->find($product_id);

        if ($product_info == null) {
            return 0;
        }

        if ($product_info != null && $product_info->is_enable_tax != config('settings.general_status.active')) {
            return 0;
        }

        $rate_info = null;

        if ($city_id != null) {
            $rate_info = TaxRate::where('profile_id', $product_info->tax_profile)
                ->where('city_id', $city_id)
                ->select('tax_rate')
                ->first();
        }
        if ($rate_info == null && $city_id == null && $postal_code != null) {
            $rate_info = TaxRate::where('profile_id', $product_info->tax_profile)
                ->where('postal_code', $postal_code)
                ->select('tax_rate')
                ->first();
        }

        if ($rate_info == null) {
            return 0;
        }
        $tax_rate = $rate_info->tax_rate;
        $total_price = $price * $quantity;
        $total_tax = ($total_price * $tax_rate) / 100;
        return $total_tax;
    }
    /**
     * Check shipping rate conditions
     *
     * @param Int $product_id
     * @param Int|Double $price
     * @param Int $quantity
     * @param Array $rates
     * @return Array
     *
     */
    public function getMatchingShippingRate($product_id, $rates, $price = 0, $quantity = 1)
    {
        $total_price = $price * $quantity;
        $product_total_weight = $this->calculateProductCBMOrTotalWeight($product_id, $quantity, true);
        $total_cbm = $this->calculateProductCBMOrTotalWeight($product_id, $quantity, false);
        $validated_rates = [];
        foreach ($rates as $rate) {
            //Shipping rate without condition
            if ($rate->has_condition == config('settings.general_status.in_active')) {
                array_push($validated_rates, $rate);
            }

            //Shipping rate with condition
            if ($rate->has_condition == config('settings.general_status.active')) {
                //Own rate
                if ($rate->rate_type == 'own_rate') {
                    //Price based condition
                    if ($rate->based_on == 'price_based') {
                        if ($total_price >= $rate->min_limit && $total_price <= $rate->max_limit) {
                            array_push($validated_rates, $rate);
                        }
                    }

                    //weight based condition
                    if ($rate->based_on != 'price_based') {
                        if ($product_total_weight >= $rate->min_limit && $product_total_weight <= $rate->max_limit) {
                            array_push($validated_rates, $rate);
                        }
                    }
                }
                //3rd Party Carrier rate
                if ($rate->rate_type != 'own_rate') {
                    //cbm  based carrier rate
                    if (isActivePluging('carrier') && SettingsRepository::getEcommerceSetting('enable_carrier_in_checkout') == config('settings.general_status.active')) {
                        if ($rate->based_on == 'weight_based') {
                            if ($total_cbm >= $rate->min_limit && $total_cbm <= $rate->max_limit) {
                                array_push($validated_rates, $rate);
                            }
                        }
                    }
                }
            }
        }

        return $validated_rates;
    }
    /**
     * Calculate product cbm
     * @param Int $product_id
     * @param Int $quantity
     *
     * @return Int
     */
    public function calculateProductCBMOrTotalWeight($product_id, $quantity, $weight = false)
    {
        $product_shipping_info = ProductShippingInfo::where('product_id', $product_id)->first();

        if ($product_shipping_info != null) {
            //Calculate total weight
            if ($weight) {
                return $product_shipping_info->weight * $quantity;
            }
            //Calculate CBM
            if (!$weight) {
                $gross_weight = $product_shipping_info->weight * $quantity / 1000; //Weight in kg

                $cbm = ($product_shipping_info->height * $product_shipping_info->width * $product_shipping_info->length) / 1000000;

                $total_cbm = $cbm * $quantity;

                if ($gross_weight > $total_cbm) {
                    return $gross_weight;
                } else {
                    return $total_cbm;
                }
            }
        } else {
            return 0;
        }
    }
    /**
     * Will create customer order
     *
     * @param Object $request
     * @return bool
     *
     */
    public function customerCheckout($request)
    {
        try {
            $products = json_decode($request['products'], true);
            $total_payable_amount = self::totalOrderAmount($products) - self::calculateOrderDiscount($request);
            DB::beginTransaction();
            $shipping_type = $request->has('pickup_point') ? config('tlecommercecore.order_type.local_pickup') : config('tlecommercecore.order_type.home_delivery');
            $order = new Orders;
            $order->order_code = self::generateOrderCode();
            $order->customer_id = auth('jwt-customer')->user()->id;
            $order->sub_total = self::calculateOrderSubTotal($products);
            $order->total_tax = self::calculateOrderTotalTax($products);
            $order->total_delivery_cost = self::calculateOrderTotalShippingCost($products);
            $order->total_discount = self::calculateOrderDiscount($request);
            $order->total_order_amount = self::totalOrderAmount($products);
            $order->total_payable_amount = $total_payable_amount;
            $order->payment_method = $request['payment_id'] == 'null' ? null : $request['payment_id'];
            $order->pickup_point_id = $request->has('pickup_point') ? $request['pickup_point'] : NULL;
            $order->shipping_address = $request->has('shipping_address') ? $request['shipping_address'] : NULL;
            $order->billing_address = $request->has('billing_address') ? $request['billing_address'] : NULL;
            $order->note = $request['note'];
            $order->shipping_type = $shipping_type;

            $order->delivery_status = config('tlecommercecore.order_delivery_status.pending');

            //Wallet payment
            if ($request['wallet_payment'] == config('settings.general_status.active') && isActivePluging('wallet')) {
                $order->payment_status = config('tlecommercecore.order_payment_status.paid');
                $order->wallet_payment = config('settings.general_status.active');
                //create wallet transaction
                $wallet_transaction = new \Plugin\Wallet\Models\WalletTransaction();
                $wallet_transaction->entry_type = config('tlecommercecore.wallet_entry_type.debit');
                $wallet_transaction->recharge_type = config('tlecommercecore.wallet_recharge_type.cart');
                $wallet_transaction->customer_id = auth('jwt-customer')->user()->id;
                $wallet_transaction->added_by = null;
                $wallet_transaction->document = null;
                $wallet_transaction->recharge_amount = self::totalOrderAmount($products) - self::calculateOrderDiscount($request);
                $wallet_transaction->status = config('tlecommercecore.wallet_transaction_status.accept');
                $wallet_transaction->payment_method_id = null;
                $wallet_transaction->transaction_id = null;
                $wallet_transaction->save();
            } else {
                $order->payment_status = config('tlecommercecore.order_payment_status.unpaid');
            }
            $order->save();
            $this->storeOrderProducts($order->id, $products, auth('jwt-customer')->user()->id);
            $this->storeCouponUsageInfo($request, $order->id, auth('jwt-customer')->user()->id);

            DB::commit();

            if ($request['payment_id'] == config('tlecommercecore.payment_methods.bank')) {
                $this->storeBankPaymentInfo($request, $order->id);
            }

            if ($request['payment_id'] == config('tlecommercecore.payment_methods.cod') || $request['payment_id'] == config('tlecommercecore.payment_methods.bank')) {
                //Send new order notification to admin and customer
                EcommerceNotification::sendNewOrderNotification($order);
            }
            return $this->generateOrderPaymentLink($order->id, auth('jwt-customer')->user()->id, 'registered', $request['payment_id'], $total_payable_amount, $request['wallet_payment']);
        } catch (\Exception $e) {
            DB::rollBack();
            return false;
        } catch (\Error $e) {
            DB::rollBack();
            return false;
        }
    }
    /**
     * Will store gust order
     *
     * @param Object $request
     * @return bool
     */
    public function guestCheckout($request)
    {
        try {
            DB::beginTransaction();
            $shipping_address = $request->has('shipping_address') ? json_decode($request['shipping_address'], true) : [];
            $customer_id = NULL;
            $guest_customer_id = NULL;
            $customer_name = $request['name'] != null ? $request['name'] : ($shipping_address['name'] ?? 'Guest Customer');
            $customer_email = $request['email'] != null ? $request['email'] : ($shipping_address['email'] ?? 'guest@default.com');

            if ($request->has('create_new_account') && getEcommerceSetting('enable_personal_info_guest_checkout') == 1) {
                //Register customer
                $customer = new Customers;
                $customer->uid = date('hisymd');
                $customer->name = !empty($customer_name) ? $customer_name : 'Guest Customer';
                $customer->email = !empty($customer_email) ? $customer_email : 'guest@default.com';
                $customer->password = Hash::make($request['password']);
                $customer->status = config('settings.general_status.active');
                $customer->save();
                $customer_id = $customer->id;
            } else {
                //Create guest customer
                $guest_customer = new GuestCustomers();
                $guest_customer->name = !empty($customer_name) ? $customer_name : 'Guest Customer';
                $guest_customer->email = !empty($customer_email) ? $customer_email : 'guest@default.com';
                $guest_customer->order_id = NULl;
                $guest_customer->save();
                $guest_customer_id = $guest_customer->id;
            }
            $shipping_address_id = NULL;
            $billing_address_id = NULL;
            //Store shipping address
            if ($request->has('shipping_address')) {
                $shipping_address = json_decode($request['shipping_address'], true);

                $customer_address = new CustomerAddress;
                $customer_address->customer_id = $customer_id;
                $customer_address->guest_customer = $guest_customer_id;
                $customer_address->name = $shipping_address['name'];
                $customer_address->country_id = $shipping_address['country_id'];
                $customer_address->state_id = $shipping_address['state_id'];
                $customer_address->city_id = $shipping_address['city_id'];
                $customer_address->postal_code = $shipping_address['postal_code'];
                $customer_address->address = $shipping_address['address'];
                $customer_address->phone_code = $shipping_address['phone_code'];
                $customer_address->phone = $shipping_address['phone'];
                $customer_address->save();
                $shipping_address_id = $customer_address->id;
            }
            //Store billing address
            if ($request->has('billing_address')) {
                $shipping_address = json_decode($request['billing_address'], true);

                $customer_address = new CustomerAddress;
                $customer_address->customer_id = $customer_id;
                $customer_address->guest_customer = $guest_customer_id;
                $customer_address->name = $shipping_address['name'];
                $customer_address->country_id = $shipping_address['country_id'];
                $customer_address->state_id = $shipping_address['state_id'];
                $customer_address->city_id = $shipping_address['city_id'];
                $customer_address->postal_code = $shipping_address['postal_code'];
                $customer_address->address = $shipping_address['address'];
                $customer_address->phone_code = $shipping_address['phone_code'];
                $customer_address->phone = $shipping_address['phone'];
                $customer_address->save();
                $billing_address_id = $customer_address->id;
            }

            $products = json_decode($request['products'], true);
            $total_payable_amount = self::totalOrderAmount($products) - self::calculateOrderDiscount($request);
            $shipping_type = $request->has('pickup_point') ? config('tlecommercecore.order_type.local_pickup') : config('tlecommercecore.order_type.home_delivery');
            $order = new Orders;
            $order->order_code = self::generateOrderCode();
            $order->customer_id = $customer_id;
            $order->sub_total = self::calculateOrderSubTotal($products);
            $order->total_tax = self::calculateOrderTotalTax($products);
            $order->total_delivery_cost = self::calculateOrderTotalShippingCost($products);
            $order->total_discount = self::calculateOrderDiscount($request);
            $order->total_order_amount = self::totalOrderAmount($products);
            $order->total_payable_amount = self::totalOrderAmount($products) - self::calculateOrderDiscount($request);
            $order->payment_method = $request['payment_id'];
            $order->pickup_point_id = $request->has('pickup_point') ? $request['pickup_point'] : NULL;
            $order->shipping_address = $shipping_address_id;
            $order->billing_address = $billing_address_id;
            $order->note = $request['note'];
            $order->shipping_type = $shipping_type;
            $order->payment_status = config('tlecommercecore.order_payment_status.unpaid');
            $order->delivery_status = config('tlecommercecore.order_delivery_status.pending');
            $order->save();
            //Update guest customer
            if ($guest_customer_id != NULL) {
                $guest_customer = GuestCustomers::where('id', $guest_customer_id)->first();
                $guest_customer->order_id = $order->id;
                $guest_customer->save();
            }
            //store order products
            $this->storeOrderProducts($order->id, $products, null);

            $this->storeCouponUsageInfo($request, $order->id, $customer_id != null ? $customer_id : null);


            $redirect_url = null;
            if ($customer_id != null) {
                $redirect_url = $this->generateOrderPaymentLink($order->id, $customer_id, 'customer', $request['payment_id'], $total_payable_amount);
            } else {
                $redirect_url = $this->generateOrderPaymentLink($order->id, $guest_customer_id, 'guest', $request['payment_id'], $total_payable_amount);
            }
            //Send new order notification to admin
            EcommerceNotification::sendNewOrderNotification($order);
            DB::commit();
            if ($request['payment_id'] == config('tlecommercecore.payment_methods.bank')) {
                $this->storeBankPaymentInfo($request, $order->id);
            }
            return $redirect_url;
        } catch (\Exception $e) {
            DB::rollBack();
            return false;
        } catch (\Error $e) {
            DB::rollBack();
            return false;
        }
    }
    /**
     * Will store order products
     *
     * @param Int $order_id
     * @param Array $products
     * @return void
     */
    public function storeOrderProducts($order_id, $products, $customer_id)
    {
        $order = Orders::find($order_id);
        foreach ($products as $product) {
            $order_discount = 0;
            if ($order->total_discount > 0) {
                $order_discount = ($order->total_discount * $product['quantity'] * $product['unitPrice']) / $order->sub_total;
            }

            $paid_amount = 0;
            $payment_status = config('tlecommercecore.order_payment_status.unpaid');
            $tax = SettingsRepository::getEcommerceSetting('enable_tax_in_checkout') == config('settings.general_status.active') ? $product['tax'] : 0;

            if ($order->wallet_payment == config('settings.general_status.active')) {
                $unit_price = $product['unitPrice'] * $product['quantity'];
                $paid_amount = ($unit_price + $product['shipping_cost'] + $tax) - $order_discount;
                $payment_status = config('tlecommercecore.order_payment_status.paid');
            }

            $product_info = Product::where('id', $product['product_id'])
                ->select(['is_refundable', 'supplier'])
                ->first();

            $order_product = new OrderHasProducts;
            $order_product->order_id = $order_id;
            $order_product->product_id = $product['product_id'];
            $order_product->variant_id = $product['variant'];
            $order_product->quantity = $product['quantity'];
            $order_product->unit_price = $product['unitPrice'];
            $order_product->purchase_price = self::getProductPurchasePrice($product['product_id'], $product['variant_code']);
            $order_product->delivery_cost = $product['shipping_cost'];
            $order_product->shipping_rate = $product['shipping_rate_id'] != null ? $product['shipping_rate_id'] : null;
            $order_product->tax = $tax;
            $order_product->discount = $product['oldPrice'] - $product['unitPrice'];
            $order_product->image = $product['image'];
            $order_product->attachment = $product['attatchment'];
            $order_product->order_discount = $order_discount;
            $order_product->total_paid = $paid_amount;
            $order_product->delivery_status = config('tlecommercecore.order_delivery_status.pending');
            $order_product->payment_status = $payment_status;
            $order_product->seller_id = $product_info->supplier;
            $order_product->return_status = $product_info->is_refundable == config('settings.general_status.active') ? config('tlecommercecore.product_return_status.available') : config('tlecommercecore.product_return_status.not_available');
            $order_product->save();

            //Update Inventory
            $this->updateProductInventory($product['product_id'], $product['quantity'], $product['variant_code']);

            //Store order tracking data
            $this->insertOrderTrackingData($order_id, $order_product->id, "Thank you for shopping. Your order is being verified");

            //Remove item from cart list
            if ($customer_id != null) {
                $cart_item = CartItem::where('customer_id', $customer_id)
                    ->where('uid', $product['uid'])
                    ->first();
                if ($cart_item != null) {
                    $cart_item->delete();
                }
            }
        }
    }

    /**
     * Will store bank payment info
     */
    public function storeBankPaymentInfo($request, $order_id)
    {
        try {
            $bank_payments = new BankPayment();
            $bank_payments->order_id = $order_id;
            $bank_payments->bank_name = $request['bank_name'];
            $bank_payments->branch_name = $request['branch_name'];
            $bank_payments->account_number = $request['account_number'];
            $bank_payments->account_name = $request['account_name'];
            $bank_payments->bank_name = $request['bank_name'];
            $bank_payments->transaction_number = $request['transaction_number'];
            if ($request->hasFile('receipt')) {
                $image = saveFileInStorage($request['receipt']);
                $bank_payments->receipt = $image;
            }
            $bank_payments->save();
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Get product purchase price
     *
     * @param Int $product_id
     * @param String $variant
     *
     * @return mixed
     */
    public static function getProductPurchasePrice($product_id, $variant = null)
    {
        if ($variant != null) {
            $variant_price = VariantProductPrice::where('product_id', $product_id)->where('variant')->select('purchase_price')->first();
            if ($variant_price != null) {
                return $variant_price->purchase_price;
            } else {
                return 0;
            }
        } else {
            $single_price = SingleProductPrice::where('product_id', $product_id)->select('purchase_price')->first();
            if ($single_price != null) {
                return $single_price->purchase_price;
            } else {
                return 0;
            }
        }
    }
    /**
     * Update product inventory
     *
     * @param Int $product_id
     * @param String $variant
     * @param Int $quantity
     * @return void
     */
    public function updateProductInventory($product_id, $quantity, $variant = null)
    {
        //Update single product inventory
        if ($variant != null) {
            $variant_price = VariantProductPrice::where('product_id', $product_id)->where('variant', $variant)->first();
            if ($variant_price != null) {
                $updated_qty = $variant_price->quantity - $quantity;
                $variant_price->quantity = $updated_qty;
                $variant_price->save();
            }
        }
        //Update single product inventory
        if ($variant == null) {
            $single_price = SingleProductPrice::where('product_id', $product_id)->first();
            if ($single_price != null) {
                $updated_qty = $single_price->quantity - $quantity;
                $single_price->quantity = $updated_qty;
                $single_price->save();
            }
        }
    }
    /**
     * Will store order tracking data
     *
     * @param Int $order_id
     * @param Int $order_package_id
     * @param String $message
     * @return void
     */
    public function insertOrderTrackingData($order_id, $order_package_id, $message)
    {
        $order_tracking = new OrderPackageTracking();
        $order_tracking->order_id = $order_id;
        $order_tracking->order_package_id = $order_package_id;
        $order_tracking->message = $message;
        $order_tracking->save();
    }
    /**
     * Generate order code
     *
     * @return mixed
     */
    public static function generateOrderCode()
    {
        $order_prefix = SettingsRepository::getEcommerceSetting('order_code_prefix');
        $prefix_separator = SettingsRepository::getEcommerceSetting('order_code_prefix_seperator');
        $identifier = date('hisymd');
        if ($order_prefix != null) {
            $code = $order_prefix . '' . $prefix_separator . '' . $identifier;
        } else {
            $code = $identifier;
        }
        return $code;
    }
    /**
     * Calculate order total
     *
     * @param Array $products
     * @return  Int|Double
     */
    public static function calculateOrderSubTotal($products)
    {
        return array_sum(array_map(fn($item) => $item['unitPrice'] * $item['quantity'], $products));
    }
    /**
     * Calculate order total tax
     *
     * @param Array $products
     * @return mixed
     */
    public static function calculateOrderTotalTax($products)
    {
        return SettingsRepository::getEcommerceSetting('enable_tax_in_checkout') == config('settings.general_status.active') ? array_sum(array_map(fn($item) => $item['tax'], $products)) : 0;
    }
    /**
     * Calculate order total shipping cost
     *
     * @param Array $products
     * @return mixed
     */
    public static function calculateOrderTotalShippingCost($products)
    {
        return array_sum(array_map(fn($item) => $item['shipping_cost'], $products));
    }
    /**
     * Calculate order total discount
     * @param Array $request
     * @return mixed
     */
    public static function calculateOrderDiscount($request)
    {
        $applied_coupon = json_decode($request['coupon_discounts'], true);
        if (sizeof($applied_coupon) > 0) {
            return array_sum(array_map(fn($item) => $item['discount'], $applied_coupon));
        }
        return 0;
    }

    public function storeCouponUsageInfo($request, $order_id, $customer_id = null)
    {
        if (isActivePluging('coupon')) {
            $applied_coupons = json_decode($request['coupon_discounts'], true);
            if (sizeof($applied_coupons) > 0) {
                foreach ($applied_coupons as $coupon) {
                    $coupon_usage = new \Plugin\Coupon\Models\CouponUsage();
                    $coupon_usage->customer_id = $customer_id;
                    $coupon_usage->order_id = $order_id;
                    $coupon_usage->coupon_id = $coupon['id'];
                    $coupon_usage->discounted_amount = $coupon['discount'];
                    $coupon_usage->coupon_code = $coupon['coupon_code'];
                    $coupon_usage->save();
                }
            }
        }
    }
    /**
     * calculate total payable amount of order
     *
     * @param Array $products
     * @return mixed
     */
    public static function totalOrderAmount($products)
    {
        return self::calculateOrderSubTotal($products) + self::calculateOrderTotalTax($products) + self::calculateOrderTotalShippingCost($products);
    }


    /**
     * Will accept order
     *
     * @param Int $order_id
     * @return bool
     */
    public function acceptOrder($order_id, $seller_id = null)
    {
        try {
            DB::beginTransaction();
            $notifiable_sellers = [];
            $order = Orders::where('id', $order_id)->first();
            if ($order != null) {

                $query = OrderHasProducts::where('order_id', $order_id);
                if ($seller_id != null) {
                    $query = $query->where('seller_id', $seller_id);
                }

                $all_products = $query->select(['delivery_status', 'seller_id', 'order_id', 'id'])->get();
                foreach ($all_products as $product) {
                    if ($product->delivery_status != config('tlecommercecore.order_delivery_status.cancelled')) {
                        $product->delivery_status = config('tlecommercecore.order_delivery_status.processing');
                        $product->save();
                        $this->insertOrderTrackingData($order_id, $product->id, 'Your order has been accepted');
                        //Filter seller ids
                        if ($seller_id == null && $product->seller_id != null && auth()->user()->user_type == null && isActivePluging('multivendor')) {
                            if (!in_array($product->seller_id, $notifiable_sellers)) {
                                array_push($notifiable_sellers, $product->seller_id);
                            }
                        }
                    }
                }
                //Update order table
                if ($order->products->count() == $all_products->count()) {
                    $order->delivery_status = config('tlecommercecore.order_delivery_status.processing');
                    $order->save();
                }
                //Send notification to customer
                $message = "Your order has been accepted";
                $btn_title = "Track Your Order";
                $mail_title = "Order accepted";
                EcommerceNotification::sendOrderStatusNotification($order->id, $order->customer_id, $message, $btn_title, $mail_title);
                //Send notification to seller
                foreach ($notifiable_sellers as $seller_id) {
                    $seller_message = "An order has been accepted. Order code " . $order->order_code;
                    EcommerceNotification::sendOrderItemUpdateStatusNotificationToSeller($order->id, $seller_id, $seller_message);
                }
                DB::commit();
                return true;
            }
            DB::rollBack();
            return false;
        } catch (\Exception $e) {
            DB::rollBack();
            return false;
        } catch (\Error $e) {
            DB::rollBack();
            return false;
        }
    }
    /**
     * Will cancel order
     *
     * @param Int $order_id
     * @return bool
     *
     */
    public function cancelOrder($order_id, $seller_id = null)
    {
        try {
            DB::beginTransaction();
            $notifiable_sellers = [];
            $order = Orders::where('id', $order_id)->first();

            if ($order != null) {
                $query = OrderHasProducts::where('order_id', $order_id);
                if ($seller_id != null) {
                    $query = $query->where('seller_id', $seller_id);
                }
                $all_products = $query->select(['delivery_status', 'seller_id', 'order_id', 'id'])->get();
                //Update order has product table
                foreach ($all_products as $product) {

                    $product->delivery_status = config('tlecommercecore.order_delivery_status.cancelled');
                    $product->save();
                    $this->insertOrderTrackingData($order_id, $product->id, 'Your order has been cancelled');
                    //Filter seller ids
                    if ($seller_id == null && $product->seller_id != null && auth()->user()->user_type == null && isActivePluging('multivendor')) {
                        if (!in_array($product->seller_id, $notifiable_sellers)) {
                            array_push($notifiable_sellers, $product->seller_id);
                        }
                    }
                }

                //Update order table status
                if ($order->products->count() == $all_products->count()) {
                    $order->delivery_status = config('tlecommercecore.order_delivery_status.cancelled');
                    $order->save();
                }

                //Send notification
                if (auth('jwt-customer')->user() != null) {
                    //Send notification to admin
                    EcommerceNotification::sendCustomerOrderCancelNotification($order->id,  'Order code ' . $order->order_code . ' has been cancelled');
                } else {
                    //Send notification to customer
                    $message = 'Your order has been cancelled. Order code ' . $order->order_code;
                    $btn_title = "Track Your Order";
                    $mail_title = "Order cancelled!";
                    EcommerceNotification::sendOrderStatusNotification($order->id, $order->customer_id, $message, $btn_title, $mail_title);
                }

                //Send notification to seller
                foreach ($notifiable_sellers as $seller_id) {
                    $seller_message = "An order has been cancelled. Order code " . $order->order_code;
                    EcommerceNotification::sendOrderItemUpdateStatusNotificationToSeller($order->id, $seller_id, $seller_message);
                }

                DB::commit();
                return true;
            }

            DB::rollBack();
            return false;
        } catch (\Exception $e) {
            DB::rollBack();
            return false;
        } catch (\Error $e) {
            DB::rollBack();
            return false;
        }
    }
    /**
     * Will update order status
     *
     * @param Object $request
     * @return bool
     */
    public function updateOrderStatus($request)
    {
        try {

            DB::beginTransaction();
            $order = Orders::where('id', $request['order_id'])->first();
            $seller_ids = [];
            foreach ($request['product'] as $product) {
                $order_product = OrderHasProducts::where('id', $product)->where('order_id', $request['order_id'])->first();
                $tracking_id = null;
                //store payment tracking message
                if ($order_product->payment_status != $request['payment_status']) {
                    $payment_change_message = $order_product->payment_status == config('tlecommercecore.order_payment_status.paid') ? "Your order has been make unpaid" : "Payment completed of your order";
                    $this->insertOrderTrackingData($request['order_id'], $product, $payment_change_message);
                    //Send notification
                    $btn_title = "Track Your Order";
                    $mail_title = "Payment received";
                    EcommerceNotification::sendOrderStatusNotification($order->id, $order->customer_id, $payment_change_message, $btn_title, $mail_title);
                }
                //Store delivery tracking message
                $delivery_change_message = null;
                $btn_title = "Track Your Order";
                $mail_title = "";
                if ($request->has('comment') && $request['comment'] != null) {
                    $delivery_change_message = $request['comment'];
                } else {
                    if ($order_product->delivery_status != $request['delivery_status']) {
                        if ($request['delivery_status'] == config('tlecommercecore.order_delivery_status.pending')) {
                            $delivery_change_message = 'Your package has been pending';
                            $mail_title = "Your order has been pending";
                        } else if ($request['delivery_status'] == config('tlecommercecore.order_delivery_status.processing')) {
                            $delivery_change_message = 'Your order is being accepted';
                            $mail_title = "Order accepted";
                        } else if ($request['delivery_status'] == config('tlecommercecore.order_delivery_status.ready_to_ship')) {
                            $delivery_change_message = 'Your package has been packed and is being handed over to our logistics partner';
                            $mail_title = "Package is ready to ship";
                        } else if ($request['delivery_status'] == config('tlecommercecore.order_delivery_status.shipped')) {

                            $mail_title = "Package is on the way";
                            if ($request->has($product . '-tracking')) {
                                $tracking_id = $request[$product . '-tracking'];
                            }

                            if ($order_product->shipping_rate_info != null) {
                                if ($order_product->shipping_rate_info->carrier_id != null) {
                                    if ($order_product->shipping_rate_info->carrier != null) {
                                        $delivery_change_message = 'Your package has been handed over to [' . $order_product->shipping_rate_info->carrier->name . ']';
                                        $delivery_change_message = $tracking_id != null ? $delivery_change_message . '. Tracking id is ' . $tracking_id : $delivery_change_message;
                                        $delivery_change_message = $order_product->shipping_rate_info->carrier->tracking_url != null ? $delivery_change_message . '. Tracking url is <a href=' . $order_product->shipping_rate_info->carrier->tracking_url . '>' . $order_product->shipping_rate_info->carrier->tracking_url . '</a>' : $delivery_change_message;
                                    }
                                } else {
                                    $delivery_change_message = "Your package has been handed over to a logistics partner";
                                    $delivery_change_message = $tracking_id != null ? $delivery_change_message . '. Tracking id is ' . $tracking_id : $delivery_change_message;
                                }
                            } else {
                                $delivery_change_message = "Your package has been handed over to a logistics partner";
                                $delivery_change_message = $tracking_id != null ? $delivery_change_message . '. Tracking id is ' . $tracking_id : $delivery_change_message;
                            }
                        } else if ($request['delivery_status'] == config('tlecommercecore.order_delivery_status.delivered')) {
                            $delivery_change_message = 'Your package has been delivered. Thank you for shopping';
                            $btn_title = "Share Review";
                            $mail_title = "Package delivered";
                        } else if ($request['delivery_status'] == config('tlecommercecore.order_delivery_status.cancelled')) {
                            $delivery_change_message = 'Your order has been cancelled';
                            $mail_title = "Cancelled Order";
                        } else {
                        }
                    }
                }

                if ($delivery_change_message != null) {
                    $this->insertOrderTrackingData($request['order_id'], $product, $delivery_change_message);
                    //Send notification to customer
                    EcommerceNotification::sendOrderStatusNotification($order->id, $order->customer_id, $delivery_change_message, $btn_title, $mail_title);
                    //Filter seller id
                    if ($order_product->seller_id != null && auth()->user()->user_type == null && isActivePluging('multivendor')) {
                        if (!in_array($order_product->seller_id, $seller_ids)) {
                            array_push($seller_ids, $order_product->seller_id);
                        }
                    }
                }
                //Update payment and delivery status and payment
                if ($order_product->payment_status != $request['payment_status']) {
                    $amount = $order_product->total_paid;
                    if ($request['payment_status'] == config('tlecommercecore.order_payment_status.paid')) {
                        $amount = $order_product->totalPayableAmount();
                    } else {
                        $amount = 0;
                    }
                    $order_product->total_paid = $amount;
                }

                $order_product->payment_status = $request['payment_status'];
                $order_product->delivery_status = $request['delivery_status'];

                if ($tracking_id != null) {
                    $order_product->tracking_id = $tracking_id;
                }

                if ($request['delivery_status'] == config('tlecommercecore.order_delivery_status.delivered')) {
                    $order_product->delivery_time = now();
                }
                $order_product->save();
            }

            //Update order table status
            $total_order_products = OrderHasProducts::where('order_id', $request['order_id'])->count();
            $order->delivery_status = $request['delivery_status'];
            if ($total_order_products == $request['product']) {
                $order->payment_status = $request['payment_status'];
            }
            $order->save();

            //Send notification to seller
            foreach ($seller_ids as $seller_id) {
                $seller_message = "Order status updated. Order code " . $order->order_code;
                EcommerceNotification::sendOrderItemUpdateStatusNotificationToSeller($order->id, $seller_id, $seller_message);
            }
            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            return false;
        }
    }
    /**
     * Change  delivery status of an item of order
     *
     * @param Int $item_id
     * @param Int $order_id
     * @param Int $status
     * @param String $status_type
     * @return bool
     */
    public function changeOrderItemStatus($item_id, $order_id, $status, $status_type = 'delivery_status')
    {
        try {
            DB::beginTransaction();

            $item = OrderHasProducts::where('order_id', $order_id)->where('id', $item_id)->first();
            if ($item != null) {
                if ($status_type == 'delivery_status') {
                    //Change order table
                    $order = Orders::where('id', $order_id)->first();
                    $order->delivery_status = $status;
                    $order->save();

                    //Change order has products table
                    $item->delivery_status = $status;
                    $item->save();

                    $delivery_change_message = null;
                    if ($status == config('tlecommercecore.order_delivery_status.cancelled')) {
                        $delivery_change_message = 'Your package has been cancelled';
                    }
                    if ($delivery_change_message != null) {
                        $this->insertOrderTrackingData($order_id, $item_id, $delivery_change_message);
                        //Send notification
                        if (auth('jwt-customer')->user() != null) {
                            //Send notification to admin when customer cancel order item
                            EcommerceNotification::sendCustomerOrderCancelNotification($order->id,  'Customer  cancel an item from order');
                        } else {
                            //Send notification to customer
                            $btn_title = "Track Your Order";
                            $mail_title = "Item has been removed from your order";
                            EcommerceNotification::sendOrderStatusNotification($order->id, $order->customer_id, $delivery_change_message, $btn_title, $mail_title);
                        }

                        //Send notification to seller when admin
                        if ($item->seller_id != null && auth()->user()->user_type == null && isActivePluging('multivendor')) {
                            if (auth('jwt-customer')->user() == null) {
                                $seller_message = "Order status updated. Order code " . $order->order_code;
                                EcommerceNotification::sendOrderItemUpdateStatusNotificationToSeller($order->id, $item->seller_id, $seller_message);
                            }
                        }
                    }
                }
            } else {
                DB::rollBack();
                return false;
            }

            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            return false;
        }
    }

    /**
     * Will fired bulk action of orders
     *
     * @param Object $request
     * @return bool
     */
    public function orderBulkAction($request)
    {
        try {
            if ($request['data']['action'] == 'delete_all') {
                $items = $request['data']['selected_items'];
                foreach ($items as $item) {
                    $this->deleteOrder($item);
                }

                return true;
            }

            $action = $request['data']['action'];
            $action_array = explode('-', $action);
            if (sizeof($action_array) > 1) {
                $items = $request['data']['selected_items'];
                $status_type = $action_array[0];
                $status_id = $action_array[1];

                if ($status_type == 'p') {
                    foreach ($items as $item) {
                        $this->updateOrderPaymentStatus($item, $status_id);
                    }
                } else {
                    foreach ($items as $item) {
                        $this->updateOrderDeliveryStatus($item, $status_id);
                    }
                }
                return true;
            } else {
                return false;
            }
        } catch (\Exception $e) {
            return false;
        }
    }
    /**
     * Will update Order delivery status
     *
     * @param Int $order_id
     * @param Int $status
     * @return void
     */
    public function updateOrderDeliveryStatus($order_id, $status)
    {
        try {
            DB::beginTransaction();
            $order = Orders::where('id', $order_id)->first();
            $message = 'Delivery status is updated';
            if ($order != null) {
                $order->delivery_status = $status;
                $order->save();
                DB::table('tl_com_ordered_products')
                    ->where('order_id', $order_id)
                    ->update(
                        [
                            'delivery_status' => $status
                        ]
                    );
                $package_ids =  DB::table('tl_com_ordered_products')->where('order_id', $order_id)->pluck('id');

                foreach ($package_ids as $id) {
                    $this->insertOrderTrackingData($order_id, $id, $message);
                }
                //Send notification
                $message = "Your order status updated";
                $btn_title = "Track Your Order";
                $mail_title = "Payment received";
                EcommerceNotification::sendOrderStatusNotification($order->id, $order->customer_id, $message, $btn_title, $mail_title);
                DB::commit();
                return true;
            }
            DB::rollBack();
            return false;
        } catch (\Exception $e) {
            DB::rollBack();
            return false;
        } catch (\Error $e) {
            DB::rollBack();
            return false;
        }
    }

    /**
     * delete order
     *
     * @param [type] $order_id
     * @return boolean
     */
    public function deleteOrder($order_id): bool
    {
        try {
            DB::beginTransaction();
            $order = Orders::where('id', $order_id)->first();
            if (($order == null)) {
                DB::commit();
                return false;
            }
            $order_products = OrderHasProducts::where('order_id', $order_id)->get();
            foreach ($order_products as $product) {
                $product->delete();
            }
            $order->delete();
            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            return false;
        }
    }

    /**
     * Will update payment status of order
     *
     * @param Int $order_id
     * @param Int $status
     * @return bool
     */
    public function updateOrderPaymentStatus($order_id, $status)
    {
        try {
            DB::beginTransaction();
            $order = Orders::where('id', $order_id)->first();
            if ($order != null) {
                $order_products = OrderHasProducts::where('order_id', $order->id)->get();
                foreach ($order_products as $product) {
                    if ($product->payment_status != $status) {
                        $message = 'Payment status is updated';
                        $amount = $product->total_paid;
                        if ($status == config('tlecommercecore.order_payment_status.paid')) {
                            $amount = $product->totalPayableAmount();
                            $message = 'Payment completed';
                        } else {
                            $amount = 0;
                            $message = 'Payment reverse to unpaid';
                        }
                        $product->total_paid = $amount;
                        $product->payment_status = $status;
                        $product->save();
                        $this->insertOrderTrackingData($order_id, $product->id, $message);
                    }
                }
                //Send notification
                $message = "Your order payment status updated";
                $btn_title = "Track Your Order";
                $mail_title = "Payment Status Updated";
                EcommerceNotification::sendOrderStatusNotification($order->id, $order->customer_id, $$message, $btn_title, $mail_title);
                DB::commit();
                return true;
            }
            DB::rollBack();
            return false;
        } catch (\Exception $e) {
            DB::rollBack();
            return false;
        } catch (\Error $e) {
            DB::rollBack();
            return false;
        }
    }
    /**
     * Store customer review
     *
     * @param Object $request
     * @param Int $customer_id
     * @return bool
     */
    public function storeCustomerProductReview($request, $customer_id)
    {
        try {
            DB::beginTransaction();
            $review_images = [];
            if ($request->has('review_images')) {
                $images = $request->file('review_images');
                foreach ($images as $image) {
                    $image = saveFileInStorage($image, false);
                    array_push($review_images, $image);
                }
            }
            if (sizeof($review_images) > 0) {
                $review_images = json_encode($review_images);
            } else {
                $review_images = NULL;
            }
            $review_item = new ProductReview();
            $review_item->customer_id = $customer_id;
            $review_item->product_id = $request['product_id'];
            $review_item->order_id = $request['order_id'];
            $review_item->review = $request['review'];
            $review_item->rating = $request['rating'];
            $review_item->images = $review_images;
            $review_item->save();
            //Send notification to admin
            $message = 'Customer gives ' . $request['rating'] . ' star review';
            $customer = Customers::find($customer_id);

            if ($customer != null) {
                $message = $customer->name . ' gives a ' . $request['rating'] . ' star review';
            }
            EcommerceNotification::sendCustomerProductReviewNotification($message);
            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            return false;
        } catch (\Error $e) {
            DB::rollBack();
            return false;
        }
    }
    /**
     * Will store return order details
     *
     * @param Object $request
     * @return bool
     */
    public function returnOrder($request)
    {
        info("returnOrder");
        try {
            if (isActivePluging('refund')) {
                $order_product = OrderHasProducts::where('id', $request['package_id'])->first();
                if ($order_product == null) {
                    return false;
                }
                DB::beginTransaction();
                $return_images = [];
                if ($request->has('return_images')) {
                    $images = $request->file('return_images');
                    foreach ($images as $image) {
                        $image = saveFileInStorage($image, false);
                        array_push($return_images, $image);
                    }
                }
                if (sizeof($return_images) > 0) {
                    $return_images = json_encode($return_images);
                } else {
                    $return_images = NULL;
                }
                //store return request
                $return_req = new \Plugin\Refund\Models\OrderReturnRequest;
                $return_req->refund_code = $order_product->order_id . '-' . date('hisymd');
                $return_req->order_id =  $order_product->order_id;
                $return_req->customer_id =  auth('jwt-customer')->user()->id;
                $return_req->ordered_product_id = $request['package_id'];
                $return_req->quantity = $order_product->quantity;
                $return_req->total_amount = $order_product->unit_price * $order_product->quantity;
                $return_req->total_refund_amount = 0;
                $return_req->images = $return_images;
                $return_req->comment = $request['refund_comment'];
                $return_req->reason_id = $request['refund_reason'];
                $return_req->return_status = config('tlecommercecore.return_request_status.pending');
                $return_req->refund_status = config('tlecommercecore.return_request_payment_status.pending');
                $return_req->save();

                //update order product return status
                $order_product->return_status = config('tlecommercecore.product_return_status.processing');
                $order_product->save();

                //store refunds tracking
                $tracking = new \Plugin\Refund\Models\RefundRequestTracking;
                $tracking->request_id = $return_req->id;
                $tracking->order_id = $order_product->order_id;
                $tracking->message = "Your refund request has been placed";
                $tracking->save();

                //Send notification to admin
                $message = 'Customer create a refund request';
                EcommerceNotification::sendCustomerOrderReturnNotification($return_req->id, $message, $order_product->seller_id);

                DB::commit();
                return true;
            } else {
                DB::rollBack();
                return false;
            }
        } catch (\Exception $e) {
            DB::rollBack();
            return false;
        } catch (\Error $e) {
            DB::rollBack();
            return false;
        }
    }
    /**
     * Will return customer return requests
     *
     * @param Object $request
     * @param Int $customer_id
     * @return Collections
     */
    public function customerReturnRequests($request, $customer_id)
    {
        if (isActivePluging('refund')) {
            return \Plugin\Refund\Models\OrderReturnRequest::where('customer_id', $customer_id)->orderBy('id', 'DESC')->paginate($request['perPage']);
        } else {
            return [];
        }
    }
    /**
     * Will return order shipping label content
     *
     * @param Int $order_id
     * @param Array $order_products
     * @return collection
     */
    public function getShippingLabelContent($order_id, $order_products)
    {
        try {
            $order_info = Orders::find($order_id);
            $num_of_products = OrderHasProducts::whereIn('id', $order_products)->sum('quantity');

            $products = OrderHasProducts::whereIn('id', $order_products)->select(
                [
                    'delivery_cost',
                    'order_id',
                    'total_paid',
                    'order_discount',
                    'tracking_id',
                    'product_id',
                    'quantity',
                    'unit_price',
                    'shipping_rate',
                    'tax'
                ]

            )->get();
            $shipping_zone = null;
            $tracking_id = $products[0]->tracking_id != null ? $products[0]->tracking_id : $order_info->order_code;
            $shipping_rate = $products[0]->shipping_rate;
            $shipping_method = null;

            if ($shipping_rate != null) {
                $rate_info = ShippingRate::where('id', $shipping_rate)->select('zone_id', 'name', 'carrier_id')->first();
                if ($rate_info != null) {
                    if ($rate_info->name != null) {
                        $shipping_method = $rate_info->name;
                    } else {
                        $carrier = ShippingCarrier::find($rate_info->carrier_id);
                        if ($carrier != null) {
                            $shipping_method = $carrier->name;
                        }
                    }
                    $shipping_zone_info = ShippingZone::where('id', $rate_info->zone_id)->select('name', 'profile_id')->first();
                    if ($shipping_zone_info != null) {
                        $shipping_zone = $shipping_zone_info->name;
                    }
                }
            }

            $shipping_type = $order_info->shipping_type == config('tlecommercecore.order_type.home_delivery') ? 'Home Delivery' : 'Local Pickup';

            $payment_method = $order_info->payment_method != config('tlecommercecore.payment_methods.cod') ? 'Non COD' : 'COD';

            $total_weight = 0;
            $total_price = 0;
            $total_order_discount = 0;
            $total_delivery_cost = 0;
            $total_tax = 0;
            $total_paid = 0;

            foreach ($products as $product) {
                $total_order_discount += $product->couponDiscountedAmount();
                $total_delivery_cost += $product['delivery_cost'];
                $total_tax += $product['tax'];
                $total_price += $product['quantity'] * $product['unit_price'];
                $total_paid += $product['total_paid'];
                $single_weight = ProductShippingInfo::where('product_id', $product['product_id'])->select('weight')->first();
                if ($single_weight != null) {
                    $total_weight += $single_weight['weight'] * $product['quantity'];
                }
            }
            $total_product_weight = $total_weight / 1000;

            $amount_to_pay = ($total_price + $total_delivery_cost + $total_tax) - $total_order_discount;
            $due_amount = $amount_to_pay - $total_paid;
            $total_payable_amount = $order_info->payment_method == config('tlecommercecore.payment_methods.cod') ? $due_amount : 0;
            $shipping_details = null;
            if ($order_info->shipping_details != null) {
                $shipping_details = [
                    'name' => $order_info->shipping_details->name,
                    'country' => $order_info->shipping_details->country != null ? $order_info->shipping_details->country->translation('name') : null,
                    'state' => $order_info->shipping_details->state != null ? $order_info->shipping_details->state->translation('name') : null,
                    'city' => $order_info->shipping_details->city != null ? $order_info->shipping_details->city->translation('name') : null,
                    'address' => $order_info->shipping_details->address,
                    'postal_code' => $order_info->shipping_details->postal_code,
                    'phone' => $order_info->shipping_details->phone,
                ];
            }
            $customer_name = $order_info->customer_info != null ? $order_info->customer_info->name : $order_info->guest_customer->name;

            $logo = SettingsRepository::getEcommerceSetting('invoice_logo') != null ? getFilePath(SettingsRepository::getEcommerceSetting('invoice_logo'), false) : getFilePath(getGeneralSetting('white_background_logo'), false);
            $system_properties =
                [
                    'title' => getGeneralSetting('system_name'),
                    'logo' => url($logo),
                    'address' => SettingsRepository::getEcommerceSetting('invoice_address'),
                    'phone' => SettingsRepository::getEcommerceSetting('invoice_phone'),
                    'email' => SettingsRepository::getEcommerceSetting('invoice_email'),
                ];


            $data = [
                'order_code' => $order_info->order_code,
                'date' => $order_info->created_at->format('d M Y'),
                'num_of_products' => $num_of_products,
                'total_product_weight' => $total_product_weight,
                'shipping_info' => $shipping_details,
                'shipping_zone' => $shipping_zone,
                'shipping_type' => $shipping_type,
                'payment_method' => $payment_method,
                'total_payable_amount' => $total_payable_amount,
                'shipping_method' => $shipping_method,
                'tracking_id' => $tracking_id,
                'system_properties' => $system_properties,
                'customer_name' => $customer_name
            ];
            return $data;
        } catch (\Exception $e) {
            return null;
        }
    }
    /**
     * Will return invoice date
     *
     * @param Int $order_id
     * @param Array $order_products
     * @return Array
     */
    public function getInvoiceData($order_id, $order_products)
    {
        try {
            $order_info = Orders::find($order_id);

            $products = OrderHasProducts::whereIn('id', $order_products)->select(
                [
                    'product_id',
                    'order_id',
                    'variant_id as variant',
                    'quantity',
                    'unit_price',
                    'delivery_cost',
                    'total_paid',
                    'order_discount',
                    'payment_status',
                    'tax',
                ]
            )->get();

            $payment_method = null;
            if ($order_info->payment_method == config('tlecommercecore.payment_methods.paypal')) {
                $payment_method = 'Paypal';
            } elseif ($order_info->payment_method == config('tlecommercecore.payment_methods.stripe')) {
                $payment_method = 'Stripe';
            } elseif ($order_info->payment_method == config('tlecommercecore.payment_methods.sslcommerz')) {
                $payment_method = 'sslcommerz';
            } elseif ($order_info->payment_method == config('tlecommercecore.payment_methods.paddle')) {
                $payment_method = 'Paddle';
            } elseif ($order_info->payment_method == config('tlecommercecore.payment_methods.paystack')) {
                $payment_method = 'Paystack';
            } elseif ($order_info->payment_method == config('tlecommercecore.payment_methods.razorpay')) {
                $payment_method = 'Razorpay';
            } elseif ($order_info->payment_method == config('tlecommercecore.payment_methods.mollie')) {
                $payment_method = 'Mollie';
            } elseif ($order_info->payment_method == config('tlecommercecore.payment_methods.bank')) {
                $payment_method = 'Bank Transfer';
            } elseif ($order_info->payment_method == config('tlecommercecore.payment_methods.gpay')) {
                $payment_method = 'Gpay';
            }

            $billing_info = [];
            if ($order_info->billing_details != null) {
                $billing_info['name'] = $order_info->billing_details['name'];
                $billing_info['phone'] = $order_info->billing_details['phone'];
                $billing_info['email'] = $order_info->customer_info != null ? $order_info->customer_info->email : $order_info->guest_customer->email;
                $billing_info['address'] = $order_info->billing_details['address'];
                $billing_info['city']
                    = $order_info->billing_details->city != null ? $order_info->billing_details->city->translation('name') : null;
                $billing_info['state'] = $order_info->billing_details->state != null ? $order_info->billing_details->state->translation('name') : null;
                $billing_info['country'] = $order_info->billing_details->country != null ? $order_info->billing_details->country->translation('name') : null;
                $billing_info['postal_code'] = $order_info->billing_details['postal_code'];
            } else {
                $billing_info['name'] = $order_info->customer_info != null ? $order_info->customer_info->name : $order_info->guest_customer->name;
                $billing_info['phone'] = $order_info->customer_info != null ? $order_info->customer_info['phone'] : null;
                $billing_info['email'] = $order_info->customer_info != null ? $order_info->customer_info->email : $order_info->guest_customer->email;
                $billing_info['address'] = null;
                $billing_info['city'] = null;
                $billing_info['state'] = null;
                $billing_info['country'] = null;
                $billing_info['postal_code'] = $order_info->customer_info != null ? $order_info->customer_info['postal_code'] : null;
            }
            $logo = SettingsRepository::getEcommerceSetting('invoice_logo') != null ? getFilePath(SettingsRepository::getEcommerceSetting('invoice_logo'), false) : getFilePath(getGeneralSetting('white_background_logo'), false);
            $system_properties =
                [
                    'title' => getGeneralSetting('system_name'),
                    'logo' => url($logo),
                    'address' => SettingsRepository::getEcommerceSetting('invoice_address'),
                    'phone' => SettingsRepository::getEcommerceSetting('invoice_phone'),
                    'email' => SettingsRepository::getEcommerceSetting('invoice_email'),
                    'paid_image' => SettingsRepository::getEcommerceSetting('invoice_paid_image') != null ? url('/') . getFilePath(SettingsRepository::getEcommerceSetting('invoice_paid_image'), false) : url('/public/backend/assets/img/invoice/paid.jpg'),
                    'unpaid_image' => SettingsRepository::getEcommerceSetting('invoice_unpaid_image') != null ? url('/') . getFilePath(SettingsRepository::getEcommerceSetting('invoice_unpaid_image'), false) : url('/public/backend/assets/img/invoice/unpaid.jpg'),
                ];


            $data = [
                'order_code' => $order_info->order_code,
                'date' => $order_info->created_at->format('d M Y'),
                'payment_method' => $payment_method,
                'billing_info' => $billing_info,
                'system_properties' => $system_properties,
                'products' => $products
            ];
            return $data;
        } catch (\Exception $e) {
            return null;
        }
    }
    /**
     * Will return order payment link
     *
     * @param Int $order_id
     * @return String
     */
    public function makeOrderPaymentLink($order_id)
    {
        try {
            $order = Orders::find($order_id);
            if ($order != null) {
                $customer_type = "guest";
                if ($order->customer_id != null) {
                    $customer_type = "registered";
                }
                $link = $this->generateOrderPaymentLink($order->id, $order->customer_id, $customer_type, $order->payment_method, $order->total_payable_amount, $order->wallet_payment);
                return $link;
            } else {
                return null;
            }
        } catch (\Exception $e) {
            return null;
        } catch (\Error $e) {
            return null;
        }
    }
    /**
     * Will return order payment url
     */
    public  function generateOrderPaymentLink($order_id, $customer_id, $customer_type, $payment_method, $payable_amount, $is_wallet_payment = 2)
    {
        $base_url = url('/');
        if ($is_wallet_payment == config('settings.general_status.active')) {
            return $base_url . '/order-success/' . $order_id;
        } else {
            if ($payment_method == config('tlecommercecore.payment_methods.cod')) {
                return $base_url . '/order-success/' . $order_id;
            } else {
                if ($payment_method == config('tlecommercecore.payment_methods.bank')) {

                    return $base_url . '/order-success/' . $order_id;
                } else {
                    $payment_method = PaymentMethods::find($payment_method);
                    $url = $base_url . '/payment/' . Str::slug($payment_method->name) . '/pay';
                    session()->put('payment_type', 'checkout');
                    session()->put('order_id', $order_id);
                    session()->put('payable_amount', $payable_amount);
                    session()->put('payment_method', $payment_method->name);
                    session()->put('payment_method_id', $payment_method->id);
                    session()->put('redirect_url', $url);
                    if ($customer_type == 'guest') {
                        session()->put('guest_customer', $customer_id);
                    } else {
                        session()->put('customer', $customer_id);
                    }
                    return $url;
                }
            }
        }
    }
}
