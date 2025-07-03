<?php

namespace Plugin\TlcommerceCore\Http\Controllers\Payment;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;
use Plugin\TlcommerceCore\Http\Controllers\Payment\PaymentController;

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

        $this->currency = \Plugin\TlcommerceCore\Repositories\PaymentMethodRepository::configKeyValue(config('tlecommercecore.payment_methods.paymob'), 'paymob_currency');
        $this->total_payable_amount = (new PaymentController())->convertCurrency($this->currency, session()->get('payable_amount'));
        $this->apiKey = \Plugin\TlcommerceCore\Repositories\PaymentMethodRepository::configKeyValue(config('tlecommercecore.payment_methods.paymob'), 'paymob_api_key');
        $this->integrationId = \Plugin\TlcommerceCore\Repositories\PaymentMethodRepository::configKeyValue(config('tlecommercecore.payment_methods.paymob'), 'paymob_integration_id');
        $this->hmacSecret = \Plugin\TlcommerceCore\Repositories\PaymentMethodRepository::configKeyValue(config('tlecommercecore.payment_methods.paymob'), 'paymob_hmac_secret');
        $this->iframe_key = \Plugin\TlcommerceCore\Repositories\PaymentMethodRepository::configKeyValue(config('tlecommercecore.payment_methods.paymob'), 'paymob_iframe_key');
    }

    public function index()
    {
        $amountCents = $this->total_payable_amount * 100; // Paymob requires amount in cents
        $amountCents = (int)$amountCents;
        $authToken = $this->authenticate();
        $orderId = $this->createOrder($authToken, $amountCents);
        $paymentKey = $this->getPaymentKey($authToken, $orderId, $amountCents);

        return view('plugin/tlecommercecore::payments.gateways.paymob.index', [
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


    //Authentication to obtain token
    public function authenticate()
    {
        $response = Http::post('https://accept.paymob.com/api/auth/tokens', [
            'api_key' => $this->apiKey,
        ]);

        return $response->json()['token'];
    }

    //Order Registration
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

    //Payment Key Request
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
            'currency' => $this->currency,
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

    protected function validateHMAC($data)
    {
        $computedHMAC = hash_hmac('sha512', implode('', array_values($data)), $this->hmacSecret);

        return $computedHMAC === $data['hmac'];
    }
}
