<?php

namespace Plugin\Saas\Http\Controllers\Admin;


use Exception;
use Illuminate\View\View;
use Illuminate\Http\Request;
use Plugin\Saas\Models\Package;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Plugin\Saas\Models\PackagePlan;
use Plugin\Saas\Models\SaasAccount;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Core\Repositories\LanguageRepository;
use Plugin\Saas\Http\Requests\PackageRequest;
use Plugin\Saas\Repositories\TenantRepository;
use Plugin\Saas\Repositories\PackageRepository;
use Plugin\Saas\Http\Requests\PackagePlanRequest;
use Plugin\Saas\Models\TLSaasPackageTranslations;
use Plugin\Saas\Repositories\StoreRepository;

class PackageController extends Controller
{

    public function __construct(
        public LanguageRepository $language_repository,
        public PackageRepository $packageRepository,
        public TenantRepository $tenantRepository
    ) {}
    /**
     * will redirect to package creation page
     */
    public function createPackage(): View
    {
        return view('plugin/saas::admin.package.create_package');
    }

    /**
     * store new package
     */
    public function storePackage(PackageRequest $request): RedirectResponse
    {
        try {
            DB::beginTransaction();
            $package = $this->packageRepository->createOrUpdatePackage($request);
            $this->packageRepository->saveOrUpdatePackagePlugins($package->id, $request);
            $this->packageRepository->saveOrUpdatePackagePrivileges($package->id, $request);
            $this->packageRepository->saveOrUpdatePackagePaymentMethods($package->id, $request);

            if ($request->type == 'paid') {
                $this->packageRepository->savePackagePlans($request, $package);
            }

            DB::commit();
            toastNotification('success', translate('Package Created Successfully!'));
            return to_route('plugin.saas.packages');
        } catch (Exception $ex) {
            DB::rollback();
            toastNotification('error', translate('Package creation failed!'));
            return back();
        }
    }

    /**
     * Will return package listings
     */
    public function packages(): View
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

        $first_plan = -1;

