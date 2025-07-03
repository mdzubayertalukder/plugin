<?php

namespace Plugin\TlcommerceCore\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Plugin\TlcommerceCore\Http\Requests\UnitRequest;
use Plugin\TlcommerceCore\Repositories\UnitRepository;

class UnitController extends Controller
{

    protected $unit_repository;

    public function __construct(UnitRepository $unit_repository)
    {
        $this->unit_repository = $unit_repository;
    }

    /**
     * will return units list
     * 
     * @return mixed
     */
    public function units()
    {
        return view('plugin/tlecommercecore::products.units.index')->with(
            [
                'units' => $this->unit_repository->unitList()
            ]
        );
    }
    /**
     * Store unit
     * 
     * @param UnitRequest $request
     * @return mixed
     */
    public function storeUnit(UnitRequest $request)
    {
        $res = $this->unit_repository->storeUnit($request);
        if ($res == true) {
            toastNotification('success', translate('New unit added successfully'), 'Success');
            return redirect()->route('plugin.tlcommercecore.product.units.list');
        } else {
            toastNotification('error', translate('Unit store failed'), 'Failed');
            return redirect()->back();
        }
    }
    /**
     * Will delete unit
     * 
     * @param \Illuminate\Http\Request $request
     * @return mixed
     */
    public function deleteUnit(Request $request)
    {
        $res = $this->unit_repository->deleteUnit($request->id);
        if ($res == true) {
            toastNotification('success',  translate('Unit deleted successfully'), 'Success');
            return redirect()->route('plugin.tlcommercecore.product.units.list');
        } else {
            toastNotification('error', translate('Unable to delete this unit'), 'Warning');
            return redirect()->back();
        }
    }
    /**
     * Will delete bulk unit
     * 
     * @param \Illuminate\Http\Request $request
     * @return void
     */
    public function deleteBulkUnit(Request $request)
    {
        $res = $this->unit_repository->deleteBulkUnit($request);
        if ($res == true) {
            toastNotification('success', translate('Selected items deleted successfully'), 'Success');
        } else {
            toastNotification('error', translate('Action failed'), 'Failed');
        }
    }
    /**
     * Wii redirect unit edit page
     * 
     * @return Int $id
     * @param Request $request
     * @return mixed
     */
    public function editUnit($id, Request $request)
    {
        return view('plugin/tlecommercecore::products.units.edit_unit')->with(
            [
                'unit_details' => $this->unit_repository->unitDetails($id),
                'lang' => $request->lang,
                'languages' => getAllLanguages()
            ]
        );
    }
    /**
     * will update unit
     * 
     * @param UnitRequest $request
     * @return mixed
     */
    public function updateUnit(UnitRequest $request)
    {
        $res = $this->unit_repository->updateUnit($request);
        if ($res == true) {
            toastNotification('success', translate('Unit updated successfully'), 'Success');
            return redirect()->route('plugin.tlcommercecore.product.units.list');
        } else {
            toastNotification('error', translate('Unit update failed'), 'Failed');
            return redirect()->back();
        }
    }
}
