<?php

namespace Plugin\TlcommerceCore\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Plugin\TlcommerceCore\Http\Resources\CartItemsCollection;
use Plugin\TlcommerceCore\Repositories\CartRepository;

class CartController extends Controller
{

    public function __construct(protected CartRepository $cart_repository)
    {
    }

    /**
     * This method will store cart item
     * 
     * @param \Illuminate\Http\Request $request
     * @return Response
     */
    public function storeCartProduct(Request $request)
    {
        $res = $this->cart_repository->storeCartItem($request, auth('jwt-customer')->user()->id);
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
     * This method will return customer cart items
     * 
     * @return Response
     */
    public function getCartItems()
    {
        $items = $this->cart_repository->customerCartItems(auth('jwt-customer')->user()->id);
        return new CartItemsCollection($items);
    }

    /**
     * This method will remove cart item
     * 
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function removeCartItem(Request $request)
    {
        $res = $this->cart_repository->removeCartItem(auth('jwt-customer')->user()->id, $request['uid']);
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
     * This method will update cart item
     * 
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateCartItem(Request $request)
    {
        $res = $this->cart_repository->updateCartItem($request, auth('jwt-customer')->user()->id);
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
