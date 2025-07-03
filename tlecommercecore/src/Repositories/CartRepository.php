<?php

namespace Plugin\TlcommerceCore\Repositories;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Plugin\TlcommerceCore\Models\CartItem;

class CartRepository
{
    /**
     * This method will store customer cart items
     * 
     * @param Int $customer_id
     * @param Array $request
     * @return Boolean
     */
    public function storeCartItem($request, $customer_id)
    {
        try {
            $item = json_decode($request['item'], true);
            DB::beginTransaction();
            $cart_item = CartItem::where('customer_id', $customer_id)
                ->where('product_id', $item['id'])
                ->where('variant_code', $item['variant_code'])
                ->first();

            if ($cart_item != null) {
                $updated_quantity = $cart_item->quantity + $item['quantity'];
                if ($updated_quantity <= $item['max_item']) {
                    $cart_item->quantity = $updated_quantity;
                    $cart_item->save();
                } else {
                    return false;
                }
            }

            if ($cart_item == null) {
                $new_item = new CartItem();
                $new_item->customer_id = $customer_id;
                $new_item->product_id = $item['id'];
                $new_item->variant_code = $item['variant_code'];
                $new_item->variant = $item['variant'];
                $new_item->uid = $item['uid'];
                $new_item->unitPrice = $item['unitPrice'];
                $new_item->oldPrice = $item['oldPrice'];
                $new_item->quantity = $item['quantity'];
                $new_item->attachment = json_encode($item['attachment']);
                $new_item->image = $item['image'];
                $new_item->min_item = $item['min_item'];
                $new_item->max_item = $item['max_item'];
                $new_item->save();
            }

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
     * Will get customer cart items
     * 
     * @param Int $customer_id
     * @return Collections
     */
    public function customerCartItems($customer_id)
    {
        try {
            $items = CartItem::with(['product' => function ($q) {
                $q->with(['seller' => function ($s) {
                    $s->select('id', 'name');
                }])->select('id', 'name', 'permalink', 'supplier');
            }])
                ->where('customer_id', $customer_id)
                ->get();
            return $items;
        } catch (\Exception $e) {
            return [];
        }
    }
    /**
     * This method will remove cart item
     * 
     * @param Int $customer_id
     * @param String $uid
     * @return bool 
     */
    public function removeCartItem($customer_id, $uid)
    {
        try {
            DB::beginTransaction();
            if ($uid == 'all') {
                $cart_item = CartItem::where('customer_id', $customer_id)
                    ->delete();
            }

            if ($uid != 'all') {
                $cart_item = CartItem::where('customer_id', $customer_id)
                    ->where('uid', $uid)
                    ->first();
                $cart_item->delete();
            }

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

    public function updateCartItem($request, $customer_id)
    {
        try {
            $item = json_decode($request['item'], true);
            DB::beginTransaction();
            $cart_item = CartItem::where('customer_id', $customer_id)
                ->where('uid', $item['uid'])
                ->first();
            if ($cart_item != null) {
                $cart_item->product_id = $item['id'];
                $cart_item->variant_code = $item['variant_code'];
                $cart_item->variant = $item['variant'];
                $cart_item->unitPrice = $item['unitPrice'];
                $cart_item->oldPrice = $item['oldPrice'];
                $cart_item->quantity = $item['quantity'];
                $cart_item->attachment = $item['attachment'];
                $cart_item->image = $item['image'];
                $cart_item->min_item = $item['min_item'];
                $cart_item->max_item = $item['max_item'];
                $cart_item->save();
                DB::commit();
                return true;
            } else {
                DB::commit();
                return false;
            }
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            return false;
        } catch (\Error $e) {
            DB::rollBack();
            return false;
        }
    }
}
