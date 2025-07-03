<?php

namespace Plugin\TlcommerceCore\Http\Controllers\Payment;

use Exception;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Plugin\TlcommerceCore\Models\PaymentMethods;
use Plugin\TlcommerceCore\Http\Controllers\Payment\PaymentController;

# IF BROWSE FROM LOCAL HOST, KEEP true
if (!defined("SSLCZ_IS_LOCAL_HOST")) {
    define("SSLCZ_IS_LOCAL_HOST", true);
}
class SSLCommerzController extends Controller
{
    protected $sslc_checkout_api_endpoint;
    protected $sslc_validation_url;
    protected $sslc_mode;
    protected $sslc_response;
    protected $sslcz_store_id;
    protected $sslcz_store_password;
    protected $sandbox;
    protected $total_payable_amount = 0;
    public $error = ' ';
    public $currency = 'BDT';


    public function setcredentials()
    {
        $this->currency = \Plugin\TlcommerceCore\Repositories\PaymentMethodRepository::configKeyValue(config('tlecommercecore.payment_methods.sslcommerz'), 'ssl_currency');
        $this->sslcz_store_id = \Plugin\TlcommerceCore\Repositories\PaymentMethodRepository::configKeyValue(config('tlecommercecore.payment_methods.sslcommerz'), 'sslcz_store_id');
        $this->sslcz_store_password = \Plugin\TlcommerceCore\Repositories\PaymentMethodRepository::configKeyValue(config('tlecommercecore.payment_methods.sslcommerz'), 'sslcz_store_password');
        $this->sandbox = \Plugin\TlcommerceCore\Repositories\PaymentMethodRepository::configKeyValue(config('tlecommercecore.payment_methods.sslcommerz'), 'sandbox');

        $this->sslc_mode = $this->sandbox == 1 ? 'sandbox' : 'securepay';

        $this->sslc_checkout_api_endpoint = "https://" . $this->sslc_mode . ".sslcommerz.com/gwprocess/v3/api.php";
        $this->sslc_validation_url = "https://" . $this->sslc_mode . ".sslcommerz.com/validator/api/validationserverAPI.php";
    }
    /**
     * Initial  payment
     */
    public function index()
    {
        $this->setcredentials();
        $post_data = [];
        $post_data['currency'] = $this->currency;
        $post_data['tran_id'] = rand(000000, 999999);
        $this->total_payable_amount = (new PaymentController())->convertCurrency($this->currency, session()->get('payable_amount'));
        $post_data['total_amount'] = $this->total_payable_amount;
        $post_data['value_a'] = session()->get('payment_type');
        $post_data['value_b'] = session()->get('payment_method_id');
        $post_data['value_c'] = session()->get('payable_amount');

        if (session()->get('payment_type') === 'checkout') {
            $post_data['value_d'] = session()->get('order_id');
        }

        if (session()->get('payment_type') === 'wallet_recharge') {
            $post_data['value_d'] = session()->get('customer');
        }

        $post_data['success_url'] = route('sslcommerz.success.payment');
        $post_data['fail_url'] = route('sslcommerz.fail.payment');
        $post_data['cancel_url'] = route('sslcommerz.cancel.payment');
        $payment_options = $this->initiate($post_data, false);

        if (!is_array($payment_options)) {
            print_r($payment_options);
            $payment_options = array();
        }
    }

    public function success(Request $request)
    {
        try {
            $this->setcredentials();
            if ($request->status === 'VALID') {
                $this->generateSession($request);
                return (new PaymentController)->payment_success('transaction id ' . $request->tran_id);
            } else {
                return (new PaymentController)->payment_failed();
            }
        } catch (Exception $ex) {
            return (new PaymentController)->payment_failed();
        }
    }
    /**
     * Payment failed
     */
    public function fail()
    {
        $base_url = url('/');
        $url = $base_url . '/payment/sslcommerz/pay';
        session()->put('redirect_url', $url);
        return (new PaymentController)->payment_failed();
    }
    /**
     * Payment cancel
     */
    public function cancel(Request $request)
    {
        $base_url = url('/');

        if ($request->value_a == 'checkout') {
            $url = $base_url . '/dashboard/order-details/' . $request->value_d;
        }

        if ($request->value_a == 'wallet_recharge') {
            $url = $base_url . '/dashboard/wallet';
        }

        session()->put('redirect_url', $url);
        return (new PaymentController)->payment_cancel();
    }