        if (sizeof($package_plans) > 0) {
            $first_plan = $package_plans[0]->id;
        }
        return view('plugin/saas::admin.package.index', [
            'package_plans' => $package_plans,
            'first_plan' => $first_plan
        ]);
    }

    /**
     * Will return all plans of requested package
     */
    public function getPlansAccordingToPackage(Request $request): JsonResponse
    {
        try {
            $data = [
                DB::raw('GROUP_CONCAT(DISTINCT(tl_saas_package_plans.id)) as id'),
                DB::raw('GROUP_CONCAT(DISTINCT(tl_saas_package_plans.name)) as name'),
                DB::raw('GROUP_CONCAT(DISTINCT(tl_saas_package_plans.duration)) as duration')
            ];

            $match_case = [
                ['tl_saas_package_has_plans.package_id', '=', $request['package_id']]
            ];

            $all_package_plans = $this->packageRepository->getPackagePlans($match_case, $data);

            return response()->json([
                'success' => true,
                'plans' => $all_package_plans,
                'message' => translate('Data retrieved successfully')
            ]);
        } catch (Exception $ex) {
            return response()->json([
                'success' => false,
                'message' => translate('Data retrieved Unsuccessful')
            ]);
        }
    }
    /**
     * Will return packages according to plan
     */
    public function getPackageAccordingToPlan(Request $request): View
    {
        $free_packages = $this->packageRepository->getFreePackages();
        $paid_packages = $this->packageRepository->getPaidPackages($request);
        $plan_id = $request['plan_id'];

        if (!isset($request['is_for_payment'])) {
            return view('plugin/saas::admin.package.include.package_list', compact('free_packages', 'paid_packages', 'plan_id'));
        }

        if (isset($request['is_for_payment'])) {
            $store_id = $request['store_id'];
            if ($store_id != 'null') {
                $store_repository = new StoreRepository();
                $current_plan = $store_repository->getStoreCurrentPlan($store_id);
            } else {
                $current_plan = null;
            }
            return view('plugin/saas::user.panel.subscription.include.package_list', compact('free_packages', 'paid_packages', 'plan_id', 'current_plan', 'store_id'));
        }
    }

    /**
     * Will return packages according to plan for frontend
     */
    public function getPackageAccordingToPlanForFrontend(Request $request): View
    {
        $free_packages = $this->packageRepository->getFreePackages();

        $paid_packages = $this->packageRepository->getPaidPackages($request);
        $plan_id = $request['plan_id'];
        $subscribe_btn_text = $request['subscribe_btn_text'];

        return view('plugin/pagebuilder::builders.builder-widgets.include.package_list', compact('free_packages', 'paid_packages', 'plan_id', 'subscribe_btn_text'));
    }

    /**
     * Edit Package
     */
    public function editPackage(Request $request, $id): View
    {
        $languages = $this->language_repository->allLanguages();
        $lang = isset($request->lang) ? $request->lang : 'en';

        $package = Package::find($id);

        $selected_planing = $this->packageRepository->getPlanningsOfPackage($package->id);

        $selected_planing_id = DB::table('tl_saas_package_has_plans')
            ->where('tl_saas_package_has_plans.package_id', '=', $id)
            ->pluck('tl_saas_package_has_plans.plan_id')->toArray();

        $package_features = $this->packageRepository->getPluginsOfPackage($package->id)->toArray();
        $package_privileges = $this->packageRepository->getPrivilegesOfPackage($package->id);
        $package_payment_methods = $this->packageRepository->getPaymentMethodsOfPackage($package->id)->toArray();

        return view('plugin/saas::admin.package.edit_package', compact('languages', 'lang', 'package', 'selected_planing_id', 'selected_planing', 'package_features', 'package_privileges', 'package_payment_methods'));
    }

    /**
     * Will update package
     */
    public function updatePackage(PackageRequest $request): RedirectResponse
    {
        try {
            DB::beginTransaction();

            if ($request->type == 'paid') {
                $subscriber_package = DB::table('tl_saas_accounts')
                    ->where('package_id', '=', $request['id'])
                    ->whereNotIn('package_plan', $request['plans']);

                if ($subscriber_package->exists()) {
                    toastNotification('error', translate('You cannot remove package plan as some subscriber is already in this plan!'));
                    return back();
                }
            }
            if ($request['lang'] != null && $request['lang'] != getDefaultLang()) {
                $package_translation = TLSaasPackageTranslations::firstOrNew(['package_id' => $request['id'], 'lang' => $request['lang']]);
                $package_translation->name = xss_clean($request['name']);
                $package_translation->save();
            } else {
                $package = $this->packageRepository->createOrUpdatePackage($request, $request['id']);
                $this->packageRepository->saveOrUpdatePackagePlugins($package->id, $request, true);
                $this->packageRepository->saveOrUpdatePackagePrivileges($package->id, $request, true);
                $this->packageRepository->saveOrUpdatePackagePaymentMethods($package->id, $request, true);

                if ($request->type == 'paid') {
                    DB::table('tl_saas_package_has_plans')
                        ->where('package_id', '=', $package->id)
                        ->delete();

                    $this->packageRepository->savePackagePlans($request, $package);
                }

                $this->tenantRepository->updateTenantDatabase($request->id);
            }

            DB::commit();
            toastNotification('success', translate('Package Updated Successfully!'));
            return back();
        } catch (Exception $ex) {
            DB::rollBack();
            toastNotification('error', translate('Package update failed!'));
            return back();
        }
    }

    /**
     * Delete Package
     */
    public function deletePackage(Request $request): RedirectResponse
    {
        $subscriber_package = SaasAccount::where('package_id', (int)$request['id']);
        if ($subscriber_package->exists()) {
            toastNotification('error', translate('You cannot delete this package as subscribers are already using this package!'));
            return back();
        }
        try {
            DB::beginTransaction();
            $package = Package::find($request->id);
            $package->delete();
            DB::commit();
            toastNotification('success', translate('Package Deleted Successfully!'));
            return back();
        } catch (\Exception $ex) {
            DB::rollBack();
            toastNotification('error', translate('Package Delete Unsuccessful!'));
            return back();
        }
    }

    /**
     * All package plans
     */
    public function packagePlans(): View
    {
        $plans = DB::table('tl_saas_package_plans')
            ->orderBy('tl_saas_package_plans.id', 'desc')
            ->select(['name', 'duration', 'id'])
            ->get();

        return view('plugin/saas::admin.package.plan', compact('plans'));
    }

    /**
     * Store package plan
     */
    public function storePackagePlans(PackagePlanRequest $request): JsonResponse
    {
        try {
            DB::beginTransaction();
            $plan = new PackagePlan();
            $plan->name = xss_clean($request['plan_name']);
            $plan->duration = $request['plan_duration'];
            $plan->save();

            DB::commit();

            return response()->json([
                'success' => true
            ]);
        } catch (Exception $ex) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'errors' => [],
                'message' => translate("Unable to create new plan")
            ]);
        }
    }

    /**
     * Will request to update package plan
     */
    public function updatePackagePlan(PackagePlanRequest $request): JsonResponse
    {
        try {

            DB::beginTransaction();
            $plan = PackagePlan::find($request['id']);
            $plan->name = xss_clean($request['plan_name']);
            $plan->duration = $request['plan_duration'];
            $plan->update();

            DB::commit();

            return response()->json([
                'success' => true
            ]);
        } catch (Exception $ex) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'errors' => [],
                'message' => translate("Unable to update plan")
            ]);
        }
    }

    /**
     * Delete Package Plan
     */
    public function deletePackagePlan(Request $request): RedirectResponse
    {
        try {
            DB::beginTransaction();
            $package_plan = PackagePlan::find($request->id);
            $package_plan->delete();
            DB::commit();
            toastNotification('success', translate('Package Plan Deleted Successfully!'));
            return back();
        } catch (Exception $ex) {
            DB::rollBack();
            toastNotification('error', translate('Package Delete Unsuccessful!'));
            return back();
        }
    }
}
