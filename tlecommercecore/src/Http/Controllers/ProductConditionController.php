<?php

namespace Plugin\TlcommerceCore\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Plugin\TlcommerceCore\Http\Requests\ProductConditionRequest;
use Plugin\TlcommerceCore\Repositories\ProductConditionRepository;

class ProductConditionController extends Controller
{

    protected $condition_repository;

    public function __construct(ProductConditionRepository $condition_repository)
    {
        $this->condition_repository = $condition_repository;
    }
    /**
     * Will return product conditions
     * 
     * @return mixed
     */
    public function conditions()
    {
        return view('plugin/tlecommercecore::products.conditions.index')->with(
            [
                'conditions' => $this->condition_repository->conditionList()
            ]
        );
    }
    /**
     * will store new product condition
     * 
     * @param ProductConditionRequest $request
     * @return mixed
     */
    public function storeCondition(ProductConditionRequest $request)
    {
        $res = $this->condition_repository->storeCondition($request);
        if ($res == true) {
            toastNotification('success', translate('New condition added successfully'), 'Success');
            return redirect()->route('plugin.tlcommercecore.product.conditions.list');
        } else {
            toastNotification('error', translate('Condition store failed'), 'Failed');
            return redirect()->back();
        }
    }
    /**
     * Will change condition status
     * 
     * @param \Illuminate\Http\Request $request
     * @return void
     */
    public function changeConditionStatus(Request $request)
    {
        $res = $this->condition_repository->changeStatus($request->id);
        if ($res == true) {
            toastNotification('success', translate('Status updated successfully'), 'Success');
        } else {
            toastNotification('error', translate('Unable to change status'), 'Failed');
        }
    }
    /**
     * Will delete product condition
     * 
     * @param \Illuminate\Http\Request $request
     * @return mixed
     */
    public function deleteCondition(Request $request)
    {
        $res = $this->condition_repository->deleteCondition($request->id);
        if ($res == true) {
            toastNotification('success', translate('Condition deleted successfully'), 'Success');
        } else {
            toastNotification('error', translate('Unable to delete'), 'Failed');
        }
        return redirect()->route('plugin.tlcommercecore.product.conditions.list');
    }
    /**
     * Will delete bulk product conditions
     * 
     * @param \Illuminate\Http\Request $request
     * @return void
     */
    public function deleteBulkCondition(Request $request)
    {
        $res = $this->condition_repository->deleteBulkCondition($request);
        if ($res == true) {
            toastNotification('success', translate('Selected items deleted successfully'), 'Success');
        } else {
            toastNotification('error', translate('Action Failed'), 'Failed');
        }
    }
    /**
     * Will redirect condition edit page
     * 
     * @param Int $id
     * @param \Illuminate\Http\Request $request
     * @return mixed
     */
    public function editCondition(Request $request, $id)
    {
        return view('plugin/tlecommercecore::products.conditions.edit_condition')->with(
            [
                'condition_details' => $this->condition_repository->conditionDetails($id),
                'lang' => $request->lang,
                'languages' => getAllLanguages()
            ]
        );
    }
    /**
     * Update product condition
     * 
     * @param ProductConditionRequest $request
     * @return mixed
     */
    public function updateCondition(ProductConditionRequest $request)
    {
        $res = $this->condition_repository->updateCondition($request);
        if ($res == true) {
            toastNotification('success', translate('Condition updated successfully'), 'Success');
        } else {
            toastNotification('error',translate( 'Unable to update'), 'Failed');
        }
        return redirect()->route('plugin.tlcommercecore.product.conditions.list');
    }
}
