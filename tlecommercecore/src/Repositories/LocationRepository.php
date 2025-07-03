<?php

namespace Plugin\TlcommerceCore\Repositories;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Plugin\TlcommerceCore\Models\Cities;
use Plugin\TlcommerceCore\Models\CitiesTranslation;
use Plugin\TlcommerceCore\Models\Country;
use Plugin\TlcommerceCore\Models\CountryTranslation;
use Plugin\TlcommerceCore\Models\States;
use Plugin\TlcommerceCore\Models\StateTranslation;

class LocationRepository
{
    /**
     * will return country list
     * 
     * @return Collections
     */
    public function countries($status = [1, 2])
    {
        return Country::withCount('states')->orderBy('id', 'ASC')->whereIn('status', $status)->get();
    }

    /**
     * Wll return countries 
     */
    public function countryList($request, $status = [1, 2])
    {
        $query = Country::query();
        if ($request->has('search_key')) {
            $query = $query->where('name', "like", '%' . $request['search_key'] . '%');
        }

        $per_page = $request->has('per_page') && $request['per_page'] != null ? $request['per_page'] : 10;
        if ($per_page != null && $per_page == 'all') {
            return $query->orderBy('name', 'ASC')->whereIn('status', $status)->paginate($query->get()->count())->withQueryString();
        } else {
            return $query->orderBy('name', 'ASC')->whereIn('status', $status)->paginate($per_page)->withQueryString();
        }
    }
    /**
     * Store new country
     * 
     * @param Array $request
     * 
     * @return bool
     */
    public function storeCountry($request)
    {
        try {
            DB::beginTransaction();
            $country = new Country;
            $country->name = $request['name'];
            $country->code = $request['code'];
            $country->phone_code = null;
            $country->status = config('settings.general_status.active');
            $country->save();
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
     * Will delete country
     * 
     * @param Int $id
     * @return bool
     */
    public function deleteCountry($id)
    {
        try {
            DB::beginTransaction();
            $country = Country::findOrFail($id);
            $country->delete();
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
     * Change country status
     * 
     * @param Int $id
     * @return bool
     */
    public function changeCountryStatus($id)
    {
        try {
            DB::beginTransaction();
            $country = Country::findOrFail($id);
            $status = $country->status == config('settings.general_status.active') ? config('settings.general_status.in_active') : config('settings.general_status.active');
            $country->status = $status;
            $country->save();
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
     * will return country details
     * 
     * @param Int $id
     * @return Collection
     */
    public function countryDetails($id)
    {
        return Country::findOrFail($id);
    }
    /**
     * will update country
     * 
     * @param Array $request
     * @return bool
     */
    public function updateCountry($request)
    {
        try {
            DB::beginTransaction();
            if ($request['lang'] != null && $request['lang'] != getDefaultLang()) {
                $country_translation = CountryTranslation::firstOrNew(['country_id' => $request['id'], 'lang' => $request['lang']]);
                $country_translation->name = $request['name'];
                $country_translation->save();
            } else {
                $country = Country::findOrFail($request['id']);
                $country->name = $request['name'];
                $country->code = $request['code'];
                $country->save();
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
     * Will applied country bulk action
     * 
     * @param Array $request
     * @return bool
     */
    public function countryBulkAction(Request $request)
    {
        try {
            DB::beginTransaction();

            //Active Country status
            if ($request['action'] == 'active') {
                $status = config('settings.general_status.active');
                Country::whereIn('id', $request['items'])
                    ->update(
                        [
                            'status' => $status
                        ]
                    );
            }
            //Inactive Country status
            if ($request['action'] == 'in_active') {
                $status = config('settings.general_status.in_active');
                Country::whereIn('id', $request['items'])
                    ->update(
                        [
                            'status' => $status
                        ]
                    );
            }

            //Delete selected countries
            if ($request['action'] == 'delete_all') {
                $status = config('settings.general_status.in_active');
                Country::whereIn('id', $request['items'])
                    ->delete();
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
     * Will applied states bulk action
     * 
     * @param Array $request
     * @return bool
     */
    public function statesBulkAction(Request $request)
    {
        try {
            DB::beginTransaction();

            //Active States status
            if ($request['action'] == 'active') {
                $status = config('settings.general_status.active');
                States::whereIn('id', $request['items'])
                    ->update(
                        [
                            'status' => $status
                        ]
                    );
            }
            //Inactive States status
            if ($request['action'] == 'in_active') {
                $status = config('settings.general_status.in_active');
                States::whereIn('id', $request['items'])
                    ->update(
                        [
                            'status' => $status
                        ]
                    );
            }

            //Delete selected sates
            if ($request['action'] == 'delete_all') {
                $status = config('settings.general_status.in_active');
                States::whereIn('id', $request['items'])
                    ->delete();
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
     * Will applied cities bulk action
     * 
     * @param Array $request
     * @return bool
     */
    public function citiesBulkAction(Request $request)
    {
        try {
            DB::beginTransaction();

            //Active Cities status
            if ($request['action'] == 'active') {
                $status = config('settings.general_status.active');
                Cities::whereIn('id', $request['items'])
                    ->update(
                        [
                            'status' => $status
                        ]
                    );
            }
            //Inactive Cities status
            if ($request['action'] == 'in_active') {
                $status = config('settings.general_status.in_active');
                Cities::whereIn('id', $request['items'])
                    ->update(
                        [
                            'status' => $status
                        ]
                    );
            }

            //Delete selected cities
            if ($request['action'] == 'delete_all') {
                $status = config('settings.general_status.in_active');
                Cities::whereIn('id', $request['items'])
                    ->delete();
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
     * Will return state list
     * 
     * @return collections
     */
    public function states($status = [1, 2])
    {
        return States::with(['country', 'state_translations'])->whereIn('status', $status)->orderBy('id', 'DESC')->get();
    }
    /**
     * Will return state list
     * 
     * @return collections
     */
    public function statesList($request, $status = [1, 2])
    {
        $query = States::with('country');

        if ($request->has('search_key')) {
            $query = $query->where('name', 'like', '%' . $request['search_key'] . '%');
        }
        $per_page = $request->has('per_page') && $request['per_page'] != null ? $request['per_page'] : 10;
        if ($per_page != null && $per_page == 'all') {
            return $query->orderBy('name', 'ASC')->whereIn('status', $status)->paginate($query->get()->count())->withQueryString();
        } else {
            return $query->orderBy('name', 'ASC')->whereIn('status', $status)->paginate($per_page)->withQueryString();
        }
    }
    /**
     * Store new State
     * 
     * @param Array $request
     * @return bool
     */
    public function storeState($request)
    {
        try {
            DB::beginTransaction();
            $state = new States;
            $state->name = $request['name'];
            $state->country_id = $request['country'];
            $state->code = $request['code'];
            $state->status = config('settings.general_status.active');
            $state->save();
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
     * Will delete state
     * 
     * @param Int $id
     * @return bool
     */
    public function deleteState($id)
    {
        try {
            DB::beginTransaction();
            $state = States::findOrFail($id);
            $state->delete();
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
     * Change state status
     * 
     * @param Int $id
     * @return bool
     */
    public function changeStateStatus($id)
    {
        try {
            DB::beginTransaction();
            $state = States::findOrFail($id);
            $status = $state->status == config('settings.general_status.active') ? config('settings.general_status.in_active') : config('settings.general_status.active');
            $state->status = $status;
            $state->save();
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
     * will return state details
     * 
     * @param Int $id
     * @return Collection
     */
    public function stateDetails($id)
    {
        return States::findOrFail($id);
    }
    /**
     * will update state
     * 
     * @param Array $request
     * @return bool
     */
    public function updateState($request)
    {
        try {
            DB::beginTransaction();
            if ($request['lang'] != null && $request['lang'] != getDefaultLang()) {
                $state_translation = StateTranslation::firstOrNew(['state_id' => $request['id'], 'lang' => $request['lang']]);
                $state_translation->name = $request['name'];
                $state_translation->save();
            } else {
                $state = States::findOrFail($request['id']);
                $state->name = $request['name'];
                $state->code = $request['code'];
                $state->country_id = $request['country'];
                $state->save();
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
     * Will return cities
     * 
     * @return Collections
     */
    public function cities()
    {
        return Cities::with('state')->orderBy('id', 'DESC')->get();
    }
    /**
     * Will return cities
     * 
     * @return Collections
     */
    public function citiesList($request, $status = [1, 2])
    {
        $query = Cities::with('state');
        if ($request->has('search_key')) {
            $query = $query->where('name', 'like', '%' . $request['search_key'] . '%');
        }

        $per_page = $request->has('per_page') && $request['per_page'] != null ? $request['per_page'] : 10;
        if ($per_page != null && $per_page == 'all') {
            return $query->orderBy('name', 'ASC')->whereIn('status', $status)->paginate($query->get()->count())->withQueryString();
        } else {
            return $query->orderBy('name', 'ASC')->whereIn('status', $status)->paginate($per_page)->withQueryString();
        }
    }
    /**
     * Store new city
     * 
     * @param Array $request
     * @return bool
     */
    public function storeCity($request)
    {
        try {
            DB::beginTransaction();
            $city = new Cities;
            $city->name = $request['name'];
            $city->state_id = $request['state'];
            $city->status = config('settings.general_status.active');
            $city->save();
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
     * will delete city
     * 
     * @param Int $id
     * @return bool
     */
    public function deleteCity($id)
    {
        try {
            DB::beginTransaction();
            $city = Cities::findOrFail($id);
            $city->delete();
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
     * Change city's status
     * 
     * @param Int $id
     * @return bool
     */
    public function changeCityStatus($id)
    {
        try {
            DB::beginTransaction();
            $city = Cities::findOrFail($id);
            $status = $city->status == config('settings.general_status.active') ? config('settings.general_status.in_active') : config('settings.general_status.active');
            $city->status = $status;
            $city->save();
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
     * Will return city details
     * 
     * @param Int $id
     * @return Collection
     */
    public function cityDetails($id)
    {
        return Cities::findOrFail($id);
    }
    /**
     * will update city
     * 
     * @param Array $request
     * @return bool
     */
    public function updateCity($request)
    {
        try {
            DB::beginTransaction();
            if ($request['lang'] != null && $request['lang'] != getDefaultLang()) {
                $city_translation = CitiesTranslation::firstOrNew(['city_id' => $request['id'], 'lang' => $request['lang']]);
                $city_translation->name = $request['name'];
                $city_translation->save();
            } else {
                $city = Cities::findOrFail($request['id']);
                $city->name = $request['name'];
                $city->state_id = $request['state'];
                $city->save();
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
