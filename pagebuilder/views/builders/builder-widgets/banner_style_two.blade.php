@php
    $primary_text = $data['primary_text_t_'];
    $secondary_text = $data['secondary_text_t_'];

    $primary_button_text = $data['primary_button_text_t_'];
    $primary_button_url = $data['primary_button_url'];

    $secondary_button_text = $data['secondary_button_text_t_'];
    $secondary_button_url = $data['secondary_button_url'];

    $background_image = $data['background_image'];
    $background_shape_image = $data['background_shape_image'];
    $foreground_image = $data['foreground_image'];
@endphp

<section class="banner style--two plugins"
    data-bg-img="{{ !empty($background_image) ? asset(getFilePath($background_image)) : '' }}">
    @if (!empty($background_shape_image))
        <img src="{{ asset(getFilePath($background_shape_image)) }}" alt="" class="svg plug-banner-shape">
    @endif

    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6 col-md-12 order-1 order-lg-0">
                <div class="banner-content">
                    <div class="content text-white">
                        <h1>{{ $primary_text }}</h1>

                        <p>{{ $secondary_text }}</p>
                    </div>

                    <div class="banner-btn-group">
                        <a href="{{ $primary_button_url }}" class="btn-crs plug s-btn">{{ $primary_button_text }}</a>
                        <a href="{{ $secondary_button_url }}" class="btn-book line-btn">{{ $secondary_button_text }}</a>
                    </div>
                </div>
            </div>
            <div class="col-lg-6 col-md-12 order-0 order-lg-1">
                <div class="banner-img text-right">
                    @if (!empty($foreground_image))
                        <img src="{{ asset(getFilePath($foreground_image)) }}" class="b-thumb" data-rjs="2"
                            alt="">
                    @endif
                </div>
            </div>
        </div>
    </div>
</section>