<?php

namespace Plugin\Refund\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Plugin\Refund\Models\OrderReturnRequest;
use Plugin\Refund\Repositories\RefundRepository;
use Plugin\Refund\Http\Requests\RefundReasonRequest;

class RefundController extends Controller
{
    protected $refund_repository;

    public function __construct(RefundRepository $refund_repository)
    {
        isActiveParentPlugin('tlecommercecore');

        $this->refund_repository = $refund_repository;
    }

    /**
     * Will return refund reason list
     * 
     * @return mixed
     */
    public function reasons()
    {
        return view('plugin/refund::refunds.reasons')->with(
            [
                'reasons' => $this->refund_repository->reasonList()
            ]
        );
    }
    /**
     * Will store new refund reason 
     * 
     * @param RefundReasonRequest $request
     * @return mixed
     */
    public function storeReason(RefundReasonRequest $request)
    {
        $res = $this->refund_repository->storeRefundReason($request);
        if ($res == true) {
            toastNotification('success', translate('New reason added successfully'), 'Success');
            return redirect()->route('plugin.refund.reasons.list');
        } else {
            toastNotification('error', translate('Action failed'), 'Failed');
            return redirect()->back();
        }
    }
    /**
     * Will delete product refund reason
     * 
     * @param \Illuminate\Http\Request $request
     * @return mixed
     */
    public function deleteReason(Request $request)
    {
        $res = $this->refund_repository->deleteReason($request->id);
        if ($res == true) {
            toastNotification('success', translate('Reason deleted successfully'), 'Success');
            return redirect()->route('plugin.refund.reasons.list');
        } else {
            toastNotification('error', translate('Action failed'), 'Failed');
            return redirect()->back();
        }
    }
    /**
     * Will delete bulk  reasons
     * 
     * @param \Illuminate\Http\Request $request
     * @return void
     */
    public function deleteBulkReason(Request $request)
    {
        $res = $this->refund_repository->deleteBulkReason($request);
        if ($res == true) {
            toastNotification('success', translate('Selected items deleted successfully'), 'Success');
        } else {
            toastNotification('error', translate('Action failed'), 'Failed');
        }
    }
    /**
     * Will change reason status
     * 
     * @param \Illuminate\Http\Request $request
     * @return mixed
     */
    public function changeReasonStatus(Request $request)
    {
        $res = $this->refund_repository->changeReasonStatus($request->id);
        if ($res == true) {
            toastNotification('success', translate('Status updated successfully'), 'Success');
        } else {
            toastNotification('error', translate('Action failed'), 'Failed');
        }
    }
    /**
     * Will return reason  details
     * 
     * @param \Illuminate\Http\Request $request
     * @param Int $id
     * @return mixed
     */
    public function editReason($id, Request $request)
    {

        return view('plugin/refund::refunds.edit_reason')->with(
            [
                'reason' => $this->refund_repository->reasonDetails($id),
                'lang' => $request->lang,
                'languages' => getAllLanguages(),
            ]
        );
    }
    /**
     * Will update refund reason
     * 
     * @param RefundReasonRequest $request
     * @return mixed
     */
    public function updateReason(RefundReasonRequest $request)
    {
        $res = $this->refund_repository->updateRefundReason($request);
        if ($res == true) {
            toastNotification('success', translate('Reason updated successfully'), 'Success');
            return redirect()->route('plugin.refund.reasons.list');
        } else {
            toastNotification('error', translate('Action failed'), 'Failed');
            return redirect()->back();
        }
    }
    /**
     * Will return refund requests
     * 
     * 
     * @param \Illuminate\Http\Request $request
     * @return mixed
     */
    public function refundRequests(Request $request)
    {
        $refund_request_list = $this->refund_repository->refundsRequestsList($request);
        return view('plugin/refund::refunds.requests')->with(
            [
                'refund_request_list' => $refund_request_list
            ]
        );
    }
    /**
     * Will return request view
     * @param \Illuminate\Http\Request $request
     * @return mixed
     */
    public function refundRequestQuickView(Request $request)
    {
        $refund_request = OrderReturnRequest::findOrFail($request['id']);
        return view('plugin/refund::refunds.quick_view')->with(
            [
                'details' => $refund_request,
                'action' => $request['action']
            ]
        );
    }
    /**
     * Will return refund request details
     * 
     * @param Int $id
     * @return mixed
     */
    public function refundRequestDetails($id)
    {
        $refund_request = OrderReturnRequest::findOrFail($id);
        return view('plugin/refund::refunds.refund_details')->with(
            [
                'details' => $refund_request
            ]
        );
    }
    /**
     * Will update refunds request status
     * 
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateRefundRequestStatus(Request $request)
    {
        $res = $this->refund_repository->updateReturnRequestStatus($request);

        if ($res) {
            return response()->json(
                [
                    'success' => true
                ]
            );
        } else {
            return response()->json(
                [
                    'success' => false
                ]
            );
        }
    }
}
