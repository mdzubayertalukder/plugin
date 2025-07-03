<?php

namespace Plugin\Multivendor\Http\Controllers\Seller;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Plugin\TlcommerceCore\Models\ShippingProfile;
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

class ProductController extends Controller
{

    public function __construct(public ProductRepository $product_repository, public UnitRepository $unit_repository, public ProductConditionRepository $product_condition_repository, public ProductTagsRepository $product_tag_repository, public VatTaxRepository $vat_tax_repository, public ColorRepository $color_repository, public ProductAttributeRepository $product_attribute_repository, public LocationRepository $location_repository)
    {
    }
    /**
     * Will return seller product list
     * 
     * @param \Illuminate\Http\Request $request
     * @return mixed
     */
    public function products(Request $request)
    {
        $products = $this->product_repository->productManagement($request, auth()->user()->id);
        return view('plugin/multivendor::seller.dashboard.pages.products.list', ['products' => $products]);
    }
    /**
     * Will load product quick action modal form
     * 
     * @param \Illuminate\Http\Request $request
     * @return mixed
     */
    public function viewProductQuickActionForm(Request $request)
    {
        return view('plugin/multivendor::seller.dashboard.pages.products.product_quick_action_form')->with([
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
            }
        } catch (\Exception $e) {
            toastNotification('error', translate('Action Failed'));
        } catch (\Error $e) {
            toastNotification('error', translate('Action Failed'));
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
            toastNotification('success', translate('Product deleted successfully'), 'Success');
            return redirect()->route('plugin.multivendor.seller.dashboard.products.list');
        } else {
            toastNotification('error', translate('This product can not be deleted'), 'Warning');
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
            toastNotification('success', translate('Product status updated successfully'), 'Success');
        } else {
            toastNotification('error', translate('Unable to change status'), 'Failed');
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
     * Will redirect to new product form page
     * 
     * @return View
     */
    public function addNewProduct()
    {
        return view('plugin/multivendor::seller.dashboard.pages.products.new')->with([
            'units' => $this->unit_repository->unitList(),
            'conditions' => $this->product_condition_repository->conditionList(),
            'shipping_profiles' => ShippingProfile::all(),
            'colors' => $this->color_repository->colorList([config('settings.general_status.active')]),
            'attributes' => $this->product_attribute_repository->attributeList(config('settings.general_status.active')),
        ]);
    }

    /**
     * Will store seller new product
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
            $message = 'New product uploaded successfully';
            if (isActivePluging('multivendor') && getGeneralSetting('product_auto_approve') != config('settings.general_status.active')) {
                $message = "Product uploaded successfully. Please wait for approval";
            }
            toastNotification('success', $message, 'Success');
            return redirect()->route('plugin.multivendor.seller.dashboard.products.list');
        } else {
            toastNotification('error', 'Product create failed', 'Failed');
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
        return view('plugin/multivendor::seller.dashboard.pages.products.edit')->with([
            'product_details' => $this->product_repository->editProduct($id, auth()->user()->id),
            'lang' => $request->lang,
            'shipping_profiles' => ShippingProfile::all(),
        ]);
    }

    /**
     * Will update seller product
     * 
     * @param ProductRequest $request
     */

    public function updateProduct(ProductRequest $request)
    {
        if ($request['product_type'] == config('tlecommercecore.product_variant.variable') && !$request->has('variations')) {
            toastNotification('error', 'Invalid Product Variations');
            return redirect()->back();
        }
        $res = $this->product_repository->updateProduct($request);
        if ($res == true) {
            toastNotification('success', translate('Product update successfully'), 'Success');
            return redirect()->route('plugin.multivendor.seller.dashboard.products.edit', ['id' => $request->id, 'lang' => $request->lang]);
        } else {
            toastNotification('error', translate('Action failed'), 'Failed');
            return redirect()->back();
        }
    }
    /**
     * Will return shipping zone list of shipping profiles
     * 
     * @param \Illuminate\Http\Request $request
     */
    public function shippingZoneLists(Request $request)
    {
        return view('plugin/multivendor::seller.dashboard.pages.products.shipping_zone', ['id' => $request['id']]);
    }
}
