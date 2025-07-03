<?php

use Illuminate\Support\Facades\Route;
use Plugin\Saas\Repositories\SettingsRepository;
use App\Http\Controllers\Auth\VerificationController;
use Plugin\Saas\Http\Controllers\User\UserController;
use Plugin\Saas\Http\Controllers\User\StoreController;
use Plugin\Saas\Http\Controllers\Payment\GpayController;
use Plugin\Saas\Http\Controllers\Admin\PackageController;
use Plugin\Saas\Http\Controllers\Payment\WipayController;
use Plugin\Saas\Http\Controllers\Payment\MollieController;
use Plugin\Saas\Http\Controllers\Payment\PaddleController;
use Plugin\Saas\Http\Controllers\Payment\PaymobController;
use Plugin\Saas\Http\Controllers\Payment\PaypalController;
use Plugin\Saas\Http\Controllers\Payment\StripeController;
use Plugin\Saas\Http\Controllers\User\DashboardController;
use Plugin\Saas\Http\Controllers\Payment\PaymentController;
use Plugin\Saas\Http\Controllers\Payment\PaystackController;
use Plugin\Saas\Http\Controllers\Payment\RazorpayController;
use Plugin\Saas\Http\Controllers\User\CustomDomainController;
use Plugin\Saas\Http\Controllers\User\SubscriptionController;
use Plugin\Saas\Http\Controllers\Payment\SSLCommerzController;
use Plugin\Saas\Http\Controllers\Payment\MercadoPagoController;
use Plugin\Saas\Http\Controllers\Payment\PowertranzpayController;
use Plugin\Saas\Http\Controllers\Payment\BkashController;

