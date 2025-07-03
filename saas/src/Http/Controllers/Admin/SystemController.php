<?php

namespace Plugin\Saas\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Exception;
use Plugin\Saas\Models\SaasSettings;
use Plugin\Saas\Models\SaasNotification;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;
use Plugin\Saas\Repositories\CurrencyRepository;

class SystemController extends Controller
{
    /**
     * Will clear system  cache
     */
    public function clearSystemCache()
    {
        try {
            cache_clear();
            toastNotification('success', translate('Cache clear successfully'));
            return redirect()->back();
        } catch (\Exception $e) {
            toastNotification('error', translate('Cache clear failed'));
        }
    }

    /**
     * Will redirect to saas general settings page
     */
    public function generalSettings(): View
    {
        $settings = DB::table('tl_saas_settings')->get();
        $settings_data = [];

        for ($i = 0; $i < sizeof($settings); $i++) {
            $settings_data[$settings[$i]->key_name] = $settings[$i]->key_value;
        }
        $currency_repository = new CurrencyRepository();
        $currencies = $currency_repository->getAllSaasCurrencies();

        $tenant_payment_methods = DB::table('tl_com_payment_methods')->get();

        return view('plugin/saas::admin.settings.general_settings', compact('currencies', 'settings_data', 'tenant_payment_methods'));
    }

    /**
     * Will store saas general settings
     */
    public function storeGeneralSettings(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'default_currency' => 'required',
            'notify_before_expired_days' => 'required',
            'notify_before_expired_interval_days' => 'required',
            'maximum_free_store' => 'required',
        ], [
            'default_currency' => 'Default currency is required',
            'notify_before_expired_days' => 'Days between initial warning and subscription ends is required',
            'notify_before_expired_interval_days' => 'Interval days between warnings is required',
            'maximum_free_store' => 'Need to give maximum free store a subscriber can create'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            DB::beginTransaction();
            DB::table('tl_saas_settings')->delete();

            $request_data = $request->all();
            if (isset($request['email_verification'])) {
                $request_data['email_verification'] = 1;
            } else {
                $request_data['email_verification'] = 0;
            }

            if (isset($request['auto_approve_subscription_request'])) {
                $request_data['auto_approve_subscription_request'] = 1;
            } else {
                $request_data['auto_approve_subscription_request'] = 0;
            }

            $dataToInsert = [];

            foreach ($request_data as $key => $value) {
                if ($key != '_token') {
                    $temp_array = [
                        'key_name' => $key,
                        'key_value' => $value
                    ];
                    array_push($dataToInsert, $temp_array);
                }
            }

            SaasSettings::insert($dataToInsert);
            DB::commit();
            toastNotification('success', 'Saas General Settings Updated successfully ', 'Success');
            return redirect()->back();
        } catch (\Exception $th) {
            DB::rollBack();
            toastNotification('error', 'Unable to update general settings', 'Failed');
            return redirect()->back();
        }
    }

    /**
     * will redirect to notification settings page
     */
    public function saasNotificationSettings(): View
    {
        $notification_settings = SaasNotification::all();
        $settings_data = [
            'roles_of_new_reg_noti' => [],
            'roles_of_new_subs_noti' => [],
            'roles_of_chng_subs_noti' => [],
            'roles_of_new_custom_domain_req_noti' => [],
        ];

        $roles = DB::table('roles')->get();

        foreach ($notification_settings as $settings) {
            if ($settings->template_id == 19) {
                array_push($settings_data['roles_of_new_reg_noti'], $settings->role_id);
            }
            if ($settings->template_id == 20) {
                array_push($settings_data['roles_of_new_subs_noti'], $settings->role_id);
            }
            if ($settings->template_id == 22) {
                array_push($settings_data['roles_of_chng_subs_noti'], $settings->role_id);
            }
            if ($settings->template_id == 25) {
                array_push($settings_data['roles_of_new_custom_domain_req_noti'], $settings->role_id);
            }
        }
        return view('plugin/saas::admin.settings.notification_settings', compact('settings_data', 'roles'));
    }

    /**
     * will store saas notification
     */
    public function storeSaasNotificationSettings(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'roles_of_new_reg_noti' => 'required',
            'roles_of_new_subs_noti' => 'required',
            'roles_of_chng_subs_noti' => 'required',
            'roles_of_new_custom_domain_req_noti' => 'required'
        ], [
            'roles_of_new_reg_noti' => 'Please select at least one role',
            'roles_of_new_subs_noti' => 'Please select at least one role',
            'roles_of_chng_subs_noti' => 'Please select at least one role',
            'roles_of_new_custom_domain_req_noti' => 'Please select at least one role',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            DB::beginTransaction();

            DB::table('tl_saas_notifications_to_roles')->delete();
            $requested_data = $request->all();
            $data_to_insert = [];
            foreach ($requested_data as $key => $value) {
                if ($key != '_token') {
                    if ($key == 'roles_of_new_reg_noti') {
                        $template_id = 19;
                    }
                    if ($key == 'roles_of_new_subs_noti') {
                        $template_id = 20;
                    }
                    if ($key == 'roles_of_chng_subs_noti') {
                        $template_id = 22;
                    }
                    if ($key == 'roles_of_new_custom_domain_req_noti') {
                        $template_id = 25;
                    }
                    for ($i = 0; $i < sizeof($value); $i++) {
                        array_push($data_to_insert, [
                            'template_id' => $template_id,
                            'role_id' => $value[$i],
                        ]);
                    }
                }
            }

            SaasNotification::insert($data_to_insert);
            DB::commit();
            toastNotification('success', 'Saas Notification Updated successfully ', 'Success');
            return redirect()->back();
        } catch (Exception $ex) {
            DB::rollBack();
            toastNotification('error', 'Unable to update notification settings', 'Failed');
            return redirect()->back();
        }
    }

    /**
     * Will redirect to domain settings page
     */
    public function domainSettings()
    {
        return view('plugin/saas::settings.domain_settings');
    }
}
