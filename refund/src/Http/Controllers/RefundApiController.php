<?php

namespace Plugin\Refund\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Plugin\Refund\Models\OrderReturnRequest;
use Plugin\Refund\Repositories\RefundRepository;
use Plugin\Refund\Http\ApiResource\RefundReasonCollection;
use Plugin\Refund\Http\ApiResource\RefundRequestDetailsResource;

class RefundApiController extends Controller
{

    protected $refund_repository;

    public function __construct(RefundRepository $refund_repository)
    {
        isActiveParentPlugin('tlecommercecore');

        $this->refund_repository = $refund_repository;
    }
    /**
     * Will return refunds reason list
     * 
     * @param \Illuminate\Http\JsonResponse
     */
    public function refundsReasons()
    {
        $reasons = $this->refund_repository->reasonList([config('settings.general_status.active')]);
        return new RefundReasonCollection($reasons);
    }

    /**
     * Will return refund request details
     * 
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function refundRequestDetails(Request $request)
    {
        return new RefundRequestDetailsResource(OrderReturnRequest::find($request['id']));
    }
}
