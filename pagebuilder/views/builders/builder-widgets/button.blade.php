@php
    $url = isset($data['button_url']) ? $data['button_url'] : '#';
    $target = isset($data['new_window']) && $data['new_window'] == 1 ? '_blank' : '_self';
    $text = isset($data['button_text_t_']) ? $data['button_text_t_'] : '';
    $justify = isset($data['alignment']) && $data['alignment'] == 'justify' ? 'd-block' :'';
@endphp

<a href="{{ $url }}" target="{{ $target }}" class="btn-crs btn-cta text-center {{ $justify }}">{{ $text }}</a>