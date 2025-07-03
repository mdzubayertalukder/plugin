<?php

namespace Plugin\TlcommerceCore\Http\Controllers\Payment;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Support\Facades\Http;

class RazorpayController extends Controller
{
    protected $total_payable_amount;
    protected $key_id;
    protected $key_secret;
    protected $currency = 'INR';

    public function setcredentials()
    {
        $this->currency = \Plugin\TlcommerceCore\Repositories\PaymentMethodRepository::configKeyValue(config('tlecommercecore.payment_methods.razorpay'), 'razorpay_currency');
        $this->key_id = \Plugin\TlcommerceCore\Repositories\PaymentMethodRepository::configKeyValue(config('tlecommercecore.payment_methods.razorpay'), 'razorpay_key_id');
        $this->key_secret = \Plugin\TlcommerceCore\Repositories\PaymentMethodRepository::configKeyValue(config('tlecommercecore.payment_methods.razorpay'), 'razorpay_key_secret');
    }

    /**
     * Initiate payment with razorpay
     */
    public function index()
    {
        try {
            $this->setcredentials();
            $this->total_payable_amount = (new PaymentController())->convertCurrency($this->currency, session()->get('payable_amount'));
            // Create the order with Razorpay
            $response = Http::withBasicAuth($this->key_id, $this->key_secret)
                ->post('https://api.razorpay.com/v1/orders', [
                    'amount' => $this->total_payable_amount * 100, // Amount in paisa
                    'currency' => $this->currency,
                    'receipt' => 'receipt_' . time(),
                    'payment_capture' => 1
                ])->throw();

            // Get the order details from the response
            $orderId = $response['id'];
            $orderAmount = $response['amount'];
            $orderCurrency = $response['currency'];

            // Generate the signature
            $signature = hash_hmac('sha256', $orderId . '|' . $orderAmount . '|' . $orderCurrency, $this->key_secret);

            // Pass the required data to the view
            return view('plugin/tlecommercecore::payments.gateways.razorpay.index', [
                'key_id' => $this->key_id,
                'order' => [
                    'id' => $orderId,
                    'amount' => $orderAmount,
                    'currency' => $orderCurrency,
                    'signature' => $signature,
                ],
            ]);
        } catch (Exception $ex) {
            return (new PaymentController)->payment_failed();
        }
    }

    /**
     * Will handle razorpay payment status
     */
    public function paymentStatus(Request $request)
    {
        try {
            $this->setcredentials();
            $paymentId = $request->input('razorpay_payment_id');
            $response = Http::withHeaders([
                'Authorization' => 'Basic ' . base64_encode($this->key_id . ':' . $this->key_secret),
                'Content-Type' => 'application/json'
            ])->get("https://api.razorpay.com/v1/payments/$paymentId");

            $payment = $response->json();

            if ($payment['status'] == 'authorized') {
                // Payment authorized, process the order
                return (new PaymentController)->payment_success("Payment-ID " . $paymentId);
            } else {
                // Payment failed, show error message
                return (new PaymentController)->payment_failed();
            }
        } catch (Exception $ex) {
            return (new PaymentController)->payment_failed();
        }
    }
}
