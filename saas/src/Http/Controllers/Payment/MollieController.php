<?php

namespace Plugin\Saas\Http\Controllers\Payment;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Support\Facades\Redirect;
use Plugin\Saas\Http\Controllers\Payment\PaymentController;

class MollieController extends Controller
{
    protected $total_payable_amount;
    protected $mollie_api_key;
    protected $currency = 'USD';

    public function __construct()
    {
        $this->currency = \Plugin\Saas\Repositories\PaymentMethodRepository::configKeyValue(config('saas.payment_methods.mollie'), 'mollie_currency');
        $this->total_payable_amount = (new PaymentController())->convertCurrency($this->currency, session()->get('payable_amount'));
        $this->mollie_api_key = \Plugin\Saas\Repositories\PaymentMethodRepository::configKeyValue(config('saas.payment_methods.mollie'), 'mollie_api_key');
    }

    /**
     * Mollie create payment
     */
    public function index()
    {
        try {
            $apiKey = $this->mollie_api_key;
            $description = 'Payment with mollie';

            $payload = [
                'amount' => [
                    'currency' => $this->currency,
                    'value' => number_format($this->total_payable_amount, 2),
                ],
                'description' => $description,
                'redirectUrl' => route('payment.callback')
            ];

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, 'https://api.mollie.com/v2/payments');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Authorization: Bearer ' . $apiKey,
                'Content-Type: application/json',
            ]);
            $result = curl_exec($ch);
            curl_close($ch);

            $response = json_decode($result, true);
            $redirect_url = $response['_links']['checkout']['href'];
            session()->put('payment_id', $response['id']);

            return Redirect::away($redirect_url);
        } catch (Exception $ex) {
            return (new PaymentController)->payment_failed();
        }
    }

    /**
     * Mollie callback function
     */
    public function paymentCallback()
    {
        try {
            $paymentId = $paymentId = session()->get('payment_id');
            $apiKey = $this->mollie_api_key;
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, 'https://api.mollie.com/v2/payments/' . $paymentId);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Authorization: Bearer ' . $apiKey,
            ]);
            $result = curl_exec($ch);
            curl_close($ch);

            $response = json_decode($result, true);

            if ($response['status'] == 'paid') {
                return (new PaymentController)->payment_success();
            } elseif ($response['status'] == 'canceled') {
                return (new PaymentController)->payment_cancel();
            } else {
                return (new PaymentController)->payment_failed();
            }
        } catch (Exception $ex) {
            return (new PaymentController)->payment_failed();
        }
    }
}
