<?php

namespace Plugin\TlcommerceCore\Repositories;

use Core\Models\User;
use Core\Http\Mail\NewOrderMail;
use Core\Jobs\SendTenantMailJob;
use Core\Http\Mail\OrderRefundMail;
use Core\Http\Mail\OrderConfirmMail;
use Illuminate\Support\Facades\Mail;
use Core\Http\Mail\ProductReviewEmail;
use Plugin\TlcommerceCore\Models\Orders;
use Core\Http\Mail\OrderStatusUpdateMail;
use Plugin\TlcommerceCore\Models\Customers;
use Illuminate\Support\Facades\Notification;
use Plugin\TlcommerceCore\Models\OrderHasProducts;
use Plugin\TlcommerceCore\Repositories\SettingsRepository;
use Plugin\TlcommerceCore\Notifications\ProductApprovalNotification;
use Plugin\TlcommerceCore\Notifications\OrderStatusUpdateNotification;
use Plugin\TlcommerceCore\Notifications\CustomerOrderCancelNotification;
use Plugin\TlcommerceCore\Notifications\CustomerOrderCreateNotification;
use Plugin\TlcommerceCore\Notifications\CustomerOrderReturnNotification;
use Plugin\TlcommerceCore\Notifications\CustomerProductReviewNotification;
use Plugin\TlcommerceCore\Notifications\CustomerOrderPaymentCompletedNotification;

