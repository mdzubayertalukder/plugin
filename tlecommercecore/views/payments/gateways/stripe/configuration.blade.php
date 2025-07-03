@php
    $currencies = getAllCurrencies();
    $selecected_currency = \Plugin\TlcommerceCore\Repositories\PaymentMethodRepository::configKeyValue(
        $method->id,
        'stripe_currency',
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
                        'input' => 'stripe_logo',
                        'data' => \Plugin\TlcommerceCore\Repositories\PaymentMethodRepository::configKeyValue(
                            $method->id,
                            'stripe_logo'),
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
                    <select name="stripe_currency" class="theme-input-style selectCurrency">
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
                <label class="black bold mb-2">{{ translate('Stripe Public Key') }}</label>
                <div class="input-option">
                    <input type="text" class="theme-input-style" name="stripe_public_key"
                        placeholder="pk_*************"
                        value="{{ \Plugin\TlcommerceCore\Repositories\PaymentMethodRepository::configKeyValue($method->id, 'stripe_public_key') }}" />
                </div>
            </div>
            <div class="form-group mb-20">
                <label class="black bold mb-2">{{ translate('Stripe Secret Key') }}</label>
                <div class="input-option">
                    <input type="text" class="theme-input-style" name="stripe_secret_key"
                        placeholder="sk_*************"
                        value="{{ \Plugin\TlcommerceCore\Repositories\PaymentMethodRepository::configKeyValue($method->id, 'stripe_secret_key') }}" />
                </div>
            </div>
            <div class="form-group mb-20">
                <label class="black bold mb-2">{{ translate('Instruction') }}</label>
                <div class="input-option">
                    <textarea name="stripe_instruction" id="instruction" class="theme-input-style">{{ \Plugin\TlcommerceCore\Repositories\PaymentMethodRepository::configKeyValue($method->id, 'stripe_instruction') }}</textarea>
                </div>
            </div>
            <div>
                <button class="btn long payment-credental-update-btn"
                    data-payment-btn="{{ $method->id }}">{{ translate('Save Changes') }}</button>
            </div>
        </form>
    </div>
    <div class="instruction">
        <a href="https://stripe.com" target="_blank">Stripe</a>
        <p>
            Customer can buy product and pay directly using
            Visa, Credit card via Stripe
        </p>
        <p class="semi-bold">
            Configuration instruction for Stripe
        </p>
        <p>To use Stripe, you need:</p>
        <ol>
            <li style="list-style-type: decimal">
                <a href="https://dashboard.stripe.com/register" target="_blank">
                    Register with Stripe
                </a>
            </li>
            <li style="list-style-type: decimal">
                <p>
                    After registration at Stripe, you will have
                    Public, Secret keys
                </p>
            </li>
            <li style="list-style-type: decimal">
                <p>
                    Enter Public, Secret keys into the box in right
                    hand
                </p>
            </li>
            <li style="list-style-type: decimal">
                <p>
                    See stripe supported currency list, <a href="https://stripe.com/docs/currencies">here</a>
                </p>
            </li>
        </ol>
    </div>
</div>
