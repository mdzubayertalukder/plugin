<?php

namespace Plugin\TlcommerceCore\Http\Controllers\Api;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Plugin\Coupon\Models\CouponProducts;
use Plugin\TlcommerceCore\Models\Cities;
use Plugin\TlcommerceCore\Models\States;
use Illuminate\Support\Facades\Validator;
use Plugin\TlcommerceCore\Models\Country;
use Plugin\TlcommerceCore\Models\Product;
use Plugin\TlcommerceCore\Models\Customers;
use Plugin\TlcommerceCore\Models\ShippingRate;
use Plugin\Coupon\Models\CouponExcludeProducts;
use Plugin\TlcommerceCore\Models\PaymentMethods;
use Plugin\TlcommerceCore\Models\ShippingZoneCities;
use Plugin\TlcommerceCore\Models\SingleProductPrice;
use Plugin\TlcommerceCore\Models\VariantProductPrice;
use Plugin\TlcommerceCore\Models\ProductHasCategories;
use Plugin\TlcommerceCore\Repositories\OrderRepository;
use Plugin\TlcommerceCore\Http\Resources\CityCollection;
use Plugin\TlcommerceCore\Http\Resources\OrderCollection;
use Plugin\TlcommerceCore\Http\Resources\StateCollection;
use Plugin\TlcommerceCore\Http\Resources\CountryCollection;
use Plugin\TlcommerceCore\Http\Requests\GuestCheckoutRequest;
use Plugin\TlcommerceCore\Http\Requests\ProductReturnRequest;
use Plugin\TlcommerceCore\Http\Resources\SingleOrderCollection;
use Plugin\TlcommerceCore\Repositories\PaymentMethodRepository;
use Plugin\TlcommerceCore\Http\Requests\AttachmentUploadRequest;
use Plugin\TlcommerceCore\Http\Resources\PaymentMethodCollection;
use Plugin\TlcommerceCore\Http\Resources\RefundRequestCollection;

class OrderController extends Controller
{
    protected $order_repository;
    protected $payment_method_repository;

    public function __construct(OrderRepository $order_repository, PaymentMethodRepository $payment_method_repository)
    {
        $this->order_repository = $order_repository;
        $this->payment_method_repository = $payment_method_repository;
    }


