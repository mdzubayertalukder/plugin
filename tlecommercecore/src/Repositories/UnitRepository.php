<?php

namespace Plugin\TlcommerceCore\Repositories;

use Illuminate\Support\Facades\DB;
use Plugin\TlcommerceCore\Models\Units;
use Plugin\TlcommerceCore\Models\UnitTranslation;

class UnitRepository
{
    /**
     * Will return units
     * 
     * @return Collection
     */
    public function unitList()
    {
        return Units::orderBy('id', 'DESC')->get();
    }
    /**
     * store unit
     * 
     * @param Array $request
     * @return bool
     */
    public function storeUnit($request)
    {
        try {
            DB::beginTransaction();
            $unit = new Units;
            $unit->name = $request['name'];
            $unit->save();
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
     * will delete unit
     * 
     * @param Int $id
     * @return bool
     */
    public function deleteUnit($id)
    {
        try {
            DB::beginTransaction();
            $unit = Units::findOrFail($id);
            $unit->delete();
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
     * Will delete bulk unit
     * 
     * @param Object $request
     * @return bool
     */
    public function deleteBulkUnit($request)
    {
        try {
            DB::beginTransaction();
            foreach ($request['data'] as $unit_id) {
                $unit = Units::findOrFail($unit_id);
                if ($unit != null) {
                    $unit->delete();
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
     * will return unit detais
     * 
     * @param Int $id
     * @return Collection
     */
    public function unitDetails($id)
    {
        return  Units::findOrFail($id);
    }
    /**
     * will update unit 
     * 
     * @param Array $request
     * @return bool
     */
    public function updateUnit($request)
    {
        try {
            DB::beginTransaction();
            if ($request['lang'] != null && $request['lang'] != getDefaultLang()) {
                $unit_translation = UnitTranslation::firstOrNew(['unit_id' => $request['id'], 'lang' => $request['lang']]);
                $unit_translation->name = $request['name'];
                $unit_translation->save();
            } else {
                $unit = Units::findOrFail($request['id']);
                $unit->name = $request['name'];
                $unit->save();
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
