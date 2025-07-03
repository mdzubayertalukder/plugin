@php
    $active_theme = getActiveTheme();
    $option_settings = getThemeOption('google_adsense', $active_theme->id);
    $adsense_list = null;
    if (isset($option_settings['adsense_list']) && $option_settings['adsense_list'] != '') {
        $adsense_list = json_decode($option_settings['adsense_list']);
    }
@endphp
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
                    value="{{ isset($widget_properties['ads_url']) ? $widget_properties['ads_url'] : '' }}">
            </div>
            <label for="new_window">
                <input type="checkbox" name="new_window" id="new_window" @checked(isset($widget_properties['new_window']) && $widget_properties['new_window'] == '1') value="1">
                {{ translate('Open in new window') }}
            </label>
        </div>

        <!-- Adsense -->
        <div class="form-group mb-20">
            <div class="col-12">
                <label class="font-14 bold black">{{ translate('Google Adsense') }} </label>
            </div>
            <div class="col-12">
                <select name="google_adsense" id="google_adsense"
                    class="form-control">
                    <option value="">{{ translate('Select Adsence') }}</option>
                    @if (isset($adsense_list))
                        @foreach ($adsense_list as $adsense)
                            <option value="{{ $adsense->adsense_index }}" @selected(isset($widget_properties['google_adsense']) && $widget_properties['google_adsense'] == $adsense->adsense_index)>{{ $adsense->adsense_title }}</option>
                        @endforeach
                    @endif
                </select>
            </div>
            <span class="font-14 d-block ml-3 mt-2 text-danger">* {{ translate('If you select AdSense, it will be overwrite Image and Url fields.') }}</span>
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
