<?php

namespace Plugin\Carrier\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Plugin\Carrier\Http\Requests\CarrierRequest;
use Plugin\Carrier\Repositories\CarrierRepository;

class CarrierController extends Controller
{
    protected $carrier_repository;

    public function __construct(CarrierRepository $carrier_repository)
    {
        isActiveParentPlugin('tlecommercecore');

        $this->carrier_repository = $carrier_repository;
    }
    /**
     * Will return carrier list
     * 
     * @return mixed
     */
    public function carriers()
    {
        return view('plugin/carrier::pages.index')->with(
            [
                'couriers' => $this->carrier_repository->couriers()
            ]
        );
    }
    /**
     * Will store new courier service
     * 
     * @param CarrierRequest $request
     * @return void
     */
    public function storeNewCourier(CarrierRequest $request)
    {
        $res = $this->carrier_repository->storeNewCourier($request);
        if ($res == true) {
            toastNotification('success', translate('New courier added successfully'), 'Success');
        } else {
            toastNotification('error', translate('Action failed'), 'Failed');
        }
    }
    /**
     * Will update courier status
     * 
     * @param \Illuminate\Http\Request $request
     * @return void
     */
    public function updateCourierStatus(Request $request)
    {
        $res = $this->carrier_repository->updateCourierStatus($request['id']);
        if ($res == true) {
            toastNotification('success', translate('Courier status updated successfully'), 'Success');
        } else {
            toastNotification('error', translate('Action failed'), 'Failed');
        }
    }
    /**
     * Will delete courier
     * 
     * @param \Illuminate\Http\Request $request
     * @return mixed
     */
    public function deleteCourier(Request $request)
    {
        $res = $this->carrier_repository->deleteCourier($request['id']);
        if ($res == true) {
            toastNotification('success', translate('Courier deleted successfully'), 'Success');
            return redirect()->route('plugin.carrier.list');
        } else {
            toastNotification('error', translate('Action failed'), 'Failed');
            return redirect()->back();
        }
    }
    /**
     * Will update courier module status
     * 
     * @param \Illuminate\Http\Request $request
     * @return mixed
     */
    public function courierModuleUpdateStatus(Request $request)
    {
        $res = $this->carrier_repository->enableDisableCourierModule();
        if ($res == true) {
            toastNotification('success', translate('Status updated successfully'), 'Success');
        } else {
            toastNotification('error', translate('Action failed'), 'Failed');
        }
    }
    /**
     * Wii return courier edit form
     * 
     * @param \Illuminate\Http\Request $request
     * @return mixed
     */
    public function editCourier(Request $request)
    {
        return view('plugin/carrier::pages.edit_courier')->with(
            [
                'courier_info' => $this->carrier_repository->courierDetails($request['id']),
            ]
        );
    }
    /**
     * Will update courier information
     * 
     * @param CarrierRequest $request
     * @return void
     */
    public function updateCourier(CarrierRequest $request)
    {
        $res = $this->carrier_repository->updateCourier($request);
        if ($res == true) {
            toastNotification('success', translate('Courier updated successfully'), 'Success');
        } else {
            toastNotification('error',translate('Action failed'), 'Failed');
        }
    }
}
