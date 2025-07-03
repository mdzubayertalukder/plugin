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

    <!-- Content Propeties -->
    <div class="tab-pane fade show active" id="content-info" role="tabpanel" aria-labelledby="content-info-tab">
        @include('plugin/pagebuilder::page-builder.includes.lang-translate', ['lang' => $lang, 'widget' => 'button'])

        <!-- Text -->
        <div class="form-group mb-3 translate-field">
            <label for="button_text" class="font-14 bold black">{{ translate('Text') }}</label>
            <div class="mt-1">
                <input type="text" name="button_text_t_" id="button_text" class="form-control"
                    placeholder="{{ translate('Button Text') }}"
                    value="{{ isset($widget_properties['button_text_t_']) ? $widget_properties['button_text_t_'] : '' }}">
            </div>
            <small>{{ translate('Title is visible in homepage. Transalate to another language') }} <a
                    href="{{ route('core.languages') }}">{{ translate('click here') }}.</a></small>
        </div>

        <!-- Link -->
        <div class="form-group mb-3">
            <label for="button_url" class="font-14 bold black">{{ translate('Link') }}</label>
            <div class="mt-1 mb-3">
                <input type="text" name="button_url" id="button_url" class="form-control"
                    placeholder="{{ translate('Button Url') }}"
                    value="{{ isset($widget_properties['button_url']) ? $widget_properties['button_url'] : '' }}">
            </div>
            <label for="new_window">
                <input type="checkbox" name="new_window" id="new_window" @checked(isset($widget_properties['new_window']) && $widget_properties['new_window'] == '1') value="1">
                {{ translate('Open in new window') }}
            </label>
        </div>

        <!-- Alignment -->
        <div class="form-group">
            <label for="alignment" class="d-block mb-2 font-14 bold black">{{ translate('Button Alignment') }}
            </label>
            <div class="btn-group" data-toggle="buttons">
                <label
                    class="btn btn-primary sm {{ isset($widget_properties['alignment']) && $widget_properties['alignment'] == 'left' ? 'active' : '' }}">
                    <input type="radio" class="d-none" name="alignment" id="left" value="left"
                        @checked(isset($widget_properties['alignment']) && $widget_properties['alignment'] == 'left')>
                    {{ translate('Left') }}
                </label>
                <label
                    class="btn btn-primary sm {{ isset($widget_properties['alignment']) && $widget_properties['alignment'] == 'center' ? 'active' : '' }}">
                    <input type="radio"class="d-none" name="alignment" id="center" value="center"
                        @checked(isset($widget_properties['alignment']) && $widget_properties['alignment'] == 'center')>
                    {{ translate('Center') }}
                </label>
                <label
                    class="btn btn-primary sm {{ isset($widget_properties['alignment']) && $widget_properties['alignment'] == 'right' ? 'active' : '' }}">
                    <input type="radio"class="d-none" name="alignment" id="right" value="right"
                        @checked(isset($widget_properties['alignment']) && $widget_properties['alignment'] == 'right')>
                    {{ translate('Right') }}
                </label>
                <label
                    class="btn btn-primary sm {{ isset($widget_properties['alignment']) && $widget_properties['alignment'] == 'justify' ? 'active' : '' }}">
                    <input type="radio"class="d-none" name="alignment" id="justify" value="justify"
                        @checked(isset($widget_properties['alignment']) && $widget_properties['alignment'] == 'justify')>
                    {{ translate('Justify') }}
                </label>
            </div>
        </div>
    </div>

    <!-- Style Propeties -->
    <div class="tab-pane fade" id="style" role="tabpanel" aria-labelledby="style-tab">
        <!-- Button Normal and Hover Color Fields -->
        <ul class="nav nav-tabs mb-20" id="buttonCssTab" role="tablist">
            <li class="nav-item">
                <a class="nav-link active" id="normal-tab" data-toggle="tab" href="#normal" role="tab"
                    aria-controls="normal" aria-selected="false">{{ translate('Normal') }}</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="hover-tab" data-toggle="tab" href="#hover" role="tab"
                    aria-controls="hover" aria-selected="false">{{ translate('Hover') }}</a>
            </li>
        </ul>
        <div class="tab-content" id="buttonTabContent">
            <div class="tab-pane fade show active" id="normal" role="tabpanel" aria-labelledby="normal-tab">
                <div class="form-group mb-20">
                    <div class="col-sm-12">
                        <label class="font-14 bold black">{{ translate('Text Color') }} </label>
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
                <div class="form-group mb-20">
                    <div class="col-sm-12">
                        <label class="font-14 bold black">{{ translate('Background Color') }} </label>
                    </div>
                    <div class="col-sm-12">
                        <div class="input-group addon">
                            <input type="text" name="background_color_c_"
                                class="color-input form-control style--two" placeholder="#000000"
                                value="{{ isset($widget_properties['background_color_c_']) ? $widget_properties['background_color_c_'] : '' }}">
                            <div class="input-group-append">
                                <input type="color" class="input-group-text theme-input-style2 color-picker"
                                    value="{{ isset($widget_properties['background_color_c_']) ? $widget_properties['background_color_c_'] : '#000000' }}">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="tab-pane fade" id="hover" role="tabpanel" aria-labelledby="hover-tab">
                <div class="form-group mb-20">
                    <div class="col-sm-12">
                        <label class="font-14 bold black">{{ translate('Text Color') }} </label>
                    </div>
                    <div class="col-sm-12">
                        <div class="input-group addon">
                            <input type="text" name="hover_color_c_" class="color-input form-control style--two"
                                placeholder="#000000"
                                value="{{ isset($widget_properties['hover_color_c_']) ? $widget_properties['hover_color_c_'] : '' }}">
                            <div class="input-group-append">
                                <input type="color" class="input-group-text theme-input-style2 color-picker"
                                    value="{{ isset($widget_properties['hover_color_c_']) ? $widget_properties['hover_color_c_'] : '#000000' }}">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="form-group mb-20">
                    <div class="col-sm-12">
                        <label class="font-14 bold black">{{ translate('Background Color') }} </label>
                    </div>
                    <div class="col-sm-12">
                        <div class="input-group addon">
                            <input type="text" name="hover_background_color_c_"
                                class="color-input form-control style--two" placeholder="#000000"
                                value="{{ isset($widget_properties['hover_background_color_c_']) ? $widget_properties['hover_background_color_c_'] : '' }}">
                            <div class="input-group-append">
                                <input type="color" class="input-group-text theme-input-style2 color-picker"
                                    value="{{ isset($widget_properties['hover_background_color_c_']) ? $widget_properties['hover_background_color_c_'] : '#000000' }}">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Font Size -->
        <div class="form-row mb-20">
            <label class="col-3 font-14 bold black my-auto">{{ translate('Font Size') }} </label>
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

        <!-- Button padding -->
        <div class="form-row mb-4">
            <div class="col-sm-12">
                <label class="font-14 bold black">{{ translate('Button Padding') }} </label>
            </div>
            <div class="col-sm-3">
                <div class="input-group addon">
                    <input type="number" class="form-control radius-0" name="button_padding_top_c_"
                        placeholder="00"
                        value="{{ isset($widget_properties['button_padding_top_c_']) ? $widget_properties['button_padding_top_c_'] : '' }}">
                    <div class="input-group-append">
                        <span class="input-group-text style--three black bold">px</span>
                    </div>
                </div>
                <small>{{ translate('Top') }}</small>
            </div>
            <div class="col-sm-3">
                <div class="input-group addon">
                    <input type="number" class="form-control radius-0" name="button_padding_right_c_"
                        placeholder="00"
                        value="{{ isset($widget_properties['button_padding_right_c_']) ? $widget_properties['button_padding_right_c_'] : '' }}">
                    <div class="input-group-append">
                        <span class="input-group-text style--three black bold">px</span>
                    </div>
                </div>
                <small>{{ translate('Right') }}</small>
            </div>
            <div class="col-sm-3">
                <div class="input-group addon">
                    <input type="number" class="form-control radius-0" name="button_padding_bottom_c_"
                        placeholder="00"
                        value="{{ isset($widget_properties['button_padding_bottom_c_']) ? $widget_properties['button_padding_bottom_c_'] : '' }}">
                    <div class="input-group-append">
                        <span class="input-group-text style--three black bold">px</span>
                    </div>
                </div>
                <small>{{ translate('Bottom') }}</small>
            </div>
            <div class="col-sm-3">
                <div class="input-group addon">
                    <input type="number" class="form-control radius-0" name="button_padding_left_c_"
                        placeholder="00"
                        value="{{ isset($widget_properties['button_padding_left_c_']) ? $widget_properties['button_padding_left_c_'] : '' }}">
                    <div class="input-group-append">
                        <span class="input-group-text style--three black bold">px</span>
                    </div>
                </div>
                <small>{{ translate('Left') }}</small>
            </div>
        </div>

        <!-- Button Border Radius -->
        <div class="form-row mb-20">
            <label class="col-3 font-14 bold black my-auto">{{ translate('Radius') }} </label>
            <div class="col-5 offset-3">
                <div class="input-group addon">
                    <input type="number" class="form-control radius-0" name="border_radius_c_" placeholder="00"
                        value="{{ isset($widget_properties['border_radius_c_']) ? $widget_properties['border_radius_c_'] : '' }}">
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
