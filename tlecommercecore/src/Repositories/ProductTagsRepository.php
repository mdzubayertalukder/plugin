<?php

namespace Plugin\TlcommerceCore\Repositories;

use Illuminate\Support\Facades\DB;
use Plugin\TlcommerceCore\Models\ProductTags;

class ProductTagsRepository
{
    /**
     * Will return tag list
     * 
     * @return Collections
     */
    public function tagList()
    {
        return ProductTags::orderBy('id', 'DESC')->get();
    }
    /**
     * Store new product tag
     * 
     * @param Array $request
     * @return bool
     */
    public function storeTag($request)
    {
        try {
            DB::beginTransaction();
            $tag = new ProductTags;
            $tag->name = $request['name'];
            $tag->permalink = $request['permalink'];
            $tag->status = 1;
            $tag->save();
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
     * Will delete tag
     * 
     * @param Int $id
     * @return bool
     */
    public function deleteTag($id)
    {
        try {
            DB::beginTransaction();
            $tag = ProductTags::findOrFail($id);
            $tag->delete();
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
     * Will delete bulk product tags
     * 
     * @param Object $request
     * @return bool
     */
    public function deleteBulkTag($request)
    {
        try {
            DB::beginTransaction();
            foreach ($request['data'] as $tag_id) {
                $tag = ProductTags::findOrFail($tag_id);
                if ($tag != null) {
                    $tag->delete();
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
            $brand = ProductTags::findOrFail($id);
            $status = config('settings.general_status.active');
            if ($brand->status == config('settings.general_status.active')) {
                $status = config('settings.general_status.in_active');
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
     * Will return tag details
     * 
     * @param Int $id
     * @return Collection
     */
    public function tagDetails($id)
    {
        return ProductTags::findOrFail($id);
    }
    /**
     * Will update tag
     * 
     * @param Array $request
     * @return mixed
     */
    public function updateTag($request)
    {
        try {
            DB::beginTransaction();
            $tag = ProductTags::findOrFail($request['id']);
            $tag->name = $request['name'];
            $tag->permalink = $request['permalink'];
            $tag->update();
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
