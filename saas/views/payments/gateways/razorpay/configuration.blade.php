@php
    $selecected_currency = \Plugin\Saas\Repositories\PaymentMethodRepository::configKeyValue(
        $method->id,
        'razorpay_currency',
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
                        'input' => 'razorpay_logo',
                        'data' => \Plugin\Saas\Repositories\PaymentMethodRepository::configKeyValue(
                            $method->id,
                            'razorpay_logo'),
                    ])
                    @if ($errors->has('razorpay_logo'))
                        <div class="invalid-input">{{ $errors->first('razorpay_logo') }}
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
                    <select name="razorpay_currency" class="theme-input-style selectCurrency">
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
                <label class="black bold mb-2">{{ translate('Razorpay Key Id') }}</label>
                <div class="input-option">
                    <input type="text" class="theme-input-style" name="razorpay_key_id"
                        placeholder="Enter Razorpay Key Id"
                        value="{{ \Plugin\Saas\Repositories\PaymentMethodRepository::configKeyValue($method->id, 'razorpay_key_id') }}"
                        required />
                </div>
            </div>
            <div class="form-group mb-20">
                <label class="black bold mb-2">{{ translate('Razorpay Key Secret') }}</label>
                <div class="input-option">
                    <input type="text" class="theme-input-style" name="razorpay_key_secret"
                        placeholder="Enter Razorpay Key Secret"
                        value="{{ \Plugin\Saas\Repositories\PaymentMethodRepository::configKeyValue($method->id, 'razorpay_key_secret') }}"
                        required />
                </div>
            </div>
            <div class="form-group mb-20">
                <label class="black bold mb-2">{{ translate('Instruction') }}</label>
                <div class="input-option">
                    <textarea name="razorpay_instruction" class="theme-input-style">{{ \Plugin\Saas\Repositories\PaymentMethodRepository::configKeyValue($method->id, 'razorpay_instruction') }}</textarea>
                </div>
            </div>
            <div>
                <button class="btn long payment-credental-update-btn"
                    data-payment-btn="{{ $method->id }}">{{ translate('Save Changes') }}</button>
            </div>
        </form>
    </div>
    <div class="instruction">
        <a href="https://razorpay.com/" target="_blank">{{ translate('Razorpay') }}</a>
        <p>
            {{ translate('Customer can buy product and pay directly using
                                                                                                Razorpay') }}
        </p>
        <p class="semi-bold">
            {{ translate('Configuration instruction for Razorpay') }}
        </p>
        <p>{{ translate('To use Razorpay, you need:') }}</p>
        <ol>
            <li style="list-style-type: decimal">
                {{ translate('Register with Razorpay') }}
            </li>
            <li style="list-style-type: decimal">
                <p>
                    {{ translate('After registration at Razorpay, you will have
                                                                                                                                            Key Id, Key Secret') }}
                </p>
            </li>
            <li style="list-style-type: decimal">
                <p>
                    {{ translate('Enter Key Id, Key Secret into the box in left
                                                                                                                        hand') }}
                </p>
            </li>
        </ol>
    </div>
</div>
