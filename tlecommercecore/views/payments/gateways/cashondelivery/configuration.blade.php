<div class="p-3 payment-method-item-body">
    <div>
        <form id="credential-form">
            <input type="hidden" name="payment_id" value="{{ $method->id }}">
            <div class="form-group mb-20">
                <label class="black bold mb-2">{{ translate('Logo') }}</label>
                <div class="input-option">
                    @include('core::base.includes.media.media_input', [
                        'input' => 'cod_logo',
                        'data' => \Plugin\TlcommerceCore\Repositories\PaymentMethodRepository::configKeyValue(
                            config('tlecommercecore.payment_methods.cod'),
                            'cod_logo'),
                    ])
                </div>
            </div>
            <div class="form-group mb-20">
                <label class="black bold mb-2">{{ translate('Instruction') }}</label>
                <div class="input-option">
                    <textarea name="cod_instruction" id="instruction" class="theme-input-style">{{ \Plugin\TlcommerceCore\Repositories\PaymentMethodRepository::configKeyValue(config('tlecommercecore.payment_methods.cod'), 'cod_instruction') }}</textarea>
                </div>
            </div>
            <div>
                <button class="btn long payment-credental-update-btn"
                    data-payment-btn="{{ $method->id }}">{{ translate('Save Changes') }}</button>
            </div>
        </form>
    </div>
</div>
