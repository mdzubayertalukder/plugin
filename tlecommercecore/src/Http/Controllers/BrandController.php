<?php

namespace Plugin\TlcommerceCore\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Plugin\TlcommerceCore\Http\Requests\BrandRequest;
use Plugin\TlcommerceCore\Repositories\BrandRepository;

class BrandController extends Controller
{

    protected $brand_repository;

    public function __construct(BrandRepository $brand_repository)
    {
        $this->brand_repository = $brand_repository;
    }
    /**
     * Return product brand list
     * 
     * @return mixed
     */
    public function productBrands()
    {
        $brands = $this->brand_repository->brandList();
        return view('plugin/tlecommercecore::products.brands.index')->with(
            [
                'brands' => $brands
            ]
        );
    }
    /**
     * Store new product brand
     * 
     * @param BrandRequest $request
     * @return mixed
     */
    public function storeNewProductBrand(BrandRequest $request)
    {
        $res = $this->brand_repository->storeNewBrand($request);
        if ($res == true) {
            toastNotification('success', translate('New Brand added successfully') , 'Success');
            return redirect()->route('plugin.tlcommercecore.product.brand.list');
        } else {
            toastNotification('error', translate('Brand store failed'), 'Failed');
            return redirect()->back();
        }
    }
    /**
     * Edit Brand
     * 
     * @param \Illuminate\Http\Request $request
     * @param Int $id
     * @return mixed
     */
    public function editBrand(Request $request, $id)
    {
        return view('plugin/tlecommercecore::products.brands.edit_brand')->with(
            [
                'brand_details' => $this->brand_repository->brandDetails($request->id),
                'lang' => $request->lang,
                'languages' => getAllLanguages()
            ]
        );
    }
    /**
     * Update product brand
     * 
     * @param BrandRequest $request
     * @return mixed
     */
    public function updateProductBrand(BrandRequest $request)
    {
        $res = $this->brand_repository->updateBrand($request);
        if ($res == true) {
            toastNotification('success', translate('Brand updated successfully'), 'Success');
            return redirect()->route('plugin.tlcommercecore.product.brand.edit', ['id' => $request['id'], 'lang' => $request['lang']]);
        } else {
            toastNotification('error', translate('Update failed') , 'Failed');
            return redirect()->back();
        }
    }
    /**
     * Delete product brand
     * 
     * @param \Illuminate\Http\Request $request
     * @return mixed
     */
    public function deleteProductBrand(Request $request)
    {
        $res = $this->brand_repository->deleteBrand($request->id);
        if ($res == true) {
            toastNotification('success', translate('Brand deleted successfully'), 'Success');
            return redirect()->route('plugin.tlcommercecore.product.brand.list');
        } else {
            toastNotification('error', translate('Unable to delete this brand'), 'Failed');
            return redirect()->back();
        }
    }
    /**
     * Will delete bulk brands
     * 
     * @param \Illuminate\Http\Request $request
     * @return void
     */
    public function deleteBulkProductBrand(Request $request)
    {
        $res = $this->brand_repository->deleteBulkBrand($request);
        if ($res == true) {
            toastNotification('success', translate('Selected items deleted successfully'), 'Success');
        } else {
            toastNotification('error', translate('Action Failed') , 'Failed');
        }
    }
    /**
     * Change product brand status
     * 
     * @param \Illuminate\Http\Request $request
     * @return mixed
     */
    public function changeProductBrandStatus(Request $request)
    {
        $res = $this->brand_repository->changeStatus($request->id);
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
     * Change featured status
     * 
     * @param \Illuminate\Http\Request $request
     * @return mixed
     */
    public function changeProductBrandFeatured(Request $request)
    {
        $res = $this->brand_repository->changeFeaturedStatus($request->id);
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
}
