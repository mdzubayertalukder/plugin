<?php

namespace Plugin\Saas\Http\Controllers\Payment;

use Carbon\Carbon;
use Illuminate\View\View;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Plugin\Saas\Models\Package;
use Plugin\Saas\Models\Currency;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;
use Core\Exceptions\CurrencyException;
use Plugin\Saas\Models\PaymentHistory;
use Plugin\Saas\Models\PaymentMethods;
use Plugin\Saas\Services\SaasNotification;
use Plugin\Saas\Repositories\CouponRepository;
use Plugin\Saas\Repositories\TenantRepository;
use Plugin\Saas\Repositories\CurrencyRepository;
use Plugin\Saas\Repositories\SubscriptionRepository;
use Plugin\Saas\Repositories\PaymentMethodRepository;
use Plugin\Saas\Http\Requests\PaymentMethodCredentialRequest;

class PaymentController extends Controller
{
    /**
     * Convert currency
     */
    public function convertCurrency($convert_to_currency, $amount)
    {
        $system_currency = \Plugin\Saas\Models\Currency::where('id', \Plugin\Saas\Repositories\SettingsRepository::getSaasSetting('default_currency'))
            ->select('code', 'conversion_rate')
            ->first();
        $to_currency = \Plugin\Saas\Models\Currency::where('code', $convert_to_currency)
            ->select('code', 'conversion_rate')
            ->first();

        if ($to_currency != null) {
            $converted_amount = ($amount / $system_currency->conversion_rate) * $to_currency->conversion_rate;
            return $converted_amount;
        }

        throw new CurrencyException("Currency error. $convert_to_currency currency is not configured.");
    }

    /**
     * Will return payment methods
     *
     * @return mixed
     */
    public function paymentMethods(): View
    {
        $currency_repository = new CurrencyRepository();
        $currencies = $currency_repository->getAllSaasCurrencies();
        $payment_methods = (new PaymentMethodRepository)->paymentMethods();
        return view('plugin/saas::payments.gateways.gateway_list')->with(
            [
                'payment_methods' => $payment_methods,
                'currencies' => $currencies
            ]
        );
    }
    /**
     * Will update payment method status
     *
     * @param \Illuminate\Http\Request $request
     * @return void
     */
    public function changePaymentMethodStatus(Request $request): void
    {
        $res = (new PaymentMethodRepository)->paymentMethodUpdateStatus($request['id']);
        if ($res) {
            toastNotification('success', translate('Payment method updated successfully'));
        } else {
            toastNotification('error', translate('Action failed'));
        }
    }
    /**
     * Will update tenant payment method status
     */
    public function changeTenantPaymentMethodStatus(Request $request): JsonResponse
    {
        $res = (new PaymentMethodRepository)->tenantPaymentMethodUpdateStatus($request['id']);
        if ($res) {
            return response()->json([
                'success' => true
            ], 200);
        }

        return response()->json([
            'success' => false
        ], 200);
    }
    /**
     * Will return payment method configuration form
     */
    public function getPaymentMethodCredentials(Request $request): JsonResponse
    {
        $method = PaymentMethods::findOrFail($request['id']);
        $currencies = Currency::all();
        $default_currency = null;
        $configuration_path = 'plugin/saas::payments.gateways.' . str_replace(' ', '', Str::lower($method->name)) . '.configuration';
        return response()->json([
            'success' => true,
            'title' => $method->name,
            'html' => view($configuration_path, ['method' => $method, 'currencies' => $currencies, 'default_currency' => $default_currency])->render()
        ], 200);
    }
    /**
     * Will update payment method credential
     *
     * @param PaymentMethodCredentialRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updatePaymentMethodCredential(PaymentMethodCredentialRequest $request): JsonResponse
    {
        $res = (new PaymentMethodRepository)->updatePaymentMethodCredential($request);

        if ($res) {
            return response()->json([
                'success' => true,
            ]);
        } else {
            return response()->json(
                [
                    'success' => false,
                ]
            );
        }
    }

    /**
     * payment process
     */
    public function createPayment(Request $request, $payment_method)
    {
        if ($request->has('success') && $request['success'] == 'failed') {
            return view('plugin/saas::payments.errors.payment_failed')->with(['gateway' => $payment_method]);
        }
        if (session()->has('payment_type') && session()->has('payable_amount')) {
            if ($payment_method == 'stripe') {
                return (new \Plugin\Saas\Http\Controllers\Payment\StripeController)->index();
            }

            if ($payment_method == 'paypal') {
                return (new \Plugin\Saas\Http\Controllers\Payment\PaypalController)->index();
            }

            if ($payment_method == 'paddle') {
                return (new \Plugin\Saas\Http\Controllers\Payment\PaddleController)->index();
            }

            if ($payment_method == 'sslcommerz') {
                return (new \Plugin\Saas\Http\Controllers\Payment\SSLCommerzController)->index();
            }

            if ($payment_method == 'paystack') {
                return (new \Plugin\Saas\Http\Controllers\Payment\PaystackController)->index();
            }

            if ($payment_method == 'razorpay') {
                return (new \Plugin\Saas\Http\Controllers\Payment\RazorpayController)->index();
            }

            if ($payment_method == 'mollie') {
                return (new \Plugin\Saas\Http\Controllers\Payment\MollieController)->index();
            }

            if ($payment_method == 'gpay') {
                return (new \Plugin\Saas\Http\Controllers\Payment\GpayController)->index();
            }

            if (isActivePluging('wipay-and-powerpay-saas')) {
                if ($payment_method == 'wipay') {
                    return (new \Plugin\WipayAndPowerpaySaas\Http\Controllers\WipayController)->index();
                }

                if ($payment_method == 'powertranzpay') {
                    return (new \Plugin\WipayAndPowerpaySaas\Http\Controllers\PowertranzpayController)->index();
                }
            }
            if ($payment_method == 'avariamoney' && isActivePluging('avariamoney')) {
                return (new \Plugin\Avariamoney\Http\Controllers\AvariamoneyController)->index();
            }

            if ($payment_method == 'paymob') {
                return (new \Plugin\Saas\Http\Controllers\Payment\PaymobController)->index();
            }

            if ($payment_method == 'mercado-pago') {
                return (new \Plugin\Saas\Http\Controllers\Payment\MercadoPagoController)->index();
            }

            if ($payment_method == 'bkash') {
                return (new \Plugin\Saas\Http\Controllers\Payment\BkashController)->index();
            }

            return redirect('/404');
        } else {
            return redirect('/404');
        }
    }

