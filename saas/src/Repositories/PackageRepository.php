<?php

namespace Plugin\Saas\Repositories;

use Illuminate\Support\Facades\DB;
use Plugin\Saas\Models\Package;

class PackageRepository
{
    /**
     * get all package plans
     * @return mixed|array
     */
    public function getActivePackagePlans()
    {
        $data = [
            DB::raw('GROUP_CONCAT(DISTINCT(tl_saas_package_plans.id)) as id'),
            DB::raw('GROUP_CONCAT(DISTINCT(tl_saas_package_plans.name)) as name'),
            DB::raw('GROUP_CONCAT(DISTINCT(tl_saas_package_plans.duration)) as duration')
        ];

        $package_plans = $this->getPackagePlans([], $data);

        $package_plans = $package_plans->toArray();
        usort($package_plans, function ($a, $b) {
            return $a->duration - $b->duration;
        });

        return $package_plans;
    }
    /**
     * get all active packages
     * @return mixed|array
     */
    public function getAllActivePackages()
    {
        $all_packages = DB::table('tl_saas_packages')
            ->where('tl_saas_packages.status', '=', config('settings.general_status.active'))
            ->select([
                'tl_saas_packages.name',
                'tl_saas_packages.id'
            ])->get();

        return $all_packages;
    }
    /**
     * get all active paid packages
     * @return mixed|array
     */
    public function getAllPaidPackages()
    {
        $all_packages = DB::table('tl_saas_packages')
            ->where('tl_saas_packages.type', '=', 'paid')
            ->where('tl_saas_packages.status', '=', config('settings.general_status.active'))
            ->select([
                'tl_saas_packages.name',
                'tl_saas_packages.id'
            ])->get();

        return $all_packages;
    }
    /**
     * Will return all package plans
     */
    public function getPackagePlans($match_case, $data)
    {
        $packages = DB::table('tl_saas_package_plans')
            ->join('tl_saas_package_has_plans', 'tl_saas_package_has_plans.plan_id', '=', 'tl_saas_package_plans.id')
            ->groupBy('tl_saas_package_plans.id')
            ->where($match_case)
            ->select($data)->get();
        return $packages;
    }

    /**
     * Will return all plannings of a package
     */
    public function getPlanningsOfPackage($package_id)
    {
        $plannings = DB::table('tl_saas_package_plans')
            ->join('tl_saas_package_has_plans', 'tl_saas_package_has_plans.plan_id', '=', 'tl_saas_package_plans.id')
            ->where('tl_saas_package_has_plans.package_id', '=', $package_id)
            ->select([
                'tl_saas_package_plans.name',
                'tl_saas_package_has_plans.cost',
                'tl_saas_package_plans.id'
            ])
            ->get();

        return $plannings;
    }
    /**
     * Will return packages by plan
     */
    public function getPackagesByPlan($match_case, $data)
    {
        $packages = DB::table('tl_saas_packages')
            ->leftJoin('tl_saas_package_has_plans', 'tl_saas_package_has_plans.package_id', '=', 'tl_saas_packages.id')
            ->leftJoin('tl_saas_package_plans', 'tl_saas_package_plans.id', '=', 'tl_saas_package_has_plans.plan_id')
            ->orderBy('tl_saas_package_has_plans.cost', 'asc')
            ->orderBy('tl_saas_packages.id', 'desc')
            ->where($match_case)
            ->select($data)
            ->get();

        return $packages;
    }

