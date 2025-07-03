<?php

namespace Plugin\TlcommerceCore\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Validation\ValidationException;
use Plugin\TlcommerceCore\Models\Customers;
use Plugin\TlcommerceCore\Repositories\OrderRepository;
use Plugin\TlcommerceCore\Http\Requests\CustomerRequest;
use Plugin\TlcommerceCore\Http\Resources\OrderCollection;
use Plugin\TlcommerceCore\Repositories\CustomerRepository;
use Plugin\TlcommerceCore\Http\Requests\CustomerLoginRequest;
use Plugin\TlcommerceCore\Http\Resources\NotificationCollection;
use Plugin\TlcommerceCore\Http\Resources\SingleCustomerCollection;
use Plugin\TlcommerceCore\Http\Requests\CustomerBasicUpdateRequest;
use Plugin\TlcommerceCore\Http\Requests\CustomerResetPasswordRequest;
use Plugin\TlcommerceCore\Http\Requests\CustomerForgotPasswordRequest;

class CustomerController extends Controller
{

    protected $customer_repository;
    protected $order_repository;

    public function __construct(CustomerRepository $customer_repository, OrderRepository $order_repository)
    {
        $this->customer_repository = $customer_repository;
        $this->order_repository = $order_repository;
    }
    /**
     * Will return customer dashboard details
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function customerDashboardDetails()
    {
        try {
            $dasboard_content = $this->customer_repository->customerDashboardDetails(auth('jwt-customer')->user()->id);
            $latest_purchases = new OrderCollection($this->order_repository->customerLatestOrders(auth('jwt-customer')->user()->id));
            return response()->json(
                [
                    'success' => true,
                    'dasboard_content' => $dasboard_content,
                    'latest_purchases' => $latest_purchases
                ]
            );
        } catch (\Exception $e) {
            return response()->json(
                [
                    'success' => false,
                ]
            );
        } catch (\Error $e) {
            return response()->json(
                [
                    'success' => false
                ]
            );
        }
    }
    /**
     * Will return customer summary
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function customerSummary()
    {
        try {
            $dasboard_content = $this->customer_repository->customerDashboardDetails(auth('jwt-customer')->user()->id);
            return response()->json(
                [
                    'success' => true,
                    'dasboard_content' => $dasboard_content,
                ]
            );
        } catch (\Exception $e) {
            return response()->json(
                [
                    'success' => false,
                ]
            );
        } catch (\Error $e) {
            return response()->json(
                [
                    'success' => false
                ]
            );
        }
    }

    /**
     * Will redirect customer basic information
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function customerBasicInfo()
    {
        try {
            $info = new SingleCustomerCollection(auth('jwt-customer')->user());
            return response()->json(
                [
                    'success' => true,
                    'info' => $info
                ]
            );
        } catch (\Exception $e) {
            return response()->json(
                [
                    'success' => false,
                ]
            );
        }
    }
    /**
     * Will update customer basic data
     * 
     * @param CustomerBasicUpdateRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateCustomerBasicInfo(CustomerBasicUpdateRequest $request)
    {
        $customer = $this->customer_repository->updateCustomerBasicInfo($request, auth('jwt-customer')->user()->id);
        if ($customer != NULL) {
            return response()->json(
                [
                    'success' => true,
                    'customer' => new SingleCustomerCollection($customer)
                ]
            );
        } else {
            return response()->json(
                [
                    'success' => false
                ]
            );
        }
    }
    /**
     * Will complete customer registration 
     * 
     * @param CustomerRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function customerRegistration(CustomerRequest $request)
    {
        $customer = $this->customer_repository->customerRegister($request);
        if ($customer != NULL) {
            return response()->json(
                [
                    'success' => true,
                    'customer' => $customer
                ]
            );
        } else {
            return response()->json(
                [
                    'success' => false
                ]
            );
        }
    }
    /**
     * Will verify customer email address
     * 
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function verifyCustomerEmail(Request $request)
    {
        $customer = $this->customer_repository->verifyCustomerEmail($request['identifier']);
        if ($customer != NULL) {
            return response()->json(
                [
                    'success' => true,
                ]
            );
        } else {
            return response()->json(
                [
                    'success' => false,
                ]
            );
        }
    }
    /**
     * Will generate customer forgot password link
     * 
     * @param CustomerForgotPasswordRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function customerForgotPassword(CustomerForgotPasswordRequest $request)
    {
        $res = $this->customer_repository->customerResetPasswordLink($request['email']);
        if ($res != NULL) {
            return response()->json(
                [
                    'success' => true,
                ]
            );
        } else {
            return response()->json(
                [
                    'success' => false
                ]
            );
        }
    }
    /**
     * Will generate customer forgot password link
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function customerEmailResetLink(Request $request)
    {
        $email = auth('jwt-customer')->user()->email;
        $res = $this->customer_repository->customerResetEmailLink($email);
        if ($res != NULL) {
            return response()->json(
                [
                    'success' => true,
                ]
            );
        } else {
            return response()->json(
                [
                    'success' => false
                ]
            );
        }
    }
    /**
     * Will verify customer reset password token
     * 
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function VerifyCustomerResetPasswordToken(Request $request)
    {
        $res = $this->customer_repository->verifyCustomerResetPasswordToken($request);
        if ($res) {
            return response()->json(
                [
                    'success' => true,
                ]
            );
        } else {
            return response()->json(
                [
                    'success' => false
                ]
            );
        }
    }
    /**
     * Will reset customer password
     * 
     * @param CustomerResetPasswordRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function customerResetPassword(CustomerResetPasswordRequest $request)
    {
        $res = $this->customer_repository->customerResetPassword($request);
        if ($res) {
            return response()->json(
                [
                    'success' => true,
                ]
            );
        } else {
            return response()->json(
                [
                    'success' => false
                ]
            );
        }
    }
    /**
     * Will reset customer email
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function customerResetEmail(Request $request)
    {
        $res = $this->customer_repository->customerResetEmail($request);
        if ($res) {
            return response()->json(
                [
                    'success' => true,
                ]
            );
        } else {
            return response()->json(
                [
                    'success' => false
                ]
            );
        }
    }
    /**
     * Get a JWT via given credentials.
     *
     * @param CustomerLoginRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function customerLogin(CustomerLoginRequest $request)
    {
        $data = [
            'email' => $request['email'],
            'password' => $request['password'],
            'status' => config('settings.general_status.active')
        ];
        $customer = Customers::where('email', $request['email'])->first();

        if ($customer == null) {
            throw ValidationException::withMessages(
                [
                    'email' => [translate('No account found associate this email', session()->get('api_locale'))]
                ]
            );
        }

        if ($customer != null && $customer->status != config('settings.general_status.active')) {
            return response()->json(
                [
                    'success' => false,
                    'message' => translate('Your account is inactive', session()->get('api_locale'))
                ]
            );
        }


        $token = auth('jwt-customer')->attempt($data);
        if (!$token) {
            return response()->json(
                [
                    'success' => false,
                    'message' => translate('Invalid email or password', session()->get('api_locale'))
                ]
            );
        }
        return $this->createNewToken($token, true);
    }
    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function customerLogout()
    {
        try {
            auth('jwt-customer')->logout();
            return response()->json(
                [
                    'success' => true
                ]
            );
        } catch (\Exception $e) {
            return response()->json(
                [
                    'success' => false
                ]
            );
        }
    }
    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        try {
            if (auth('jwt-customer')->user()) {
                return $this->createNewToken(null, false);
            }
            return $this->createNewToken(auth('jwt-customer')->refresh(), true);
        } catch (\Exception $e) {
            return response()->json(
                [
                    'success' => false,
                ]
            );
        }
    }
    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function userProfile()
    {
        return response()->json(auth('jwt-customer')->user());
    }
    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function createNewToken($token, $token_refresh)
    {
        if (auth('jwt-customer')->user()->status == config('settings.general_status.active')) {
            return response()->json([
                'success' => true,
                'access_token' => $token,
                'token_refresh' => $token_refresh,
                'token_type' => 'bearer',
                'expires_in' => auth('jwt-customer')->factory()->getTTL() * 60,
                'user' => new SingleCustomerCollection(auth('jwt-customer')->user()),
                'dashboard_content' => $this->customer_repository->customerDashboardDetails(auth('jwt-customer')->user()->id),
                'notifications' => new NotificationCollection(Customers::find(auth('jwt-customer')->user()->id)->unreadNotifications)
            ]);
        }
        return response()->json([
            'success' => false,
        ]);
    }
}
