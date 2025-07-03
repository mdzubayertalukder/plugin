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
        <!-- Image -->
        <div class="form-group mb-20">
            <div class="col-sm-12">
                <label class="font-14 bold black">{{ translate('Image') }} </label>
            </div>
            <div class="col-md-12">
                @include('core::base.includes.media.media_input', [
                    'input' => 'ads_image',
                    'data' => isset($widget_properties['ads_image']) ? $widget_properties['ads_image'] : null,
                ])
            </div>
        </div>

        <!-- Link -->
        <div class="form-group mb-3">
            <label for="ads_url" class="font-14 bold black">{{ translate('Link') }}</label>
            <div class="mt-1 mb-3">
                <input type="text" name="ads_url" id="ads_url" class="form-control"
                    placeholder="{{ translate('Ads Url') }}"
                    value="{{ isset($widget_properties['ads_url']) ? $widget_properties['ads_url'] : '' }}" required>
            </div>
            <label for="new_window">
                <input type="checkbox" name="new_window" id="new_window" @checked(isset($widget_properties['new_window']) && $widget_properties['new_window'] == '1') value="1">
                {{ translate('Open in new window') }}
            </label>
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
