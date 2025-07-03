<?php

namespace Plugin\Saas\Http\Controllers\Payment;

use Stripe\Stripe;
use Stripe\Checkout\Session;
use App\Http\Controllers\Controller;
use Plugin\Saas\Http\Controllers\Payment\PaymentController;

class StripeController extends Controller
{
    protected $stripe_public_key;
    protected $stripe_secret_key;
    protected $currency = "USD";

    public function __construct()
    {
        $this->currency = \Plugin\Saas\Repositories\PaymentMethodRepository::configKeyValue(config('saas.payment_methods.stripe'), 'stripe_currency');
        $this->stripe_public_key = \Plugin\Saas\Repositories\PaymentMethodRepository::configKeyValue(config('saas.payment_methods.stripe'), 'stripe_public_key');
        $this->stripe_secret_key = \Plugin\Saas\Repositories\PaymentMethodRepository::configKeyValue(config('saas.payment_methods.stripe'), 'stripe_secret_key');
    }
    /**
     * Initial stripe payment
     */
    public function index()
    {
        return view('plugin/saas::payments.gateways.stripe.index')->with(
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
        $total_payable_amount = (new PaymentController())->convertCurrency($this->currency, session()->get('payable_amount'));
        $amount = round($total_payable_amount * 100);

        Stripe::setApiKey($this->stripe_secret_key);
        $session = Session::create([
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
            'success_url' => route('plugin.saas.stripe.success.payment'),
            'cancel_url' => route('plugin.saas.stripe.cancel.payment'),
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