<?php

namespace Plugin\TlcommerceCore\Repositories;

use Illuminate\Support\Facades\DB;
use Plugin\TlcommerceCore\Models\ProductCondition;
use Plugin\TlcommerceCore\Models\ProductConditionTranslation;

class ProductConditionRepository
{
    /**
     * will return condition list
     * 
     * @return Collections
     */
    public function conditionList()
    {
        return ProductCondition::orderBy('id', 'DESC')->get();
    }
    /**
     * Will store new condition
     * 
     * @param Array $request
     * @return bool
     */
    public function storeCondition($request)
    {
        try {
            DB::beginTransaction();
            $condition = new ProductCondition;
            $condition->name = $request['name'];
            $condition->status = 1;
            $condition->save();
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
            $condition = ProductCondition::findOrFail($id);
            $status = config('settings.general_status.active');
            if ($condition->status == config('settings.general_status.active')) {
                $status = config('settings.general_status.in_active');
            }
            $condition->status = $status;
            $condition->save();
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
     * Will delete product condition
     * 
     * @param Int $id
     * @return bool
     */
    public function deleteCondition($id)
    {
        try {
            DB::beginTransaction();
            $condition = ProductCondition::findOrFail($id);
            $condition->delete();
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
     * Will delete bulk conditions
     * 
     * @param Object $request
     * @return mixed
     */
    public function deleteBulkCondition($request)
    {
        try {
            DB::beginTransaction();
            foreach ($request['data'] as $condition_id) {
                $condition = ProductCondition::find($condition_id);
                if ($condition != null) {
                    $condition->delete();
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
     * Will return condition details
     * 
     * @param Int $id
     * @return Collection
     */
    public function conditionDetails($id)
    {
        return ProductCondition::findOrFail($id);
    }
    /**
     * Will update product condition
     * 
     * @param Array $request
     * @return bool
     */
    public function updateCondition($request)
    {
        try {
            DB::beginTransaction();
            if ($request['lang'] != null && $request['lang'] != getDefaultLang()) {
                $unit_translation = ProductConditionTranslation::firstOrNew(['condition_id' => $request['id'], 'lang' => $request['lang']]);
                $unit_translation->name = $request['name'];
                $unit_translation->save();
            } else {
                $condition = ProductCondition::findOrFail($request['id']);
                $condition->name = $request['name'];
                $condition->save();
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
}
