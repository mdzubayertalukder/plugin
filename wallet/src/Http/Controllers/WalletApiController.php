<?php

namespace Plugin\Wallet\Http\Controllers;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Plugin\TlcommerceCore\Models\Customers;
use Plugin\TlcommerceCore\Models\PaymentMethods;
use Plugin\Wallet\Repositories\WalletRepository;
use Plugin\Wallet\Http\Requests\OfflineRechargeRequest;
use Plugin\Wallet\Http\ApiResource\OfflinePaymentResource;
use Plugin\Wallet\Http\ApiResource\CustomerWalletTransactionResource;

class WalletApiController extends Controller
{
    protected $wallet_repository;

    public function __construct(WalletRepository $wallet_repository)
    {
        $this->wallet_repository = $wallet_repository;
    }

    /**
     * Will return payment methods
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function paymentMethods()
    {
        $offline_methods = new OfflinePaymentResource($this->wallet_repository->activeOfflinePaymentMethods());
        $online_methods = $this->wallet_repository->activeOnlinePaymentMethods();
        return response()->json(
            [
                'success' => true,
                'online_methods' => $online_methods,
                'offline_methods' => $offline_methods,
            ]
        );
    }
    /**
     * Will store offline payment
     * 
     * @param OfflineRechargeRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function storeOfflinePayment(OfflineRechargeRequest $request)
    {
        $transaction_image = null;
        if ($request->has('transaction_image') && $request['transaction_image']) {
            $transaction_image = saveFileInStorage($request['transaction_image'], false);
        }

        $amount = currencyExchange($request['recharge_amount'], false, $request['currency']);

        $data = [
            'entry_type' => config('tlecommercecore.wallet_entry_type.credit'),
            'recharge_type' => config('tlecommercecore.wallet_recharge_type.offline'),
            'customer_id' => auth()->user()->id,
            'transaction_id' => $request['transaction_id'],
            'payment_method_id' => $request['payment_method'],
            'recharge_amount' => $amount,
            'document' => $transaction_image,
            'added_by' => null,
            'status' => config('tlecommercecore.wallet_transaction_status.pending'),
        ];

        $res = $this->wallet_repository->storeWalletTraction($data);
        if ($res) {
            //Send notification to admin
            $customer = Customers::find(auth()->user()->id);
            $message = "Recharged wallet via offline";
            if ($customer != null) {
                $message = $customer->name . " recharged wallet via offline";
            }

            \Plugin\Wallet\Repositories\WalletNotification::sendCustomerWalletRechargeNotification($message);

            return response()->json(
                [
                    'success' => true,
                ]
            );
        } else {
            return response()->json([
                'success' => false,
            ]);
        }
    }

    public function onlineWalletRecharge(Request $request)
    {
        try {
            $amount = currencyExchange($request['recharge_amount'], false, $request['currency']);
            $base_url = url('/');
            $payment_method = PaymentMethods::find($request['payment_method']);
            if ($payment_method != null) {
                $url = $base_url . '/payment/' . Str::slug($payment_method->name) . '/pay';
                session()->put('payment_type', 'wallet_recharge');
                session()->put('customer', auth('jwt-customer')->user()->id);
                session()->put('payable_amount', $amount);
                session()->put('payment_method', $payment_method->name);
                session()->put('payment_method_id', $payment_method->id);
                session()->put('redirect_url', $url);
                return response()->json([
                    'success' => true,
                    'url' => $url
                ]);
            } else {
                return response()->json([
                    'success' => false
                ]);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false
            ]);
        }
    }

    /**
     * Will return customer wallet transaction
     * 
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function customerWalletTransaction(Request $request)
    {
        return new CustomerWalletTransactionResource($this->wallet_repository->customerWalletTransactions(auth()->user()->id, $request));
    }
    /**
     * Will return customer wallet summary
     */
    public function customerWalletSummary(Request $request)
    {
        $res = $this->wallet_repository->customerWalletSummary(auth()->user()->id);
        if ($res) {
            return response()->json(
                [
                    'success' => true,
                    'summary' => $res
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