<?php

namespace Plugin\Saas\Http\Controllers\Admin;

use Exception;
use Carbon\Carbon;
use Core\Models\User;
use App\Models\Tenant;
use Illuminate\View\View;
use Illuminate\Http\Request;
use Plugin\Saas\Models\Package;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Plugin\Saas\Models\PackagePlan;
use Plugin\Saas\Models\SaasAccount;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\File;
use App\Jobs\UpdateTenantDatabaseJob;
use Illuminate\Http\RedirectResponse;
use Plugin\Saas\Services\SaasNotification;
use Plugin\Saas\Http\Requests\StoreRequest;
use Plugin\Saas\Repositories\StoreRepository;
use Plugin\Saas\Repositories\TenantRepository;
use Plugin\Saas\Repositories\PackageRepository;
use Plugin\Saas\Repositories\SubscriptionRepository;
use Plugin\Saas\Http\Requests\StorePlanUpdateRequest;

class StoreController extends Controller
{
    public function __construct(
        public SubscriptionRepository $sub_repo,
        public TenantRepository $tenantRepository,
        public PackageRepository $packageRepository,
        public StoreRepository $storeRepository
    ) {}

    /**
     * Will create a new store
     */
    public function createNewStore(StoreRequest $request): JsonResponse
    {
        if (!$this->storeRepository->isValidStoreName($request['store_name'])) {
            return response()->json([
                'success' => false,
                'message' => translate('Invalid/Duplicate store name!')
            ], 200);
        }

        try {
            DB::beginTransaction();
            $package = Package::find((int)$request['package_id']);
            $plan = DB::table('tl_saas_package_plans')
                ->where('id', '=', $request['plan_id'])
                ->first();

            $currentDate = Carbon::now();
            $user = User::find((int)$request['subscriber_id']);
            $package_details = [
                'package_id' => $package->id,
                'plan_id' => $package->type == 'paid' ? $request['plan_id'] : null,
                'membership_type' => 'member',
                'valid_till' => $package->type == 'paid' ? $currentDate->addDays((int)$plan->duration)->format("Y-m-d") : null,
                'store_name' => $request['store_name']
            ];

            $tenant = $this->tenantRepository->createNewTenant($request);
            $saas_account = $this->sub_repo->storeSaasAccountDetails($package_details, $user, $tenant, default_language: $request['default_language'], default_currency: $request['default_currency']);
            DB::commit();

            //Handle Database
            $this->tenantRepository->createOrUpdateSingleTenantDatabase($tenant->id, $package->id, $saas_account->id, 0);

            //Handle notification
            $notification_service = new SaasNotification();
            $notification_service->newSubscriptionNotificationToSubscriber($saas_account->id);
            $notification_service->newSubscriptionNotificationAdmin($saas_account->id);

            return response()->json([
                'success' => true,
            ], 200);
        } catch (Exception $ex) {
            $error = [
                'message' => 'Error occurred during create a store of a  subscriber',
                'data' => request()->all(),
                'error' => $ex
            ];
            Log::channel('tenant_database')->info(json_encode($error));
            DB::rollBack();
            return response()->json([
                'success' => false
            ], 500);
        }
    }

    /**
     * Will show all stores of user
     */
    public function index(Request $request): View
    {
        $stores = $this->storeRepository->storeLists($request);
        return view('plugin/saas::admin.subscriptions.store.index', compact('stores'));
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

        if ($saas_account_details == null) {
            abort(404);
        }

        $domain = DB::table('domains')->where('tenant_id', '=', $saas_account_details->tenant_id)->first();
        $saas_account_details->domain = $domain != null ? $domain->domain : null;

        $tenant = DB::table('tenants')->where('id', '=', $saas_account_details->tenant_id)->value('data');
        $data = json_decode($tenant);
        $database_prefix = \Plugin\Saas\Repositories\SettingsRepository::getSaasSetting('database_prefix');
        $saas_account_details->database = $data != null ? $data->tenancy_db_name : null;

        $package_details = $this->storeRepository->storePackageDetails($saas_account_details);
        $payment_history = $this->storeRepository->storePaymentHistory($store_id)->get();

        return view('plugin/saas::admin.subscriptions.store.details', compact('saas_account_details', 'package_details', 'payment_history'));
    }

