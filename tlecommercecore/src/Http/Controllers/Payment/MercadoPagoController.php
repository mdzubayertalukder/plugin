<?php

namespace Plugin\TlcommerceCore\Http\Controllers\Payment;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;
use Plugin\TlcommerceCore\Http\Controllers\Payment\PaymentController;

class MercadoPagoController extends Controller
{

    protected $accessToken;
    protected $public_key;
    protected $total_payable_amount;
    protected $currency = "BRL";

    public function __construct()
    {
        $this->accessToken = \Plugin\TlcommerceCore\Repositories\PaymentMethodRepository::configKeyValue(config('tlecommercecore.payment_methods.mercado-pago'), 'mc_pago_access_token');
        $this->public_key = \Plugin\TlcommerceCore\Repositories\PaymentMethodRepository::configKeyValue(config('tlecommercecore.payment_methods.mercado-pago'), 'mc_pago_public_key');
    }

    public function index()
    {
        $this->currency = \Plugin\TlcommerceCore\Repositories\PaymentMethodRepository::configKeyValue(config('tlecommercecore.payment_methods.mercado-pago'), 'mc_pago_currency');
        $this->total_payable_amount = (new PaymentController())->convertCurrency($this->currency, session()->get('payable_amount'));

        // $product_title ="Checkout";

        // Prepare the request payload
        $payload = [
            "items" => [
                [
                    "title" => 'Product Title',
                    "quantity" => 1,
                    "currency_id" => $this->currency,
                    "unit_price" => $this->total_payable_amount
                ]
            ],
            "back_urls" => [
                "success" => route('mercadopago.payment.success.ecommerce'),
                "failure" => route('mercadopago.payment.failure.ecommerce'),
                "pending" => route('mercadopago.payment.pending.ecommerce'),
            ],
            "auto_return" => "approved",
        ];

        // Make the API call
        $response = Http::withToken($this->accessToken)
            ->post('https://api.mercadopago.com/checkout/preferences', $payload);


        if ($response->successful()) {
            $responseBody = $response->json();
            return redirect($responseBody['init_point']);

            // return view('plugin/saas::payments.gateways.mercadopago.index', [
            //     'init_point' => $responseBody['init_point'],
            //     'public_key' => $this->public_key
            // ]);
        }

        return (new PaymentController)->payment_failed();
    }

    public function success(Request $request)
    {
        $data = $this->getPaymentDetails($request['payment_id']);
        if (!empty($data) && isset($data['id']) && !empty($data['id'])) {
            return (new PaymentController)->payment_success($data['id']);
        }

        return (new PaymentController)->payment_failed();
    }

    public function failure(Request $request)
    {
        return (new PaymentController)->payment_failed();
    }

    public function pending(Request $request)
    {
        return (new PaymentController)->payment_failed();
    }

    public function getPaymentDetails($paymentId)
    {
        $url = "https://api.mercadopago.com/v1/payments/{$paymentId}?access_token=" . $this->accessToken;
        $response = Http::get($url);
        return $response->json();
    }
}
