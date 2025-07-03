@php
    $verticalSlide = 1;
    $horizontalSlide = 1;
    $total_slide = 1;
    $slideBlog = true;
@endphp
<div class="widget-content">
    @if ($slideBlog)
        <!-- Post Carousel -->
        <div class="wmcp-cover owl-carousel" data-owl-mouse-drag="true" data-owl-dots="true" data-owl-margin="20"
            data-owl-items="2">
            @foreach ($data as $key => $review)
                <!-- Single Testimolial -->
                <div class="testimonial-single style--two plug">
                    <div class="ts-top d-flex align-items-center">
                        <div class="tst-content media align-items-center">
                            <div class="ts-img m-0">
                                <img src="{{ $review['reviewer_image']['path'] }}" data-rjs="2" alt="">
                            </div>
                            <div class="content media-body">
                                @if ((float) $review['rating'] >= 5)
                                    <!-- Ratings -->
                                    <div class="star-rating">
                                        <i class="fa fa-star"></i>
                                        <i class="fa fa-star"></i>
                                        <i class="fa fa-star"></i>
                                        <i class="fa fa-star"></i>
                                        <i class="fa fa-star"></i>
                                    </div>
                                    <!-- Ratings -->
                                @elseif(strpos($review['rating'], '.'))
                                    @for ($i = 0; $i < (int) $review['rating']; $i++)
                                        <i class="fa fa-star"></i>
                                    @endfor
                                    <i class="fa fa-star-half-o"></i>
                                @else
                                    @for ($i = 0; $i < (int) $review['rating']; $i++)
                                        <i class="fa fa-star"></i>
                                    @endfor
                                @endif
                                <h5>{{ $review['reviewer_name'] }}</h5>
                            </div>
                        </div>
                    </div>
                    <div class="ts-bottom">
                        <p>{{ $review['comment'] }}</p>
                    </div>
                </div>
                <!-- End Single Testimolial -->
            @endforeach
        </div>
        <!-- End of Post Carousel -->
    @endif
</div>