    /**
     * Update store
     */
    public function updateStore(Request $request)
    {
        try {
            DB::beginTransaction();
            $store = SaasAccount::find($request['store_id']);
            $store->store_name = $request['store_name'];
            $store->update();
            DB::commit();
            toastNotification('success', translate('Store updated Successfully!'));
            return back();
        } catch (Exception $ex) {
            DB::rollBack();
            toastNotification('error', translate('Unable to update store!'));
            return back();
        }
    }

    /**
     * Update store status
     */
    public function updateStoreStatus(Request $request): JsonResponse
    {
        try {
            DB::beginTransaction();
            $saas_account = SaasAccount::findOrFail($request['id']);
            if ($saas_account->status == 1) {
                $saas_account->status = 0;
            } else {
                $saas_account->status = 1;
            }

            $saas_account->update();

            //handle notification
            $notification_service = new SaasNotification();
            $notification_service->storeStatusUpdateNotificationToSubscriber($saas_account->id);

            DB::commit();
            return response()->json([
                'success' => true
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false
            ], 500);
        }
    }

    /**
     * Will create and import tenant database
     */
    public function createTenantDatabase(Request $request): RedirectResponse
    {
        try {
            $store = SaasAccount::find((int)$request['store_id']);
            $this->tenantRepository->createOrUpdateSingleTenantDatabase($store->tenant_id, $store->package_id, $store->id, 0, true);
            toastNotification('success', translate('Tenant database creation successfully!'));
            return back();
        } catch (Exception $ex) {
            $error = [
                'message' => 'Error occurred during manually creating database',
                'data' => request()->all(),
                'error' => $ex
            ];
            Log::channel('tenant_database')->info(json_encode($error));

            toastNotification('error', translate('Tenant database creation unsuccessful!'));
            return back();
        }
    }

    /**
     * Will update tenant database
     */
    public function updateTenantDatabase(Request $request): JsonResponse
    {
        try {
            $store = SaasAccount::find((int)$request['id']);
            $this->tenantRepository->createOrUpdateSingleTenantDatabase($store->tenant_id, $store->package_id, $store->id, 1);
            toastNotification('success', translate('Tenant database updated successfully!'));
            return response()->json([
                'success' => true
            ]);
        } catch (Exception $ex) {
            $error = [
                'message' => 'Error occurred during manually updating database once failed from admin',
                'data' => request()->all(),
                'error' => $ex
            ];
            Log::channel('tenant_database')->info(json_encode($error));

            toastNotification('error', translate('Tenant database update failed!'));
            return response()->json([
                'success' => false
            ]);
        }
    }

    /**
     * Will update custom tenant core database
     */
    public function updateTenantCoreDatabase(Request $request): JsonResponse
    {
        $root_path = storage_path('app/tempUpdate');

        try {
            cache_clear();
            $config_file = @file_get_contents($root_path . '/updates/config.json', true);
            if ($config_file == false) {
                abort(500, 'The update file is corrupt.');
            }
            $json = json_decode($config_file, true);

            if (isset($json['sql']) && !empty($json['sql'])) {
                $store = SaasAccount::find((int)$request['id']);

                $sql_files = $json['sql'];

                $job = new UpdateTenantDatabaseJob($store->tenant_id, $sql_files, false);
                dispatch($job);
            }

            cache_clear();
            toastNotification('success', 'Successfully updated', 'Success');
            return response()->json([
                'success' => true
            ]);
        } catch (\Exception $ex) {
            $error = [
                'message' => 'Error occurred during manually updating database once failed while system update admin',
                'data' => request()->all(),
                'error' => $ex
            ];
            Log::channel('tenant_database')->info(json_encode($error));

            cache_clear();
            toastNotification('error', 'Something went wrong', 'Error');
            return response()->json([
                'success' => false
            ]);
        } catch (\Error $ex) {
            $error = [
                'message' => 'Error occurred during manually updating database once failed while system update admin',
                'data' => request()->all(),
                'error' => $ex
            ];
            Log::channel('tenant_database')->info(json_encode($error));

            cache_clear();
            toastNotification('error', 'Something went wrong', 'Error');
            return response()->json([
                'success' => false
            ]);
        }
    }

