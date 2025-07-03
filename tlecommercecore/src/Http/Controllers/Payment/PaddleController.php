<?php

namespace Plugin\TlcommerceCore\Http\Controllers\Payment;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Exception;
use Plugin\TlcommerceCore\Http\Controllers\Payment\PaymentController;

class PaddleController extends Controller
{
    protected $total_payable_amount;
    protected $paddle_vendor_id;
    protected $paddle_public_key;
    protected $paddle_vendor_auth_code;
    protected $sandbox;
    protected $paddle_api_endpoint;
    protected $mode;
    protected $currency = 'USD';

    public function setcredentials()
    {
        $this->currency = \Plugin\TlcommerceCore\Repositories\PaymentMethodRepository::configKeyValue(config('tlecommercecore.payment_methods.paddle'), 'paddle_currency');
        $this->paddle_vendor_id = \Plugin\TlcommerceCore\Repositories\PaymentMethodRepository::configKeyValue(config('tlecommercecore.payment_methods.paddle'), 'paddle_vendor_id');
        $this->paddle_public_key = \Plugin\TlcommerceCore\Repositories\PaymentMethodRepository::configKeyValue(config('tlecommercecore.payment_methods.paddle'), 'paddle_public_key');
        $this->paddle_vendor_auth_code = \Plugin\TlcommerceCore\Repositories\PaymentMethodRepository::configKeyValue(config('tlecommercecore.payment_methods.paddle'), 'paddle_vendor_auth_code');
        $this->sandbox = \Plugin\TlcommerceCore\Repositories\PaymentMethodRepository::configKeyValue(config('tlecommercecore.payment_methods.paddle'), 'sandbox');
        $this->mode = $this->sandbox == 1 ? 'sandbox-vendors' : 'vendors';
        $this->paddle_api_endpoint = "https://" . $this->mode . ".paddle.com/api/2.0/product/generate_pay_link";
    }
    /**
     * Initial Paddle payment
     */
    public function index()
    {
        try {
            $this->setcredentials();
            $payment_id = rand();
            session()->put('payment_id', $payment_id);
            @ini_set('max_execution_time', 0);
            @set_time_limit(0);
            $this->total_payable_amount = (new PaymentController())->convertCurrency($this->currency, session()->get('payable_amount'));
            $customer_currency = $this->currency;
            $amount  = $this->total_payable_amount;
            $webhook_url = route('paddle.payment.success', ['paddle_order_id' => '{checkout_id}']);
            $return_url = route('paddle.payment.return', ['paddle_order_id' => '{checkout_id}']);

            if (session()->get('payment_type') === 'checkout') {
                $order_id = session()->get('order_id');
                $products = DB::table('tl_com_ordered_products')
                    ->join('tl_com_products', 'tl_com_products.id', '=', 'tl_com_ordered_products.product_id')
                    ->where('tl_com_ordered_products.order_id', $order_id)
                    ->pluck('tl_com_products.name')
                    ->toArray();
                $title = implode(', ', $products);
                $data = [
                    'vendor_id' => $this->paddle_vendor_id,
                    'vendor_auth_code' => $this->paddle_vendor_auth_code,
                    'title' => $title,
                    'webhook_url' => $webhook_url,
                    'prices' => ["$customer_currency:$amount"],
                    'return_url' => $return_url,
                    'customer_currency' => $customer_currency,
                    'discountable' => 0,
                    'quantity_variable' => 0
                ];
            }

            if (session()->get('payment_type') === 'wallet_recharge') {
                $data = [
                    'vendor_id' => $this->paddle_vendor_id,
                    'vendor_auth_code' => $this->paddle_vendor_auth_code,
                    'title' => "Recharge Wallet",
                    'webhook_url' => $webhook_url,
                    'prices' => ["$customer_currency:$amount"],
                    'return_url' => $return_url,
                    'customer_currency' => $customer_currency,
                    'discountable' => 0,
                    'quantity_variable' => 0
                ];
            }


            $data = http_build_query($data);

            $curl = curl_init();

            curl_setopt_array($curl, [
                CURLOPT_URL => $this->paddle_api_endpoint,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "POST",
                CURLOPT_POSTFIELDS => $data,
                CURLOPT_HTTPHEADER => [
                    "Content-Type: application/x-www-form-urlencoded"
                ],
            ]);

            //execute post
            $result = curl_exec($curl);
            $err = curl_error($curl);

            //close connection
            curl_close($curl);

            if ($err) {
                return (new PaymentController)->payment_failed();
            }

            $charge = json_decode($result);
            if ($charge->success && $charge->response->url != null) {
                return redirect($charge->response->url);
            } else {
                return (new PaymentController)->payment_failed();
            }
        } catch (Exception $ex) {
            return (new PaymentController)->payment_failed();
        }
    }

    public function paddleSuccess(Request $request)
    {
        try {
            $checkout_id = $request->paddle_order_id;
            if ($checkout_id != null && session()->get('payment_id')) {
                return (new PaymentController)->payment_success('Paddle checkout id ' . $checkout_id);
            } else {
                return (new PaymentController)->payment_failed();
            }
        } catch (Exception $ex) {
            return (new PaymentController)->payment_failed();
        }
    }

    public function paddleReturn(Request $request)
    {
        try {
            $checkout_id = $request->paddle_order_id;
            if ($checkout_id != null && session()->get('payment_id')) {
                return (new PaymentController)->payment_success('Paddle checkout id ' . $checkout_id);
            } else {
                return (new PaymentController)->payment_failed();
            }
        } catch (Exception $ex) {
            return (new PaymentController)->payment_failed();
        }
    }
}