Route::group(['prefix' => getSaasPrefix(), 'middleware' => ['handle.expired.account']], function () {
    Route::get('/registration', [UserController::class, 'register'])->name('plugin.saas.user.registration');
    Route::post('/registration', [UserController::class, 'storeUserDetails'])->name('plugin.saas.user.registration');

    Route::get('/login', [UserController::class, 'login'])->name('subscriber.login');
    Route::post('/login', [UserController::class, 'attemptLogin'])->name('subscriber.attemptLogin');


    Route::get('/password-reset-link-form', [UserController::class, 'passwordResetLink'])->name('subscriber.password.reset.link');
    Route::post('email-reset-password-link', [UserController::class, 'emailResetPasswordLink'])->name('subscriber.email.reset.password.link');
    Route::get('reset-password/{token}', [UserController::class, 'resetPassword'])->name('subscriber.reset.password');
    Route::post('reset-your-password', [UserController::class, 'resetPasswordPost'])->name('subscriber.reset.password.post');

    Route::get('/email/verify', [VerificationController::class, 'show'])->name('verification.notice');
    Route::post('/email/verify/resend', [VerificationController::class, 'resend'])->name('verification.resend');
    Route::get('/email/verify/{id}/{hash}', [VerificationController::class, 'verify'])->name('verification.verify');

    Route::post('/get-packages-according-to-plan', [PackageController::class, 'getPackageAccordingToPlanForFrontend'])
        ->name('plugin.saas.get.packages.according.to.plan.frontend');

    $applied_middleware = ['admin.subscriber.separation:4'];
    if (env('IS_USER_REGISTERED') == 1) {
        $email_verification = SettingsRepository::getSaasSetting('email_verification');

        if ($email_verification == 1) {
            array_push($applied_middleware, 'verified');
        }
    }

    Route::group(['middleware' => $applied_middleware], function () {

        Route::get('profile', [UserController::class, 'profile'])->name('subscriber.profile');
        Route::post('update-profile', [UserController::class, 'updateProfile'])->name('subscriber.update.profile')->middleware('demo');
        Route::get('dashboard', [DashboardController::class, 'saasDashboard'])->name('plugin.saas.user.dashboard');

        Route::get('plan-order/{package}/{plan?}/{is_trial?}/{store?}', [StoreController::class, 'orderAPlan'])->name('plugin.saas.user.order.plan');
        Route::post('confirm-plan-order', [StoreController::class, 'confirmPlanOrder'])->name('plugin.saas.user.create.store.plan.buy');


        Route::get('stores', [StoreController::class, 'index'])->name('plugin.saas.user.stores');
        Route::get('store-details/{store_id}', [StoreController::class, 'storeDetails'])->name('plugin.saas.user.store.details');
        Route::get('print-subscription-payment-invoice/{store_id}', [StoreController::class, 'printInvoice'])->name('plugin.saas.print.subscription.payment.invoice');
        Route::post('update-store', [StoreController::class, 'updateStore'])->name('plugin.saas.update.store');


        //Subscription Routes
        Route::post('get-states-of-country', [SubscriptionController::class, 'getStatesOfCountry'])->name('plugin.saas.get.states.of.country');
        Route::post('get-cities-of-state', [SubscriptionController::class, 'getCitiesOfState'])->name('plugin.saas.get.cities.of.state');

        Route::get('change-subscription-plan/{store_id}', [SubscriptionController::class, 'changeSubscriptionPlan'])->name('plugin.saas.change.subscription.plan');
        Route::get('subscribe-now', [SubscriptionController::class, 'subscribeNow'])->name('plugin.saas.subscribe.now');
        Route::post('apply-coupon', [SubscriptionController::class, 'applyCoupon'])->name('plugin.saas.apply.coupon');
        Route::post('make-payment', [SubscriptionController::class, 'makePayment'])->name('plugin.saas.make.payment');
        Route::get('payment/{id}/pay', [PaymentController::class, 'createPayment']);

        //Redeem Coupon
        Route::get('redeem-coupon', [SubscriptionController::class, 'redeemCoupon'])->name('plugin.saas.redeem.coupon');
        Route::post('apply-redeem-coupon', [SubscriptionController::class, 'applyRedeemCoupon'])->name('plugin.saas.apply.redeem.coupon');

        //Custom Domains
        Route::get('custom-domain', [CustomDomainController::class, 'customDomain'])->name('plugin.saas.custom.domain');
        Route::post('request-custom-domain', [CustomDomainController::class, 'requestCustomDomain'])->name('plugin.saas.request.custom.domain');

        //Transaction History
        Route::get('payment-history', [SubscriptionController::class, 'paymentHistory'])->name('plugin.saas.payment.history');


        /**
         * Stripe payment
         */
        Route::any('/stripe/create-session', [StripeController::class, 'create_checkout_session'])->name('plugin.saas.stripe.generate.token');
        Route::get('/stripe/success', [StripeController::class, 'success'])->name('plugin.saas.stripe.success.payment');
        Route::get('/stripe/cancel', [StripeController::class, 'cancel'])->name('plugin.saas.stripe.cancel.payment');

        /**
         * Paypal payment
         */
        Route::get('/paypal/success', [PaypalController::class, 'success'])->name('plugin.saas.paypal.success');
        Route::get('/paypal/cancel', [PaypalController::class, 'cancel'])->name('plugin.saas.paypal.cancel');

        /**
         * paddle payment
         */
        Route::any('/paddle/success', [PaddleController::class, 'paddleSuccess'])->name('plugin.saas.paddle.payment.success');
        Route::any('/paddle/return', [PaddleController::class, 'paddleReturn'])->name('plugin.saas.paddle.payment.return');


        //Paystack
        Route::get('/pay/callback', [PaystackController::class, 'callback'])->name('plugin.saas.pay.callback');

        //Razorpay
        Route::post('/razorpay-payment-submit', [RazorpayController::class, 'paymentStatus'])->name('plugin.saas.razorpay.payment.submit');

        //Mollie
        Route::get('/payment-callback', [MollieController::class, 'paymentCallback'])->name('payment.callback');
        Route::get('/payment-webhook', [MollieController::class, 'paymentWebhook'])->name('payment.webhook');

        //Google pay
        Route::post('/googlepay-payment-submit', [GpayController::class, 'googlepayPaymentSubmit'])->name('googlepay.payment.submit');

        Route::get('/wipay-payment-submit', [WipayController::class, 'completePayment'])->name('wipay.payment.submit');

        Route::post('/powertranzpay-payment-submit', [PowertranzpayController::class, 'submit'])->name('powertranzpay.payment.submit');
    });

    /**
     * SSLCommerz payment
     */
    Route::any('/ssl-commerce/success', [SSLCommerzController::class, 'success'])->name('plugin.saas.sslcommerz.success.payment');
    Route::any('/ssl-commerce/cancel', [SSLCommerzController::class, 'cancel'])->name('plugin.saas.sslcommerz.cancel.payment');
    Route::any('/ssl-commerce/fail', [SSLCommerzController::class, 'fail'])->name('plugin.saas.sslcommerz.fail.payment');

    Route::post('/powertranzpay-payment-finalize', [PowertranzpayController::class, 'finalize'])->name('powertranzpay.payment.finalize');

    //Paymob 
    Route::get('/paymob/callback', [PaymobController::class, 'callback'])->name('plugin.saas.paymob.callback');

    //Mercado Pago
    Route::get('/payment/mercadopago/success', [MercadoPagoController::class, 'success'])->name('mercadopago.payment.success');
    Route::get('/payment/mercadopago/failure', [MercadoPagoController::class, 'failure'])->name('mercadopago.payment.failure');
    Route::get('/payment/mercadopago/pending', [MercadoPagoController::class, 'pending'])->name('mercadopago.payment.pending');

    //bKash
    Route::post('/bkash/get-token', [BkashController::class, 'getToken'])->name('plugin.saas.bkash.get.token');
    Route::post('/bkash/create-payment', [BkashController::class, 'createPayment'])->name('plugin.saas.bkash.create.payment');
    Route::post('/bkash/execute-payment', [BkashController::class, 'executePayment'])->name('plugin.saas.bkash.execute.payment');
    Route::get('/bkash/callback', [BkashController::class, 'callback'])->name('plugin.saas.bkash.callback');
    Route::get('/bkash/success', [BkashController::class, 'success'])->name('plugin.saas.bkash.success.payment');
    Route::get('/bkash/cancel', [BkashController::class, 'cancel'])->name('plugin.saas.bkash.cancel.payment');
});
