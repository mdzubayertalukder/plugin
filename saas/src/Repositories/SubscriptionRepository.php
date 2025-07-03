<?php

namespace Plugin\Saas\Repositories;

use Carbon\Carbon;
use Plugin\Saas\Models\Package;
use Illuminate\Support\Facades\DB;
use Plugin\Saas\Models\PackagePlan;
use Plugin\Saas\Models\SaasAccount;
use Illuminate\Support\Facades\Auth;
use Plugin\Saas\Repositories\PaymentRepository;

class SubscriptionRepository
{
    protected $name;
    /**
     * Will store saas account details
     */
    public function storeSaasAccountDetails($package_details, $subscriber, $tenant, $store_id = null, $is_for_update = 0, $default_language = null, $default_currency = null): SaasAccount
    {

        if ($is_for_update == 1) {
            $account = SaasAccount::find($store_id);
            session()->put('previous_package_id', $account->package_id);
            session()->put('previous_plan_id', $account->package_plan);
        }
        if ($is_for_update != 1) {
            $account = new SaasAccount();
            $account->initial_language = $default_language;
            $account->initial_currency = $default_currency;
        }

        $account->package_id = $package_details['package_id'];
        $account->user_id = $subscriber->id;
        $account->package_plan = $package_details['plan_id'];
        $account->membership_type = $package_details['membership_type'];
        $account->valid_till = $package_details['valid_till'];

        if ($is_for_update == 1) {
            $account->renewed = $account->renewed + 1;
            $account->is_db_updated = 0;
            $account->status = 0;
            $account->update();
            session()->put('saas_account_id', $account->id);
        }

        if ($is_for_update != 1) {
            $account->renewed = 0;
            $account->is_db_created = 0;
            $account->status = 0;
            $account->tenant_id = $tenant->id;
            $account->store_name = $package_details['store_name'];
            $account->save();
            session()->put('saas_account_id', $account->id);
        }
        return $account;
    }

    /**
     * Will return package details for subscription account
     */
    public function getDetailsForSubscriptionAccount($request, $is_trail_user)
    {
        $package = Package::find((int)$request['package_id']);
        $plan = PackagePlan::find((int)$request['plan_id']);

        $membership_type = $is_trail_user ? 'trail' : 'member';

        $currentDate = Carbon::now();
        if ($is_trail_user) {
            $valid_till = $currentDate->addDays((int)$package->trail_period)->format("Y-m-d");
        } else {
            $valid_till = $currentDate->addDays((int)$plan->duration);
        }

        $details = [
            'package_id' => $request['package_id'],
            'plan_id' => $request['plan_id'],
            'membership_type' => $membership_type,
            'valid_till' => $valid_till,
            'store_name' => $request['store_name'],
        ];

        return $details;
    }

    /**
     * Will return package details for subscription account
     */
    public function getDetailsForFreeSubscriptionAccount($request)
    {
        $membership_type = 'member';
        $plan_id = null;

        if (isset($request['is_redeem_coupon']) && $request['is_redeem_coupon'] == 1) {
            $plan_id = config('saas.plans.lifetime');
        }

        $package_details = [
            'package_id' => $request['package_id'],
            'plan_id' => $plan_id,
            'membership_type' => $membership_type,
            'valid_till' => null,
            'store_name' => $request['store_name'],
        ];

        return $package_details;
    }

    /**
     * create saas account
     */
    public function createSaasAccountForFreePackage($request, $package, $tenant, $is_for_update = 0, $saas_account_id = null)
    {
        if ($is_for_update == 1) {
            $account = SaasAccount::find($saas_account_id);
            session()->put('previous_package_id', $account->package_id);
            session()->put('previous_plan_id', $account->package_plan);
        } else {
            $account = new SaasAccount();
            $account->initial_language = $request['default_language'];
            $account->initial_currency = $request['default_currency'];
        }



        $account->user_id = Auth::user()->id;
        $account->package_id = $package->id;

        if ($request['membership_type'] == 'trail') {
            $account->package_plan = $request['plan_id'];
            $account->membership_type = 'trail';

            $currentDate = Carbon::now();
            $account->valid_till = $currentDate->addDays((int)$package->trail_period)->format("Y-m-d");
        } else {
            $account->package_plan = null;
            $account->membership_type = 'member';
            $account->valid_till = null;
        }


        if ($is_for_update == 1) {
            $account->renewed = $account->renewed + 1;
            $account->is_db_updated = 0;
            $account->status = 0;
            $account->update();
            session()->put('saas_account_id', $account->id);
        } else {
            $account->renewed = 0;
            $account->is_db_created = 0;
            $account->status = 0;
            $account->store_name = $request['store_name'];
            $account->tenant_id = $tenant->id;
            $account->save();
            session()->put('saas_account_id', $account->id);
        }
        return $account->id;
    }

