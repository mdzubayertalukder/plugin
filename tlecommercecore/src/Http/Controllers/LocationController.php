<?php

namespace Plugin\TlcommerceCore\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Plugin\TlcommerceCore\Http\Requests\StateRequest;
use Plugin\TlcommerceCore\Http\Requests\CitiesRequest;
use Plugin\TlcommerceCore\Http\Requests\CountryRequest;
use Plugin\TlcommerceCore\Repositories\LocationRepository;

class LocationController extends Controller
{

    protected $location_repository;

    public function __construct(LocationRepository $location_repository)
    {
        $this->location_repository = $location_repository;
    }
    /**
     * Will return country list
     * 
     * @return mixed
     */
    public function countries(Request $request)
    {
        return view('plugin/tlecommercecore::shipping.locations.country.index')->with(
            [
                'countries' => $this->location_repository->countryList($request),
            ]
        );
    }
    /**
     * Will return new country page
     * 
     * @return mixed
     */
    public function newCountry()
    {
        return view('plugin/tlecommercecore::shipping.locations.country.add_new');
    }
    /**
     * Store new country
     * 
     * @param CountryRequest $request
     * @return mixed
     */
    public function storeNewCountry(CountryRequest $request)
    {
        $res = $this->location_repository->storeCountry($request);
        if ($res == true) {
            toastNotification('success', translate('Country added successfully'), 'Success');
            return redirect()->route('plugin.tlcommercecore.shipping.locations.country.list');
        } else {
            toastNotification('error', translate('Country store failed'), 'Failed');
            return redirect()->back();
        }
    }
    /**
     * Delete a country
     * 
     * @param \Illuminate\Http\Request $request
     * @return mixed
     */
    public function deleteCountry(Request $request)
    {
        $res = $this->location_repository->deleteCountry($request->id);
        if ($res == true) {
            toastNotification('success', translate('Country deleted successfully'), 'Success');
            return redirect()->route('plugin.tlcommercecore.shipping.locations.country.list');
        } else {
            toastNotification('error', translate('Country delete failed'), 'Failed');
            return redirect()->back();
        }
    }
    /**
     * Change language status
     * 
     * @param \Illuminate\Http\Request $request
     * @return mixed
     */
    public function countryStatusChange(Request $request)
    {
        $res = $this->location_repository->changeCountryStatus($request->id);
        if ($res == true) {
            toastNotification('success',  translate('Status updated successfully'), 'Success');
        } else {
            toastNotification('success', translate('Status update failed'), 'failed');
        }
    }
    /**
     * Will redirect edit country page
     * 
     * @param Int $id
     * @return mixed
     */
    public function editCountry($id)
    {
        return view('plugin/tlecommercecore::shipping.locations.country.edit')->with(
            [
                'countryDetails' => $this->location_repository->countryDetails($id)
            ]
        );
    }
    /**
     * will update country
     * 
     * @param CountryRequest $request
     * @return mixed
     */
    public function updateCountry(CountryRequest $request)
    {
        $res = $this->location_repository->updateCountry($request);
        if ($res == true) {
            toastNotification('success', translate('Country updated successfully'), 'Success');
            return redirect()->route('plugin.tlcommercecore.shipping.locations.country.edit', ['id' => $request['id'], 'lang' => $request['lang']]);
        } else {
            toastNotification('error', translate('Country update failed'), 'Failed');
            return redirect()->back();
        }
    }

