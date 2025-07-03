<?php

namespace Plugin\Saas\Repositories;

use Plugin\Saas\Models\Currency;
use Illuminate\Support\Facades\DB;

class CurrencyRepository
{

    /**
     * Will return all active saas currency
     */
    public function getAllSaasCurrencies()
    {
        $currencies = DB::table('tl_saas_currencies')
            ->where('status', '=', 1)
            ->select([
                'id',
                'code',
                'name'
            ])->get();

        return $currencies;
    }

    /**
     * get default saas currency
     * @return mixed|array
     */
    public function getSaasCurrency()
    {
        $saas_currency = Currency::where('id', \Plugin\Saas\Repositories\SettingsRepository::getSaasSetting('default_currency'))
            ->select('code', 'conversion_rate')
            ->first();

        return $saas_currency;
    }
}