    /**
     * Will return free packages
     */
    public function getFreePackages()
    {
        $match_case = [
            ['tl_saas_packages.type', '=', 'free']
        ];
        $data = [
            'tl_saas_packages.name',
            'tl_saas_packages.type',
            'tl_saas_packages.id',
            'tl_saas_packages.trail_period',
            DB::raw('0 as cost'),
            DB::raw('"" as plugins'),
            DB::raw('"" as privileges'),
            DB::raw('"" as payment_methods'),
        ];
        $free_packages = $this->getPackagesByPlan($match_case, $data);

        for ($i = 0; $i < sizeof($free_packages); $i++) {
            $package = $free_packages[$i];
            foreach ($package as $key => $value) {
                if ($key == 'plugins') {
                    $package->$key = $this->getPluginsOfPackage($package->id);
                }
                if ($key == 'privileges') {
                    $package->$key = json_decode($this->getPrivilegesOfPackage($package->id));
                }
                if ($key == 'payment_methods') {
                    $package->$key = $this->getPaymentMethodsOfPackage($package->id);
                }
            }
        }
        return $free_packages;
    }

    /**
     * Will return paid packages
     */
    public function getPaidPackages($request)
    {
        $plan_id = $request['plan_id'];
        $match_case = [
            ['tl_saas_packages.type', '=', 'paid'],
            ['tl_saas_package_plans.id', '=', (int)$plan_id]
        ];

        if ($request['is_for_payment'] == 1) {
            $match_case = [
                ['tl_saas_packages.type', '=', 'paid'],
                ['tl_saas_package_plans.id', '=', (int)$plan_id],
                ['tl_saas_packages.status', '=', 1]
            ];
        }


        $data = [
            'tl_saas_packages.name',
            'tl_saas_packages.type',
            'tl_saas_packages.id',
            'tl_saas_package_plans.id as plan_id',
            'tl_saas_package_plans.duration',
            'tl_saas_package_plans.name as plan_name',
            'tl_saas_package_has_plans.cost',
            'tl_saas_packages.trail_period',
            DB::raw('"" as plugins'),
            DB::raw('"" as privileges'),
            DB::raw('"" as payment_methods'),
        ];
        $paid_packages = $this->getPackagesByPlan($match_case, $data);

        for ($i = 0; $i < sizeof($paid_packages); $i++) {
            $package = $paid_packages[$i];
            foreach ($package as $key => $value) {
                if ($key == 'plugins') {
                    $package->$key = $this->getPluginsOfPackage($package->id);
                }
                if ($key == 'privileges') {
                    $package->$key = $this->getPrivilegesOfPackage($package->id);
                }
                if ($key == 'payment_methods') {
                    $package->$key = $this->getPaymentMethodsOfPackage($package->id);
                }
            }
        }

        return $paid_packages;
    }

    /**
     * Will return all plugins of a package
     */
    public function getPluginsOfPackage($package_id)
    {
        $plugins = DB::table('tl_saas_package_has_plugins')
            ->join('tl_plugins', 'tl_plugins.id', '=', 'tl_saas_package_has_plugins.plugin_id')
            ->where('tl_saas_package_has_plugins.package_id', '=', $package_id)
            ->select([
                'tl_plugins.id as plugin_id',
                'tl_plugins.name as plugin_name',
            ])->get();

        return $plugins;
    }

    /**
     * Will return all privileges of a package
     */
    public function getPrivilegesOfPackage($package_id)
    {
        $privileges = DB::table('tl_saas_package_has_privileges')
            ->where('tl_saas_package_has_privileges.package_id', '=', $package_id)
            ->select([
                'tl_saas_package_has_privileges.privileges',
            ])->first();

        if ($privileges != null) {
            return $privileges->privileges;
        }

        return $privileges;
    }

    /**
     * Will return all payment methods of a package
     */
    public function getPaymentMethodsOfPackage($package_id)
    {
        $payment_gateways = DB::table('tl_saas_package_has_payment_methods')
            ->where('tl_saas_package_has_payment_methods.package_id', '=', $package_id)
            ->select([
                'tl_saas_package_has_payment_methods.payment_method_id as payment_method',
            ])->get();

        return $payment_gateways;
    }