    /**
     * Will return state list
     * 
     * @return mixed
     */
    public function states(Request $request)
    {
        return view('plugin/tlecommercecore::shipping.locations.state.index')->with(
            [
                'states' => $this->location_repository->statesList($request)
            ]
        );
    }
    /**
     * Will redirect new state form page
     * 
     * @return mixed
     */
    public function newState()
    {
        return view('plugin/tlecommercecore::shipping.locations.state.add_new')->with(
            [
                'countries' => $this->location_repository->countries()
            ]
        );
    }
    /**
     * Store new state
     * 
     * @param StateRequest $request
     * @return mixed
     */
    public function storeState(StateRequest $request)
    {
        $res = $this->location_repository->storeState($request);
        if ($res == true) {
            toastNotification('success', translate('State added successfully'), 'Success');
            return redirect()->route('plugin.tlcommercecore.shipping.locations.states.list');
        } else {
            toastNotification('error', translate('Action failed'), 'Failed');
            return redirect()->back();
        }
    }
    /**
     * Will delete a store
     * 
     * @param \Illuminate\Http\Request $request
     * @return mixed
     */
    public function deleteState(Request $request)
    {
        $res = $this->location_repository->deleteState($request->id);
        if ($res == true) {
            toastNotification('success', translate('State deleted successfully'), 'Success');
            return redirect()->route('plugin.tlcommercecore.shipping.locations.states.list');
        } else {
            toastNotification('error', translate('Action failed'), 'Failed');
            return redirect()->back();
        }
    }
    /**
     * Will change state status
     * 
     * @param \Illuminate\Http\Request $request
     * @return mixed
     */
    public function changeStateStatus(Request $request)
    {
        $res = $this->location_repository->changeStateStatus($request->id);
        if ($res == true) {
            toastNotification('success', translate('Status updated successfully'), 'Success');
        } else {
            toastNotification('error', translate('Action failed'), 'Failed');
        }
    }
    /**
     * Will redirect state edit page
     * 
     * @param Int $id
     * @return mixed
     */
    public function editState($id)
    {
        return view('plugin/tlecommercecore::shipping.locations.state.edit')->with(
            [
                'countries' => $this->location_repository->countries(),
                'stateDetails' => $this->location_repository->stateDetails($id)
            ]
        );
    }
    /**
     * will update state
     * 
     * @param StateRequest $request
     * @return mixed
     */
    public function updateState(StateRequest $request)
    {
        $res = $this->location_repository->updateState($request);
        if ($res == true) {
            toastNotification('success', translate('State updated successfully'), 'Success');
            return redirect()->route('plugin.tlcommercecore.shipping.locations.states.edit', ['id' => $request['id'], 'lang' => $request['lang']]);
        } else {
            toastNotification('error', translate('Action failed'), 'Failed');
            return redirect()->back();
        }
    }
    /**
     * Will return cities list
     * 
     * @return mixed
     */
    public function cities(Request $request)
    {
        return view('plugin/tlecommercecore::shipping.locations.cities.index')->with(
            [
                'cities' => $this->location_repository->citiesList($request)
            ]
        );
    }
    /**
     * Will redirect new city page
     * 
     * @return mixed
     */
    public function newCity()
    {
        return view('plugin/tlecommercecore::shipping.locations.cities.add_new')->with(
            [
                'states' => $this->location_repository->states([config('settings.general_status.active')])
            ]
        );
    }
    /**
     * Will store new city information
     * 
     * @param CitiesRequest $request
     * @return mixed
     */
    public function storeNewCity(CitiesRequest $request)
    {
        $res = $this->location_repository->storeCity($request);
        if ($res == true) {
            toastNotification('success', translate('City added successfully'), 'Success');
            return redirect()->route('plugin.tlcommercecore.shipping.locations.cities.list');
        } else {
            toastNotification('error', translate('Action failed'), 'Failed');
            return redirect()->back();
        }
    }
    /**
     * Will delete city
     * 
     * @param \Illuminate\Http\Request $request
     * @return mixed
     */
    public function deleteCity(Request $request)
    {
        $res = $this->location_repository->deleteCity($request->id);
        if ($res == true) {
            toastNotification('success', translate('City deleted successfully'), 'Success');
            return redirect()->route('plugin.tlcommercecore.shipping.locations.cities.list');
        } else {
            toastNotification('error', translate('Action failed'), 'Failed');
            return redirect()->back();
        }
    }
    /**
     * Will change city's status
     * 
     * @param \Illuminate\Http\Request $request
     * @return mixed
     */
    public function changeCityStatus(Request $request)
    {
        $res = $this->location_repository->changeCityStatus($request->id);
        if ($res == true) {
            toastNotification('success', translate('Status updated successfully'), 'Success');
        } else {
            toastNotification('error', translate('Action failed'), 'Failed');
        }
    }
    /**
     * Will redirect edit city page
     * 
     * @param Int $id
     * @return mixed
     */
    public function editCity($id)
    {
        return view('plugin/tlecommercecore::shipping.locations.cities.edit')->with(
            [
                'states' => $this->location_repository->states([config('settings.general_status.active')]),
                'city_details' => $this->location_repository->cityDetails($id)
            ]
        );
    }
    /**
     * will update city
     * 
     * @param CitiesRequest $request
     * @return mixed
     */
    public function updateCity(CitiesRequest $request)
    {
        $res = $this->location_repository->updateCity($request);
        if ($res == true) {
            toastNotification('success', translate('City updated successfully'), 'Success');
            return redirect()->route('plugin.tlcommercecore.shipping.locations.cities.edit', ['id' => $request['id'], 'lang' => $request['lang']]);
        } else {
            toastNotification('error', translate('Action failed'), 'Failed');
            return redirect()->back();
        }
    }

    /**
     * Will fire country bulk actions
     * 
     * @param \Illuminate\Http\Request $request
     */
    public function countryBulkActions(Request $request)
    {
        $res = $this->location_repository->countryBulkAction($request);
        return response()->json(
            [
                'success' => $res,
            ]
        );
    }

    /**
     * Will fire states bulk actions
     * 
     * @param \Illuminate\Http\Request $request
     */
    public function stateBulkActions(Request $request)
    {
        $res = $this->location_repository->statesBulkAction($request);
        return response()->json(
            [
                'success' => $res,
            ]
        );
    }

    /**
     * Will fire cities bulk actions
     * 
     * @param \Illuminate\Http\Request $request
     */
    public function cityBulkActions(Request $request)
    {
        $res = $this->location_repository->citiesBulkAction($request);
        return response()->json(
            [
                'success' => $res,
            ]
        );
    }
}