class EcommerceNotification
{
    /**
     * Will send order status notification to customer
     *
     * @param Int $order_id
     * @param Int $customer_id
     * @param String $message
     */
    public static function sendOrderStatusNotification($order_id, $customer_id, $message, $btn_title, $mail_title)
    {

        $link = '/dashboard/order-details/' . $order_id;
        $data = [
            'message' => $message,
            'link' => $link
        ];
        $notifiable_customer = Customers::where('id', $customer_id)->first();
        if ($notifiable_customer != null) {
            $notifiable_customer->notify(new OrderStatusUpdateNotification($data));
            //Send mail to customer
            $mail_data = [
                'template_id' => 11,
                'keywords' => getEmailTemplateVariables(11, true),
                'subject' => $mail_title,
                '_tracking_url_' => url('/') . '/dashboard/order-details/' . $order_id,
                '_customer_name_' => $notifiable_customer->name,
                '_message_' => $message,
                '_btn_title_' => $btn_title,
                '_mail_title_' => $mail_title,
            ];
            SendTenantMailJob::dispatch($notifiable_customer->email, $mail_data, getTenantMailConfig());
        }
    }
    /**
     * Will send order item status notification to seller
     *
     * @param Int $seller
     * @param String $message
     */
    public static function sendOrderItemUpdateStatusNotificationToSeller($order_id, $seller_id, $message)
    {

        $seller_link = '/seller/order-details/' . $order_id;
        $seller_data = [
            'message' => $message,
            'link' => $seller_link
        ];
        $notifiable_seller = User::where('id', $seller_id)->where('user_type', config('tlecommercecore.user_type.seller'))->get();
        if ($notifiable_seller != null) {
            Notification::send($notifiable_seller, new OrderStatusUpdateNotification($seller_data));
        }
    }
    /**
     * Will send order status notification to admin
     *
     * @param Int $order_id
     */
    public static function sendSellerCreateProductNotificationToAdmin($product_id)
    {
        $link = '/seller-products';
        $message = translate('Seller create a new product');
        $data = [
            'message' => $message,
            'link' => $link
        ];
        //Send notification to admin
        $notifiable_admins = User::where('user_type', config('tlecommercecore.user_type.admin'))->where('status', config('settings.general_status.active'))->get();
        if ($notifiable_admins != null) {
            Notification::send($notifiable_admins, new OrderStatusUpdateNotification($data));
        }
    }
    /**
     * Will send order status notification to admin
     *
     * @param Int $order_id
     */
    public static function sendSellerOrderStatusNotificationToAdmin($order_id, $message = null)
    {
        $link = '/orders/order-details/' . $order_id;
        $order_details = Orders::where('id', $order_id)->first();
        $message = 'Order code ' . $order_details->order_code . ' has been accepted by seller';
        $data = [
            'message' => $message,
            'link' => $link
        ];
        //Send notification to admin
        $notifiable_admins = User::where('user_type', config('tlecommercecore.user_type.admin'))->where('status', config('settings.general_status.active'))->get();
        if ($notifiable_admins != null) {
            Notification::send($notifiable_admins, new OrderStatusUpdateNotification($data));
        }
    }
    /**
     * Will send new order notification
     */
    public static function sendNewOrderNotification($order)
    {
        //Send notification to admin
        $link = '/orders/order-details/' . $order->id;
        $message =  "New order has been placed. Order code " . $order->order_code;
        $data = [
            'message' => $message,
            'link' => $link
        ];
        $admins = User::where('user_type', config('tlecommercecore.user_type.admin'))->where('status', config('settings.general_status.active'))->get();
        if ($admins != null) {
            Notification::send($admins, new CustomerOrderCreateNotification($data));
        }
        //Send Email to admin
        if (SettingsRepository::getEcommerceSetting('admin_new_order_email_notification') == config('settings.general_status.active')) {
            $admin_emails = User::where('user_type', config('tlecommercecore.user_type.admin'))->where('status', config('settings.general_status.active'))->pluck('email');
            $mail_data = [
                'template_id' => 13,
                'keywords' => getEmailTemplateVariables(13, true),
                'subject' => 'New Order Placed!',
                '_order_code_' =>  $order->order_code,
                '_tracking_url_' => url('/') . '/' . getAdminPrefix() . '/orders/order-details/' . $order->id,
                '_order_details_' => view('plugin/tlecommercecore::mail.order_details_mail', ['order_id' => $order->id])->render(),
            ];

            SendTenantMailJob::dispatch($admin_emails, $mail_data, getTenantMailConfig());
        }

        //send notification to seller
        if (isActivePluging('multivendor')) {
            $seller_link = '/seller/order-details/' . $order->id;
            $seller_data = [
                'message' => $message,
                'link' => $seller_link
            ];
            $seller_ids = OrderHasProducts::where('order_id', $order->id)->distinct()->pluck('seller_id');

            $sellers = User::whereIn('id', $seller_ids)->where('user_type', config('tlecommercecore.user_type.seller'))->get();
            if ($sellers != null) {
                Notification::send($sellers, new CustomerOrderCreateNotification($seller_data));
            }
        }

        //Send invoice to customer
        if (SettingsRepository::getEcommerceSetting('send_invoice_to_customer_mail') == config('settings.general_status.active')) {
            $customer_email = $order->customer_info != null ? $order->customer_info?->email : $order->guest_customer?->email;
            $customer_name = $order->customer_info != null ? $order->customer_info?->name : 'Guest Customer';
            if ($customer_email != null) {
                $mail_data = [
                    'template_id' => 10,
                    'keywords' => getEmailTemplateVariables(10, true),
                    'subject' => 'Your order has been placed!',
                    '_order_code_' =>  $order->order_code,
                    '_tracking_url_' => url('/') . '/dashboard/order-details/' . $order->id,
                    '_customer_name_' => $customer_name,
                    '_order_details_' => view('plugin/tlecommercecore::mail.order_details_mail', ['order_id' => $order->id])->render(),
                ];
                SendTenantMailJob::dispatch($customer_email, $mail_data, getTenantMailConfig());
            }
        }
    }
    /**
     * Will send new order notification
     *
     * @param Int $order_id
     */
    public static function sendCustomerOrderCancelNotification($order_id, $message)
    {

        //Send notification to admin
        $link = '/orders/order-details/' . $order_id;
        $data = [
            'message' => $message,
            'link' => $link
        ];
        $admins = User::where('user_type', config('tlecommercecore.user_type.admin'))->where('status', config('settings.general_status.active'))->get();
        if ($admins != null) {
            Notification::send($admins, new CustomerOrderCancelNotification($data));
        }

        //Send customer order cancel email notification to admin
        if (SettingsRepository::getEcommerceSetting('admin_order_cancel_email_notification') == config('settings.general_status.active')) {
            $admin_emails = User::where('user_type', config('tlecommercecore.user_type.admin'))->where('status', config('settings.general_status.active'))->pluck('email');
            $mail_data = [
                'template_id' => 14,
                'keywords' => getEmailTemplateVariables(14, true),
                'subject' => 'Order Cancelled!',
                '_mail_title_' =>  "Order Cancelled",
                '_btn_title_' =>  "View Order Details",
                '_message_' =>  $message,
                '_action_url_' => url('/') . '/' . getAdminPrefix() . '/orders/order-details/' . $order_id,
            ];
            SendTenantMailJob::dispatch($admin_emails, $mail_data, getTenantMailConfig());
        }

        //send notification to seller
        if (isActivePluging('multivendor')) {
            $seller_link = '/seller/order-details/' . $order_id;
            $seller_data = [
                'message' => $message,
                'link' => $seller_link
            ];
            $seller_ids = OrderHasProducts::where('order_id', $order_id)->distinct()->pluck('seller_id');
            $notifiable_sellers = User::whereIn('id', $seller_ids)->where('user_type', config('tlecommercecore.user_type.seller'))->get();
            if ($notifiable_sellers != null) {
                Notification::send($notifiable_sellers, new CustomerOrderCancelNotification($seller_data));
            }
        }
    }

