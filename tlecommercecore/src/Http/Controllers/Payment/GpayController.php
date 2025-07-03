<?php

namespace Plugin\TlcommerceCore\Http\Controllers\Payment;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Support\Facades\Http;

class GpayController extends Controller
{
    protected $total_payable_amount;
    protected $marchant_id; 
    protected $marchant_name;
    protected $currency = 'USD';
    protected $mode = 'TEST';

    public function setcredentials()
    {
        $this->currency = \Plugin\TlcommerceCore\Repositories\PaymentMethodRepository::configKeyValue(config('tlecommercecore.payment_methods.gpay'), 'gpay_currency');
        $this->marchant_id = \Plugin\TlcommerceCore\Repositories\PaymentMethodRepository::configKeyValue(config('tlecommercecore.payment_methods.gpay'), 'gpay_marchant_id');
        $this->marchant_name = \Plugin\TlcommerceCore\Repositories\PaymentMethodRepository::configKeyValue(config('tlecommercecore.payment_methods.gpay'), 'gpay_marchant_name');
        $sandbox = \Plugin\TlcommerceCore\Repositories\PaymentMethodRepository::configKeyValue(config('tlecommercecore.payment_methods.gpay'), 'sandbox');

        $this->mode = $sandbox == '1' ? 'TEST' : 'PRODUCTION';
    }

    /**
     * Initiate payment with gpay
     */
    public function index()
    {
        $this->setcredentials();
        
        $this->total_payable_amount = (new PaymentController())->convertCurrency($this->currency, session()->get('payable_amount'));

        $data = [
            'currency' => $this->currency,
            'total_payable_amount' => $this->total_payable_amount,
            'marchant_id' => $this->marchant_id,
            'marchant_name' => $this->marchant_name,
            'mode' => $this->mode,
        ];

        return view('plugin/tlecommercecore::payments.gateways.gpay.index', $data);
    }

    /**
     * Will handle gpay payment status
     */
    public function googlepayPaymentSubmit(Request $request)
    {
        try {
            if ($request['payment_status'] == 1) {
                return (new PaymentController)->payment_success("Marchant-ID " . $request['marchant_id']);
            } else {
                return (new PaymentController)->payment_failed();
            }
        } catch (Exception $ex) {
            return (new PaymentController)->payment_failed();
        }
    }
}