    /**
     * Will save package plans
     */
    public function savePackagePlans($request, $package)
    {
        $plans = $request['plans'];
        $cost = $request['cost'];
        $data = [];

        for ($i = 0; $i < sizeof($plans); $i++) {
            $plan_cost = $cost[$i] == null ? 0 : $cost[$i];
            $array = [
                'package_id' => $package->id,
                'plan_id' => $plans[$i],
                'cost' => $plan_cost,
            ];
            array_push($data, $array);
        }
        DB::table('tl_saas_package_has_plans')->insert($data);
    }

    /**
     * Will save or update package
     */
    public function createOrUpdatePackage($request, $id = null): Package
    {
        if ($id != null) {
            $package = Package::find($id);
        } else {
            $package = new Package();
        }
        $package->type = $request->type;
        $package->name = $request->name;
        $package->is_featured = $request->is_featured;
        $package->status = $request->status;
        $package->trail_period = $request->type == 'paid' ? $request->trail_period : 0;
        $package->save();

        return $package;
    }

    /**
     * Will save package plugins
     */
    public function saveOrUpdatePackagePlugins($package_id, $request, $is_for_update = false): void
    {
        if ($is_for_update) {
            DB::table('tl_saas_package_has_plugins')
                ->where('package_id', '=', $package_id)
                ->delete();
        }

        if (isset($request['plugins'])) {
            $plugin_list = [];
            foreach ($request['plugins'] as $plugin) {
                $array = [
                    'package_id' => $package_id,
                    'plugin_id' => $plugin
                ];
                array_push($plugin_list, $array);
            }
            DB::table('tl_saas_package_has_plugins')->insert($plugin_list);
        }
    }

    /**
     * Will save package payment methods
     */
    public function saveOrUpdatePackagePaymentMethods($package_id, $request, $is_for_update = false)
    {
        if ($is_for_update) {
            DB::table('tl_saas_package_has_payment_methods')
                ->where('package_id', '=', $package_id)
                ->delete();
        }

        if (isset($request['payment_methods'])) {
            $plugin_list = [];
            foreach ($request['payment_methods'] as $value) {
                $array = [
                    'package_id' => $package_id,
                    'payment_method_id' => $value
                ];
                array_push($plugin_list, $array);
            }
            DB::table('tl_saas_package_has_payment_methods')->insert($plugin_list);
        }
    }

    /**
     * Will save package privileges
     */
    public function saveOrUpdatePackagePrivileges($package_id, $request, $is_for_update = false)
    {
        if ($is_for_update) {
            DB::table('tl_saas_package_has_privileges')
                ->where('package_id', '=', $package_id)
                ->delete();
        }

        $package_privileges = $request->all();

        $filtered_privileges = array_filter($package_privileges, function ($key) {
            return strpos($key, 'package_privileges_') !== false;
        }, ARRAY_FILTER_USE_KEY);

        foreach ($filtered_privileges as $key => $privileges) {
            $filtered_privileges[$key] = (int)$privileges;
        }

        $data = [];

        $array = [
            'package_id' => $package_id,
            'privileges' => json_encode($filtered_privileges)
        ];
        array_push($data, $array);

        DB::table('tl_saas_package_has_privileges')->insert($data);
    }

    /**
     * get package by coupon id & type
     * @return mixed|array
     */
    public function getPackageByCouponIdAndType($coupon_id, $coupon_type)
    {
        $package = DB::table('tl_saas_coupons')
            ->join('tl_saas_coupons_of_packages', 'tl_saas_coupons_of_packages.coupon_id', '=', 'tl_saas_coupons.id')
            ->join('tl_saas_packages', 'tl_saas_packages.id', '=', 'tl_saas_coupons_of_packages.package_id')
            ->where('tl_saas_coupons.id', '=', $coupon_id);
        if ($coupon_type == 'discount') {
            $package = $package->pluck('tl_saas_packages.name as package');
        } else {
            $package = $package->select([
                'tl_saas_packages.id'
            ])->first()->id;
        }
        return $package;
    }
}