    /**
     * Payment unsuccessful
     */
    public function payment_failed()
    {
        if (!session()->get('is_for_update')) {
            $this->delete_tenant();
        }
        toastNotification('error', 'Payment Failed. Please try again', 'Error');
        $redirect_url = session()->get('redirect_url') != null ? session()->get('redirect_url') : '/';
        $this->clear_payment_session();
        return redirect()->to($redirect_url);
    }

    /**
     * Payment cancel
     */
    public function payment_cancel()
    {
        if (!session()->get('is_for_update')) {
            $this->delete_tenant();
        }

        toastNotification('error', 'Payment Canceled. Please try again', 'Error');
        $redirect_url = session()->get('redirect_url') != null ? session()->get('redirect_url') : '/';
        $this->clear_payment_session();
        return redirect()->to($redirect_url);
    }

    public function delete_tenant()
    {
        try {
            $tenant = session()->get('tenant');
            if ($tenant != null) {
                $tenant->delete();
            }
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Clear Payment session
     */
    public function clear_payment_session(): void
    {
        session()->forget('payment_type');
        session()->forget('payable_amount');
        session()->forget('payment_method');
        session()->forget('payment_method_id');
        session()->forget('redirect_url');

        session()->forget('name');
        session()->forget('email');
        session()->forget('phone');
        session()->forget('country');
        session()->forget('state');
        session()->forget('city');
        session()->forget('address');
        session()->forget('coupon_code');
        session()->forget('primary_amount');
        session()->forget('discount_amount');
        session()->forget('package_id');
        session()->forget('plan_id');
        session()->forget('currency');
        session()->forget('store_id');
        session()->forget('store_name');
        session()->forget('tenant');
        session()->forget('is_for_update');
    }

    /**
     * Payment success
     */
    public function payment_success($payment_info = null): RedirectResponse
    {
        try {
            DB::beginTransaction();
            $sub_repo = new SubscriptionRepository();

            $plan = DB::table('tl_saas_package_plans')
                ->where('id', '=', session()->get('plan_id'))
                ->first();

            $currentDate = Carbon::now();

            $package_details = [
                'package_id' => session()->get('package_id'),
                'plan_id' => session()->get('plan_id'),
                'membership_type' => 'member',
                'valid_till' => $currentDate->addDays((int)$plan->duration)->format("Y-m-d"),
                'store_name' => session()->get('store_name')
            ];
            $user = Auth::user();
            $tenant = session()->get('tenant');
            $saas_account = null;

            //handle saas account
            if (session()->get('is_for_update')) {
                $saas_account = $sub_repo->storeSaasAccountDetails($package_details, $user, $tenant, session()->get('store_id'), 1);
            } else {
                $saas_account = $sub_repo->storeSaasAccountDetails($package_details, $user, $tenant, default_currency: session()->get('default_currency'), default_language: session()->get('default_language'));
            }

            $package = Package::find((int)session()->get('package_id'));

            // Concatenate the saas_account_id, plan_id, and current_date
            $current_date = date('Ymd');
            $concatenated_string = 'SU' . $saas_account->id . 'PA' . session()->get('package_id') . 'PL' . session()->get('plan_id') . 'D' . $current_date;
            $pid = $concatenated_string;

            //Store payment info
            $payment = new PaymentHistory();

            $payment->pid = $pid;
            $payment->saas_account_id = $saas_account->id;
            $payment->user_id = Auth::user()->id;
            $payment->title = "Subscribed to " . $package->name . "'s " . $plan->name . ' plan';
            $payment->name = session()->get('name');
            $payment->email = session()->get('email');
            $payment->phone = session()->get('phone');

            $payment->country = session()->get('country');
            $payment->state = session()->get('state');
            $payment->city = session()->get('city');
            $payment->address = session()->get('address');
            $payment->method = session()->get('payment_method');
            $payment->currency = session()->get('currency')->code;
            $payment->coupon_code = session()->get('coupon_code');
            $payment->primary_amount = session()->get('primary_amount') != null ? session()->get('primary_amount') : 0;
            $payment->discount_amount = session()->get('discount_amount') != null ? session()->get('discount_amount') : 0;
            $payment->final_amount = session()->get('payable_amount') != null ? session()->get('payable_amount') : 0;
            $payment->package_id = session()->get('package_id');
            $payment->plan = session()->get('plan_id');
            $payment->status = 'paid';
            $payment->payment_info = $payment_info;
            $payment->save();

            //Handle Coupon Management
            if (session()->has('coupon_code')) {
                $coupon_repository = new CouponRepository();
                $coupon_repository->updateCouponUsedInfo(session()->get('coupon_code'));
            }

            //Handle Notification
            $notification_service = new SaasNotification();
            if (!session()->get('is_for_update')) {
                $notification_service->newSubscriptionNotificationToSubscriber($saas_account->id);
                $notification_service->newSubscriptionNotificationAdmin($saas_account->id);
            } else {
                $notification_service->changeSubscriptionNotificationToSubscriber($saas_account->id);
                $notification_service->changeSubscriptionNotificationToAdmin($saas_account->id);
            }

            DB::commit();

            //handle database
            $repo = new TenantRepository();
            if (!session()->get('is_for_update')) {
                $repo->createOrUpdateSingleTenantDatabase($saas_account->tenant_id, $package->id, $saas_account->id, 0);
            } else {
                $repo->createOrUpdateSingleTenantDatabase(null, $package->id, $saas_account->id, 1);
            }

            $this->clear_payment_session();
            toastNotification('success', translate('You have successfully subscribed to ' . $package->name . ' !'));
            return redirect()->route('plugin.saas.user.dashboard');
        } catch (\Exception $e) {
            DB::rollback();
            toastNotification('error', 'Payment success. External error occurred', 'Error');
            return redirect('/');
        }
    }
}
