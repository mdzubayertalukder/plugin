<?php

namespace Plugin\TlcommerceCore\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Plugin\TlcommerceCore\Models\Cities;
use Plugin\TlcommerceCore\Models\States;
use Plugin\TlcommerceCore\Models\Country;
use Plugin\TlcommerceCore\Models\Product;
use Illuminate\Database\Eloquent\Collection;
use Plugin\TlcommerceCore\Models\ProductTags;
use Plugin\TlcommerceCore\Models\ProductBrand;
use Plugin\TlcommerceCore\Models\ProductCategory;
use Plugin\TlcommerceCore\Models\ShippingProfile;
use Plugin\TlcommerceCore\Models\ProductAttribute;
use Plugin\TlcommerceCore\Models\ProductShareOption;
use Plugin\TlcommerceCore\Repositories\UnitRepository;
use Plugin\TlcommerceCore\Http\Requests\ProductRequest;
use Plugin\TlcommerceCore\Repositories\BrandRepository;
use Plugin\TlcommerceCore\Repositories\ColorRepository;
use Plugin\TlcommerceCore\Repositories\VatTaxRepository;
use Plugin\TlcommerceCore\Repositories\ProductRepository;
use Plugin\TlcommerceCore\Repositories\CategoryRepository;
use Plugin\TlcommerceCore\Repositories\LocationRepository;
use Plugin\TlcommerceCore\Repositories\ProductTagsRepository;
use Plugin\TlcommerceCore\Repositories\ProductAttributeRepository;
use Plugin\TlcommerceCore\Repositories\ProductConditionRepository;
use Plugin\TlcommerceCore\Repositories\ProductCollectionRepository;

class ProductController extends Controller
{

    protected $product_repository;
    protected $category_repository;
    protected $brand_repository;
    protected $unit_repository;
    protected $product_condition_repository;
    protected $product_tag_repository;
    protected $vat_tax_repository;
    protected $color_repository;
    protected $product_attribute_repository;
    protected $location_repository;
    protected $collection_repository;

    public function __construct(ProductCollectionRepository $collection_repository, ProductRepository $product_repository, CategoryRepository $category_repository, BrandRepository $brand_repository, UnitRepository $unit_repository, ProductConditionRepository $product_condition_repository, ProductTagsRepository $product_tag_repository, VatTaxRepository $vat_tax_repository, ColorRepository $color_repository, ProductAttributeRepository $product_attribute_repository, LocationRepository $location_repository)
    {
        $this->product_repository = $product_repository;
        $this->category_repository = $category_repository;
        $this->brand_repository = $brand_repository;
        $this->unit_repository = $unit_repository;
        $this->product_condition_repository = $product_condition_repository;
        $this->product_tag_repository = $product_tag_repository;
        $this->vat_tax_repository = $vat_tax_repository;
        $this->color_repository = $color_repository;
        $this->product_attribute_repository = $product_attribute_repository;
        $this->location_repository = $location_repository;
        $this->collection_repository = $collection_repository;
    }
    /**
     * Will return product list
     *
     * @return mixed
     */
    public function productList(Request $request)
    {
        return view('plugin/tlecommercecore::products.product.product_list')->with([
            'products' => $this->product_repository->productManagement($request, null, 'inhouse')
        ]);
    }
    /**
     * Will return product dropdown options
     *
     * @param \Illuminate\Http\Request $request
     * @return Response
     */
    public function productDropdownOptions(Request $request)
    {
        $query = Product::query()->select('id', 'name as text');
        if ($request->has('term')) {
            $term = trim($request->term);
            $query = $query->where('name', 'LIKE',  '%' . $term . '%');
        }

        $categories = $query->orderBy('name', 'asc')->paginate(10);
        $morePages = true;

        if (empty($categories->nextPageUrl())) {
            $morePages = false;
        }
        $results = array(
            "results" => $categories->items(),
            "pagination" => array(
                "more" => $morePages
            )
        );

        return response()->json($results);
    }

