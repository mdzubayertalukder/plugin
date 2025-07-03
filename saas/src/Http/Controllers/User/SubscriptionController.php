<?php

namespace Plugin\Saas\Http\Controllers\User;


use Exception;
use App\Models\Tenant;
use Illuminate\View\View;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Plugin\Saas\Models\Package;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Plugin\Saas\Models\SaasAccount;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Plugin\Saas\Models\PaymentMethods;
use Illuminate\Support\Facades\Validator;
use Plugin\Saas\Services\SaasNotification;
use Plugin\Saas\Repositories\CouponRepository;
use Plugin\Saas\Repositories\TenantRepository;
use Plugin\Saas\Repositories\PackageRepository;
use Plugin\Saas\Repositories\CurrencyRepository;
use Plugin\Saas\Repositories\LocationRepository;
use Plugin\Saas\Repositories\SubscriptionRepository;
use Plugin\Saas\Repositories\PaymentMethodRepository;
use Plugin\Saas\Repositories\StoreRepository;

class SubscriptionController extends Controller
{
    public function __construct(
        protected PaymentMethodRepository $payment_method_repository,
        protected SubscriptionRepository $subscription_repository,
        protected LocationRepository $locationRepository,
        protected TenantRepository $tenantRepository,
        protected PackageRepository $packageRepository,
        protected StoreRepository $storeRepository
    ) {}

    /**
     * Will redirect to subscription form
     */
    public function subscribeNow(): View
    {
        $data = [
            DB::raw('GROUP_CONCAT(DISTINCT(tl_saas_package_plans.id)) as id'),
            DB::raw('GROUP_CONCAT(DISTINCT(tl_saas_package_plans.name)) as name'),
            DB::raw('GROUP_CONCAT(DISTINCT(tl_saas_package_plans.duration)) as duration')
        ];

        $package_plans = $this->packageRepository->getPackagePlans([], $data);
        $package_plans = $package_plans->toArray();
        usort($package_plans, function ($a, $b) {
            return $a->duration - $b->duration;
        });

        $saas_account = DB::table('tl_saas_accounts')
            ->where('user_id', '=', Auth::user()->id);

        $first_plan = -1;
        if ($saas_account->exists() && $saas_account->first()->package_plan != null) {
            $first_plan = $saas_account->first()->package_plan;
        } elseif (sizeof($package_plans) > 0) {
            $first_plan = $package_plans[0]->id;
        }

        return view('plugin/saas::user.panel.subscription.subscribe', compact('package_plans', 'first_plan'));
    }

    /**
     * Will return states of selected country
     */
    public function getStatesOfCountry(Request $request): JsonResponse
    {
        try {
            $all_states = $this->locationRepository->getStatesByCountryId($request['country_id'])->get();

            return response()->json([
                'success' => true,
                'states' => $all_states,
                'message' => translate('Data retrieved successfully')
            ]);
        } catch (Exception $ex) {
            return response()->json([
                'success' => false,
                'message' => translate('Data retrieved unsuccessful')
            ]);
        }
    }

    /**
     * Will return cities of selected state
     */
    public function getCitiesOfState(Request $request): JsonResponse
    {
        try {
            $all_cities = $this->locationRepository->getCitiesByStateId($request['state_id'])->get();

            return response()->json([
                'success' => true,
                'cities' => $all_cities,
                'message' => translate('Data retrieved successfully')
            ]);
        } catch (Exception $ex) {
            return response()->json([
                'success' => false,
                'message' => translate('Data retrieved unsuccessful')
            ]);
        }
    }

