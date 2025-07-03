<?php

namespace Plugin\TlcommerceCore\Repositories;

use Illuminate\Support\Facades\DB;
use Plugin\TlcommerceCore\Models\Cities;
use Plugin\TlcommerceCore\Models\ShippingZoneHasTaxes;
use Plugin\TlcommerceCore\Models\States;
use Plugin\TlcommerceCore\Models\TaxProfile;
use Plugin\TlcommerceCore\Models\TaxRate;

class VatTaxRepository
{

    /**
     * Will return tax profiles list
     * 
     */
    public function taxProfiles($status = null)
    {

        if ($status != null) {
            return TaxProfile::where('status', $status)->get();
        }

        if ($status == null) {
            return TaxProfile::orderBy('id', 'DESC')->get();
        }
    }

    /**
     * Will store new tax profile
     * 
     * @param Array $request
     * @return bool
     */
    public function storeTaxProfile($request)
    {
        try {
            $tax_profile = new TaxProfile();
            $tax_profile->title = $request['title'];
            $tax_profile->status = $request['status'];
            $tax_profile->save();
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
    /**
     * Will store new tax rates
     * 
     * @param Array $request
     * @return bool
     */
    public function storeTaxRates($request)
    {

        try {
            if ($request['city_id'] != null) {
                $this->storeTaxRateInfo($request['profile_id'], $request['tax_name'], $request['tax_rate'], $request['postal_code'], $request['city_id']);
                return true;
            }

            if ($request['city_id'] == null) {
                if ($request['state_id'] != null) {
                    $cities = Cities::whereIn('state_id', $request['state_id'])->pluck('id');
                    $this->storeTaxRateInfo($request['profile_id'], $request['tax_name'], $request['tax_rate'], $request['postal_code'], $cities);
                    return true;
                }

                if ($request['state_id'] == null && $request['country_id'] != null) {
                    $cities = Cities::whereIn('state_id', States::whereIn('country_id', $request['country_id'])->pluck('id'))->pluck('id');
                    $this->storeTaxRateInfo($request['profile_id'], $request['tax_name'], $request['tax_rate'], $request['postal_code'], $cities);
                    return true;
                }
            }
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
    /**
     * Will store tax rate info
     */
    public function storeTaxRateInfo($profile_id, $tax_name, $tax_rate, $postal_code, $cities)
    {
        foreach ($cities as $city_id) {
            $city_info = Cities::with(['state'])
                ->where('id', $city_id)
                ->first();

            if ($city_info != null) {
                $new_rate = new TaxRate();
                $new_rate->country_id = $city_info->state != null ?  $city_info->state->country_id : null;
                $new_rate->state_id = $city_info->state_id;
                $new_rate->city_id = $city_id;
                $new_rate->tax_name = $tax_name;
                $new_rate->tax_rate = $tax_rate;
                $new_rate->postal_code = $postal_code;
                $new_rate->profile_id = $profile_id;
                $new_rate->save();
            }
        }
    }
    /**
     * Will store new tax profile
     * 
     * @param Array $request
     * @return bool
     */
    public function updateTaxProfile($request)
    {
        try {
            DB::beginTransaction();
            $tax_profile = TaxProfile::findOrFail($request['id']);
            $tax_profile->title = $request['title'];
            $tax_profile->status = $request['status'];
            $tax_profile->save();
            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            return false;
        }
    }
    /**
     * Will return tax profile details
     * 
     * @param Int $id
     * @return Collection
     */
    public function taxProfileDetails($id)
    {
        return  TaxProfile::with(['rates'])->findOrFail($id);
    }

    /**
     * Will return tax profile rates
     * 
     * @param Int $profile_id
     * @return Collections
     */
    public function taxProfileTaxRates($profile_id, $request)
    {

        $query = TaxRate::where('profile_id', $profile_id);
        if ($request->has('search_key') && $request['search_key'] != null) {
            $query = $query->whereHas('country', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request['search_key'] . '%');
            })
                ->orWhere('postal_code', 'like', '%' . $request['search_key'] . '%')
                ->orWhere('tax_name', 'like', '%' . $request['search_key'] . '%');
        }

        $per_page = $request->has('per_page') && $request['per_page'] != null ? $request['per_page'] : 10;

        if ($per_page != null && $per_page == 'all') {
            $items = $query->paginate($query->get()->count())
                ->withQueryString();
        } else {
            $items = $query->paginate($per_page)
                ->withQueryString();
        }

        return $items;
    }

    /**
     * Will delete  tax profile
     * 
     * @param Int $id
     * @return bool
     */
    public function deleteTaxProfile($id)
    {
        try {
            DB::beginTransaction();
            $tax_profile = TaxProfile::findOrFail($id);
            $tax_profile->delete();
            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            return false;
        }
    }
}
