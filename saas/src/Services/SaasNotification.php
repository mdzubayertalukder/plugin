<?php

namespace Plugin\Saas\Services;

use DateTime;
use Core\Models\User;
use Illuminate\Support\Facades\DB;
use Plugin\Saas\Models\SaasAccount;
use Illuminate\Support\Facades\Mail;
use Plugin\Saas\Mail\CommonSaasEmail;
use Plugin\Saas\Notifications\UserNotification;

class SaasNotification
{
    /**
     * activate subscription schedule notification
     */
    public function subscriptionScheduleNotification()
    {
        $notify_before_expired_days = \Plugin\Saas\Repositories\SettingsRepository::getSaasSetting('notify_before_expired_days');
        $notify_before_expired_interval_days = \Plugin\Saas\Repositories\SettingsRepository::getSaasSetting('notify_before_expired_interval_days');
        $saas_accounts = SaasAccount::all();

        foreach ($saas_accounts as $account) {
            if ($account->valid_till != null) {
                $saas_account = SaasAccount::find((int)$account->id);

                $valid_till = $account->valid_till;

                $currentDate = new DateTime();
                $expired_date = new DateTime($valid_till);

                $difference = $currentDate->diff($expired_date)->days;

                if ($expired_date >= $currentDate) {
                    if ($difference - $notify_before_expired_days == 0) {
                        if ($saas_account->is_notified != 1) {
                            $this->subscriptionReminderNotificationToSubscriber($saas_account->id);
                            $saas_account->is_notified = 1;
                        }
                    } elseif (($notify_before_expired_days - $difference) % $notify_before_expired_interval_days == 0) {
                        if ($saas_account->is_notified != 1) {
                            $this->subscriptionReminderNotificationToSubscriber($saas_account->id);
                            $saas_account->is_notified = 1;
                        }
                    } else {
                        $saas_account->is_notified = 0;
                    }
                } else {
                    if ($saas_account->is_notified != 1) {
                        $this->subscriptionExpiredNotificationToSubscriber($saas_account->id);
                        $saas_account->status = 0;
                        $saas_account->is_notified = 1;
                    }
                }
                $saas_account->update();
            }
        }
    }

    /**
     * will return saas account details by id
     */
    public function getNotifiableSaasAccountDetails($saas_account_id)
    {
        $saas_account_details = DB::table('tl_saas_accounts')
            ->join('tl_users', 'tl_users.id', '=', 'tl_saas_accounts.user_id')
            ->join('tl_saas_packages', 'tl_saas_packages.id', '=', 'tl_saas_accounts.package_id')
            ->leftJoin('tl_saas_package_plans', 'tl_saas_package_plans.id', '=', 'tl_saas_accounts.package_plan')
            ->where('tl_saas_accounts.id', '=', $saas_account_id)
            ->select([
                'tl_users.name as subscriber',
                'tl_saas_packages.name as package_name',
                'tl_saas_package_plans.name as plan_name',
                'tl_saas_accounts.membership_type',
                'tl_saas_accounts.valid_till',
                'tl_saas_accounts.store_name',
                'tl_saas_accounts.tenant_id',
                'tl_saas_accounts.user_id',
                'tl_saas_accounts.status',
                'tl_saas_accounts.id',
            ])->first();

        if ($saas_account_details != null) {
            if ($saas_account_details->valid_till == null) {
                $saas_account_details->valid_till = "Lifetime";
            }

            if ($saas_account_details->plan_name == null) {
                $saas_account_details->plan_name = "Lifetime";
            }

            $domain = DB::table('domains')->where('tenant_id', '=', $saas_account_details->tenant_id)->first();
            $saas_account_details->domain = $domain != null ? $domain->domain : null;
        }

        return $saas_account_details;
    }
    /**
     * Send notification to admin when new subscriber registered
     */
    public  function subscriberRegistrationNotificationToAdmin(int $user_id): void
    {

        $user = User::find($user_id);

        $mail_data = [
            'template_id' => 19,
            'keywords' => getEmailTemplateVariables(19, true),
            'subject' => $this->getSubjectByTemplateId(19),

            '_customer_name_' =>  $user->name,
            '_app_name_' =>  env('APP_NAME'),
        ];

        $admins = $this->getAllNotifiableAdmins(19);
        foreach ($admins as $admin) {
            $admin_user = User::find($admin->id);
            $data = [
                'message' => 'New subscriber ' . $user->name . ' has registered',
                'link' => '/' . getAdminPrefix() . '/subscribers'
            ];
            $admin_user->notify(new UserNotification($data));

            Mail::to($admin->email)->send(new CommonSaasEmail($mail_data));
        }
    }
    /**
     * will send new subscription notification to subscriber
     *
     */
    public function newSubscriptionNotificationToSubscriber(int $saas_account_id): void
    {
        $saas_account = $this->getNotifiableSaasAccountDetails($saas_account_id);

        $mail_data = [
            'template_id' => 15,
            'keywords' => getEmailTemplateVariables(15, true),
            'subject' => $this->getSubjectByTemplateId(15),

            '_package_name_' =>  $saas_account->package_name,
            '_member_type_' =>  $saas_account->membership_type,
            '_valid_till_' =>  $saas_account->valid_till,

            '_store_name_' => $saas_account->store_name,
            '_store_link_' =>  'https://' . $saas_account->domain,
            '_contact_us_' =>  'https://' . $saas_account->domain . '/contact'
        ];

        $user = User::find($saas_account->user_id);

        $data = [
            'message' => 'Subscription successful.' . 'Store Name: ' . $saas_account->store_name . ' Plan Name: ' . $saas_account->plan_name . ' Package Name: ' . $saas_account->package_name . ' Status: ' . ($saas_account->status == 1 ? 'Active' . ' Till: ' . $saas_account->valid_till : 'Inactive'),
            'link' => '/' . getSaasPrefix() . '/stores'
        ];
        $user->notify(new UserNotification($data));

        Mail::to($user->email)->send(new CommonSaasEmail($mail_data));
    }

