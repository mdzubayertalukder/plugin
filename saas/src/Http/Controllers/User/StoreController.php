<?php

namespace Plugin\Saas\Http\Controllers\User;

use BPDF;
use Exception;
use Carbon\Carbon;
use Illuminate\View\View;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Plugin\Saas\Models\Package;
use Plugin\Saas\Models\Currency;
use Illuminate\Support\Facades\DB;
use Plugin\Saas\Models\PackagePlan;
use Plugin\Saas\Models\SaasAccount;
use App\Http\Controllers\Controller;
use Core\Models\Language;
use Plugin\Saas\Models\PackagePrice;
use Illuminate\Http\RedirectResponse;
use Plugin\Saas\Models\PaymentMethods;
use Plugin\Saas\Services\SaasNotification;
use Plugin\Saas\Repositories\StoreRepository;
use Plugin\Saas\Repositories\TenantRepository;
use Plugin\Saas\Repositories\PackageRepository;
use Plugin\Saas\Repositories\CurrencyRepository;
use Plugin\Saas\Repositories\LocationRepository;
use Plugin\Saas\Repositories\SettingsRepository;
use Plugin\Saas\Http\Requests\PackageConfirmRequest;
use Plugin\Saas\Repositories\SubscriptionRepository;
use Plugin\Saas\Repositories\PaymentMethodRepository;

class StoreController extends Controller
{
    public function __construct(
        public SubscriptionRepository $sub_repo,
        protected PaymentMethodRepository $payment_method_repository,
        public TenantRepository $tenantRepository,
        public LocationRepository $locationRepository,
        public PackageRepository $packageRepository,
        public StoreRepository $storeRepository
    ) {}

    /**
     * Will register a plan
     */
    public function orderAPlan($package, $plan = null, $is_trial = 0, $store = null): View
    {
        $package_info = Package::with(['privileges' => function ($q) {
            $q->select(['package_id', 'privileges']);
        }, 'plugins' => function ($qp) {
            $qp->select(['plugin_id', 'package_id']);
        }, 'payment_methods' => function ($qpm) {
            $qpm->select(['package_id', 'payment_method_id']);
        }])
            ->where('id', $package)
            ->select(['id', 'name', 'trail_period', 'type'])
            ->first();

        $plan_info = PackagePlan::where('id', $plan)->first();

        $price_info = PackagePrice::where('package_id', $package)
            ->where('plan_id', $plan)
            ->first();

        if ($package_info == null) {
            abort(404);
        }

        $plugins = availablePluginsForTenant();
        $payment_methods = getTenantPaymentGateways();

        $valid_is_trial = 0;
        if ($is_trial == 1 && $package_info->type == 'paid' && $store == null && $package_info->trail_period > 0) {
            $valid_is_trial = 1;
        }

        $payment_gateways = null;
        $billing_details = null;
        if ($package_info->type == 'paid' && $valid_is_trial == 0) {
            $payment_gateways =  $this->payment_method_repository->paymentMethods(config('settings.general_status.active'));
            $payment_gateways = $payment_gateways->map(function ($item) {
                $logo = \Plugin\Saas\Repositories\PaymentMethodRepository::configKeyValue(config('saas.payment_methods.' . strtolower($item->name)), strtolower($item->name) . '_logo');
                if (!empty($logo)) {
                    $logo = asset(getFilePath($logo));
                }
                $item->logo = $logo;

                return $item;
            });
            $billing_details = DB::table('tl_saas_payment_histories')
                ->where('user_id', '=', auth()->user()->id)
                ->first();
        }

        $store_info = null;
        if ($store != null) {
            $store_info = SaasAccount::findOrFail($store);
        }
        $countries = $this->locationRepository->getAllCountries();
        $languages = Language::where('status', 1)->get();
        $currency_repository = new CurrencyRepository();
        $currencies = $currency_repository->getAllSaasCurrencies();

        return view('plugin/saas::user.plan-order.buy-plan')->with([
            'package_info' => $package_info,
            'plan_info' => $plan_info,
            'price_info' => $price_info,
            'plugins' => $plugins,
            'payment_methods' => $payment_methods,
            'payment_gateways' => $payment_gateways,
            'store_info' => $store_info,
            'valid_is_trial' => $valid_is_trial,
            'countries' => $countries,
            'billing_details' => $billing_details,
            'languages' => $languages,
            'currencies' => $currencies
        ]);
    }

