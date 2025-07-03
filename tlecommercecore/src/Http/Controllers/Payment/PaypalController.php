<?php

namespace Plugin\TlcommerceCore\Http\Controllers\Payment;

use Exception;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Redirect;
use PayPalCheckoutSdk\Core\PayPalHttpClient;
use PayPalCheckoutSdk\Core\SandboxEnvironment;
use PayPalCheckoutSdk\Core\ProductionEnvironment;
use PayPalCheckoutSdk\Orders\OrdersCreateRequest;
use PayPalCheckoutSdk\Orders\OrdersCaptureRequest;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Plugin\TlcommerceCore\Http\Controllers\Payment\PaymentController;

class PaypalController extends Controller
{

    protected $total_payable_amount;
    protected $paypal_client_id;
    protected $paypal_client_secret;
    protected $is_active_sandbox;
    protected $currency = 'USD';

    public function setcredentials()
    {
        $this->currency = \Plugin\TlcommerceCore\Repositories\PaymentMethodRepository::configKeyValue(config('tlecommercecore.payment_methods.paypal'), 'paypal_currency');
        $this->paypal_client_id = \Plugin\TlcommerceCore\Repositories\PaymentMethodRepository::configKeyValue(config('tlecommercecore.payment_methods.paypal'), 'paypal_client_id');
        $this->paypal_client_secret = \Plugin\TlcommerceCore\Repositories\PaymentMethodRepository::configKeyValue(config('tlecommercecore.payment_methods.paypal'), 'paypal_client_secret');
        $this->is_active_sandbox = \Plugin\TlcommerceCore\Repositories\PaymentMethodRepository::configKeyValue(config('tlecommercecore.payment_methods.paypal'), 'sandbox');
    }

    public function index()
    {
        $this->setcredentials();

        $clientId = $this->paypal_client_id;
        $clientSecret = $this->paypal_client_secret;

        if ($this->is_active_sandbox == 1) {
            $environment = new SandboxEnvironment($clientId, $clientSecret);
        } else {
            $environment = new ProductionEnvironment($clientId, $clientSecret);
        }

        $this->total_payable_amount = (new PaymentController())->convertCurrency($this->currency, session()->get('payable_amount'));
        $client = new PayPalHttpClient($environment);
        $request = new OrdersCreateRequest();
        $request->prefer('return=representation');
        $request->body = [
            "intent" => "CAPTURE",
            "purchase_units" => [[
                "reference_id" => rand(000000, 999999),
                "amount" => [
                    "value" => number_format($this->total_payable_amount, 2, '.', ''),
                    "currency_code" => $this->currency
                ]
            ]],
            "application_context" => [
                "cancel_url" => route('paypal.cancel'),
                "return_url" => route('paypal.success')
            ]
        ];
        try {
            $response = $client->execute($request);
            return Redirect::to($response->result->links[1]->href);
        } catch (HttpException $ex) {
            return (new PaymentController)->payment_failed();
        } catch (Exception $e) {
            return (new PaymentController)->payment_failed();
        }
    }


    public function cancel(Request $request)
    {
        return (new PaymentController)->payment_cancel();
    }

    public function success(Request $request)
    {
        $this->setcredentials();

        $clientId = $this->paypal_client_id;
        $clientSecret = $this->paypal_client_secret;

        if ($this->is_active_sandbox == 1) {
            $environment = new SandboxEnvironment($clientId, $clientSecret);
        } else {
            $environment = new ProductionEnvironment($clientId, $clientSecret);
        }
        $client = new PayPalHttpClient($environment);

        $ordersCaptureRequest = new OrdersCaptureRequest($request->token);
        $ordersCaptureRequest->prefer('return=representation');
        try {
            $response = $client->execute($ordersCaptureRequest);
            $payment_id = 'id-' . $response->id;
            return (new PaymentController)->payment_success(json_encode($payment_id));
        } catch (Exception $ex) {
            return (new PaymentController)->payment_failed();
        }
    }
}