    /**
     * will send new subscription notification to admin
     */
    public function newSubscriptionNotificationAdmin(int $saas_account_id): void
    {
        $saas_account = $this->getNotifiableSaasAccountDetails($saas_account_id);

        $user = User::find($saas_account->user_id);

        $mail_data = [
            'template_id' => 20,
            'keywords' => getEmailTemplateVariables(20, true),
            'subject' => $this->getSubjectByTemplateId(20),

            '_customer_name_' =>  $user->name,
            '_package_name_' =>  $saas_account->package_name,
            '_plan_name_' =>  $saas_account->plan_name,
            '_member_type_' =>  $saas_account->membership_type,
            '_valid_till_' =>  $saas_account->valid_till,
            '_store_link_' =>  'https://' . $saas_account->domain
        ];

        $admins = $this->getAllNotifiableAdmins(20);
        foreach ($admins as $admin) {
            $admin_user = User::find($admin->id);
            $data = [
                'message' => $user->name . ' has subscribed into package: ' . $saas_account->package_name . ' plan: ' . $saas_account->plan_name . ' status: ' . ($saas_account->status == 1 ? 'Active' . ' Till: ' . $saas_account->valid_till : 'Inactive'),
                'link' => '/' . getAdminPrefix() . '/stores'
            ];
            $admin_user->notify(new UserNotification($data));
            Mail::to($admin->email)->send(new CommonSaasEmail($mail_data));
        }
    }

    /**
     * will send new subscription notification after changing subscription plan
     */
    public function changeSubscriptionNotificationToSubscriber(int $saas_account_id): void
    {
        $saas_account = $this->getNotifiableSaasAccountDetails($saas_account_id);

        $mail_data = [
            'template_id' => 18,
            'keywords' => getEmailTemplateVariables(18, true),
            'subject' => $this->getSubjectByTemplateId(18),

            '_package_name_' =>  $saas_account->package_name,
            '_member_type_' =>  $saas_account->membership_type,
            '_valid_till_' =>  $saas_account->valid_till,

            '_store_name_' => $saas_account->store_name,
            '_store_link_' =>  'https://' . $saas_account->domain,
            '_contact_us_' =>  'https://' . $saas_account->domain . '/contact'
        ];

        $user = User::find($saas_account->user_id);
        $data = [
            'message' => 'Subscription plan updated.' . 'Store Name: ' . $saas_account->store_name . ' Plan Name: ' . $saas_account->plan_name . ' Package Name: ' . $saas_account->package_name . ' Status: ' . ($saas_account->status == 1 ? 'Active' . ' Till: ' . $saas_account->valid_till : 'Inactive'),
            'link' => '/' . getSaasPrefix() . '/stores'
        ];
        $user->notify(new UserNotification($data));


        Mail::to($user->email)->send(new CommonSaasEmail($mail_data));
    }

