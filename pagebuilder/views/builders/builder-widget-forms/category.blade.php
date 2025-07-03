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
        @include('plugin/pagebuilder::page-builder.includes.lang-translate', [
            'lang' => $lang,
            'widget' => 'category',
        ])

        <!-- Type and Placeholder -->
        <div class="form-group row mt-2">
            <label for="type" class="col-2 mb-2 font-14 bold black">{{ translate('Type') }}
            </label>
            <select name="type" id="type" class="col-5 offset-3 form-control">
                <option value="dropdown" @selected(isset($widget_properties['type']) && $widget_properties['type'] == 'dropdown')>{{ translate('Dropdown') }}</option>
                <option value="list" @selected(isset($widget_properties['type']) && $widget_properties['type'] == 'list')>{{ translate('List') }}</option>
            </select>
        </div>

        <div
            class="form-group translate-field {{ isset($widget_properties['type']) && $widget_properties['type'] != 'dropdown' ? 'd-none' : '' }} col-12 mt-3 placeholder">
            <label for="placeholder" class="mb-2 font-14 bold black">{{ translate('Placeholder') }}
            </label>
            <input type="text" name="placeholder_t_" id="placeholder" class="form-control mb-2"
                placeholder="{{ translate('Select Placeholder') }}"
                value="{{ isset($widget_properties['placeholder_t_']) ? $widget_properties['placeholder_t_'] : '' }}">
            </label>
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
        $(document).on('change', '#type', function() {
            let selected = $(this).find('option:selected').val();
            if (selected && selected == 'dropdown') {
                $('.placeholder').removeClass('d-none');
            } else {
                $('.placeholder').addClass('d-none');
            }
        })

    })(jQuery);
</script>
