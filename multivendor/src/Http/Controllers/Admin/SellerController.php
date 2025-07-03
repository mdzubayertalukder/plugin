<?php

namespace Plugin\Multivendor\Http\Controllers\Admin;

use Core\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Core\Http\Requests\UserRequest;
use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Support\Facades\Hash;
use Plugin\Multivendor\Models\SellerShop;
use Plugin\TlcommerceCore\Models\Product;
use Plugin\TlcommerceCore\Models\ProductReview;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Plugin\Multivendor\Models\SellerPayoutRequests;
use Plugin\Multivendor\Repositories\SellerRepository;
use Plugin\Multivendor\Http\Requests\ShopUpdateRequest;
use Plugin\TlcommerceCore\Repositories\OrderRepository;
use Plugin\Multivendor\Http\Requests\SellerUpdateRequest;
use Plugin\TlcommerceCore\Repositories\ProductRepository;

class SellerController extends Controller
{

    public function __construct(public SellerRepository $sellerRepository, public OrderRepository $order_repository, public ProductRepository $productRepository)
    {
    }
    /**
     * Will redirect seller list
     *
     * @param \Illuminate\Http\Request $request
     */
    public function sellerList(Request $request)
    {
        $sellers = $this->sellerRepository->sellerList($request);
        return view('plugin/multivendor::admin.sellers.list', ['sellers' => $sellers]);
    }
    /**
     * Will return seller details page
     * @param Int $id
     */
    public function sellerDetails($id, Request $request)
    {
        $seller_details = $this->sellerRepository->sellerDetails($id);

        if ($seller_details == null) {
            abort(404);
        }

        $total_sales = \Plugin\TlcommerceCore\Models\OrderHasProducts::where('seller_id', $id)
            ->select('unit_price', 'quantity')
            ->get()
            ->sum(function ($sale) {
                return $sale->unit_price * $sale->quantity;
            });

        //Order List
        $order_query = \Plugin\TlcommerceCore\Models\Orders::with([
            'customer_info',
            'guest_customer',
            'products' => function ($query) use ($id) {
                $query->where('seller_id', $id)->select('order_id', 'seller_id', 'quantity', 'delivery_cost', 'unit_price', 'tax');
            },
        ])
            ->select('order_code', 'id', 'created_at', 'total_payable_amount', 'customer_id')
            ->orderBy('id', 'DESC');

        $order_query = $order_query->whereHas('products', function ($q) use ($id) {
            $q->where('seller_id', $id);
        });
        $orders = $order_query->get();
        //Seller products
        $products = Product::with(['product_translations', 'variations', 'reviews', 'single_price', 'unit_info'])->where('supplier', $id)->get();
        //refunds
        if (isActivePluging('refund')) {
            $refund_query = \Plugin\Refund\Models\OrderReturnRequest::with(['customer' => function ($q) {
                $q->select('id', 'uid', 'name');
            }, 'product' => function ($q) {
                $q->select('*');
            }, 'order' => function ($q) {
                $q->select(['id', 'order_code', 'customer_id']);
            }]);
            $refund_query = $refund_query->whereHas('product', function (Builder $query) use ($id) {
                $query->where('supplier', $id);
            });
            $refunds = $refund_query->get();
        } else {
            $refunds = null;
        }
        //Payouts
        $payout_query = SellerPayoutRequests::with(['seller'])
            ->orderBy('id', 'DESC');


        $payout_query = $payout_query->whereHas('seller', function ($q) use ($id) {
            $q->where('id', $id);
        });
        $payouts = $payout_query->get();

        //Product reviews
        $data = [
            'tl_com_product_reviews.id',
            'tl_com_product_reviews.status',
            'tl_com_product_reviews.created_at',
            'tl_com_customers.name as customer_name',
            'tl_com_customers.id as customer_id',
            'tl_com_orders.order_code as order_code',
            'tl_com_orders.id as order_id',
            'tl_com_product_reviews.rating',
            'tl_com_products.name as product_name',
            'tl_com_products.id as product_id'
        ];

        $reviews = ProductReview::query()
            ->join('tl_com_customers', 'tl_com_customers.id', '=', 'tl_com_product_reviews.customer_id')
            ->join('tl_com_products', 'tl_com_products.id', '=', 'tl_com_product_reviews.product_id')
            ->join('tl_com_orders', 'tl_com_orders.id', '=', 'tl_com_product_reviews.order_id')
            ->orderBy('tl_com_product_reviews.id', 'DESC')
            ->select($data)
            ->where('tl_com_products.supplier', $id)
            ->get();


        return view('plugin/multivendor::admin.sellers.details')->with(
            [
                'seller_details' => $seller_details,
                'total_sales' => $total_sales,
                'products' => $products,
                'orders' => $orders,
                'refunds' => $refunds,
                'payouts' => $payouts,
                'reviews' => $reviews
            ]
        );
    }