    /**
     * will send change subscription notification to admin
     */
    public function changeSubscriptionNotificationToAdmin(int $saas_account_id): void
    {
        $saas_account = $this->getNotifiableSaasAccountDetails($saas_account_id);

        $user = User::find($saas_account->user_id);

        $mail_data = [
            'template_id' => 22,
            'keywords' => getEmailTemplateVariables(22, true),
            'subject' => $this->getSubjectByTemplateId(22),

            '_customer_name_' =>  $user->name,
            '_plan_name_' =>  $saas_account->plan_name,
            '_package_name_' =>  $saas_account->package_name,
            '_member_type_' =>  $saas_account->membership_type,
            '_valid_till_' =>  $saas_account->valid_till,
            '_store_name_' =>  $saas_account->store_name,
            '_store_link_' =>  'https://' . $saas_account->domain
        ];

        $admins = $this->getAllNotifiableAdmins(22);
        foreach ($admins as $admin) {
            $admin_user = User::find($admin->id);
            $data = [
                'message' => $user->name . ' has changed subscription to package: ' . $saas_account->package_name . ' plan: ' . $saas_account->plan_name . ' status: ' . ($saas_account->status == 1 ? 'Active' . ' Till: ' . $saas_account->valid_till : 'Inactive'),
                'link' => '/' . getAdminPrefix() . '/stores'
            ];
            $admin_user->notify(new UserNotification($data));

            Mail::to($admin->email)->send(new CommonSaasEmail($mail_data));
        }
    }

    /**
     * will send store status update notification
     *
     */
    public function storeStatusUpdateNotificationToSubscriber(int $saas_account_id): void
    {
        $saas_account = $this->getNotifiableSaasAccountDetails($saas_account_id);

        $mail_data = [
            'template_id' => 23,
            'keywords' => getEmailTemplateVariables(23, true),
            'subject' => $this->getSubjectByTemplateId(23),

            '_store_name_' => $saas_account->store_name,
            '_status_' => $saas_account->store_name,
            '_store_list_page_' => url('/') . '/' . getSaasPrefix() . '/stores'
        ];

        $user = User::find($saas_account->user_id);
        $data = [
            'message' => 'Store status updated.' . 'Store Name: ' . $saas_account->store_name . ' Status: ' . ($saas_account->status == 1 ? 'Active' : 'Inactive'),
            'link' => '/' . getSaasPrefix() . '/stores'
        ];
        $user->notify(new UserNotification($data));

        Mail::to($user->email)->send(new CommonSaasEmail($mail_data));
    }

    /**
     * will send new reminder notification before subscription expired
     *
     */
    public function subscriptionReminderNotificationToSubscriber(int $saas_account_id): void
    {
        $saas_account = $this->getNotifiableSaasAccountDetails($saas_account_id);

        $mail_data = [
            'template_id' => 16,
            'keywords' => getEmailTemplateVariables(16, true),
            'subject' => $this->getSubjectByTemplateId(16),
            '_valid_till_' =>  $saas_account->valid_till,
            '_store_name_' => $saas_account->store_name,
            '_subscription_renew_link_' =>  url('/') . '/' . getSaasPrefix() . '/change-subscription-plan' . '/' . $saas_account->id,
            '_contact_us_' =>  'https://' . $saas_account->domain . '/contact'
        ];

        $user = User::find($saas_account->user_id);
        $data = [
            'message' => 'Your subscription will end on ' . $saas_account->valid_till . '. Please renew subscription to stay with us',
            'link' => '/' . getSaasPrefix() . '/stores'
        ];
        $user->notify(new UserNotification($data));
        Mail::to($user->email)->send(new CommonSaasEmail($mail_data));
    }

