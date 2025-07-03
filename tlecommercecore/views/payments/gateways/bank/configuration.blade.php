<div class="p-3 payment-method-item-body">
    <div>
        <form id="credential-form">
            <input type="hidden" name="payment_id" value="{{ $method->id }}">
            <div class="form-group mb-20">
                <label class="black bold mb-2">{{ translate('Logo') }}</label>
                <div class="input-option">
                    @include('core::base.includes.media.media_input', [
                        'input' => 'bank_logo',
                        'data' => \Plugin\TlcommerceCore\Repositories\PaymentMethodRepository::configKeyValue(
                            config('tlecommercecore.payment_methods.bank'),
                            'bank_logo'),
                    ])
                </div>
            </div>
            <div class="form-group mb-20">
                <div class="d-flex">
                    <div>
                        <label class="black bold">{{ translate('Enable Transaction Form in Checkout') }}</label>
                    </div>
                    <div>
                        <label class="switch glow primary medium ml-2">
                            <input type="checkbox" name="enable_bank_transaction_details_form"
                                @if (
                                    \Plugin\TlcommerceCore\Repositories\PaymentMethodRepository::configKeyValue(
                                        $method->id,
                                        'enable_bank_transaction_details_form') == config('settings.general_status.active')) checked @endif />
                            <span class="control"></span>
                        </label>
                    </div>
                </div>
            </div>

            <div class="form-group mb-20">
                <label class="black bold mb-2">{{ translate('Instruction') }}</label>
                <div class="input-option">
                    <textarea name="bank_instruction" id="instruction" class="theme-input-style">{{ \Plugin\TlcommerceCore\Repositories\PaymentMethodRepository::configKeyValue(config('tlecommercecore.payment_methods.bank'), 'bank_instruction') }}</textarea>
                </div>
            </div>
            <div>
                <button class="btn long payment-credental-update-btn"
                    data-payment-btn="{{ $method->id }}">{{ translate('Save Changes') }}</button>
            </div>
        </form>
    </div>
</div>