    /**
     * Will update custom tenant plugin database
     */
    public function updateTenantPluginDatabase(Request $request): JsonResponse
    {
        try {
            $store = SaasAccount::find((int)$request['id']);

            $all_uninstalled_plugins = DB::table('tl_saas_plugin_install_failed')
                ->where('tenant_id', '=', $store->tenant_id)
                ->pluck('plugin_location');

            $pluginDestinationPath = base_path("plugins/");

            foreach ($all_uninstalled_plugins as $location) {
                //Import Database
                $db = $pluginDestinationPath . '' . $location . '/data.sql';

                if (file_exists($db)) {
                    $sql = file_get_contents($db);
                    $job = new UpdateTenantDatabaseJob($store->tenant_id, $sql, true, $location);
                    dispatch($job);
                }
            }
            toastNotification('success', translate('Tenant database updated successfully!'));
            return response()->json([
                'success' => true
            ]);
        } catch (\Exception $ex) {
            $error = [
                'message' => 'Error occurred during manually updating plugin database once failed from admin',
                'data' => request()->all(),
                'error' => $ex
            ];
            Log::channel('tenant_database')->info(json_encode($error));

            toastNotification('error', translate('Tenant database update failed!'));
            return response()->json([
                'success' => false
            ]);
        }
    }

    /**
     * Will delete store
     */
    public function storeDelete(Request $request): RedirectResponse
    {
        try {
            $saas_account = SaasAccount::find($request['store_id']);
            $tenant_id = $saas_account->tenant_id;

            if ($saas_account->is_db_created == 0) {
                DB::table('tenants')->where('id', '=', $tenant_id)->delete();
            } else {
                $tenant = Tenant::find($tenant_id);
                if (!empty($tenant)) {
                    Tenant::find($tenant_id)->delete();
                }
            }

            $saas_account->delete();
            if (File::exists(public_path("tenant/tenant$tenant_id"))) {
                chmod(public_path("tenant/tenant$tenant_id"), 0777);
                rrmdir(public_path("tenant/tenant$tenant_id"));
            }

            if (isActivePluging('paddle-recurring')) {
                \Plugin\PaddleRecurring\Services\PaddleService::cancelSubscription($saas_account->tenant_id);
            }

            toastNotification('success', translate('Store Deleted Successfully'));
            return back();
        } catch (Exception $ex) {
            toastNotification('error', translate('Unable to delete store!'));
            return back();
        }
    }

    /**
     * Will update store plan
     */
    public function updateStorePlan(StorePlanUpdateRequest $request)
    {
        try {
            DB::beginTransaction();
            $package = Package::find((int)$request['package_id']);

            if ($package->type == 'paid') {
                $request['membership_type'] = 'member';
                $plan = PackagePlan::find($request['plan_id']);
            }

            $currentDate = Carbon::now();
            $subscriber = User::find((int)$request['subscriber_id']);
            $package_details = [
                'package_id' => $package->id,
                'plan_id' => $package->type == 'paid' ? $request['plan_id'] : null,
                'membership_type' => 'member',
                'valid_till' => $package->type == 'paid' ? $currentDate->addDays((int)$plan->duration)->format("Y-m-d") : null,
                'store_name' => $request['store_name']
            ];

            $saas_account = $this->sub_repo->storeSaasAccountDetails($package_details, $subscriber, null, $request['store_id'], 1);
            DB::commit();

            $this->tenantRepository->createOrUpdateSingleTenantDatabase($saas_account->tenant_id, $package->id, $saas_account->id, 1);

            //handle notification
            $notification_service = new SaasNotification();
            $notification_service->changeSubscriptionNotificationToSubscriber($saas_account->id);
            $notification_service->changeSubscriptionNotificationToAdmin($saas_account->id);

            return response()->json([
                'success' => true,
            ], 200);
        } catch (Exception $ex) {
            dd($ex);
            $error = [
                'message' => 'Error occurred during upgrading subscription plan from admin panel',
                'data' => request()->all(),
                'error' => $ex
            ];
            Log::channel('tenant_database')->info(json_encode($error));

            DB::rollBack();
            return response()->json([
                'success' => false
            ], 500);
        }
    }
}
