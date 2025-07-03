@php
    $sellers = \Core\Models\User::select('id', 'name')
        ->where('user_type', 3)
        ->where('status', config('settings.general_status.active'))
        ->get();
    
    $selectedSellers = isset($widget_properties['sellers']) ? $widget_properties['sellers'] : [];
@endphp
<ul class="nav nav-tabs mb-20" id="myTab" role="tablist">
    <li class="nav-item">
        <a class="nav-link active" id="content-info-tab" data-toggle="tab" href="#content-info" role="tab"
            aria-controls="content-info" aria-selected="true">{{ translate('Content') }}</a>
    </li>
    <li class="nav-item">
        <a class="nav-link" id="background-tab" data-toggle="tab" href="#background" role="tab"
            aria-controls="background" aria-selected="false">{{ translate('Background') }}</a>
    </li>
    <li class="nav-item">
        <a class="nav-link" id="advanced-tab" data-toggle="tab" href="#advanced" role="tab" aria-controls="button"
            aria-selected="false">{{ translate('Advanced') }}</a>
    </li>
</ul>
<div class="tab-content" id="myTabContent">

    <!-- Content Properties -->
    <div class="tab-pane fade show active" id="content-info" role="tabpanel" aria-labelledby="content-info-tab">

        <!-- Select Sellers -->
        <div class="form-row mb-3">
            <div class="col-sm-12">
                <label class="font-14 bold black">{{ translate('Select Sellers') }}</label>
            </div>
            <div class="col-sm-12">
                <select class="theme-input-style seller-selector" name="sellers[]" multiple="multiple" required>
                    @foreach ($sellers as $seller)
                        <option value="{{ $seller->id }}" @if (in_array($seller->id, $selectedSellers)) selected @endif>
                            {{ $seller->name }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>

        <!-- Banner Options -->
        <div class="form-row mb-3">
            <div class="col-sm-12">
                <label class="font-14 bold black"> {{ translate('Banner Option') }} </label>
            </div>
            <div class="col-sm-12">
                <select class="theme-input-style" name="banner_option" id="banner_option" required>
                    <option value="with_banner" @selected(isset($widget_properties['banner_option']) && $widget_properties['banner_option'] == 'with_banner')>
                        {{ translate('With Banner') }}</option>

                    <option value="without_banner" @selected(isset($widget_properties['banner_option']) && $widget_properties['banner_option'] == 'without_banner')>
                        {{ translate('Without Banner') }}
                    </option>
                </select>
            </div>
            <div class="col-12 mt-3">
                <img src="/public/themes/tlcommerce/assets/img/seller_with_banner.png"
                    alt="Seller With Banner"
                    class="with_banner {{ isset($widget_properties['banner_option']) && $widget_properties['banner_option'] == 'with_banner' ? '' : 'd-none' }}">
                <img src="/public/themes/tlcommerce/assets/img/seller_without_banner.png"
                    alt="Seller Without Banner"
                    class="without_banner {{ isset($widget_properties['banner_option']) && $widget_properties['banner_option'] == 'without_banner' ? '' : 'd-none' }}">
            </div>
        </div>

        <!-- Seller List Style -->
        <div class="form-group mt-2">
            <label for="style" class="col-5 mb-2 font-14 bold black">{{ translate('Seller List Style') }}
            </label>
            <select name="style" id="style" class="col-6 form-control d-inline ml-4" required>
                <option value="slider" @selected(isset($widget_properties['style']) && $widget_properties['style'] == 'slider')>{{ translate('Slider') }}</option>
                <option value="list" @selected(isset($widget_properties['style']) && $widget_properties['style'] == 'list')>{{ translate('List') }}</option>
            </select>
        </div>

        <!-- Slider Item -->
        <div
            class="form-group mt-2 slide-item {{ isset($widget_properties['style']) && $widget_properties['style'] == 'list' ? 'd-none' : '' }}">
            <label for="slide_item" class="col-5 mb-2 font-14 bold black p-0">{{ translate('Slider Item') }}
            </label>
            <div class="row">
                <div class="col-sm-4">
                    <select name="slide_item_lg" id="slide_item_lg" class="form-control">
                        <option value="1" @selected(isset($widget_properties['slide_item_lg']) && $widget_properties['slide_item_lg'] == '1')>1 Item</option>
                        <option value="2" @selected(isset($widget_properties['slide_item_lg']) && $widget_properties['slide_item_lg'] == '2')>2 Item</option>
                        <option value="3" @selected(isset($widget_properties['slide_item_lg']) && $widget_properties['slide_item_lg'] == '3')>3 Item</option>
                        <option value="4" @selected(isset($widget_properties['slide_item_lg']) && $widget_properties['slide_item_lg'] == '4')>4 Item</option>
                        <option value="5" @selected(isset($widget_properties['slide_item_lg']) && $widget_properties['slide_item_lg'] == '5')>5 Item</option>
                        <option value="6" @selected(isset($widget_properties['slide_item_lg']) && $widget_properties['slide_item_lg'] == '6')>6 Item</option>
                    </select>
                    <small class="bold ml-2">{{ translate('Desktop') }}</small>
                </div>

                <div class="col-sm-4">
                    <select name="slide_item_md" id="slide_item_md" class="form-control">
                        <option value="1" @selected(isset($widget_properties['slide_item_md']) && $widget_properties['slide_item_md'] == '1')>1 Item</option>
                        <option value="2" @selected(isset($widget_properties['slide_item_md']) && $widget_properties['slide_item_md'] == '2')>2 Item</option>
                        <option value="3" @selected(isset($widget_properties['slide_item_md']) && $widget_properties['slide_item_md'] == '3')>3 Item</option>
                        <option value="4" @selected(isset($widget_properties['slide_item_md']) && $widget_properties['slide_item_md'] == '4')>4 Item</option>
                        <option value="5" @selected(isset($widget_properties['slide_item_md']) && $widget_properties['slide_item_md'] == '5')>5 Item</option>
                        <option value="6" @selected(isset($widget_properties['slide_item_md']) && $widget_properties['slide_item_md'] == '6')>6 Item</option>
                    </select>
                    <small class="bold ml-2">{{ translate('Tab') }}</small>
                </div>

                <div class="col-sm-4">
                    <select name="slide_item_sm" id="slide_item_sm" class="form-control">
                        <option value="1" @selected(isset($widget_properties['slide_item_sm']) && $widget_properties['slide_item_sm'] == '1')>1 Item</option>
                        <option value="2" @selected(isset($widget_properties['slide_item_sm']) && $widget_properties['slide_item_sm'] == '2')>2 Item</option>
                        <option value="3" @selected(isset($widget_properties['slide_item_sm']) && $widget_properties['slide_item_sm'] == '3')>3 Item</option>
                        <option value="4" @selected(isset($widget_properties['slide_item_sm']) && $widget_properties['slide_item_sm'] == '4')>4 Item</option>
                        <option value="5" @selected(isset($widget_properties['slide_item_sm']) && $widget_properties['slide_item_sm'] == '5')>5 Item</option>
                        <option value="6" @selected(isset($widget_properties['slide_item_sm']) && $widget_properties['slide_item_sm'] == '6')>6 Item</option>
                    </select>
                    <small class="bold ml-2">{{ translate('Mobile') }}</small>
                </div>
            </div>
            <!-- Pagination -->
            <label for="pagination" class="ml-3 mt-3">
                <input type="checkbox" name="pagination" id="pagination" @checked(isset($widget_properties['pagination']) && $widget_properties['pagination'] == '1')
                    value="1">
                {{ translate('Show Slider Pagination') }}
            </label>
        </div>

        <!-- Seller Column -->
        <div
            class="form-group mt-2 product-column {{ isset($widget_properties['style']) && $widget_properties['style'] == 'list' ? '' : 'd-none' }}">
            <label for="column" class="col-5 mb-2 font-14 bold black">{{ translate('Seller Column') }}
            </label>
            <select name="column" id="column" class="col-6 form-control d-inline ml-4">
                <option value="col-12" @selected(isset($widget_properties['column']) && $widget_properties['column'] == 'col-12')>1 Column</option>
                <option value="col-6" @selected(isset($widget_properties['column']) && $widget_properties['column'] == 'col-6')>2 Column</option>
                <option value="col-sm-6 col-md-4" @selected(isset($widget_properties['column']) && $widget_properties['column'] == 'col-sm-6 col-md-4')>3 Column</option>
                <option value="col-sm-6 col-md-3" @selected(isset($widget_properties['column']) && $widget_properties['column'] == 'col-sm-6 col-md-3')>4 Column</option>
                <option value="col-sm-6 col-md-2" @selected(isset($widget_properties['column']) && $widget_properties['column'] == 'col-sm-6 col-md-2')>6 Column</option>
            </select>
        </div>

    </div>

    <!-- Include background Properties -->
    @include('plugin/tlcommerce-pagebuilder::page-builder.properties.background-properties', [
        'properties' => $widget_properties,
    ])

    <!-- Include Advance Properties -->
    @include('plugin/tlcommerce-pagebuilder::page-builder.properties.advance-properties', [
        'properties' => $widget_properties,
    ])
</div>
<script>
    (function($) {
        'use strict';

        // Select Init
        $('.seller-selector').select2({
            theme: "classic",
            placeholder: 'Select Seller'
        });

        $('#banner_option').on('change', function() {
            let option = $(this).find('option:selected').val();
            if (option == 'with_banner') {
                $('.with_banner').removeClass('d-none');
                $('.without_banner').addClass('d-none');
            }
            if (option == 'without_banner') {
                $('.with_banner').addClass('d-none');
                $('.without_banner').removeClass('d-none');
            }
        });

        $('#style').on('change', function() {
            let option = $(this).find('option:selected').val();
            if (option == 'slider') {
                $('.slide-item').removeClass('d-none');
                $('.product-column').addClass('d-none');
            }
            if (option == 'list') {
                $('.product-column').removeClass('d-none');
                $('.slide-item').addClass('d-none');
            }
        });

    })(jQuery);
</script>
