<?php

namespace Plugin\TlcommerceCore\Http\Controllers\Api;

use Core\Models\Language;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;
use Core\Repositories\SettingsRepository as CoreSettingRepository;
use Illuminate\Support\Facades\DB;
use Plugin\TlcommerceCore\Models\Country;
use Plugin\TlcommerceCore\Models\Currency;
use Plugin\TlcommerceCore\Models\Customers;
use Plugin\TlcommerceCore\Repositories\SettingsRepository;

class SettingsController extends Controller
{

    /**
     * Will return all  active languages
     * Will return active currencies
     * Will return active countries
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function siteProperties(Request $request)
    {
        try {
            $languages = Cache::rememberForever('-active-languages', function () {
                return Language::where('status', config('settings.general_status.active'))
                    ->select('id', 'native_name as title', 'code')
                    ->get();
            });

            $currencies = Cache::rememberForever('active-currencies', function () {
                return Currency::where('status', config('settings.general_status.active'))
                    ->select('id', 'name', 'code', 'symbol', 'conversion_rate', 'position', 'thousand_separator', 'decimal_separator', 'number_of_decimal')
                    ->get();
            });

            $siteProperties = Cache::rememberForever('site-properties', function () {
                return CoreSettingRepository::SiteProperties();
            });

            $site_Settings = Cache::rememberForever('e-commerce-settings', function () {
                return  SettingsRepository::siteSettings();
            });

            $customer_limit = tenancy()->central(function ($tenant) {
                $request = app('request');
                $current_domain = clean_domain($request->getHost());
                $domain = DB::table('domains')->where('domain', '=', $current_domain)->first();
                $package_id = DB::table('tl_saas_accounts')->where('tenant_id', '=', $domain->tenant_id)
                    ->value('package_id');

                $package_privilege = DB::table('tl_saas_package_has_privileges')
                    ->where('package_id', '=', $package_id)
                    ->value('privileges');

                $package_privilege = json_decode($package_privilege);
                return (int)$package_privilege->package_privileges_customers;
            });

            $total_customers = Customers::count();
            if ($total_customers >= $customer_limit && $customer_limit != -1) {
                $siteProperties['customer_limit_over'] = 1;
            } else {
                $siteProperties['customer_limit_over'] = 0;
            }

            return response()->json(
                [
                    'success' => true,
                    'languages' => $languages,
                    'currencies' => $currencies,
                    'siteProperties' => $siteProperties,
                    'site_settings' => $site_Settings
                ]
            );
        } catch (\Exception $e) {
            return response()->json(
                [
                    'success' => false,
                ]
            );
        }
    }

    /**
     * Will return countries phone code
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function phoneCodes()
    {
        try {
            $phone_codes = Country::where('status', config('settings.general_status.active'))->pluck('phone_code');
            return response()->json(
                [
                    'success' => true,
                    'phone_codes' => $phone_codes
                ]
            );
        } catch (\Exception $e) {
            return response()->json(
                [
                    'success' => false
                ]
            );
        }
    }
}
