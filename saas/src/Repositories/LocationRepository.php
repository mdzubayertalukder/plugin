<?php

namespace Plugin\Saas\Repositories;

use Illuminate\Support\Facades\DB;

class LocationRepository
{
    /**
     * get all countries
     * @return mixed|array
     */
    public function getAllCountries()
    {
        $all_countries = DB::table('tl_countries')->select([
            'id',
            'name'
        ])
            ->orderBy('name', 'ASC')
            ->get();

        return $all_countries;
    }
    /**
     * will return query of states of requested country
     */
    public function getStatesByCountryId($country_id)
    {
        $query = DB::table('tl_countries')
            ->join('tl_states', 'tl_states.country_id', '=', 'tl_countries.id')
            ->where('tl_countries.id', '=', $country_id)
            ->select([
                'tl_states.id',
                'tl_states.name'
            ])
            ->orderBy('tl_states.name', 'ASC');

        return $query;
    }

    /**
     * will return query of cities of requested state
     *
     */
    public function getCitiesByStateId($state_id)
    {
        $query = DB::table('tl_states')
            ->join('tl_cities', 'tl_cities.state_id', '=', 'tl_states.id')
            ->where('tl_states.id', '=', $state_id)
            ->select([
                'tl_cities.id',
                'tl_cities.name'
            ]);

        return $query;
    }
}
