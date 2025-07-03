<?php

namespace Plugin\TlcommerceCore\Http\Controllers\Payment;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Plugin\TlcommerceCore\Http\Controllers\Payment\PaymentController;

class BkashController extends Controller
{
    protected $total_payable_amount;
    protected $app_key;
    protected $app_secret;
    protected $username;
    protected $password;
    protected $currency = "BDT";
    protected $base_url;
    protected $sandbox_url = 'https://tokenized.sandbox.bka.sh/v1.2.0-beta';
    protected $live_url = 'https://tokenized.pay.bka.sh/v1.2.0-beta';

    public function setCredentials()
    {
        $this->currency = \Plugin\TlcommerceCore\Repositories\PaymentMethodRepository::configKeyValue(config('tlecommercecore.payment_methods.bkash'), 'bkash_currency');
        $this->app_key = \Plugin\TlcommerceCore\Repositories\PaymentMethodRepository::configKeyValue(config('tlecommercecore.payment_methods.bkash'), 'bkash_app_key');
        $this->app_secret = \Plugin\TlcommerceCore\Repositories\PaymentMethodRepository::configKeyValue(config('tlecommercecore.payment_methods.bkash'), 'bkash_app_secret');
        $this->username = \Plugin\TlcommerceCore\Repositories\PaymentMethodRepository::configKeyValue(config('tlecommercecore.payment_methods.bkash'), 'bkash_username');
        $this->password = \Plugin\TlcommerceCore\Repositories\PaymentMethodRepository::configKeyValue(config('tlecommercecore.payment_methods.bkash'), 'bkash_password');

        $sandbox = \Plugin\TlcommerceCore\Repositories\PaymentMethodRepository::configKeyValue(config('tlecommercecore.payment_methods.bkash'), 'sandbox');
        $this->base_url = $sandbox == '1' ? $this->sandbox_url : $this->live_url;
    }

    /**
     * Initial bKash payment
     */
    public function index()
    {
        $this->setCredentials();
        $this->total_payable_amount = (new PaymentController())->convertCurrency($this->currency, session()->get('payable_amount'));

        $data = [
            'currency' => $this->currency,
            'total_payable_amount' => number_format($this->total_payable_amount, 2, '.', ''),
            'app_key' => $this->app_key,
            'base_url' => $this->base_url,
        ];

        return view('plugin/tlecommercecore::payments.gateways.bkash.index', $data);
    }

    /**
     * Get bKash token
     */
    public function getToken()
    {
        $this->setCredentials();

        // Validate credentials
        if (empty($this->app_key) || empty($this->app_secret) || empty($this->username) || empty($this->password)) {
            return response()->json([
                'success' => false,
                'message' => 'bKash credentials not configured. Please configure App Key, App Secret, Username and Password in payment settings.',
            ]);
        }

        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'username' => $this->username,
                'password' => $this->password,
            ])->post($this->base_url . '/tokenized/checkout/token/grant', [
                'app_key' => $this->app_key,
                'app_secret' => $this->app_secret,
            ]);

            \Illuminate\Support\Facades\Log::info('bKash Token Request', [
                'url' => $this->base_url . '/tokenized/checkout/token/grant',
                'app_key' => $this->app_key,
                'username' => $this->username,
                'response_status' => $response->status(),
                'response_body' => $response->body(),
            ]);

            if ($response->successful()) {
                $data = $response->json();
                if (isset($data['id_token'])) {
                    return response()->json([
                        'success' => true,
                        'token' => $data['id_token'],
                    ]);
                } else {
                    return response()->json([
                        'success' => false,
                        'message' => 'Token not found in response. Response: ' . json_encode($data),
                    ]);
                }
            }

            $errorMessage = 'HTTP ' . $response->status();
            $responseData = $response->json();

            if (isset($responseData['errorMessage'])) {
                $errorMessage .= ': ' . $responseData['errorMessage'];
            } elseif (isset($responseData['message'])) {
                $errorMessage .= ': ' . $responseData['message'];
            } else {
                $errorMessage .= ': ' . $response->body();
            }

            return response()->json([
                'success' => false,
                'message' => $errorMessage,
            ]);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('bKash Token Generation Error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Connection error: ' . $e->getMessage(),
            ]);
        }
    }

    /**
     * Create bKash payment
     */
    public function createPayment(Request $request)
    {
        $this->setCredentials();
        $this->total_payable_amount = (new PaymentController())->convertCurrency($this->currency, session()->get('payable_amount'));

        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'authorization' => $request->token,
                'x-app-key' => $this->app_key,
            ])->post($this->base_url . '/tokenized/checkout/create', [
                'mode' => '0011',
                'payerReference' => 'Payment-' . time(),
                'callbackURL' => route('bkash.callback'),
                'amount' => number_format($this->total_payable_amount, 2, '.', ''),
                'currency' => $this->currency,
                'intent' => 'sale',
                'merchantInvoiceNumber' => 'Invoice-' . time(),
            ]);

            if ($response->successful()) {
                $data = $response->json();
                return response()->json([
                    'success' => true,
                    'data' => $data,
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Failed to create payment',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Execute bKash payment
     */
    public function executePayment(Request $request)
    {
        $this->setCredentials();

        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'authorization' => $request->token,
                'x-app-key' => $this->app_key,
            ])->post($this->base_url . '/tokenized/checkout/execute', [
                'paymentID' => $request->paymentID,
            ]);

            if ($response->successful()) {
                $data = $response->json();

                if (isset($data['transactionStatus']) && $data['transactionStatus'] === 'Completed') {
                    // Payment successful - store transaction details
                    session()->put('bkash_transaction_id', $data['trxID']);
                    session()->put('bkash_payment_id', $data['paymentID']);

                    return response()->json([
                        'success' => true,
                        'data' => $data,
                        'redirect_url' => route('bkash.success.payment')
                    ]);
                }
            }

            return response()->json([
                'success' => false,
                'message' => 'Payment execution failed',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ]);
        }
    }

    /**
     * bKash payment callback
     */
    public function callback(Request $request)
    {
        if ($request->has('status') && $request->status === 'success') {
            return redirect()->route('bkash.success.payment');
        }

        return redirect()->route('bkash.cancel.payment');
    }

    /**
     * Success bKash payment
     */
    public function success()
    {
        try {
            return (new PaymentController)->payment_success();
        } catch (\Exception $e) {
            return (new PaymentController)->payment_failed();
        }
    }

    /**
     * Cancel bKash payment
     */
    public function cancel()
    {
        return (new PaymentController)->payment_failed();
    }
}