    /**
     * Confirm a plan buy
     */
    public function confirmPlanOrder(PackageConfirmRequest $request): RedirectResponse
    {
        try {
            DB::beginTransaction();
            $package = Package::find($request['package']);

            if ($package == null) {
                abort(404);
            }


            //Create Store or update plan with free package
            if ($package->type == 'free') {
                $saas_account_id = "";
                $tenant = null;
                $is_for_update = 0;

                //Update Store Plan
                if ($request->has('store')) {
                    $is_for_update = 1;
                    $saas_account = SaasAccount::where('id', $request['store'])->first();
                    $saas_account->package_id = $package->id;
                    $saas_account->package_plan = null;
                    $saas_account->membership_type = 'member';
                    $saas_account->valid_till = null;
                    $saas_account->save();
                    $saas_account_id = $saas_account->id;
                    $tenant = $saas_account->tenant;
                }

                //Create New Store
                if (!$request->has('store')) {
                    //check free store limit
                    $total_free_store = DB::table('tl_saas_accounts')->where('package_plan', '=', null)->where('user_id', auth()->user()->id)->count();
                    if ($total_free_store >= \Plugin\Saas\Repositories\SettingsRepository::getSaasSetting('maximum_free_store')) {
                        DB::commit();
                        toastNotification('error', translate('You cannot create another free store as free store creation quota is over !'));
                        return back();
                    }
                    $is_for_update = 0;
                    $tenant = $this->tenantRepository->createNewTenant($request);
                    $request['membership_type'] = 'member';
                    $saas_account_id = $this->sub_repo->createSaasAccountForFreePackage($request, $package, $tenant, $is_for_update, null);
                }


                //Sending Notification
                $notification_service = new SaasNotification();
                if (!$request->has('store')) {
                    $notification_service->newSubscriptionNotificationToSubscriber($saas_account_id);
                    $notification_service->newSubscriptionNotificationAdmin($saas_account_id);
                }

                if ($request->has('store')) {
                    $notification_service->changeSubscriptionNotificationToSubscriber($saas_account_id);
                    $notification_service->changeSubscriptionNotificationToAdmin($saas_account_id);
                }

                DB::commit();
                $this->tenantRepository->createOrUpdateSingleTenantDatabase($tenant->id, $package->id, $saas_account_id, $is_for_update);
                toastNotification('success', translate('You have successfully subscribed to ' . $package->name . ' !'));
                return redirect()->route('plugin.saas.user.dashboard');
            }

            //Create store with trial package
            if ($package->type == 'paid' && $request->has('is_trial')) {
                $plan_info = PackagePlan::findOrFail($request['plan_id']);
                $tenant = $this->tenantRepository->createNewTenant($request);

                $currentDate = Carbon::now();
                $valid_till = $currentDate->addDays((int)$package->trail_period)->format("Y-m-d");

                $package_details = [
                    'package_id' => $package->id,
                    'plan_id' => $plan_info->id,
                    'membership_type' => 'trail user',
                    'valid_till' =>  $valid_till,
                    'store_name' => $request['store_name']
                ];

                $user = auth()->user();
                $saas_account = $this->sub_repo->storeSaasAccountDetails($package_details, $user, $tenant, default_language: $request['default_language'], default_currency: $request['default_currency']);

                //Send notification
                $notification_service = new SaasNotification();
                $notification_service->newSubscriptionNotificationToSubscriber($saas_account->id);
                $notification_service->newSubscriptionNotificationAdmin($saas_account->id);

                DB::commit();

                //handle tenant database
                $this->tenantRepository->createOrUpdateSingleTenantDatabase($tenant->id, $package->id, $saas_account->id, 0);
                toastNotification('success', translate('You have successfully  subscribed to ' . $package->name . ' on trial'));
                return redirect()->route('plugin.saas.user.dashboard');
            }

            //Redirect to payment page
            if ($package->type == 'paid' && !$request->has('is_trial')) {
                $is_for_update = false;
                $saas_account_id = null;
                $tenant = null;

                if ($request->has('store')) {
                    $is_for_update = 1;
                    $saas_account_id = $request['store'];
                    $saas_account = SaasAccount::where('id', $request['store'])->first();
                    $tenant = $saas_account->tenant;
                    session()->put('store_id', $request['store']);
                }

                if ($saas_account_id == null) {
                    $tenant = $this->tenantRepository->createNewTenant($request);
                }

                $payment_method = PaymentMethods::find($request['payment_method']);
                $base_url = url('/');
                $url = $base_url . '/' . getSaasPrefix() . '/payment' . '/' . Str::slug($payment_method->name) . '/pay';

                $currency_repository = new CurrencyRepository;
                $saas_default_currency = $currency_repository->getSaasCurrency();

                session()->put('default_language', $request['default_language']);
                session()->put('default_currency', $request['default_currency']);

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
                session()->put('package_id', $request['package']);
                session()->put('plan_id', $request['plan_id']);
                session()->put('currency', $saas_default_currency);
                session()->put('store_id', $saas_account_id);
                session()->put('store_name', $request['store_name']);
                session()->put('tenant', $tenant);
                session()->put('is_for_update', $is_for_update);

                session()->put('redirect_url', route('plugin.saas.user.dashboard'));

                DB::commit();

                return redirect($url);
            }
        } catch (Exception $ex) {
            DB::rollBack();
            toastNotification('error', 'Package Confirmation fail', 'Error');
            return redirect()->back();
        } catch (Stancl\Tenancy\Exceptions\DomainOccupiedByOtherTenantException $dex) {
            DB::rollBack();
            toastNotification('error', $dex->getMessage(), 'Error');
            return redirect()->back();
        }
    }


