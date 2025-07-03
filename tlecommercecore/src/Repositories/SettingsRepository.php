<?php

namespace Plugin\TlcommerceCore\Repositories;

use Core\Models\TlPage;
use Illuminate\Support\Facades\DB;
use Plugin\TlcommerceCore\Models\Currency;
use Plugin\TlcommerceCore\Models\EcommerceConfig;
use Plugin\TlcommerceCore\Repositories\SettingsRepository as EcommerceSettingRepository;

class SettingsRepository
{
    /**
     * Will return ecommerce setting
     *
     *
     * @param String $key
     * @param mixed $default_value
     * @return String
     */
    public static function getEcommerceSetting($key, $fallback = NULL)
    {
        try {
            if (EcommerceConfig::where('key_name', $key)->exists()) {
                $config = EcommerceConfig::where('key_name', $key)->first();
            } else {
                $config = EcommerceConfig::firstOrCreate(['key_name' => $key]);
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

    /**
     * Get default currency
     */
    public static function defaultCurrency()
    {
        return Currency::where('id', self::getEcommerceSetting('default_currency'))
            ->select('id', 'name', 'code', 'symbol', 'conversion_rate', 'position', 'thousand_separator', 'decimal_separator', 'number_of_decimal')
            ->first();
    }

    /**
     * Will update shipping Option
     *
     * @param Object $request
     * @return bool
     */
    public function updateShippingOption($request)
    {
        try {
            DB::beginTransaction();
            $settings = [
                'shipping_option' => $request['shipping_option'],
            ];
            $settings_keys = array_keys($settings);
            foreach ($settings_keys as $key) {
                $config = EcommerceConfig::firstOrCreate(['key_name' => $key]);
                $config->key_value = $settings[$key];
                $config->save();
            }
            EcommerceSettingRepository::resetEcommerceCache();
            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            return false;
        } catch (\Error $e) {
            DB::rollBack();
            return false;
        }
    }
    /**
     * Will update flat rate shipping cost
     *
     * @param Object $request
     * @return bool
     */
    public function updateFlatRateShipping($request)
    {
        try {
            DB::beginTransaction();
            $settings = [
                'flat_rate_shipping_cost' => $request['flat_rate_shipping_cost'],
            ];
            $settings_keys = array_keys($settings);
            foreach ($settings_keys as $key) {
                $config = EcommerceConfig::firstOrCreate(['key_name' => $key]);
                $config->key_value = $settings[$key];
                $config->save();
            }

            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            return false;
        } catch (\Error $e) {
            DB::rollBack();
            return false;
        }
    }

    /**
     * Will update ecommerce settings
     *
     * @param Object $request
     * @return bool
     */
    public function updateEcommerceSettings($request)
    {
        try {
            DB::beginTransaction();
            $settings = [
                'enable_product_reviews'                   => $request->has('enable_product_reviews') ? config('settings.general_status.active') : config('settings.general_status.in_active'),
                'enable_product_star_rating'               => $request->has('enable_product_star_rating') ? config('settings.general_status.active') : config('settings.general_status.in_active'),
                'required_product_star_rating'             => $request->has('required_product_star_rating') ? config('settings.general_status.active') : config('settings.general_status.in_active'),
                'verified_customer_on_product_review'      => $request->has('verified_customer_on_product_review') ? config('settings.general_status.active') : config('settings.general_status.in_active'),
                'only_varified_customer_left_review'       => $request->has('only_varified_customer_left_review') ? config('settings.general_status.active') : config('settings.general_status.in_active'),
                'enable_product_compare'                   => $request->has('enable_product_compare') ? config('settings.general_status.active') : config('settings.general_status.in_active'),
                'manage_product_stock'                     => $request->has('manage_product_stock') ? config('settings.general_status.active') : config('settings.general_status.in_active'),
                'enable_product_discount'                  => $request->has('enable_product_discount') ? config('settings.general_status.active') : config('settings.general_status.in_active'),
                'enable_billing_address'                   => $request->has('enable_billing_address') ? config('settings.general_status.active') : config('settings.general_status.in_active'),
                'use_shipping_address_as_billing_address'  => $request->has('use_shipping_address_as_billing_address') ? config('settings.general_status.active') : config('settings.general_status.in_active'),
                'enable_guest_checkout'                    => $request->has('enable_guest_checkout') ? config('settings.general_status.active') : config('settings.general_status.in_active'),
                'enable_personal_info_guest_checkout'      => $request->has('enable_personal_info_guest_checkout') ? config('settings.general_status.active') : config('settings.general_status.in_active'),
                'create_account_in_guest_checkout'         => $request->has('create_account_in_guest_checkout') ? config('settings.general_status.active') : config('settings.general_status.in_active'),
                'send_invoice_to_customer_mail'            => $request->has('send_invoice_to_customer_mail') ? config('settings.general_status.active') : config('settings.general_status.in_active'),
                'enable_tax_in_checkout'                   => $request->has('enable_tax_in_checkout') ? config('settings.general_status.active') : config('settings.general_status.in_active'),
                'enable_coupon_in_checkout'                => $request->has('enable_coupon_in_checkout') ? config('settings.general_status.active') : config('settings.general_status.in_active'),
                'enable_multiple_coupon_in_checkout'       => $request->has('enable_multiple_coupon_in_checkout') ? config('settings.general_status.active') : config('settings.general_status.in_active'),
                //Wallet Settings
                'enable_minumun_order_amount'              => $request->has('enable_minumun_order_amount') ? config('settings.general_status.active') : config('settings.general_status.in_active'),
                'enable_wallet_in_checkout'                => $request->has('enable_wallet_in_checkout') ? config('settings.general_status.active') : config('settings.general_status.in_active'),
                'minimum_wallet_recharge_amount'           => $request->has('minimum_wallet_recharge_amount') ? $request['minimum_wallet_recharge_amount'] : 0,
                'enable_order_note_in_checkout'            => $request->has('enable_order_note_in_checkout') ? config('settings.general_status.active') : config('settings.general_status.in_active'),
                'enable_document_in_checkout'              => $request->has('enable_document_in_checkout') ? config('settings.general_status.active') : config('settings.general_status.in_active'),
                'customer_auto_approved'                   => $request->has('customer_auto_approved') ? config('settings.general_status.active') : config('settings.general_status.in_active'),
                'customer_email_varification'              => $request->has('customer_email_varification') ? config('settings.general_status.active') : config('settings.general_status.in_active'),
                'customer_social_auth'                     => $request->has('customer_social_auth') ? config('settings.general_status.active') : config('settings.general_status.in_active'),
                'min_order_amount'                         => $request->has('min_order_amount') ? $request['min_order_amount'] : 0,
                'cancel_order_time_limit'                  => $request->has('cancel_order_time_limit') ? $request['cancel_order_time_limit'] : 0,
                'cancel_order_time_limit_unit'             => $request->has('cancel_order_time_limit_unit') ? $request['cancel_order_time_limit_unit'] : 'Days',
                'return_order_time_limit'                  => $request->has('return_order_time_limit') ? $request['return_order_time_limit'] : 0,
                'return_order_time_limit_unit'             => $request->has('return_order_time_limit_unit') ? $request['return_order_time_limit_unit'] : 'Days',
                'product_per_page'                         => $request['product_per_page'] != null ? $request['product_per_page'] : 10,
                'enable_carrier_in_checkout'               => $request->has('enable_carrier_in_checkout') ? config('settings.general_status.active') : config('settings.general_status.in_active'),
                'order_code_prefix'                        => $request->has('order_code_prefix') ? $request['order_code_prefix'] : null,
                'maximum_wallet_used_in_single_order'      => $request->has('maximum_wallet_used_in_single_order') ? $request['maximum_wallet_used_in_single_order'] : 0,
                'enable_wallet_online_recharge'            => $request->has('enable_wallet_online_recharge') ? config('settings.general_status.active') : config('settings.general_status.in_active'),
                'enable_wallet_offline_recharge'           => $request->has('enable_wallet_offline_recharge') ? config('settings.general_status.active') : config('settings.general_status.in_active'),
                'enable_pickuppoint_in_checkout'           => $request->has('enable_pickuppoint_in_checkout') ? config('settings.general_status.active') : config('settings.general_status.in_active'),
                'order_code_prefix_seperator'              => $request->has('order_code_prefix_seperator') ? $request['order_code_prefix_seperator'] : null,
                //Invoice Settings
                'invoice_logo'                             => $request->has('invoice_logo') ? $request['invoice_logo'] : null,
                'invoice_paid_image'                       => $request->has('invoice_paid_image') ? $request['invoice_paid_image'] : null,
                'invoice_unpaid_image'                     => $request->has('invoice_unpaid_image') ? $request['invoice_unpaid_image'] : null,
                'invoice_email'                            => $request->has('invoice_email') ? $request['invoice_email'] : null,
                'invoice_phone'                            => $request->has('invoice_phone') ? $request['invoice_phone'] : null,
                'invoice_address'                          => $request->has('invoice_address') ? $request['invoice_address'] : null,
                //General Settings
                'default_currency'                         => $request->has('default_currency') ? $request['default_currency'] : null,
                'customer_term_condition_page'             => $request->has('customer_term_condition_page') ? $request['customer_term_condition_page'] : null,
                'seller_term_condition_page'               => $request->has('seller_term_condition_page') ? $request['seller_term_condition_page'] : null,
                //Email Settings
                'admin_new_order_email_notification'       => $request->has('admin_new_order_email_notification') ? config('settings.general_status.active') : config('settings.general_status.in_active'),
                'admin_order_refund_email_notification'    => $request->has('admin_order_refund_email_notification') ? config('settings.general_status.active') : config('settings.general_status.in_active'),
                'admin_order_cancel_email_notification'    => $request->has('admin_order_cancel_email_notification') ? config('settings.general_status.active') : config('settings.general_status.in_active'),
                'admin_product_review_email_notification'  => $request->has('admin_product_review_email_notification') ? config('settings.general_status.active') : config('settings.general_status.in_active'),
                'admin_wallet_recharge_email_notification' => $request->has('admin_wallet_recharge_email_notification') ? config('settings.general_status.active') : config('settings.general_status.in_active'),
                //Shop settings
                'shop_name'                                => $request->has('shop_name') ? $request['shop_name'] : null,
                'shop_slug'                                => $request->has('shop_slug') ? $request['shop_slug'] : null,
                'shop_logo'                                => $request->has('shop_logo') ? $request['shop_logo'] : null,
                'shop_banner'                              => $request->has('shop_banner') ? $request['shop_banner'] : null,
                'shop_phone'                               => $request->has('shop_phone') ? $request['shop_phone'] : null,
                'shop_address'                             => $request->has('shop_address') ? $request['shop_address'] : null,
                'shop_meta_title'                          => $request->has('shop_meta_title') ? $request['shop_meta_title'] : null,
                'shop_meta_image'                          => $request->has('shop_meta_image') ? $request['shop_meta_image'] : null,
                'shop_meta_description'                    => $request->has('shop_meta_description') ? $request['shop_meta_description'] : null,
                //Checkout form
                'enable_name_in_checkout'                  => $request->has('enable_name_in_checkout') ? config('settings.general_status.active') : config('settings.general_status.in_active'),
                'name_required_in_checkout'                => $request->has('name_required_in_checkout') ? config('settings.general_status.active') : config('settings.general_status.in_active'),
                'enable_email_in_checkout'                 => $request->has('enable_email_in_checkout') ? config('settings.general_status.active') : config('settings.general_status.in_active'),
                'email_required_in_checkout'               => $request->has('email_required_in_checkout') ? config('settings.general_status.active') : config('settings.general_status.in_active'),
                'enable_phone_in_checkout'                 => $request->has('enable_phone_in_checkout') ? config('settings.general_status.active') : config('settings.general_status.in_active'),
                'phone_required_in_checkout'               => $request->has('phone_required_in_checkout') ? config('settings.general_status.active') : config('settings.general_status.in_active'),
                'enable_address_in_checkout'               => $request->has('enable_address_in_checkout') ? config('settings.general_status.active') : config('settings.general_status.in_active'),
                'address_required_in_checkout'             => $request->has('address_required_in_checkout') ? config('settings.general_status.active') : config('settings.general_status.in_active'),
                'enable_post_code_in_checkout'             => $request->has('enable_post_code_in_checkout') ? config('settings.general_status.active') : config('settings.general_status.in_active'),
                'post_code_required_in_checkout'           => $request->has('post_code_required_in_checkout') ? config('settings.general_status.active') : config('settings.general_status.in_active'),
                'hide_country_state_city_in_checkout'      => $request->has('hide_country_state_city_in_checkout') ? config('settings.general_status.active') : config('settings.general_status.in_active'),

            ];
            $settings_keys = array_keys($settings);

            foreach ($settings_keys as $key) {
                $config = EcommerceConfig::firstOrCreate(['key_name' => $key]);
                $config->key_value = $settings[$key];
                $config->save();
            }

            //reset cache
            self::resetEcommerceCache();

            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            return false;
        } catch (\Error $e) {
            DB::rollBack();
            return false;
        }
    }

    /**
     * Will return site settings
     *
     * @return Array
     */
    public static function siteSettings()
    {
        try {
            $data = [
                'shipping_option'                         => getEcommerceSetting('shipping_option') != null ? getEcommerceSetting('shipping_option') : config('tlecommercecore.shipping_cost_options.profile_wise_rate'),
                'hide_country_state_city_in_checkout'     => self::getEcommerceSetting('hide_country_state_city_in_checkout') != null && self::getEcommerceSetting('hide_country_state_city_in_checkout') == config('settings.general_status.in_active') ? config('settings.general_status.in_active') : config('settings.general_status.active'),
                'post_code_required_in_checkout'          => self::getEcommerceSetting('post_code_required_in_checkout') != null && self::getEcommerceSetting('post_code_required_in_checkout') == config('settings.general_status.in_active') ? config('settings.general_status.in_active') : config('settings.general_status.active'),
                'enable_tax_in_checkout'                  => self::getEcommerceSetting('enable_tax_in_checkout') == config('settings.general_status.active') ? config('settings.general_status.active') : config('settings.general_status.in_active'),
                'enable_product_reviews'                  => self::getEcommerceSetting('enable_product_reviews'),
                'enable_tax_in_checkout'                  => self::getEcommerceSetting('enable_tax_in_checkout', config('settings.general_status.in_active')),
                'enable_product_star_rating'              => self::getEcommerceSetting('enable_product_star_rating'),
                'required_product_star_rating'            => self::getEcommerceSetting('required_product_star_rating'),
                'verified_customer_on_product_review'     => self::getEcommerceSetting('verified_customer_on_product_review'),
                'only_varified_customer_left_review'      => self::getEcommerceSetting('only_varified_customer_left_review'),
                'enable_product_compare'                  => self::getEcommerceSetting('enable_product_compare'),
                'enable_product_discount'                 => self::getEcommerceSetting('enable_product_discount'),
                'product_per_page'                        => self::getEcommerceSetting('product_per_page'),
                'enable_online_recharge'                  => self::getEcommerceSetting('enable_wallet_online_recharge'),
                'enable_offline_recharge'                 => self::getEcommerceSetting('enable_wallet_offline_recharge'),
                'minimum_recharge_amount'                 => self::getEcommerceSetting('minimum_wallet_recharge_amount'),
                'enable_guest_checkout'                   => self::getEcommerceSetting('enable_guest_checkout'),
                'customer_term_condition_page_slug'       => self::getEcommerceSetting('customer_term_condition_page') != null ? self::getPageUrl(self::getEcommerceSetting('customer_term_condition_page')) : '',
                'seller_term_condition_page_slug'         => self::getEcommerceSetting('seller_term_condition_page') != null ? self::getPageUrl(self::getEcommerceSetting('seller_term_condition_page')) : '',
                'return_order_time_limit_unit'            => self::getEcommerceSetting('return_order_time_limit_unit'),
                'return_order_time_limit'                 => self::getEcommerceSetting('return_order_time_limit'),
                'cancel_order_time_limit_unit'            => self::getEcommerceSetting('cancel_order_time_limit_unit'),
                'cancel_order_time_limit'                 => self::getEcommerceSetting('cancel_order_time_limit'),
                'enable_billing_address'                  => self::getEcommerceSetting('enable_billing_address'),
                'use_shipping_address_as_billing_address' => self::getEcommerceSetting('use_shipping_address_as_billing_address'),
                'create_account_in_guest_checkout'        => self::getEcommerceSetting('create_account_in_guest_checkout'),
                'enable_tax_in_checkout'                  => self::getEcommerceSetting('enable_tax_in_checkout'),
                'enable_coupon_in_checkout'               => self::getEcommerceSetting('enable_coupon_in_checkout'),
                'enable_multiple_coupon_in_checkout'      => self::getEcommerceSetting('enable_multiple_coupon_in_checkout'),
                'enable_minumun_order_amount'             => self::getEcommerceSetting('enable_minumun_order_amount'),
                'enable_wallet_in_checkout'               => self::getEcommerceSetting('enable_wallet_in_checkout'),
                'enable_order_note_in_checkout'           => self::getEcommerceSetting('enable_order_note_in_checkout'),
                'enable_document_in_checkout'             => self::getEcommerceSetting('enable_document_in_checkout'),
                'enable_carrier_in_checkout'              => self::getEcommerceSetting('enable_carrier_in_checkout'),
                'min_order_amount'                        => self::getEcommerceSetting('min_order_amount'),
                'enable_pickuppoint_in_checkout'          => self::getEcommerceSetting('enable_pickuppoint_in_checkout'),
                'is_active_wallet'                        => isActivePluging('wallet') ? config('settings.general_status.active') : config('settings.general_status.in_active'),
                'is_active_pickuppoint'                   => isActivePluging('pickuppoint') ? config('settings.general_status.active') : config('settings.general_status.in_active'),
                'is_active_coupon'                        => isActivePluging('coupon') ? config('settings.general_status.active') : config('settings.general_status.in_active'),
                'is_active_multivendor'                   => isActivePluging('multivendor') ? config('settings.general_status.active') : config('settings.general_status.in_active'),
                //checkout form
                'enable_name_in_checkout'     => self::getEcommerceSetting('enable_name_in_checkout') != null && self::getEcommerceSetting('enable_name_in_checkout') == config('settings.general_status.in_active') ? config('settings.general_status.in_active') : config('settings.general_status.active'),
                'name_required_in_checkout'     => self::getEcommerceSetting('name_required_in_checkout') != null && self::getEcommerceSetting('name_required_in_checkout') == config('settings.general_status.in_active') ? config('settings.general_status.in_active') : config('settings.general_status.active'),
                'enable_email_in_checkout'     => self::getEcommerceSetting('enable_email_in_checkout') != null && self::getEcommerceSetting('enable_email_in_checkout') == config('settings.general_status.in_active') ? config('settings.general_status.in_active') : config('settings.general_status.active'),
                'email_required_in_checkout'     => self::getEcommerceSetting('email_required_in_checkout') != null && self::getEcommerceSetting('email_required_in_checkout') == config('settings.general_status.in_active') ? config('settings.general_status.in_active') : config('settings.general_status.active'),
                'enable_phone_in_checkout'     => self::getEcommerceSetting('enable_phone_in_checkout') != null && self::getEcommerceSetting('enable_phone_in_checkout') == config('settings.general_status.in_active') ? config('settings.general_status.in_active') : config('settings.general_status.active'),
                'phone_required_in_checkout'     => self::getEcommerceSetting('phone_required_in_checkout') != null && self::getEcommerceSetting('phone_required_in_checkout') == config('settings.general_status.in_active') ? config('settings.general_status.in_active') : config('settings.general_status.active'),
                'enable_address_in_checkout'     => self::getEcommerceSetting('enable_address_in_checkout') != null && self::getEcommerceSetting('enable_address_in_checkout') == config('settings.general_status.in_active') ? config('settings.general_status.in_active') : config('settings.general_status.active'),
                'address_required_in_checkout'     => self::getEcommerceSetting('address_required_in_checkout') != null && self::getEcommerceSetting('address_required_in_checkout') == config('settings.general_status.in_active') ? config('settings.general_status.in_active') : config('settings.general_status.active'),
                'enable_post_code_in_checkout'     => self::getEcommerceSetting('enable_post_code_in_checkout') != null && self::getEcommerceSetting('enable_post_code_in_checkout') == config('settings.general_status.in_active') ? config('settings.general_status.in_active') : config('settings.general_status.active'),
                'post_code_required_in_checkout'     => self::getEcommerceSetting('post_code_required_in_checkout') != null && self::getEcommerceSetting('post_code_required_in_checkout') == config('settings.general_status.in_active') ? config('settings.general_status.in_active') : config('settings.general_status.active'),
                'enable_personal_info_guest_checkout' => self::getEcommerceSetting('enable_personal_info_guest_checkout') != null && self::getEcommerceSetting('enable_personal_info_guest_checkout') == config('settings.general_status.in_active') ? config('settings.general_status.in_active') : config('settings.general_status.active'),
            ];

            return $data;
        } catch (\Exception $e) {
            return NULL;
        } catch (\Error $e) {
            return NULL;
        }
    }

    /**
     * Will reset tlcommerce settings cache
     */
    public static function resetEcommerceCache()
    {
        cache()->forget('default-currency-details');
        cache()->forget('e-commerce-settings');
        cache()->rememberForever('e-commerce-settings', function () {
            return  self::siteSettings();
        });
    }

    /**
     * Get page url
     */
    public static function getPageUrl($page_id)
    {
        $page = TlPage::where('id', $page_id)->select('permalink')->first();
        if ($page != null) {
            return $page->permalink;
        }
        return null;
    }
}