    /**
     * Check if user already subscribed with the requested plan
     */
    public function isUserAlreadySubscribed($request)
    {
        $currentDate = date('Y-m-d');

        $status = DB::table('tl_saas_accounts')
            ->where('tl_saas_accounts.id', '=', $request['store_id'])
            ->where('tl_saas_accounts.package_id', '=', $request['package_id'])
            ->where(function ($query) use ($request) {
                $query->where('tl_saas_accounts.package_plan', '=', $request['plan_id'])
                    ->orWhereNull('tl_saas_accounts.package_plan');
            })
            ->where('tl_saas_accounts.membership_type', '=', 'member')
            ->where(function ($query) use ($currentDate) {
                $query->whereDate('tl_saas_accounts.valid_till', '>', $currentDate)
                    ->orWhereNull('tl_saas_accounts.valid_till');
            })
            ->exists();

        return $status;
    }

    /**
     * Check if user already subscribed for lifetime
     */
    public function isUserAlreadySubscribedForLifetime($package_id)
    {
        $status = DB::table('tl_saas_accounts')
            ->where('tl_saas_accounts.user_id', '=', Auth::user()->id)
            ->where('tl_saas_accounts.package_id', '=', $package_id)
            ->where('tl_saas_accounts.package_plan', '=', config('saas.plans.lifetime'))
            ->exists();

        return $status;
    }

    /**
     * create saas account
     */
    public function createSaasAccountForRedeemCoupon($package, $is_for_update = false, $saas_account_id = null)
    {
        if ($is_for_update) {
            $account = SaasAccount::find($saas_account_id);
        } else {
            $account = new SaasAccount();
        }

        $account->user_id = Auth::user()->id;
        $account->status = 1;
        $account->package_id = $package->id;
        $account->authorized_features = $package->features;

        $account->package_plan = config('saas.plans.lifetime');
        $account->membership_type = 'member';
        $account->valid_till = null;

        if ($is_for_update) {
            $account->update();
        } else {
            $account->save();
        }
        return $account->id;
    }

    /**
     * Will return saas account details query builder
     */
    public function getSaasAccountDetails($match_case, $data)
    {
        $saas_account_details = DB::table('tl_saas_accounts')
            ->join('tl_users', 'tl_users.id', '=', 'tl_saas_accounts.user_id')
            ->join('tl_saas_packages', 'tl_saas_packages.id', '=', 'tl_saas_accounts.package_id')
            ->leftJoin('tl_saas_package_plans', 'tl_saas_package_plans.id', '=', 'tl_saas_accounts.package_plan')
            ->where($match_case)
            ->orderBy('tl_saas_accounts.id', 'desc')
            ->select($data);

        return $saas_account_details;
    }

    /**
     * will return store list
     */
    public function subscriberStoresList($subscriber_id)
    {
        $query = SaasAccount::with(['domain' => function ($q) {
            $q->select(['domain', 'tenant_id']);
        }, 'package' => function ($pq) {
            $pq->with(['package_translations'])->select('id', 'name');
        }, 'plan' => function ($plq) {
            $plq->select(['name', 'id']);
        }]);

        $query = $query->where('user_id', $subscriber_id);

        $stores = $query->orderBy('id', 'DESC')->get();
        return $stores;
    }

    /**
     * will return store list
     */
    public function subscriberPaymentHistory($subscriber_id)
    {
        $data = [
            'title',
            'method',
            'coupon_code',
            'currency',
            'discount_amount',
            'final_amount',
            'updated_at',
            'pid',
            'saas_account_id as store_id'
        ];
        $match_case = [
            ['user_id', '=', $subscriber_id]
        ];
        $payment_history = PaymentRepository::getPaymentHistories($data, $match_case);

        return $payment_history;
    }
}
