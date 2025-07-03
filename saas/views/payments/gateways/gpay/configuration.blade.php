@php
    $selecected_currency = \Plugin\Saas\Repositories\PaymentMethodRepository::configKeyValue(
        $method->id,
        'gpay_currency',
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
                        'input' => 'gpay_logo',
                        'data' => \Plugin\Saas\Repositories\PaymentMethodRepository::configKeyValue(
                            $method->id,
                            'gpay_logo'),
                    ])
                    @if ($errors->has('gpay_logo'))
                        <div class="invalid-input">{{ $errors->first('gpay_logo') }}
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
                    <select name="gpay_currency" class="theme-input-style selectCurrency">
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
                <label class="black bold mb-2">{{ translate('Merchant Name') }}</label>
                <div class="input-option">
                    <input type="text" class="theme-input-style" name="gpay_marchant_name"
                        placeholder="Enter Marchant Name"
                        value="{{ \Plugin\Saas\Repositories\PaymentMethodRepository::configKeyValue($method->id, 'gpay_marchant_name') }}"
                        required />
                </div>
            </div>
            <div class="form-group mb-20">
                <label class="black bold mb-2">{{ translate('Merchant Id') }}</label>
                <div class="input-option">
                    <input type="text" class="theme-input-style" name="gpay_marchant_id"
                        placeholder="Enter Marchant Id"
                        value="{{ \Plugin\Saas\Repositories\PaymentMethodRepository::configKeyValue($method->id, 'gpay_marchant_id') }}"
                        required />
                </div>
            </div>

            <div class="form-group mb-20">
                <label class="black bold mb-2">{{ translate('Instruction') }}</label>
                <div class="input-option">
                    <textarea name="gpay_instruction" class="theme-input-style">{{ \Plugin\Saas\Repositories\PaymentMethodRepository::configKeyValue($method->id, 'gpay_instruction') }}</textarea>
                </div>
            </div>
            <div>
                <button class="btn long payment-credental-update-btn"
                    data-payment-btn="{{ $method->id }}">{{ translate('Save Changes') }}</button>
            </div>
        </form>
    </div>
    <div class="instruction">
        <a href="https://gpay.com/" target="_blank">Gpay</a>
        <p>
            {{ translate('Customer can buy product and pay directly using
                                                            Gpay') }}
        </p>
        <p class="semi-bold">
            {{ translate('Configuration instruction for Gpay') }}
        </p>
        <p>{{ translate('To use Gpay, you need:') }}</p>
        <ol>
            <li style="list-style-type: decimal">
                <a href="https://pay.google.com/business/console">{{ translate('Register with gpay') }}</a>
            </li>
            <li style="list-style-type: decimal">
                <p>
                    {{ translate('After registration at gpay, you will have
                                                                                                    Merchant Id, Merchant Name') }}
                </p>
            </li>
            <li style="list-style-type: decimal">
                <p>
                    {{ translate('Enter Merchant Id, Merchant Name into the box in left
                                                                                                    hand') }}
                </p>
            </li>
            <li style="list-style-type: decimal">
                <p>
                    {{ translate('See gpay supported currency list,') }} <a
                        href="https://support.google.com/merchants/answer/160637?hl=en#zippy=%2Ctarget-country-currency-local-language">here</a>
                </p>
            </li>
        </ol>
    </div>
</div>
