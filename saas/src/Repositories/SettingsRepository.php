<?php

namespace Plugin\Saas\Repositories;


use Plugin\Saas\Models\SaasConfig;

class SettingsRepository
{
    /**
     * Will return saas setting
     * 
     * 
     * @param String $key
     * @param mixed $default_value
     * @return String
     */
    public static function getSaasSetting($key, $fallback = NULL)
    {
        try {
            if (SaasConfig::where('key_name', $key)->exists()) {
                $config = SaasConfig::where('key_name', $key)->first();
            } else {
                $config = SaasConfig::firstOrCreate(['key_name' => $key]);
                $config->key_value = $fallback;
                $config->save();
            }
            return $config->key_value;
        } catch (\Exception $e) {
            return $fallback;
        } catch (\Error $e) {
            return $fallback;
        }
    }
}
