<ul class="nav nav-tabs mb-20" id="myTab" role="tablist">
    <li class="nav-item">
        <a class="nav-link active" id="content-info-tab" data-toggle="tab" href="#content-info" role="tab"
            aria-controls="content-info" aria-selected="true">{{ translate('Content') }}</a>
    </li>
    <li class="nav-item">
        <a class="nav-link" id="style-tab" data-toggle="tab" href="#style" role="tab"
            aria-controls="style" aria-selected="true">{{ translate('Style') }}</a>
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
        @include('plugin/tlcommerce-pagebuilder::page-builder.includes.lang-translate', ['lang' => $lang, 'widget' => 'heading_tag'])

        <!-- Tag -->
        <div class="form-group mb-3">
            <label for="tag" class="font-14 bold black">{{ translate('Tag') }}</label>
            <div class="mt-1">
                <select name="tag" id="tag" class="form-control">
                    <option value="h1" @selected(isset($widget_properties['tag']) && $widget_properties['tag'] == 'h1')>h1</option>
                    <option value="h2" @selected(isset($widget_properties['tag']) && $widget_properties['tag'] == 'h2')>h2</option>
                    <option value="h3" @selected(isset($widget_properties['tag']) && $widget_properties['tag'] == 'h3')>h3</option>
                    <option value="h4" @selected(isset($widget_properties['tag']) && $widget_properties['tag'] == 'h4')>h4</option>
                    <option value="h5" @selected(isset($widget_properties['tag']) && $widget_properties['tag'] == 'h5')>h5</option>
                    <option value="h6" @selected(isset($widget_properties['tag']) && $widget_properties['tag'] == 'h6')>h6</option>
                    <option value="div" @selected(isset($widget_properties['tag']) && $widget_properties['tag'] == 'div')>div</option>
                    <option value="span" @selected(isset($widget_properties['tag']) && $widget_properties['tag'] == 'span')>span</option>
                    <option value="p" @selected(isset($widget_properties['tag']) && $widget_properties['tag'] == 'p')>p</option>
                </select>
            </div>
        </div>

        <!-- Text -->
        <div class="form-group mb-3 translate-field">
            <label for="heading_text" class="font-14 bold black">{{ translate('Text') }}</label>
            <div class="mt-1">
                <input type="text" name="heading_text_t_" id="heading_text" class="form-control"
                    placeholder="{{ translate('Heading Text') }}" required
                    value="{{ isset($widget_properties['heading_text_t_']) ? $widget_properties['heading_text_t_'] : '' }}">
            </div>
        </div>

        <!-- Text Alignment -->
        <div class="form-group">
            <label for="alignment" class="d-block mb-2 font-14 bold black">{{ translate('Text Alignment') }}
            </label>
            <div class="btn-group" data-toggle="buttons">
                <label class="btn btn-primary sm {{ isset($widget_properties['alignment']) && $widget_properties['alignment'] == 'start' ? 'active':'' }}">
                    <input type="radio" class="d-none" name="alignment" id="start" value="start"
                        @checked(isset($widget_properties['alignment']) && $widget_properties['alignment'] == 'start')>
                    {{ translate('Start') }}
                </label>
                <label class="btn btn-primary sm {{ isset($widget_properties['alignment']) && $widget_properties['alignment'] == 'center' ? 'active':'' }}">
                    <input type="radio"class="d-none" name="alignment" id="center" value="center"
                        @checked(isset($widget_properties['alignment']) && $widget_properties['alignment'] == 'center')>
                    {{ translate('Center') }}
                </label>
                <label class="btn btn-primary sm {{ isset($widget_properties['alignment']) && $widget_properties['alignment'] == 'end' ? 'active':'' }}">
                    <input type="radio"class="d-none" name="alignment" id="end" value="end"
                        @checked(isset($widget_properties['alignment']) && $widget_properties['alignment'] == 'end')>
                    {{ translate('End') }}
                </label>
            </div>
        </div>
    </div>

    <!-- Style properties -->
    <div class="tab-pane fade" id="style" role="tabpanel" aria-labelledby="style-tab">
        <!-- Title Color -->
        <div class="form-group mb-20">
            <div class="col-sm-12">
                <label class="font-14 bold black">{{ translate('Color') }} </label>
            </div>
            <div class="col-sm-12">
                <div class="input-group addon">
                    <input type="text" name="color_c_" class="color-input form-control style--two"
                        placeholder="#000000" value="{{ isset($widget_properties['color_c_']) ? $widget_properties['color_c_'] : '' }}">
                    <div class="input-group-append">
                        <input type="color" class="input-group-text theme-input-style2 color-picker" value="{{ isset($widget_properties['color_c_']) ? $widget_properties['color_c_'] : '#000000' }}">
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Include background Properties -->
    @include('plugin/tlcommerce-pagebuilder::page-builder.properties.background-properties', ['properties' => $widget_properties])

    <!-- Include Advance Properties -->
    @include('plugin/tlcommerce-pagebuilder::page-builder.properties.advance-properties', ['properties' => $widget_properties])
</div>
<script>
    (function($) {
        'use strict'

        // Checked Button
        let heading_alignment = $('input[name="heading_alignment"]:checked');
        heading_alignment.parent().addClass('active');

    })(jQuery);
</script>
