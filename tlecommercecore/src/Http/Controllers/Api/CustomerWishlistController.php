<?php

namespace Plugin\TlcommerceCore\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Plugin\TlcommerceCore\Http\Resources\ProductCollection;
use Plugin\TlcommerceCore\Repositories\WishlistRepository;

class CustomerWishlistController extends Controller
{
    protected $wishlist_repository;

    public function __construct(WishlistRepository $wishlist_repository)
    {
        $this->wishlist_repository = $wishlist_repository;
    }
    /**
     * Will store product to  customer wishlist
     * 
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse 
     */
    public function storeProductToWishlist(Request $request)
    {
        $res = $this->wishlist_repository->storeWishlistProduct($request, auth('jwt-customer')->user()->id);
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
     * Will return customer wishlisted product
     * 
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getCustomerWishlistProducts(Request $request)
    {
        return new ProductCollection($this->wishlist_repository->customerWishlistedProducts(auth('jwt-customer')->user()->id, $request));
    }
    /**
     * Will remove product from wishlist
     * 
     * @param \Illuminate\Http\Request $request
     * @param \Illuminate\Http\JsonResponse
     */
    public function removeProductFromWishlist(Request $request)
    {
        $res = $this->wishlist_repository->removeWishlistProduct($request, auth('jwt-customer')->user()->id);
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
}
