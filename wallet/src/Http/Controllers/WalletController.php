<?php

namespace Plugin\Wallet\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Plugin\Wallet\Repositories\WalletRepository;
use Plugin\Wallet\Http\Requests\OfflinePaymentMethodRequest;
use Plugin\Wallet\Http\Requests\AdminWalletManualActionRequest;

class WalletController extends Controller
{
    protected $wallet_repository;

    public function __construct(WalletRepository $wallet_repository)
    {
        isActiveParentPlugin('tlecommercecore');

        $this->wallet_repository = $wallet_repository;
    }
    /**
     * Will return wallet recharges list
     * 
     * @return mixed
     */
    public function walletRecharges(Request $request)
    {
        $recharges = $this->wallet_repository->walletTransaction($request);
        return view('plugin/wallet::wallet.wallet_recharge')->with(
            [
                'recharges' => $recharges
            ]
        );
    }

    /**
     * Will return offline payment methods
     * 
     * @param \Illuminate\Http\Request $request
     * @return mixed
     */
    public function offlinePaymentMethods(Request $request)
    {
        $payment_methods = $this->wallet_repository->offlineMethods();
        return view('plugin/wallet::wallet.offline_payment_methods')->with(
            [
                'payment_methods' => $payment_methods
            ]
        );
    }
    /**
     * Will store offline payment method
     * 
     * @param OfflinePaymentMethodRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function storeOfflinePaymentMethod(OfflinePaymentMethodRequest $request)
    {
        $res = $this->wallet_repository->storeOfflinePayment($request);
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
    /**
     * Will delete payment method
     * 
     * @param \Illuminate\Http\Request $request
     * @return mixed
     * 
     */
    public function deleteOfflinePaymentMethod(Request $request)
    {
        $res = $this->wallet_repository->deletePaymentMethod($request['id']);
        if ($res) {
            toastNotification('success', translate('Payment method delete successfully'));
        } else {
            toastNotification('error', translate('Delete fail'));
        }

        return redirect()->back();
    }
    /**
     * Will store offline payment method
     * 
     * @param OfflinePaymentMethodRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateOfflinePaymentMethod(OfflinePaymentMethodRequest $request)
    {
        $res = $this->wallet_repository->updateOfflinePayment($request);
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

    /**
     * Will store customer manual wallet transactions
     * 
     * @param AdminWalletManualActionRequest $request 
     * @return mixed
     */
    public function addDeductCustomerWallet(AdminWalletManualActionRequest $request)
    {
        $data = [
            'entry_type' => $request['action'],
            'recharge_type' => config('tlecommercecore.wallet_recharge_type.manual'),
            'customer_id' => $request['customer_id'],
            'transaction_id' => null,
            'payment_method_id' => null,
            'recharge_amount' => $request['amount'],
            'document' => null,
            'added_by' => auth()->user()->id,
            'status' => config('tlecommercecore.wallet_transaction_status.accept'),
        ];

        $res = $this->wallet_repository->storeWalletTraction($data);
        if ($res) {
            toastNotification('success', translate('Customer wallet updated successfully'));
        } else {
            toastNotification('error', translate('Failed'));
        }

        return to_route('plugin.tlcommercecore.customers.details', ['id' => $request['customer_id']]);
    }

    /**
     * Will update status of wallet transaction with bulk
     * 
     * @param \Illuminate\Http\Request $request
     * @return void
     */
    public function walletBulkAction(Request $request)
    {
        $res = $this->wallet_repository->bulkStatusUpdate($request);
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
                ]
            );
        }
    }
}
