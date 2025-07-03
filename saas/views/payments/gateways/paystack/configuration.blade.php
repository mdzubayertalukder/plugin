@php
    $selecected_currency = \Plugin\Saas\Repositories\PaymentMethodRepository::configKeyValue(
        $method->id,
        'paystack_currency',
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
                        'input' => 'paystack_logo',
                        'data' => \Plugin\Saas\Repositories\PaymentMethodRepository::configKeyValue(
                            $method->id,
                            'paystack_logo'),
                    ])
                    @if ($errors->has('paystack_logo'))
                        <div class="invalid-input">{{ $errors->first('paystack_logo') }}
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
                    <select name="paystack_currency" class="theme-input-style selectCurrency">
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
                <label class="black bold mb-2">{{ translate('Paystack Public Key') }}</label>
                <div class="input-option">
                    <input type="text" class="theme-input-style" name="paystack_public_key"
                        placeholder="Enter Paystack Public Key"
                        value="{{ \Plugin\Saas\Repositories\PaymentMethodRepository::configKeyValue($method->id, 'paystack_public_key') }}"
                        required />
                </div>
            </div>
            <div class="form-group mb-20">
                <label class="black bold mb-2">{{ translate('Paystack Secret Key') }}</label>
                <div class="input-option">
                    <input type="text" class="theme-input-style" name="paystack_secret_key"
                        placeholder="Enter Paystack Secret Key"
                        value="{{ \Plugin\Saas\Repositories\PaymentMethodRepository::configKeyValue($method->id, 'paystack_secret_key') }}"
                        required />
                </div>
            </div>

            <div class="form-group mb-20">
                <label class="black bold mb-2">{{ translate('Instruction') }}</label>
                <div class="input-option">
                    <textarea name="paystack_instruction" class="theme-input-style">{{ \Plugin\Saas\Repositories\PaymentMethodRepository::configKeyValue($method->id, 'paystack_instruction') }}</textarea>
                </div>
            </div>
            <div>
                <button class="btn long payment-credental-update-btn"
                    data-payment-btn="{{ $method->id }}">{{ translate('Save Changes') }}</button>
            </div>
        </form>
    </div>
    <div class="instruction">
        <a href="https://paystack.com/" target="_blank">{{ translate('Paystack') }}</a>
        <p>
            {{ translate('Customer can buy product and pay directly using
                                                            Paystack') }}
        </p>
        <p class="semi-bold">
            {{ translate('Configuration instruction for Paystack') }}
        </p>
        <p>{{ translate('To use Paystack, you need:') }}</p>
        <ol>
            <li style="list-style-type: decimal">
                {{ translate('Register with Paystack') }}
            </li>
            <li style="list-style-type: decimal">
                <p>
                    {{ translate('After registration at Paystack, you will have
                                                                                                    Public Key, Secret Key') }}
                </p>
            </li>
            <li style="list-style-type: decimal">
                <p>
                    {{ translate('Enter Public Key, Secret Key into the box in left
                                                                                                    hand') }}
                </p>
            </li>
        </ol>
    </div>
</div>
