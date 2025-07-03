<?php

namespace Plugin\TlcommerceCore\Repositories;

use Illuminate\Support\Facades\DB;
use Plugin\TlcommerceCore\Models\Colors;
use Plugin\TlcommerceCore\Models\ColorTranslation;

class ColorRepository
{

    /**
     * Will return color list
     * 
     * @return Collection
     */
    public function colorList($status = [1, 2])
    {
        return Colors::whereIn('status', $status)->orderBy('id', 'DESC')->get();
    }
    /**
     * Store new color
     * 
     * @param Array $request
     * @return bool
     */
    public function storeColor($request)
    {
        try {
            DB::beginTransaction();
            $color = new Colors;
            $color->name = $request['name'];
            $color->code = $request['code'];
            $color->save();
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
     * delete color
     * 
     * @param Int $id
     * @return bool
     */
    public function deleteColor($id)
    {
        try {
            DB::beginTransaction();
            $color = Colors::findOrFail($id);
            $color->delete();
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
     * delete bulk color
     * 
     * @param Object $request
     * @return bool
     */
    public function deleteBulkColor($request)
    {
        try {
            DB::beginTransaction();
            foreach ($request['data'] as $color_id) {
                $color = Colors::find($color_id);
                if ($color != null) {
                    $color->delete();
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
     * Color details
     * 
     * @param Int $id
     * @return Collection
     */
    public function colorDetails($id)
    {
        return Colors::findOrFail($id);
    }
    /**
     * Update color
     * 
     * @param Array $request
     * @return mixed
     */
    public function updateColor($request)
    {
        try {
            DB::beginTransaction();
            if ($request['lang'] != null && $request['lang'] != getDefaultLang()) {
                $color_translation = ColorTranslation::firstOrNew(['color_id' => $request['id'], 'lang' => $request['lang']]);
                $color_translation->name = $request['name'];
                $color_translation->save();
            } else {
                $color = Colors::findOrFail($request['id']);
                $color->name = $request['name'];
                $color->code = $request['code'];
                $color->save();
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
