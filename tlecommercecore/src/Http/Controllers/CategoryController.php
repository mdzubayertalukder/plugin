<?php

namespace Plugin\TlcommerceCore\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;
use Plugin\TlcommerceCore\Models\ProductCategory;
use Plugin\TlcommerceCore\Repositories\CategoryRepository;
use Plugin\TlcommerceCore\Http\Requests\ProductCategoryRequest;

class CategoryController extends Controller
{
    protected $category_repository;

    public function __construct(CategoryRepository $category_repository)
    {
        $this->category_repository = $category_repository;
    }

    /**
     *Get list of product categories
     *
     *@return mixed
     */
    public function categories()
    {
        $categories = $this->category_repository->categoryList();
        return view('plugin/tlecommercecore::products.categories.index')->with(
            [
                'categories' => $categories
            ]
        );
    }
    /**
     * Redirect to new category page
     * 
     * @return mixed
     */
    public function newCategory()
    {
        $categories = $this->category_repository->categoryItems();
        return view('plugin/tlecommercecore::products.categories.new_category')->with(
            [
                'categories' => $categories
            ]
        );
    }
    /**
     * Store new category
     * 
     * @param ProductCategoryRequest $request
     * @return mixed
     */
    public function newCategoryStore(ProductCategoryRequest $request)
    {
        $res = $this->category_repository->storeNewCategory($request);
        if ($res == true) {
            $this->resetMegaCategoriesCache();
            toastNotification('success', translate('New category added successfully'), 'Success');
            return redirect()->route('plugin.tlcommercecore.product.category.list');
        } else {
            toastNotification('error', translate('Category store failed'), 'Failed');
            return redirect()->back();
        }
    }
    /**
     * Redirect to edit category page
     * 
     * @param Int $id
     * @return mixed
     */
    public function editCategory(Request $request, $id)
    {
        return view('plugin/tlecommercecore::products.categories.edit_category')->with(
            [
                'category_details' => $this->category_repository->categoryDetails($id),
                'lang' => $request->lang,
                'categories' => $this->category_repository->categoryItems(),
                'languages' => getAllLanguages()
            ]
        );
    }
    /**
     * Update category
     * 
     * @param ProductCategoryRequest $request
     * @return mixed
     */
    public function updateCategory(ProductCategoryRequest $request)
    {
        $res = $this->category_repository->updateCategory($request);
        if ($res == true) {
            $this->resetMegaCategoriesCache();
            toastNotification('success', translate('Category updated successfully'), 'Success');
            return redirect()->route('plugin.tlcommercecore.product.category.list');
        } else {
            toastNotification('error', translate('Update failed'), 'Failed');
            return redirect()->back();
        }
    }
    /**
     * Delete category
     * 
     * @param \Illuminate\Http\Request $request
     * @return mixed
     */
    public function deleteCategory(Request $request)
    {
        $res = $this->category_repository->deleteCategory($request->id);
        if ($res == true) {
            $this->resetMegaCategoriesCache();
            toastNotification('success', translate('Category deleted successfully'), 'Success');
            return redirect()->route('plugin.tlcommercecore.product.category.list');
        } else {
            toastNotification('error', translate('Unable to delete this category'), 'Warning');
            return redirect()->route('plugin.tlcommercecore.product.category.list');
        }
    }
    /**
     * Will delete bulk category
     * 
     * @param \Illuminate\Http\Request $request
     * @return void
     */
    public function deleteBulkCategory(Request $request)
    {
        $res = $this->category_repository->deleteBulkCategory($request);
        if ($res == true) {
            $this->resetMegaCategoriesCache();
            toastNotification('success', translate('Selected items deleted successfully'), 'Success');
        } else {
            toastNotification('error', translate('Action Failed'), 'Failed');
        }
    }
    /**
     * Change category status
     * 
     * @param \Illuminate\Http\Request $request
     * @return mixed
     */
    public function categoryChangeStatus(Request $request)
    {
        $res = $this->category_repository->changeStatus($request->id);
        if ($res) {
            $this->resetMegaCategoriesCache();
            return response()->json([
                'success' => true
            ]);
        } else {
            return response()->json([
                'success' => false
            ]);
        }
    }
    /**
     * Change category featured status
     * 
     * @param \Illuminate\Http\Request $request
     * @return mixed
     */
    public function changeCategoryFeaturedStatus(Request $request)
    {
        $res = $this->category_repository->changeFeaturedStatus($request->id);
        if ($res) {
            cache_clear();
            return response()->json([
                'success' => true
            ]);
        } else {
            return response()->json([
                'success' => false
            ]);
        }
    }

    /**
     * Will reset categories cache
     */
    public function resetMegaCategoriesCache()
    {
        cache()->forget('mega-categories');
        Cache::remember('mega-categories', 60 * 60 * 24, function () {
            return  ProductCategory::with(['category_translations', 'childs' => function ($q) {
                $q->with(['category_translations', 'childs' => function ($dq) {
                    $dq->with(['category_translations'])
                        ->select('id', 'name', 'permalink', 'icon', 'parent', 'status')
                        ->where('status', config('settings.general_status.active'));
                }])
                    ->select('id', 'name', 'permalink', 'icon', 'parent', 'status')
                    ->where('status', config('settings.general_status.active'));
            }])
                ->whereNull('parent')
                ->where('status', config('settings.general_status.active'))
                ->orderBy('id', 'ASC')
                ->get();
        });
    }
}
