<?php

namespace Plugin\TlcommerceCore\Repositories;

use Illuminate\Support\Facades\DB;
use Plugin\TlcommerceCore\Models\AttributeValues;
use Plugin\TlcommerceCore\Models\ProductAttribute;
use Plugin\TlcommerceCore\Models\ProductAttributeTranslation;

class ProductAttributeRepository
{
    /**
     * Will return attribute list
     * 
     * @return Collections
     */
    public function attributeList($status = null)
    {
        if ($status == null) {
            return ProductAttribute::orderBy('id', 'DESC')->get();
        } else {
            return ProductAttribute::orderBy('id', 'DESC')->where('status', $status)->get();
        }
    }
    /**
     * Will store product attribute
     * 
     * @param Array $request
     * @return bool
     */
    public function storeAttribute($request)
    {
        try {
            DB::beginTransaction();
            $attribute = new ProductAttribute;
            $attribute->name = $request['name'];
            $attribute->status = 1;
            $attribute->save();
            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollback();
            return false;
        } catch (\Error $e) {
            DB::rollback();
            return false;
        }
    }
    /**
     * Will return attribute details
     * 
     * @param Int $id
     * @return Collection
     */
    public function attributeDetails($id)
    {
        return ProductAttribute::with('attribute_values')->findOrFail($id);
    }
    /**
     * Will update product attribute
     * 
     * @param Array $request
     * @return bool
     */
    public function updateAttribute($request)
    {
        try {
            DB::beginTransaction();
            if ($request['lang'] != null && $request['lang'] != getDefaultLang()) {
                $attribute_translation = ProductAttributeTranslation::firstOrNew(['attribute_id' => $request['id'], 'lang' => $request['lang']]);
                $attribute_translation->name = $request['name'];
                $attribute_translation->save();
            } else {
                $attribute = ProductAttribute::findOrFail($request['id']);
                $attribute->name = $request['name'];
                $attribute->save();
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
     * Will delete attribute
     * 
     * @param Int $id
     * @return bool
     */
    public function deleteAttribute($id)
    {
        try {
            DB::beginTransaction();
            $attribute = ProductAttribute::findOrFail($id);
            $attribute->delete();
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
     * Will delete bulk attribute
     * 
     * @param Object $request
     * @return bool
     */
    public function deleteBulkAttribute($request)
    {
        try {
            DB::beginTransaction();
            foreach ($request['data'] as $attribute_id) {
                $attribute = ProductAttribute::find($attribute_id);
                if ($attribute != null) {
                    $attribute->delete();
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
     * Will store attribute value
     * 
     * @param Array $request
     * @return bool
     */
    public function storeAttributeValue($request)
    {
        try {
            DB::beginTransaction();
            $attribute_value = new AttributeValues;
            $attribute_value->name = $request['name'];
            $attribute_value->value = $request['name'];
            $attribute_value->attribute_id = $request['attribute_id'];
            $attribute_value->status = 1;
            $attribute_value->save();
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
     * Will delete attbute value
     * 
     * @param Int $id
     * @return bool
     */
    public function deleteAttributeValue($id)
    {
        try {
            DB::beginTransaction();
            $attribute_value = AttributeValues::findOrFail($id);
            $attribute_value->delete();
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
     * Will return attribute value details
     * 
     * @param Int $id
     * @return Collection
     */
    public function attributeValueDetails($id)
    {
        return AttributeValues::findOrFail($id);
    }
    /**
     * will update attribute value
     * 
     * @param Array $request
     * @return bool
     */
    public function updateAttributeValue($request)
    {
        try {
            DB::beginTransaction();
            $attribute_value = AttributeValues::findOrFail($request['id']);
            $attribute_value->name = $request['name'];
            $attribute_value->value = $request['name'];
            $attribute_value->update();
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
     * Change attribute Status
     * 
     * @param Int $id
     * @return bool
     */
    public function changeAttributeStatus($id)
    {
        try {
            DB::beginTransaction();
            $attribute = ProductAttribute::findOrFail($id);
            $status = config('settings.general_status.active');
            if ($attribute->status == config('settings.general_status.active')) {
                $status = config('settings.general_status.in_active');
            }
            $attribute->status = $status;
            $attribute->save();
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
     * Change attribute  value Status
     * 
     * @param Int $id
     * @return bool
     */
    public function changeAttributeValusStatus($id)
    {
        try {
            DB::beginTransaction();
            $attribute_value = AttributeValues::findOrFail($id);
            $status = config('settings.general_status.active');
            if ($attribute_value->status == config('settings.general_status.active')) {
                $status = config('settings.general_status.in_active');
            }
            $attribute_value->status = $status;
            $attribute_value->save();
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
