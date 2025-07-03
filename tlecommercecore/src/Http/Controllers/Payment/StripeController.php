<?php

namespace Plugin\TlcommerceCore\Http\Controllers\Payment;

use App\Http\Controllers\Controller;
use Plugin\TlcommerceCore\Http\Controllers\Payment\PaymentController;

class StripeController extends Controller
{
    protected $total_payable_amount;
    protected $stripe_public_key;
    protected $stripe_secret_key;
    protected $currency = "USD";

    public function setcredentials()
    {
        $this->currency = \Plugin\TlcommerceCore\Repositories\PaymentMethodRepository::configKeyValue(config('tlecommercecore.payment_methods.stripe'), 'stripe_currency');
        $this->stripe_public_key = \Plugin\TlcommerceCore\Repositories\PaymentMethodRepository::configKeyValue(config('tlecommercecore.payment_methods.stripe'), 'stripe_public_key');
        $this->stripe_secret_key = \Plugin\TlcommerceCore\Repositories\PaymentMethodRepository::configKeyValue(config('tlecommercecore.payment_methods.stripe'), 'stripe_secret_key');
    }
    /**
     * Initial stripe payment
     */
    public function index()
    {
        $this->setcredentials();
        return view('plugin/tlecommercecore::payments.gateways.stripe.index')->with(
            [
                'stripe_public_key' => $this->stripe_public_key,
            ]
        );
    }
    /**
     * Create stripe payment
     */
    public function create_checkout_session()
    {
        $this->setcredentials();
        $this->total_payable_amount = (new PaymentController())->convertCurrency($this->currency, session()->get('payable_amount'));
        $amount = round($this->total_payable_amount * 100);

        \Stripe\Stripe::setApiKey($this->stripe_secret_key);
        $session = \Stripe\Checkout\Session::create([
            'payment_method_types' => ['card'],
            'line_items' => [
                [
                    'price_data' => [
                        'currency' => $this->currency,
                        'product_data' => [
                            'name' => "Payment"
                        ],
                        'unit_amount' => $amount,
                    ],
                    'quantity' => 1,
                ]
            ],
            'mode' => 'payment',
            'success_url' => route('stripe.success.payment'),
            'cancel_url' => route('stripe.cancel.payment'),
        ]);
        return response()->json(['id' => $session->id, 'status' => 200]);
    }
    /**
     * Success stripe payment
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
     * Cancel Stripe payment
     */
    public function cancel()
    {
        return (new PaymentController)->payment_failed();
    }
}