    /**
     * Will uploads order attachment
     *
     * @param AttachmentUploadRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function uploadOrderAttachment(AttachmentUploadRequest $request)
    {
        $res = $this->order_repository->uploadAttachment($request);
        if ($res != null) {
            return response()->json(
                [
                    'success' => true,
                    'attatchment' => $res
                ]
            );
        } else {
            return response()->json(
                [
                    'success' => false
                ]
            );
        }
    }
    /**
     * Will remove order attachment
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function removeOrderAttachment(Request $request)
    {
        $res = removeMediaById($request['file_id']);
        if ($res) {
            return response()->json([
                'success' => true,
            ]);
        } else {
            return response()->json(
                [
                    'success' > false
                ]
            );
        }
    }
    /**
     * Will return active payment methods
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function activePaymentMethods(Request $request)
    {
        try {
            $is_available_cod = true;
            $products = json_decode($request['products'], true);
            foreach ($products as $product) {
                $res = $this->isAvailableCodInCheckout($product['id'], $request['city'], $request['pickup_point']);
                if (!$res) {
                    $is_available_cod = $res;
                    break;
                }
                $is_available_cod = $res;
            }

            $query = PaymentMethods::where('status', config('settings.general_status.active'));
            if (!$is_available_cod) {
                $query = $query->whereNot('id', config('tlecommercecore.payment_methods.cod'));
            }
            $payment_methods = $query->get();

            return new PaymentMethodCollection($payment_methods);
        } catch (\Exception $e) {
            return response()->json(
                [
                    'success' => false,
                ]
            );
        }
    }

    public function isAvailableCodInCheckout($product_id, $city_id, $pickup_point)
    {
        $product_info = Product::select(['is_active_cod', 'cod_location_type', 'id'])
            ->findOrFail($product_id);

        //Inactive cod
        if ($product_info->is_active_cod != config('settings.general_status.active')) {
            return false;
        }
        //Cod in any location
        if ($product_info->is_active_cod == config('settings.general_status.active') && $product_info->cod_location_type == config('tlecommercecore.cod_location.anywhere')) {
            return true;
        }
        //Cod in custom location
        if ($product_info->is_active_cod == config('settings.general_status.active') && $product_info->cod_location_type == config('tlecommercecore.cod_location.custom')) {
            $delivery_cities = [$city_id];
            if ($pickup_point != null) {
                $delivery_cities = DB::table('tl_com_shipping_zone_has_cities')->whereIn('zone_id', DB::table('tl_pick_up_points')->where('id', $pickup_point)->pluck('zone'))->pluck('city_id');
            }

            //check cities
            $cod_cities = DB::table('tl_com_product_cod_cities')
                ->where('product_id', $product_info->id)
                ->whereIn('city_id', $delivery_cities)
                ->count();
            if ($cod_cities > 0) {
                return true;
            }

            //Check state
            $product_cod_cities = DB::table('tl_com_product_cod_cities')
                ->where('product_id', $product_info->id)
                ->count();
            $delivery_states = DB::table('tl_com_cities')->whereIn('id', $delivery_cities)->pluck('state_id');
            $cod_states = DB::table('tl_com_product_cod_states')
                ->where('product_id', $product_info->id)
                ->whereIn('state_id', $delivery_states)
                ->count();

            if ($cod_states > 0 && $product_cod_cities < 1) {
                return true;
            }
            //Check counties
            $product_cod_states = DB::table('tl_com_product_cod_states')
                ->where('product_id', $product_info->id)
                ->count();
            $cod_countries = DB::table('tl_com_product_cod_countries')
                ->where('product_id', $product_info->id)
                ->whereIn('country_id', DB::table('tl_com_state')->whereIn('id', $delivery_states)->pluck('country_id'))
                ->count();

            if ($cod_countries > 0 && $product_cod_cities < 1 && $product_cod_states < 1) {
                return true;
            }
        }


        return false;
    }
    /**
     * Will return country list
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function countryList()
    {
        try {
            $countries = Country::with(['country_translations'])
                ->where('status', config('settings.general_status.active'))
                ->select('id', 'name', 'code')
                ->orderBy('name', 'ASC')
                ->get();
            return new CountryCollection($countries);
        } catch (\Exception $e) {
            return response()->json(
                [
                    'success' => false
                ]
            );
        }
    }
    /**
     * Will return states of a country
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function countryStates(Request $request)
    {
        try {
            $states = States::with(['state_translations'])->where('country_id', $request['country_id'])
                ->select('name', 'id', 'code')
                ->where('status', config('settings.general_status.active'))
                ->orderBy('name', 'ASC')
                ->get();

            return new StateCollection($states);
        } catch (\Exception $e) {
            return response()->json(
                [
                    'success' => false
                ]
            );
        }
    }
    /**
     * Will return cities of a state
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function stateCities(Request $request)
    {
        try {
            $cities = Cities::with(['city_translations'])->where('state_id', $request['state_id'])
                ->where('status', config('settings.general_status.active'))
                ->select('id', 'name')
                ->orderBy('name', 'ASC')
                ->get();
            return new CityCollection($cities);
        } catch (\Exception $e) {
            return response()->json(
                [
                    'success' => false,
                ]
            );
        }
    }
    /**
     * Will get shipping options
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function shippingOptions(Request $request)
    {

        try {
            return response()->json($this->order_repository->shippingOptions($request));
        } catch (\Exception $e) {
            return response()->json(
                [
                    'success' => false,
                ]
            );
        }
    }
    /**
     * Will calculate Delivery Cost
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function shippingAvaiblityDeliveryCost(Request $request)
    {
        try {
            $shipping_zone = ShippingZoneCities::where('city_id', $request['location'])->first();

            return $shipping_zone;
            //return if selected location has no shipping zone
            if ($shipping_zone == null) {
                return response()->json(
                    [
                        'success' => true,
                        'shipiing_avaible' => false
                    ]
                );
            }

            $shipping_rates = ShippingRate::where('zone_id', $shipping_zone->zone_id)->get();
            //return if zone has no shipping rate
            if (count($shipping_rates) < 1) {
                return response()->json(
                    [
                        'success' => true,
                        'shipiing_avaible' => false
                    ]
                );
            }
            //return if zone has single shhipping rate and rate has no condition
            if (count($shipping_rates) == 1 && $shipping_rates[0]->has_condition == config('settings.general_status.in_active')) {
                return response()->json(
                    [
                        'success' => true,
                        'shipiing_avaible' => true,
                        'standard_delivery_cost' => 10,
                        'express_delivery_cost' => $shipping_rates[0]->express_cost
                    ]
                );
            }

            $express_delivery_cost = 0;
            $standard_delivery_cost = 0;
            if ($request->has('location')) {
                $product_list = json_decode($request->products, true);
                foreach ($product_list as $product) {
                    $shippingAvailability = $this->order_repository->productShippingAvailability($product['id'], $request['location']);
                    if ($shippingAvailability) {
                        //calculate delivery cost
                        $standard_delivery_cost += $this->order_repository->productDeliveryCost($product['id'], $request['location']);
                        $express_delivery_cost += $this->order_repository->productDeliveryCost($product['id'], $request['location']);
                    } else {
                        //return if any product of cart is not available in selected location
                        return response()->json(
                            [
                                'success' => true,
                                'shipiing_avaible' => false
                            ]
                        );
                    }
                }

                return response()->json(
                    [
                        'success' => true,
                        'shipiing_avaible' => true,
                        'standard_delivery_cost' => 10,
                        'express_delivery_cost' => $express_delivery_cost
                    ]
                );
            } else {
                return response()->json(
                    [
                        'success' => false,
                        'message' => 'Invalid Location'
                    ]
                );
            }
        } catch (\Exception $e) {
            return response()->json(
                [
                    'success' => false,
                    'message' => 'Invalid Location'
                ]
            );
        }
    }
    /**
     * Will validate cart items
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function validateCartItems(Request $request)
    {
        $items = json_decode($request['items'], true);
        $updated_cart = [];
        foreach ($items as $item) {

            $attachment = null;
            if ($item['attachment'] != null && gettype($item['attachment']) != 'array') {
                $attachment = json_decode($item['attachment']);
            }

            if ($item['attachment'] != null && gettype($item['attachment']) == 'array') {
                $attachment = $item['attachment'];
            }

            $temp['id'] = $item['id'];
            $temp['uid'] = $item['uid'];
            $temp['name'] = $item['name'];
            $temp['permalink'] = $item['permalink'];
            $temp['image'] = $item['image'];
            $temp['variant'] = $item['variant'];
            $temp['variant_code'] = $item['variant_code'];
            $temp['quantity'] = $item['quantity'];
            $temp['unitPrice'] = $item['unitPrice'];
            $temp['oldPrice'] = $item['oldPrice'];
            $temp['min_item'] = $item['min_item'];
            $temp['max_item'] = $item['max_item'];
            $temp['attachment'] = $attachment;
            $temp['seller'] = $item['seller'];
            $temp['shop_name'] = $item['shop_name'];
            $temp['shop_slug'] = $item['shop_slug'];
            $temp['is_available'] = $this->checkAvailabilityCartItem($item);
            $temp['is_selected'] = $this->checkAvailabilityCartItem($item) == 1 ? true : false;
            array_push($updated_cart, $temp);
        }

        return response()->json(
            [
                'success' => true,
                'items' => $updated_cart
            ]
        );
    }

    public function checkAvailabilityCartItem($item)
    {
        $product_details = Product::where('id', $item['id'])->select('supplier', 'id', 'status', 'is_approved', 'has_variant')->first();

        //If Product not found
        if ($product_details == null) {
            return config('settings.general_status.in_active');
        }

        //Check product status
        if ($product_details->status != config('settings.general_status.active')) {
            return config('settings.general_status.in_active');
        }

        //check  single product available stock
        if ($product_details->has_variant == config('tlecommercecore.product_variant.single')) {
            $single_price = SingleProductPrice::where('product_id', $item['id'])->select('quantity', 'product_id')->first();
            if ($single_price == null) {
                return config('settings.general_status.in_active');
            }
            if ($single_price->quantity < $item['quantity']) {
                return config('settings.general_status.in_active');
            }
        }

        //check  variant product available stock
        if ($product_details->has_variant == config('tlecommercecore.product_variant.variable')) {
            $variant_price = VariantProductPrice::where('product_id', $item['id'])
                ->where('variant', $item['variant_code'])
                ->orWhere('variant', $item['variant_code'] . '/')
                ->select('quantity')
                ->first();

            if ($variant_price == null) {
                return config('settings.general_status.in_active');
            }
            if ($variant_price->quantity < $item['quantity']) {
                return config('settings.general_status.in_active');
            }
        }

        //check  seller's product approval status when multivendor active
        if (isActivePluging('multivendor') && $product_details->is_approved != config('settings.general_status.active')) {
            return config('settings.general_status.in_active');
        }

        //check supplier and shop status
        if (isActivePluging('multivendor') && $item['seller'] != null) {

            if ($product_details->seller->status != config('settings.general_status.active')) {
                return config('settings.general_status.in_active');
            }

            if ($product_details->seller->shop == null) {
                return config('settings.general_status.in_active');
            }

            if ($product_details->seller->shop->status != config('settings.general_status.active')) {
                return config('settings.general_status.in_active');
            }
        }


        return config('settings.general_status.active');
    }

    /**
     * Will apply coupon code
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function applyCoupon(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'coupon_code' => 'required'
        ]);

        if ($validator->fails()) {
            return $this->couponError('Enter a valid coupon');
        }
        try {
            //Plugin not activated
            if (!isActivePluging('coupon')) {
                return $this->couponError('Coupon not applicable right now');
            }

            //Coupon details
            $coupon_details = \Plugin\Coupon\Models\Coupons::where('code', $request['coupon_code'])->first();
            if ($coupon_details == null) {
                return $this->couponError('Coupon Not Found');
            }

            //check expire data
            $today = Carbon::now()->toDateString();
            if ($coupon_details->expire_date != null && $coupon_details->expire_date < $today) {
                return $this->couponError('Coupon is Expired');
            }

            //Check Allowed email
            if ($coupon_details->alowed_email != null) {
                //If customer id not found
                if ($request['customer_id'] == null) {
                    return $this->couponError('Invalid Coupon');
                }

                //customer details
                $customer_details = Customers::where('id', $request['customer_id'])->first();
                //Customer not found
                if ($customer_details == null) {
                    return $this->couponError('Invalid Coupon');
                }
                //when email not match
                if ($customer_details->email != $coupon_details->alowed_email) {
                    return $this->couponError('Invalid Coupon');
                }
            }

            //Check usage limit per coupon
            $per_coupon_usage = $coupon_details->usage_limit_per_coupon;
            if ($per_coupon_usage != null) {
                $previous_usage = \Plugin\Coupon\Models\CouponUsage::where('coupon_id', $coupon_details->id)->count();
                if ($previous_usage >= $per_coupon_usage) {
                    return $this->couponError('Crossed the limit of usage');
                }
            }
            //Check usage limit per user
            $coupon_usage_per_user = $coupon_details->usage_limit_per_user;
            if ($coupon_usage_per_user != null && $request['customer_id'] != null) {
                $previous_user_usage = \Plugin\Coupon\Models\CouponUsage::where('coupon_id', $coupon_details->id)
                    ->where('customer_id', $request['customer_id'])
                    ->count();
                if ($previous_user_usage >= $coupon_usage_per_user) {
                    return $this->couponError('You have crossed the limit of usage');
                }
            }

            $products = json_decode($request['products'], true);
            $total_cart_price = array_reduce($products, function ($sum, $item) {
                $sum += $item['unitPrice'] * $item['quantity'];
                return $sum;
            }, 0);

            //Minimum spend validation
            if ($coupon_details->minimum_spend_amount != null && $coupon_details->minimum_spend_amount > $total_cart_price) {
                return $this->couponError('You have to need more shopping to apply this coupon');
            }

            //Maximum spend validation
            if ($coupon_details->maximum_spend_amount != null && $coupon_details->maximum_spend_amount < $total_cart_price) {
                return $this->couponError('Coupon is not applicable');
            }



            $cart_items = array_map(function ($product) {
                return $product['id'];
            }, $products);

            $applicable_product_id = $cart_items;

            //Filter selected product
            $selected_products = CouponProducts::where('coupon_id', $coupon_details->id)->pluck('product_id')->toArray();
            if (count($selected_products) > 0) {
                $applicable_product_id = array_intersect($applicable_product_id, $selected_products);
                $applicable_product_id = array_values($applicable_product_id);
            }

            //Selected categories
            $selected_categories = $coupon_details->categories->pluck('category_id');
            if (count($selected_categories) > 0) {
                $applicable_product_id = ProductHasCategories::whereIn('product_id', $cart_items)->whereIn('category_id', $selected_categories)->pluck('product_id');
            }

            //Selected Brand
            $selected_brands = $coupon_details->brands->pluck('brand_id');
            if (count($selected_brands) > 0) {
                $applicable_product_id = Product::whereIn('id', $cart_items)->whereIn('brand', $selected_brands)->pluck('id');
            }


            //Filter exclude products
            $exclude_products = CouponExcludeProducts::where('coupon_id', $coupon_details->id)->pluck('product_id')->toArray();
            if (count($exclude_products) > 0) {
                $applicable_product_id = array_diff($cart_items, $exclude_products);
            }

            $discounted_amount = 0;
            //Flat Discount
            if ($coupon_details->discount_type == config('tlecommercecore.amount_type.flat')) {
                $discounted_amount = $coupon_details->discount_amount;
            }
            //Percent Discount
            if ($coupon_details->discount_type != config('tlecommercecore.amount_type.flat')) {
                $total_price = 0;
                foreach ($products as $item) {
                    foreach ($applicable_product_id as $productId) {
                        if ($productId == $item['id']) {
                            $temp = $item['unitPrice'] * $item['quantity'];
                            $total_price += $temp;
                        }
                    }
                }
                $discounted_amount = ($total_price * $coupon_details->discount_amount) / 100;
            }

            return response()->json(
                [
                    'success' => true,
                    'discount' => $discounted_amount,
                    'coupon_code' => $coupon_details->code,
                    'coupon_id' => $coupon_details->id,
                    'free_shipping' => $coupon_details->free_shipping
                ]
            );
        } catch (\Exception $e) {
            return response()->json(
                [
                    'success' => false,
                ]
            );
        }
    }
    /**
     * Will return apply coupon error response
     *
     * @param String $message
     */
    public function couponError($message = null)
    {
        return response()->json(
            [
                'success' => false,
                'message' => translate($message, session()->get('api_locale'))
            ]
        );
    }

