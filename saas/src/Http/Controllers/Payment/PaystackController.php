<?php

namespace Plugin\Saas\Http\Controllers\Payment;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class PaystackController extends Controller
{
    protected $total_payable_amount;
    protected $secret_key;
    protected $currency = 'GHS';

    public function __construct()
    {
        $this->currency = \Plugin\Saas\Repositories\PaymentMethodRepository::configKeyValue(config('saas.payment_methods.paystack'), 'paystack_currency');
        $this->total_payable_amount = (new PaymentController())->convertCurrency($this->currency, session()->get('payable_amount'));
        $this->total_payable_amount = sprintf("%.2f", $this->total_payable_amount);
        $this->secret_key = \Plugin\Saas\Repositories\PaymentMethodRepository::configKeyValue(config('saas.payment_methods.paystack'), 'paystack_secret_key');
    }

    /**
     * Initial Paystack payment
     */
    public function index()
    {
        $post_data=[];
        $post_data['amount'] = $this->total_payable_amount * 100;
        $post_data['currency'] = $this->currency;
        $post_data['reference'] = uniqid('ref_');
        $post_data['callback_url'] = route('plugin.saas.pay.callback');
        $post_data['email'] = Auth::user()->email;

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->secret_key,
            'Content-Type' => 'application/json',
            'Accept' => 'application/json'
        ])->post('https://api.paystack.co/transaction/initialize', $post_data);
        
        if ($response->failed()) {
            // Handle error response from Paystack API
            return (new PaymentController)->payment_failed();
        }

        $data = $response->json();
        return redirect($data['data']['authorization_url']);
    }

    /**
     * Paystack callback url
     */
    public function callback(Request $request)
    {
        
        $reference = $request->input('reference');

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->secret_key,
            'Content-Type' => 'application/json',
            'Accept' => 'application/json'
        ])->get("https://api.paystack.co/transaction/verify/$reference");

        
        if ($response->failed()) {
            // Handle error response from Paystack API
            return (new PaymentController)->payment_failed();
        }

        $data = $response->json();

        if ($data['data']['status'] === 'success') {
            // Payment successful, update your database accordingly
            return (new PaymentController)->payment_success("Reference: ".$reference);
        } else {
            // Payment failed, update your database accordingly
            return (new PaymentController)->payment_failed();
        }
    }
}
