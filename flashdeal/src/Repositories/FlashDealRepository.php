<?php

namespace Plugin\Flashdeal\Repositories;

use Illuminate\Support\Facades\DB;
use Plugin\Flashdeal\Models\DealTranslation;
use Plugin\Flashdeal\Models\Product;
use Plugin\Flashdeal\Models\FlashDeal;
use Plugin\Flashdeal\Models\FlashDealProducts;

class FlashDealRepository
{
    /**
     * Wii return flashdeal
     * 
     * @return Collections
     */
    public function dealsList()
    {
        return FlashDeal::orderBy('id', 'DESC')->get();
    }
    /**
     * Will store flash deal
     * 
     * @param Object $request
     * @return mixed
     */
    public function storeFlashDeal($request)
    {
        try {
            $deal = new FlashDeal;
            $deal->title = $request['title'];
            $deal->background_color = $request['background_color'];
            $deal->text_color = $request['text_color'];
            $deal->background_image = $request['banner'];
            $deal->permalink = $request['permalink'];
            $deal->start_date = $request['start_date'];
            $deal->end_date = $request['expiry_date'];
            $deal->save();
            return $deal->id;
        } catch (\Exception $e) {
            return null;
        } catch (\Error $e) {
            return null;
        }
    }
    /**
     * Will return flash deal details
     * 
     * @param Int $id
     * @return Collection
     */
    public function dealDetails($id)
    {
        return FlashDeal::findOrFail($id);
    }
    /**
     * Will update deal
     * 
     * @param Object $request
     * @return bool
     */
    public function updateDeal($request)
    {
        try {
            DB::beginTransaction();
            if ($request['lang'] != null && $request['lang'] != getDefaultLang()) {
                $deal_translation = DealTranslation::firstOrNew(['deal_id' => $request['id'], 'lang' => $request['lang']]);
                $deal_translation->title = $request['title'];
                $deal_translation->save();
            } else {
                $deal = FlashDeal::findOrFail($request['id']);
                $deal->title = $request['title'];
                $deal->background_color = $request['background_color'];
                $deal->text_color = $request['text_color'];
                $deal->background_image = $request['banner'];
                $deal->permalink = $request['permalink'];
                $deal->start_date = $request['start_date'];
                $deal->end_date = $request['expiry_date'];
                $deal->save();
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
     * Will delete deal
     * 
     * @param Object $request
     * @return bool
     */
    public function deleteDeal($request)
    {
        try {
            DB::beginTransaction();
            $deal = FlashDeal::findOrFail($request['id']);
            $deal->deal_products()->delete();
            $deal->delete();
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
     * Will update deal status
     * 
     * @param Int $id
     * @return bool
     */
    public function updateDealStatus($id)
    {
        try {
            DB::beginTransaction();
            $deal = FlashDeal::findOrFail($id);
            $status = config('settings.general_status.active');

            if ($deal->status == config('settings.general_status.active')) {
                $status = config('settings.general_status.in_active');
            }
            $deal->status = $status;
            $deal->save();
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
     * Will delete bulk deal
     * 
     * @param Object $request
     * @return bool
     */
    public function deleteDealBulk($request)
    {
        try {
            DB::beginTransaction();
            foreach ($request['data'] as $item) {
                $deal = FlashDeal::find($item);
                if ($deal != null) {
                    $deal->deal_products()->delete();
                    $deal->delete();
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
     * Will return active product list
     * 
     * @return Collections
     */
    public function getActiveProducts()
    {
        return Product::whereNotIn('id', FlashDealProducts::pluck('product_id'))->where('status', config('settings.general_status.active'))->get();
    }
    /**
     * Will store flash deal products
     * 
     * @param Object $request
     * @return bool
     */
    public function storeFlashDealProducts($request)
    {
        try {
            foreach ($request['products'] as $product) {
                $deal_product = FlashDealProducts::firstOrCreate(['product_id' => $product, 'deal_id' => $request['deal_id']]);
                $deal_product->discount_type = $request['discount_type'];
                $deal_product->discount = $request['discount'] != null ?  $request['discount'] : 0;
                $deal_product->save();
            }
            return true;
        } catch (\Exception $e) {
            return false;
        } catch (\Error $e) {
            return false;
        }
    }
    /**
     * Will remove deal product
     * 
     * @param Object $request
     * @return bool
     */
    public function removeDealProduct($request)
    {
        try {
            DB::beginTransaction();
            $deal_product = FlashDealProducts::findOrFail($request['deal_product_id']);
            $deal_product->delete();
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
     * Will remove bulk deal products
     * 
     * @param Object $request
     * @return bool
     */
    public function removeDealProductBulk($request)
    {
        try {
            DB::beginTransaction();
            foreach ($request->data['selected_items'] as $item) {
                $deal_product = FlashDealProducts::find($item);
                if ($deal_product != null) {
                    $deal_product->delete();
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
     * Will update deal Product
     * 
     * @param Object $request
     * @return bool
     */
    public function updateDealProduct($request)
    {
        try {
            DB::beginTransaction();
            $deal_product = FlashDealProducts::findOrFail($request['deal_product_id']);
            $deal_product->discount = $request['discount'];
            $deal_product->discount_type = $request['discount_type'];
            $deal_product->save();
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
}
