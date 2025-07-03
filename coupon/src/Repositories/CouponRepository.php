<?php

namespace Plugin\Coupon\Repositories;

use Illuminate\Support\Facades\DB;
use Plugin\Coupon\Models\CouponBrands;
use Plugin\Coupon\Models\CouponCategory;
use Plugin\Coupon\Models\CouponExcludeBrand;
use Plugin\Coupon\Models\CouponExcludeCategory;
use Plugin\Coupon\Models\CouponExcludeProducts;
use Plugin\Coupon\Models\CouponProducts;
use Plugin\Coupon\Models\Coupons;

class CouponRepository
{
    /**
     * Will return coupons 
     * 
     * @return Collections
     */
    public function coupons()
    {
        return Coupons::orderBy('id', 'DESC')->get();
    }
    /**
     * Will store coupon data
     * 
     * @param Object $request
     * @return bool
     */
    public function storeCoupon($request)
    {
        try {
            DB::beginTransaction();
            $exclude_sale_items = $request->has('exclude_sale_items') ? config('settings.general_status.active') : config('settings.general_status.in_active');
            $individual_use = $request->has('individual_use') ? config('settings.general_status.active') : config('settings.general_status.in_active');
            $allow_free_shipping = $request->has('allow_free_shipping') ? config('settings.general_status.active') : config('settings.general_status.in_active');

            $coupon = Coupons::firstOrCreate(['code' => $request['code']]);

            $coupon->code = $request['coupon_code'];
            $coupon->description = $request['description'];
            $coupon->alowed_email = $request['alowed_email'];
            $coupon->discount_type = $request['discount_amount_type'];
            $coupon->discount_amount = $request['discount_amount'];
            $coupon->expire_date = $request['coupon_expire_date'];
            $coupon->free_shipping = $allow_free_shipping;
            $coupon->minimum_spend_amount = $request['minimum_spend'] != null ? $request['minimum_spend'] : 0;
            $coupon->maximum_spend_mount = $request['maximum_spend'] != null ? $request['maximum_spend'] : 0;
            $coupon->individual_use_only = $individual_use;
            $coupon->exclude_sale_items = $exclude_sale_items;
            $coupon->usage_limit_per_coupon = $request['use_limit_per_coupon'] != null ? $request['use_limit_per_coupon'] : 0;
            $coupon->usage_limit_per_user = $request['use_limit_per_user'] != null ? $request['use_limit_per_user'] : 0;
            $coupon->status = config('settings.general_status.active');
            $coupon->save();

            //Store coupon products
            if ($request['products'] != null) {
                foreach ($request['products']  as $product) {

                    $coupon_product = CouponProducts::firstOrCreate(['product_id' => $product, 'coupon_id' => $coupon->id]);
                    $coupon_product->save();
                }
            }
            //Store coupon exclude products
            if ($request['exclude_products'] != null) {
                foreach ($request['exclude_products']  as $product) {

                    $coupon_exclude_product = CouponExcludeProducts::firstOrCreate(['product_id' => $product, 'coupon_id' => $coupon->id]);
                    $coupon_exclude_product->save();
                }
            }

            //Store coupon brands
            if ($request['brands'] != null) {
                foreach ($request['brands']  as $brand) {

                    $coupon_brand = CouponBrands::firstOrCreate(['brand_id' => $brand, 'coupon_id' => $coupon->id]);
                    $coupon_brand->save();
                }
            }
            //Store coupon exclude brands
            if ($request['exclude_brands'] != null) {
                foreach ($request['exclude_brands']  as $brand) {

                    $coupon_exclde_brand = CouponExcludeBrand::firstOrCreate(['brand_id' => $brand, 'coupon_id' => $coupon->id]);
                    $coupon_exclde_brand->save();
                }
            }
            //Store coupon categories
            if ($request['categories'] != null) {
                foreach ($request['categories']  as $category) {

                    $coupon_category = CouponCategory::firstOrCreate(['category_id' => $category, 'coupon_id' => $coupon->id]);
                    $coupon_category->save();
                }
            }
            //Store coupon exclude categories
            if ($request['exclude_categories'] != null) {
                foreach ($request['exclude_categories']  as $category) {

                    $coupon_exclude_category = CouponExcludeCategory::firstOrCreate(['category_id' => $category, 'coupon_id' => $coupon->id]);
                    $coupon_exclude_category->save();
                }
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
     * Will return coupon details
     * 
     * @param Int $id
     * @return Collection
     */
    public function couponDetails($id)
    {
        return Coupons::findOrFail($id);
    }
    /**
     * Will update coupon data
     * 
     * @param Object $request
     * @return bool
     */
    public function updateCoupon($request)
    {

        try {
            DB::beginTransaction();
            $exclude_sale_items = $request->has('exclude_sale_items') ? config('settings.general_status.active') : config('settings.general_status.in_active');
            $individual_use = $request->has('individual_use') ? config('settings.general_status.active') : config('settings.general_status.in_active');
            $allow_free_shipping = $request->has('allow_free_shipping') ? config('settings.general_status.active') : config('settings.general_status.in_active');

            $coupon = Coupons::findOrFail($request['id']);

            $coupon->code = $request['coupon_code'];
            $coupon->description = $request['description'];
            $coupon->alowed_email = $request['alowed_email'];
            $coupon->discount_type = $request['discount_amount_type'];
            $coupon->discount_amount = $request['discount_amount'];
            $coupon->expire_date = $request['coupon_expire_date'];
            $coupon->free_shipping = $allow_free_shipping;
            $coupon->minimum_spend_amount = $request['minimum_spend'];
            $coupon->maximum_spend_mount = $request['maximum_spend'];
            $coupon->individual_use_only = $individual_use;
            $coupon->exclude_sale_items = $exclude_sale_items;
            $coupon->usage_limit_per_coupon = $request['use_limit_per_coupon'];
            $coupon->usage_limit_per_user = $request['use_limit_per_user'];
            $coupon->status = config('settings.general_status.active');
            $coupon->save();

            //Store coupon products
            $coupon->products()->delete();
            if ($request['products'] != null) {
                foreach ($request['products']  as $product) {

                    $coupon_product = CouponProducts::firstOrCreate(['product_id' => $product, 'coupon_id' => $coupon->id]);
                    $coupon_product->save();
                }
            }
            //Store coupon exclude products
            $coupon->exclude_products()->delete();
            if ($request['exclude_products'] != null) {
                foreach ($request['exclude_products']  as $product) {

                    $coupon_exclude_product = CouponExcludeProducts::firstOrCreate(['product_id' => $product, 'coupon_id' => $coupon->id]);
                    $coupon_exclude_product->save();
                }
            }

            //Store coupon brands
            $coupon->brands()->delete();
            if ($request['brands'] != null) {
                foreach ($request['brands']  as $brand) {

                    $coupon_brand = CouponBrands::firstOrCreate(['brand_id' => $brand, 'coupon_id' => $coupon->id]);
                    $coupon_brand->save();
                }
            }
            //Store coupon exclude brands
            $coupon->exclude_brands()->delete();
            if ($request['exclude_brands'] != null) {
                foreach ($request['exclude_brands']  as $brand) {

                    $coupon_exclde_brand = CouponExcludeBrand::firstOrCreate(['brand_id' => $brand, 'coupon_id' => $coupon->id]);
                    $coupon_exclde_brand->save();
                }
            }
            //Store coupon categories
            $coupon->categories()->delete();
            if ($request['categories'] != null) {
                foreach ($request['categories']  as $category) {

                    $coupon_category = CouponCategory::firstOrCreate(['category_id' => $category, 'coupon_id' => $coupon->id]);
                    $coupon_category->save();
                }
            }
            //Store coupon exclude categories
            $coupon->exclude_categories()->delete();
            if ($request['exclude_categories'] != null) {
                foreach ($request['exclude_categories']  as $category) {

                    $coupon_exclude_category = CouponExcludeCategory::firstOrCreate(['category_id' => $category, 'coupon_id' => $coupon->id]);
                    $coupon_exclude_category->save();
                }
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
     * Will updated coupon status
     * 
     * @param Int $id
     * @return bool
     */
    public function updateCouponStatus($id)
    {
        try {
            DB::beginTransaction();
            $coupon = Coupons::findOrFail($id);
            $status = config('settings.general_status.active');

            if ($coupon->status == config('settings.general_status.active')) {
                $status = config('settings.general_status.in_active');
            }
            $coupon->status = $status;
            $coupon->save();
            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            return false;
        } catch (\Error) {
            DB::rollBack();
            return false;
        }
    }
    /**
     * Will delete coupon
     * 
     * @param Int $id
     * @return bool
     */
    public function deleteCoupon($id)
    {
        try {
            DB::beginTransaction();
            $coupon = Coupons::findOrFail($id);
            $coupon->delete();
            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            return false;
        } catch (\Error) {
            DB::rollBack();
            return false;
        }
    }
    /**
     * Will delete bulk coupon
     * 
     * @param Object $request
     * @return bool
     */
    public function deleteBulkCoupons($request)
    {
        try {
            DB::beginTransaction();
            foreach ($request->data as $item) {
                $coupon = Coupons::find($item);
                if ($coupon != null) {
                    $coupon->delete();
                }
            }
            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            return false;
        } catch (\Error) {
            DB::rollBack();
            return false;
        }
    }
}
