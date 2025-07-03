@php
$selecected_currency = \Plugin\Saas\Repositories\PaymentMethodRepository::configKeyValue(
$method->id,
'bkash_currency',
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
                    'input' => 'bkash_logo',
                    'data' => \Plugin\Saas\Repositories\PaymentMethodRepository::configKeyValue(
                    $method->id,
                    'bkash_logo'),
                    ])
                    @if ($errors->has('bkash_logo'))
                    <div class="invalid-input">{{ $errors->first('bkash_logo') }}
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
                    <select name="bkash_currency" class="theme-input-style selectCurrency">
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
                <label class="black bold mb-2">{{ translate('App Key') }}</label>
                <div class="input-option">
                    <input type="text" class="theme-input-style" name="bkash_app_key"
                        placeholder="Enter bKash App Key"
                        value="{{ \Plugin\Saas\Repositories\PaymentMethodRepository::configKeyValue($method->id, 'bkash_app_key') }}"
                        required />
                    @if ($errors->has('bkash_app_key'))
                    <div class="invalid-input">{{ $errors->first('bkash_app_key') }}
                    </div>
                    @endif
                </div>
            </div>

            <div class="form-group mb-20">
                <label class="black bold mb-2">{{ translate('App Secret') }}</label>
                <div class="input-option">
                    <input type="text" class="theme-input-style" name="bkash_app_secret"
                        placeholder="Enter bKash App Secret"
                        value="{{ \Plugin\Saas\Repositories\PaymentMethodRepository::configKeyValue($method->id, 'bkash_app_secret') }}"
                        required />
                    @if ($errors->has('bkash_app_secret'))
                    <div class="invalid-input">{{ $errors->first('bkash_app_secret') }}
                    </div>
                    @endif
                </div>
            </div>

            <div class="form-group mb-20">
                <label class="black bold mb-2">{{ translate('Username') }}</label>
                <div class="input-option">
                    <input type="text" class="theme-input-style" name="bkash_username"
                        placeholder="Enter bKash Username"
                        value="{{ \Plugin\Saas\Repositories\PaymentMethodRepository::configKeyValue($method->id, 'bkash_username') }}"
                        required />
                    @if ($errors->has('bkash_username'))
                    <div class="invalid-input">{{ $errors->first('bkash_username') }}
                    </div>
                    @endif
                </div>
            </div>

            <div class="form-group mb-20">
                <label class="black bold mb-2">{{ translate('Password') }}</label>
                <div class="input-option">
                    <input type="password" class="theme-input-style" name="bkash_password"
                        placeholder="Enter bKash Password"
                        value="{{ \Plugin\Saas\Repositories\PaymentMethodRepository::configKeyValue($method->id, 'bkash_password') }}"
                        required />
                    @if ($errors->has('bkash_password'))
                    <div class="invalid-input">{{ $errors->first('bkash_password') }}
                    </div>
                    @endif
                </div>
            </div>

            <div class="form-group mb-20">
                <label class="black bold mb-2">{{ translate('Instruction') }}</label>
                <div class="input-option">
                    <textarea name="bkash_instruction" id="instruction" class="theme-input-style">{{ \Plugin\Saas\Repositories\PaymentMethodRepository::configKeyValue($method->id, 'bkash_instruction') }}</textarea>
                    @if ($errors->has('bkash_instruction'))
                    <div class="invalid-input">{{ $errors->first('bkash_instruction') }}
                    </div>
                    @endif
                </div>
            </div>

            <button type="submit" class="btn long" id="save-btn">
                {{ translate('Save') }}
            </button>
        </form>
    </div>
</div>