 @php
     $selecected_currency = \Plugin\Saas\Repositories\PaymentMethodRepository::configKeyValue(
         $method->id,
         'paymob_currency',
     );
     $default_currency = $selecected_currency == null ? getSaasDefaultCurrency() : $selecected_currency;
 @endphp
 <div class="border-top2 p-3 payment-method-item-body">
     <div class="configuration">
         <form id="credential-form">
             <input type="hidden" name="payment_id" value="{{ $method->id }}">
             <div class="form-group mb-20">
                 <label class="black bold mb-2">{{ translate('Logo') }}</label>
                 <div class="input-option">
                     @include('core::base.includes.media.media_input', [
                         'input' => 'paymob_logo',
                         'data' => \Plugin\Saas\Repositories\PaymentMethodRepository::configKeyValue(
                             $method->id,
                             'paymob_logo'),
                     ])
                     @if ($errors->has('paymob_logo'))
                         <div class="invalid-input">{{ $errors->first('paymob_logo') }}
                         </div>
                     @endif
                 </div>
             </div>
             <div class="form-group mb-20">
                 <label class="black bold">{{ translate('Currency') }}</label>
                 <div class="mb-2">
                     <a href="{{ route('plugin.saas.all.currencies') }}"
                         class="mt-2">({{ translate('Please setup exchange rate for the choosed currency') }})</a>
                 </div>
                 <div class="input-option">
                     <select name="paymob_currency" class="theme-input-style selectCurrency">
                         @foreach ($currencies as $currency)
                             <option value="{{ $currency->code }}" class="text-uppercase"
                                 {{ $currency->code == $default_currency ? 'selected' : '' }}>
                                 {{ $currency->name }}
                             </option>
                         @endforeach
                     </select>
                 </div>
             </div>
             <div class="form-group mb-20">
                 <label class="black bold mb-2">{{ translate('Paymob Api Key') }}</label>
                 <div class="input-option">
                     <input type="text" class="theme-input-style" name="paymob_api_key"
                         placeholder="Enter Paymob Api Key"
                         value="{{ \Plugin\Saas\Repositories\PaymentMethodRepository::configKeyValue($method->id, 'paymob_api_key') }}"
                         required />
                 </div>
             </div>

             <div class="form-group mb-20">
                 <label class="black bold mb-2">{{ translate('Integration ID') }}</label>
                 <div class="input-option">
                     <input type="text" class="theme-input-style" name="paymob_integration_id"
                         placeholder="Enter Paymob Integration ID"
                         value="{{ \Plugin\Saas\Repositories\PaymentMethodRepository::configKeyValue($method->id, 'paymob_integration_id') }}"
                         required />
                 </div>
             </div>

             <div class="form-group mb-20">
                 <label class="black bold mb-2">{{ translate('Paymob HMAC  Secret') }}</label>
                 <div class="input-option">
                     <input type="text" class="theme-input-style" name="paymob_hmac_secret"
                         placeholder="Enter Paymob HMAC  Secret"
                         value="{{ \Plugin\Saas\Repositories\PaymentMethodRepository::configKeyValue($method->id, 'paymob_hmac_secret') }}"
                         required />
                 </div>
             </div>

             <div class="form-group mb-20">
                 <label class="black bold mb-2">{{ translate('Paymob Iframe Key') }}</label>
                 <div class="input-option">
                     <input type="text" class="theme-input-style" name="paymob_iframe_key"
                         placeholder="Enter Paymob Iframe Key"
                         value="{{ \Plugin\Saas\Repositories\PaymentMethodRepository::configKeyValue($method->id, 'paymob_iframe_key') }}"
                         required />
                 </div>
             </div>

             <div class="form-group mb-20">
                 <label class="black bold mb-2">{{ translate('Instruction') }}</label>
                 <div class="input-option">
                     <textarea name="paymob_instruction" class="theme-input-style" placeholder="Enter Instruction">{{ \Plugin\Saas\Repositories\PaymentMethodRepository::configKeyValue($method->id, 'paymob_instruction') }}</textarea>
                 </div>
             </div>
             <div>
                 <button class="btn long payment-credental-update-btn"
                     data-payment-btn="{{ $method->id }}">{{ translate('Save Changes') }}</button>
             </div>
         </form>
     </div>
     <div class="instruction">
         <a href="https://accept.paymob.com/" target="_blank">Paymob</a>
         <p>
             {{ translate('Subscriber can subscribe via paymob') }}
         </p>
         <p class="semi-bold">
             {{ translate('Configuration instruction for Paymob') }}
         </p>
         <p>{{ translate('To use Paymob, you need:') }}</p>
         <ol>
             <li style="list-style-type: decimal">
                 {{ translate('Register with Paymob') }}
             </li>
             <li style="list-style-type: decimal">
                 <p>
                     {{ translate('Colllect api key') }}
                 </p>
             </li>
             <li style="list-style-type: decimal">
                 <p>
                     {{ translate('HMAC  Secret') }}
                 </p>
             </li>
             <li style="list-style-type: decimal">
                 <p>
                     {{ translate('Create Intregration and Collect ID') }}
                 </p>
             </li>
             <li style="list-style-type: decimal">
                 <p>
                     {{ translate('Create IFrame and Collect ID') }}
                 </p>
             </li>
         </ol>
         <p class="mb-0 mt-3 semi-bold">
             {{ translate('Callback URL') }}
         </p>
         <p class="alert alert-info">{{ route('plugin.saas.paymob.callback') }}</p>
     </div>
 </div>
