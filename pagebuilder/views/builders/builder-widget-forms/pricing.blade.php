<ul class="nav nav-tabs mb-20" id="myTab" role="tablist">
    <li class="nav-item">
        <a class="nav-link active" id="content-info-tab" data-toggle="tab" href="#content-info" role="tab"
            aria-controls="content-info" aria-selected="true">{{ translate('Content') }}</a>
    </li>

    
</ul>
<div class="tab-content" id="myTabContent">
    <!-- Content Properties -->
    <div class="tab-pane fade show active" id="content-info" role="tabpanel" aria-labelledby="content-info-tab">
        @include('plugin/pagebuilder::page-builder.includes.lang-translate', [
            'lang' => $lang,
            'widget' => 'pricing',
        ])

        <!-- Subscribe Button Text -->
        <div class="form-group mb-4 translate-field">
            <label for="subscribe_btn_text" class="font-14 bold black">{{ translate('Subscribe Button Text') }}</label>
            <input type="text" id="subscribe_btn_text" name="subscribe_btn_text_t_" class="form-control"
                placeholder="{{ translate('Subscribe Button Text') }}" required
                value="{{ isset($widget_properties['subscribe_btn_text_t_']) ? $widget_properties['subscribe_btn_text_t_'] : '' }}">
        </div>
    </div>

    <!-- Include Advance Properties -->
    @include('plugin/pagebuilder::page-builder.properties.advance-properties', [
        'properties' => $widget_properties,
    ])
</div>
