@php
    $deals = \Plugin\Flashdeal\Models\FlashDeal::where('status', config('settings.general_status.active'))->get();
@endphp
<ul class="nav nav-tabs mb-20" id="myTab" role="tablist">
    <li class="nav-item">
        <a class="nav-link active" id="content-info-tab" data-toggle="tab" href="#content-info" role="tab"
            aria-controls="content-info" aria-selected="true">{{ translate('Content') }}</a>
    </li>
    <li class="nav-item">
        <a class="nav-link" id="stylist-tab" data-toggle="tab" href="#stylist" role="tab" aria-controls="stylist"
            aria-selected="true">{{ translate('Style') }}</a>
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
        @include('plugin/tlcommerce-pagebuilder::page-builder.includes.lang-translate', [
            'lang' => $lang,
            'widget' => 'flash_deal',
        ])

        <!-- Select Flash Deal -->
        <div class="form-row mb-3">
            <div class="col-sm-12">
                <label class="font-14 bold black">{{ translate('Select Flash Deal') }}</label>
            </div>
            <div class="col-sm-12">
                @if (count($deals) > 0)
                    <select class="theme-input-style" name="flash_deal_id">
                        @foreach ($deals as $deal)
                            <option value="{{ $deal->id }}" @selected(isset($widget_properties['flash_deal_id']) && $widget_properties['flash_deal_id'] == $deal->id)>
                                {{ $deal->translation('title', getLocale()) }}
                            </option>
                        @endforeach
                    </select>
                @else
                    <a href="{{ route('plugin.flashdeal.list') }}" target="_blank"><span
                            class="text text-danger">{{ translate('No Deal Found') }}.</span>
                        {{ translate('Create New Deal') }}</a>
                @endif
            </div>
        </div>

        <!-- Title -->
        <div class="form-row mb-3 translate-field">
            <div class="col-sm-12">
                <label class="font-14 bold black">{{ translate('Title') }} </label>
            </div>
            <div class="col-sm-12">
                <div class="input-group addon">
                    <input type="text" name="title_t_" class="theme-input-style"
                        placeholder="{{ translate('Title') }}"
                        value="{{ isset($widget_properties['title_t_']) ? $widget_properties['title_t_'] : '' }}"
                        required>

                </div>
            </div>
        </div>

        <!-- Flash Deal Count -->
        <div class="form-group mb-3">
            <label for="count" class="font-14 bold black">{{ translate('Product Count') }}</label>
            <div class="mt-1">
                <input type="number" min="1" step="1" required name="count" id="count"
                    class="form-control" placeholder="{{ translate('Product Count') }}"
                    value="{{ isset($widget_properties['count']) ? $widget_properties['count'] : '' }}">
            </div>
        </div>

        <!-- Falsh Deal Style -->
        <div class="form-group mt-2">
            <label for="style" class="col-5 mb-2 font-14 bold black">{{ translate('Falsh Deal Style') }}
            </label>
            <select name="style" id="style" class="col-6 form-control d-inline ml-4">
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

        <!-- Product Column -->
        <div
            class="form-group mt-2 product-column {{ isset($widget_properties['style']) && $widget_properties['style'] == 'list' ? '' : 'd-none' }}">
            <label for="column" class="col-5 mb-2 font-14 bold black">{{ translate('Product Column') }}
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

    <!-- Style Properties -->
    <div class="tab-pane fade" id="stylist" role="tabpanel" aria-labelledby="stylist-tab">
        <!-- Title Color -->
        <div class="form-group mb-20">
            <div class="col-sm-12">
                <label class="font-14 bold black">{{ translate('Title Color') }} </label>
            </div>
            <div class="col-sm-12">
                <div class="input-group addon">
                    <input type="text" name="color_c_" class="color-input form-control style--two"
                        placeholder="#000000"
                        value="{{ isset($widget_properties['color_c_']) ? $widget_properties['color_c_'] : '' }}">
                    <div class="input-group-append">
                        <input type="color" class="input-group-text theme-input-style2 color-picker"
                            value="{{ isset($widget_properties['color_c_']) ? $widget_properties['color_c_'] : '#000000' }}">
                    </div>
                </div>
            </div>
        </div>

        <!-- Title Color -->
        <div class="form-group mb-20">
            <div class="col-sm-12">
                <label class="font-14 bold black">{{ translate('Countdown Color') }} </label>
            </div>
            <div class="col-sm-12">
                <div class="input-group addon">
                    <input type="text" name="count_color_c_" class="color-input form-control style--two"
                        placeholder="#000000"
                        value="{{ isset($widget_properties['count_color_c_']) ? $widget_properties['count_color_c_'] : '' }}">
                    <div class="input-group-append">
                        <input type="color" class="input-group-text theme-input-style2 color-picker"
                            value="{{ isset($widget_properties['count_color_c_']) ? $widget_properties['count_color_c_'] : '#000000' }}">
                    </div>
                </div>
            </div>
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
        'use strict'

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