    /**
     * Will load product quick action modal form
     *
     * @param \Illuminate\Http\Request $request
     * @return mixed
     */
    public function viewProductQuickActionForm(Request $request)
    {
        return view('plugin/tlecommercecore::products.product.product_quick_action_modal')->with([
            'product_details' => $this->product_repository->productDetails($request['id']),
            'action' => $request['action'],
        ]);
    }
    /**
     * Will update product discount
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateProductDiscount(Request $request)
    {
        $res = $this->product_repository->updateProductDiscount($request);
        if ($res) {
            return response()->json(
                [
                    'success' => true,
                ]
            );
        } else {
            return response()->json(
                [
                    'success' => false,
                ]
            );
        }
    }
    /**
     * Will update product price
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateProductPrice(Request $request)
    {
        $res = $this->product_repository->updateProductPrice($request);
        if ($res) {
            return response()->json(
                [
                    'success' => true,
                ]
            );
        } else {
            return response()->json(
                [
                    'success' => false,
                ]
            );
        }
    }

    /**
     * Will update product stock
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateProductStock(Request $request)
    {
        $res = $this->product_repository->updateProductStock($request);
        if ($res) {
            return response()->json(
                [
                    'success' => true,
                ]
            );
        } else {
            return response()->json(
                [
                    'success' => false,
                ]
            );
        }
    }

    /**
     * Will redirect new  product page
     *
     * @return mixed
     */
    public function addNewProduct()
    {
        return view('plugin/tlecommercecore::products.product.add_new_product')->with([
            'units' => $this->unit_repository->unitList(),
            'conditions' => $this->product_condition_repository->conditionList(),
            'shipping_profiles' => ShippingProfile::all(),
            'colors' => $this->color_repository->colorList([config('settings.general_status.active')]),
            'attributes' => $this->product_attribute_repository->attributeList(config('settings.general_status.active')),
            'product_collections' => $this->collection_repository->collections([config('settings.general_status.active')])
        ]);
    }
    /**
     * Will store new product
     *
     * @param ProductRequest $request
     * @return mixed
     */
    public function storeNewProduct(ProductRequest $request)
    {
        if ($request['product_type'] == config('tlecommercecore.product_variant.variable') && !$request->has('variations')) {
            toastNotification('error', 'Invalid Product Variations');
            return redirect()->back();
        }
        $res = $this->product_repository->storeNewProduct($request);
        if ($res == true) {
            toastNotification('success', translate('New product created successfully'), 'Success');
            return redirect()->route('plugin.tlcommercecore.product.list');
        } else {
            toastNotification('error', translate('Action failed'), 'Failed');
            return redirect()->back();
        }
    }
    /**
     * Will redirect product edit page
     *
     * @param Int $id
     * @param \Illuminate\Http\Request $request
     * @return mixed
     */
    public function editProduct($id, Request $request)
    {
        return view('plugin/tlecommercecore::products.product.edit_product')->with([
            'product_details' => $this->product_repository->editProduct($id),
            'lang' => $request->lang,
            'shipping_profiles' => ShippingProfile::all(),
        ]);
    }

