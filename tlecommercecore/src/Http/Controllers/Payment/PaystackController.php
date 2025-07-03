<?php

namespace Plugin\TlcommerceCore\Http\Controllers\Payment;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;

class PaystackController extends Controller
{
    protected $total_payable_amount;
    protected $secret_key;
    protected $currency = 'GHS';

    public function setcredentials()
    {
        $this->currency = \Plugin\TlcommerceCore\Repositories\PaymentMethodRepository::configKeyValue(config('tlecommercecore.payment_methods.paystack'), 'paystack_currency');
        $this->secret_key = \Plugin\TlcommerceCore\Repositories\PaymentMethodRepository::configKeyValue(config('tlecommercecore.payment_methods.paystack'), 'paystack_secret_key');
    }

    /**
     * Initial Paystack payment
     */
    public function index()
    {
        try {
            $this->setcredentials();

            $post_data = [];
            $this->total_payable_amount = (new PaymentController())->convertCurrency($this->currency, session()->get('payable_amount'));
            $post_data['amount'] = $this->total_payable_amount * 100;
            $post_data['currency'] = $this->currency;
            $post_data['reference'] = uniqid('ref_');
            $post_data['callback_url'] = route('pay.callback');

            if (session()->get('payment_type') === 'checkout') {
                $email = "";
                $order_id = session()->get('order_id');

                $order_details = DB::table('tl_com_orders')
                    ->where('id', '=', $order_id)
                    ->first();

                if ($order_details != null) {
                    $customer_id = $order_details->customer_id;
                    if ($customer_id != null) {
                        $email = DB::table('tl_com_customers')
                            ->where('id', '=', $customer_id)
                            ->first()->email;
                    } else {
                        $email = DB::table('tl_com_guest_customer')
                            ->where('order_id', '=', $order_id)
                            ->first()->email;
                    }
                }
                $post_data['email'] = $email;
            }

            if (session()->get('payment_type') === 'wallet_recharge') {
                $email = DB::table('tl_com_customers')
                    ->where('id', '=', session()->get('customer'))
                    ->first()->email;
                $post_data['email'] = $email;
            }


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
        } catch (Exception $ex) {
            return (new PaymentController)->payment_failed();
        }
    }

    /**
     * Paystack callback url
     */
    public function callback(Request $request)
    {
        try {
            $this->setcredentials();
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
                return (new PaymentController)->payment_success("Reference: " . $reference);
            } else {
                // Payment failed, update your database accordingly
                return (new PaymentController)->payment_failed();
            }
        } catch (Exception $ex) {
            return (new PaymentController)->payment_failed();
        }
    }
}