    /**
     * Will place customer order
     *
     * @param \Illuminate\Http\Request $request
     * @param \Illuminate\Http\JsonResponse
     */
    public function createCustomerOrder(Request $request)
    {
        if ($request['payment_id'] == config('tlecommercecore.payment_methods.bank')) {
            $request->validate([
                'receipt' => 'required|mimes:png,jpg,pdf',
                'bank_name' => 'nullable|max:250',
                'branch_name' => 'nullable|max:250',
                'account_number' => 'nullable|max:250',
                'account_name' => 'nullable|max:250',
                'transaction_number' => 'nullable|max:250',
            ]);
        }

        $response_url = $this->order_repository->customerCheckout($request);
        if ($response_url != NULL) {
            return response()->json(
                [
                    'success' => true,
                    'response_url' => $response_url
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
     * Will create guest order
     *
     * @param GuestCheckoutRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function guestCheckout(GuestCheckoutRequest $request)
    {
        $response_url = $this->order_repository->guestCheckout($request);
        if ($response_url != NULL) {
            return response()->json(
                [
                    'success' => true,
                    'response_url' => $response_url
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
     * Will cancel a order
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function cancelOrder(Request $request)
    {
        if ($request->has('item_id') && $request['item_id'] != null) {
            $res = $this->order_repository->changeOrderItemStatus($request['item_id'], $request['order_id'], config('tlecommercecore.order_delivery_status.cancelled'));
        } else {
            $res = $this->order_repository->cancelOrder($request['order_id']);
        }


        if ($res) {
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
     * Will return customer orders
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function customerOrders(Request $request)
    {
        return new OrderCollection($this->order_repository->customerOrders($request, auth('jwt-customer')->user()->id));
    }
    /**
     * Generate order payment link
     */
    public function makeOrderPayment(Request $request)
    {
        $link = $this->order_repository->makeOrderPaymentLink($request['order_id']);
        if ($link != null) {
            return response()->json(
                [
                    'success' => true,
                    'link' => $link
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
     * Will return customer order details
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function customerOrderDetails(Request $request)
    {
        if ($request->has('order_id')) {
            return new SingleOrderCollection($this->order_repository->customerOrderDetails(auth('jwt-customer')->user()->id, $request['order_id']));
        } else {
            return new SingleOrderCollection($this->order_repository->OrderDetailsByOrderId($request->order_code));
        }
    }
    /**
     * Will store customer return product details
     *
     * @param ProductReturnRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function customerOrderReturn(ProductReturnRequest $request)
    {
        if ($request['return_images'] != null) {
            $imageRules = array(
                'return_images' => 'nullable|image|mimes:jpg,jpeg,png|max:2000'
            );
            foreach ($request['return_images'] as $image) {
                $image = array('return_images' => $image);
                $imageValidator = Validator::make($image, $imageRules);
                if ($imageValidator->fails()) {
                    return response()->json(['errors' => $imageValidator->errors()], 422);
                }
            }
        }
        $res = $this->order_repository->returnOrder($request);
        if ($res) {
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
     * Will return customer refunds requests
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function customerReturnRequests(Request $request)
    {
        return new RefundRequestCollection($this->order_repository->customerReturnRequests($request, auth('jwt-customer')->user()->id));
    }
    /**
     * Will return guest customer order details
     */
    public function guestCustomerOrderDetails(Request $request)
    {
        return new SingleOrderCollection($this->order_repository->OrderDetailsByOrderId($request['order_code']));
    }
    /**
     * Will store a product review
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function reviewProduct(Request $request)
    {
        if ($request['review_images'] != null) {
            $imageRules = array(
                'review_images' => 'nullable|image|mimes:jpg,jpeg,png|max:2000'
            );
            foreach ($request['review_images'] as $image) {
                $image = array('review_images' => $image);
                $imageValidator = Validator::make($image, $imageRules);
                if ($imageValidator->fails()) {
                    return response()->json(['errors' => $imageValidator->errors()], 422);
                }
            }
        }
        $res = $this->order_repository->storeCustomerProductReview($request, auth('jwt-customer')->user()->id);

        if ($res) {
            return response()->json(
                [
                    'success' => true
                ]
            );
        } else {
            return response()->json(
                [
                    'success' => false
                ]
            );
        }
    }
}
