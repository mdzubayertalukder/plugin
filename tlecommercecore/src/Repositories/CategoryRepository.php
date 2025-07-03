<?php

namespace Plugin\TlcommerceCore\Repositories;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\App;
use Plugin\TlcommerceCore\Models\ProductCategory;
use Plugin\TlcommerceCore\Models\ProductCategoryTranslations;

class CategoryRepository
{
    /**
     * Get product category list
     * 
     * @return collection
     */
    public function categoryList()
    {
        return ProductCategory::with('parentCategory')->orderBy('id', 'DESC')->get();
    }
    /**
     * Get product category list
     * 
     * @return collection
     */
    public function activeCategory()
    {
        return ProductCategory::where('status', config('settings.general_status.active'))->orderBy('id', 'DESC')->get();
    }
    /**
     * Get product category list
     * 
     * @return collection
     */
    public function categoryItems()
    {
        return ProductCategory::where('parent', NULL)->where('status', config('settings.general_status.active'))->orderBy('id', 'DESC')->get();
    }
    /**
     * Get product category list
     * 
     * @param Int $id
     * @return collection
     */
    public function categoryDetails($id)
    {
        return ProductCategory::findOrFail($id);
    }

    /**
     * Store New category
     * 
     * @param Array $request
     * @return mixed
     */
    public function storeNewCategory($request)
    {
        try {
            DB::beginTransaction();
            $cat = new ProductCategory;
            $cat->name = $request['name'];
            $cat->parent = $request['parent'];
            $cat->meta_title = $request['meta_title'];
            $cat->permalink = $request['permalink'];
            $cat->meta_description = $request['meta_description'];
            $cat->icon = $request['icon'];
            $cat->meta_image = $request['meta_image'];
            $cat->status = 1;
            $cat->save();
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
     * Delete Category
     * 
     * @param Int $id
     * @return boolean
     */
    public function deleteCategory($id)
    {
        try {
            DB::beginTransaction();
            $category = ProductCategory::findOrFail($id);
            $category->delete();
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
     * Will delete bulk categories
     * 
     * @param Object $request
     * @return bool
     */
    public function deleteBulkCategory($request)
    {
        try {
            DB::beginTransaction();
            foreach ($request['data'] as $category_id) {
                ProductCategory::find($category_id)->delete();
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
     * @return boolean
     */
    public function changeStatus($id)
    {
        try {
            DB::beginTransaction();
            $category = ProductCategory::findOrFail($id);
            $status = config('settings.general_status.active');
            if ($category->status == config('settings.general_status.active')) {
                $status = config('settings.general_status.in_active');
            }
            $category->status = $status;
            $category->save();
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
     * Change category featured status
     * 
     * @param Int $id
     * @return boolean
     */
    public function changeFeaturedStatus($id)
    {
        try {
            DB::beginTransaction();
            $category = ProductCategory::findOrFail($id);
            $status = config('settings.general_status.active');
            if ($category->is_featured == config('settings.general_status.active')) {
                $status = config('settings.general_status.in_active');
            }
            $category->is_featured = $status;
            $category->save();
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
     * Update product category
     * 
     * @param Array $id
     * @return boolean
     */
    public function updateCategory($request)
    {
        try {
            DB::beginTransaction();
            if ($request['lang'] != null && $request['lang'] != getDefaultLang()) {
                $cat_translation = ProductCategoryTranslations::firstOrNew(['category_id' => $request['id'], 'lang' => $request['lang']]);
                $cat_translation->name = $request['name'];
                $cat_translation->save();
            } else {
                $cat = ProductCategory::findOrFail($request['id']);
                $cat->name = $request['name'];
                $cat->parent = $request['parent'];
                $cat->meta_title = $request['meta_title'];
                $cat->permalink = $request['permalink'];
                $cat->meta_description = $request['meta_description'];
                $cat->icon = $request['icon'];
                $cat->meta_image = $request['meta_image'];
                $cat->save();

                $this->updateCategoryInsideMenu($cat->id, $request);
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
     * Update category url inside mega menu
     *
     * @param  int $category_id
     * @param  mixed $request
     * @return void
     */
    public function updateCategoryInsideMenu($category_id, $request)
    {
        DB::table('tl_menus')
            ->where('tl_menus.category_id', '=', (int)$category_id)
            ->update([
                'url' => App::make('url')->to('/') . '/' . $request['permalink']
            ]);
    }
}
