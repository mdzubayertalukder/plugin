<div class="card">
    <div class="card-body">
        <div class="form-row mb-20">
            <label class="font-14 bold black col-sm-4">{{ translate('Order code prefix') }}
            </label>
            <div class="col-sm-4">
                <input type="text" name="order_code_prefix" value="{{ getEcommerceSetting('order_code_prefix') }}"
                    class="theme-input-style" placeholder="{{ translate('Enter prefix') }}">
            </div>
        </div>
        <div class="form-row mb-20">
            <label class="font-14 bold black col-sm-4">{{ translate('Order code prefix seperator') }}
            </label>
            <div class="col-sm-4">
                <input type="text" name="order_code_prefix_seperator"
                    value="{{ getEcommerceSetting('order_code_prefix_seperator') }}" class="theme-input-style"
                    placeholder="{{ translate('Enter prefix seperator') }}">
            </div>
        </div>
        <div class="form-row mb-20">
            <div class="col-sm-4">
                <label class="font-14 bold black">{{ translate('Can cancel order within') }}
                </label>
            </div>
            <div class="col-sm-4">
                <div class="input-group addon">
                    <input type="text" name="cancel_order_time_limit"
                        value="{{ getEcommerceSetting('cancel_order_time_limit') }}" placeholder="0"
                        class="form-control style--two">
                    <div class="input-group-append">
                        <select class="form-control" name="cancel_order_time_limit_unit">
                            <option value="{{ config('tlecommercecore.time_unit.Days') }}"
                                @if (getEcommerceSetting('cancel_order_time_limit_unit') == config('tlecommercecore.time_unit.Days')) selected @endif>
                                {{ translate('Days') }}</option>
                            <option value="{{ config('tlecommercecore.time_unit.Hours') }}"
                                @if (getEcommerceSetting('cancel_order_time_limit_unit') == config('tlecommercecore.time_unit.Hours')) selected @endif>
                                {{ translate('Hours') }}</option>
                            <option value="{{ config('tlecommercecore.time_unit.Minutes') }}"
                                @if (getEcommerceSetting('cancel_order_time_limit_unit') == config('tlecommercecore.time_unit.Minutes')) selected @endif>
                                {{ translate('Minutes') }}</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>
        <div class="form-row mb-20">
            <div class="col-sm-4">
                <label class="font-14 bold black">{{ translate('Can return order within') }}
                </label>
            </div>
            <div class="col-sm-4">
                <div class="input-group addon">
                    <input type="text" name="return_order_time_limit"
                        value="{{ getEcommerceSetting('return_order_time_limit') }}" placeholder="0"
                        class="form-control style--two">
                    <div class="input-group-append">
                        <select class="form-control" name="return_order_time_limit_unit">
                            <option value="{{ config('tlecommercecore.time_unit.Days') }}"
                                @if (getEcommerceSetting('return_order_time_limit_unit') == config('tlecommercecore.time_unit.Days')) selected @endif>
                                {{ translate('Days') }}</option>
                            <option value="{{ config('tlecommercecore.time_unit.Hours') }}"
                                @if (getEcommerceSetting('return_order_time_limit_unit') == config('tlecommercecore.time_unit.Hours')) selected @endif>
                                {{ translate('Hours') }}</option>
                            <option value="{{ config('tlecommercecore.time_unit.Minutes') }}"
                                @if (getEcommerceSetting('return_order_time_limit_unit') == config('tlecommercecore.time_unit.Minutes')) selected @endif>
                                {{ translate('Minutes') }}</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
