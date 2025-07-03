<?php

namespace Plugin\PickupPoint\Http\Repositories;

use Illuminate\Support\Facades\DB;
use Plugin\PickupPoint\Models\PickupPoint;

class PickupPointRepository
{
    /**
     * get pickup point list query builder
     *
     * @return mixed
     */
    public function getPickupPointList()
    {

        return PickupPoint::with(['country', 'state', 'city'])->get()->map(function ($item) {
            return [
                'pickup_id' => $item->id,
                'name' => $item->name,
                'location' => $item->location,
                'status' => $item->status,
                'phone' => $item->phone,
                'country' => $item->country != null ? $item->country->translation('name') : null,
                'state' => $item->state != null ? $item->state->translation('name') : null,
                'city' => $item->city != null ? $item->city->translation('name') : null,
            ];
        });
    }

    /**
     * Will get active pickup point list
     * 
     * @param Object $request
     * @return Collections
     */
    public function getActivePickupPoint($request)
    {
        return PickupPoint::with(['country', 'city', 'state'])
            ->where('status', config('settings.general_status.active'))
            ->select('name', 'location', 'phone', 'country_id', 'city_id', 'state_id', 'id')
            ->get();
    }
}
