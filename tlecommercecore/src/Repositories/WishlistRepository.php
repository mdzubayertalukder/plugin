<?php

namespace Plugin\TlcommerceCore\Repositories;

use Plugin\TlcommerceCore\Models\CustomerWishlist;
use Plugin\TlcommerceCore\Models\Product;

class WishlistRepository
{
    /**
     * Store product to wistlist
     * 
     * @param Object $request
     * @return bool
     */
    public function storeWishlistProduct($request, $customer_id)
    {
        try {
            if ($request->has('product_id')) {
                $wishlist = CustomerWishlist::firstOrNew(['product_id' => $request['product_id'], 'customer_id' => $customer_id]);
                $wishlist->save();
                return true;
            } else {
                return false;
            }
        } catch (\Exception $e) {
            return false;
        } catch (\Error $e) {
            return false;
        }
    }
    /**
     * Will return customer wistlist
     * 
     * @param Object $request
     * @param Int $customer_id
     * @return Collections
     */
    public function customerWishlistedProducts($customer_id, $request = null)
    {

        if ($request != null && $request->has('perPage')) {
            return Product::whereIn('id', CustomerWishlist::where('customer_id', $customer_id)->pluck('product_id'))->paginate($request['perPage']);
        } else {
            return Product::whereIn('id', CustomerWishlist::where('customer_id', $customer_id)->pluck('product_id'))->get();
        }
    }
    /**
     * Remove product to wistlist
     * 
     * @param Object $request
     * @return bool
     */
    public function removeWishlistProduct($request, $customer_id)
    {
        try {
            if ($request->has('product_id')) {
                $wishlist = CustomerWishlist::where('product_id', $request['product_id'])->where('customer_id', $customer_id)->first();
                if ($wishlist != null) {
                    $wishlist->delete();
                    return true;
                } else {
                    return false;
                }
            } else {
                return false;
            }
        } catch (\Exception $e) {
            return false;
        } catch (\Error $e) {
            return false;
        }
    }
}
