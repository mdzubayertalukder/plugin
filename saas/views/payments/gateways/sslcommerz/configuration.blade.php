@php
    $selecected_currency = \Plugin\Saas\Repositories\PaymentMethodRepository::configKeyValue(
        $method->id,
        'sslcz_currency',
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
                        'input' => 'sslcommerz_logo',
                        'data' => \Plugin\Saas\Repositories\PaymentMethodRepository::configKeyValue(
                            $method->id,
                            'sslcommerz_logo'),
                    ])
                    @if ($errors->has('sslcommerz_logo'))
                        <div class="invalid-input">{{ $errors->first('sslcommerz_logo') }}
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
                    <select name="sslcz_currency" class="theme-input-style selectCurrency">
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
                <label class="black bold mb-2">{{ translate('Sslcz Store Id') }}</label>
                <div class="input-option">
                    <input type="text" class="theme-input-style" name="sslcz_store_id" placeholder="Enter Store Id"
                        value="{{ \Plugin\Saas\Repositories\PaymentMethodRepository::configKeyValue($method->id, 'sslcz_store_id') }}" />
                </div>
            </div>
            <div class="form-group mb-20">
                <label class="black bold mb-2">{{ translate('Sslcz store password') }}</label>
                <div class="input-option">
                    <input type="text" class="theme-input-style" name="sslcz_store_password"
                        placeholder="Enter Store Password"
                        value="{{ \Plugin\Saas\Repositories\PaymentMethodRepository::configKeyValue($method->id, 'sslcz_store_password') }}" />
                </div>
            </div>
            <div class="form-group mb-20">
                <div class="d-flex">
                    <label class="black bold">{{ translate('Sandbox mode') }}</label>
                    <label class="switch glow primary medium ml-2">
                        <input type="checkbox" name="sandbox" @if (
                            \Plugin\Saas\Repositories\PaymentMethodRepository::configKeyValue($method->id, 'sandbox') ==
                                config('settings.general_status.active')) checked @endif />
                        <span class="control"></span>
                    </label>

                </div>
            </div>
            <div class="form-group mb-20">
                <label class="black bold mb-2">{{ translate('Instruction') }}</label>
                <div class="input-option">
                    <textarea name="sslcommerz_instruction" class="theme-input-style">{{ \Plugin\Saas\Repositories\PaymentMethodRepository::configKeyValue($method->id, 'sslcommerz_instruction') }}</textarea>
                </div>
            </div>
            <div>
                <button class="btn long payment-credental-update-btn"
                    data-payment-btn="{{ $method->id }}">{{ translate('Save Changes') }}</button>
            </div>
        </form>
    </div>
    <div class="ml-5 instruction">
        <a href="https://sslcommerz.com/" target="_blank">{{ translate('SSLCommerz') }}</a>
        <p>
            {{ translate('Customer can buy product and pay directly using
                                                                                    sslcommerz') }}
        </p>
        <p class="semi-bold">
            {{ translate('Configuration instruction for sslcommerz') }}
        </p>
        <p>{{ translate('To use Sslcommerz, you need:') }}</p>
        <ol>
            <li style="list-style-type: decimal">
                {{ translate('Register with Sslcommerz') }}
            </li>
            <li style="list-style-type: decimal">
                <p>
                    {{ translate('After registration at Sslcommerz, you will have
                                                                                                    Store Id,store password') }}
                </p>
            </li>
            <li style="list-style-type: decimal">
                <p>
                    {{ translate('Enter Store Id,store password into the box in left
                                                                                                    hand') }}
                </p>
            </li>
        </ol>
    </div>
</div>