    /**
     * will send new subscription expired notification
     *
     */
    public function subscriptionExpiredNotificationToSubscriber(int $saas_account_id): void
    {
        $saas_account = $this->getNotifiableSaasAccountDetails($saas_account_id);
        $mail_data = [
            'template_id' => 17,
            'keywords' => getEmailTemplateVariables(17, true),
            'subject' => $this->getSubjectByTemplateId(17),
            '_store_name_' => $saas_account->store_name,
            '_subscription_renew_link_' =>  url('/') . '/' . getSaasPrefix() . '/change-subscription-plan' . '/' . $saas_account->id,
            '_contact_us_' =>  'https://' . $saas_account->domain . '/contact'
        ];

        $user = User::find($saas_account->user_id);
        $data = [
            'message' => 'Your subscription period ends . Please renew subscription to stay with us',
            'link' => '/' . getSaasPrefix() . '/stores'
        ];
        $user->notify(new UserNotification($data));

        Mail::to($user->email)->send(new CommonSaasEmail($mail_data));
    }
    /**
     * will send custom domain request status update notification
     */
    function customDomainRequestStatusUpdateToSubscriber(int $saas_account_id): void
    {
        $saas_account = SaasAccount::find((int)$saas_account_id);

        $status = "";
        if ($saas_account->customDomain->status == 0) {
            $status = 'pending';
        }
        if ($saas_account->customDomain->status == 1) {
            $status = 'approved';
        }
        if ($saas_account->customDomain->status == 2) {
            $status = 'cancelled';
        }

        $mail_data = [
            'template_id' => 25,
            'keywords' => getEmailTemplateVariables(25, true),
            'subject' => $this->getSubjectByTemplateId(25),

            '_status_' => $status,
            '_custom_domain_' => $saas_account->customDomain->requested_domain,
            '_sub_domain_' => $saas_account->customDomain->current_domain,
            '_see_custom_domain_requests_' => url('/') . '/' . getSaasPrefix() . '/custom-domain'
        ];

        $user = User::find($saas_account->user_id);
        $data = [
            'message' => "Your custom domain request of " . $saas_account->customDomain->requested_domain . ' for ' . $saas_account->customDomain->current_domain . ' is ' . $status,
            'link' => '/' . getSaasPrefix() . '/custom-domain'
        ];
        $user->notify(new UserNotification($data));

        Mail::to($user->email)->send(new CommonSaasEmail($mail_data));
    }

    /**
     * will send new custom domain request notification
     *
     */
    public function newCustomDomainRequestNotificationToAdmin(int $saas_account_id): void
    {
        $saas_account = SaasAccount::find((int)$saas_account_id);
        $mail_data = [
            'template_id' => 24,
            'keywords' => getEmailTemplateVariables(24, true),
            'subject' => $this->getSubjectByTemplateId(24),

            '_custom_domain_' =>  $saas_account->customDomain->requested_domain,
            '_sub_domain_' =>  $saas_account->customDomain->current_domain,
            '_see_custom_domain_requests_' => url('/') . getAdminPrefix() . '/custom-domains'
        ];

        $admins = $this->getAllNotifiableAdmins(25);
        foreach ($admins as $admin) {
            $admin_user = User::find($admin->id);
            $data = [
                'message' => "A new custom domain request of " . $saas_account->customDomain->requested_domain . " has come  for " . $saas_account->customDomain->current_domain,
                'link' => '/' . getAdminPrefix() . '/custom-domains'
            ];
            $admin_user->notify(new UserNotification($data));

            Mail::to($admin->email)->send(new CommonSaasEmail($mail_data));
        }
    }

    /**
     * get all admins to send notification
     */
    public function getAllNotifiableAdmins($template_id)
    {
        $admins = DB::table('tl_saas_notifications_to_roles')
            ->join('model_has_roles', 'model_has_roles.role_id', '=', 'tl_saas_notifications_to_roles.role_id')
            ->join('tl_users', 'tl_users.id', '=', 'model_has_roles.model_id')
            ->where('tl_saas_notifications_to_roles.template_id', '=', $template_id)
            ->select([
                'tl_users.email',
                'tl_users.id'
            ])->get();
        return $admins;
    }
    /**
     * get subject by template id
     */
    public function getSubjectByTemplateId($template_id)
    {
        $email_template_properties = DB::table('tl_email_template_properties')
            ->where('tl_email_template_properties.email_type', '=', $template_id)
            ->first();
        return $email_template_properties == null ? '' : $email_template_properties->subject;
    }
}
