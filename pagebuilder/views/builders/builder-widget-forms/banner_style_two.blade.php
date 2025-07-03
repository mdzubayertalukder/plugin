<ul class="nav nav-tabs mb-20" id="myTab" role="tablist">
    <li class="nav-item">
        <a class="nav-link active" id="content-info-tab" data-toggle="tab" href="#content-info" role="tab"
            aria-controls="content-info" aria-selected="true">{{ translate('Content') }}</a>
    </li>
    <li class="nav-item">
        <a class="nav-link" id="style-tab" data-toggle="tab" href="#style" role="tab" aria-controls="style"
            aria-selected="true">{{ translate('Style') }}</a>
    </li>
    <li class="nav-item">
        <a class="nav-link" id="advanced-tab" data-toggle="tab" href="#advanced" role="tab" aria-controls="button"
            aria-selected="false">{{ translate('Advanced') }}</a>
    </li>
</ul>
<div class="tab-content" id="myTabContent">
    <!-- Content Properties -->
    <div class="tab-pane fade show active" id="content-info" role="tabpanel" aria-labelledby="content-info-tab">
        @include('plugin/pagebuilder::page-builder.includes.lang-translate', [
            'lang' => $lang,
            'widget' => 'banner_style_two',
        ])

        <!-- Primary Text -->
        <div class="form-group mb-3 translate-field">
            <label for="primary_text" class="font-14 bold black">{{ translate('Primary Text') }}</label>
            <div class="mt-1">
                <input type="text" name="primary_text_t_" id="primary_text" class="form-control"
                    placeholder="{{ translate('Heading Text') }}" required
                    value="{{ isset($widget_properties['primary_text_t_']) ? $widget_properties['primary_text_t_'] : '' }}">
            </div>
        </div>

        <!-- Secondary Text -->
        <div class="form-group mb-3 translate-field">
            <label for="secondary_text" class="font-14 bold black">{{ translate('Secondary Text') }}</label>
            <div class="mt-1">
                <input type="text" name="secondary_text_t_" id="secondary_text" class="form-control"
                    placeholder="{{ translate('Heading Text') }}" required
                    value="{{ isset($widget_properties['secondary_text_t_']) ? $widget_properties['secondary_text_t_'] : '' }}">
            </div>
        </div>

        <!-- Primary Button Text -->
        <div class="form-group mb-3 translate-field">
            <label for="primary_button_text" class="font-14 bold black">{{ translate('Primary Button Text') }}</label>
            <div class="mt-1">
                <input type="text" name="primary_button_text_t_" id="primary_button_text" class="form-control"
                    placeholder="{{ translate('Primary Button Text') }}" required
                    value="{{ isset($widget_properties['primary_button_text_t_']) ? $widget_properties['primary_button_text_t_'] : '' }}">
            </div>
        </div>

        <!-- Primary Button Link -->
        <div class="form-group mb-3">
            <label for="primary_button_url" class="font-14 bold black">{{ translate('Primary Button Link') }}</label>
            <div class="mt-1 mb-3">
                <input type="text" name="primary_button_url" id="primary_button_url" class="form-control"
                    placeholder="{{ translate('Primary Button Url') }}" required
                    value="{{ isset($widget_properties['primary_button_url']) ? $widget_properties['primary_button_url'] : '' }}">
            </div>
            <label for="primary_new_window">
                <input type="checkbox" name="primary_new_window" id="primary_new_window" @checked(isset($widget_properties['primary_new_window']) && $widget_properties['primary_new_window'] == '1')
                    value="1">
                {{ translate('Open in new window') }}
            </label>
        </div>


        <!-- Secondary Button Text -->
        <div class="form-group mb-3 translate-field">
            <label for="secondary_button_text"
                class="font-14 bold black">{{ translate('Secondary Button Text') }}</label>
            <div class="mt-1">
                <input type="text" name="secondary_button_text_t_" id="secondary_button_text" class="form-control"
                    placeholder="{{ translate('Secondary Button Text') }}" required
                    value="{{ isset($widget_properties['secondary_button_text_t_']) ? $widget_properties['secondary_button_text_t_'] : '' }}">
            </div>
        </div>

        <!-- Secondary Button Link -->
        <div class="form-group mb-3">
            <label for="secondary_button_url"
                class="font-14 bold black">{{ translate('Secondary Button Link') }}</label>
            <div class="mt-1 mb-3">
                <input type="text" name="secondary_button_url" id="secondary_button_url" class="form-control"
                    placeholder="{{ translate('Secondary Button Url') }}" required
                    value="{{ isset($widget_properties['secondary_button_url']) ? $widget_properties['secondary_button_url'] : '' }}">
            </div>
            <label for="secondary_new_window">
                <input type="checkbox" name="secondary_new_window" id="secondary_new_window"
                    @checked(isset($widget_properties['secondary_new_window']) && $widget_properties['secondary_new_window'] == '1') value="1">
                {{ translate('Open in new window') }}
            </label>
        </div>

        <!-- Background Image -->
        <div class="form-group mb-20">
            <label class="font-14 bold black">{{ translate('Background Image') }} </label>
            @include('core::base.includes.media.media_input', [
                'input' => 'background_image',
                'data' => isset($widget_properties['background_image'])
                    ? $widget_properties['background_image']
                    : null,
            ])
        </div>

        <!-- Background Shape Image -->
        <div class="form-group mb-20">
            <label class="font-14 bold black">{{ translate('Background Shape Image') }} </label>
            @include('core::base.includes.media.media_input', [
                'input' => 'background_shape_image',
                'data' => isset($widget_properties['background_shape_image'])
                    ? $widget_properties['background_shape_image']
                    : null,
            ])
        </div>

        <!-- Foreground Image -->
        <div class="form-group mb-20">
            <label class="font-14 bold black">{{ translate('Foreground Image') }} </label>
            @include('core::base.includes.media.media_input', [
                'input' => 'foreground_image',
                'data' => isset($widget_properties['foreground_image'])
                    ? $widget_properties['foreground_image']
                    : null,
            ])
        </div>
    </div>
    <!-- /Content Properties -->

    <!-- Style properties -->
    <div class="tab-pane fade" id="style" role="tabpanel" aria-labelledby="style-tab">
        <!-- Primary Text Color -->
        <div class="form-group mb-20">
            <div class="col-sm-12">
                <label class="font-14 bold black">{{ translate('Primary Text Color') }} </label>
            </div>
            <div class="col-sm-12">
                <div class="input-group addon">
                    <input type="text" name="primary_text_color_c_" class="color-input form-control style--two"
                        placeholder="#000000"
                        value="{{ isset($widget_properties['primary_text_color_c_']) ? $widget_properties['primary_text_color_c_'] : '' }}">
                    <div class="input-group-append">
                        <input type="color" class="input-group-text theme-input-style2 color-picker"
                            value="{{ isset($widget_properties['primary_text_color_c_']) ? $widget_properties['primary_text_color_c_'] : '#000000' }}">
                    </div>
                </div>
            </div>
        </div>

        <!-- Secondary Text Color -->
        <div class="form-group mb-20">
            <div class="col-sm-12">
                <label class="font-14 bold black">{{ translate('Secondary Text Color') }} </label>
            </div>
            <div class="col-sm-12">
                <div class="input-group addon">
                    <input type="text" name="secondary_text_color_c_" class="color-input form-control style--two"
                        placeholder="#000000"
                        value="{{ isset($widget_properties['secondary_text_color_c_']) ? $widget_properties['secondary_text_color_c_'] : '' }}">
                    <div class="input-group-append">
                        <input type="color" class="input-group-text theme-input-style2 color-picker"
                            value="{{ isset($widget_properties['secondary_text_color_c_']) ? $widget_properties['secondary_text_color_c_'] : '#000000' }}">
                    </div>
                </div>
            </div>
        </div>

        <!-- Primary Button Color -->
        <div class="form-group mb-20">
            <div class="col-sm-12">
                <label class="font-14 bold black">{{ translate('Primary Button Color') }} </label>
            </div>
            <div class="col-sm-12">
                <div class="input-group addon">
                    <input type="text" name="primary_button_color_c_" class="color-input form-control style--two"
                        placeholder="#000000"
                        value="{{ isset($widget_properties['primary_button_color_c_']) ? $widget_properties['primary_button_color_c_'] : '' }}">
                    <div class="input-group-append">
                        <input type="color" class="input-group-text theme-input-style2 color-picker"
                            value="{{ isset($widget_properties['primary_button_color_c_']) ? $widget_properties['primary_button_color_c_'] : '#000000' }}">
                    </div>
                </div>
            </div>
        </div>

        <!-- Primary Button BG Color -->
        <div class="form-group mb-20">
            <div class="col-sm-12">
                <label class="font-14 bold black">{{ translate('Primary Button BG Color') }} </label>
            </div>
            <div class="col-sm-12">
                <div class="input-group addon">
                    <input type="text" name="primary_button_background_color_c_"
                        class="color-input form-control style--two" placeholder="#000000"
                        value="{{ isset($widget_properties['primary_button_background_color_c_']) ? $widget_properties['primary_button_background_color_c_'] : '' }}">
                    <div class="input-group-append">
                        <input type="color" class="input-group-text theme-input-style2 color-picker"
                            value="{{ isset($widget_properties['primary_button_background_color_c_']) ? $widget_properties['primary_button_background_color_c_'] : '#000000' }}">
                    </div>
                </div>
            </div>
        </div>

        <!-- Primary Button Hover Color -->
        <div class="form-group mb-20">
            <div class="col-sm-12">
                <label class="font-14 bold black">{{ translate('Primary Button Hover Color') }} </label>
            </div>
            <div class="col-sm-12">
                <div class="input-group addon">
                    <input type="text" name="primary_button_hover_color_c_"
                        class="color-input form-control style--two" placeholder="#000000"
                        value="{{ isset($widget_properties['primary_button_hover_color_c_']) ? $widget_properties['primary_button_hover_color_c_'] : '' }}">
                    <div class="input-group-append">
                        <input type="color" class="input-group-text theme-input-style2 color-picker"
                            value="{{ isset($widget_properties['primary_button_hover_color_c_']) ? $widget_properties['primary_button_hover_color_c_'] : '#000000' }}">
                    </div>
                </div>
            </div>
        </div>

        <!-- Primary Button Hover BG Color -->
        <div class="form-group mb-20">
            <div class="col-sm-12">
                <label class="font-14 bold black">{{ translate('Primary Button Hover BG Color') }} </label>
            </div>
            <div class="col-sm-12">
                <div class="input-group addon">
                    <input type="text" name="primary_button_hover_background_color_c_"
                        class="color-input form-control style--two" placeholder="#000000"
                        value="{{ isset($widget_properties['primary_button_hover_background_color_c_']) ? $widget_properties['primary_button_hover_background_color_c_'] : '' }}">
                    <div class="input-group-append">
                        <input type="color" class="input-group-text theme-input-style2 color-picker"
                            value="{{ isset($widget_properties['primary_button_hover_background_color_c_']) ? $widget_properties['primary_button_hover_background_color_c_'] : '#000000' }}">
                    </div>
                </div>
            </div>
        </div>

        <!-- Secondary Button Color -->
        <div class="form-group mb-20">
            <div class="col-sm-12">
                <label class="font-14 bold black">{{ translate('Secondary Button Color') }} </label>
            </div>
            <div class="col-sm-12">
                <div class="input-group addon">
                    <input type="text" name="secondary_button_color_c_"
                        class="color-input form-control style--two" placeholder="#000000"
                        value="{{ isset($widget_properties['secondary_button_color_c_']) ? $widget_properties['secondary_button_color_c_'] : '' }}">
                    <div class="input-group-append">
                        <input type="color" class="input-group-text theme-input-style2 color-picker"
                            value="{{ isset($widget_properties['secondary_button_color_c_']) ? $widget_properties['secondary_button_color_c_'] : '#000000' }}">
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- /Style properties -->

    <!-- Include Advance Properties -->
    @include('plugin/pagebuilder::page-builder.properties.advance-properties', [
        'properties' => $widget_properties,
    ])
</div>
<script>
    (function($) {
        'use strict'

        // Checked Button
        let heading_alignment = $('input[name="heading_alignment"]:checked');
        heading_alignment.parent().addClass('active');

    })(jQuery);
</script>