<?php

namespace Plugin\TlcommerceCore\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;
use Plugin\TlcommerceCore\Repositories\ProductRepository;
use Plugin\TlcommerceCore\Repositories\CustomerRepository;
use Plugin\TlcommerceCore\Repositories\WishlistRepository;
use Plugin\TlcommerceCore\Http\Requests\CustomerUpdateRequest;
use Plugin\TlcommerceCore\Http\Resources\SingleCustomerCollection;
use Plugin\TlcommerceCore\Http\Requests\CustomerResetPasswordRequest;

class CustomerController extends Controller
{
    protected $customer_repository;
    protected $product_repository;
    protected $wishlist_repository;

    public function __construct(CustomerRepository $customer_repository, ProductRepository $product_repository, WishlistRepository $wishlist_repository)
    {
        $this->customer_repository = $customer_repository;
        $this->product_repository = $product_repository;
        $this->wishlist_repository = $wishlist_repository;
    }
    /**
     * Will return customer list
     * 
     * @param \Illuminate\Http\Request $request
     * @return mixed
     */
    public function customers(Request $request)
    {
        $customers = $this->customer_repository->customerList($request);

        return view('plugin/tlecommercecore::customers.index')->with(
            [
                'customers' => $customers
            ]
        );
    }
    /**
     * Will redirect customer details page
     * 
     * @param Int $id
     * @return mixed
     */
    public function customerDetails($id)
    {
        $customer_details = $this->customer_repository->customerDetails($id);
        $customer_orders = $this->customer_repository->customerOrderList($id);
        $return_requests = $this->customer_repository->customerReturnRequests($id);
        $reviews = $this->product_repository->customerReviewList($id);
        $wishlist = $this->wishlist_repository->customerWishlistedProducts($id);
        $customer_addresses = $this->customer_repository->customerAllAddress($id);
        $customer_wallet_transactions = $this->customer_repository->customerWalletTransaction($id);

        $total_credit = collect($customer_wallet_transactions)->count() > 0 ? $customer_wallet_transactions->where('entry_type', config('tlecommercecore.wallet_entry_type.credit'))
            ->where('status', config('tlecommercecore.wallet_transaction_status.accept'))
            ->sum('recharge_amount') : 0;
        $total_debit = collect($customer_wallet_transactions)->count() > 0 ? $customer_wallet_transactions->where('entry_type', config('tlecommercecore.wallet_entry_type.debit'))
            ->where('status', config('tlecommercecore.wallet_transaction_status.accept'))
            ->sum('recharge_amount') : 0;

        $total_purchase = array_reduce($customer_orders->toArray(), function ($a, $b) {
            return $a + $b->total_payable_amount;
        }, 0);

        $cancelled_orders = DB::table('tl_com_orders')
            ->leftjoin('tl_com_ordered_products', 'tl_com_ordered_products.order_id', 'tl_com_orders.id')
            ->groupBy('tl_com_orders.id')
            ->where('tl_com_orders.customer_id', $id)
            ->where('tl_com_ordered_products.delivery_status', config('tlecommercecore.order_delivery_status.cancelled'))
            ->select(['tl_com_orders.id'])
            ->get()->count();

        return view('plugin/tlecommercecore::customers.details')->with(
            [
                'customer_details' => $customer_details,
                'customer_orders' => $customer_orders,
                'return_requests' => $return_requests,
                'reviews' => $reviews,
                'wishlists' => $wishlist,
                'customer_addresses' => $customer_addresses,
                'total_purchase' => $total_purchase,
                'cancelled_orders' => $cancelled_orders,
                'customer_wallet_transactions' => $customer_wallet_transactions,
                'total_credit' => $total_credit,
                'total_debit' => $total_debit
            ]
        );
    }

    /**
     * Will change customer status
     * 
     * @param \Illuminate\Http\Request $request
     * @return mixed
     */
    public function changeCustomerStatus(Request $request)
    {
        $res = $this->customer_repository->changeStatus($request['id']);
        if ($res) {
            toastNotification('success', 'Status updated successfully');
        } else {
            toastNotification('error', 'Status update failed');
        }
    }
    /**
     * Will reset customer password
     * 
     * @param 
     * 
     * @return \Illuminate\Http\Resources;
     */
    public function resetCustomerPassword(CustomerResetPasswordRequest $request)
    {
        $res = $this->customer_repository->changeCustomerPassword($request['id'], $request['password']);
        if ($res) {
            return response()->json([
                'success' => true,
            ]);
        } else {
            return response()->json([
                'success' => false,
            ]);
        }
    }

    /** 
     * Will update customer basic info
     * 
     */
    public function updateCustomerInfo(CustomerUpdateRequest $request)
    {
        $res = $this->customer_repository->updateCustomerBasicInfo($request, $request['id']);
        if ($res) {
            return response()->json([
                'success' => true,
            ]);
        } else {
            return response()->json([
                'success' => false,
            ]);
        }
    }
    /**
     * Customer secret login
     * 
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function customerSecretLogin(Request $request)
    {
        $customer_details = $this->customer_repository->customerDetails($request['id']);
        $token = JWTAuth::fromUser($customer_details);
        if (!$token) {
            return response()->json(
                [
                    'success' => false,
                ]
            );
        }
        return $this->createNewToken($token);
    }

    /**
     * Will delete a customer
     * 
     * @param \Illuminate\Http\Request $request
     * @return mixed
     */
    public function deleteCustomer(Request $request)
    {
        $res = $this->customer_repository->deleteCustomer($request['id']);
        if ($res) {
            toastNotification('success', translate('Customer delete successfully'));
        } else {
            toastNotification('error', translate('Unable to delete this customer'));
        }
        return redirect()->back();
    }
    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function createNewToken($token)
    {
        return response()->json([
            'success' => true,
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth('jwt-customer')->factory()->getTTL() * 60,
            'user' => new SingleCustomerCollection(auth('jwt-customer')->user()),
            'dashboard_content' => $this->customer_repository->customerDashboardDetails(auth('jwt-customer')->user()->id)
        ]);
    }
}
