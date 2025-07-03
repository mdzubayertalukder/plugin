@php
    switch ($data['type']) {
        case 'recent':
            $blogs = frontendSidebarRecentBlogs(4);
            break;
        case 'featured':
            $blogs = frontendSidebarFeaturedBlogs(4);
            break;
        case 'view':
            $blogs = mostViewedBlogs(4);
            break;
        case 'popular':
            $blogs = mostPopularBlogs(4);
            break;
        case 'comment':
            $blogs = frontendSidebarMostCommentBlogs(4);
            break;
        case 'category':
            $blogs = blogsByCategory($data['category'], 4);
            break;
        default:
            $blogs = 0;
            break;
    }
@endphp

<div class="banner-slider-cover d-flex align-items-center p-0">
    <div class="banner-slider owl-carousel">
        @if (isset($blogs) && count($blogs) > 1)
            @foreach ($blogs as $blog)
                @php
                    switch ($blog->formate) {
                        case 'video':
                            $class = 'post-has-general-video';
                            break;
                        case 'audio':
                            $class = 'post-has-general-audio';
                            break;
                        default:
                            $class = '';
                            break;
                    }
                @endphp
                <div class="post-default post-has-bg-img m-0  {{ $class }}">
                    <div class="post-thumb">
                        <a href="{{ route('theme.default.blog_details', $blog->permalink) }}" aria-label="blog image">
                            @php
                                $variation = getImageVariation($blog->image, 'large');
                            @endphp
                            <div data-bg-img="{{ isset($blog->image) ? $variation : '' }}">
                            </div>
                        </a>
                    </div>
                    <div class="post-data">
                        <!-- Category -->
                        <div class="cats">
                            {{-- Checking if blog category id exists --}}
                            @if (count($blog->blog_category))
                                @foreach ($blog->blog_category as $cat)
                                    <a href="{{ route('theme.default.blogByCategory', $cat->permalink) }}"
                                        class="mr-1">{{ $cat->name }}</a>
                                @endforeach
                            @endif
                        </div>
                        <!-- Title -->
                        <div class="title">
                            <h2><a href="{{ route('theme.default.blog_details', $blog->permalink) }}">
                                    {{ $blog->name }}
                                </a>
                            </h2>
                        </div>
                    </div>
                </div>
            @endforeach
        @endif
    </div>
    <!-- End of Banner Dots Slider -->
</div>