    public function updateProduct(ProductRequest $request)
    {
        if ($request['product_type'] == config('tlecommercecore.product_variant.variable') && !$request->has('variations')) {
            toastNotification('error', 'Invalid Product Variations');
            return redirect()->back();
        }
        $res = $this->product_repository->updateProduct($request);
        if ($res == true) {
            toastNotification('success', translate('Product update successfully'), 'Success');
            return redirect()->route('plugin.tlcommercecore.product.edit', ['id' => $request->id, 'lang' => $request->lang]);
        } else {
            toastNotification('error', translate('Action failed'), 'Failed');
            return redirect()->back();
        }
    }
    /**
     * Will update product status
     *
     * @param \Illuminate\Http\Request $request
     * @return void
     */
    public function updateProductStatus(Request $request)
    {
        $res = $this->product_repository->changeStatus($request->id);
        if ($res == true) {
            toastNotification('success', translate('Product status updated successfully'));
        } else {
            toastNotification('error', translate('Unable to change status'));
        }
    }
    /**
     * Will update product status
     *
     * @param \Illuminate\Http\Request $request
     * @return void
     */
    public function updateProductApprovalStatus(Request $request)
    {
        $res = $this->product_repository->changeApprovalStatus($request->id);
        if ($res == config('settings.general_status.active')) {
            toastNotification('success', 'Product approved  successfully');
        }
        if ($res == config('settings.general_status.in_active')) {
            toastNotification('warning', 'Removed approval status successfully');
        }
        if (!$res) {
            toastNotification('error', 'Unable to change status');
        }
    }
    /**
     * Will update product featured status
     *
     * @param \Illuminate\Http\Request $request
     * @return void
     */
    public function updateProductFeaturedStatus(Request $request)
    {
        $res = $this->product_repository->updateFeaturedStatus($request->id);
        if ($res == true) {
            toastNotification('success', 'Product featured status updated successfully', 'Success');
        } else {
            toastNotification('error', 'Unable to change status', 'Failed');
        }
    }
    /**
     * Will delete product
     *
     * @param \Illuminate\Http\Request $request
     * @return mixed
     */
    public function deleteProduct(Request $request)
    {
        $res = $this->product_repository->deleteProduct($request->id);
        if ($res == true) {
            toastNotification('success', 'Product deleted successfully', 'Success');
            return redirect()->route('plugin.tlcommercecore.product.list');
        } else {
            toastNotification('error', 'This product can not be deleted', 'Warning');
            return redirect()->back();
        }
    }
    /**
     * Will applied bulk products
     *
     * @param \Illuminate\Http\Request $request
     * @return void
     */
    public function productBulkAction(Request $request)
    {
        try {
            if ($request->has('items') && $request->has('action')) {
                //Bulk delete
                if ($request['action'] == 'delete_all') {
                    foreach ($request['items'] as $product_id) {
                        $this->product_repository->deleteProduct($product_id);
                    }
                    toastNotification('success', translate('Items Deleted Successfully'));
                }
                //Bulk active
                if ($request['action'] == 'active') {
                    foreach ($request['items'] as $product_id) {
                        $this->product_repository->changeStatus($product_id, config('settings.general_status.active'));
                    }
                    toastNotification('success', translate('Items make active successfully'));
                }
                //Bulk inactive
                if ($request['action'] == 'in_active') {
                    foreach ($request['items'] as $product_id) {
                        $this->product_repository->changeStatus($product_id, config('settings.general_status.in_active'));
                    }
                    toastNotification('success', translate('Items make inactive successfully'));
                }
                //Bulk remove discount
                if ($request['action'] == 'remove_discount') {
                    foreach ($request['items'] as $product_id) {
                        $this->product_repository->updateProductDiscount($request, $product_id, 0);
                    }
                    toastNotification('success', translate('Remove discount from items successfully'));
                }
                //Bulk make feature
                if ($request['action'] == 'feature_active') {
                    foreach ($request['items'] as $product_id) {
                        $this->product_repository->updateFeaturedStatus($product_id, config('settings.general_status.active'));
                    }
                    toastNotification('success', translate('Selected items featured successfully'));
                }
                //Bulk remove from featured list
                if ($request['action'] == 'feature_in_active') {
                    foreach ($request['items'] as $product_id) {
                        $this->product_repository->updateFeaturedStatus($product_id, config('settings.general_status.in_active'));
                    }
                    toastNotification('success', translate('Selected items remove from featured list'));
                }
            }
        } catch (\Exception $e) {
            toastNotification('error', translate('Action Failed'));
        } catch (\Error $e) {
            toastNotification('error', translate('Action Failed'));
        }
    }
    /**
     * Add product choice  option
     *
     * @param \Illuminate\Http\Request $request
     * @return mixed
     */
    public function addProductChoiceOption(Request $request)
    {
        $attributes = ProductAttribute::with('attribute_values')->where('id', $request->attribute_id)->first();
        return view('plugin/tlecommercecore::products.product.choice_option')->with([
            'attribute' => $attributes
        ]);
    }
    /**
     * Generate product variant combination
     *
     * @param \Illuminate\Http\Request $request
     * @return mixed
     */
    public function variantCombination(Request $request)
    {

        $option_choices = array();

        if ($request->has('product_attributes')) {
            $product_options = $request->product_attributes;
            sort($product_options, SORT_NUMERIC);

            foreach ($product_options as $key => $option) {

                $option_name = 'attribute_' . $option . '_selected';
                $choices = array();

                if ($request->has($option_name)) {

                    $product_option_values = $request[$option_name];
                    sort($product_option_values, SORT_NUMERIC);

                    foreach ($product_option_values as $key => $item) {
                        array_push($choices, $item);
                    }
                    $option_choices[$option] =  $choices;
                }
            }
        }
        if ($request->has('selected_colors')) {
            $option_choices['color'] = $request->selected_colors;
        }

        $combinations = array(array());
        foreach ($option_choices as $property => $property_values) {
            $tmp = array();
            foreach ($combinations as $combination_item) {
                foreach ($property_values as $property_value) {
                    $tmp[] = $combination_item + array($property => $property_value);
                }
            }
            $combinations = $tmp;
        }
        return view('plugin/tlecommercecore::products.product.variant_combination')->with(
            [
                'combinations' => $combinations
            ]
        );
    }
    /**
     * Get color variant image upload options
     *
     * @param \Illuminate\Http\Request $request
     * @return mixed
     */
    public function colorVariantImageInput(Request $request)
    {
        $colors = $request->selected_colors;
        $file_user_filter = false;
        if ($request->has('seller_id') && $request['seller_id'] != null) {
            $file_user_filter = true;
        }
        return view('plugin/tlecommercecore::products.product.color_variant_images')->with(
            [
                'colors' => $colors,
                'file_user_filter' => $file_user_filter
            ]
        );
    }