    /**
     * Payment init
     */
    public function initiate($post_data, $get_pay_options = false)
    {
        $this->setcredentials();
        if ($post_data != '' && is_array($post_data)) {

            $post_data['store_id'] = $this->sslcz_store_id;
            $post_data['store_passwd'] = $this->sslcz_store_password;

            $load_sslc = $this->sendRequest($post_data);

            if ($load_sslc) {
                if (isset($this->sslc_response['status']) && $this->sslc_response['status'] == 'SUCCESS') {

                    if (!$get_pay_options) {
                        if (isset($this->sslc_response['GatewayPageURL']) && $this->sslc_response['GatewayPageURL'] != '') {
                            echo "
                                <script>
                                    window.location.href = '" . $this->sslc_response['GatewayPageURL'] . "';
                                </script>
                            ";
                            exit;
                        } else {
                            $this->error = "No redirect URL found!";
                            return $this->error;
                        }
                    }

                    if ($get_pay_options) {
                        $options = array();
                        # VISA GATEWAY
                        if (isset($this->sslc_response['gw']['visa']) && $this->sslc_response['gw']['visa'] != "") {
                            $sslcz_visa = explode(",", $this->sslc_response['gw']['visa']);
                            foreach ($sslcz_visa as $gw_value) {
                                if ($gw_value == 'dbbl_visa') {
                                    //$options['cards'][0]['name'] = "DBBL VISA";
                                    //$options['cards'][0]['link'] =  "<a class='hvr-pop' href='".$this->sslc_response['redirectGatewayURL']."dbbl_visa'><img style='width:60px; height:60px' src='".$this->_get_image("dbbl_visa", $this->sslc_response)."' alt='dbbl_visa'/></a>";
                                }
                                if ($gw_value == 'brac_visa') {
                                    //$options['cards'][1]['name'] = "BRAC VISA";
                                    //$options['visa'][1]['link'] =  "<a class='hvr-pop' href='".$this->sslc_response['redirectGatewayURL']."brac_visa'><img style='width:60px; height:60px' src='".$this->_get_image("brac_visa", $this->sslc_response)."' alt='brac_visa'/></a>";
                                }
                                if ($gw_value == 'city_visa') {
                                    //$options['cards'][2]['name'] = "CITY VISA";
                                    //$options['cards'][2]['link'] =  "<a class='hvr-pop' href='".$this->sslc_response['redirectGatewayURL']."city_visa'><img style='width:60px; height:60px' src='".$this->_get_image("city_visa", $this->sslc_response)."' alt='city_visa'/></a>";
                                }
                                if ($gw_value == 'ebl_visa') {
                                    //$options['cards'][3]['name'] = "EBL VISA";
                                    //$options['cards'][3]['link'] =  "<a class='hvr-pop' href='".$this->sslc_response['redirectGatewayURL']."ebl_visa'><img style='width:60px; height:60px' src='".$this->_get_image("ebl_visa", $this->sslc_response)."' alt='ebl_visa'/></a>";
                                }
                                if ($gw_value == 'visacard') {
                                    $options['cards'][4]['name'] = "VISA";
                                    $options['cards'][4]['link'] = "<a class='hvr-pop' href='" . $this->sslc_response['redirectGatewayURL'] . "visacard'><img style='width:60px; height:60px' src='" . $this->_get_image("visacard", $this->sslc_response) . "' alt='visacard'/></a>";
                                }
                            }
                        } # END OF VISA

                        # MASTER GATEWAY
                        if (isset($this->sslc_response['gw']['master']) && $this->sslc_response['gw']['master'] != "") {
                            $sslcz_visa = explode(",", $this->sslc_response['gw']['master']);
                            foreach ($sslcz_visa as $gw_value) {
                                if ($gw_value == 'dbbl_master') {
                                    //$options['cards'][5]['name'] = "DBBL MASTER";
                                    //$options['cards'][5]['link'] =  "<a class='hvr-pop' href='".$this->sslc_response['redirectGatewayURL']."dbbl_master'><img style='width:60px; height:60px' src='".$this->_get_image("dbbl_master", $this->sslc_response)."' alt='dbbl_master'/></a>";
                                }
                                if ($gw_value == 'brac_master') {
                                    //$options['cards'][6]['name'] = "BRAC MASTER";
                                    //$options['master'][6]['link'] =  "<a class='hvr-pop' href='".$this->sslc_response['redirectGatewayURL']."brac_master'><img style='width:60px; height:60px' src='".$this->_get_image("brac_master", $this->sslc_response)."' alt='brac_master'/></a>";
                                }
                                if ($gw_value == 'city_master') {
                                    //$options['cards'][7]['name'] = "CITY MASTER";
                                    //$options['cards'][7]['link'] =  "<a class='hvr-pop' href='".$this->sslc_response['redirectGatewayURL']."city_master'><img style='width:60px; height:60px' src='".$this->_get_image("city_master", $this->sslc_response)."' alt='city_master'/></a>";
                                }
                                if ($gw_value == 'ebl_master') {
                                    //$options['cards'][8]['name'] = "EBL MASTER";
                                    //$options['cards'][8]['link'] =  "<a class='hvr-pop' href='".$this->sslc_response['redirectGatewayURL']."ebl_master'><img style='width:60px; height:60px' src='".$this->_get_image("ebl_master", $this->sslc_response)."' alt='ebl_master'/></a>";
                                }
                                if ($gw_value == 'mastercard') {
                                    $options['cards'][9]['name'] = "MASTER";
                                    $options['cards'][9]['link'] = "<a class='hvr-pop' href='" . $this->sslc_response['redirectGatewayURL'] . "mastercard'><img style='width:60px; height:60px' src='" . $this->_get_image("mastercard", $this->sslc_response) . "' alt='mastercard'/></a>";
                                }
                            }
                        } # END OF MASTER


                        # AMEX GATEWAY
                        if (isset($this->sslc_response['gw']['amex']) && $this->sslc_response['gw']['amex'] != "") {
                            $sslcz_visa = explode(",", $this->sslc_response['gw']['amex']);
                            foreach ($sslcz_visa as $gw_value) {
                                if ($gw_value == 'city_amex') {
                                    $options['cards'][10]['name'] = "AMEX";
                                    $options['cards'][10]['link'] = "<a class='hvr-pop' href='" . $this->sslc_response['redirectGatewayURL'] . "city_amex'><img style='width:60px; height:60px' src='" . $this->_get_image("city_amex", $this->sslc_response) . "' alt='city_amex'/></a>";
                                }
                            }
                        } # END OF AMEX


                        # OTHER CARDS GATEWAY
                        if (isset($this->sslc_response['gw']['othercards']) && $this->sslc_response['gw']['othercards'] != "") {
                            $sslcz_visa = explode(",", $this->sslc_response['gw']['othercards']);
                            foreach ($sslcz_visa as $gw_value) {
                                if ($gw_value == 'dbbl_nexus') {
                                    $options['others'][0]['name'] = "NEXUS";
                                    $options['others'][0]['link'] = "<a class='hvr-pop' href='" . $this->sslc_response['redirectGatewayURL'] . "dbbl_nexus'><img style='width:60px; height:60px' src='" . $this->_get_image("dbbl_nexus", $this->sslc_response) . "' alt='dbbl_nexus'/></a>";
                                }

                                if ($gw_value == 'qcash') {
                                    $options['others'][1]['name'] = "QCASH";
                                    $options['others'][1]['link'] = "<a class='hvr-pop' href='" . $this->sslc_response['redirectGatewayURL'] . "qcash'><img style='width:60px; height:60px' src='" . $this->_get_image("qcash", $this->sslc_response) . "' alt='qcash'/></a>";
                                }

                                if ($gw_value == 'fastcash') {
                                    $options['others'][2]['name'] = "FASTCASH";
                                    $options['others'][2]['link'] = "<a class='hvr-pop' href='" . $this->sslc_response['redirectGatewayURL'] . "fastcash'><img style='width:60px; height:60px' src='" . $this->_get_image("fastcash", $this->sslc_response) . "' alt='fastcash'/></a>";
                                }
                            }
                        } # END OF OTHER CARDS

                        # INTERNET BANKING GATEWAY
                        if (isset($this->sslc_response['gw']['internetbanking']) && $this->sslc_response['gw']['internetbanking'] != "") {
                            $sslcz_visa = explode(",", $this->sslc_response['gw']['internetbanking']);
                            foreach ($sslcz_visa as $gw_value) {
                                if ($gw_value == 'city') {
                                    $options['internet'][0]['name'] = "CITYTOUCH";
                                    $options['internet'][0]['link'] = "<a class='hvr-pop' href='" . $this->sslc_response['redirectGatewayURL'] . "city'><img style='width:60px; height:60px' src='" . $this->_get_image("city", $this->sslc_response) . "' alt='city'/></a>";
                                }

                                if ($gw_value == 'bankasia') {
                                    $options['internet'][1]['name'] = "BANK ASIA";
                                    $options['internet'][1]['link'] = "<a class='hvr-pop' href='" . $this->sslc_response['redirectGatewayURL'] . "bankasia'><img style='width:60px; height:60px' src='" . $this->_get_image("bankasia", $this->sslc_response) . "' alt='bankasia'/></a>";
                                }

                                if ($gw_value == 'ibbl') {
                                    $options['internet'][2]['name'] = "IBBL";
                                    $options['internet'][2]['link'] = "<a class='hvr-pop' href='" . $this->sslc_response['redirectGatewayURL'] . "ibbl'><img style='width:60px; height:60px' src='" . $this->_get_image("ibbl", $this->sslc_response) . "' alt='ibbl'/></a>";
                                }

                                if ($gw_value == 'mtbl') {
                                    $options['internet'][3]['name'] = "MTBL";
                                    $options['internet'][3]['link'] = "<a class='hvr-pop' href='" . $this->sslc_response['redirectGatewayURL'] . "mtbl'><img style='width:60px; height:60px' src='" . $this->_get_image("mtbl", $this->sslc_response) . "' alt='mtbl'/></a>";
                                }
                            }
                        } # END OF INTERNET BANKING

                        # MOBILE BANKING GATEWAY
                        if (isset($this->sslc_response['gw']['mobilebanking']) && $this->sslc_response['gw']['mobilebanking'] != "") {
                            $sslcz_visa = explode(",", $this->sslc_response['gw']['mobilebanking']);
                            foreach ($sslcz_visa as $gw_value) {
                                if ($gw_value == 'dbblmobilebanking') {
                                    $options['mobile'][0]['name'] = "DBBL MOBILE BANKING";
                                    $options['mobile'][0]['link'] = "<a class='hvr-pop' href='" . $this->sslc_response['redirectGatewayURL'] . "dbblmobilebanking'><img style='width:60px; height:60px' src='" . $this->_get_image("dbblmobilebanking", $this->sslc_response) . "' alt='dbblmobilebanking'/></a>";
                                }

                                if ($gw_value == 'bkash') {
                                    $options['mobile'][1]['name'] = "Bkash";
                                    $options['mobile'][1]['link'] = "<a class='hvr-pop' href='" . $this->sslc_response['redirectGatewayURL'] . "bkash'><img style='width:60px; height:60px' src='" . $this->_get_image("bkash", $this->sslc_response) . "' alt='bkash'/></a>";
                                }

                                if ($gw_value == 'abbank') {
                                    $options['mobile'][2]['name'] = "AB Direct";
                                    $options['mobile'][2]['link'] = "<a class='hvr-pop' href='" . $this->sslc_response['redirectGatewayURL'] . "abbank'><img style='width:60px; height:60px' src='" . $this->_get_image("abbank", $this->sslc_response) . "' alt='abbank'/></a>";
                                }

                                if ($gw_value == 'ibbl') {
                                    $options['mobile'][3]['name'] = "IBBL";
                                    $options['mobile'][3]['link'] = "<a class='hvr-pop' href='" . $this->sslc_response['redirectGatewayURL'] . "ibbl'><img style='width:60px; height:60px' src='" . $this->_get_image("ibbl", $this->sslc_response) . "' alt='ibbl'/></a>";
                                }

                                if ($gw_value == 'mycash') {
                                    $options['mobile'][4]['name'] = "MYCASH";
                                    $options['mobile'][4]['link'] = "<a class='hvr-pop' href='" . $this->sslc_response['redirectGatewayURL'] . "mycash'><img style='width:60px; height:60px' src='" . $this->_get_image("mycash", $this->sslc_response) . "' alt='mycash'/></a>";
                                }

                                if ($gw_value == 'ific') {
                                    $options['mobile'][5]['name'] = "IFIC";
                                    $options['mobile'][5]['link'] = "<a class='hvr-pop' href='" . $this->sslc_response['redirectGatewayURL'] . "ific'><img style='width:60px; height:60px' src='" . $this->_get_image("ific", $this->sslc_response) . "' alt='ific'/></a>";
                                }
                            }
                        } # END OF MOBILE BANKING

                        return $options;
                    }
                } else {

                    $this->error = "Invalid Credential!";
                    return $this->error;
                }
            } else {
                $this->error = "Connectivity Issue. Please contact your sslcommerz manager";
                return $this->error;
            }
        } else {
            $msg = "Please provide a valid information list about transaction with transaction id, amount, success url, fail url, cancel url, store id and pass at least";
            $this->error = $msg;
            return false;
        }
    }


