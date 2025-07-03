<?php

namespace Plugin\Wallet\Repositories;

use Core\Models\User;
use Core\Jobs\SendTenantMailJob;
use Illuminate\Support\Facades\Mail;
use Core\Http\Mail\WalletRechargeMail;
use Plugin\TlcommerceCore\Models\Customers;
use Illuminate\Support\Facades\Notification;
use Plugin\TlcommerceCore\Repositories\SettingsRepository;
use Plugin\Wallet\Notifications\CustomerWalletRechargeNotification;
use Plugin\Wallet\Notifications\WalletTransactionStatusUpdateNotification;

class WalletNotification
{

    /**
     * Wii send customer wallet recharge notification to admin
     *
     * @param String $message
     * @return void
     */
    public static function sendCustomerWalletRechargeNotification($message)
    {
        $link = '/wallet/wallet-transactions';
        $data = [
            'message' => $message,
            'link' => $link
        ];
        $users = User::whereNull('user_type')->get();
        if ($users != null) {
            Notification::send($users, new CustomerWalletRechargeNotification($data));
        }
        //Send email notification to admin
        if (SettingsRepository::getEcommerceSetting('admin_wallet_recharge_email_notification') == config('settings.general_status.active')) {
            $admin_emails = User::whereNull('user_type')->where('status', config('settings.general_status.active'))->pluck('email');
            info($admin_emails);
            $mail_data = [
                'template_id' => 14,
                'keywords' => getEmailTemplateVariables(14, true),
                'subject' => 'Wallet Recharge',
                '_mail_title_' =>  "Wallet Recharge",
                '_btn_title_' =>  "View Wallet Transactions",
                '_message_' =>  $message,
                '_action_url_' => url('/') . '/' . getAdminPrefix() . '/wallet/wallet-transactions',
            ];
            SendTenantMailJob::dispatch($admin_emails, $mail_data, getTenantMailConfig());
        }
    }

    /**
     * Wii send customer wallet recharge status update notification to customer
     *
     * @param String $message
     * @return void
     */
    public static function sendWalletStatusUpdateNotification($customer_id, $message)
    {
        $link = '/dashboard/wallet';
        $data = [
            'message' => $message,
            'link' => $link
        ];
        $customer = Customers::where('id', $customer_id)->first();
        if ($customer != null) {
            $customer->notify(new WalletTransactionStatusUpdateNotification($data));
        }
    }
}
