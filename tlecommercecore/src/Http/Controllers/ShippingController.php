<?php

namespace Plugin\TlcommerceCore\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\View;
use Plugin\TlcommerceCore\Models\Country;
use Plugin\TlcommerceCore\Models\ShippingRate;
use Plugin\TlcommerceCore\Models\ShippingProfile;
use Plugin\TlcommerceCore\Models\ShippingProfileProducts;
use Plugin\TlcommerceCore\Repositories\ProductRepository;
use Plugin\TlcommerceCore\Repositories\LocationRepository;
use Plugin\TlcommerceCore\Repositories\SettingsRepository;
use Plugin\TlcommerceCore\Repositories\ShippingRepository;
use Plugin\TlcommerceCore\Http\Requests\ShippingRateRequest;
use Plugin\TlcommerceCore\Http\Requests\ShippingTimeRequest;
use Plugin\TlcommerceCore\Http\Requests\ShippingZoneRequest;
use Plugin\TlcommerceCore\Http\Requests\ShippingProfileRequest;
use Plugin\TlcommerceCore\Models\LocationWiseShippingRate;

class ShippingController extends Controller
{
    protected $shipping_repository;
    protected $product_repository;
    protected $location_repository;
    protected $settings_repository;

    public function __construct(SettingsRepository $settings_repository, ShippingRepository $shipping_repository, ProductRepository $product_repository, LocationRepository $location_repository)
    {
        $this->shipping_repository = $shipping_repository;
        $this->product_repository = $product_repository;
        $this->location_repository = $location_repository;
        $this->settings_repository = $settings_repository;
    }
    /**
     * Will return delivery and shipping configuration
     * 
     * @return mixed
     */
    public function shippingAndDelivery()
    {
        return view('plugin/tlecommercecore::shipping.configuration.index')->with(
            [
                'shippingTimes' => $this->shipping_repository->shippingTimes(),
                'shipping_profiles' => $this->shipping_repository->shippingProfiles(),
                'location_based_shipping_rates' => $this->shipping_repository->locationBasedShippingCosts(),
            ]
        );
    }

    /**
     * Will update shipping option
     * 
     * @param \Illuminate\Http\Request $request
     * @return RedirectResponse
     */
    public function updateShippingOption(Request $request)
    {
        $res = $this->settings_repository->updateShippingOption($request);
        if ($res) {
            toastNotification('success', 'Shipping option updated successfully');
        }
        if (!$res) {
            toastNotification('error', 'Shipping option update failed');
        }
        return  to_route('plugin.tlcommercecore.shipping.configuration');
    }
    /**
     * Will update shipping option
     * 
     * @param \Illuminate\Http\Request $request
     * @return RedirectResponse
     */
    public function updateFlatRateShipping(Request $request)
    {
        $res = $this->settings_repository->updateFlatRateShipping($request);
        if ($res) {
            toastNotification('success', 'Flat rate shipping cost updated successfully');
        }
        if (!$res) {
            toastNotification('error', 'Flat rate shipping cost update failed');
        }
        return  to_route('plugin.tlcommercecore.shipping.configuration');
    }

    public function storeNewLocationBasedShippingRate(Request $request)
    {
        $request->validate([
            'title' => 'required|max:250',
            'cost' => 'required|numeric'
        ]);

        $res = $this->shipping_repository->storeNewLocationBasedRate($request);
        if ($res == true) {
            toastNotification('success', translate('New rate created successfully'), 'Success');
        } else {
            toastNotification('error', translate('Action failed'), 'Failed');
        }
    }

