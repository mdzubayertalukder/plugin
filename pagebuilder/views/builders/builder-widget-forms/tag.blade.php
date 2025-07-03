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
        <!-- Tag Type -->
        <div class="form-group mb-3">
            <label for="type" class="font-14 bold black">{{ translate('Tag') }}</label>
            <div class="mt-1">
                <select name="type" id="type" class="form-control">
                    <option value="recent" @selected(isset($widget_properties['type']) && $widget_properties['type'] == 'recent')>{{ translate('Recent Tag') }}</option>
                    <option value="popular" @selected(isset($widget_properties['type']) && $widget_properties['type'] == 'popular')>{{ translate('Popular Tag') }}</option>
                </select>
            </div>
        </div>

        <!-- Tag Count -->
        <div class="form-group mb-3">
            <label for="tag_count" class="font-14 bold black">{{ translate('Tag Count') }}</label>
            <div class="mt-1">
                <input type="number" min="1" step="1" required name="tag_count" id="tag_count" class="form-control"
                    placeholder="{{ translate('Tag Count') }}"
                    value="{{ isset($widget_properties['tag_count']) ? $widget_properties['tag_count'] : '' }}">
            </div>
        </div>
    </div>

    <!-- Style properties -->
    <div class="tab-pane fade" id="style" role="tabpanel" aria-labelledby="style-tab">
        <!-- Tag Text Color -->
        <div class="form-group mb-3">
            <label for="tag_color" class="font-14 bold black">{{ translate('Tag Text Color') }}</label>
            <div class="input-group addon">
                <input type="text" name="tag_color_c_" class="color-input form-control style--two"
                    placeholder="#000000"
                    value="{{ isset($widget_properties['tag_color_c_']) ? $widget_properties['tag_color_c_'] : '' }}">
                <div class="input-group-append">
                    <input type="color" class="input-group-text theme-input-style2 color-picker"
                        value="{{ isset($widget_properties['tag_color_c_']) ? $widget_properties['tag_color_c_'] : '#000000' }}">
                </div>
            </div>
        </div>

        <!-- Tag Text Hover Color -->
        <div class="form-group mb-3">
            <label for="tag_hover_color" class="font-14 bold black">{{ translate('Tag Text Hover Color') }}</label>
            <div class="input-group addon">
                <input type="text" name="tag_hover_color_c_" class="color-input form-control style--two"
                    placeholder="#000000"
                    value="{{ isset($widget_properties['tag_hover_color_c_']) ? $widget_properties['tag_hover_color_c_'] : '' }}">
                <div class="input-group-append">
                    <input type="color" class="input-group-text theme-input-style2 color-picker"
                        value="{{ isset($widget_properties['tag_hover_color_c_']) ? $widget_properties['tag_hover_color_c_'] : '#000000' }}">
                </div>
            </div>
        </div>

        <!-- Tag Background Color -->
        <div class="form-group mb-3">
            <label for="tag_background_color"
                class="font-14 bold black">{{ translate('Tag Background Color') }}</label>
            <div class="input-group addon">
                <input type="text" name="tag_background_color_c_" class="color-input form-control style--two"
                    placeholder="#000000"
                    value="{{ isset($widget_properties['tag_background_color_c_']) ? $widget_properties['tag_background_color_c_'] : '' }}">
                <div class="input-group-append">
                    <input type="color" class="input-group-text theme-input-style2 color-picker"
                        value="{{ isset($widget_properties['tag_background_color_c_']) ? $widget_properties['tag_background_color_c_'] : '#000000' }}">
                </div>
            </div>
        </div>

        <!-- Tag Background Hover Color -->
        <div class="form-group mb-3">
            <label for="tag_hover_background_color"
                class="font-14 bold black">{{ translate('Tag Background Hover Color') }}</label>
            <div class="input-group addon">
                <input type="text" name="tag_hover_background_color_c_"
                    class="color-input form-control style--two" placeholder="#000000"
                    value="{{ isset($widget_properties['tag_hover_background_color_c_']) ? $widget_properties['tag_hover_background_color_c_'] : '' }}">
                <div class="input-group-append">
                    <input type="color" class="input-group-text theme-input-style2 color-picker"
                        value="{{ isset($widget_properties['tag_hover_background_color_c_']) ? $widget_properties['tag_hover_background_color_c_'] : '#000000' }}">
                </div>
            </div>
        </div>

        <!-- Text Font Size -->
        <div class="form-group mb-20">
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
