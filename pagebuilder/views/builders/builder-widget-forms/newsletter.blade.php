<ul class="nav nav-tabs mb-20" id="myTab" role="tablist">
    <li class="nav-item">
        <a class="nav-link active" id="content-info-tab" data-toggle="tab" href="#content-info" role="tab"
            aria-controls="content-info" aria-selected="true">{{ translate('Content') }}</a>
    </li>
    <li class="nav-item">
        <a class="nav-link" id="style-tab" data-toggle="tab" href="#style" role="tab" aria-controls="style"
            aria-selected="false">{{ translate('Style') }}</a>
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
        @include('plugin/pagebuilder::page-builder.includes.lang-translate', ['lang' => $lang, 'widget' => 'newsletter'])

        <!-- Newsletter Short Desc -->
        <div class="form-group mb-3 translate-field">
            <label for="short_desc" class="font-14 bold black">{{ translate('Newsletter Short Desc') }}</label>
            <textarea id="short_desc" name="short_desc_t_" class="theme-input-style style--two"
                placeholder="{{ translate('Newsletter Short Desc') }}">{{ isset($widget_properties['short_desc_t_']) ? $widget_properties['short_desc_t_'] : '' }}</textarea>
        </div>

        <!-- Email Placeholder -->
        <div class="form-group mb-3 translate-field">
            <label for="email_placeholder" class="font-14 bold black">{{ translate('Email Placeholder') }}</label>
            <input type="text" id="email_placeholder" name="email_placeholder_t_" class="form-control"
                placeholder="{{ translate('Email Placeholder') }}"
                value="{{ isset($widget_properties['email_placeholder_t_']) ? $widget_properties['email_placeholder_t_'] : '' }}">
        </div>

        <!-- Button Text -->
        <div class="form-group mb-4 translate-field">
            <label for="btn_text" class="font-14 bold black">{{ translate('Button Text') }}</label>
            <input type="text" id="btn_text" name="btn_text_t_" class="form-control"
                placeholder="{{ translate('Button Text') }}" value="{{ isset($widget_properties['btn_text_t_']) ? $widget_properties['btn_text_t_'] : '' }}">
        </div>
    </div>

    <!-- Style Properties -->
    <div class="tab-pane fade" id="style" role="tabpanel" aria-labelledby="style-tab">
        <!-- Text Color -->
        <div class="form-group mb-3">
            <label for="color" class="font-14 font-14 bold black">{{ translate('Text Color') }}</label>
            <div class="input-group addon">
                <input type="text" name="color_c_" class="color-input form-control style--two"
                    placeholder="#000000" value="{{ isset($widget_properties['color_c_']) ? $widget_properties['color_c_'] : '' }}">
                <div class="input-group-append">
                    <input type="color" class="input-group-text theme-input-style2 color-picker" value="{{ isset($widget_properties['color_c_']) ? $widget_properties['color_c_'] : '#000000' }}">
                </div>
            </div>
        </div>

        <!-- Button Text Color -->
        <div class="form-group mb-3">
            <label for="btn_color" class="font-14 bold black">{{ translate('Button Text Color') }}</label>
            <div class="input-group addon">
                <input type="text" name="btn_color_c_" class="color-input form-control style--two"
                    placeholder="#000000" value="{{ isset($widget_properties['btn_color_c_']) ? $widget_properties['btn_color_c_'] : '' }}">
                <div class="input-group-append">
                    <input type="color" class="input-group-text theme-input-style2 color-picker" value="{{ isset($widget_properties['btn_color_c_']) ? $widget_properties['btn_color_c_'] : '#000000' }}">
                </div>
            </div>
        </div>

        <!-- Button Text Hover Color -->
        <div class="form-group mb-3">
            <label for="btn_hover_color" class="font-14 bold black">{{ translate('Button Text Hover Color') }}</label>
            <div class="input-group addon">
                <input type="text" name="btn_hover_color_c_" class="color-input form-control style--two"
                    placeholder="#000000" value="{{ isset($widget_properties['btn_hover_color_c_']) ? $widget_properties['btn_hover_color_c_'] : '' }}">
                <div class="input-group-append">
                    <input type="color" class="input-group-text theme-input-style2 color-picker" value="{{ isset($widget_properties['btn_hover_color_c_']) ? $widget_properties['btn_hover_color_c_'] : '#000000' }}">
                </div>
            </div>
        </div>

        <!-- Button Background Color -->
        <div class="form-group mb-3">
            <label for="btn_background_color" class="font-14 bold black">{{ translate('Button Background Color') }}</label>
            <div class="input-group addon">
                <input type="text" name="btn_background_color_c_" class="color-input form-control style--two"
                    placeholder="#000000" value="{{ isset($widget_properties['btn_background_color_c_']) ? $widget_properties['btn_background_color_c_'] : '' }}">
                <div class="input-group-append">
                    <input type="color" class="input-group-text theme-input-style2 color-picker" value="{{ isset($widget_properties['btn_background_color_c_']) ? $widget_properties['btn_background_color_c_'] : '#000000' }}">
                </div>
            </div>
        </div>

        <!-- Button Background Hover Color -->
        <div class="form-group mb-3">
            <label for="btn_hover_background_color" class="font-14 bold black">{{ translate('Button Background Hover Color') }}</label>
            <div class="input-group addon">
                <input type="text" name="btn_hover_background_color_c_" class="color-input form-control style--two"
                    placeholder="#000000" value="{{ isset($widget_properties['btn_hover_background_color_c_']) ? $widget_properties['btn_hover_background_color_c_'] : '' }}">
                <div class="input-group-append">
                    <input type="color" class="input-group-text theme-input-style2 color-picker" value="{{ isset($widget_properties['btn_hover_background_color_c_']) ? $widget_properties['btn_hover_background_color_c_'] : '#000000' }}">
                </div>
            </div>
        </div>

        <!-- Text Font Size -->
        <div class="form-row mb-20">
            <label class="col-3 font-14 bold black my-auto">{{ translate('Text Font Size') }} </label>
            <div class="col-5 offset-3">
                <div class="input-group addon">
                    <input type="number" class="form-control radius-0" name="font_size_c_" placeholder="00"
                        value="{{ isset($widget_properties['font_size_c_']) ? $widget_properties['font_size_c_'] : '' }}">
                    <div class="input-group-append">
                        <span class="input-group-text style--three black bold">px</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Include background Properties -->
    @include('plugin/pagebuilder::page-builder.properties.background-properties', [
        'properties' => $widget_properties,
    ])

    <!-- Include Advance Properties -->
    @include('plugin/pagebuilder::page-builder.properties.advance-properties', [
        'properties' => $widget_properties,
    ])
</div>
