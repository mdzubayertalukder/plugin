<?php

namespace Plugin\TlcommerceCore\Repositories;

use Plugin\TlcommerceCore\Models\Currency;

class CurrencyRepository
{

    /**
     * Will return currency list
     * 
     * @return Collections
     */
    public function currencies($status = null)
    {
        try {
            if ($status != null) {
                return Currency::where('status', $status)->get();
            } else {
                return Currency::all();
            }
        } catch (\Exception $e) {
            return [];
        }
    }
}