    /**
     * Will return product category options
     *
     * @param \Illuminate\Http\Request $request
     * @return JsonResponse
     */
    public function productCategoryOption(Request $request)
    {
        $query = ProductCategory::with(['childs' => function ($q) {
            $q->where('status', config('settings.general_status.active'))
                ->select('id', 'name', 'parent');
        }, 'category_translations'])
            ->select('id', 'name', 'parent')
            ->where('status', config('settings.general_status.active'));

        if ($request->has('term')) {
            $term = trim($request->term);
            $query = $query->where('name', 'LIKE',  '%' . $term . '%');
        }

        $categories = $query->orderBy('id', 'asc')->paginate(2);

        $output = [];

        foreach ($categories->items() as $category) {
            $item['id'] = $category->id;
            $item['text'] = $category->translation('name', getLocale());
            array_push($output, $item);

            if ($category->childs != null) {
                foreach ($category->childs as $child) {
                    $sub_item['id'] = $child->id;
                    $sub_item['text'] = '-- ' . $child->translation('name', getLocale());
                    array_push($output, $sub_item);

                    if ($child->childs != null) {
                        foreach ($child->childs as $pro_child) {
                            $sub_sub_item['id'] = $pro_child->id;
                            $sub_sub_item['text'] = '--- ' . $pro_child->translation('name', getLocale());
                            array_push($output, $sub_sub_item);
                        }
                    }
                }
            }
        }

        $morePages = true;

        if (empty($categories->nextPageUrl())) {
            $morePages = false;
        }
        $results = array(
            "results" => $output,
            "pagination" => array(
                "more" => $morePages
            )
        );

        return response()->json($results);
    }
    /**
     * Will return product brand options
     *
     * @param \Illuminate\Http\Request $request
     * @return JsonResponse
     */
    public function productBrandsOption(Request $request)
    {
        $query = ProductBrand::with(['brand_translations' => function ($q) {
            $q->select('name', 'brand_id', 'lang');
        }])
            ->select('id', 'name')
            ->where('status', config('settings.general_status.active'));


        if ($request->has('term')) {
            $term = trim($request->term);
            $query = $query->where('name', 'LIKE',  '%' . $term . '%');
        }

        $brands = $query->orderBy('id', 'asc')->paginate(10);

        $morePages = true;

        if (empty($brands->nextPageUrl())) {
            $morePages = false;
        }
        $output = collect($brands->items())->map(function ($item) {
            return [
                'id' => $item->id,
                'text' => $item->translation('name', getLocale())
            ];
        });
        $results = array(
            "results" => $output,
            "pagination" => array(
                "more" => $morePages
            )
        );

        return response()->json($results);
    }
    /**
     * Will Return product tags options
     *
     * @param \Illuminate\Http\Request $request
     * @return JsonResponse
     */
    public function productTagsOption(Request $request)
    {

        $query = ProductTags::select('id', 'name as text')
            ->where('status', config('settings.general_status.active'));


        if ($request->has('term')) {
            $term = trim($request->term);
            $query = $query->where('name', 'LIKE',  '%' . $term . '%');
        }

        $tags = $query->orderBy('id', 'DESC')->paginate(10);

        $morePages = true;

        if (empty($tags->nextPageUrl())) {
            $morePages = false;
        }

        $results = array(
            "results" => $tags->items(),
            "pagination" => array(
                "more" => $morePages
            )
        );

        return response()->json($results);
    }
    /**
     * Will return product cod countries dropdown options
     *
     * @param \Illuminate\Http\Request $request
     * @return JsonResponse
     */
    public function codCountriesDropdownOptions(Request $request)
    {
        $query = Country::with(['country_translations'])->select('id', 'name')
            ->where('status', config('settings.general_status.active'))
            ->orderBy('name', 'ASC');


        if ($request->has('term')) {
            $term = trim($request->term);
            $query = $query->where('name', 'LIKE',  '%' . $term . '%');
        }

        $countries = $query->orderBy('name', 'ASC')->paginate(10);

        $collection = new Collection($countries->items());

        $modifiedCollection = $collection->map(function ($item) {
            $item->id = $item->id;
            $item->text = $item->translation('name');
            return $item;
        });

        $morePages = true;

        if (empty($countries->nextPageUrl())) {
            $morePages = false;
        }

        $results = array(
            "results" => $modifiedCollection,
            "pagination" => array(
                "more" => $morePages
            )
        );

        return response()->json($results);
    }

