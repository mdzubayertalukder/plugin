 @php
     $currencies = getAllCurrencies();
     $selecected_currency = \Plugin\TlcommerceCore\Repositories\PaymentMethodRepository::configKeyValue(
         $method->id,
         'mollie_currency',
     );
     $default_currency = $selecected_currency == null ? getDefaultCurrency() : $selecected_currency;
 @endphp
 <div class="p-3 payment-method-item-body">
     <div class="configuration">
         <form id="credential-form">
             <input type="hidden" name="payment_id" value="{{ $method->id }}">
             <div class="form-group mb-20">
                 <label class="black bold mb-2">{{ translate('Logo') }}</label>
                 <div class="input-option">
                     @include('core::base.includes.media.media_input', [
                         'input' => 'mollie_logo',
                         'data' => \Plugin\TlcommerceCore\Repositories\PaymentMethodRepository::configKeyValue(
                             $method->id,
                             'mollie_logo'),
                     ])
                 </div>
             </div>
             <div class="form-group mb-20">
                 <label class="black bold">{{ translate('Currency') }}</label>
                 <div class="mb-2">
                     <a href="{{ route('plugin.tlcommercecore.ecommerce.all.currencies') }}"
                         class="mt-2">({{ translate('Please setup exchange rate for the choosed currency') }})</a>
                 </div>
                 <div class="input-option">
                     <select name="mollie_currency" class="theme-input-style selectCurrency">
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
                 <label class="black bold mb-2">{{ translate('Mollie Api Key') }}</label>
                 <div class="input-option">
                     <input type="text" class="theme-input-style" name="mollie_api_key"
                         placeholder="Enter Mollie Api Key"
                         value="{{ \Plugin\TlcommerceCore\Repositories\PaymentMethodRepository::configKeyValue($method->id, 'mollie_api_key') }}"
                         required />
                 </div>
             </div>

             <div class="form-group mb-20">
                 <label class="black bold mb-2">{{ translate('Instruction') }}</label>
                 <div class="input-option">
                     <textarea name="mollie_instruction" id="instruction" class="theme-input-style">{{ \Plugin\TlcommerceCore\Repositories\PaymentMethodRepository::configKeyValue($method->id, 'mollie_instruction') }}</textarea>
                 </div>
             </div>
             <div>
                 <button class="btn long payment-credental-update-btn"
                     data-payment-btn="{{ $method->id }}">{{ translate('Save Changes') }}</button>
             </div>
         </form>
     </div>
     <div class="instruction">
         <a href="https://www.paddle.com/" target="_blank">Mollie</a>
         <p>
             Customer can buy product and pay directly using mollie
         </p>
         <p class="semi-bold">
             Configuration instruction for Moolie
         </p>
         <p>To use Mollie, you need:</p>
         <ol>
             <li style="list-style-type: decimal">
                 Register with Mollie
             </li>
             <li style="list-style-type: decimal">
                 <p>
                     After registration at mollie, you will have
                     mollie api key
                 </p>
             </li>
             <li style="list-style-type: decimal">
                 <p>
                     Enter mollie api key
                 </p>
             </li>
             <li style="list-style-type: decimal">
                 <p>
                     See mollie supported currency list, <a
                         href="https://docs.mollie.com/payments/multicurrency">here</a>
                 </p>
             </li>
         </ol>
     </div>
 </div>
