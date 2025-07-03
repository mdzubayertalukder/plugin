<?php

namespace Plugin\Multivendor\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Plugin\Multivendor\Repositories\SellerEarningRepository;

class PaymentController extends Controller
{

    public function __construct(public SellerEarningRepository $sellerEarningRepository)
    {
    }

    /**
     * Will return payout requests
     * 
     * @param \Illuminate\Http\Request $request
     */
    public function payoutRequest(Request $request)
    {
        $payout_requests = $this->sellerEarningRepository->allPayoutRequests($request);
        return view('plugin/multivendor::admin.earning.seller_payout_requests', ['payout_requests' => $payout_requests]);
    }
    /**
     * Will return payouts
     * 
     * @param \Illuminate\Http\Request $request
     */
    public function payouts(Request $request)
    {
        $payouts = $this->sellerEarningRepository->allPayouts($request);
        return view('plugin/multivendor::admin.earning.seller_payouts', ['payouts' => $payouts]);
    }

    /**
     * Will return seller earnings history
     * 
     * @param \Illuminate\Http\Request $request
     */
    public function sellerEarnings(Request $request)
    {
        $earnings = $this->sellerEarningRepository->sellerEarningHistory($request);
        return view('plugin/multivendor::admin.earning.history', ['earnings' => $earnings]);
    }
    /**
     * Will return seller payout request details
     * 
     * @param \Illuminate\Http\Request $request
     * @return JsonResponse
     */
    public function payoutRequestDetails(Request $request)
    {
        $request_details = $this->sellerEarningRepository->payoutRequestDetails($request['id']);

        return response()->json(
            [
                'success' => true,
                'data' => view('plugin/multivendor::admin.earning.seller_payout_requests_details', ['request_details' => $request_details])->render()
            ]
        );
    }
    /**
     * Will update payout request status
     * 
     */
    public function updatePayoutRequestStatus(Request $request)
    {
        $res = $this->sellerEarningRepository->updatePayoutRequestDetails($request);
        if ($res) {
            return response()->json(
                [
                    'success' => true,
                ]
            );
        } else {
            return response()->json(
                [
                    'success' => false,
                    'message' => translate('Payout status update failed')
                ]
            );
        }
    }
}
