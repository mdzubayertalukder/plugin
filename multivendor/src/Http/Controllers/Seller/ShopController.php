<?php

namespace Plugin\Multivendor\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use Plugin\Multivendor\Repositories\SellerRepository;
use Plugin\Multivendor\Http\Requests\ShopUpdateRequest;
use Plugin\Multivendor\Http\Requests\ShopSeoUpdateRequest;

class ShopController extends Controller
{
    public function __construct(public SellerRepository $sellerRepository)
    {
    }

    /**
     * Will return shop settings page
     * 
     * @return View
     */
    public function shopSettings()
    {
        $basic_settings = auth()->user()->shop;
        return view('plugin/multivendor::seller.dashboard.pages.shop_settings.settings', ['basic_settings' => $basic_settings]);
    }

    /**
     * Will update shop settings
     * 
     * @param ShopUpdateRequest $request
     * @return mixed
     */
    public function updateShopSettings(ShopUpdateRequest $request)
    {
        $res = $this->sellerRepository->updateSellerShop($request);
        if ($res) {
            toastNotification('success', 'Shop updated successfully');
            return to_route('plugin.multivendor.seller.dashboard.shop.settings');
        } else {
            toastNotification('error', 'Shop update failed');
            return redirect()->back();
        }
    }

    /**
     * Will update shop seo settings
     * 
     * @param ShopSeoUpdateRequest $request
     * @return mixed
     */
    public function updateShopSeoSettings(ShopSeoUpdateRequest $request)
    {
        $res = $this->sellerRepository->updateSellerShopSeoInfo($request);
        if ($res) {
            toastNotification('success', 'Shop seo info updated successfully');
            return to_route('plugin.multivendor.seller.dashboard.shop.settings');
        } else {
            toastNotification('error', 'Shop seo info update failed');
            return redirect()->back();
        }
    }
}