    /**
     * Apply coupon
     */
    public function applyCoupon(Request $request)
    {
        try {
            $currentDate = date('Y-m-d');
            $coupon = DB::table('tl_saas_coupons')
                ->join('tl_saas_coupons_of_packages', 'tl_saas_coupons_of_packages.coupon_id', '=', 'tl_saas_coupons.id')
                ->where('tl_saas_coupons.coupon_code', '=', $request['coupon'])
                ->where('tl_saas_coupons_of_packages.package_id', '=', $request['package'])
                ->where('valid_till', '>=', $currentDate)
                ->select([
                    'discount',
                    'total_used',
                    'coupon_usable_times'
                ])->first();

            if (!empty($coupon) && $coupon->total_used < $coupon->coupon_usable_times) {
                $discount = (float)$coupon->discount;

                $package_price = (float)DB::table('tl_saas_package_has_plans')
                    ->where('package_id', '=', $request['package'])
                    ->where('plan_id', '=', $request['plan_id'])
                    ->first()->cost;

                $discount_amount = ($discount * $package_price) / 100;
                $total = (float)$package_price - $discount_amount;

                $summery = [
                    'discount_amount' => currencyExchange($discount_amount),
                    'discount_amount_value' => $discount_amount,
                    'total' => currencyExchange($total),
                    'total_value' => $total,
                ];

                return response()->json([
                    'success' => true,
                    'summery' => $summery,
                    'message' => translate('Price after applying coupon')
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => translate('This coupon is not applicable here')
            ]);
        } catch (Exception $ex) {
            return response()->json([
                'success' => false,
                'message' => translate('This coupon is not applicable here')
            ]);
        }
    }

    /**
     * Make payment
     */
    public function makePayment(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required',
            'phone' => 'required',
            'country' => 'nullable|exists:tl_countries,id',
            'state' => 'nullable|exists:tl_states,id',
            'city' => 'nullable|exists:tl_cities,id',
            'address' => 'required',
            'payment_method' => 'required|exists:tl_saas_payment_methods,id'
        ]);

        if (!$validator->fails() && $request['store_id'] == "null") {
            $validator = Validator::make($request->all(), [
                'store_name' => 'required|unique:tl_saas_accounts,store_name',
            ]);
        }

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            DB::beginTransaction();
            $saas_account_id = $request['store_id'];
            $tenant = null;

            if ($saas_account_id == "null") {
                $tenant = $this->tenantRepository->createNewTenant($request);
            } else {
                $saas_account = SaasAccount::find($request['store_id']);
                $tenant = Tenant::where('id', $saas_account->tenant_id)->first();
            }


            $payment_method = PaymentMethods::find($request['payment_method']);
            $base_url = url('/');
            $url = $base_url . '/' . getSaasPrefix() . '/payment' . '/' . Str::slug($payment_method->name) . '/pay';
            $currency_repository = new CurrencyRepository;
            $saas_default_currency = $currency_repository->getSaasCurrency();

            session()->put('payment_type', 'checkout');
            session()->put('payable_amount', $request['amount']);
            session()->put('payment_method', $payment_method->name);
            session()->put('payment_method_id', $payment_method->id);
            session()->put('redirect_url', $url);

            session()->put('name', $request['name']);
            session()->put('email', $request['email']);
            session()->put('phone', $request['phone']);
            session()->put('country', isset($request['country']) ? $request['country'] : null);
            session()->put('state', isset($request['state']) ? $request['state'] : null);
            session()->put('city', isset($request['city']) ? $request['city'] : null);
            session()->put('address', $request['address']);
            session()->put('coupon_code', $request['coupon_code']);
            session()->put('primary_amount', $request['primary_amount']);
            session()->put('discount_amount', $request['discount_amount']);
            session()->put('package_id', $request['package_id']);
            session()->put('plan_id', $request['plan_id']);
            session()->put('currency', $saas_default_currency);
            session()->put('store_id', $saas_account_id);
            session()->put('store_name', $request['store_name']);
            session()->put('tenant', $tenant);
            session()->put('is_for_update', $saas_account_id == "null" ? false : true);
            session()->put('redirect_url', route('plugin.saas.user.dashboard'));
            DB::commit();

