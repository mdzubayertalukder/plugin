<form id="edit-shipping-rate-form">
    <input type="hidden" value="{{ $shippingRate->zone_id }}" name="zone_id">
    <input type="hidden" value="{{ $shippingRate->id }}" name="rate_id">
    @if (isActivePluging('carrier'))
        <!--Shipping rate type-->
        <div class="form-row mb-20">
            <div class="d-flex w-100">
                <div class="align-items-center d-flex mr-30">
                    <div class="custom-radio mr-1">
                        <input type="radio" name="edit_rate_type" id="edit_own_rate"
                            value="{{ config('tlecommercecore.shipping_rate_type.own_rate') }}"
                            @if ($shippingRate->rate_type == config('tlecommercecore.shipping_rate_type.own_rate')) checked @endif class="edit-rate-type-selector">
                        <label for="edit_own_rate"></label>
                    </div>
                    <label for="edit_own_rate" class="black font-14 mb-0">{{ translate('Own Rate') }}</label>
                </div>

                <div class="align-items-center d-flex">
                    <div class="custom-radio mr-1">
                        <input type="radio" name="edit_rate_type" id="edit_courier_rate"
                            value="{{ config('tlecommercecore.shipping_rate_type.carrier_rate') }}"
                            @if ($shippingRate->rate_type == config('tlecommercecore.shipping_rate_type.carrier_rate')) checked @endif class="edit-rate-type-selector">
                        <label for="edit_courier_rate"></label>
                    </div>
                    <label for="edit_courier_rate" class="black font-14 mb-0">{{ translate('Carrier Rate') }}</label>
                </div>
            </div>
        </div>
    @endif
    <!--End Shipping rate type-->
    <!--Shipping time-->
    <div class="form-row mb-20">
        <div class="col-sm-12">
            <label class="font-14 bold black">{{ translate('Shipping Time') }}</label>
            @if (count($shipping_time) > 0)
                <select class="theme-input-style time-select" name="shipping_time">
                    @foreach ($shipping_time as $time)
                        <option data-min="{{ $time->min_value }}" data-min-unit="{{ $time->min_unit }}"
                            data-max="{{ $time->max_value }}" data-max-unit="{{ $time->max_unit }}"
                            value="{{ $time->id }}" @if ($shippingRate->delivery_time == $time->id) selected @endif>
                            {{ $time->min_value }}
                        </option>
                    @endforeach
                </select>
            @else
                <p class="alert alert-danger">{{ translate('No shipping time found') }}</p>
                <a href="{{ route('plugin.tlcommercecore.shipping.configuration') }}#ShippingTimes"
                    class="btn-link">{{ translate('Add new shipping time') }}</a>
            @endif
        </div>
    </div>

    <!--End Shipping Time-->

    <div
        class="carrier-shipping-rate {{ $shippingRate->rate_type == config('tlecommercecore.shipping_rate_type.carrier_rate') ? '' : 'd-none' }}">
        <!--Carrier Selector-->
        <div class="form-row mb-20 courier-selector">
            <div class="col-sm-12">
                <label class="font-14 bold black">{{ translate('Carrier') }} </label>
                @if (count($couriers) > 0)
                    <select name="courier" class="theme-input-style">
                        <option>{{ translate('Select a carrier') }}</option>
                        @foreach ($couriers as $courier)
                            <option value="{{ $courier['id'] }}" @if ($shippingRate->carrier_id == $courier['id']) selected @endif>
                                {{ $courier['name'] }}</option>
                        @endforeach
                    </select>
                @else
                    <p class="alert alert-danger">{{ translate('No carrier found') }}</p>
                    <a href="{{ route('plugin.tlcommercecore.shipping.configuration') }}#ShippingCarriers"
                        class="btn-link">{{ translate('Add new carrier') }}</a>
                @endif
            </div>
        </div>
        <!--End Carrier Selector-->
        <!--Shipped By-->
        <div class="form-row mb-20 courier-selector">
            <label class="font-14 bold black">{{ translate('Shipped By') }} </label>
            <select name="shipped_by" class="theme-input-style">
                <option>{{ translate('Select a medium') }}</option>
                <option value="{{ config('tlecommercecore.shipped_by.by_air') }}"
                    @if ($shippingRate->shipping_medium == config('tlecommercecore.shipped_by.by_air')) selected @endif>
                    {{ translate('Air Freight') }}</option>
                <option value="{{ config('tlecommercecore.shipped_by.by_ship') }}"
                    @if ($shippingRate->shipping_medium == config('tlecommercecore.shipped_by.by_ship')) selected @endif>
                    {{ translate('Ocean Freight') }}</option>
                <option value="{{ config('tlecommercecore.shipped_by.by_rail') }}"
                    @if ($shippingRate->shipping_medium == config('tlecommercecore.shipped_by.by_rail')) selected @endif>
                    {{ translate('Rail Freight') }}</option>
                <option value="{{ config('tlecommercecore.shipped_by.by_train') }}"
                    @if ($shippingRate->shipping_medium == config('tlecommercecore.shipped_by.by_train')) selected @endif>
                    {{ translate('Road Freight') }}</option>
            </select>
        </div>
        <!--End Shipped By-->
        <!--Carrier Shipping Condition-->
        <div class="form-row mb-20">
            <table class="table table-responsive">
                <thead>
                    <th>
                        <label class="font-14 bold black">{{ translate('Minimum Volumetric Weight') }}</label>
                    </th>
                    <th>
                        <label class="font-14 bold black">{{ translate('Maximum Volumetric Weight') }}</label>
                    </th>
                    <th>
                        <label class="font-14 bold black">{{ translate('Shipping Cost') }}</label>
                    </th>
                    <th>
                        <label class="font-14 bold black">{{ translate('Action') }}</label>
                    </th>
                </thead>
                <tbody id="carrier-shipping-weight-range">
                    <tr>
                        <td>
                            <div class="input-group addon">
                                <input type="text" name="carrier_condition[0][min_weight]"
                                    class="form-control style--two" placeholder="0.00"
                                    value="{{ $shippingRate->min_limit }}">
                                <div class="input-group-append">
                                    <div class="input-group-text px-3 bold">{{ translate('Kg') }}
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="input-group addon">
                                <input type="text" name="carrier_condition[0][max_weight]"
                                    class="form-control style--two" placeholder="0.00"
                                    value="{{ $shippingRate->max_limit }}">
                                <div class="input-group-append">
                                    <div class="input-group-text px-3 bold">{{ translate('Kg') }}
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="input-group addon">
                                <div class="input-group-prepend">
                                    <div class="input-group-text px-3 bold">{{ currencySymbol() }}</div>
                                </div>
                                <input type="text" name="carrier_condition[0][cost]" class="form-control"
                                    placeholder="0.00" value="{{ $shippingRate->shipping_cost }}">

                            </div>
                        </td>
                        <td>
                            <a href="#" class="area-disabled delete-weight-range text-danger">
                                <i class="icofont-ui-delete"></i>
                            </a>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <!--End carrier shipping conditon-->
    </div>

    <div
        class="own-shipping-rate {{ $shippingRate->rate_type == config('tlecommercecore.shipping_rate_type.own_rate') ? '' : 'd-none' }}">
        <!--Rate Name-->
        <div class="form-row mb-20">
            <div class="col-sm-12">
                <label class="font-14 bold black">{{ translate('Rate Name') }} </label>
            </div>
            <div class="col-sm-12">
                <input type="hidden" id="is-active-condition-edit" name="is_active_condition"
                    value="{{ $shippingRate->has_condition }}">
                <input type="text" name="rate_name" class="theme-input-style" value="{{ $shippingRate->name }}"
                    placeholder="{{ translate('Type Name') }}">
            </div>
        </div>
        <!--End Rate Name-->
        <!--Shipping Cost-->
        <div class="form-row mb-20">
            <div class="col-sm-12">
                <label class="font-14 bold black">{{ translate('Shipping Cost') }}</label>
                <input type="text" name="shipping_cost" class="theme-input-style"
                    value="{{ $shippingRate->shipping_cost }}" placeholder="0.00">
            </div>
        </div>
        <!--End Shipping Cost-->
        <!--Own rate condition add button-->
        <div class="form-row mb-20">
            <a href="#"
                class="btn-link new-condition-btn black {{ $shippingRate->has_condition == config('settings.general_status.active') ? 'd-none' : '' }}">
                {{ translate('Add New Condition') }}
            </a>
        </div>
        <!--End Custom Shipping condition add button-->
        <!--Own rate conditions-->
        <div
            class="conditions mb-20 {{ $shippingRate->has_condition == config('settings.general_status.active') ? '' : 'd-none' }}">
            <div class="form-row">
                <div class="d-flex d-sm-inline-flex align-items-center mr-sm-2 mb-3">
                    <div class="custom-radio mr-1">
                        <input type="radio" id="weight_based_edit" value="weight_based" name="conditionTypeEdit"
                            @if ($shippingRate->based_on == config('tlecommercecore.shipping_based_on.weight_based')) checked @endif class="edit-rate-condition-switcher">
                        <label for="weight_based_edit"></label>
                    </div>
                    <label for="weight_based_edit"
                        class="mt-1 font-14 bold black">{{ translate('Based on item weight') }}</label>
                </div>
                <div class="d-flex d-sm-inline-flex align-items-center mr-sm-2 mb-3">
                    <div class="custom-radio mr-1">
                        <input type="radio" id="price_based_edit" value="price_based" name="conditionTypeEdit"
                            @if ($shippingRate->based_on == config('tlecommercecore.shipping_based_on.price_based')) checked @endif class="edit-rate-condition-switcher">
                        <label for="price_based_edit"></label>
                    </div>
                    <label for="price_based_edit"
                        class="mt-1 font-14 bold black">{{ translate('Based on order price') }}</label>
                </div>
            </div>
            <div
                class="form-row weight-based {{ $shippingRate->based_on == config('tlecommercecore.shipping_based_on.weight_based') ? '' : 'd-none' }}">
                <div class="col-sm-6">
                    <label class="font-14 bold black">{{ translate('Minimum Weight') }}</label>
                    <div class="input-group addon">
                        <input type="text" name="min_weight" class="form-control style--two"
                            value="{{ $shippingRate->min_limit }}" placeholder="0.00">
                        <div class="input-group-append">
                            <div class="input-group-text px-3 bold">gm</div>
                        </div>
                        @if ($errors->has('min_weight'))
                            <div class="invalid-input">{{ $errors->first('min_weight') }}</div>
                        @endif
                    </div>

                </div>
                <div class="col-sm-6">
                    <label class="font-14 bold black">{{ translate('Maximum Weight') }}</label>
                    <div class="input-group addon">
                        <input type="text" name="max_weight" class="form-control style--two"
                            value="{{ $shippingRate->max_limit }}" placeholder="0.00">
                        <div class="input-group-append">
                            <div class="input-group-text px-3 bold">gm</div>
                        </div>
                        @if ($errors->has('max_weight'))
                            <div class="invalid-input">{{ $errors->first('max_weight') }}</div>
                        @endif

                    </div>
                </div>
            </div>
            <div
                class="form-row price-based {{ $shippingRate->based_on == config('tlecommercecore.shipping_based_on.price_based') ? '' : 'd-none' }}">
                <div class="col-sm-6">
                    <label class="font-14 bold black">{{ translate('Minimum Price') }}</label>
                    <div class="input-group addon">
                        <input type="text" name="min_price" class="form-control style--two"
                            value="{{ $shippingRate->min_limit }}" placeholder="0.00">
                        <div class="input-group-append">
                            <div class="input-group-text px-3 bold">{{ currencySymbol() }}</div>
                        </div>
                        @if ($errors->has('min_price'))
                            <div class="invalid-input">{{ $errors->first('min_price') }}</div>
                        @endif
                    </div>
                </div>
                <div class="col-sm-6">
                    <label class="font-14 bold black">{{ translate('Maximum Price') }}</label>
                    <div class="input-group addon">
                        <input type="text" name="max_price" class="form-control style--two"
                            value="{{ $shippingRate->max_limit }}" placeholder="0.00">
                        <div class="input-group-append">
                            <div class="input-group-text px-3 bold">{{ currencySymbol() }}</div>
                        </div>
                        @if ($errors->has('max_price'))
                            <div class="invalid-input">{{ $errors->first('max_price') }}</div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        <!--End Own rate condition-->
        <!--Own rate condition remove button-->
        <div class="form-row mb-20">
            <a href="#"
                class="btn-link remove-condition-btn text-danger {{ $shippingRate->has_condition == config('settings.general_status.active') ? '' : 'd-none' }}">
                {{ translate('Remove Condition') }}
            </a>
        </div>
        <!--End  Own rate condition remove button-->
    </div>
    <div class="form-row">
        <div class="col-12 text-right">
            <button type="submit" class="btn long update-rate-btn">{{ translate('Save Changes') }}</button>
        </div>
    </div>