    /**
     * Will show all stores of user
     */
    public function index(): View
    {
        $query = SaasAccount::with(['domain' => function ($q) {
            $q->select(['domain', 'tenant_id']);
        }, 'package' => function ($pq) {
            $pq->with(['package_translations'])->select('id', 'name');
        }, 'plan' => function ($plq) {
            $plq->select(['name', 'id']);
        }]);

        $query = $query->where('user_id', auth()->user()->id);

        $stores = $query->orderBy('id', 'DESC')->paginate(10)->withQueryString();

        return view('plugin/saas::user.panel.store.index', compact('stores'));
    }

    /**
     * Will return store details
     */
    public function storeDetails($store_id): View
    {
        $data = [
            'tl_users.name as subscriber',
            'tl_saas_packages.id as package_id',
            'tl_saas_packages.name as package_name',
            'tl_saas_packages.type as package_type',
            'tl_saas_package_plans.name as plan_name',
            'tl_saas_accounts.membership_type',
            'tl_saas_accounts.created_at as created_at',
            'tl_saas_accounts.valid_till as due_date',
            'tl_saas_accounts.store_name',
            'tl_saas_accounts.status',
            'tl_saas_accounts.tenant_id',
            'tl_saas_accounts.package_plan',
            'tl_saas_accounts.id as store_id',
            'tl_saas_accounts.renewed'
        ];

        $match_case = [
            ['tl_saas_accounts.id', '=', $store_id]
        ];

        $saas_account_details = $this->sub_repo->getSaasAccountDetails($match_case, $data)->first();

        if ($saas_account_details != null) {
            $domain = DB::table('domains')->where('tenant_id', '=', $saas_account_details->tenant_id)->first();
            $saas_account_details->domain = $domain != null ? $domain->domain : null;

            $package_details = $this->storeRepository->storePackageDetails($saas_account_details);
            $payment_history = $this->storeRepository->storePaymentHistory($store_id)->get();
            return view('plugin/saas::user.panel.store.storeDetails', compact('saas_account_details', 'package_details', 'payment_history'));
        } else {
            abort(404);
        }
    }

    /**
     * Will print invoice
     */
    public function printInvoice($store_id)
    {
        $admin_logo = DB::table('tl_general_settings')
            ->join('tl_general_settings_has_values', 'tl_general_settings_has_values.settings_id', '=', 'tl_general_settings.id')
            ->where('tl_general_settings.name', '=', 'admin_logo')
            ->value('value');

        $site_title = DB::table('tl_general_settings')
            ->join('tl_general_settings_has_values', 'tl_general_settings_has_values.settings_id', '=', 'tl_general_settings.id')
            ->where('tl_general_settings.name', '=', 'system_name')
            ->value('value');

        $payment_history = $this->storeRepository->storePaymentHistory($store_id)->first();

        $font_family = "Roboto";
        $local = getLocale();

        if ($local  == 'bd') {
            $font_family = 'Bangla';
        }

        if ($local  == 'sa') {
            $font_family = 'Arabic';
        }

        if ($local  == 'il') {
            $font_family = 'Hebrew';
        }

        $default_currency_id = $default_currency_id = SettingsRepository::getSaasSetting('default_currency');
        $default_currency = Currency::find($default_currency_id);
        $currency_font = 'Arial Unicode MS';
        if ($default_currency->symbol == '₹') {
            $currency_font = 'Roboto';
        }

        $data = [
            'admin_logo' => $admin_logo,
            'site_title' => $site_title,
            'payment_history' => $payment_history,
            'font_family' => $font_family,
            'currency_font' => $currency_font,
        ];

        $default_language = getLocale();
        $is_rtl = DB::table('tl_languages')
            ->where('code', '=', $default_language)
            ->where('is_rtl', '=', 1)
            ->exists();
        if ($is_rtl) {
            $invoice_view = 'plugin/saas::user.panel.subscription.invoice_rtl';
            $pdf = BPDF::loadView($invoice_view, $data)->set_option('isFontSubSettingEnabled', true);
        } else {
            $invoice_view = 'plugin/saas::user.panel.subscription.invoice';
            $pdf = BPDF::loadView($invoice_view, $data)->set_option('isFontSubSettingEnabled', true);
        }

        return $pdf->download();
    }

    /**
     * Update store
     */
    public function updateStore(Request $request): RedirectResponse
    {
        if (!$this->storeRepository->isValidStoreName($request['store_name'])) {
            toastNotification('error', translate('Your given store name is already in use!'));
            return back();
        }

        try {
            $store = SaasAccount::find($request['store_id']);
            $store->store_name = $request['store_name'];
            $store->update();
            toastNotification('success', translate('Store updated Successfully!'));
            return back();
        } catch (Exception $ex) {
            toastNotification('error', translate('Unable to update store!'));
            return back();
        }
    }
}