    /**
     * Will update location wise shipping rate
     */
    public function updateLocationBasedShippingRate(Request $request)
    {
        $request->validate([
            'title' => 'required|max:250',
            'cost' => 'required|numeric'
        ]);

        $res = $this->shipping_repository->updateLocationBasedRate($request);
        if ($res == true) {
            toastNotification('success', translate('Shipping rate updated successfully'), 'Success');
        } else {
            toastNotification('error', translate('Action failed'), 'Failed');
        }
    }
    /**
     * Will delete location based rate
     */
    public function deleteLocationBasedShippingRate(Request $request)
    {
        $res = $this->shipping_repository->deleteLocationBasedShippingRate($request['id']);
        if ($res == true) {
            toastNotification('success', translate('Shipping rate deleted successfully'), 'Success');
            return redirect()->route('plugin.tlcommercecore.shipping.configuration');
        } else {
            toastNotification('error', translate('Unable to delete this shipping rate'), 'Failed');
            return redirect()->back();
        }
    }
    /**
     * Will delete Shipping Time
     * 
     * @param \Illuminate\Http\Request $request
     * @return mixed
     */
    public function deleteShippingTime(Request $request)
    {
        $res = $this->shipping_repository->deleteShippingTime($request['id']);
        if ($res == true) {
            toastNotification('success', translate('Shipping time deleted successfully'), 'Success');
            return redirect()->route('plugin.tlcommercecore.shipping.configuration');
        } else {
            toastNotification('error', translate('Unable to delete this delivery time'), 'warning');
            return redirect()->back();
        }
    }
    /**
     * Will Store new shipping time 
     * 
     * @param \Plugin\TlcommerceCore\Http\Requests\ShippingTimeRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function storeShippingTime(ShippingTimeRequest $request)
    {
        if ($this->shipping_repository->storeShippingTime($request)) {
            toastNotification('success', translate('New shipping time created successfully'));
        } else {
            toastNotification('success', translate('Failed create shipping time'));
        }
    }
    /**
     * Will return shipping profile form
     * 
     * @return mixed
     */
    public function shippingProfileForm()
    {
        return view('plugin/tlecommercecore::shipping.configuration.shipping_profile_form');
    }
    /**
     * Will store shipping profile
     * 
     * @param \Plugin\TlcommerceCore\Http\Requests\ShippingProfileRequest $request
     * @return mixed
     */
    public function storeShippingProfile(ShippingProfileRequest $request)
    {
        $res = $this->shipping_repository->storeShippingProfile($request);
        if ($res != null) {
            toastNotification('success', translate('New shipping profile created successfully'));
            return redirect()->route('plugin.tlcommercecore.shipping.profile.manage', $res);
        } else {
            toastNotification('error', translate('Action failed'));
            return redirect()->back();
        }
    }
    /**
     * Will return shipping profile details
     * 
     * @param Int $id
     * @return mixed
     */
    public function manageShippingProfile($id)
    {
        $couriers = [];
        if (isActivePluging('carrier')) {
            $couriers = \Plugin\Carrier\Models\ShippingCarrier::where('status', config('settings.general_status.active'))->get();
        }
        return view('plugin/tlecommercecore::shipping.configuration.manage_profile')->with(
            [
                'profile_info' => $this->shipping_repository->profileDetails($id),
                'shipping_time' => $this->shipping_repository->shippingTimes(),
                'couriers' => $couriers
            ]
        );
    }
    /**
     * This method will return shipping location  selection list
     * 
     * @param Request $request
     * @return Response 
     */
    public function locationUlList(Request $request)
    {
        try {
            $countries = Country::orderBy('id', 'ASC')
                ->where('status', config('settings.general_status.active'))
                ->select('name', 'id')
                ->paginate($request['perPage']);

            if ($request->has('type') && $request['type'] == 'location') {
                return response()->json(
                    [
                        'success' => true,
                        'list' => View::make('plugin/tlecommercecore::shipping.configuration.location-wise.location_options')->with(['countries' => $countries])->render(),
                    ]
                );
            }

            return response()->json(
                [
                    'success' => true,
                    'list' => View::make('plugin/tlecommercecore::shipping.configuration.location_options')->with(['countries' => $countries, 'profile_id' => $request['profile_id']])->render(),
                ]
            );
        } catch (\Exception $e) {
            return response()->json(
                [
                    'success' => false,
                ]
            );
        }
    }
    /**
     * This method will return shipping location  selection list
     * 
     * @param Request $request
     * @return Response 
     */
    public function locationUlListEdt(Request $request)
    {
        try {

            $countries = Country::where('status', config('settings.general_status.active'))
                ->orderByRaw("(
                    SELECT COUNT(*)
                    FROM tl_com_location_wise_shipping_rate_countries
                    WHERE country_id = tl_countries.id
                    AND rate_id = ?
                 ) DESC", [$request['rate_id']])
                ->orderBy('id', 'ASC')
                ->select('name', 'id')
                ->paginate($request['perPage']);

            if ($request->has('type') && $request['type'] == 'location') {
                return response()->json(
                    [
                        'success' => true,
                        'list' => View::make('plugin/tlecommercecore::shipping.configuration.location-wise.edit-location-option')->with(['countries' => $countries, 'rate_id' => $request['rate_id']])->render(),
                    ]
                );
            }


            $countries = Country::where('status', config('settings.general_status.active'))
                ->orderByRaw("(
                    SELECT COUNT(*)
                    FROM tl_com_shipping_zone_has_countries
                    WHERE country_id = tl_countries.id
                    AND zone_id = ?
                 ) DESC", [$request['zone_id']])
                ->orderBy('id', 'ASC')
                ->select('name', 'id')
                ->paginate($request['perPage']);

            return response()->json(
                [
                    'success' => true,
                    'list' => View::make('plugin/tlecommercecore::shipping.configuration.edit_zone_location_option')->with(['countries' => $countries, 'profile_id' => $request['profile_id'], 'zone_id' => $request['zone_id']])->render(),
                ]
            );
        } catch (\Exception $e) {
            return response()->json(
                [
                    'success' => false,
                ]
            );
        }
    }

