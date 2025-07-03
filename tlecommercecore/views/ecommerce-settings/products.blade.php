<div class="card">
    <div class="card-body">
        <div class="form-row mb-20">
            <div class="col-sm-6">
                <label class="font-14 bold black ">{{ translate('Enable product reviews') }}
                </label>
            </div>
            <div class="col-sm-6">
                <label class="switch glow primary medium">
                    <input type="checkbox" name="enable_product_reviews" class="enable-product-review"
                        @if (getEcommerceSetting('enable_product_reviews') == config('settings.general_status.active')) checked @endif>
                    <span class="control"></span>
                </label>
            </div>
        </div>
        <div
            class="product-review-setting-group {{ getEcommerceSetting('enable_product_reviews') == config('settings.general_status.active') ? '' : 'd-none' }}">
            <div class="form-row mb-20">
                <div class="col-sm-6">
                    <label class="font-14 bold black ">{{ translate('Enable star rating on product reviews') }}
                    </label>
                </div>
                <div class="col-sm-6">
                    <label class="switch glow primary medium">
                        <input type="checkbox" name="enable_product_star_rating"
                            @if (getEcommerceSetting('enable_product_star_rating') == config('settings.general_status.active')) checked @endif>
                        <span class="control"></span>
                    </label>
                </div>
            </div>
            <div class="form-row mb-20">
                <div class="col-sm-6">
                    <label class="font-14 bold black ">{{ translate('Star rating should be required not optional') }}
                    </label>
                </div>
                <div class="col-sm-6">
                    <label class="switch glow primary medium">
                        <input type="checkbox" name="required_product_star_rating"
                            @if (getEcommerceSetting('required_product_star_rating') == config('settings.general_status.active')) checked @endif>
                        <span class="control"></span>
                    </label>
                </div>
            </div>
            <div class="form-row mb-20">
                <div class="col-sm-6">
                    <label
                        class="font-14 bold black ">{{ translate('Show Verified customer label on product reviews') }}
                    </label>
                </div>
                <div class="col-sm-6">
                    <label class="switch glow primary medium">
                        <input type="checkbox" name="verified_customer_on_product_review"
                            @if (getEcommerceSetting('verified_customer_on_product_review') == config('settings.general_status.active')) checked @endif>
                        <span class="control"></span>
                    </label>
                </div>
            </div>
            <div class="form-row mb-20">
                <div class="col-sm-6">
                    <label class="font-14 bold black ">{{ translate('Reviews can only be left by verified customer') }}
                    </label>
                </div>
                <div class="col-sm-6">
                    <label class="switch glow primary medium">
                        <input type="checkbox" name="only_varified_customer_left_review"
                            @if (getEcommerceSetting('only_varified_customer_left_review') == config('settings.general_status.active')) checked @endif>
                        <span class="control"></span>
                    </label>
                </div>
            </div>
        </div>
        <hr>
        <div class="form-row mb-20">
            <div class="col-sm-6">
                <label class="font-14 bold black ">{{ translate('Enable product compare') }}
                </label>
            </div>
            <div class="col-sm-6">
                <label class="switch glow primary medium">
                    <input type="checkbox" name="enable_product_compare"
                        @if (getEcommerceSetting('enable_product_compare') == config('settings.general_status.active')) checked @endif>
                    <span class="control"></span>
                </label>
            </div>
        </div>
        <div class="form-row mb-20">
            <div class="col-sm-6">
                <label class="font-14 bold black ">{{ translate('Enable Product Discount') }}
                </label>
            </div>
            <div class="col-sm-6">
                <label class="switch glow primary medium">
                    <input type="checkbox" name="enable_product_discount"
                        @if (getEcommerceSetting('enable_product_discount') == config('settings.general_status.active')) checked @endif>
                    <span class="control"></span>
                </label>
            </div>
        </div>
        <div class="form-row mb-20">
            <div class="col-sm-6">
                <label class="font-14 bold black ">{{ translate('Display product perpage') }}
                </label>
            </div>
            <div class="col-sm-4">
                <input type="text" name="product_per_page" class="theme-input-style"
                    value="{{ getEcommerceSetting('product_per_page') }}">
            </div>
        </div>
    </div>
</div>
