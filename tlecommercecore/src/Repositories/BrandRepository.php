<?php

namespace Plugin\TlcommerceCore\Repositories;

use Illuminate\Support\Facades\DB;
use Plugin\TlcommerceCore\Models\ProductBrand;
use Plugin\TlcommerceCore\Models\ProductBrandTranslations;

class BrandRepository
{
    /**
     * Return brands list
     * 
     * @return mixed
     */
    public function brandList()
    {
        return ProductBrand::orderBy('id', 'DESC')->get();
    }
    /**
     * Return  active brands list
     * 
     * @return mixed
     */
    public function activeBrands()
    {
        return ProductBrand::where('status', config('settings.general_status.active'))->orderBy('id', 'DESC')->get();
    }
    /**
     * Store new brand
     * 
     * @param Array
     * @return boolean
     */
    public function storeNewBrand($request)
    {
        try {
            DB::beginTransaction();
            $brand = new ProductBrand;
            $brand->name = $request['name'];
            $brand->permalink = $request['permalink'];
            $brand->meta_title = $request['meta_title'];
            $brand->meta_description = $request['meta_description'];
            $brand->logo = $request['logo'];
            $brand->meta_image = $request['meta_image'];
            $brand->status = 1;
            $brand->save();
            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::commit();
            return false;
        } catch (\Error $e) {
            DB::commit();
            return false;
        }
    }
    /**
     * Will return brand details
     * 
     * @param Int $id
     * @return Collection
     */
    public function brandDetails($id)
    {
        return ProductBrand::findOrFail($id);
    }
    /**
     * Update brand
     * 
     * @param Array $reqest
     * @return bool
     */
    public function updateBrand($request)
    {
        try {
            DB::beginTransaction();
            if ($request['lang'] != null && $request['lang'] != getDefaultLang()) {
                $brand_translation = ProductBrandTranslations::firstOrNew(['brand_id' => $request['id'], 'lang' => $request['lang']]);
                $brand_translation->name = $request['name'];
                $brand_translation->save();
            } else {
                $brand = ProductBrand::findOrFail($request['id']);
                $brand->name = $request['name'];
                $brand->meta_title = $request['meta_title'];
                $brand->permalink = $request['permalink'];
                $brand->meta_description = $request['meta_description'];
                $brand->logo = $request['logo'];
                $brand->meta_image = $request['meta_image'];
                $brand->save();
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
    /**
     * Delete brand
     * 
     * @param Int $id
     * @return boolean
     */
    public function deleteBrand($id)
    {
        try {
            DB::beginTransaction();
            $brand = ProductBrand::findOrFail($id);
            $brand->delete();
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
     * Will delete bulk brand
     * 
     * @param Object $request
     * @return bool
     */
    public function deleteBulkBrand($reqest)
    {
        try {
            DB::beginTransaction();
            foreach ($reqest['data'] as $brand_id) {
                $brand = ProductBrand::findOrFail($brand_id);
                if ($brand != null) {
                    $brand->delete();
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
     * Change Status
     * 
     * @param Int $id
     * @return bool
     */
    public function changeStatus($id)
    {
        try {
            DB::beginTransaction();
            $brand = ProductBrand::findOrFail($id);
            $status = 1;
            if ($brand->status == 1) {
                $status = 2;
            }
            $brand->status = $status;
            $brand->save();
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
     * Change brand featured status
     * 
     * @param Int $id
     * @return boolean
     */
    public function changeFeaturedStatus($id)
    {
        try {
            DB::beginTransaction();
            $brand = ProductBrand::findOrFail($id);
            $status = 1;
            if ($brand->is_featured == 1) {
                $status = 2;
            }
            $brand->is_featured = $status;
            $brand->save();
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