    /**
     * Will send customer product review notification to admin
     *
     * @param String $message
     */
    public static function sendCustomerProductReviewNotification($message)
    {
        $link = '/product-reviews';
        $data = [
            'message' => $message,
            'link' => $link
        ];
        $users = User::where('user_type', config('tlecommercecore.user_type.admin'))->where('status', config('settings.general_status.active'))->get();
        if ($users != null) {
            Notification::send($users, new CustomerProductReviewNotification($data));
        }

        //Send customer product review email notification to admin
        if (SettingsRepository::getEcommerceSetting('admin_product_review_email_notification') == config('settings.general_status.active')) {
            $admin_emails = User::where('user_type', config('tlecommercecore.user_type.admin'))->where('status', config('settings.general_status.active'))->pluck('email');
            $mail_data = [
                'template_id' => 14,
                'keywords' => getEmailTemplateVariables(14, true),
                'subject' => 'Product Review',
                '_mail_title_' =>  "Product Review Received",
                '_btn_title_' =>  "View Product Reviews",
                '_message_' =>  $message,
                '_action_url_' => url('/') . '/' . getAdminPrefix() . '/product-reviews',
            ];
            SendTenantMailJob::dispatch($admin_emails, $mail_data, getTenantMailConfig());
        }
    }

    /**
     * Will send customer order return notification to admin
     *
     * @param Int $id
     * @param String $message
     */
    public static function sendCustomerOrderReturnNotification($refund_id, $message, $seller_id = null)
    {
        //Send notification to admin
        $link = '/refunds/refund-request-details/' . $refund_id;
        $data = [
            'message' => $message,
            'link' => $link
        ];
        $notifiable_admins = User::where('user_type', config('tlecommercecore.user_type.admin'))->where('status', config('settings.general_status.active'))->get();
        if ($notifiable_admins != null) {
            Notification::send($notifiable_admins, new CustomerOrderReturnNotification($data));
        }
        //Send email notification to admin
        if (SettingsRepository::getEcommerceSetting('admin_order_refund_email_notification') == config('settings.general_status.active')) {
            $admin_emails = User::where('user_type', config('tlecommercecore.user_type.admin'))->where('status', config('settings.general_status.active'))->pluck('email');
            $mail_data = [
                'template_id' => 14,
                'keywords' => getEmailTemplateVariables(14, true),
                'subject' => 'Refund Request Created',
                '_mail_title_' =>  "Refund Request Created",
                '_btn_title_' =>  "View Request Details",
                '_message_' =>  $message,
                '_action_url_' => url('/') . '/' . getAdminPrefix() . '/refunds/refund-request-details/' . $refund_id,
            ];
            SendTenantMailJob::dispatch($admin_emails, $mail_data, getTenantMailConfig());
        }

        //send notification to seller
        if ($seller_id != null && isActivePluging('multivendor')) {
            $seller_link = '/seller/refunds';
            $seller_data = [
                'message' => $message,
                'link' => $seller_link
            ];
            $notifiable_seller = User::where('id', $seller_id)->where('user_type', config('tlecommercecore.user_type.seller'))->get();
            if ($notifiable_seller != null) {
                Notification::send($notifiable_seller, new CustomerOrderReturnNotification($seller_data));
            }
        }
    }

