<?php

namespace Plugin\Saas\Repositories;

use Illuminate\Support\Facades\DB;

class PaymentRepository
{
    /**
     * Will return payment histories with requested data
     */
    public static function getPaymentHistories($data, $match_case)
    {
        $payments = DB::table('tl_saas_payment_histories')
            ->orderBy('id', 'desc')
            ->where($match_case)
            ->select($data)->get();
        return $payments;
    }
}