    /**
     * This method will return searched shipping location list
     * 
     * @param Request $request
     * @return Response 
     */
    public function searchLocationUlList(Request $request)
    {
        try {
            $searchKey = $request['key'];
            $countries = Country::where('name', 'LIKE', "%$searchKey%")
                ->orWhereHas('states', function ($query) use ($searchKey) {
                    $query->where('name', 'LIKE', "%$searchKey%")
                        ->orWhereHas('cities', function ($query) use ($searchKey) {
                            $query->where('name', 'LIKE', "%$searchKey%");
                        });
                })
                ->orderBy('id', 'ASC')
                ->where('status', config('settings.general_status.active'))
                ->paginate($request['perPage']);

            if ($request->has('type') && $request['type'] == 'location') {
                return response()->json(
                    [
                        'success' => true,
                        'found' => count($countries) ? true : false,
                        'totalPage' => $countries->total(),
                        'list' => View::make('plugin/tlecommercecore::shipping.configuration.location-wise.location_options')->with(['countries' => $countries])->render(),
                    ]
                );
            }

            return response()->json(
                [
                    'success' => true,
                    'found' => count($countries) ? true : false,
                    'totalPage' => $countries->total(),
                    'list' => View::make('plugin/tlecommercecore::shipping.configuration.location_options')->with(['countries' => $countries, 'profile_id' => $request['profile_id']])->render(),
                ]
            );
        } catch (\Exception $e) {
            return response()->json(
                [
                    'success' => false,
                ]
            );
        }
    }
    /**
     * This method will return searched shipping location list for edit
     * 
     * @param Request $request
     * @return Response 
     */
    public function searchLocationUlListEdit(Request $request)
    {
        try {
            $searchKey = $request['key'];

            $countries = Country::where('name', 'LIKE', "%$searchKey%")
                ->orWhereHas('states', function ($query) use ($searchKey) {
                    $query->where('name', 'LIKE', "%$searchKey%")
                        ->orWhereHas('cities', function ($query) use ($searchKey) {
                            $query->where('name', 'LIKE', "%$searchKey%");
                        });
                })
                ->orderByRaw("(
                    SELECT COUNT(*)
                    FROM tl_com_location_wise_shipping_rate_countries
                    WHERE country_id = tl_countries.id
                    AND rate_id = ?
                 ) DESC", [$request['rate_id']])
                ->orderBy('id', 'ASC')
                ->select('name', 'id')
                ->paginate($request['perPage']);

            if ($request->has('type') && $request['type'] == 'location') {
                return response()->json(
                    [
                        'success' => true,
                        'found' => count($countries) ? true : false,
                        'totalPage' => $countries->total(),
                        'list' => View::make('plugin/tlecommercecore::shipping.configuration.location-wise.edit-location-option')->with(['countries' => $countries, 'rate_id' => $request['rate_id']])->render(),
                    ]
                );
            }

            $countries = Country::where('name', 'LIKE', "%$searchKey%")
                ->orWhereHas('states', function ($query) use ($searchKey) {
                    $query->where('name', 'LIKE', "%$searchKey%")
                        ->orWhereHas('cities', function ($query) use ($searchKey) {
                            $query->where('name', 'LIKE', "%$searchKey%");
                        });
                })
                ->orderByRaw("(
                    SELECT COUNT(*)
                    FROM tl_com_shipping_zone_has_countries
                    WHERE country_id = tl_countries.id
                    AND zone_id = ?
                 ) DESC", [$request['zone_id']])
                ->orderBy('id', 'ASC')
                ->paginate($request['perPage']);

            return response()->json(
                [
                    'success' => true,
                    'found' => count($countries) ? true : false,
                    'totalPage' => $countries->total(),
                    'list' => View::make('plugin/tlecommercecore::shipping.configuration.edit_zone_location_option')->with(['countries' => $countries, 'profile_id' => $request['profile_id'], 'zone_id' => $request['zone_id']])->render(),
                ]
            );
        } catch (\Exception $e) {
            return response()->json(
                [
                    'success' => false,
                ]
            );
        }
    }

