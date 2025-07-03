<?php

namespace Plugin\Saas\Http\Controllers\Payment;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;
use Plugin\Saas\Http\Controllers\Payment\PaymentController;

class PaymobController extends Controller
{

    protected $apiKey;
    protected $integrationId;
    protected $hmacSecret;
    protected $total_payable_amount;
    protected $iframe_key;
    protected $currency = "EGP";

    public function __construct()
    {

        $this->currency = \Plugin\Saas\Repositories\PaymentMethodRepository::configKeyValue(config('saas.payment_methods.paymob'), 'paymob_currency');
        $this->total_payable_amount = (new PaymentController())->convertCurrency($this->currency, session()->get('payable_amount'));
        $this->apiKey = \Plugin\Saas\Repositories\PaymentMethodRepository::configKeyValue(config('saas.payment_methods.paymob'), 'paymob_api_key');
        $this->integrationId = \Plugin\Saas\Repositories\PaymentMethodRepository::configKeyValue(config('saas.payment_methods.paymob'), 'paymob_integration_id');
        $this->hmacSecret = \Plugin\Saas\Repositories\PaymentMethodRepository::configKeyValue(config('saas.payment_methods.paymob'), 'paymob_hmac_secret');
        $this->iframe_key = \Plugin\Saas\Repositories\PaymentMethodRepository::configKeyValue(config('saas.payment_methods.paymob'), 'paymob_iframe_key');
    }

    public function index()
    {
        $amountCents = $this->total_payable_amount * 100; // Paymob requires amount in cents
        $amountCents = (int)$amountCents;
        $authToken = $this->authenticate();
        $orderId = $this->createOrder($authToken, $amountCents);
        $paymentKey = $this->getPaymentKey($authToken, $orderId, $amountCents);

        return view('plugin/saas::payments.gateways.paymob.index', [
            'payment_key' => $paymentKey,
            'iframe_key' => $this->iframe_key
        ]);
    }

    public function callback(Request $request)
    {
        try {
            $transactionData = $request->all();

            if ($transactionData['success'] == 'true') {
                return (new PaymentController)->payment_success($transactionData['id']);
            } else {
                return (new PaymentController)->payment_failed();
            }
        } catch (\Exception $e) {
            return (new PaymentController)->payment_failed();
        }
    }


    // Step 1: Authentication to obtain token
    public function authenticate()
    {
        $response = Http::post('https://accept.paymob.com/api/auth/tokens', [
            'api_key' => $this->apiKey,
        ]);

        return $response->json()['token'];
    }

    // Step 2: Order Registration
    public function createOrder($token, $amountCents)
    {
        $response = Http::post('https://accept.paymob.com/api/ecommerce/orders', [
            'auth_token' => $token,
            'delivery_needed' => false,
            'amount_cents' => $amountCents,
            'currency' => $this->currency,
            'items' => [],
        ]);

        return $response->json()['id'];
    }

    // Step 3: Payment Key Request
    public function getPaymentKey($token, $orderId, $amountCents)
    {
        $response = Http::post('https://accept.paymob.com/api/acceptance/payment_keys', [
            'auth_token' => $token,
            'amount_cents' => $amountCents,
            'expiration' => 3600,
            'order_id' => $orderId,
            'billing_data' => [
                "apartment" => "NA",
                "email" => "user@example.com",
                "floor" => "NA",
                "first_name" => "First",
                "last_name" => "Last",
                "street" => "NA",
                "building" => "NA",
                "phone_number" => "0123456789",
                "postal_code" => "NA",
                "city" => "Cairo",
                "country" => "EG",
                "state" => "NA"
            ],
            'currency' => 'EGP',
            'integration_id' => $this->integrationId,
        ]);
        return $response->json()['token'];
    }

    public function verifyPayment($transactionId)
    {
        $authToken = $this->authenticate();

        $response = Http::get("https://accept.paymob.com/api/acceptance/transactions/{$transactionId}", [
            'auth_token' => $authToken,
        ]);

        return $response->json();
    }

    protected function validateHMAC($callbackData)
    {
        $hmacSecret = $this->hmacSecret;
        $dataString = implode('', [
            $callbackData['amount_cents'],
            $callbackData['created_at'],
            $callbackData['currency'],
            $callbackData['error_occured'],
            $callbackData['has_parent_transaction'],
            $callbackData['id'],
            $callbackData['integration_id'],
            $callbackData['is_3d_secure'],
            $callbackData['is_auth'],
            $callbackData['is_capture'],
            $callbackData['is_refunded'],
            $callbackData['is_standalone_payment'],
            $callbackData['is_voided'],
            $callbackData['order'],
            $callbackData['owner'],
            $callbackData['pending'],
            $callbackData['source_data_pan'],
            $callbackData['source_data_sub_type'],
            $callbackData['success'],
        ]);

        $computedHMAC = hash_hmac('sha512', $dataString, $hmacSecret);

        return $computedHMAC === $callbackData['hmac'];
    }
}
