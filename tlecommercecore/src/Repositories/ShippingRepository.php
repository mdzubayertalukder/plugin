<?php

namespace Plugin\TlcommerceCore\Repositories;

use Illuminate\Support\Facades\DB;
use Plugin\TlcommerceCore\Models\Cities;
use Plugin\TlcommerceCore\Models\States;
use Plugin\TlcommerceCore\Models\ShippingRate;
use Plugin\TlcommerceCore\Models\ShippingZone;
use Plugin\TlcommerceCore\Models\ShippingTimes;
use Plugin\TlcommerceCore\Models\ShippingProfile;
use Plugin\TlcommerceCore\Models\ShippingZoneCities;
use Plugin\TlcommerceCore\Models\ShippingZoneStates;
use Plugin\TlcommerceCore\Models\ShippingZoneCountries;
use Plugin\TlcommerceCore\Models\ShippingProfileProducts;
use Plugin\TlcommerceCore\Models\LocationWiseShippingRate;
use Plugin\TlcommerceCore\Models\LocationWiseShippingRateCity;
use Plugin\TlcommerceCore\Models\LocationWiseShippingRateStates;
use Plugin\TlcommerceCore\Models\LocationWiseShippingRateCountry;

class ShippingRepository
{

    /**
     * Will return shipping profiles
     *
     */
    public function shippingProfiles()
    {
        return ShippingProfile::all();
    }