            return response()->json(
                [
                    'success' => true,
                    'response_url' => $url
                ]
            );
        } catch (Exception $ex) {
            DB::rollBack();
            return response()->json(
                [
                    'success' => false,
                ]
            );
        }
    }

    /**
     * redirect to redeem coupon page
     */
    public function redeemCoupon()
    {
        return view('plugin/saas::user.panel.subscription.redeem_coupon');
    }

    /**
     * Apply redeem coupon
     */
    public function applyRedeemCoupon(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'coupon_code' => 'required|exists:tl_saas_coupons,coupon_code',
            'store_name' => 'required|unique:tl_saas_accounts,store_name',
        ]);

        if ($validator->fails()) {
            toastNotification('error', translate('Invalid coupon!'));
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        if (!$this->storeRepository->isValidStoreName($request['store_name'])) {
            toastNotification('error', translate('Invalid/Duplicate store name!'));
            return back();
        }

        try {
            $currentDate = date('Y-m-d');
            $coupon = DB::table('tl_saas_coupons')
                ->where('coupon_code', '=', $request['coupon_code'])
                ->where('coupon_type', '=', 'redeemable')
                ->where('valid_till', '>=', $currentDate)
                ->select([
                    'id',
                    'total_used',
                    'coupon_usable_times',
                    'coupon_code'
                ])->first();

            if (empty($coupon) || $coupon->total_used >= $coupon->coupon_usable_times) {
                toastNotification('error', translate('Invalid coupon!'));
                return back();
            }

            $package_repository = new PackageRepository();
            $package_id = $package_repository->getPackageByCouponIdAndType($coupon->id, 'redeemable');

            $package = Package::find((int)$package_id);

            $user = Auth::user();
            $package_details = [
                'package_id' => $package_id,
                'plan_id' => config('saas.plans.lifetime'),
                'membership_type' => 'member',
                'valid_till' => null,
                'store_name' => $request['store_name']
            ];

            DB::beginTransaction();
            $tenant = $this->tenantRepository->createNewTenant($request);
            $saas_account = $this->subscription_repository->storeSaasAccountDetails($package_details, $user, $tenant);


            session()->put('tenant_id', $tenant->id);
            session()->put('user_id', $user->id);

            $request = [
                'package_id' => $package_id,
                'plan_id' => config('saas.plans.lifetime'),
                'membership_type' => 'member',
            ];
            $coupon_repository = new CouponRepository();
            $coupon_repository->updateCouponUsedInfo($coupon->coupon_code);
            DB::commit();

            $this->tenantRepository->createOrUpdateSingleTenantDatabase($saas_account->tenant_id, $package->id, $saas_account->id, 0);

            //handle notification
            $notification_service = new SaasNotification();
            $notification_service->newSubscriptionNotificationToSubscriber($saas_account->id);
            $notification_service->newSubscriptionNotificationAdmin($saas_account->id);

            toastNotification('success', translate('You have successfully subscribed to ' . $package->name . ' for lifetime !'));
            return redirect()->route('plugin.saas.user.dashboard');
        } catch (Exception $ex) {
            $error = [
                'message' => 'Error occurred during redeem coupon to create new store',
                'data' => request()->all(),
                'error' => $ex
            ];
            Log::channel('tenant_database')->info(json_encode($error));

            DB::rollBack();
            toastNotification('error', translate('Redeem unsuccessful!'));
            return back();
        }
    }

    /**
     * Fetch payment history
     */
    public function paymentHistory()
    {
        try {
            $payment_history = DB::table('tl_saas_payment_histories')
                ->where('user_id', '=', Auth::user()->id)
                ->orderBy('id', 'desc')
                ->select([
                    'title',
                    'method',
                    'coupon_code',
                    'currency',
                    'discount_amount',
                    'final_amount',
                    'updated_at',
                    'pid',
                    'saas_account_id as store_id'
                ])->get();

            return view('plugin/saas::user.panel.subscription.payment_history', compact('payment_history'));
        } catch (Exception $ex) {
            toastNotification('error', translate('Unable to fetch payment history!'));
            return back();
        }
    }

    /**
     * Will redirect to subscription plan changing page
     */
    public function changeSubscriptionPlan($store_id)
    {
        try {
            $data = [
                DB::raw('GROUP_CONCAT(DISTINCT(tl_saas_package_plans.id)) as id'),
                DB::raw('GROUP_CONCAT(DISTINCT(tl_saas_package_plans.name)) as name'),
                DB::raw('GROUP_CONCAT(DISTINCT(tl_saas_package_plans.duration)) as duration')
            ];

            $package_plans = $this->packageRepository->getPackagePlans([], $data);

            $saas_account = DB::table('tl_saas_accounts')
                ->where('id', '=', $store_id);

            $first_plan = -1;
            if ($saas_account->exists() && $saas_account->first()->package_plan != null) {
                $first_plan = $saas_account->first()->package_plan;
            } elseif (sizeof($package_plans) > 0) {
                $first_plan = $package_plans[0]->id;
            }

            return view('plugin/saas::user.panel.subscription.subscribe', compact('package_plans', 'first_plan', 'store_id'));
        } catch (Exception $ex) {
            toastNotification('error', translate('Unable to fetch package info !'));
            return back();
        }
    }
}
