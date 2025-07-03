<?php

use Plugin\Saas\Models\Package;
use Plugin\Saas\Models\Currency;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Request;
use Plugin\Saas\Repositories\SettingsRepository;

if (!function_exists('getAllPlans')) {
    /**
     * get all plans
     * @return mixed|array
     */
    function getAllPlans()
    {
        $all_package_plans = DB::table('tl_saas_package_plans')
            ->select([
                'name',
                'id'
            ])->get();
        return $all_package_plans;
    }
}


if (!function_exists('systemHasPermissionToCreateDatabase')) {
    /**
     * will check if user has permission to create database
     */
    function systemHasPermissionToCreateDatabase()
    {
        if (env('HAS_DB_CREATE_PERMISSION') == 1) {
            return true;
        } else {
            return false;
        }
    }
}

if (!function_exists('getCurrentHostName')) {
    /**
     * get current host name
     */
    function getCurrentHostName()
    {
        $request = Request::instance();
        $host = $request->getHost();
        return $host;
    }
}

if (!function_exists('getSaasDefaultCurrency')) {
    /**
     * Get default currency
     *
     *
     */
    function getSaasDefaultCurrency()
    {
        $default_currency_id = SettingsRepository::getSaasSetting('default_currency');
        $default_currency = Currency::find((int)$default_currency_id);
        return $default_currency->code;
    }
}

if (!function_exists('currencyExchange')) {
    /**
     * Exchange currency
     */
    function currencyExchange($value, $formatting = true, $target_currency_id = NULL)
    {
        //Get system currency
        $default_currency_details = Cache::rememberForever('default-saas-currency-details', function () {
            $default_currency_id = SettingsRepository::getSaasSetting('default_currency');
            return Currency::find($default_currency_id);
        });

        //Convert to target currency
        if ($target_currency_id != null) {
            $target_currency = Currency::where('id', $target_currency_id)->select('conversion_rate')->first();
            $converted_amount = ($value / $target_currency->conversion_rate) * $default_currency_details->conversion_rate;
            $value = $converted_amount;
        }

        //Formatting Currency
        if ($default_currency_details != null) {
            if ($formatting) {
                $formatting_value = number_format($value, $default_currency_details->number_of_decimal, $default_currency_details->decimal_separator, $default_currency_details->thousand_separator);
                $position = $default_currency_details->position;

                switch ($position) {
                    case "1":
                        return $default_currency_details->symbol . '' . $formatting_value;
                        break;
                    case "2":
                        return $formatting_value . '' . $default_currency_details->symbol;
                        break;
                    case "3":
                        return $default_currency_details->symbol . ' ' . $formatting_value;
                        break;
                    case "4":
                        return $formatting_value . ' ' . $default_currency_details->symbol;
                        break;
                    default:
                        return $formatting_value;
                }
            } else {
                return $value;
            }
        } else {
            return $value;
        }
    }
}

if (!function_exists('translatePackageName')) {
    /**
     * will translated name of package
     */
    function translatePackageName($id)
    {
        $package = Package::find($id);
        return $package->translation('name', getLocale());
    }
}

if (!function_exists('jobExistsInQueue')) {
    /**
     * will check if any job exists in queue
     */
    function jobExistsInQueue()
    {
        $total_jobs = DB::table('jobs')->select(['payload'])->count();
        return $total_jobs;
    }
}

if (!function_exists('checkIfAnyStoreIsNotUpdated')) {
    /**
     * will check if any store is not updated
     */
    function checkIfAnyStoreIsNotUpdated()
    {
        $all_account_status = DB::table('tl_saas_accounts')
            ->where('is_system_db_updated', '=', 0)
            ->orWhere('is_plugin_db_updated', '=', 0)
            ->orWhere('is_db_updated', '=', 0)
            ->exists();

        return $all_account_status;
    }
}

if (!function_exists('rrmdir')) {
    /**
     * will remove directory
     */

    function rrmdir($dir)
    {
        if (is_dir($dir)) {
            $objects = scandir($dir);
            foreach ($objects as $object) {
                if ($object != '.' && $object != '..') {
                    if (is_dir($dir . '/' . $object)) {
                        rrmdir($dir . '/' . $object);
                    } else {
                        unlink($dir . '/' . $object);
                    }
                }
            }
            rmdir($dir);
        }
    }
}

if (!function_exists('isExtendedLicense')) {
    /**
     * check if extended license
     */

    function isExtendedLicense()
    {
        $license = DB::table('user_keys')->where('type', '!=', 'Regular')->exists();
        return $license;
    }
}

if (!function_exists('getTenantPaymentGateways')) {
    /**
     * will return all tlcommerce payment gateways
     */

    function getTenantPaymentGateways()
    {
        $data = [];
        $payment_methods =  DB::table('tl_com_payment_methods')->select(['name', 'id'])->where('status', 1)->get();

        for ($i = 0; $i < sizeof($payment_methods); $i++) {
            $data[$payment_methods[$i]->name] = $payment_methods[$i]->id;
        }

        return $data;
    }
}
