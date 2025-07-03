<?php

namespace Plugin\Multivendor\Http\Controllers\Api;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Validation\ValidationException;
use Plugin\Multivendor\Repositories\SellerRepository;
use Plugin\Multivendor\Http\Requests\SellerRegistrationRequest;

class SellerAuthenticationController extends Controller
{

    public function __construct(public SellerRepository $seller_repository)
    {
    }
    /**
     * Seller registration
     *
     * @param SellerRegistrationRequest $request
     * @return JsonResponse
     */
    public function sellerRegistration(SellerRegistrationRequest $request)
    {
        $slug = Str::slug($request['shop_url']);
        $available = $this->seller_repository->checkSellerShopAvailability($slug);

        if (!$available) {
            throw ValidationException::withMessages(
                [
                    'shop_url' => [translate('Shop url is not available', session()->get('api_locale'))]
                ]
            );
        }

        $res = $this->seller_repository->storeSeller($request->validated());
        return response()->json([
            'success' => $res
        ]);
    }

    /**
     * Will check seller shop url
     *
     * @param \Illuminate\Http\Request $request
     * @return JsonResponse
     */
    public function shopAvailabilityCheck(Request $request)
    {
        $slug = Str::slug($request['slug']);
        $available = $this->seller_repository->checkSellerShopAvailability($slug);
        if (!$available) {
            throw ValidationException::withMessages(
                [
                    'shop_url' => [translate('Shop url is not available', session()->get('api_locale'))]
                ]
            );
        }
        return response()->json([
            'success' => true,
            'slug' => $slug,
            'availability' => $available
        ]);
    }
}
