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
        <!-- Image -->
        <div class="form-group mb-20">
            <div class="col-sm-12">
                <label class="font-14 bold black">{{ translate('Image') }} </label>
            </div>
            <div class="col-md-12">
                @include('core::base.includes.media.media_input', [
                    'input' => 'widget_image',
                    'data' => isset($widget_properties['widget_image'])
                        ? $widget_properties['widget_image']
                        : null,
                ])
            </div>
        </div>

        <!-- Link -->
        <div class="form-group row mt-2">
            <label for="link" class="col-2 mb-2 font-14 bold black">{{ translate('Link') }}
            </label>
            <select name="link" id="link" class="col-5 offset-3 form-control">
                <option value="none" @selected(isset($widget_properties['link']) && $widget_properties['link'] == 'none')>{{ translate('None') }}</option>
                <option value="media_file" @selected(isset($widget_properties['link']) && $widget_properties['link'] == 'media_file')>{{ translate('Media File') }}</option>
                <option value="custom_url" @selected(isset($widget_properties['link']) && $widget_properties['link'] == 'custom_url')>{{ translate('Custom Url') }}</option>
            </select>
            <div
                class="{{ isset($widget_properties['link']) && $widget_properties['link'] == 'custom_url' ? '' : 'd-none' }} col-12 mt-3 link-box">
                <input type="text" name="link_url" id="link_url" class="form-control mb-2"
                    placeholder="{{ translate('Your Link') }}"
                    value="{{ isset($widget_properties['link_url']) ? $widget_properties['link_url'] : '' }}">
                <label for="new_window">
                    <input type="checkbox" name="new_window" id="new_window" @checked(isset($widget_properties['new_window']) && $widget_properties['new_window'] == '1')
                        value="1">
                    {{ translate('Open in new window') }}
                </label>
            </div>
        </div>

        <!-- Alignment -->
        <div class="form-group">
            <label for="alignment" class="d-block mb-2 font-14 bold black">{{ translate('Image Alignment') }}
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
            </div>
        </div>
    </div>

    <!-- Style Properties -->
    <div class="tab-pane fade" id="style" role="tabpanel" aria-labelledby="style-tab">
        <!-- Width -->
        <div class="form-group group">
            <div class="col-12">
                <label class="font-14 bold black">{{ translate('Width') }}
                </label>
            </div>
            <div class="col-12">
                <div class="row align-items-center">
                    <input type="range" class="col-sm-8 range-selector" id="range_width_c_" style="height: 30%;"
                        min="1" max="1000" value="{{ isset($widget_properties['width_c_']) ? $widget_properties['width_c_'] : '' }}">

                    <div class="col-sm-4 input-group addon">
                        <input type="number" class="form-control" name="width_c_" min="1" max="1000"
                            id="width_c_" value="{{ isset($widget_properties['width_c_']) ? $widget_properties['width_c_'] : '' }}">
                        <div class="input-group-append">
                            <span class="input-group-text style--three black bold">px</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Max Width -->
        <div class="form-group group">
            <div class="col-12">
                <label class="font-14 bold black">{{ translate('Max Width') }}
                </label>
            </div>
            <div class="col-12">
                <div class="row align-items-center">
                    <input type="range" class="col-sm-8 range-selector" id="range_max_width_c_" style="height: 30%;"
                        min="1" max="1000" value="{{ isset($widget_properties['max_width_c_']) ? $widget_properties['max_width_c_'] : '' }}">

                    <div class="col-sm-4 input-group addon">
                        <input type="number" class="form-control" name="max_width_c_" min="1" max="1000"
                            id="max_width_c_" value="{{ isset($widget_properties['max_width_c_']) ? $widget_properties['max_width_c_'] : '' }}">
                        <div class="input-group-append">
                            <span class="input-group-text style--three black bold">px</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Height -->
        <div class="form-group group">
            <div class="col-12">
                <label class="font-14 bold black">{{ translate('Height') }}
                </label>
            </div>
            <div class="col-12">
                <div class="row align-items-center">
                    <input type="range" class="col-sm-8 range-selector" id="range_height_c_" style="height: 30%;"
                        min="1" max="1000" value="{{ isset($widget_properties['height_c_']) ? $widget_properties['height_c_'] : '' }}">

                    <div class="col-sm-4 input-group addon">
                        <input type="number" class="form-control" name="height_c_" min="1" max="1000"
                            id="height_c_" value="{{ isset($widget_properties['height_c_']) ? $widget_properties['height_c_'] : '' }}">
                        <div class="input-group-append">
                            <span class="input-group-text style--three black bold">px</span>
                        </div>
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
<script>
    (function($) {
        'use strict'

        // Checked Button
        $(document).on('change', '#link', function() {
            let selected = $(this).find('option:selected').val();
            if (selected && selected == 'custom_url') {
                $('.link-box').removeClass('d-none');
            } else {
                $('.link-box').addClass('d-none');
            }
        })

    })(jQuery);
</script>