    /**
     * Send api request
     */
    protected function sendRequest($data)
    {
        $this->setcredentials();
        $handle = curl_init();
        curl_setopt($handle, CURLOPT_URL, $this->sslc_checkout_api_endpoint);
        curl_setopt($handle, CURLOPT_POST, 1);
        curl_setopt($handle, CURLOPT_POSTFIELDS, $data);
        curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);

        if (SSLCZ_IS_LOCAL_HOST) {
            curl_setopt($handle, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($handle, CURLOPT_SSL_VERIFYPEER, false);
        } else {
            curl_setopt($handle, CURLOPT_SSL_VERIFYHOST, 2); // Its default value is now 2
            curl_setopt($handle, CURLOPT_SSL_VERIFYPEER, true);
        }


        $content = curl_exec($handle);


        $code = curl_getinfo($handle, CURLINFO_HTTP_CODE);

        if ($code == 200 && !(curl_errno($handle))) {
            curl_close($handle);
            $apiResponse = $content;
            $this->sslc_response = json_decode($apiResponse, true);
            return $this;
        } else {
            curl_close($handle);
            $msg = "FAILED TO CONNECT WITH SSLCOMMERZ API";
            $this->error = $msg;
            return false;
        }
    }
    /**
     * Regenerate session
     */
    protected function generateSession($request)
    {
        session()->put('payment_type', $request->value_a);
        session()->put('payment_method_id', $request->value_b);
        session()->put('payable_amount', $request->value_c);

        $payment_method = PaymentMethods::where('id', $request->value_b)->first();
        session()->put('payment_method_id', $payment_method->name);

        if ($request->value_a == 'checkout') {
            session()->put('order_id', $request->value_d);
        }

        if ($request->value_a == 'wallet_recharge') {
            session()->put('customer', $request->value_d);
        }
    }
}
