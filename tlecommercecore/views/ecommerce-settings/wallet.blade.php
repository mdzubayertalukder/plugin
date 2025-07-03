 <div class="card">
     <div class="card-body">
         @if (!isTenant() && !isActivePluging('wallet'))
             <p class="mt-0 font-13">{{ translate('You need to active or install') }} <a
                     href="{{ route('core.plugins.index') }}" class="btn-link">Wallet Plugin</a>
                 {{ translate('to manage wallets') }}</p>
         @endif
         <div class="{{ isActivePluging('wallet') ? '' : 'area-disabled mb-0' }}">
             <div class="form-row mb-20">
                 <div class="col-sm-6">
                     <label class="font-14 bold black">{{ translate('Enable online recharge') }}
                     </label>
                 </div>
                 <div class="col-sm-6">
                     <label class="switch glow primary medium">
                         <input type="checkbox" name="enable_wallet_online_recharge" @checked(getEcommerceSetting('enable_wallet_online_recharge') == config('settings.general_status.active'))>
                         <span class="control"></span>
                     </label>
                 </div>
             </div>
             <div class="form-row mb-20">
                 <div class="col-sm-6">
                     <label class="font-14 bold black">{{ translate('Enable offline recharge') }}
                     </label>
                 </div>
                 <div class="col-sm-6">
                     <label class="switch glow primary medium">
                         <input type="checkbox" name="enable_wallet_offline_recharge" @checked(getEcommerceSetting('enable_wallet_offline_recharge') == config('settings.general_status.active'))>
                         <span class="control"></span>
                     </label>
                 </div>
             </div>
             <div class="form-row mb-20">
                 <div class="col-sm-6">
                     <label class="font-14 bold black">{{ translate('Minimum recharge amount') }}
                     </label>
                 </div>
                 <div class="col-sm-3">
                     <input type="number" class="theme-input-style" name="minimum_wallet_recharge_amount"
                         value="{{ getEcommerceSetting('minimum_wallet_recharge_amount') }}">
                 </div>
             </div>
         </div>

     </div>
 </div>