    /**
     * Will delete a seller
     *
     * @param \Illuminate\Http\Request $request
     */
    public function deleteSeller(Request $request)
    {
        $res = $this->sellerRepository->deleteSeller($request['id']);
        if ($res) {
            toastNotification('success', 'Seller deleted successfully');
            return redirect()->back();
        } else {
            toastNotification('error', 'Seller deleted failed');
            return redirect()->back();
        }
    }
    /**
     * Will update seller info
     *
     * @param SellerUpdateRequest $request
     */
    public function updateSeller(SellerUpdateRequest $request)
    {
        try {
            $request->validate([
                'phone' => 'required',
            ]);
            DB::beginTransaction();
            $user = User::find($request['id']);
            $user->name = $request['name'];
            $user->email = $request['email'];
            if (request('password') != null && request('password_confirmation') != null) {
                $user->password = Hash::make($request['password']);
            }

            $user->image = $request['pro_pic'];
            $user->update();
            $shop = SellerShop::where('seller_id', $user->id)->first();
            $shop->seller_phone = $request['phone'];
            $shop->update();

            DB::commit();

            return response()->json([
                'success' => true
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false
            ]);
        }
    }
    /**
     * Will update seller shop
     *
     * @param ShopUpdateRequest $request
     * @return JsonResponse
     */
    public function updateSellerShop(ShopUpdateRequest $request)
    {
        $res = $this->sellerRepository->updateSellerShop($request);

        return response()->json(
            [
                'success' => $res
            ]
        );
    }
    /**
     * Will return seller dropdown options
     *
     * @param \Illuminate\Http\Request $request
     * @return JsonResponse
     */
    public function sellerDropdownList(Request $request)
    {
        $query = User::select('id', 'name')
            ->where('user_type', 3)
            ->where('status', config('settings.general_status.active'));


        if ($request->has('term')) {
            $term = trim($request->term);
            $query = $query->where('name', 'LIKE',  '%' . $term . '%');
        }

        $sellers = $query->orderBy('id', 'asc')->paginate(10);

        $morePages = true;

        if (empty($sellers->nextPageUrl())) {
            $morePages = false;
        }
        $output = collect($sellers->items())->map(function ($item) {
            return [
                'id' => $item->id,
                'text' => $item->name
            ];
        });
        $results = array(
            "results" => $output,
            "pagination" => array(
                "more" => $morePages
            )
        );

        return response()->json($results);
    }
    /**
     * Will update seller status
     *
     * @param \Illuminate\Http\Request $request
     * @return JsonResponse
     */
    public function updateSellerStatus(Request $request)
    {
        $res = $this->sellerRepository->updateSellerStatus($request['id']);
        return response()->json(
            [
                'success' => $res
            ]
        );
    }

    /**
     * Will update seller shop status
     *
     * @param \Illuminate\Http\Request $request
     * @return JsonResponse
     */
    public function updateSellerShopStatus(Request $request)
    {
        $res = $this->sellerRepository->updateSellerShopStatus($request['id']);
        return response()->json(
            [
                'success' => $res
            ]
        );
    }
}
