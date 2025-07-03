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
        <!-- Alignment -->
        <div class="form-group">
            <label for="btn_alignment" class="d-block mb-2 font-14 bold black">{{ translate('Submit Button Alignment') }}
            </label>
            <div class="btn-group" data-toggle="buttons">
                <label
                    class="btn btn-primary sm {{ isset($widget_properties['btn_alignment']) && $widget_properties['btn_alignment'] == 'left' ? 'active' : '' }}">
                    <input type="radio" class="d-none" name="btn_alignment" id="left" value="left"
                        @checked(isset($widget_properties['btn_alignment']) && $widget_properties['btn_alignment'] == 'left')>
                    {{ translate('Left') }}
                </label>
                <label
                    class="btn btn-primary sm {{ isset($widget_properties['btn_alignment']) && $widget_properties['btn_alignment'] == 'center' ? 'active' : '' }}">
                    <input type="radio"class="d-none" name="btn_alignment" id="center" value="center"
                        @checked(isset($widget_properties['btn_alignment']) && $widget_properties['btn_alignment'] == 'center')>
                    {{ translate('Center') }}
                </label>
                <label
                    class="btn btn-primary sm {{ isset($widget_properties['btn_alignment']) && $widget_properties['btn_alignment'] == 'right' ? 'active' : '' }}">
                    <input type="radio"class="d-none" name="btn_alignment" id="right" value="right"
                        @checked(isset($widget_properties['btn_alignment']) && $widget_properties['btn_alignment'] == 'right')>
                    {{ translate('Right') }}
                </label>
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