</form>
<script src="{{ asset('/public/backend/assets/plugins/select2/select2.min.js') }}"></script>
<script type="text/javascript">
    (function($) {
        "use strict";
        /**
         * Select shipping time
         * */
        $('.time-select').select2({
            theme: "classic",
            templateResult: formatTime,
            templateSelection: formatTime,
            placeholder: '{{ translate('Select Shipping Time') }}',
        });
        /**
         * Generate select shipping time options
         **/
        function formatTime(opt) {
            if (!opt.id) {
                return opt.text;
            }
            var min = $(opt.element).attr('data-min');
            var min_unit = $(opt.element).attr('data-min-unit');
            var max = $(opt.element).attr('data-max');
            var max_unit = $(opt.element).attr('data-max-unit');
            var opt = min + ' ' + min_unit + ' - ' + max + ' ' + max_unit;
            return opt;
        };
        /**
         * 
         * New Condition
         * */
        $('.new-condition-btn').on('click', function(e) {
            $("#is-active-condition-edit").val(1);
            $('.conditions').removeClass('d-none');
            $('.new-condition-btn').addClass('d-none');
            $('.remove-condition-btn').removeClass('d-none');
        });
        /**
         * 
         * remove Condition 
         * */
        $('.remove-condition-btn').on('click', function(e) {
            $("#is-active-condition-edit").val(0);
            $('.conditions').addClass('d-none');
            $('.new-condition-btn').removeClass('d-none');
            $('.remove-condition-btn').addClass('d-none');
        });
        /**
         * Update shipping rate
         * 
         **/
        $('.update-rate-btn').on('click', function(e) {
            e.preventDefault();
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                },
                type: "POST",
                data: $('#edit-shipping-rate-form').serialize(),
                url: '{{ route('plugin.tlcommercecore.shipping.rate.update') }}',
                success: function(response) {
                    location.reload();
                },
                error: function(response) {
                    $.each(response.responseJSON.errors, function(field_name, error) {
                        $(document).find('[name=' + field_name + ']').after(
                            '<div class="invalid-input">' + error + '</div>')
                    })
                }
            });
        });
        /**
         * Choose shipping rate type
         *  
         **/
        $('.edit-rate-type-selector').on('change', function(e) {
            let selected_rate_type = $('input[name="edit_rate_type"]:checked').val();
            if (selected_rate_type === 'carrier_rate') {
                $('.carrier-shipping-rate').removeClass('d-none')
                $('.own-shipping-rate').addClass('d-none')
            } else {
                $('.carrier-shipping-rate').addClass('d-none')
                $('.own-shipping-rate').removeClass('d-none')
            }
        })
        /**
         * 
         * Condition Type
         */
        $('.edit-rate-condition-switcher').on('change', function(e) {
            let condition_type = $('input[name="conditionTypeEdit"]:checked').val();
            if (condition_type === 'weight_based') {
                $('.weight-based').removeClass('d-none')
                $('.price-based').addClass('d-none')
            } else {
                $('.weight-based').addClass('d-none')
                $('.price-based').removeClass('d-none')
            }
        })
    })(jQuery);
</script>
