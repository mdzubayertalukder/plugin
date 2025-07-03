@php
    $feature_image = $data['feature_image'];
@endphp

<div class="single-feature box single-service style--six align-items-center media justify-content-center" id="feature">
    @if (!empty($feature_image))
        <div class="service-icon m-0">
            <img src="{{ asset(getFilePath($feature_image)) }}" class="svg" alt="">
        </div>
    @endif
    @empty(!$data['feature_description_t_'])
        <div class="service-content media-body">
            {!! fix_image_urls($data['feature_description_t_']) !!}
        </div>
    @endempty
</div>
