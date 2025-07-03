 <div class="card">
     <div class="card-body">
         <div class="form-row mb-20">
             <div class="col-sm-6">
                 <label class="font-14 bold black">{{ translate('New Order Email Notification') }}
                 </label>
             </div>
             <div class="col-sm-6">
                 <label class="switch glow primary medium">
                     <input type="checkbox" name="admin_new_order_email_notification" @checked(getEcommerceSetting('admin_new_order_email_notification') == config('settings.general_status.active'))>
                     <span class="control"></span>
                 </label>
             </div>
         </div>
         <div class="form-row mb-20">
             <div class="col-sm-6">
                 <label class="font-14 bold black">{{ translate('Order Refund Email Notification') }}
                 </label>
             </div>
             <div class="col-sm-6">
                 <label class="switch glow primary medium">
                     <input type="checkbox" name="admin_order_refund_email_notification" @checked(getEcommerceSetting('admin_order_refund_email_notification') == config('settings.general_status.active'))>
                     <span class="control"></span>
                 </label>
             </div>
         </div>
         <div class="form-row mb-20">
             <div class="col-sm-6">
                 <label class="font-14 bold black">{{ translate('Order Cancel Email Notification') }}
                 </label>
             </div>
             <div class="col-sm-6">
                 <label class="switch glow primary medium">
                     <input type="checkbox" name="admin_order_cancel_email_notification" @checked(getEcommerceSetting('admin_order_cancel_email_notification') == config('settings.general_status.active'))>
                     <span class="control"></span>
                 </label>
             </div>
         </div>
         <div class="form-row mb-20">
             <div class="col-sm-6">
                 <label class="font-14 bold black">{{ translate('Product Review Email Notification') }}
                 </label>
             </div>
             <div class="col-sm-6">
                 <label class="switch glow primary medium">
                     <input type="checkbox" name="admin_product_review_email_notification" @checked(getEcommerceSetting('admin_product_review_email_notification') == config('settings.general_status.active'))>
                     <span class="control"></span>
                 </label>
             </div>
         </div>
         @if (isActivePluging('wallet'))
             <div class="form-row mb-20">
                 <div class="col-sm-6">
                     <label class="font-14 bold black">{{ translate('Wallet Recharge Email Notification') }}
                     </label>
                 </div>
                 <div class="col-sm-6">
                     <label class="switch glow primary medium">
                         <input type="checkbox" name="admin_wallet_recharge_email_notification"
                             @checked(getEcommerceSetting('admin_wallet_recharge_email_notification') == config('settings.general_status.active'))>
                         <span class="control"></span>
                     </label>
                 </div>
             </div>
             <div class="form-row mb-20">
                 <div class="col-12">
                     <h5 class="text text-danger mb-2">{{ translate('Note') }}</h5>
                     <p class="mt-0 font-13">
                         {{ translate('Enable  email notification you need to complete email configuration and Cron Job setup') }}
                         <br>
                         <a href="{{ route('core.email.smtp.configuration') }}"
                             class="btn-link">{{ translate('Configure Email') }}
                         </a>
                     </p>
                 </div>
             </div>
         @endif
     </div>
 </div>