    /**
     * Will store new zone
     * 
     * @param ShippingZoneRequest $request
     * @return mixed
     */
    public function storeNewShippingZone(ShippingZoneRequest $request)
    {
        $res = $this->shipping_repository->storeNewZone($request);
        if ($res == true) {
            toastNotification('success', translate('New zone created successfully'), 'Success');
        } else {
            toastNotification('error', translate('Action failed'), 'Failed');
        }
    }
    /**
     * Will return shipping zone edit form
     * 
     * @param \Illuminate\Http\Request $request
     * @return mixed
     */
    public function editShippingZone(Request $request)
    {
        //location based shipping rate
        if ($request->has('type') && $request['type'] == 'location') {
            $shippingRate = LocationWiseShippingRate::findOrFail($request['id']);
            return response()->json([
                'success' => true,
                'view' => view('plugin/tlecommercecore::shipping.configuration.location-wise.edit-shipping-rate', ['shipping_rate' => $shippingRate])->render()
            ], 200);
        }


        return view('plugin/tlecommercecore::shipping.configuration.edit_shipping_zone')->with(
            [
                'profile_info' => $this->shipping_repository->profileDetails($request['profile']),
                'zone_info' => $this->shipping_repository->shippingZoneDetails($request['id'])
            ]
        );
    }
    /**
     * Will update shipping zone
     * 
     * @param ShippingZoneRequest $request
     * @return void
     */
    public function updateShippingZone(ShippingZoneRequest $request)
    {
        $res = $this->shipping_repository->updateShippingZone($request);
        if ($res == true) {
            toastNotification('success', translate('Shipping zone successfully'), 'Success');
        } else {
            toastNotification('error', translate('Action failed'), 'Failed');
        }
    }
    /**
     * Will delete a zone
     * 
     * @param \Illuminate\Http\Request $request
     * @return mixed
     */
    public function deleteZone(Request $request)
    {
        $res = $this->shipping_repository->deleteZone($request['id']);
        if ($res == true) {
            toastNotification('success', translate('Zone deleted successfully'), 'Success');
            return redirect()->route('plugin.tlcommercecore.shipping.profile.manage', $request['profile_id']);
        } else {
            toastNotification('error', translate('Unable to delete this shipping zone'), 'warning');
            return redirect()->back();
        }
    }
    /**
     * Will store shipping rate
     * 
     * @param ShippingRateRequest $request
     * @return void
     */
    public function storeShippingRate(ShippingRateRequest $request)
    {
        $res = $this->shipping_repository->storeShippingRate($request);
        if ($res == true) {
            toastNotification('success', translate('New shipping rate created successfully'), 'Success');
        } else {
            toastNotification('error', translate('Action failed'), 'Failed');
        }
    }
    /**
     * Will delete shipping rate
     * 
     * @param \Illuminate\Http\Request $request
     * @return void
     */
    public function deleteShippingRate(Request $request)
    {
        try {
            $rate = ShippingRate::findOrFail($request['id']);
            $rate->delete();
            toastNotification('success', translate('Shipping rate deleted successfully'));
            return redirect()->route('plugin.tlcommercecore.shipping.profile.manage', $request['profile_id']);
        } catch (\Exception $e) {
            toastNotification('error', translate('Unable to delete shipping rate'));
            return redirect()->route('plugin.tlcommercecore.shipping.profile.manage', $request['profile_id']);
        } catch (\Error $e) {
            toastNotification('error', translate('Unable to delete shipping rate'));
            return redirect()->route('plugin.tlcommercecore.shipping.profile.manage', $request['profile_id']);
        }
    }
    /**
     * Wii return shipping rate edit form
     * 
     * @param \Illuminate\Http\Request $request
     * @return mixed
     */
    public function editShippingRate(Request $request)
    {
        $couriers = [];
        if (isActivePluging('carrier')) {
            $couriers = \Plugin\Carrier\Models\ShippingCarrier::where('status', config('settings.general_status.active'))->get();
        }
        $shippingRate = ShippingRate::findOrFail($request['id']);
        return view('plugin/tlecommercecore::shipping.configuration.edit_shipping_rate')->with(
            [
                'shippingRate' => $shippingRate,
                'shipping_time' => $this->shipping_repository->shippingTimes(),
                'couriers' => $couriers
            ]
        );
    }
    /**
     * Will update sipping rate
     * 
     * @param ShippingRateRequest $request
     * @return void
     */
    public function updateShippingRate(ShippingRateRequest $request)
    {
        $res = $this->shipping_repository->updateShippingRate($request);
        if ($res == true) {
            toastNotification('success', translate('Shipping rate updated successfully'), 'Success');
        } else {
            toastNotification('error', translate('Action failed'), 'Failed');
        }
    }
    /**
     * Will update shipping profile
     * 
     * @param ShippingProfileRequest $request
     * @return void
     */
    public function updateShippingProfile(ShippingProfileRequest $request)
    {
        try {
            DB::beginTransaction();
            $profile = ShippingProfile::findOrFail($request['profile_id']);
            $profile->name = $request['profile_name'];
            $profile->location = $request['location'];
            $profile->address = $request['address'];
            $profile->save();
            DB::commit();
            toastNotification('success', translate('Shipping profile updated successfully'));
        } catch (\Exception $e) {
            DB::rollBack();
            toastNotification('error', translate('Update failed'));
        } catch (\Error $e) {
            DB::rollBack();
            toastNotification('error', translate('Update failed'));
        }
    }
    /**
     * Will remove product from profile
     */
    public function removeProduct(Request $request)
    {
        try {
            DB::beginTransaction();
            ShippingProfileProducts::where('product_id', $request['id'])
                ->where('profile_id', $request['profile_id'])
                ->delete();
            DB::commit();
            toastNotification('success', 'Remove product successfully');
            return to_route('plugin.tlcommercecore.shipping.profile.manage', ['id' => $request['profile_id'], 'module' => 'products']);
        } catch (\Exception $e) {
            DB::rollBack();
            toastNotification('error', 'Remove product failed');
            return redirect()->back();
        }
    }
    /**
     * Will update shipping profile product list
     * 
     * @param \Illuminate\Http\Request $request
     * @return mixed
     */
    public function updateShippingProductList(Request $request)
    {
        try {
            DB::beginTransaction();
            $profile = ShippingProfile::findOrFail($request['profile_id']);
            if ($request->has('products')) {
                foreach ($request['products'] as $product) {
                    $profile_products = new ShippingProfileProducts;
                    $profile_products->product_id    = $product;
                    $profile_products->profile_id    = $request['profile_id'];
                    $profile_products->save();
                }
                toastNotification('success', translate('Product list updated successfully'));
            } else {
                toastNotification('error', translate('No item Selected'));
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            toastNotification('error', translate('Update failed'));
        } catch (\Error $e) {
            DB::rollBack();
            toastNotification('error', translate('Update failed'));
        }
    }
    /**
     * Will delete shipping profile
     * 
     * @param \Illuminate\Http\Request $request
     * @return mixed
     */
    public function deleteShippingProfile(Request $request)
    {
        $res = $this->shipping_repository->deleteShippingProfile($request['id']);
        if ($res == true) {
            toastNotification('success', translate('Shipping profile deleted successfully'), 'Success');
            return redirect()->route('plugin.tlcommercecore.shipping.configuration');
        } else {
            toastNotification('error', translate('Unable to delete this shipping profile'), 'Failed');
            return redirect()->back();
        }
    }
}
