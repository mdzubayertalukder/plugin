<?php

namespace Plugin\TlcommerceCore\Repositories;

use Illuminate\Support\Facades\DB;
use Plugin\TlcommerceCore\Models\ProductCollection;
use Plugin\TlcommerceCore\Models\CollectionHasProducts;
use Plugin\TlcommerceCore\Models\CollectionTranslation;

class ProductCollectionRepository
{

    /**
     * Will return collections
     * 
     * @return Collections
     */
    public function collections($status = [1, 2])
    {
        return ProductCollection::whereIn('status', $status)->orderBy('id', 'DESC')->get();
    }
    /**
     * Will store new product collection
     * 
     * @param Object $request
     * @return Mixed
     */
    public function storeNewCollection($request)
    {
        try {
            $collection = new ProductCollection;
            $collection->name = $request['name'];
            $collection->image = $request['image'];
            $collection->permalink = $request['permalink'];
            $collection->status = config('settings.general_status.active');
            $collection->save();
            return $collection->id;
        } catch (\Exception $e) {
            return null;
        } catch (\Error $e) {
            return null;
        }
    }
    /**
     * Will update product collections
     * 
     * @param Object $request
     * @return bool
     */
    public function updateCollection($request)
    {
        try {
            DB::beginTransaction();
            if ($request['lang'] != null && $request['lang'] != getDefaultLang()) {
                $collection_translation = CollectionTranslation::firstOrNew(['collection_id' => $request['id'], 'lang' => $request['lang']]);
                $collection_translation->name = $request['name'];
                $collection_translation->save();
            } else {
                $collection = ProductCollection::findOrFail($request['id']);
                $collection->name = $request['name'];
                $collection->permalink = $request['permalink'];
                $collection->image = $request['image'];
                $collection->save();
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
     * Will delete product collection
     * 
     * @param Int $id
     * @return bool
     */
    public function deleteProductCollection($id)
    {
        try {
            DB::beginTransaction();
            $collection = ProductCollection::findOrFail($id);
            $collection->products()->delete();
            $collection->delete();
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
     * Will delete bulk product collection
     * 
     * @param Object $request
     * @return bool
     */
    public function deleteBulkProductCollection($request)
    {
        try {
            DB::beginTransaction();
            foreach ($request['data'] as $id) {
                $collection = ProductCollection::find($id);
                if ($collection != null) {
                    $collection->products()->delete();
                    $collection->delete();
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
     * Will delete collection status
     * 
     * @param Int $id
     * @return bool
     */
    public function updateCollectionStatus($id)
    {
        try {
            DB::beginTransaction();
            $collection = ProductCollection::findOrFail($id);
            $status = config('settings.general_status.active');

            if ($collection->status == config('settings.general_status.active')) {
                $status = config('settings.general_status.in_active');
            }
            $collection->status = $status;
            $collection->save();
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
     * Will Store collection products
     * 
     * @param Object $request
     * @return bool
     */
    public function storeCollectionProducts($request)
    {
        try {
            DB::beginTransaction();
            foreach ($request['products'] as $product) {
                $collection_product = CollectionHasProducts::firstOrCreate(['collection_id' => $request['collection_id'], 'product_id' => $product]);
                $collection_product->save();
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
     * Will remove collection product
     * 
     *@param Object $request
     *@return  bool
     */
    public function removeCollectionProduct($request)
    {
        try {
            DB::beginTransaction();
            $collection_product = CollectionHasProducts::findOrFail($request['id']);
            $collection_product->delete();
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
     * Will remove collection product
     * 
     *@param Object $request
     *@return  bool
     */
    public function removeBulkCollectionProduct($request)
    {
        try {
            DB::beginTransaction();
            foreach ($request->data['selected_items'] as $item) {
                $collection_product = CollectionHasProducts::find($item);
                if ($collection_product != null) {
                    $collection_product->delete();
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
    /**
     * Will return collection details
     * 
     * @param Int $id
     * @return Collection
     */
    public function collectionDetails($id)
    {
        return ProductCollection::findOrFail($id);
    }
}
