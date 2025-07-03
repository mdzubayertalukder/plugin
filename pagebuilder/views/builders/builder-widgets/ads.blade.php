@php
    $active_theme = getActiveTheme();
    $option_settings = getThemeOption('google_adsense', $active_theme->id);
    $adsense_list = null;
    if (isset($option_settings['adsense_list']) && $option_settings['adsense_list'] != '') {
        $adsense_list = json_decode($option_settings['adsense_list'], true);
    }
    $ads_url = isset($data['ads_url']) ? $data['ads_url'] : '/';
    $ads_image = isset($data['ads_image']) ? $data['ads_image'] : '';
    $target = isset($data['new_window']) && $data['new_window'] == 1 ? '_blank' : '_self';
@endphp

@if (isset($data['google_adsense']) &&
        !empty($data['google_adsense']) &&
        isset($adsense_list) &&
        findAdsense($data['google_adsense'], $adsense_list))
    {!! findAdsense($data['google_adsense'], $adsense_list) !!}
@else
    <a href="{{ $ads_url }}" aria-label="ads" target="{{ $target }}"><img data-src="{{ asset(getFilePath($ads_image)) }}" alt="Ads"
            class="img-fluid lazy"></a>
@endif
