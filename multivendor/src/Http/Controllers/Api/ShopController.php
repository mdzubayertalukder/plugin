<?php

namespace Plugin\Multivendor\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Plugin\Multivendor\Resources\ShopResource;
use Plugin\Multivendor\Repositories\SellerRepository;
use Plugin\Multivendor\Resources\ShopResourceCollection;
use Plugin\TlcommerceCore\Repositories\ProductRepository;
use Plugin\TlcommerceCore\Http\Resources\ProductCollection;
use Plugin\TlcommerceCore\Http\Resources\ProductReviewCollection;

class ShopController extends Controller
{

    public function __construct(public SellerRepository $sellerRepository, public ProductRepository $product_repository)
    {
    }

    /**
     * Will return all shop list
     * 
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function activeShopList(Request $request)
    {
        $shop_list = $this->sellerRepository->activeShops($request);
        return new ShopResourceCollection($shop_list);
    }
    /**
     * Will return top seller list
     * 
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function topSellerList(Request $request)
    {
        $shop_list = $this->sellerRepository->activeShops($request);
        return new ShopResourceCollection($shop_list);
    }
    /**
     * Will return shop details
     * 
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function shopDetails(Request $request)
    {
        $seller_id = $this->sellerRepository->sellerIdByShopSlug($request['slug']);

        $shop_details = $this->sellerRepository->activeShopDetailsBySlug($request['slug']);
        if ($shop_details == null) {
            return response()->json(
                [
                    'status' => 200,
                    'success' => false,
                ]
            );
        }

        return response()->json(
            [
                'status' => 200,
                'success' => true,
                'details' => new ShopResource($shop_details),
                'review_summary' => $this->sellerRepository->sellerReviewSummary($seller_id)
            ]
        );
    }

    /**
     * Will return seller shop products
     * 
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function shopProducts(Request $request)
    {
        $seller_id = $this->sellerRepository->sellerIdByShopSlug($request['slug']);
        $query = $this->product_repository->filterProducts($request, $seller_id);
        $query = $query->where('supplier', $seller_id);
        $top_selling_items = $this->sellerRepository->shopProducts($query, 'top_selling');

        $query1 = $this->product_repository->filterProducts($request, $seller_id);
        $query1 = $query1->where('supplier', $seller_id);
        $new_items = $this->sellerRepository->shopProducts($query1, 'new');

        $query2 = $this->product_repository->filterProducts($request, $seller_id);
        $query2 = $query2->where('supplier', $seller_id);
        $featured_items = $this->sellerRepository->shopProducts($query2, 'featured');
        return response()->json(
            [
                'success' => true,
                'new_items' => new ProductCollection($new_items),
                'featured_items' => new ProductCollection($featured_items),
                'top_selling_items' => new ProductCollection($top_selling_items),

            ]
        );
    }

    /**
     * Will return Shop All Products
     * 
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function shopAllProducts(Request $request)
    {
        $seller_id = $this->sellerRepository->sellerIdByShopSlug($request['slug']);
        $query = $this->product_repository->filterProducts($request, $seller_id);
        //sorting by newest items
        if ($request->has('sorting') && $request['sorting'] === 'newest') {
            $query = $query->orderBy('id', 'DESC');
        }
        //sorting by popular items
        if ($request->has('sorting') && $request['sorting'] === 'popular') {
            $query = $query->withCount('orders as number_of_order')
                ->orderBy('number_of_order', 'desc');
        }
        //Price low to high
        if ($request->has('sorting') && $request['sorting'] == 'lowToHigh') {
            $query = $query->whereHas('single_price', function ($q) {
                $q->orderBy('unit_price', 'ASC');
            })->orWhereHas('variations', function ($q) {
                $q->orderBy('unit_price', 'ASC');
            });
        }
        //Price high to low
        if ($request->has('sorting') && $request['sorting'] == 'highToLow') {
            $query = $query->whereHas('single_price', function ($q) {
                $q->orderBy('unit_price', 'DESC');
            })->orWhereHas('variations', function ($q) {
                $q->orderBy('unit_price', 'DESC');
            });
        }

        $products = $query->paginate($request->perPage);

        return new ProductCollection($products);
    }
    /**
     * Will return seller reviews
     * 
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function shopAllReviews(Request $request)
    {
        $seller_id = $this->sellerRepository->sellerIdByShopSlug($request['slug']);

        $reviews = $this->sellerRepository->sellerAllReviews($request, $seller_id);
        return  new ProductReviewCollection($reviews);
    }
    /**
     * Store shop follower
     * 
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function storeShopFollower(Request $request)
    {
        $seller_id = $this->sellerRepository->sellerIdByShopSlug($request['slug']);
        if (DB::table('tl_com_seller_followers')->where('seller_id', $seller_id)->where('customer_id', auth('jwt-customer')->user()->id)->exists()) {
            return response()->json([
                'status' => 200,
                'success' => true,
                'duplicate' => true,
            ]);
        }
        $res = $this->sellerRepository->storeSellerFollower($seller_id, auth('jwt-customer')->user()->id);
        return response()->json([
            'status' => 200,
            'success' => $res,
        ]);
    }
}