    /**
     * Will return shipping processing times
     * 
     */
    public function shippingTimes()
    {
        return ShippingTimes::all();
    }
    /**
     * Will delete Shipping time
     * 
     * @param Int $id
     * @return Boolean
     */
    public function deleteShippingTime($id)
    {
        try {
            DB::beginTransaction();
            $shipping_time = ShippingTimes::findOrFail($id);
            $shipping_time->delete();
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
     * Will store shipping time
     * 
     * @param Object $request
     * @return Boolean
     */
    public function storeShippingTime($request)
    {
        try {
            $shipping_time = new shippingTimes;
            $shipping_time->min_value = $request['minimmum_shipping_time'];
            $shipping_time->min_unit = $request['minimmum_shipping_time_unit'];
            $shipping_time->max_value = $request['maximum_shipping_time'];
            $shipping_time->max_unit = $request['maximum_shipping_time_unit'];
            $shipping_time->save();
            return true;
        } catch (\Exception $e) {
            return false;
        } catch (\Error $e) {
            return false;
        }
    }
    /**
     * Will return location based shipping rates
     */
    public function locationBasedShippingCosts()
    {
        $query = LocationWiseShippingRate::with(['countries', 'states', 'cities'])->orderBy('id', 'DESC');

        $rates = $query->get();
        return $rates;
    }
    /**
     * Will store shipping profile
     * 
     * @param Object $request
     * @return Int 
     */
    public function storeShippingProfile($request)
    {
        try {
            DB::beginTransaction();
            $shipping_profile = new ShippingProfile;
            $shipping_profile->name = $request['profile_name'];
            $shipping_profile->address = $request['address'];
            $shipping_profile->location = $request['location'];
            $shipping_profile->profile_type     = 'custom';
            $shipping_profile->save();

            if ($request->has('products')) {
                foreach ($request['products'] as $product) {
                    $profile_products = new ShippingProfileProducts;
                    $profile_products->product_id    = $product;
                    $profile_products->profile_id    = $shipping_profile->id;
                    $profile_products->save();
                }
            }
            DB::commit();
            return $shipping_profile->id;
        } catch (\Exception $e) {
            DB::rollBack();
            return null;
        } catch (\Error $e) {
            DB::rollBack();
            return null;
        }
    }
    /**
     * Will return shipping  profile ddetails
     * 
     * @param Int $id
     * @return Collection 
     */
    public function profileDetails($id)
    {
        return ShippingProfile::with(['products', 'zones'])->findOrFail($id);
    }
    /**
     * Will store new zone
     * 
     * @param Object $request
     * @return bool
     */
    public function storeNewZone($request)
    {
        try {
            DB::beginTransaction();
            $zone = new ShippingZone;
            $zone->profile_id = $request['profile_id'];
            $zone->name = $request['name'];
            $zone->save();
            //store zone countries
            if ($request->has('country_id')) {
                foreach ($request['country_id'] as $country) {
                    $zone_country = new ShippingZoneCountries;
                    $zone_country->zone_id = $zone->id;
                    $zone_country->country_id = $country;
                    $zone_country->save();
                }
            }
            //store zone states
            if ($request->has('state_id')) {
                foreach ($request['state_id'] as $state) {
                    $zone_state = new ShippingZoneStates;
                    $zone_state->zone_id = $zone->id;
                    $zone_state->state_id = $state;
                    $zone_state->save();
                }
            }
            //store zone cities
            if ($request->has('city_id')) {
                foreach ($request['city_id'] as $city) {
                    $zone_city = new ShippingZoneCities;
                    $zone_city->zone_id = $zone->id;
                    $zone_city->city_id = $city;
                    $zone_city->save();
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

    public function storeNewLocationBasedRate($request)
    {
        try {
            DB::beginTransaction();
            $rate = new LocationWiseShippingRate();
            $rate->title = $request['title'];
            $rate->cost = $request['cost'];
            $rate->save();

            //store rate countries
            if ($request->has('country_id')) {
                foreach ($request['country_id'] as $country) {
                    $rate_country = new LocationWiseShippingRateCountry;
                    $rate_country->rate_id = $rate->id;
                    $rate_country->country_id = $country;
                    $rate_country->save();
                }
            }
            //store rate states
            if ($request->has('state_id')) {
                foreach ($request['state_id'] as $state) {
                    $rate_state = new LocationWiseShippingRateStates;
                    $rate_state->rate_id = $rate->id;
                    $rate_state->state_id = $state;
                    $rate_state->save();
                }
            }
            //store rate cities
            if ($request->has('city_id')) {
                foreach ($request['city_id'] as $city) {
                    $rate_city = new LocationWiseShippingRateCity;
                    $rate_city->rate_id = $rate->id;
                    $rate_city->city_id = $city;
                    $rate_city->save();
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

    public function updateLocationBasedRate($request)
    {
        try {
            DB::beginTransaction();
            $rate = LocationWiseShippingRate::findOrFail($request['id']);
            $rate->title = $request['title'];
            $rate->cost = $request['cost'];
            $rate->save();

            //Store rate countries
            LocationWiseShippingRateCountry::where('rate_id', $request['id'])->delete();
            if ($request->has('country_id')) {
                foreach ($request['country_id'] as $country) {
                    $rate_country = new LocationWiseShippingRateCountry;
                    $rate_country->rate_id = $rate->id;
                    $rate_country->country_id = $country;
                    $rate_country->save();
                }
            }

            //Store rate states
            LocationWiseShippingRateStates::where('rate_id', $request['id'])->delete();
            if ($request->has('state_id')) {
                foreach ($request['state_id'] as $state) {
                    $rate_state = new LocationWiseShippingRateStates;
                    $rate_state->rate_id = $rate->id;
                    $rate_state->state_id = $state;
                    $rate_state->save();
                }
            }

            //Store rate cities
            LocationWiseShippingRateCity::where('rate_id', $request['id'])->delete();
            if ($request->has('city_id')) {
                foreach ($request['city_id'] as $city) {
                    $rate_city = new LocationWiseShippingRateCity;
                    $rate_city->rate_id = $rate->id;
                    $rate_city->city_id = $city;
                    $rate_city->save();
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
     * Will return all shipping zone
     * 
     * @return Collections
     */
    public function shippingZones()
    {
        return ShippingZone::all();
    }
    /**
     * Will update shipping zone
     * 
     * @param Object $request
     * @return Boolean
     */
    public function updateShippingZone($request)
    {
        try {
            DB::beginTransaction();
            $zone = ShippingZone::findOrFail($request['id']);
            $zone->name = $request['name'];
            $zone->save();
            $zone->cities()->delete();
            $zone->countries()->delete();
            $zone->states()->delete();
            //store zone countries
            if ($request->has('country_id')) {
                foreach ($request['country_id'] as $country) {
                    $zone_country = new ShippingZoneCountries;
                    $zone_country->zone_id = $zone->id;
                    $zone_country->country_id = $country;
                    $zone_country->save();
                }
            }
            //store zone states
            if ($request->has('state_id')) {
                foreach ($request['state_id'] as $state) {
                    $zone_state = new ShippingZoneStates;
                    $zone_state->zone_id = $zone->id;
                    $zone_state->state_id = $state;
                    $zone_state->save();
                }
            }
            //store zone cities
            if ($request->has('city_id')) {
                foreach ($request['city_id'] as $city) {
                    $zone_city = new ShippingZoneCities;
                    $zone_city->zone_id = $zone->id;
                    $zone_city->city_id = $city;
                    $zone_city->save();
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
     * Will return shipping Zone details
     * 
     * @param Int $id
     * @return Collection
     */
    public function shippingZoneDetails($id)
    {
        return ShippingZone::findOrFail($id);
    }
    /**
     * Will return states of a shipping zone
     * 
     * @param Int $zone_id
     * @return Collections
     */
    public function zonesStates($zone_id)
    {
        return States::with(['state_translations'])->whereIn('id', Cities::whereIn('id', ShippingZoneCities::where('zone_id', $zone_id)->pluck('city_id'))->distinct('state_id')->pluck('state_id'))->get();
    }
    /**
     * Will store shipping rate
     * 
     * @param Object $array
     * @return bool
     */
    public function storeShippingRate($request)
    {
        try {
            if ($request->has('rate_type') && $request['rate_type'] == config('tlecommercecore.shipping_rate_type.carrier_rate')) {
                if ($request->has('carrier_condition') && count($request['carrier_condition']) > 0) {
                    foreach ($request['carrier_condition'] as $condition) {
                        $min_limit = $condition['min_weight'] != null ? $condition['min_weight'] : 0;
                        $max_limit = $condition['max_weight'] != null ? $condition['max_weight'] : 0;
                        $shipping_cost = $condition['cost'] != null ? $condition['cost'] : 0;
                        $rate = new ShippingRate;
                        $rate->zone_id = $request['zone_id'];
                        $rate->carrier_id = $request['courier'];
                        $rate->has_condition = config('settings.general_status.active');
                        $rate->shipping_cost = $shipping_cost;
                        $rate->delivery_time = $request['shipping_time'];
                        $rate->rate_type = config('tlecommercecore.shipping_rate_type.carrier_rate');
                        $rate->based_on = config('tlecommercecore.shipping_based_on.weight_based');
                        $rate->min_limit = $min_limit;
                        $rate->max_limit = $max_limit;
                        $rate->shipping_medium = $request['shipped_by'];
                        $rate->condition_unit = 'Kg';
                        $rate->save();
                    }
                }
            } else {
                $has_condition = $request['is_active_condition'] == config('settings.general_status.active') ? config('settings.general_status.active') : config('settings.general_status.in_active');
                $shipping_based_on = $request['condionType'] == 'weight_based' ? config('tlecommercecore.shipping_based_on.weight_based') : config('tlecommercecore.shipping_based_on.price_based');
                $min_limit = $request['condionType'] == 'weight_based' ? $request['min_weight'] : $request['min_price'];
                $max_limit = $request['condionType'] == 'weight_based' ? $request['max_weight'] : $request['max_price'];
                $unit = $request['condionType'] == 'weight_based' ? 'gm' : '$';
                $rate = new ShippingRate;
                $rate->zone_id = $request['zone_id'];
                $rate->name = $request['rate_name'];
                $rate->has_condition = $has_condition;
                $rate->based_on = $shipping_based_on;
                $rate->min_limit = $min_limit;
                $rate->max_limit = $max_limit;
                $rate->condition_unit = $unit;
                $rate->shipping_cost = $request['shipping_cost'];
                $rate->delivery_time = $request['shipping_time'];
                $rate->rate_type = config('tlecommercecore.shipping_rate_type.own_rate');
                $rate->save();
            }
            return true;
        } catch (\Exception $e) {
            return false;
        } catch (\Error $e) {
            return false;
        }
    }
    /**
     * Will update shipping rate 
     * 
     * @param Object $request
     * @return Boolean
     */
    public function updateShippingRate($request)
    {
        try {
            if ($request->has('edit_rate_type') && $request['edit_rate_type'] == config('tlecommercecore.shipping_rate_type.carrier_rate')) {
                if ($request->has('carrier_condition') && count($request['carrier_condition']) > 0) {
                    foreach ($request['carrier_condition'] as $condition) {
                        $min_limit = $condition['min_weight'] != null ? $condition['min_weight'] : 0;
                        $max_limit = $condition['max_weight'] != null ? $condition['max_weight'] : 0;
                        $shipping_cost = $condition['cost'] != null ? $condition['cost'] : 0;
                        $rate = ShippingRate::findOrFail($request['rate_id']);
                        $rate->zone_id = $request['zone_id'];
                        $rate->carrier_id = $request['courier'];
                        $rate->has_condition = config('settings.general_status.active');
                        $rate->shipping_cost = $shipping_cost;
                        $rate->delivery_time = $request['shipping_time'];
                        $rate->rate_type = config('tlecommercecore.shipping_rate_type.carrier_rate');
                        $rate->based_on = config('tlecommercecore.shipping_based_on.weight_based');
                        $rate->min_limit = $min_limit;
                        $rate->max_limit = $max_limit;
                        $rate->shipping_medium = $request['shipped_by'];
                        $rate->condition_unit = 'Kg';
                        $rate->save();
                    }
                }
            } else {
                $has_condition = $request['is_active_condition'] == config('settings.general_status.active') ? config('settings.general_status.active') : config('settings.general_status.in_active');
                $shipping_based_on = $request['conditionTypeEdit'] == 'weight_based' ? config('tlecommercecore.shipping_based_on.weight_based') : config('tlecommercecore.shipping_based_on.price_based');
                $min_limit = $request['conditionTypeEdit'] == 'weight_based' ? $request['min_weight'] : $request['min_price'];
                $max_limit = $request['conditionTypeEdit'] == 'weight_based' ? $request['max_weight'] : $request['max_price'];
                $unit = $request['conditionTypeEdit'] == 'weight_based' ? 'gm' : '$';
                $rate = ShippingRate::findOrFail($request['rate_id']);
                $rate->zone_id = $request['zone_id'];
                $rate->name = $request['rate_name'];
                $rate->has_condition = $has_condition;
                $rate->based_on = $shipping_based_on;
                $rate->min_limit = $min_limit;
                $rate->max_limit = $max_limit;
                $rate->condition_unit = $unit;
                $rate->shipping_cost = $request['shipping_cost'];
                $rate->delivery_time = $request['shipping_time'];
                $rate->rate_type = config('tlecommercecore.shipping_rate_type.own_rate');
                $rate->save();
            }
            return true;
        } catch (\Exception $e) {
            return false;
        } catch (\Error $e) {
            return false;
        }
    }
    /**
     * Will delete shipping profile
     * 
     * @param Int $id
     * @return Boolean
     */
    public function deleteShippingProfile($id)
    {
        try {
            $shipping_profile = ShippingProfile::findOrFail($id);
            if ($shipping_profile->profile_type == 'general') {
                return false;
            }
            DB::beginTransaction();
            if (count($shipping_profile->zones) > 0) {
                foreach ($shipping_profile->zones as $zone) {
                    $zone->cities()->delete();
                    $zone->countries()->delete();
                    $zone->states()->delete();
                    $zone->rates()->delete();
                }
            }
            $shipping_profile->zones()->delete();
            $shipping_profile->products()->delete();
            $shipping_profile->delete();
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

    public function deleteLocationBasedShippingRate($id)
    {
        try {
            $shipping_rate = LocationWiseShippingRate::findOrFail($id);

            DB::beginTransaction();
            LocationWiseShippingRateCountry::where('rate_id', $id)->delete();
            LocationWiseShippingRateStates::where('rate_id', $id)->delete();
            LocationWiseShippingRateCity::where('rate_id', $id)->delete();
            $shipping_rate->delete();
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
     * Will delete a zone
     * 
     * @param Int $id
     * @return bool
     */
    public function deleteZone($id)
    {
        try {
            DB::beginTransaction();
            $zone = ShippingZone::findOrFail($id);
            $zone->rates()->delete();
            $zone->cities()->delete();
            $zone->countries()->delete();
            $zone->states()->delete();
            $zone->delete();
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
     * Will return country in use in another shipping zone or not
     * 
     * @param Int $country_id
     * @param Int $profile_id
     * @return Boolean
     */
    public static function countryInAnotherZone($country_id, $profile_id)
    {
        if (count(ShippingZoneCountries::where('country_id', $country_id)->pluck('country_id')) > 0) {
            $country_profiles = ShippingZone::whereIn('id', ShippingZoneCountries::where('country_id', $country_id)->pluck('zone_id'))->pluck('profile_id')->toArray();
            if (in_array($profile_id, $country_profiles)) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }


    /**
     * Will return a state is use in another shipping  profile or not
     * 
     * @param Int $state_id
     * @param Int $profile_id
     * @return Boolean
     */
    public static function stateInAnotherZone($state_id, $profile_id)
    {

        if (count(ShippingZoneStates::where('state_id', $state_id)->pluck('state_id')) > 0) {
            $state_profiles = ShippingZone::whereIn('id', ShippingZoneStates::where('state_id', $state_id)->pluck('zone_id'))->pluck('profile_id')->toArray();
            if (in_array($profile_id, $state_profiles)) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }
    /**
     *  Will return a city is use in another shipping profile or not
     * 
     * @param Int $city_id
     * @param int $profile_id
     * @return Boolean 
     */
    public static function cityInAnotherZone($city_id, $profile_id)
    {
        if (count(ShippingZoneCities::where('city_id', $city_id)->pluck('city_id')) > 0) {
            $city_profiles = ShippingZone::whereIn('id', ShippingZoneCities::where('city_id', $city_id)->pluck('zone_id'))->pluck('profile_id')->toArray();
            if (in_array($profile_id, $city_profiles)) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    public static function countryInAnotherShippingRate($country_id, $rate_id = null)
    {
        if (count(LocationWiseShippingRateCountry::where('country_id', $country_id)->pluck('country_id')) > 0) {
            if ($rate_id == null) {
                return true;
            }

            return true;
        } else {
            return false;
        }
    }

    public static function stateInAnotherShippingRate($state_id, $rate_id = null)
    {

        if (count(LocationWiseShippingRateStates::where('state_id', $state_id)->pluck('state_id')) > 0) {
            if ($rate_id == null) {
                return true;
            }

            return true;
        } else {
            return false;
        }
    }
    /**
     *  Will return a city is use in another shipping profile or not
     * 
     * @param Int $city_id
     * @param int $profile_id
     * @return Boolean 
     */
    public static function cityInAnotherShippingRate($city_id, $rate_id = null)
    {
        if (count(LocationWiseShippingRateCity::where('city_id', $city_id)->pluck('city_id')) > 0) {

            if ($rate_id == null) {
                return true;
            }

            return true;
        } else {
            return false;
        }
    }
}