    /**
     * Will send customer order payment completed to admin
     *
     * @param Int $order_id
     * @param String $message
     */
    public static function sendCustomerOrderPaymentCompletedNotification($order_id, $message)
    {
        $link = '/orders/order-details/' . $order_id;
        $data = [
            'message' => $message,
            'link' => $link
        ];
        $notifiable_admins = User::where('user_type', config('tlecommercecore.user_type.admin'))->where('status', config('settings.general_status.active'))->get();
        if ($notifiable_admins != null) {
            Notification::send($notifiable_admins, new CustomerOrderPaymentCompletedNotification($data));
        }
    }
    /**
     * Will send payout request status update notification to seller
     *
     * @param Int $seller
     * @param String $message
     */
    public static function sendPayoutRequestStatusUpdateNotificationToSeller($seller_id, $message)
    {
        $seller_link = '/seller/payout-requests';
        $seller_data = [
            'message' => $message,
            'link' => $seller_link
        ];
        $notifiable_seller = User::where('id', $seller_id)->where('user_type', config('tlecommercecore.user_type.seller'))->get();
        if ($notifiable_seller != null) {
            Notification::send($notifiable_seller, new OrderStatusUpdateNotification($seller_data));
        }
    }
    /**
     * Will send payout request create notification to admin
     *
     * @param Int $seller
     * @param String $message
     */
    public static function sendPayoutRequestNotificationToAdmin()
    {

        $link = '/seller-payout-requests';
        $data = [
            'message' => 'A seller create a payout request',
            'link' => $link
        ];
        $notifiable_admins = User::where('user_type', config('tlecommercecore.user_type.admin'))->where('status', config('settings.general_status.active'))->get();
        if ($notifiable_admins != null) {
            Notification::send($notifiable_admins, new CustomerOrderPaymentCompletedNotification($data));
        }
    }

    /**
     * Will send earning notification to seller
     *
     * @param Int $seller
     * @param String $message
     */
    public static function sendEarningNotificationToSeller($seller_id, $message)
    {

        $seller_link = '/seller/earning';
        $seller_data = [
            'message' => $message,
            'link' => $seller_link
        ];
        $notifiable_seller = User::where('id', $seller_id)->where('user_type', config('tlecommercecore.user_type.seller'))->get();
        if ($notifiable_seller != null) {
            Notification::send($notifiable_seller, new OrderStatusUpdateNotification($seller_data));
        }
    }

    /**
     * Will send update seller product approval status notification to seller
     *
     * @param Int $seller
     * @param String $message
     */
    public static function sendUpdateProductApprovalStatusNotificationToSeller($seller_id, $message)
    {

        $seller_link = '/seller/products';
        $seller_data = [
            'message' => $message,
            'link' => $seller_link
        ];
        $notifiable_seller = User::where('id', $seller_id)->where('user_type', config('tlecommercecore.user_type.seller'))->get();
        if ($notifiable_seller != null) {
            Notification::send($notifiable_seller, new ProductApprovalNotification($seller_data));
        }
    }
}
