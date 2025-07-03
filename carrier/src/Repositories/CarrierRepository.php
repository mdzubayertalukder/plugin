<?php

namespace Plugin\Carrier\Repositories;

use Illuminate\Support\Facades\DB;
use Plugin\Carrier\Models\ShippingCarrier;

class CarrierRepository
{
    /**
     * Will store new courier service
     *
     * @param Object $request
     * @return bool
     */
    public function storeNewCourier($request)
    {
        try {
            $courier = new ShippingCarrier;
            $courier->name = $request['name'];
            $courier->tracking_url = $request['tracking_url'];
            $courier->logo = $request['logo'];
            $courier->save();
            return true;
        } catch (\Exception $e) {
            return false;
        } catch (\Error $e) {
            return false;
        }
    }
    /**
     * Will return all courier list
     *
     * @return collections
     */
    public function couriers($status = null)
    {
        if ($status != null) {
            return ShippingCarrier::where('status', $status)->get()->map(function ($courier) {
                return [
                    'id' => $courier->id,
                    'name' => $courier->name,
                    'tracking_url' => $courier->tracking_url,
                    'logo' => $courier->logo,
                    'status' => $courier->status
                ];
            });
        } else {
            return ShippingCarrier::all()->map(function ($courier) {
                return [
                    'id' => $courier->id,
                    'name' => $courier->name,
                    'logo' => $courier->logo,
                    'tracking_url' => $courier->tracking_url,
                    'status' => $courier->status
                ];
            });
        }
    }
    /**
     * Will update courier status
     *
     * @param Int $id
     * @return bool
     */
    public function updateCourierStatus($id)
    {
        try {
            DB::beginTransaction();
            $courier = ShippingCarrier::findOrFail($id);
            $status = $courier->status == config('settings.general_status.in_active') ? config('settings.general_status.active') : config('settings.general_status.in_active');
            $courier->status = $status;
            $courier->save();
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
     * Will delete a courier
     *
     * @param Int $id
     * @return bool
     */
    public function deleteCourier($id)
    {
        try {
            DB::beginTransaction();
            $courier = ShippingCarrier::findOrFail($id);
            $courier->delete();
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
     * Will return courier info
     *
     * @param Int $id
     * @return collection
     */
    public function courierDetails($id)
    {
        return ShippingCarrier::findOrFail($id);
    }
    /**
     * Will update courier details
     *
     * @param Object $request
     * @return bool
     */
    public function updateCourier($request)
    {
        try {
            DB::beginTransaction();
            $courier = ShippingCarrier::findOrFail($request['id']);
            $courier->name = $request['name'];
            $courier->tracking_url = $request['tracking_url'];
            $courier->logo = $request['edit_logo'];
            $courier->save();
            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            return false;
        } catch (\Exception $e) {
            DB::rollBack();
            return false;
        }
    }
}
