@php
    $products = Plugin\TlcommerceCore\Models\Product::select('id', 'name', 'permalink')->get();
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
            'widget' => 'featured_product',
        ])

        <!-- Product List -->
        <div class="form-row mb-3">
            <div class="col-sm-12">
                <label class="font-14 bold black">{{ translate('Select Product') }}</label>
            </div>
            <div class="col-sm-12">
                @if (count($products) > 0)
                    <select class="form-control" name="product_id" id="select_product">
                        @foreach ($products as $product)
                            <option value="{{ $product->id }}" data-permalink="{{ $product->permalink }}"
                                @selected(isset($widget_properties['product_id']) && $widget_properties['product_id'] == $product->id)>
                                {{ $product->translation('name', getLocale()) }}
                            </option>
                        @endforeach
                    </select>
                @endif
            </div>
            <input type="hidden" name="permalink" id="permalink">
        </div>

        <!-- Featured Product Image -->
        <div class="form-row mb-3">
            <div class="col-sm-12">
                <label class="font-14 bold black">{{ translate('Featured Product Image') }} </label>
            </div>
            <div class="col-md-12">
                @include('core::base.includes.media.media_input', [
                    'input' => 'cta_image',
                    'data' => isset($widget_properties['cta_image']) ? $widget_properties['cta_image'] : null,
                ])
            </div>
        </div>

        <!-- Featured Product Video Url -->
        <div class="form-row mb-20">
            <div class="col-sm-12">
                <label class="font-14 bold black">{{ translate('Video url') }} </label>
            </div>
            <div class="col-sm-12">
                <div class="input-group addon">
                    <input type="text" name="video_url" class="theme-input-style" placeholder="Youtube video link"
                        value="{{ isset($widget_properties['video_url']) ? $widget_properties['video_url'] : '' }}">
                </div>
            </div>
        </div>

        <!-- Featured Product Meta Title -->
        <div class="form-row mb-20 translate-field">
            <div class="col-sm-12">
                <label class="font-14 bold black">{{ translate('Meta Title') }} </label>
            </div>
            <div class="col-sm-12">
                <div class="input-group addon">
                    <input type="text" name="meta_title_t_" class="theme-input-style"
                        value="{{ isset($widget_properties['meta_title_t_']) ? $widget_properties['meta_title_t_'] : '' }}"
                        placeholder="Meta Title" required>
                </div>
            </div>
        </div>

        <!-- Featured Product Title -->
        <div class="form-row mb-20 translate-field">
            <div class="col-sm-12">
                <label class="font-14 bold black">{{ translate('Title') }} </label>
            </div>
            <div class="col-sm-12">
                <div class="input-group addon">
                    <input type="text" name="title_t_" class="theme-input-style"
                        value="{{ isset($widget_properties['title_t_']) ? $widget_properties['title_t_'] : '' }}"
                        placeholder="Title" required>
                </div>
            </div>
        </div>

        <!-- Featured Product Paragraph -->
        <div class="form-row mb-20 translate-field">
            <div class="col-sm-12">
                <label class="font-14 bold black">{{ translate('Paragraph') }} </label>
            </div>
            <div class="col-sm-12">
                <div class="input-group addon">
                    <input type="text" name="paragraph_t_" class="theme-input-style"
                        value="{{ isset($widget_properties['paragraph_t_']) ? $widget_properties['paragraph_t_'] : '' }}"
                        placeholder="{{ translate('Paragraph') }}" required>
                </div>
            </div>
        </div>

        <!-- Featured Product Button Text -->
        <div class="form-row mb-20 translate-field">
            <div class="col-sm-12">
                <label class="font-14 bold black">{{ translate('Button Text') }}</label>
            </div>
            <div class="col-sm-12">
                <div class="input-group addon">
                    <input type="text" name="button_text_t_" class="theme-input-style"
                        value="{{ isset($widget_properties['button_text_t_']) ? $widget_properties['button_text_t_'] : '' }}"
                        placeholder="{{ translate('Button Text') }}" required>
                </div>
            </div>
        </div>

    </div>

    <!-- Style Properties -->
    <div class="tab-pane fade" id="stylist" role="tabpanel" aria-labelledby="stylist-tab">

        <!-- Text Color -->
        <div class="form-group mb-20">
            <div class="col-sm-12">
                <label class="font-14 bold black">{{ translate('Text Color') }} </label>
            </div>
            <div class="col-sm-12">
                <div class="input-group addon">
                    <input type="text" name="text_color_c_" class="color-input form-control style--two"
                        placeholder="#000000"
                        value="{{ isset($widget_properties['text_color_c_']) ? $widget_properties['text_color_c_'] : '' }}">
                    <div class="input-group-append">
                        <input type="color" class="input-group-text theme-input-style2 color-picker"
                            value="{{ isset($widget_properties['text_color_c_']) ? $widget_properties['text_color_c_'] : '#000000' }}">
                    </div>
                </div>
            </div>
        </div>

        <!-- Play Button Color -->
        <div class="form-group mb-20">
            <div class="col-sm-12">
                <label class="font-14 bold black">{{ translate('Play Button Color') }} </label>
            </div>
            <div class="col-sm-12">
                <div class="input-group addon">
                    <input type="text" name="play_button_color_c_" class="color-input form-control style--two"
                        placeholder="#000000"
                        value="{{ isset($widget_properties['play_button_color_c_']) ? $widget_properties['play_button_color_c_'] : '' }}">
                    <div class="input-group-append">
                        <input type="color" class="input-group-text theme-input-style2 color-picker"
                            value="{{ isset($widget_properties['play_button_color_c_']) ? $widget_properties['play_button_color_c_'] : '#000000' }}">
                    </div>
                </div>
            </div>
        </div>

        <!-- Play Button Border Color -->
        <div class="form-group mb-20">
            <div class="col-sm-12">
                <label class="font-14 bold black">{{ translate('Play Button Border Color') }} </label>
            </div>
            <div class="col-sm-12">
                <div class="input-group addon">
                    <input type="text" name="play_button_border_color_c_"
                        class="color-input form-control style--two" placeholder="#000000"
                        value="{{ isset($widget_properties['play_button_border_color_c_']) ? $widget_properties['play_button_border_color_c_'] : '' }}">
                    <div class="input-group-append">
                        <input type="color" class="input-group-text theme-input-style2 color-picker"
                            value="{{ isset($widget_properties['play_button_border_color_c_']) ? $widget_properties['play_button_border_color_c_'] : '#000000' }}">
                    </div>
                </div>
            </div>
        </div>

        <!-- Button Color -->
        <div class="form-group mb-20">
            <div class="col-sm-12">
                <label class="font-14 bold black">{{ translate('Button Color') }} </label>
            </div>
            <div class="col-sm-12">
                <div class="input-group addon">
                    <input type="text" name="button_color_c_" class="color-input form-control style--two"
                        placeholder="#000000"
                        value="{{ isset($widget_properties['button_color_c_']) ? $widget_properties['button_color_c_'] : '' }}">
                    <div class="input-group-append">
                        <input type="color" class="input-group-text theme-input-style2 color-picker"
                            value="{{ isset($widget_properties['button_color_c_']) ? $widget_properties['button_color_c_'] : '#000000' }}">
                    </div>
                </div>
            </div>
        </div>

        <!-- Button Hover Color -->
        <div class="form-group mb-20">
            <div class="col-sm-12">
                <label class="font-14 bold black">{{ translate('Button Hover Color') }} </label>
            </div>
            <div class="col-sm-12">
                <div class="input-group addon">
                    <input type="text" name="button_hover_color_c_" class="color-input form-control style--two"
                        placeholder="#000000"
                        value="{{ isset($widget_properties['button_hover_color_c_']) ? $widget_properties['button_hover_color_c_'] : '' }}">
                    <div class="input-group-append">
                        <input type="color" class="input-group-text theme-input-style2 color-picker"
                            value="{{ isset($widget_properties['button_hover_color_c_']) ? $widget_properties['button_hover_color_c_'] : '#000000' }}">
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
        'use strict';

        // Select Init
        $('#select_product').select2({
            theme: "classic"
        });

        // No Product Select
        let permalink = $('#select_product option:first').data('permalink');
        $('#permalink').val(permalink);

        // On Product Select
        $('#select_product').on('change', function() {
            let selected_permalink = $(this).find('option:selected').data('permalink');
            $('#permalink').val(selected_permalink);
        })

    })(jQuery);
</script>
