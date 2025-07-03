<?php

namespace Plugin\Saas\Repositories;

use Illuminate\Http\Request;
use Plugin\Saas\Models\Package;
use Illuminate\Support\Facades\DB;
use Plugin\Saas\Models\PackagePlan;
use Plugin\Saas\Models\SaasAccount;

class StoreRepository
{
    /**
     * Check if store name is valid
     */
    public function isValidStoreName(string $store_name): bool
    {
        $pattern = '/[!@#$%^&*()_+{}\[\]:;<>,.?~\\-]/';

        if (!empty($store_name) && empty(preg_match($pattern, $store_name))) {
            $subdomain = implode('', explode(' ', strtolower($store_name)));


            $duplicate_store = DB::table('tl_saas_accounts')
                ->where('store_name', '=', $store_name);

            $duplicate_domain = DB::table('domains')
                ->where('domains.main_domain', '=', $subdomain . '.' . getCurrentHostName())
                ->orWhere('domains.main_domain', '=', $subdomain . '.' . getCurrentHostName());


            if ($duplicate_store->exists() || $duplicate_domain->exists()) {
                return false;
            }
        } else {
            return false;
        }

        return true;
    }

    /**
     * will return current plan
     *
     */
    public function getStoreCurrentPlan($store_id)
    {
        $current_plan = null;
        $saas_account = DB::table('tl_saas_accounts')
            ->where('id', '=', $store_id)
            ->first();
        $package_type = Package::find((int)$saas_account->package_id)->type;

        $plan_duration = null;
        if ($package_type == 'paid') {
            $plan_duration = PackagePlan::find((int)$saas_account->package_plan)->duration;
        }
        if ($saas_account) {
            $current_plan = [
                'package_id' => $saas_account->package_id,
                'package_plan' => $saas_account->package_plan,
                'plan_duration' => $plan_duration,
                'package_type' => $package_type,
            ];
        }

        return $current_plan;
    }

    /**
     * Will return store list
     */
    public function storeLists(Request $request)
    {
        $query = SaasAccount::with(['domain' => function ($q) {
            $q->select(['domain', 'tenant_id']);
        }, 'package' => function ($pq) {
            $pq->with(['package_translations'])->select('id', 'name');
        }, 'plan' => function ($plq) {
            $plq->select(['name', 'id']);
        }, 'user' => function ($uq) {
            $uq->select(['id', 'name', 'email']);
        }, 'tenant' => function ($tq) {
            $tq->select('id', 'data');
        }]);

        //Filter by creation data
        if ($request->has('store_creation_date') && $request['store_creation_date'] != null) {
            $date_range = explode(' - ', $request['store_creation_date']);
            $query = $query->where('updated_at', '>=', $date_range[0])
                ->where('updated_at', '<=', $date_range[1]);
        }

        //Filter By Store status
        if ($request->has('subscription_status') && $request['subscription_status'] != null && $request['subscription_status'] != 'all') {
            $query = $query->where('status', $request['subscription_status']);
        }

        //Filter by store name
        if ($request->has('store_name') && $request['store_name'] != null) {
            $query = $query->where('store_name', 'like', '%' . $request['store_name'] . '%');
        }

        //filter by user name
        if ($request->has('subscriber_name') && $request['subscriber_name'] != null) {
            $query = $query->whereHas('user', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request['subscriber_name'] . '%');
            });
        }

        //Calculate page number
        $per_page = 5;
        if ($request->has('per_page') && $request['per_page'] != null) {
            if ($request['per_page'] == 'all') {
                $per_page = 100;
            } else {
                $per_page = $request['per_page'];
            }
        }
        $stores = $query->orderBy('id', 'DESC')->paginate($per_page)->withQueryString();
        return $stores;
    }
    /**
     * Will return package details for saas account
     */
    public function storePackageDetails($saas_account_details)
    {
        $match_case = [
            ['tl_saas_packages.id', '=', $saas_account_details->package_id],
        ];

        if ($saas_account_details->package_type == 'paid') {
            array_push($match_case, [
                'tl_saas_package_plans.id',
                '=',
                $saas_account_details->package_plan
            ]);
        } else {
            array_push($match_case, ['tl_saas_packages.type', '=', 'free']);
        }

        $data = [
            DB::raw('"" as plugins'),
            DB::raw('"" as privileges'),
            DB::raw('"" as payment_methods'),
        ];

        $packageRepository = new PackageRepository();
        $package_details = $packageRepository->getPackagesByPlan($match_case, $data);

        for ($i = 0; $i < sizeof($package_details); $i++) {
            $package = $package_details[$i];
            foreach ($package as $key => $value) {
                if ($key == 'plugins') {
                    $package->$key = $packageRepository->getPluginsOfPackage($saas_account_details->package_id);
                }
                if ($key == 'privileges') {
                    $package->$key = json_decode($packageRepository->getPrivilegesOfPackage($saas_account_details->package_id));
                }
                if ($key == 'payment_methods') {
                    $package->$key = $packageRepository->getPaymentMethodsOfPackage($saas_account_details->package_id);
                }
            }
        }

        $package_details = $package_details[0];

        return $package_details;
    }
    /**
     * Will return payment history by saas account
     */
    public function storePaymentHistory($saas_account_id)
    {
        $payment_history = DB::table('tl_saas_payment_histories')
            ->where('tl_saas_payment_histories.saas_account_id', '=', $saas_account_id)
            ->orderBy('id', 'desc')
            ->select([
                'tl_saas_payment_histories.id',
                'tl_saas_payment_histories.title',
                'tl_saas_payment_histories.method',
                'tl_saas_payment_histories.coupon_code',
                'tl_saas_payment_histories.currency',
                'tl_saas_payment_histories.discount_amount',
                'tl_saas_payment_histories.primary_amount',
                'tl_saas_payment_histories.discount_amount',
                'tl_saas_payment_histories.final_amount',
                'tl_saas_payment_histories.updated_at',
                'tl_saas_payment_histories.pid'
            ]);

        return $payment_history;
    }
}
