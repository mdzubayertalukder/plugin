@php
    $selecected_currency = \Plugin\Saas\Repositories\PaymentMethodRepository::configKeyValue(
        $method->id,
        'paddle_currency',
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
                        'input' => 'paddle_logo',
                        'data' => \Plugin\Saas\Repositories\PaymentMethodRepository::configKeyValue(
                            $method->id,
                            'paddle_logo'),
                    ])
                    @if ($errors->has('paddle_logo'))
                        <div class="invalid-input">{{ $errors->first('paddle_logo') }}
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
                    <select name="paddle_currency" class="theme-input-style selectCurrency">
                        @foreach ($currencies as $currency)
                            <option value="{{ $currency->code }}" class="text-uppercase"
                                {{ $currency->code == $default_currency ? 'selected' : '' }}>
                                {{ $currency->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            @if (isActivePluging('paddle-recurring'))
                <div class="form-group mb-20">
                    <div class="d-flex">
                        <label class="black bold">{{ translate('Paddle recurring Payment') }}</label>
                        <label class="switch glow primary medium ml-2">
                            <input type="checkbox" name="paddle_recurring_payment" class="paddle-recurring-switcher"
                                @if (
                                    \Plugin\Saas\Repositories\PaymentMethodRepository::configKeyValue($method->id, 'paddle_recurring_payment') ==
                                        config('settings.general_status.active')) checked @endif />
                            <span class="control"></span>
                        </label>

                    </div>
                </div>
            @endif

            <!--Paddlre Credentails-->
            <div
                class="w-100 paddle-credentails {{ isActivePluging('paddle-recurring') &&
                \Plugin\Saas\Repositories\PaymentMethodRepository::configKeyValue($method->id, 'paddle_recurring_payment') ==
                    config('settings.general_status.active')
                    ? 'd-none'
                    : '' }}">
                <div class="form-group mb-20">
                    <label class="black bold mb-2">{{ translate('Paddle Vendor ID') }}</label>
                    <div class="input-option">
                        <input type="text" class="theme-input-style" name="paddle_vendor_id"
                            placeholder="Enter Paddle Vendor ID"
                            value="{{ \Plugin\Saas\Repositories\PaymentMethodRepository::configKeyValue($method->id, 'paddle_vendor_id') }}" />
                    </div>
                </div>
                <div class="form-group mb-20">
                    <label class="black bold mb-2">{{ translate('Paddle Public Key') }}</label>
                    <div class="input-option">
                        <input type="text" class="theme-input-style" name="paddle_public_key"
                            placeholder="Enter Paddle Public Key"
                            value="{{ \Plugin\Saas\Repositories\PaymentMethodRepository::configKeyValue($method->id, 'paddle_public_key') }}" />
                    </div>
                </div>
                <div class="form-group mb-20">
                    <label class="black bold mb-2">{{ translate('Vendor Auth Code') }}</label>
                    <div class="input-option">
                        <input type="text" class="theme-input-style" name="paddle_vendor_auth_code"
                            placeholder="Enter Vendor Auth Code"
                            value="{{ \Plugin\Saas\Repositories\PaymentMethodRepository::configKeyValue($method->id, 'paddle_vendor_auth_code') }}" />
                    </div>
                </div>
            </div>
            <!--End paddle credentails-->

            <!--Paddle recurring payment-->
            @if (isActivePluging('paddle-recurring'))
                <div
                    class="w-100 paddle-recurring-credentails {{ \Plugin\Saas\Repositories\PaymentMethodRepository::configKeyValue($method->id, 'paddle_recurring_payment') ==
                    config('settings.general_status.active')
                        ? ''
                        : 'd-none' }}">
                    <div class="form-group mb-20">
                        <label class="black bold mb-2">{{ translate('Paddle Api Key') }}</label>
                        <div class="input-option">
                            <input type="text" class="theme-input-style" name="paddle_api_key"
                                placeholder="Enter Paddle Api Key"
                                value="{{ \Plugin\Saas\Repositories\PaymentMethodRepository::configKeyValue($method->id, 'paddle_api_key') }}" />
                        </div>
                    </div>
                    <div class="form-group mb-20">
                        <label class="black bold mb-2">{{ translate('Client Side Key') }}</label>
                        <div class="input-option">
                            <input type="text" class="theme-input-style" name="paddle_client_side_key"
                                placeholder="Enter Paddle Client Side Key"
                                value="{{ \Plugin\Saas\Repositories\PaymentMethodRepository::configKeyValue($method->id, 'paddle_client_side_key') }}" />
                        </div>
                    </div>
                    <div class="form-group mb-20">
                        <label class="black bold mb-2">{{ translate('Product ID') }}</label>
                        <div class="input-option">
                            <input type="text" class="theme-input-style" name="paddle_product_id"
                                placeholder="Enter Product ID"
                                value="{{ \Plugin\Saas\Repositories\PaymentMethodRepository::configKeyValue($method->id, 'paddle_product_id') }}" />
                        </div>
                    </div>
                </div>
            @endif
            <!--End Paddle recurring payment-->


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
                    <textarea name="paddle_instruction" class="theme-input-style">{{ \Plugin\Saas\Repositories\PaymentMethodRepository::configKeyValue($method->id, 'paddle_instruction') }}</textarea>
                </div>
            </div>
            <div>
                <button class="btn long payment-credental-update-btn"
                    data-payment-btn="{{ $method->id }}">{{ translate('Save Changes') }}</button>
            </div>
        </form>
    </div>
    <div class="instruction">
        <a href="https://www.paddle.com/" target="_blank">{{ translate('Paddle') }}</a>
        <p>
            {{ translate('Customer can buy product and pay directly using
                                                                                                                                                                                                                                                              Visa, Credit card via Paddle') }}
        </p>
        <p class="semi-bold">
            {{ translate('Configuration instruction for Paddle') }}
        </p>
        <p>{{ translate('To use Paddle, you need:') }}</p>
        <ol>
            <li style="list-style-type: decimal">
                {{ translate('Register with Paddle') }}
            </li>
            <li style="list-style-type: decimal">
                <p>
                    {{ translate('After registration at Paddle, you will have
                                                                                                                                                                                                                                                                                                                                                                                                                                      Vendor ID, Public Key, Auth Code') }}
                </p>
            </li>
            <li style="list-style-type: decimal">
                <p>
                    {{ translate('Enter Vendor ID, Public Key, Auth Code into the box in left
                                                                                                                                                                                                                                                                                                                                                                                                                                      hand') }}
                </p>
            </li>
        </ol>
    </div>
</div>
