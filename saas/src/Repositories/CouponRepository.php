<?php

namespace Plugin\Saas\Repositories;

use Illuminate\Support\Facades\DB;

class CouponRepository
{
    /**
     * Will return packages by requested coupon type
     *
     * @return Collections
     */
    public function packagesOfCouponType($match_case, $data)
    {
        $query_builder = DB::table('tl_saas_coupons')
            ->join('tl_saas_coupons_of_packages', 'tl_saas_coupons_of_packages.coupon_id', 'tl_saas_coupons.id')
            ->join('tl_saas_packages', 'tl_saas_packages.id', '=', 'tl_saas_coupons_of_packages.package_id')
            ->where($match_case)
            ->select($data);

        return $query_builder;
    }
    /**
     * Will update coupon info
     */
    public function updateCouponUsedInfo($coupon_code): void
    {
        DB::table('tl_saas_coupons')
            ->where('coupon_code', '=', $coupon_code)
            ->update([
                'status' => 1,
                'total_used' => DB::raw('total_used + 1')
            ]);
    }
}
