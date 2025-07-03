<?php

namespace Plugin\Multivendor\Http\Controllers\Seller;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Validation\ValidationException;
use Plugin\Multivendor\Http\Requests\PayoutSettingRequest;
use Plugin\Multivendor\Repositories\SellerEarningRepository;
use Plugin\Multivendor\Http\Requests\SellerPayoutFormRequest;

class EarningController extends Controller
{

    public function __construct(public SellerEarningRepository $sellerEarningRepository)
    {
    }

    /**
     * Will return Seller payout requests
     * 
     * @param \Illuminate\Http\Request $request
     */
    public function payoutRequests(Request $request)
    {
        $payout_requests = $this->sellerEarningRepository->sellerPayoutRequests($request, auth()->user()->id);
        return view('plugin/multivendor::seller.dashboard.pages.earning.payout_requests', ['payout_requests' => $payout_requests]);
    }

    /**
     * Will return seller payouts
     * 
     * @param \Illuminate\Http\Request $request
     */
    public function payouts(Request $request)
    {
        $payouts = $this->sellerEarningRepository->sellerPayouts($request, auth()->user()->id);
        return view('plugin/multivendor::seller.dashboard.pages.earning.payouts', ['payouts' => $payouts]);
    }
    /**
     * Will send payout request
     * 
     * @param SellerPayoutFormRequest $request
     * 
     */
    public function payoutRequestsSend(SellerPayoutFormRequest $request)
    {
        if ($request['amount'] > auth()->user()->sellerWithdrawableBalance()) {

            throw ValidationException::withMessages(
                [
                    'amount' => [translate('You can not request more than your  available balance')]
                ]
            );
        }

        if (getGeneralSetting('seller_min_withdrawal_amount') != null && $request['amount'] < getGeneralSetting('seller_min_withdrawal_amount')) {
            throw ValidationException::withMessages(
                [
                    'amount' => [translate('Minimum withdrawable amount is') . ' ' . currencyExchange(getGeneralSetting('seller_min_withdrawal_amount'))]
                ]
            );
        }

        $res = $this->sellerEarningRepository->storeSellerPayoutRequest($request, auth()->user()->id);
        if ($res) {
            return response()->json([
                'success' => true
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => translate('Something went wrong. Please try again')
            ]);
        }
    }
    /**
     * Will return payout settings page
     */
    public function payoutSettings()
    {
        $payout_info = $this->sellerEarningRepository->sellerPayoutInfo(auth()->user()->id);
        return view('plugin/multivendor::seller.dashboard.pages.earning.payout_info', ['payout_info' => $payout_info]);
    }

    /**
     * Will update seller payout settings
     * 
     * @param PayoutSettingRequest $request
     */
    public function updatePayoutSettings(PayoutSettingRequest $request)
    {
        $res = $this->sellerEarningRepository->updateSellerPayoutInfo($request, auth()->user()->id);
        if ($res) {
            toastNotification('success', 'Payout updated successfully');
            return redirect()->route('plugin.multivendor.seller.dashboard.earning.payout.settings');
        } else {
            toastNotification('error', 'Payout update failed');
            return redirect()->back();
        }
    }

    /**
     * Will return seller earning history
     * 
     * @param \Illuminate\Http\Request $request
     */
    public function sellerEarnings(Request $request)
    {
        $earnings = $this->sellerEarningRepository->sellerEarningHistory($request, auth()->user()->id);
        return view('plugin/multivendor::seller.dashboard.pages.earning.history', ['earnings' => $earnings]);
    }
}
