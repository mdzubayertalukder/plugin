<?php

namespace Plugin\Multivendor\Http\Controllers\Seller;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Core\Exceptions\ThemeRequiredPluginException;
use Plugin\Multivendor\Repositories\SellerRepository;

class RefundController extends Controller
{
    public function __construct(public SellerRepository $sellerRepository)
    {
        if (!isActivePluging('refund')) {
            throw new ThemeRequiredPluginException('Refund is not available');
        }
    }


    /**
     * Will return refunds list
     * 
     * @param \Illuminate\Http\Request $request
     */
    public function refundList(Request $request)
    {

        $refund_request_list = $this->sellerRepository->refundsList($request, auth()->user()->id);
        return view('plugin/multivendor::seller.dashboard.pages.refunds.refunds_list', ['refund_request_list' => $refund_request_list]);
    }
    /**
     * Refund request quick view
     * 
     * @param \Illuminate\Http\Request $request
     */
    public function refundRequestQuickView(Request $request)
    {
        $refund_request = \Plugin\Refund\Models\OrderReturnRequest::findOrFail($request['id']);
        return view('plugin/multivendor::seller.dashboard.pages.refunds.quick_view')->with(
            [
                'details' => $refund_request,
                'action' => $request['action']
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
        $refund_repository = new \Plugin\Refund\Repositories\RefundRepository;
        $res = $refund_repository->updateReturnRequestStatus($request, auth()->user()->id);
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
