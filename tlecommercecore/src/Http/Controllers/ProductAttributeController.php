<?php

namespace Plugin\TlcommerceCore\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Plugin\TlcommerceCore\Http\Requests\ProductAttributeRequest;
use Plugin\TlcommerceCore\Repositories\ProductAttributeRepository;
use Plugin\TlcommerceCore\Http\Requests\ProductAttributeValueRequest;

class ProductAttributeController extends Controller
{

    protected $attribute_repository;

    public function __construct(ProductAttributeRepository $attribute_repository)
    {
        $this->attribute_repository = $attribute_repository;
    }
    /**
     * Will return attributes list
     * 
     * @return mixed
     */
    public function productAttributes()
    {
        return view('plugin/tlecommercecore::products.attributes.index')->with(
            [
                'attributes' => $this->attribute_repository->attributeList()
            ]
        );
    }
    /**
     * Store product attribute
     * 
     * @param ProductAttributeRequest $request
     * @return mixed
     */
    public function storeAttribute(ProductAttributeRequest $request)
    {
        $res = $this->attribute_repository->storeAttribute($request);
        if ($res == true) {
            toastNotification('success', translate('Attribute added successfully'), 'Success');
            return redirect()->route('plugin.tlcommercecore.product.attributes.list');
        } else {
            toastNotification('error', translate('Attribute adding failed'), 'Failed');
            return redirect()->back();
        }
    }
    /**
     * Edit product attribute
     * 
     * @param Int $id
     * @param \Illuminate\Http\Request $request 
     * @return mixed
     */
    public function editAttribute($id, Request $request)
    {
        return view('plugin/tlecommercecore::products.attributes.edit_attribute')->with(
            [
                'attribute_details' => $this->attribute_repository->attributeDetails($id),
                'lang' => $request->lang,
                'languages' => getAllLanguages()
            ]
        );
    }
    /**
     * will update product attribute
     * 
     * @param  ProductAttributeRequest $request
     * @return mixed
     */
    public function updateAttribute(ProductAttributeRequest $request)
    {
        $res = $this->attribute_repository->updateAttribute($request);
        if ($res == true) {
            toastNotification('success', translate('Attribute updated successfully'), 'Success');
            return redirect()->route('plugin.tlcommercecore.product.attributes.list');
        } else {
            toastNotification('error', translate('Attribute update failed'), 'Failed');
            return redirect()->back();
        }
    }
    /**
     * Will delete attribute
     * 
     * @param \Illuminate\Http\Request $request
     * @return mixed
     */
    public function deleteAttribute(Request $request)
    {
        $res = $this->attribute_repository->deleteAttribute($request->id);
        if ($res == true) {
            toastNotification('success', translate('Attribute deleted successfully'), 'Success');
            return redirect()->route('plugin.tlcommercecore.product.attributes.list');
        } else {
            toastNotification('error', translate('Unable to delete this attribute'), 'warning');
            return redirect()->back();
        }
    }
    /**
     * Will delete bulk attribute
     * 
     * @param \Illuminate\Http\Request $request
     * @return void
     */
    public function deleteBulkAttribute(Request $request)
    {
        $res = $this->attribute_repository->deleteBulkAttribute($request);
        if ($res == true) {
            toastNotification('success', translate('Selected items deleted successfully'), 'Success');
        } else {
            toastNotification('error', translate('Unable to delete attribute'), 'Warning');
        }
    }
    /**
     * will return attribute values
     * 
     * @param Int $id
     * @return mixed
     */
    public function attributeValues($id)
    {
        return view('plugin/tlecommercecore::products.attributes.attribute_values')->with(
            [
                'attribute_details' => $this->attribute_repository->attributeDetails($id)
            ]
        );
    }
    /**
     * Will store product attribute value
     * 
     * @param ProductAttributeValueRequest $request
     * @return mixed
     */
    public function attributeValuesStore(ProductAttributeValueRequest $request)
    {
        $res = $this->attribute_repository->storeAttributeValue($request);
        if ($res == true) {
            toastNotification('success', translate('Attribute value added successfully'), 'Success');
            return redirect()->route('plugin.tlcommercecore.product.attributes.values', $request->attribute_id);
        } else {
            toastNotification('error', translate('Attribute value insert failed'), 'Failed');
            return redirect()->back();
        }
    }
    /**
     * Delete attribute value
     * 
     * @param \Illuminate\Http\Request $request
     * @return mixed
     */
    public function attributeValueDelete(Request $request)
    {
        $res = $this->attribute_repository->deleteAttributeValue($request->id);
        if ($res == true) {
            toastNotification('success', translate('Attribute value delete successfully'), 'Success');
        } else {
            toastNotification('error', translate('Attribute value delete failed'), 'Failed');
        }
        return redirect()->back();
    }
    /**
     * Will redirect attribute value edit page
     * 
     * @param Int $id
     * @return mixed 
     */
    public function attributeValueEdit($id)
    {
        return view('plugin/tlecommercecore::products.attributes.edit_attribute_value')->with(
            [
                'value_details' => $this->attribute_repository->attributeValueDetails($id)
            ]
        );
    }
    /**
     * Update attribute value
     * 
     * @param  ProductAttributeValueRequest $request
     * @return mixed
     */
    public function attributeValueUpdate(ProductAttributeValueRequest $request)
    {
        $res = $this->attribute_repository->updateAttributeValue($request);
        if ($res == true) {
            toastNotification('success', translate('Attribute value update successfully'), 'Success');
            return redirect()->route('plugin.tlcommercecore.product.attributes.values', $request->attribute_id);
        } else {
            toastNotification('error', translate('Attribute value update failed'), 'Failed');
            return redirect()->back();
        }
    }
    /**
     * Will change product attribute status
     * 
     * @param \Illuminate\Http\Request $request
     * @return void
     */
    public function attributeStatusChange(Request $request)
    {
        $res = $this->attribute_repository->changeAttributeStatus($request->id);
        if ($res == true) {
            toastNotification('success', translate('Attribute status update successfully'), 'Success');
        } else {
            toastNotification('error', translate('Attribute status update failed'), 'Failed');
        }
    }
    /**
     * Will change product attribute status
     * 
     * @param \Illuminate\Http\Request $request
     * @return void
     */
    public function attributeValueStatusChange(Request $request)
    {
        $res = $this->attribute_repository->changeAttributeValusStatus($request->id);
        if ($res == true) {
            toastNotification('success', translate('Status update successfully'), 'Success');
        } else {
            toastNotification('error', translate('Status update failed'), 'Failed');
        }
    }
}
