<?php

namespace Plugin\Coupon\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Plugin\Coupon\Http\Requests\CouponRequest;
use Plugin\Coupon\Repositories\CouponRepository;
use Plugin\TlcommerceCore\Repositories\BrandRepository;
use Plugin\TlcommerceCore\Repositories\CategoryRepository;
use Plugin\TlcommerceCore\Repositories\ProductRepository;

class CouponController extends Controller
{
    protected $coupon_repository;
    protected $product_repository;
    protected $category_repository;
    protected $brand_repository;

    public function __construct(CouponRepository $coupon_repository, ProductRepository $product_repository, CategoryRepository $category_repository, BrandRepository $brand_repository)
    {
        isActiveParentPlugin('tlecommercecore');

        $this->coupon_repository = $coupon_repository;
        $this->product_repository = $product_repository;
        $this->category_repository = $category_repository;
        $this->brand_repository = $brand_repository;
    }

    /**
     * Will return coupon list
     * 
     * @return mixed
     */
    public function coupons()
    {
        return view('plugin/coupon::marketings.coupons.index')->with(
            [
                'coupons' => $this->coupon_repository->coupons()
            ]
        );
    }
    /**
     * Will return create new coupon page
     * 
     * @return mixed
     */
    public function createNewCoupon()
    {
        return view('plugin/coupon::marketings.coupons.add_new')->with(
            [
                'products' => $this->product_repository->activeProducts(),
                'categories' => $this->category_repository->activeCategory(),
                'brands' => $this->brand_repository->activeBrands()
            ]
        );
    }
    /**
     * Will store new Coupon
     * 
     * @param CouponRequest $request
     * @return mixed
     */
    public function storeNewCoupon(CouponRequest $request)
    {
        $res = $this->coupon_repository->storeCoupon($request);
        if ($res == true) {
            toastNotification('success', translate('Coupon created successfully'), 'Success');
            return redirect()->route('plugin.tlcommercecore.marketing.coupon.list');
        } else {
            toastNotification('error', translate('Coupon create failed'), 'Failed');
            return redirect()->back();
        }
    }

    /**
     * Will return coupon details
     * 
     * @param Int $id
     * @return mixed
     */
    public function editCoupon($id)
    {
        return view('plugin/coupon::marketings.coupons.edit')->with(
            [
                'products' => $this->product_repository->activeProducts(),
                'categories' => $this->category_repository->activeCategory(),
                'brands' => $this->brand_repository->activeBrands(),
                'coupon_details' => $this->coupon_repository->couponDetails($id)
            ]
        );
    }
    /**
     * Will update Coupon
     * 
     * @param CouponRequest $request
     * @return mixed
     */
    public function updateCoupon(CouponRequest $request)
    {
        $res = $this->coupon_repository->updateCoupon($request);
        if ($res == true) {
            toastNotification('success', translate('Coupon updated successfully'), 'Success');
            return redirect()->route('plugin.tlcommercecore.marketing.coupon.edit', $request['id']);
        } else {
            toastNotification('error', translate('Coupon update failed'), 'Failed');
            return redirect()->back();
        }
    }
    /**
     * Will update coupon status
     * 
     * @param \Illuminate\Http\Request $request
     * @return void
     */
    public function updateCouponStatus(Request $request)
    {
        $res = $this->coupon_repository->updateCouponStatus($request->id);
        if ($res == true) {
            toastNotification('success', translate('Status successfully'), 'Success');
        } else {
            toastNotification('error', translate('Action failed'), 'Failed');
        }
    }
    /**
     * Will delete a single coupon
     * 
     * @param \Illuminate\Http\Request $request
     * @return mixed
     */
    public function deleteCoupon(Request $request)
    {
        $res = $this->coupon_repository->deleteCoupon($request['id']);
        if ($res == true) {
            toastNotification('success', translate('Coupon delete successfully'), 'Success');
            return redirect()->route('plugin.tlcommercecore.marketing.coupon.list');
        } else {
            toastNotification('error', translate('Coupon delete failed'), 'Failed');
            return redirect()->back();
        }
    }
    /**
     * Will delete bulk  coupon
     * 
     * @param \Illuminate\Http\Request $request
     * @return void
     */
    public function deleteBulkCoupon(Request $request)
    {
        $res = $this->coupon_repository->deleteBulkCoupons($request);
        if ($res == true) {
            toastNotification('success', translate('Coupons deleted successfully'), 'Success');
        } else {
            toastNotification('error', translate('Action failed'), 'Failed');
        }
    }
}
