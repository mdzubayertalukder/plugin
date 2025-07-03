<div class="card">
    <div class="card-body">
        <div class="form-row mb-20">
            <div class="col-sm-4">
                <label class="font-14 bold black ">{{ translate('Enable Tax') }}
                </label>
            </div>
            <div class="col-sm-1">
                <label class="switch glow primary medium">
                    <input type="checkbox" name="enable_tax_in_checkout" @checked(getEcommerceSetting('enable_tax_in_checkout') == config('settings.general_status.active'))>
                    <span class="control"></span>
                </label>
            </div>
            <div class="col-sm-7">
                <p class="mt-0 font-13">
                    {{ translate('You can manage tax profile from') }}
                    <a href="{{ route('plugin.tlcommercecore.ecommerce.settings.taxes.list') }}"
                        class="btn-link">{{ translate('Tax') }}
                    </a>
                </p>
            </div>
        </div>

    </div>
</div>