    /**
     * Will return cod state dropdown options
     *
     * @param \Illuminate\Http\Request $request
     * @return JsonResponse
     */
    public function codStateDropdownOptions(Request $request)
    {
        $query = States::with(['state_translations'])->select('id', 'name')
            ->where('status', config('settings.general_status.active'))
            ->orderBy('name', 'ASC');


        if ($request->has('countries')) {
            $term = trim($request->term);
            $query = $query->whereIn('country_id', $request->countries);
        }

        if ($request->has('country')) {
            $term = trim($request->term);
            $query = $query->where('country_id', $request['country']);
        }

        if ($request->has('term')) {
            $term = trim($request->term);
            $query = $query->where('name', 'LIKE',  '%' . $term . '%');
        }

        $states = $query->orderBy('name', 'ASC')->paginate(10);

        $collection = new Collection($states->items());

        $modifiedCollection = $collection->map(function ($item) {
            $item->id = $item->id;
            $item->text = $item->translation('name');
            return $item;
        });

        $morePages = true;

        if (empty($states->nextPageUrl())) {
            $morePages = false;
        }

        $results = array(
            "results" => $modifiedCollection,
            "pagination" => array(
                "more" => $morePages
            )
        );

        return response()->json($results);
    }
    /**
     * Will return cod cities dropdown options
     *
     * @param \Illuminate\Http\Request $request
     * @return JsonResponse
     */
    public function codCityDropdownOptions(Request $request)
    {
        $query = Cities::with(['city_translations'])->select('id', 'name')
            ->where('status', config('settings.general_status.active'))
            ->orderBy('name', 'ASC');

        if ($request->has('states')) {
            $term = trim($request->term);
            $query = $query->whereIn('state_id', $request->states);
        }

        if ($request->has('state')) {
            $term = trim($request->term);
            $query = $query->where('state_id', $request['state']);
        }

        if ($request->has('term')) {
            $term = trim($request->term);
            $query = $query->where('name', 'LIKE',  '%' . $term . '%');
        }

        $cities = $query->orderBy('name', 'ASC')->paginate(10);

        $collection = new Collection($cities->items());

        $modifiedCollection = $collection->map(function ($item) {
            $item->id = $item->id;
            $item->text = $item->translation('name');
            return $item;
        });


        $morePages = true;

        if (empty($cities->nextPageUrl())) {
            $morePages = false;
        }

        $results = array(
            "results" => $modifiedCollection,
            "pagination" => array(
                "more" => $morePages
            )
        );

        return response()->json($results);
    }

