@php
    $limit = $data['count'];
    
    switch ($data['type']) {
        case 'recent':
            $blogs = frontendSidebarRecentBlogs($limit);
            break;
        case 'featured':
            $blogs = frontendSidebarFeaturedBlogs($limit);
            break;
        case 'view':
            $blogs = mostViewedBlogs($limit);
            break;
        case 'popular':
            $blogs = mostPopularBlogs($limit);
            break;
        case 'comment':
            $blogs = frontendSidebarMostCommentBlogs($limit);
            break;
        case 'category':
            $blogs = blogsByCategory($data['category'], $limit);
            break;
        default:
            $blogs = 0;
            break;
    }
    
    $slideBlog = false;
    
    if (!empty($blogs) && isset($data['horizontal_count']) && isset($data['vertical_count'])) {
        $verticalSlide = $data['vertical_count'];
        $horizontalSlide = $data['horizontal_count'];
        $total_slide = (int) ceil(count($blogs) / $verticalSlide);
        $slideBlog = true;
    }
@endphp
<div class="widget-content">
    @if ($slideBlog)
        <!-- Post Carousel -->
        
        <div class="wmcp-cover owl-carousel" data-owl-mouse-drag="true" data-owl-dots="true" data-owl-margin="20"
            data-owl-items="{{ $horizontalSlide }}">
            @php
                $slide_blog = [];
                $slideBreakCount = $verticalSlide;
            @endphp
            @for ($i = 0; $i < $total_slide; $i++)
                <!-- Carousel Item -->
                <div class="wmcp-item d-block">
                    @foreach ($blogs as $key => $blog)
                        @if (!in_array($key, $slide_blog))
                            <!-- Single Post -->
                            <div class="wmc-post d-block">
                                <a href="{{ route('theme.default.blog_details', $blog->permalink) }}">
                                    @php
                                        $variation = getImageVariation($blog->image, 'medium');
                                    @endphp
                                    <img data-src="{{ $variation }}" alt="{{ $blog->name }}" class="img-fluid lazy">
                                </a>
                                <div class="wmc-post-title">
                                    <h5> <a
                                            href="{{ route('theme.default.blog_details', $blog->permalink) }}">{{ $blog->name }}</a>
                                    </h5>
                                </div>
                            </div>
                            <!-- End of Single Post -->
                            @php
                                array_push($slide_blog, $key);
                            @endphp
                        @endif
                        @if ($key == $slideBreakCount - 1)
                            @php
                                $slideBreakCount += $verticalSlide;
                            @endphp
                        @break
                    @endif
                @endforeach
            </div>
            <!-- End of Carousel Item -->
        @endfor
    </div>
    <!-- End of Post Carousel -->
@endif
</div>