    /**
     * Will return product share options
     *
     * @return mixed
     */
    public function shareOptions()
    {
        $share_options = ProductShareOption::all();
        return view('plugin/tlecommercecore::products.product.share_options')->with(
            [
                'share_options' => $share_options,
            ]
        );
    }
    /**
     * Update status of product share option
     *
     * @param \Illuminate\Http\Request $request
     * @return void
     */
    public function shareOptionUpdateStatus(Request $request)
    {
        try {
            DB::beginTransaction();
            $option = ProductShareOption::find($request['id']);
            $option->status = $option->status == config('settings.general_status.active') ? config('settings.general_status.in_active') : config('settings.general_status.active');
            $option->save();
            DB::commit();
            toastNotification('success', translate('Status updated successfully'));
        } catch (\Exception $e) {
            DB::rollBack();
            toastNotification('error', translate('Status update failed'));
        }
    }

    /**
     * Will return product reviews list
     *
     * @param \Illuminate\Http\Request $request
     * @return mixed
     */
    public function productReviewsList(Request $request)
    {
        $reviews = $this->product_repository->reviewList($request);

        return view('plugin/tlecommercecore::products.product.reviews')->with(
            [
                'reviews' => $reviews,
            ]
        );
    }
    /**
     * will update  product review status
     *
     * @param \Illuminate\Http\Request $request
     * @return void
     */
    public function updateProductReviewStatus(Request $request)
    {
        $res = $this->product_repository->updateReviewStatus($request['id']);

        if ($res) {
            toastNotification('success', translate('Review status updated successfully'));
        } else {
            toastNotification('error', translate('Status update failed'));
        }
    }
    /**
     * will return product review details
     *
     * @param \Illuminate\Http\Request $request
     * @return mixed
     *
     */
    public function productReviewDetails(Request $request)
    {
        $details = $this->product_repository->productReviewDetails($request['id']);

        return view('plugin/tlecommercecore::products.product.review_details')->with(
            [
                'details' => $details,
            ]
        );
    }
    /**
     * Will delete  product review review
     *
     * @param \Illuminate\Http\Request $request
     * @return mixed
     */
    public function productReviewdelete(Request $request)
    {
        $res = $this->product_repository->productReviewDelete($request['id']);

        if ($res) {
            toastNotification('success', translate('Review deleted successfully'));
            return to_route('plugin.tlcommercecore.product.reviews.list');
        } else {
            toastNotification('error', translate('Review delete failed'));
            return redirect()->back();
        }
    }
}
